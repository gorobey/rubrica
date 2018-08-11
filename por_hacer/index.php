    <div class="container">
        <div id="taskApp" class="col-sm-9 col-sm-offset-1">
            <h2>Tareas</h2>
            <!-- form -->
            <div class="panel panel-default">
                <h3 id="subtitulo" class="text-center">Añade Una nueva Tarea</h3>
                <form id="form_tasks" action="" class="app-form">
                    <input type="hidden" id="id">
                    <div id="error_message" class="error fuente9"></div>
                    <input type="text" class="form-control" id="tarea" autofocus>
                    <input id="enviar" type="submit" value="Añadir" class="btn btn-block btn-primary">
                    <div class="row">
                        <div class="col-sm-2">
                            <label class="control-label fuente9 negrita" style="position:relative; top:7px; left:8px;">Filtrar por:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="filtro"  style="position:relative; top:2px;">
                                <option value="0" selected>Selecciona una opcion...</option>
                                <option value="1">Todas Las Tareas</option>
                                <option value="2">Tareas Completadas</option>
                                <option value="3">Tareas No Completadas</option>
                            </select>
                        </div>
                    </div>
                </form>
                <!-- message -->
                <div id="text_message" class=""></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                        <th>Hecho</th>
                        <th>Tarea</th>
                        <th></th>
                        <th></th>
                        </tr>
                    </thead>
                    <tbody id="tareas">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            cargarTareas();
            // Código para filtrar las tareas
            $("#filtro").change(function(){
                var tipo = $(this).val();
                var where = "";
                if(tipo==0){
                    $("#tareas").html("<tr><td colspan='4' align='center'>Debes seleccionar una opción del filtro...</td></tr>");
                    return false;
                }else if(tipo==1){ // Todas las tareas
                    where = " ";
                }else if(tipo==2){ // Tareas completadas
                    where = " WHERE hecho = 1 ";
                }else if(tipo==3){ // Tareas no completadas
                    where = " WHERE hecho = 0 ";
                }
                // Llamada mediante AJAX a la consulta que mostrará las tareas requeridas
                $.ajax({
                    url: "por_hacer/consultar_tareas.php",
                    method: "POST",
                    data: {
                        where: where
                    },
                    type: "html",
                    success: function(response){
                        $("#error_message").html("");
                        $("#tarea").val("");
                        $("#tareas").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            });
            // Código para ingresar una tarea
            $('#form_tasks').submit(function(event){
                event.preventDefault();
                var tarea = $('#tarea').val();
                if(tarea.trim()==""){
                    $("#error_message").html("Debes ingresar el texto de tu tarea...");
                    $("#tarea").focus();
                    return false;
                }
                if($("#enviar").val() == "Añadir"){
                    $.ajax({
                        url: "por_hacer/insertar_tarea.php",
                        method: "POST",
                        data: {
                            tarea: tarea
                        },
                        type: "html",
                        success: function(response){
                            $("#error_message").html("");
                            $('#text_message').html(response);
                            $("#tarea").val("");
                            cargarTareas();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }else if($("#enviar").val() == "Actualizar"){
                    var id = $("#id").val();
                    $.ajax({
                        url: "por_hacer/actualizar_tarea.php",
                        method: "POST",
                        data: {
                            id: id,
                            tarea: tarea
                        },
                        type: "html",
                        success: function(response){
                            $("#error_message").html("");
                            $('#text_message').html(response);
                            $("#tarea").val("");
                            $("#subtitulo").html("Añade una Nueva Tarea");
                            $("#enviar").val("Añadir");
                            cargarTareas();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });
        });
        function cargarTareas(){
            // Obtengo todas las tareas ingresadas en la base de datos
            $.ajax({
                url: "por_hacer/cargar_tareas.php",
                method: "GET",
                type: "html",
                success: function(response){
                    //console.log(response);
                    $("#tareas").html("");
                    $('#tareas').append(response);
                    $('select').val('0');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function editTask(id){
            //Primero obtengo la descripción de la tarea seleccionada
            $.ajax({
                url: "por_hacer/obtener_tarea.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    console.log(response);
                    var tarea = jQuery.parseJSON(response);
                    $("#tarea").val(tarea.tarea);
                    $("#subtitulo").html("Actualiza esta Tarea");
                    $("#enviar").val("Actualizar");
                    $("#id").val(id);
                    $("#tarea").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function deleteTask(id){
            //Elimino la tarea mediante AJAX
            $.ajax({
                url: "por_hacer/eliminar_tarea.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    console.log(response);
                    cargarTareas();
                    $("#tarea").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function checkTask(obj, id){
            var done = obj.checked;
            $.ajax({
                    type: "POST",
                    url: "por_hacer/actualizar_hecho.php",
                    data: "id="+id+"&done="+done,
                    success: function(resultado){
                        cargarTareas(); //Para refrescar la lista de tareas
                }
            });			
        }
    </script>