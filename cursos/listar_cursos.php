<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$cursos = new cursos();
	$cursos->code = $_GET["id_especialidad"];
	echo $cursos->listarCursos();
?>
