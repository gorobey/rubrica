<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.recomendaciones.php");
	$recomendaciones = new recomendaciones();
	//Recepcion de las variables enviadas mediante POST
	$id_escala_calificaciones = $_POST["id_escala_calificaciones"];
	$id_paralelo_asignatura = $_POST["id_paralelo_asignatura"];
	$recomendaciones->re_plan_de_mejora = $_POST["plandemejora"];
	echo $recomendaciones->editarPlanMejoraAnual($id_escala_calificaciones,$id_paralelo_asignatura);
?>