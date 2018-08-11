<?php
	require_once("../clases/class.mysql.php");
	require_once("../clases/class.mensajes.php");
	$mensaje = new mensajes();
    $mensaje->code = $_POST["id"];
    $id_usuario = $_POST["id_usuario"];
    $id_perfil = $_POST["id_perfil"];
    $id_menu = $_POST["id_menu"];
    if ($mensaje->eliminarMensaje()) {
        echo json_encode(array(
                                'error' => false, 
                                'mensaje' => 'admin2.php?id_usuario=' . $id_usuario 
                                            . '&id_perfil=' . $id_perfil 
                                            . '&id_menu=' . $id_menu 
                                            . '&enlace=vistas/administracion/mensajes/list.php'
                                            . '&file_js=vistas/administracion/mensajes/mensajes.js'
                              )
                        );
    }
?>