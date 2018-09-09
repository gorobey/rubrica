    <div class="container">
        <div id="div_usuarios" class="col-sm-10 col-sm-offset-1">
            <h2>Usuarios</h2>
            <input type="hidden" id="id_usuario">
            <!-- panel -->
            <div class="panel panel-default">
                <h3 id="subtitulo" class="text-center">Selecciona un Perfil</h3>
                <form id="form_menus" action="" class="app-form">
                    <select id="cboPerfiles" class="form-control">
                        <option value="0">Seleccione ...</option>
                    </select>
                    <div class="col-sm-3">
                        <button id="btn-new" type="button" class="btn btn-block btn-primary" style="position:relative; top:7px;" data-toggle="modal" data-target="#addnew">
                            Nuevo
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" id="search_user" class="form-control" style="position:relative; top:7px;" placeholder="Escriba el nombre del usuario a ser asociado...">
                    </div>
                    <div class="col-sm-3">
                        <button id="btn-assoc" type="button" class="btn btn-block btn-success" style="position:relative; top:7px;" onclick="asociarUsuarioPerfil()">
                            Asociar
                        </button>
                    </div>
                </form>
                <!-- message -->
                <div id="text_message" class="fuente9 text-center" style="position: relative; top: 7px;"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                        <th>Nro.</th>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Activo</th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Eliminar --></th>
                        <th><!-- Botón Des-Asociar --></th>
                        </tr>
                    </thead>
                    <tbody id="usuarios">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel">Nuevo Usuario</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="frm_usuario" action="" method="post">
                                <div class="form-group has-feedback">
                                    <label for="new_us_titulo">Título:</label>
                                    <input type="text" class="form-control" id="new_us_titulo" value="">
                                    <span class="help-desk error" id="mensaje1"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="new_us_apellidos">Apellidos:</label>
                                    <input type="text" class="form-control" id="new_us_apellidos" value="">
                                    <span class="help-desk error" id="mensaje2"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="new_us_nombres">Nombres:</label>
                                    <input type="text" class="form-control" id="new_us_nombres" value="">
                                    <span class="help-desk error" id="mensaje3"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="new_us_login">Usuario:</label>
                                    <input type="text" class="form-control" id="new_us_login" value="">
                                    <span class="help-desk error" id="mensaje4"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="new_us_password">Clave:</label>
                                    <input type="text" class="form-control" id="new_us_password" value="">
                                    <span class="help-desk error" id="mensaje5"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addUsuario()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel">Editar Usuario</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="frm_edit_usuario" action="" method="post">
                                <div class="form-group has-feedback">
                                    <label for="edit_us_titulo">Título:</label>
                                    <input type="text" class="form-control" id="edit_us_titulo" value="">
                                    <span class="help-desk error" id="message1"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="edit_us_apellidos">Apellidos:</label>
                                    <input type="text" class="form-control" id="edit_us_apellidos" value="">
                                    <span class="help-desk error" id="message2"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="edit_us_nombres">Nombres:</label>
                                    <input type="text" class="form-control" id="edit_us_nombres" value="">
                                    <span class="help-desk error" id="message3"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="edit_us_login">Usuario:</label>
                                    <input type="text" class="form-control" id="edit_us_login" value="">
                                    <span class="help-desk error" id="message4"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="edit_us_password">Clave:</label>
                                    <input type="text" class="form-control" id="edit_us_password" value="">
                                    <span class="help-desk error" id="message5"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="edit_us_activo">Activo:</label>
                                    <select class="form-control" id="edit_us_activo">
                                        <!-- Aquí se desplegará el campo us_activo -->
                                    </select>
                                    <span class="help-desk error" id="message6"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateUsuario()"><span class="glyphicon glyphicon-floppy-disk"></span> Editar</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            $("#btn-new").attr("disabled",true);
            $("#btn-assoc").attr("disabled",true);
            cargar_perfiles();
            $("#cboPerfiles").change(function(e){
                // Código para recuperar los usuarios asociados al perfil seleccionado
                listarUsuarios();
            });
            $("#search_user").autocomplete({
                source:function(request,response){
                    $.ajax({
                        url: 'usuario/obtener_usuarios.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {valor: request.term},
                        success: function(data){
                            console.log(data);
                            response(data);
                        }
                    });
                    
                },
                minLength:2,
                select:function(event,ui){
                    $("#id_usuario").val(ui.item.id);
                },
            });
            $("#usuarios").html("<tr><td colspan='8' align='center'>Debes seleccionar un perfil...</td></tr>");
        });
        function cargar_perfiles()
        {
            $.get("scripts/cargar_perfiles.php", function(resultado){
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $('#cboPerfiles').append(resultado);
                }
            });	
        }
        function listarUsuarios()
        {
            var id_perfil = document.getElementById("cboPerfiles").value;
            $("#text_message").html("");
            if(id_perfil==0){
                $("#usuarios").html("<tr><td colspan='7' align='center'>Debes seleccionar un perfil...</td></tr>");
                $("#btn-new").attr("disabled",true);
                $("#btn-assoc").attr("disabled",true);
            }else{
                $("#btn-new").attr("disabled",false);
                $("#btn-assoc").attr("disabled",false);
                $.get("usuario/listar_usuarios.php", { id_perfil: id_perfil },
                    function(resultado)
                    {
                        if(resultado == false)
                        {
                            alert("Error");
                        }
                        else
                        {
                            $("#usuarios").html(resultado);
                        }
                    }
                );
            }
        }
        function addUsuario(){
            var id_perfil = $("#cboPerfiles").val();
            var us_titulo = $("#new_us_titulo").val();
            var us_apellidos = $("#new_us_apellidos").val();
            var us_nombres = $("#new_us_nombres").val();
            var us_login = $("#new_us_login").val();
            var us_password = $("#new_us_password").val();

            var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
            var reg_username = /^[a-z\d_]{4,15}$/i;
            var reg_titulo = /^([a-zA-Z.]{3,5})$/i;

            var cont_errores = 0;

            if(us_titulo.trim()==""){
                $("#mensaje1").html("Debe ingresar el título del Usuario");
                $("#mensaje1").fadeIn("slow");
                cont_errores++;
            }else if(!reg_titulo.test(us_titulo)){
                $("#mensaje1").html("Debe ingresar un título válido para el Usuario");
                $("#mensaje1").fadeIn("slow");
                cont_errores++;
            }else {
                $("#mensaje1").fadeOut();
            }

            if(us_apellidos.trim()==""){
                $("#mensaje2").html("Debe ingresar los apellidos del Usuario");
                $("#mensaje2").fadeIn("slow");
                cont_errores++;
            }else if(!reg_texto.test(us_apellidos)){
                $("#mensaje2").html("Debe ingresar apellidos válidos para el Usuario");
                $("#mensaje2").fadeIn("slow");
                cont_errores++;
            }else {
                $("#mensaje2").fadeOut();
            }

            if(us_nombres.trim()==""){
                $("#mensaje3").html("Debe ingresar los nombres del Usuario");
                $("#mensaje3").fadeIn("slow");
                cont_errores++;
            }else if(!reg_texto.test(us_nombres)){
                $("#mensaje3").html("Debe ingresar nombres válidos para el Usuario");
                $("#mensaje3").fadeIn("slow");
                cont_errores++;
            }else {
                $("#mensaje3").fadeOut();
            }

            if(us_login.trim()==""){
                $("#mensaje4").html("Debe ingresar el nombre de usuario");
                $("#mensaje4").fadeIn("slow");
                cont_errores++;
            }else if(!reg_username.test(us_login)){
                $("#mensaje4").html("Debe ingresar un nombre de usuario válido");
                $("#mensaje4").fadeIn("slow");
                cont_errores++;
            }else {
                $("#mensaje4").fadeOut();
            }

            if(us_password.trim()==""){
                $("#mensaje5").html("Debe ingresar la contraseña del Usuario");
                $("#mensaje5").fadeIn("slow");
                cont_errores++;
            }else if(!reg_username.test(us_password)){
                $("#mensaje5").html("Debe ingresar una contraseña válida");
                $("#mensaje5").fadeIn("slow");
                cont_errores++;
            }else {
                $("#mensaje5").fadeOut();
            }

            if(cont_errores==0){
                $.ajax({
                    url: "usuario/insertar_usuario.php",
                    method: "POST",
                    type: "html",
                    data: {
                        id_perfil: id_perfil,
                        us_titulo: us_titulo,
                        us_apellidos: us_apellidos,
                        us_nombres: us_nombres,
                        us_login: us_login,
                        us_password: us_password
                    },
                    success: function(response){
                        listarUsuarios();
                        $('#addnew').modal('hide');
                        $("#text_message").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        }
        function updateUsuario(){
            var id_usuario = $("#id_usuario").val();
            var id_perfil = $("#cboPerfiles").val();
            var us_titulo = $("#edit_us_titulo").val();
            var us_apellidos = $("#edit_us_apellidos").val();
            var us_nombres = $("#edit_us_nombres").val();
            var us_login = $("#edit_us_login").val();
            var us_password = $("#edit_us_password").val();
            var us_activo = $("#edit_us_activo").val();

            var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
            var reg_username = /^[a-z\d_]{4,15}$/i;
            var reg_titulo = /^([a-zA-Z.]{3,5})$/i;

            var cont_errores = 0;

            if(us_titulo.trim()==""){
                $("#message1").html("Debe ingresar el título del Usuario");
                $("#message1").fadeIn("slow");
                cont_errores++;
            }else if(!reg_titulo.test(us_titulo)){
                $("#message1").html("Debe ingresar un título válido para el Usuario");
                $("#message1").fadeIn("slow");
                cont_errores++;
            }else {
                $("#message1").fadeOut();
            }

            if(us_apellidos.trim()==""){
                $("#message2").html("Debe ingresar los apellidos del Usuario");
                $("#message2").fadeIn("slow");
                cont_errores++;
            }else if(!reg_texto.test(us_apellidos)){
                $("#message2").html("Debe ingresar apellidos válidos para el Usuario");
                $("#message2").fadeIn("slow");
                cont_errores++;
            }else {
                $("#message2").fadeOut();
            }

            if(us_nombres.trim()==""){
                $("#message3").html("Debe ingresar los nombres del Usuario");
                $("#message3").fadeIn("slow");
                cont_errores++;
            }else if(!reg_texto.test(us_nombres)){
                $("#message3").html("Debe ingresar nombres válidos para el Usuario");
                $("#message3").fadeIn("slow");
                cont_errores++;
            }else {
                $("#message3").fadeOut();
            }

            if(us_login.trim()==""){
                $("#message4").html("Debe ingresar el nombre de usuario");
                $("#message4").fadeIn("slow");
                cont_errores++;
            }else if(!reg_username.test(us_login)){
                $("#message4").html("Debe ingresar un nombre de usuario válido");
                $("#message4").fadeIn("slow");
                cont_errores++;
            }else {
                $("#message4").fadeOut();
            }

            if(us_password.trim()==""){
                $("#message5").html("Debe ingresar la contraseña del Usuario");
                $("#message5").fadeIn("slow");
                cont_errores++;
            }else if(!reg_username.test(us_password)){
                $("#message5").html("Debe ingresar una contraseña válida");
                $("#message5").fadeIn("slow");
                cont_errores++;
            }else {
                $("#message5").fadeOut();
            }

            if(cont_errores==0){
                $.ajax({
                    url: "usuario/actualizar_usuario.php",
                    method: "POST",
                    type: "html",
                    data: {
                        id_usuario: id_usuario,
                        id_perfil: id_perfil,
                        us_titulo: us_titulo,
                        us_apellidos: us_apellidos,
                        us_nombres: us_nombres,
                        us_login: us_login,
                        us_password: us_password,
                        us_activo: us_activo
                    },
                    success: function(response){
                        listarUsuarios();
                        $('#editUser').modal('hide');
                        $("#text_message").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        }
        function limpiarUsuario()
        {
            $("#edit_us_titulo").val("");
            $("#edit_us_apellidos").val("");
            $("#edit_us_nombres").val("");
            $("#edit_us_login").val("");
            $("#edit_us_password").val("");
        }
        function editarUsuario(id_usuario)
        {
            limpiarUsuario();
            $.ajax({
                type: "POST",
                url: "usuario/obtener_usuario.php",
                data: "id_usuario="+id_usuario,
                success: function(resultado){
                    var usuario = eval('(' + resultado + ')');
                    //console.log(usuario);
                    $("#id_usuario").val(usuario.id_usuario);
                    $("#edit_us_titulo").val(usuario.us_titulo);
                    $("#edit_us_apellidos").val(usuario.us_apellidos);
                    $("#edit_us_nombres").val(usuario.us_nombres);
                    $("#edit_us_login").val(usuario.us_login);
                    $("#edit_us_password").val(usuario.us_password);
                    document.getElementById("edit_us_activo").length = 0;
                    html0 = "<option value='1'";
                    html1 = (usuario.us_activo=='1')? ' selected': '';
                    $("#edit_us_activo").append(html0+html1+">Sí</option>");
                    html0 = "<option value='0'";
                    html1 = (usuario.us_activo=='0')? ' selected': '';
                    $("#edit_us_activo").append(html0+html1+">No</option>");
                    $('#editUser').modal('show');
                }
            });
        }
        function asociarUsuarioPerfil()
        {
            // Valores de los parámetros que se van a asociar
            var usuario = $("#search_user").val();

            if (usuario.trim()=="") {
                alert("Debes \"buscar\" el usuario que va a ser asociado...");
                $("#search_user").focus();
            } else {
                var id_usuario = $("#id_usuario").val();
                var id_perfil = $("#cboPerfiles").val();
                $.ajax({
                    url: "usuario/asociar_usuario_perfil.php",
                    method: "POST",
                    type: "html",
                    data: {
                        id_usuario: id_usuario,
                        id_perfil: id_perfil
                    },
                    success: function(response){
                        listarUsuarios();
                        $("#text_message").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        }
        function desasociarUsuarioPerfil(id_usuario)
        {
            var id_perfil = $("#cboPerfiles").val();
            $.ajax({
                url: "usuario/desasociar_usuario_perfil.php",
                method: "POST",
                type: "html",
                data: {
                    id_usuario: id_usuario,
                    id_perfil: id_perfil
                },
                success: function(response){
                    listarUsuarios();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    </script>