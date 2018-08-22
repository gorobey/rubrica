<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$curso = new cursos();
	$curso->code = $_POST["id_curso"];
	$curso->id_especialidad = $_POST["id_especialidad"];
	$curso->cu_nombre = $_POST["cu_nombre"];
        $curso->bol_proyectos = $_POST["bol_proyectos"];
	$curso->cu_abreviatura = $_POST["cu_abreviatura"];
	echo $curso->actualizarCurso();
?>
