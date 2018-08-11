<div class="container">
    <div id="appApoEval" class="col-sm-10 col-sm-offset-1">
        <h2>Aportes de Evaluación</h2>
        <input type="hidden" id="id_aporte_evaluacion">
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Período de Evaluación</h4>
            <form id="form_apo_eval" action="" class="app-form">
                <select id="cboPerEval" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Aporte de Evaluaci&oacute;n
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th colspan=2>Acciones</th>
                    </tr>
                </thead>
                <tbody id="apo_eval">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- New Menu Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Nuevo Aporte de Evaluaci&oacute;n</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_ap_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_ap_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="new_ap_tipo">
                            <option value="0">Seleccione...</option> 
                            <option value="1">PARCIAL</option>
                            <option value="2">EXAMEN QUIMESTRAL</option>
                            <option value="3">SUPLETORIO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addApoEval()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Menu Modal -->
<div class="modal fade" id="editApoEval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Aporte de Evaluaci&oacute;n</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_ap_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_ap_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="edit_ap_tipo">
                            <option value="0">Seleccione...</option> 
                            <option value="1">PARCIAL</option>
                            <option value="2">EXAMEN QUIMESTRAL</option>
                            <option value="3">SUPLETORIO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateApoEval()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled","true");
        cargarPeriodosEvaluacion();
        $("#cboPerEval").change(function(e){
            // Código para recuperar los aportes de evaluación asociados al período de evaluación seleccionado
            listarAportesEvaluacion();
        });           
        $("#apo_eval").html("<tr><td colspan='4' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");
    });
    function cargarPeriodosEvaluacion()
	{
        $.get("scripts/cargar_periodos_evaluacion.php", { },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $("#cboPerEval").append(resultado);
                }
            }
        );
	}
    function listarAportesEvaluacion()
	{
        var id = document.getElementById("cboPerEval").value;
        if(id==0){
            $("#apo_eval").html("<tr><td colspan='4' align='center'>Debes seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>");
            $("#btn-new").attr("disabled",true);
        }else{
            $.post("aportes_evaluacion/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#btn-new").attr("disabled",false);
                        $("#apo_eval").html(resultado);
                    }
                }
            );
        }
	}
    function deleteApoEval(id)
	{
		// Validación de la entrada de datos
		
		if (id=="") {
			$("#text_message").html("No se ha pasado el parámetro de id_aporte_evaluacion...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este aporte de evaluacion?")
			if (eliminar) {
				$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
                    type: "POST",
                    url: "aportes_evaluacion/eliminar_aporte_evaluacion.php",
                    data: "id_aporte_evaluacion="+id,
                    success: function(resultado){
                        $("#text_message").html(resultado);
                        listarAportesEvaluacion();
                    }
				});			
			}
		}	
	}
    function editApoEval(id){
        //Obtengo los datos del aporte de evaluación seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "aportes_evaluacion/obtener_aporte_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_aporte_evaluacion: id
            },
            success: function(response){
                $("#text_message").html("");
                $("#id_aporte_evaluacion").val(id);
                var aporte = jQuery.parseJSON(response);
                $("#edit_ap_nombre").val(aporte.ap_nombre);
                $("#edit_ap_abreviatura").val(aporte.ap_abreviatura);
                var ap_tipo = aporte.ap_tipo;
                document.getElementById("edit_ap_tipo").length = 0;
                var html0 = '<option value="0">Seleccione...</option>';
                var html1 = '<option value="1"';
                var selected = (ap_tipo == 1)? ' selected': '';
                var html2 = '>PARCIAL</option>';
                $('#edit_ap_tipo').append(html0+html1+selected+html2);
                var html1 = '<option value="2"';
                var selected = (ap_tipo == 2)? ' selected': '';
                var html2 = '>EXAMEN QUIMESTRAL</option>';
                $('#edit_ap_tipo').append(html1+selected+html2);
                var html1 = '<option value="3"';
                var selected = (ap_tipo == 3)? ' selected': '';
                var html2 = '>SUPLETORIO</option>';
                $('#edit_ap_tipo').append(html1+selected+html2);
                $('#editApoEval').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        }); 
    }
    function updateApoEval() {
        var id = $("#id_aporte_evaluacion").val();
        var nombre = $("#edit_ap_nombre").val();
        var abreviatura = $("#edit_ap_abreviatura").val();
        var ap_tipo = $("#edit_ap_tipo").val();
        $.ajax({
            url: "aportes_evaluacion/actualizar_aporte_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_aporte_evaluacion: id,
                ap_nombre: nombre,
                ap_abreviatura: abreviatura,
                ap_tipo: ap_tipo
            },
            success: function(response){
                $("#text_message").html(response);
                listarAportesEvaluacion();
                $('#editApoEval').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function addApoEval(){
        var id_periodo_evaluacion = $("#cboPerEval").val();
        var ap_nombre = $("#new_ap_nombre").val();
        var ap_abreviatura = $("#new_ap_abreviatura").val();
        var ap_tipo = $("#new_ap_tipo").val();
        $.ajax({
            url: "aportes_evaluacion/insertar_aporte_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_periodo_evaluacion: id_periodo_evaluacion,
                ap_nombre: ap_nombre,
                ap_abreviatura: ap_abreviatura,
                ap_tipo: ap_tipo
            },
            success: function(response){
                listarAportesEvaluacion();
                $('#addnew').modal('hide');
                $("#text_message").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }    
</script>