<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.tareas.php");
    $where = $_POST["where"];
	$tarea = new tareas();
	echo $tarea->consultarTareas($where);
?>