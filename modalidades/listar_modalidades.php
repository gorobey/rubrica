<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.modalidades.php");
	$modalidad = new modalidades();
	echo $modalidad->listarModalidades();
?>
