<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_asignatura.php");
	$tipos_asignatura = new tipos_asignatura();
	$tipos_asignatura->code = $_POST["id_tipo_asignatura"];
	echo $tipos_asignatura->eliminarTipoAsignatura();
?>
