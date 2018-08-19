<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->code = $_POST["id_tipo_educacion"];
    $tipos_educacion->te_nombre = $_POST["te_nombre"];
    $tipos_educacion->te_bachillerato = $_POST["te_bachillerato"];
	echo $tipos_educacion->actualizarNivelEducacion();
?>
