<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$cursos = new cursos();
	$cursos->code = $_POST["id_curso"];
	$cursos->id_especialidad = $_POST["id_especialidad"];
	$cursos->cu_nombre = $_POST["cu_nombre"];
	$cursos->cu_superior = $_POST["cu_superior"];
        $cursos->bol_proyectos = $_POST["bol_proyectos"];
	echo $cursos->actualizarCurso();
?>
