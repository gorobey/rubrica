<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_asignatura.php");
	session_start();
	$tipos_asignatura = new tipos_asignatura();
	$tipos_asignatura->code = $_SESSION["id_periodo_lectivo"];
	echo $tipos_asignatura->listar_tipos_asignatura();
?>
