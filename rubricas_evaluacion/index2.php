<div class="container">
    <div id="appRubEval" class="col-sm-10 col-sm-offset-1">
        <h2>Rúbricas de Evaluación</h2>
        <input type="hidden" id="id_rubrica_evaluacion">
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
                <h4 id="subtitulo" class="text-center">Selecciona un Tipo de Asignatura</h4>
                <select id="cboTipoAsignatura" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nueva R&uacute;brica de Evaluaci&oacute;n
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
                <center><h4 class="modal-title" id="myModalLabel">Nueva R&uacute;brica de Evaluaci&oacute;n</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_ru_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_ru_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="new_tipo_rubrica">
                            <option value="0">Seleccione...</option> 
                            <option value="1">OBLIGATORIO</option>
                            <option value="2">OPCIONAL</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addRubEval()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Menu Modal -->
<div class="modal fade" id="editRubEval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Editar R&uacute;brica de Evaluaci&oacute;n</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_ru_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_ru_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="edit_tipo_rubrica">
                            <option value="0">Seleccione...</option> 
                            <option value="1">OBLIGATORIO</option>
                            <option value="2">OPCIONAL</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateRubEval()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled",true);
        $("#cboApoEval").attr("disabled",true);
        $("#cboTipoAsignatura").attr("disabled",true);
        cargarPeriodosEvaluacion();
        cargarTiposAsignatura();
        $("#cboPerEval").change(function(e){
            // Código para recuperar los aportes de evaluación asociados al período de evaluación seleccionado
            if($(this).val()==0){
                $("#cboApoEval").attr("disabled",true);
                $("#cboTipoAsignatura").attr("disabled",true);
            }else{
                cargarAportesEvaluacion();
                $("#cboApoEval").attr("disabled",false);
                $("#cboTipoAsignatura").attr("disabled",false);
            }
        });
        $("#cboApoEval").change(function(e){
            if($("#cboTipoAsignatura").val()!=0){
                listarRubricasEvaluacion();
            }
        })
        $("#cboTipoAsignatura").change(function(e){
            // Código para recuperar las rúbricas de evaluación asociadas al aporte de evaluación seleccionado
            listarRubricasEvaluacion();
        });
        $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");
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
    function cargarTiposAsignatura()
	{
        $.get("scripts/cargar_tipos_asignatura.php", { },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $("#cboTipoAsignatura").append(resultado);
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
            $.get("scripts/cargar_aportes_evaluacion.php", 
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
        var id_aporte = $("#cboApoEval").val();
        var id_tipo = $("#cboTipoAsignatura").val();
        if (id_aporte==0){
            $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un aporte de evaluacion...</td></tr>");
            $("#btn-new").attr("disabled",true);
        } else if (id_tipo==0){
            $("#rub_eval").html("<tr><td colspan='5' align='center'>Debes seleccionar un tipo de asignatura...</td></tr>");
            $("#btn-new").attr("disabled",true);
        } else {
            $("#btn-new").attr("disabled",false);
            $.post("rubricas_evaluacion/cargar_rubricas.php", 
                { 
                    id_aporte_evaluacion: id_aporte,
                    id_tipo_asignatura: id_tipo
                },
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
    function editRubEval(id){
        //Obtengo los datos de la rubrica de evaluación seleccionada
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "rubricas_evaluacion/obtener_rubrica.php",
            method: "POST",
            type: "html",
            data: {
                id_rubrica: id
            },
            success: function(response){
                $("#text_message").html("");
                $("#id_rubrica_evaluacion").val(id);
                var rubrica = jQuery.parseJSON(response);
                $("#edit_ru_nombre").val(rubrica.ru_nombre);
                $("#edit_ru_abreviatura").val(rubrica.ru_abreviatura);
                var tipo_rubrica = rubrica.id_tipo_rubrica;
                document.getElementById("edit_tipo_rubrica").length = 0;
                var html0 = '<option value="0">Seleccione...</option>';
                var html1 = '<option value="1"';
                var selected = (tipo_rubrica == 1)? ' selected': '';
                var html2 = '>OBLIGATORIO</option>';
                $('#edit_tipo_rubrica').append(html0+html1+selected+html2);
                var html1 = '<option value="2"';
                var selected = (tipo_rubrica == 2)? ' selected': '';
                var html2 = '>OPCIONAL</option>';
                $('#edit_tipo_rubrica').append(html1+selected+html2);
                $('#editRubEval').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        }); 
    }
    function updateRubEval() {
        var id = $("#id_rubrica_evaluacion").val();
        var id_aporte = $("#cboApoEval").val();
        var nombre = $("#edit_ru_nombre").val();
        var abreviatura = $("#edit_ru_abreviatura").val();
        var tipo_rubrica = $("#edit_tipo_rubrica").val();
        $.ajax({
            url: "rubricas_evaluacion/actualizar_rubrica.php",
            method: "POST",
            type: "html",
            data: {
                id_rubrica_evaluacion: id,
                id_aporte_evaluacion: id_aporte,
                ru_nombre: nombre,
                ru_abreviatura: abreviatura,
                tipo_rubrica: tipo_rubrica
            },
            success: function(response){
                $("#text_message").html(response);
                listarRubricasEvaluacion();
                $('#editRubEval').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function deleteRubEval(id){
        //Elimino la rubrica de evaluacion mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "rubricas_evaluacion/eliminar_rubrica_evaluacion.php",
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
    function addRubEval(){
        var id_aporte = $("#cboApoEval").val();
        var id_tipo = $("#cboTipoAsignatura").val();
        var ru_nombre = $("#new_ru_nombre").val();
        var ru_abreviatura = $("#new_ru_abreviatura").val();
        var tipo_rubrica = $("#new_tipo_rubrica").val();
        if (ru_nombre.trim()==""){
            alert("Debes ingresar el nombre de la rubrica de evaluacion...");
        } else if (ru_abreviatura.trim()==""){
            alert("Debes ingresar la abreviatura de la rubrica de evaluacion...");
        } else if (tipo_rubrica==0){
            alert("Debes seleccionar el tipo de rubrica de evaluacion...");
        } else {
            $.ajax({
                url: "rubricas_evaluacion/insertar_rubrica.php",
                method: "POST",
                type: "html",
                data: {
                    id_aporte_evaluacion: id_aporte,
                    id_tipo_asignatura: id_tipo,
                    id_tipo_rubrica: tipo_rubrica,
                    ru_nombre: ru_nombre,
                    ru_abreviatura: ru_abreviatura
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