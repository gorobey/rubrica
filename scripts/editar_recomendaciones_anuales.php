<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.recomendaciones.php");
	session_start();
	$recomendaciones = new recomendaciones();
	//Recepcion de las variables enviadas mediante POST
	$id_escala_calificaciones = $_POST["id_escala_calificaciones"];
	$id_paralelo_asignatura = $_POST["id_paralelo_asignatura"];
	$recomendaciones->re_plan_de_mejora = $_POST["plandemejora"];
	$recomendaciones->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $recomendaciones->editarPlanMejoraAnual($id_escala_calificaciones,$id_paralelo_asignatura,$id_periodo_lectivo);
?>