<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$alineacion = $_POST["alineacion"];
	$dia_semana->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $dia_semana->mostrarDiasSemana($alineacion);
?>
