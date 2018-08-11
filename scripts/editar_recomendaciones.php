<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.recomendaciones.php");
	$recomendaciones = new recomendaciones();
	//Recepcion de las variables enviadas mediante POST
	$id_escala_calificaciones = $_POST["id_escala_calificaciones"];
	$id_paralelo_asignatura = $_POST["id_paralelo_asignatura"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$recomendaciones->re_plan_de_mejora = $_POST["plandemejora"];
	echo $recomendaciones->editarRecomendaciones($id_escala_calificaciones,$id_paralelo_asignatura,$id_aporte_evaluacion);
?>