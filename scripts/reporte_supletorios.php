<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
	$id_paralelo = $_POST["id_paralelo"];
	echo $paralelos->listarCalificacionesSupletoriosSecretaria($id_paralelo, $_SESSION["id_periodo_lectivo"], 2);
?>
