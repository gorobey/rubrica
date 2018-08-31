<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	session_start();
	$nivel_educacion = new tipos_educacion();
	$nivel_educacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    $nivel_educacion->te_nombre = $_POST["te_nombre"];
    $nivel_educacion->te_bachillerato = $_POST["te_bachillerato"];
	echo $nivel_educacion->insertarNivelEducacion();
?>
