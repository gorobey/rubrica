$("#form_mensajes").on("submit", function(event){
    event.preventDefault();
    
    // Aqui valido los campos del formulario
    var texto = $("#texto").val();
    var id_usuario = "<?php echo $id_usuario; ?>";
    var id_perfil = "<?php echo $id_perfil; ?>";

    if (texto == null || texto.length == 0 || /^\s+$/.test(texto)) {
        $("#texto").parent().attr("class","form-group has-error");
        $("#texto").parent().children("span").text("Debe ingresar el texto del mensaje.");
        return false;
    }

    // Aqui debo ingresar el texto mediante AJAX
    $.ajax({
        url: "scripts/mensajes/store.php",
        type: "POST",
        data: {
            texto: texto,
            id_usuario: id_usuario,
            id_perfil: id_perfil
        },
        success: function(data){
            alert(data);
            // Si se insertó correctamente, redirecciono al listado de mensajes
            <?php isset($_GET['id_menu'])? $str_id_menu = '&id_menu=' . $_GET['id_menu'] : $str_id_menu = '' ?>
            window.location = "<?php echo 'admin2.php?id_usuario=' . $id_usuario . '&id_perfil=' . $id_perfil . $str_id_menu . '&enlace=vistas/administracion/mensajes/list.php&file_js=vistas/administracion/mensajes/mensajes.js' ?>";
        },
        error: function(request, status, error){
            // Aqui envio a la consola el error generado
            console.log(request.responseText);
        }
    });
})
