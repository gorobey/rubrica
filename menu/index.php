    <div class="container">
        <div id="div_menus" class="col-sm-10 col-sm-offset-1">
            <h2>Menus</h2>
            <input type="hidden" id="id_menu">
            <!-- panel -->
            <div class="panel panel-default">
                <h4 id="subtitulo" class="text-center">Selecciona un Perfil</h4>
                <form id="form_menus" action="" class="app-form">
                    <select id="cboPerfiles" class="form-control">
                        <option value="0">Seleccione ...</option>
                    </select>
                    <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                        Nuevo Men&uacute;
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
                        <th>Publicado</th>
                        <th><!-- Botón Submenús --></th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Borrar --></th>
                        <th><!-- Botón Subir --></th>
                        <th><!-- Botón Bajar --></th>
                        </tr>
                    </thead>
                    <tbody id="menus">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
        <div id="div_submenus" class="col-sm-10 col-sm-offset-1">
            <h2>SubMenus</h2>
            <input type="hidden" id="mnu_padre">
            <!-- panel -->
            <div class="panel panel-default">
                <form id="form_submenus" action="" class="app-form">
                    <button id="btn-new-submenu" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addNewSubmenu">
                        Nuevo SubMen&uacute;
                    </button>
                </form>
                <!-- message -->
                <div id="text_message2" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Publicado</th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Borrar --></th>
                        <th><!-- Botón Subir --></th>
                        <th><!-- Botón Bajar --></th>
                        </tr>
                    </thead>
                    <tbody id="submenus">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- New Menu Modal -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel1">Nuevo Men&uacute;</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Texto:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_mnu_texto" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Enlace:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_mnu_enlace" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Publicado:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_mnu_publicado">
                                <option value="1">S&iacute;</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addMenu()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- New Sub Menu Modal -->
    <div class="modal fade" id="addNewSubmenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel2">Nuevo Sub Men&uacute;</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Texto:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_submnu_texto" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Enlace:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_submnu_enlace" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Publicado:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_submnu_publicado">
                                <option value="1">S&iacute;</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addSubMenu()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>    
    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel3">Editar Men&uacute;</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Texto:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_mnu_texto" name="mnu_texto" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Enlace:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_mnu_enlace" name="mnu_enlace" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Publicado:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_mnu_publicado" name="mnu_publicado">
                                <option value="1">S&iacute;</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateMenu()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Sub Menu Modal -->
    <div class="modal fade" id="editSubMenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel4">Editar Sub Men&uacute;</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Texto:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_submnu_texto" name="mnu_texto" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Enlace:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_submnu_enlace" name="mnu_enlace" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Publicado:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_submnu_publicado" name="mnu_publicado">
                                <option value="1">S&iacute;</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateSubMenu()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            // JQuery Listo para utilizar
            $("#btn-new").attr("disabled","true");
            $("#btn-new-submenu").attr("disabled","true");
            cargar_perfiles();
            $("#cboPerfiles").change(function(e){
                // Código para recuperar los menús asociados al perfil seleccionado
                listarMenus();
            });           
            $("#menus").html("<tr><td colspan='7' align='center'>Debes seleccionar un perfil...</td></tr>");
            $("#submenus").html("<tr><td colspan='6' align='center'>Debes seleccionar un menú...</td></tr>");
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
        function listarMenus()
        {
            var id_perfil = $("#cboPerfiles").val();
            $("#submenus").html("<tr><td colspan='6' align='center'>Debes seleccionar un menú...</td></tr>");
            if(id_perfil==0){
                $("#menus").html("<tr><td colspan='7' align='center'>Debes seleccionar un perfil...</td></tr>");
                $("#btn-new").attr("disabled",true);
            }else{
                $("#btn-new").attr("disabled",false);
                $.get("menu/listar_menus.php", 
                    { 
                        id_perfil: id_perfil
                    },
                    function(resultado)
                    {
                        if(resultado == false)
                        {
                            alert("Error");
                        }
                        else
                        {
                            $("#menus").html(resultado);
                        }
                    }
                );
            }
        }
        function addMenu(){
            var id_perfil = $("#cboPerfiles").val();
            var mnu_texto = $("#new_mnu_texto").val();
            var mnu_enlace = $("#new_mnu_enlace").val();
            var mnu_publicado = $("#new_mnu_publicado").val();
            $.ajax({
                url: "menu/insertar_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id_perfil: id_perfil,
                    mnu_texto: mnu_texto,
                    mnu_enlace: mnu_enlace,
                    mnu_publicado: mnu_publicado
                },
                success: function(response){
                    listarMenus();
                    $('#addnew').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function addSubMenu(){
            var id_perfil = $("#cboPerfiles").val();
            var mnu_texto = $("#new_submnu_texto").val();
            var mnu_enlace = $("#new_submnu_enlace").val();
            var mnu_publicado = $("#new_submnu_publicado").val();
            var mnu_padre = $("#mnu_padre").val();
            $("#text_message2").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "menu/insertar_submenu.php",
                method: "POST",
                type: "html",
                data: {
                    id_perfil: id_perfil,
                    mnu_texto: mnu_texto,
                    mnu_enlace: mnu_enlace,
                    mnu_publicado: mnu_publicado,
                    mnu_padre: mnu_padre
                },
                success: function(response){
                    $('#addNewSubmenu').modal('hide');
                    $("#text_message2").html(response);
                    listarSubmenus(mnu_padre);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function editMenu(id){
            //Primero obtengo los datos editables del menú seleccionado
            $("#id_menu").val(id);
            $.ajax({
                url: "menu/obtener_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    var menu = jQuery.parseJSON(response);
                    $("#edit_mnu_texto").val(menu.mnu_texto);
                    $("#edit_mnu_enlace").val(menu.mnu_enlace);
                    var mnu_publicado = menu.mnu_publicado;
                    document.getElementById("edit_mnu_publicado").length = 0;
                    var html1 = '<option value="1"';
                    var selected = (mnu_publicado == 1)? ' selected': '';
                    var html2 = '>S&iacute;</option>';
                    $('#edit_mnu_publicado').append(html1+selected+html2);
                    var html1 = '<option value="0"';
                    var selected = (mnu_publicado == 0)? ' selected': '';
                    var html2 = '>No</option>';
                    $('#edit_mnu_publicado').append(html1+selected+html2);
                    $('#editMenu').modal('show');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); 
        }
        function editSubMenu(id){
            //Primero obtengo los datos editables del menú seleccionado
            $("#id_menu").val(id);
            $.ajax({
                url: "menu/obtener_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    var menu = jQuery.parseJSON(response);
                    $("#edit_submnu_texto").val(menu.mnu_texto);
                    $("#edit_submnu_enlace").val(menu.mnu_enlace);
                    var mnu_publicado = menu.mnu_publicado;
                    document.getElementById("edit_submnu_publicado").length = 0;
                    var html1 = '<option value="1"';
                    var selected = (mnu_publicado == 1)? ' selected': '';
                    var html2 = '>S&iacute;</option>';
                    $('#edit_submnu_publicado').append(html1+selected+html2);
                    var html1 = '<option value="0"';
                    var selected = (mnu_publicado == 0)? ' selected': '';
                    var html2 = '>No</option>';
                    $('#edit_submnu_publicado').append(html1+selected+html2);
                    $('#editSubMenu').modal('show');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); 
        }
        function updateMenu() {
            var id = $("#id_menu").val();
            var id_perfil = $("#cboPerfiles").val();
            var texto = $("#edit_mnu_texto").val();
            var enlace = $("#edit_mnu_enlace").val();
            var publicado = $("#edit_mnu_publicado").val();
            $.ajax({
                url: "menu/actualizar_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id,
                    id_perfil: id_perfil,
                    texto: texto,
                    enlace: enlace,
                    publicado: publicado
                },
                success: function(response){
                    $("#text_message").html(response);
                    listarMenus();
                    $('#editMenu').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function updateSubMenu() {
            var id = $("#id_menu").val();
            var texto = $("#edit_submnu_texto").val();
            var enlace = $("#edit_submnu_enlace").val();
            var publicado = $("#edit_submnu_publicado").val();
            var mnu_padre = $("#mnu_padre").val();
            $.ajax({
                url: "menu/actualizar_submenu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id,
                    texto: texto,
                    enlace: enlace,
                    publicado: publicado
                },
                success: function(response){
                    $("#editSubMenu").modal("hide");
                    $("#text_message2").html(response);
                    listarSubmenus(mnu_padre);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function eliminarMenu(id){
            //Elimino el menu mediante AJAX
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "menu/eliminar_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    $("#text_message").html(response);
                    listarMenus();
                    $("#cboPerfiles").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function eliminarSubMenu(id){
            //Elimino el submenu mediante AJAX
            var mnu_padre = $("#mnu_padre").val();
            $("#text_message2").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
            $.ajax({
                url: "menu/eliminar_menu.php",
                method: "POST",
                type: "html",
                data: {
                    id: id
                },
                success: function(response){
                    $("#text_message2").html(response);
                    listarSubmenus(mnu_padre);
                    $("#cboPerfiles").focus();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
        function subirMenu(id_menu, id_perfil)
        {
            if (id_menu == "" || id_perfil == "") {
                document.getElementById("text_message").innerHTML = "No se han pasado correctamente los par&aacute;metros id_menu e id_perfil...";
            } else {
                $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                        type: "POST",
                        url: "menu/subir_menu.php",
                        data: "id_menu="+id_menu+"&id_perfil="+id_perfil,
                        success: function(resultado){
                            $("#text_message").html(resultado);
                            listarMenus();
                    }
                });			
            }	
        }
        function subirSubmenu(id_menu, mnu_padre)
        {
            if (id_menu == "" || mnu_padre == "") {
                document.getElementById("text_message").innerHTML = "No se han pasado correctamente los par&aacute;metros id_menu e mnu_padre...";
            } else {
                $("#text_message2").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                        type: "POST",
                        url: "menu/subir_submenu.php",
                        data: "id_menu="+id_menu+"&mnu_padre="+mnu_padre,
                        success: function(resultado){
                            $("#text_message2").html(resultado);
                            listarSubmenus(mnu_padre);
                    }
                });			
            }	
        }
        function bajarSubmenu(id_menu, mnu_padre)
        {
            if (id_menu == "" || mnu_padre == "") {
                document.getElementById("text_message").innerHTML = "No se han pasado correctamente los par&aacute;metros id_menu e mnu_padre...";
            } else {
                $("#text_message2").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                        type: "POST",
                        url: "menu/bajar_submenu.php",
                        data: "id_menu="+id_menu+"&mnu_padre="+mnu_padre,
                        success: function(resultado){
                            $("#text_message2").html(resultado);
                            listarSubmenus(mnu_padre);
                    }
                });			
            }	
        }
        function bajarMenu(id_menu, id_perfil)
        {
            if (id_menu == "" || id_perfil == "") {
                document.getElementById("text_message").innerHTML = "No se han pasado correctamente los par&aacute;metros id_menu e id_perfil...";
            } else {
                $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                        type: "POST",
                        url: "menu/bajar_menu.php",
                        data: "id_menu="+id_menu+"&id_perfil="+id_perfil,
                        success: function(resultado){
                        $("#text_message").html(resultado);
                        listarMenus();
                    }
                });			
            }	
        }
        function listarSubmenus(id){
            if(id==""){
                $("#text-message2").html("No se ha pasado correctamente el par&aacute;metro id_menu...");
                $("#btn-new-submenu").attr("disabled",true);
            }else{
                $("#mnu_padre").val(id);
                $("#btn-new-submenu").attr("disabled",false);
                $.get("menu/listar_submenus.php", 
                    { 
                        id: id
                    },
                    function(resultado)
                    {
                        if(resultado == false)
                        {
                            alert("Error");
                        }
                        else
                        {
                            $("#submenus").html(resultado);
                        }
                    }
                );
            }
        }
    </script>