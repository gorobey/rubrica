<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aportes_evaluacion = new aportes_evaluacion();
    $aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
    $aportes_evaluacion->id_tipo_asignatura = $_POST["id_tipo_asingatura"];
	echo $aportes_evaluacion->mostrarTitulosRubricasEstudiante();
?>
