<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.escalas.php");
	session_start();
	$cualitativa = $_POST["cualitativa"];
	$cuantitativa = $_POST["cuantitativa"];
	$minima = $_POST["minima"];
	$maxima = $_POST["maxima"];
	$equivalencia = $_POST["equivalencia"];
	$orden = $_POST["orden"];
	$escala = new escalas();
	echo $escala->insertarEscala($_SESSION["id_periodo_lectivo"], $cualitativa, $cuantitativa, $minima, $maxima, $equivalencia, $orden);
?>
