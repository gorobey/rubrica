<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.inspectores.php");
    session_start();
    $inspector = new inspectores();
    $inspector->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $inspector->cargarParalelosInspectores();
?>
