<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	$paralelos = new paralelos();
	$id_curso = $_GET["id_curso"];
	echo $paralelos->listarAsignaturasCurso($id_curso);
?>
