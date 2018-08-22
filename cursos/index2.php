<div class="container">
    <div id="appCursos" class="col-sm-10 col-sm-offset-1">
        <h2>Cursos</h2>
        <input type="hidden" id="id_curso">
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona una Especialidad</h4>
            <form id="form_cursos" action="" class="app-form">
                <select id="cboEspecialidades" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Curso
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
                <tbody id="lista_cursos">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- New Curso Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Nuevo Curso</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_cu_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="new_cu_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tiene Proyectos:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="new_bol_proyectos">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addCurso()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Curso Modal -->
<div class="modal fade" id="editCurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Curso</h4></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_cu_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_cu_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tiene Proyectos:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="edit_bol_proyectos">
                            <!-- Aqui se genera el contenido dinamicamente -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updateCurso()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled","true");
        cargarEspecialidades();
        $("#cboEspecialidades").change(function(e){
            // Código para recuperar los cursos asociados a la especialidad seleccionada
            listarCursos();
        });           
        $("#lista_cursos").html("<tr><td colspan='4' align='center'>Debes seleccionar una especialidad...</td></tr>");
    });
    function cargarEspecialidades()
	{
        $.get("scripts/cargar_especialidades.php", { },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $("#cboEspecialidades").append(resultado);
                }
            }
        );
	}
    function listarCursos()
	{
        var id = document.getElementById("cboEspecialidades").value;
        if(id==0){
            $("#apo_eval").html("<tr><td colspan='4' align='center'>Debes seleccionar una especialidad...</td></tr>");
            $("#btn-new").attr("disabled",true);
        }else{
            $.post("cursos/cargar_cursos.php", { id_especialidad: id },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#btn-new").attr("disabled",false);
                        $("#lista_cursos").html(resultado);
                    }
                }
            );
        }
	}
    function deleteCurso(id)
	{
		// Validación de la entrada de datos
		
		if (id=="") {
			$("#text_message").html("No se ha pasado el parámetro de id_curso...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este curso?")
			if (eliminar) {
				$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
                    type: "POST",
                    url: "cursos/eliminar_curso.php",
                    data: "id_curso="+id,
                    success: function(resultado){
                        $("#text_message").html(resultado);
                        listarCursos();
                    }
				});			
			}
		}	
	}
    function editCurso(id){
        //Obtengo los datos del curso seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "cursos/obtener_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id
            },
            success: function(response){
                $("#text_message").html("");
                $("#id_curso").val(id);
                var curso = jQuery.parseJSON(response);
                $("#edit_cu_nombre").val(curso.cu_nombre);
                $("#edit_cu_abreviatura").val(curso.cu_abreviatura);
                var bol_proyectos = curso.bol_proyectos;
                document.getElementById("edit_bol_proyectos").length = 0;
                var html1 = '<option value="1"';
                var selected = (bol_proyectos == 1)? ' selected': '';
                var html2 = '>Sí</option>';
                $('#edit_bol_proyectos').append(html1+selected+html2);
                var html1 = '<option value="0"';
                var selected = (bol_proyectos == 0)? ' selected': '';
                var html2 = '>No</option>';
                $('#edit_bol_proyectos').append(html1+selected+html2);
                $('#editCurso').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        }); 
    }
    function updateCurso() {
        var id = $("#id_curso").val();
        var id_esp = $("#cboEspecialidades").val();
        var nombre = $("#edit_cu_nombre").val();
        var abreviatura = $("#edit_cu_abreviatura").val();
        var bol_proyectos = $("#edit_bol_proyectos").val();
        $.ajax({
            url: "cursos/actualizar_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id,
                id_especialidad: id_esp,
                cu_nombre: nombre,
                cu_abreviatura: abreviatura,
                bol_proyectos: bol_proyectos
            },
            success: function(response){
                $("#text_message").html(response);
                listarCursos();
                $('#editCurso').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function addCurso(){
        var id_especialidad = $("#cboEspecialidades").val();
        var cu_nombre = $("#new_cu_nombre").val();
        var cu_abreviatura = $("#new_cu_abreviatura").val();
        var bol_proyectos = $("#new_bol_proyectos").val();
        $.ajax({
            url: "cursos/insertar_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_especialidad: id_especialidad,
                cu_nombre: cu_nombre,
                cu_abreviatura: cu_abreviatura,
                bol_proyectos: bol_proyectos
            },
            success: function(response){
                listarCursos();
                $('#addnew').modal('hide');
                $("#text_message").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }    
</script>