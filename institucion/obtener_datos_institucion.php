<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.institucion.php");
	$institucion = new institucion();
	echo $institucion->obtenerDatosInstitucion();
?>
