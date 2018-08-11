<?php
	sleep(1);
	session_start();
	require_once("clases/class.mysql.php");
	require_once("clases/class.estudiantes.php");
	$estudiante = new estudiantes();
	//recibo las variables de tipo post de la pagina login.php
	$cedula = $_POST['cedula'];
	$id_periodo_lectivo = $_POST['id_periodo_lectivo'];
	$captcha = sha1($_POST["captcha"]);
	//$cookie_captcha = $_COOKIE["captcha"];
	$session_captcha = $_SESSION['captcha'];
	//validacion del codigo captcha
	if ($captcha != $session_captcha) {
		echo json_encode(array('error' => true));
	} else {
		//consultar a la tabla sw_estudiante si existe un estudiante coincidente en la tabla
		$_SESSION['usuario_logueado'] = false;
		if ($estudiante->existeEstudiante($cedula, $id_periodo_lectivo)) {
			$id_estudiante = $estudiante->obtenerIdEstudianteCedula($cedula, $id_periodo_lectivo);
			$_SESSION['usuario_logueado'] = true;
			$_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
			$_SESSION['id_estudiante'] = $id_estudiante;
			echo json_encode(array('id_estudiante' => $id_estudiante));
			//setcookie("captcha","",time()-3600);
		} else {
			echo json_encode(array('error' => true));
		}
	}
?>