<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	echo $paralelos->listarCalificacionesDeGraciaParalelo($id_paralelo, $id_asignatura, $_SESSION["id_periodo_lectivo"],4);
?>
