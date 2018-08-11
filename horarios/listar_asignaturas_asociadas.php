<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$id_paralelo = $_GET["id_paralelo"];
	$id_dia_semana = $_GET["id_dia_semana"];
	echo $horario->listarHorarioParalelo($id_paralelo, $id_dia_semana);
?>
