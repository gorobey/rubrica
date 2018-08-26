<div class="container">
    <div id="asignaturasApp" class="col-sm-10 col-sm-offset-1">
        <h2>Asignaturas</h2>
        <input type="hidden" id="id_asignatura">
        <input type="hidden" id="id_area">
        <!-- panel -->
        <div class="panel panel-default">
            <form id="form_asignaturas" action="" class="app-form">
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nueva Asignatura
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Area</th>
                        <th>Nombre</th>
                        <th>Abreviatura</th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Borrar --></th>
                    </tr>
                </thead>
                <tbody id="lista_asignaturas">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- New Asignatura Modal -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel1">Nueva Asignatura</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Area:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_areas">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_as_nombre" value="">
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_as_abreviatura" value="">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_tipos">
                                <!-- Aqui se cargan los tipos de asignaturas dinamicamente -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addAsignatura()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Asignatura Modal -->
    <div class="modal fade" id="editAsignatura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel3">Editar Asignatura</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Area:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_ar_nombre" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_as_nombre" name="edit_as_nombre" value="">
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_as_abreviatura" name="edit_as_abreviatura" value="">
                            <span class="help-desk error" id="mensaje5"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_cbo_tipos">
                                <!-- Aqui se cargan los tipos de asignaturas dinamicamente -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateAsignatura()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargar_areas();
        cargar_tipos();
        listarAsignaturas();
    });
    function cargar_areas()
    {
        $.get("scripts/cargar_areas.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#new_cbo_areas').append(resultado);
            }
        });
    }
    function cargar_tipos()
    {
        $.get("scripts/cargar_tipos_asignatura.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#new_cbo_tipos').append(resultado);
            }
        });
    }
    function listarAsignaturas()
    {
        $.get("asignaturas/cargar_asignaturas.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#lista_asignaturas').html(resultado);
            }
        });	
    }
    function addAsignatura(){
        var id_area = $("#new_cbo_areas").val();
        var as_nombre = $("#new_as_nombre").val();
        var as_abreviatura = $("#new_as_abreviatura").val();
        var as_tipo = $("#new_cbo_tipos").val();

        // expresiones regulares para validar el ingreso de datos
        var reg_nombre = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,84})$/i;
		var reg_abreviatura = /^([a-zA-Z.]{3,8})$/i;
        
        // contador de errores
        var cont_errores = 0;

        if (id_area == 0){
            $("#mensaje1").html("Debes seleccionar el Area");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje1").fadeOut();
        }

        if(as_nombre.trim()==""){
            $("#mensaje2").html("Debes ingresar el nombre de la Asignatura");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else if(!reg_nombre.test(as_nombre)){
            $("#mensaje2").html("Debes ingresar un nombre válido para la Asignatura");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje2").fadeOut();
        }

        if(as_abreviatura.trim()==""){
            $("#mensaje3").html("Debes ingresar la abreviatura de la Asignatura");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else if(!reg_abreviatura.test(as_abreviatura)){
            $("#mensaje3").html("Debes ingresar una abreviatura válida para la Asignatura");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje3").fadeOut();
        }

        if(cont_errores==0){
            $.ajax({
                url: "asignaturas/insertar_asignatura.php",
                method: "POST",
                type: "html",
                data: {
                    id_area: id_area,
                    as_nombre: as_nombre,
                    as_abreviatura: as_abreviatura,
                    id_tipo_asignatura: as_tipo
                },
                success: function(response){
                    listarAsignaturas();
                    $('#addnew').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function updateAsignatura(){
        var id_area = $("#id_area").val();
        var id_asignatura = $("#id_asignatura").val();
        var as_nombre = $("#edit_as_nombre").val();
        var as_abreviatura = $("#edit_as_abreviatura").val();
        var as_tipo = $("#edit_cbo_tipos").val();

        // expresiones regulares para validar el ingreso de datos
        var reg_nombre = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,84})$/i;
		var reg_abreviatura = /^([a-zA-Z.]{3,8})$/i;
        
        // contador de errores
        var cont_errores = 0;

        if(as_nombre.trim()==""){
            $("#mensaje4").html("Debes ingresar el nombre de la Asignatura");
            $("#mensaje4").fadeIn("slow");
            cont_errores++;
        }else if(!reg_nombre.test(as_nombre)){
            $("#mensaje4").html("Debes ingresar un nombre válido para la Asignatura");
            $("#mensaje4").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje4").fadeOut();
        }

        if(as_abreviatura.trim()==""){
            $("#mensaje5").html("Debes ingresar la abreviatura de la Asignatura");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        }else if(!reg_abreviatura.test(as_abreviatura)){
            $("#mensaje5").html("Debes ingresar una abreviatura válida para la Asignatura");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje5").fadeOut();
        }

        if(cont_errores==0){
            $.ajax({
                url: "asignaturas/actualizar_asignatura.php",
                method: "POST",
                type: "html",
                data: {
                    id_asignatura: id_asignatura,
                    id_area: id_area,
                    as_nombre: as_nombre,
                    as_abreviatura: as_abreviatura,
                    id_tipo_asignatura: as_tipo
                },
                success: function(response){
                    listarAsignaturas();
                    $('#editAsignatura').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function editAsignatura(id)
    {
        $.ajax({
            type: "POST",
            url: "asignaturas/obtener_asignatura.php",
            data: "id_asignatura="+id,
            success: function(resultado){
                var asignatura = eval('(' + resultado + ')');
                //console.log(usuario);
                $("#id_asignatura").val(id);
                $("#id_area").val(asignatura.id_area);
                $("#edit_ar_nombre").val(asignatura.ar_nombre);
                $("#edit_as_nombre").val(asignatura.as_nombre);
                $("#edit_as_abreviatura").val(asignatura.as_abreviatura);
                html0 = "<option value='1'";
                html1 = (asignatura.id_tipo_asignatura==1)?" selected":"";
                html2 = ">CUANTITATIVA</option>";
                $("#edit_cbo_tipos").append(html0+html1+html2);
                html0 = "<option value='2'";
                html1 = (asignatura.id_tipo_asignatura==2)?" selected":"";
                html2 = ">CUALITATIVA</option>";
                $("#edit_cbo_tipos").append(html0+html1+html2);
                $('#editAsignatura').modal('show');
            }
        });
    }
    function deleteAsignatura(id){
        //Elimino la asignatura mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "asignaturas/eliminar_asignatura.php",
            method: "POST",
            type: "html",
            data: {
                id_asignatura: id
            },
            success: function(response){
                $("#text_message").html(response);
                listarAsignaturas();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>