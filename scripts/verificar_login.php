<?php
	sleep(1);
	session_start();
	require_once("clases/class.mysql.php");
	require_once("clases/class.usuarios.php");
	$usuarios = new usuarios();
	//recibo las variables de tipo post de la pagina login.php
	$login = $_POST['uname'];
	$clave = $_POST['passwd'];
	$id_periodo_lectivo = $_POST['cboPeriodo'];
	$id_perfil = $_POST['cboPerfil'];
	//consultar a la tabla usuario si existe un usuario en la tabla
	$_SESSION['usuario_logueado'] = false;
	if ($usuarios->existeUsuario($login, $clave, $id_perfil)) {
	    $id_usuario = $usuarios->obtenerIdUsuario($login, $clave, $id_perfil);
		$_SESSION['usuario_logueado'] = true;
		$_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
		$_SESSION['id_usuario'] = $id_usuario;
		echo json_encode(array('id_usuario' => $id_usuario));
	} else {
		echo json_encode(array('error' => true));
	}
?>