    <div class="container">
        <div id="areasApp" class="col-sm-9 col-sm-offset-1">
            <h2>Areas</h2>
            <input type="hidden" id="id_area">
            <!-- form -->
            <div class="panel panel-default">
                <h4 id="subtitulo" class="text-center">Añade Una nueva Area</h4>
                <form id="form_areas" action="" class="app-form">
                    <input type="text" class="form-control" id="ar_nombre" autofocus>
                    <input id="enviar" type="submit" value="Añadir" class="btn btn-block btn-primary">
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
                    <tbody id="lista_areas">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            cargarAreas();
            // Código para editar un area
            $('#form_areas').submit(function(event){
                event.preventDefault();
                var ar_nombre = $('#ar_nombre').val();
                if(ar_nombre.trim()==""){
                    $("#text_message").html("Debes ingresar el nombre del area...");
                    $("#ar_nombre").focus();
                    return false;
                }
                if($("#enviar").val() == "Añadir"){
                    $.ajax({
                        url: "areas/insertar_area.php",
                        method: "POST",
                        data: {
                            ar_nombre: ar_nombre
                        },
                        type: "html",
                        success: function(response){
                            $('#text_message').html(response);
                            $("#ar_nombre").val("");
                            cargarAreas();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }else if($("#enviar").val() == "Actualizar"){
                    var id = $("#id_area").val();
                    $.ajax({
                        url: "areas/actualizar_area.php",
                        method: "POST",
                        data: {
                            id_area: id,
                            ar_nombre: ar_nombre
                        },
                        type: "html",
                        success: function(response){
                            $('#text_message').html(response);
                            $("#ar_nombre").val("");
                            $("#subtitulo").html("Añade una Nueva Area");
                            $("#enviar").val("Añadir");
                            cargarAreas();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });
        });
        function cargarAreas(){
            // Obtengo todas las areas ingresadas en la base de datos
            $.ajax({
                url: "areas/cargar_areas.php",
                method: "GET",
                type: "html",
                success: function(response){
                    $("#lista_areas").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function editArea(id){
            //Primero obtengo el nombre del area seleccionada
            $.ajax({
                url: "areas/obtener_area.php",
                method: "POST",
                type: "html",
                data: {
                    id_area: id
                },
                success: function(response){
                    var area = jQuery.parseJSON(response);
                    $("#ar_nombre").val(area.ar_nombre);
                    $("#subtitulo").html("Actualiza esta Area");
                    $("#enviar").val("Actualizar");
                    $("#id_area").val(id);
                    $("#ar_nombre").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function deleteArea(id){
            //Elimino el area mediante AJAX
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "areas/eliminar_area.php",
                method: "POST",
                type: "html",
                data: {
                    id_area: id
                },
                success: function(response){
                    $("#text_message").html(response);
                    cargarAreas();
                    $("#ar_nombre").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    </script>