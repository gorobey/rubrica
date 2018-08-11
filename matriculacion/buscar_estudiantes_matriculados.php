<?php
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$estudiantes->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$patron = $_POST["patron"];
	echo $estudiantes->buscarEstudiantesMatriculados($patron);
?>
