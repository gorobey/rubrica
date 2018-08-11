<div class="container">
    <div id="appRubEval" class="col-sm-10 col-sm-offset-1">
        <h2>Rúbricas de Proyectos</h2>
        <input type="hidden" id="id_rubrica_evaluacion_club">
        <!-- panel -->
        <div class="panel panel-default">
            <form id="form_rub_eval" action="" class="app-form">
                <h4 id="subtitulo" class="text-center">Selecciona un Período de Evaluación</h4>
                <select id="cboPerEval" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <h4 id="subtitulo" class="text-center">Selecciona un Aporte de Evaluación</h4>
                <select id="cboApoEval" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nueva R&uacute;brica de Proyecto
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
                        <th>Abreviatura</th>
                        <th colspan=2>Acciones</th>
                    </tr>
                </thead>
                <tbody id="rub_eval">
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
                <center><h4 class="modal-title" id="myModalLabel">Nueva R&uacute;brica de Proyecto</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_rc_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_rc_abreviatura" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addRubProy()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Menu Modal -->
<div class="modal fade" id="editRubProy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Editar R&uacute;brica de Proyecto</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_rc_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_rc_abreviatura" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRubProy()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled",true);
        cargarPeriodosEvaluacion();
        $("#cboPerEval").change(function(e){
            // Código para recuperar los aportes de evaluación asociados al período de evaluación seleccionado
            cargarAportesEvaluacion();
        });
        $("#cboApoEval").change(function(e){
            // Código para recuperar las rúbricas de evaluación asociadas al aporte de evaluación seleccionado
            listarRubricasEvaluacion();
        });
        $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");
    });
    function cargarPeriodosEvaluacion()
	{
        $.get("scripts/cargar_periodos_evaluacion_principales.php", { },
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
    function cargarAportesEvaluacion()
	{
        var id = $("#cboPerEval").val();
        document.getElementById("cboApoEval").length = 0;
        $('#cboApoEval').append('<option value="0">Seleccione...</option>');
        $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un aporte de evaluacion...</td></tr>");
        $("#btn-new").attr("disabled",true);
        if (id==0){
            $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");
        } else {
            $.get("scripts/cargar_aportes_principales_evaluacion.php", 
                { 
                    id_periodo_evaluacion: id
                },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#cboApoEval").append(resultado);
                    }
                }
            );
        }
	}
    function listarRubricasEvaluacion()
    {
        var id = $("#cboApoEval").val();
        if (id==0){
            $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un aporte de evaluacion...</td></tr>");
            $("#btn-new").attr("disabled",true);
        } else {
            $("#btn-new").attr("disabled",false);
            $.get("rubricas_proyecto/listar_rubricas.php", { id_aporte_evaluacion: id },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#rub_eval").html(resultado);
                    }
                }
            );
        }
    }
    function editRubProy(id){
        //Obtengo los datos de la rubrica de proyecto seleccionada
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "rubricas_proyecto/obtener_rubrica.php",
            method: "POST",
            type: "html",
            data: {
                id_rubrica: id
            },
            success: function(response){
                $("#text_message").html("");
                $("#id_rubrica_evaluacion_club").val(id);
                var rubrica = jQuery.parseJSON(response);
                $("#edit_rc_nombre").val(rubrica.rc_nombre);
                $("#edit_rc_abreviatura").val(rubrica.rc_abreviatura);
                $('#editRubProy').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        }); 
    }
    function updateRubProy() {
        var id = $("#id_rubrica_evaluacion_club").val();
        var id_aporte = $("#cboApoEval").val();
        var nombre = $("#edit_rc_nombre").val();
        var abreviatura = $("#edit_rc_abreviatura").val();
        if (nombre.trim()==""){
            alert("Debes ingresar el nombre de la rubrica de proyecto...");
        } else if (abreviatura.trim()==""){
            alert("Debes ingresar la abreviatura de la rubrica de proyecto...");
        } else {
            $.ajax({
                url: "rubricas_proyecto/actualizar_rubrica.php",
                method: "POST",
                type: "html",
                data: {
                    id_rubrica_evaluacion: id,
                    id_aporte_evaluacion: id_aporte,
                    rc_nombre: nombre,
                    rc_abreviatura: abreviatura
                },
                success: function(response){
                    $("#text_message").html(response);
                    listarRubricasEvaluacion();
                    $('#editRubProy').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function deleteRubProy(id){
        //Elimino la rubrica de evaluacion mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "rubricas_proyecto/eliminar_rubrica.php",
            method: "POST",
            type: "html",
            data: {
                id_rubrica_evaluacion: id
            },
            success: function(response){
                $("#text_message").html(response);
                listarRubricasEvaluacion();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function addRubProy(){
        var id = $("#cboApoEval").val();
        var rc_nombre = $("#new_rc_nombre").val();
        var rc_abreviatura = $("#new_rc_abreviatura").val();
        if (rc_nombre.trim()==""){
            alert("Debes ingresar el nombre de la rubrica de proyecto...");
        } else if (rc_abreviatura.trim()==""){
            alert("Debes ingresar la abreviatura de la rubrica de proyecto...");
        } else {
            $.ajax({
                url: "rubricas_proyecto/insertar_rubrica.php",
                method: "POST",
                type: "html",
                data: {
                    id_aporte_evaluacion: id,
                    rc_nombre: rc_nombre,
                    rc_abreviatura: rc_abreviatura
                },
                success: function(response){
                    listarRubricasEvaluacion();
                    $('#addnew').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
</script>