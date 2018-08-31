<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	session_start();
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$tipos_educacion->te_nombre = $_POST["te_nombre"];
	echo $tipos_educacion->insertarNivelEducacion();
?>
