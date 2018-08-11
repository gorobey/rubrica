<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.tareas.php");
	$tarea = new tareas();
	echo $tarea->cargarTareas();
?>