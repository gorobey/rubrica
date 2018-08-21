<div class="container">
    <div id="appEspecialidades" class="col-sm-10 col-sm-offset-1">
        <h2>Especialidades</h2>
        <input type="hidden" id="id_especialidad">
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Nivel de Educación</h4>
            <form id="form_especialidades" action="" class="app-form">
                <select id="cboNivEdu" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nueva Especialidad
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
                <tbody id="lista_especialidades">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- New Especialidad Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Nueva Especialidad</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_es_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Figura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_es_figura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_es_abreviatura" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addEspecialidad()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Especialidad Modal -->
<div class="modal fade" id="editEspecialidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Especialidad</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_es_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Figura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_es_figura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_es_abreviatura" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateEspecialidad()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled","true");
        cargarNivelesEducacion();
        $("#cboNivEdu").change(function(e){
            // Código para recuperar las especialidades asociadas al nivel educativo seleccionado
            listarEspecialidades();
        });           
        $("#lista_especialidades").html("<tr><td colspan='4' align='center'>Debes seleccionar un nivel educativo...</td></tr>");
    });
    function cargarNivelesEducacion()
	{
        $.get("scripts/cargar_tipos_educacion.php", { },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $("#cboNivEdu").append(resultado);
                }
            }
        );
	}
    function listarEspecialidades()
	{
        var id = $("#cboNivEdu").val();
        if(id==0){
            $("#lista_especialidades").html("<tr><td colspan='4' align='center'>Debes seleccionar un nivel educativo...</td></tr>");
            $("#btn-new").attr("disabled",true);
        }else{
            $.post("especialidades/cargar_especialidades.php", { id_tipo_educacion: id },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#btn-new").attr("disabled",false);
                        $("#lista_especialidades").html(resultado);
                    }
                }
            );
        }
	}
    function deleteEspecialidad(id)
	{
		// Validación de la entrada de datos
		
		if (id=="") {
			$("#text_message").html("No se ha pasado el parámetro de id_especialidad...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar esta especialidad?")
			if (eliminar) {
				$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
                    type: "POST",
                    url: "especialidades/eliminar_especialidad.php",
                    data: "id_especialidad="+id,
                    success: function(resultado){
                        $("#text_message").html(resultado);
                        listarEspecialidades();
                    }
				});			
			}
		}	
	}
    function editEspecialidad(id){
        //Obtengo los datos de la especialidad seleccionada
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "especialidades/obtener_especialidad.php",
            method: "POST",
            type: "html",
            data: {
                id_especialidad: id
            },
            success: function(response){
                $("#text_message").html("");
                $("#id_especialidad").val(id);
                var especialidad = jQuery.parseJSON(response);
                $("#edit_es_nombre").val(especialidad.es_nombre);
                $("#edit_es_figura").val(especialidad.es_figura);
                $("#edit_es_abreviatura").val(especialidad.es_abreviatura);
                $('#editEspecialidad').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        }); 
    }
    function updateEspecialidad() {
        var id = $("#id_especialidad").val();
        var nombre = $("#edit_es_nombre").val();
        var figura = $("#edit_es_figura").val();
        var abreviatura = $("#edit_es_abreviatura").val();
        var id_tipo_educacion = $("#cboNivEdu").val();
        $.ajax({
            url: "especialidades/actualizar_especialidad.php",
            method: "POST",
            type: "html",
            data: {
                id_especialidad: id,
                es_nombre: nombre,
                es_figura: figura,
                es_abreviatura: abreviatura,
                id_tipo_educacion: id_tipo_educacion
            },
            success: function(response){
                $("#text_message").html(response);
                listarEspecialidades();
                $('#editEspecialidad').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function addEspecialidad(){
        var id_tipo_educacion = $("#cboNivEdu").val();
        var es_nombre = $("#new_es_nombre").val();
        var es_figura = $("#new_es_figura").val();
        var es_abreviatura = $("#new_es_abreviatura").val();
        $.ajax({
            url: "especialidades/insertar_especialidad.php",
            method: "POST",
            type: "html",
            data: {
                id_tipo_educacion: id_tipo_educacion,
                es_nombre: es_nombre,
                es_figura: es_figura,
                es_abreviatura: es_abreviatura
            },
            success: function(response){
                listarEspecialidades();
                $('#addnew').modal('hide');
                $("#text_message").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }    
</script>