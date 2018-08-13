<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	session_start();
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->code = $_SESSION["id_periodo_lectivo"];
	echo $tipos_educacion->cargar_tipos_educacion();
?>
