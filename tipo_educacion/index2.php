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
    <!-- Edit Nivel Modal -->
    <div class="modal fade" id="editNivelEdu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel">Editar Nivel de Educaci&oacute;n</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_te_nombre" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">¿Es Bachillerato?:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_te_bachillerato">
                                <!-- Aqui se va a generar dinamicamente el contenido -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateNivelEducacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            cargarNivelesEducacion();
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
        function editNivelEducacion(id){
            //Procedimiento para obtener los datos del nivel de educacione elegido
            $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
            $.ajax({
                url: "tipo_educacion/obtener_tipo_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    id_tipo_educacion: id
                },
                success: function(response){
                    $("#text_message").html("");
                    $("#id_tipo_educacion").val(id);
                    var nivel_educacion = jQuery.parseJSON(response);
                    $("#edit_te_nombre").val(nivel_educacion.te_nombre);
                    var es_bachillerato = nivel_educacion.te_bachillerato;
                    document.getElementById("edit_te_bachillerato").length = 0;
                    var html1 = '<option value="1"';
                    var selected = (es_bachillerato == 1)? ' selected': '';
                    var html2 = '>Sí</option>';
                    $('#edit_te_bachillerato').append(html1+selected+html2);
                    var html1 = '<option value="0"';
                    var selected = (es_bachillerato == 0)? ' selected': '';
                    var html2 = '>No</option>';
                    $('#edit_te_bachillerato').append(html1+selected+html2);
                    $('#editNivelEdu').modal('show');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function updateNivelEducacion() {
            var id = $("#id_tipo_educacion").val();
            var nombre = $("#edit_te_nombre").val();
            var es_bachillerato = $("#edit_te_bachillerato").val();
            $.ajax({
                url: "tipo_educacion/actualizar_nivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    id_tipo_educacion: id,
                    te_nombre: nombre,
                    te_bachillerato: es_bachillerato
                },
                success: function(response){
                    $("#text_message").html(response);
                    cargarNivelesEducacion();
                    $('#editNivelEdu').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function deleteNivelEducacion(id){
            //Elimino el nivel de educacion mediante AJAX
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "tipo_educacion/eliminar_tipo_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    id_tipo_educacion: id
                },
                success: function(response){
                    $("#text_message").html(response);
                    cargarNivelesEducacion();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    </script>