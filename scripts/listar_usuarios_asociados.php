<?php
    require_once 'clases/class.mysql.php';
    require_once 'clases/class.usuarios.php';
    $usuario = new usuarios();
    $usuario->id_perfil = $_POST["id_perfil"];
    echo $usuario->listarUsuariosAsociados();
?>
