    <div class="container">
        <div id="tipoEducacionApp" class="col-sm-9 col-sm-offset-1">
            <h2>Niveles de Educación</h2>
            <input type="hidden" id="id_tipo_educacion">
            <!-- form -->
            <div class="panel panel-default">
                <form id="form_tipos_educacion" action="" class="app-form">
                    <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                        Nuevo Nivel de Educación
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
                    <tbody id="niveles_educacion">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- New Nivel Modal -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel">Nuevo Nivel de Educaci&oacute;n</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_te_nombre" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">¿Es Bachillerato?:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_te_bachillerato">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addNivelEducacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            cargarNivelesEducacion();
            // Código para editar un perfil
            $('#form_perfiles').submit(function(event){
                event.preventDefault();
                var perfil = $('#perfil').val();
                if(perfil.trim()==""){
                    $("#error_message").html("Debes ingresar el texto del perfil...");
                    $("#perfil").focus();
                    return false;
                }
                if($("#enviar").val() == "Añadir"){
                    $.ajax({
                        url: "perfil/insertar_perfil.php",
                        method: "POST",
                        data: {
                            perfil: perfil
                        },
                        type: "html",
                        success: function(response){
                            $("#error_message").html("");
                            $('#text_message').html(response);
                            $("#perfil").val("");
                            cargarPerfiles();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }else if($("#enviar").val() == "Actualizar"){
                    var id = $("#id").val();
                    $.ajax({
                        url: "perfil/actualizar_perfil.php",
                        method: "POST",
                        data: {
                            id: id,
                            perfil: perfil
                        },
                        type: "html",
                        success: function(response){
                            $("#error_message").html("");
                            $('#text_message').html(response);
                            $("#perfil").val("");
                            $("#subtitulo").html("Añade un Nuevo Perfil");
                            $("#enviar").val("Añadir");
                            cargarPerfiles();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });
        });
        function cargarNivelesEducacion(){
            // Obtengo todas los perfiles ingresados en la base de datos
            $.ajax({
                url: "tipo_educacion/cargar_tipos_de_educacion.php",
                method: "GET",
                type: "html",
                success: function(response){
                    $("#niveles_educacion").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function editPerfil(id){
            //Primero obtengo el nombre del perfil seleccionado
            $.ajax({
                url: "perfil/obtener_perfil.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    var perfil = jQuery.parseJSON(response);
                    $("#perfil").val(perfil.pe_nombre);
                    $("#subtitulo").html("Actualiza este Perfil");
                    $("#enviar").val("Actualizar");
                    $("#id").val(id);
                    $("#perfil").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function deletePerfil(id){
            //Elimino el perfil mediante AJAX
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "perfil/eliminar_perfil.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    $("#text_message").html(response);
                    cargarPerfiles();
                    $("#perfil").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    </script>