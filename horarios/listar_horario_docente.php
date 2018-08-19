<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$id_usuario = $_SESSION["id_usuario"];
	$id_dia_semana = $_GET["id_dia_semana"];
	echo $horario->listarHorarioDocente($id_usuario, $id_dia_semana);
?>
