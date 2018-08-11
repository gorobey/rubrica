<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.escalas.php");
	$escala = new escalas();
	echo $escala->actualizarEscala($_POST["id"], $_POST["cualitativa"], $_POST["cuantitativa"], $_POST["minima"], $_POST["maxima"], $_POST["equivalencia"], $_POST["orden"]);
?>
