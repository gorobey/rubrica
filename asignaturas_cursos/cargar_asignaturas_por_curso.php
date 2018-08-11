<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	session_start();
	$asignaturas = new selects();
	$id_curso = $_POST["id_curso"];
	echo $asignaturas->cargarAsignaturasPorCurso($id_curso);
?>