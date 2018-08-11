<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->code = $_POST["id_tipo_educacion"];
	echo $tipos_educacion->obtenerDatosTipoEducacion();
?>
