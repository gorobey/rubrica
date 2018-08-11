<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubricas_evaluacion = new rubricas_evaluacion();
	$rubricas_evaluacion->id_aporte_evaluacion = $_GET["id_aporte_evaluacion"];
	echo $rubricas_evaluacion->listarRubricasEvaluacionProyectos();
?>
