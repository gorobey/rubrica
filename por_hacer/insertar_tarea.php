<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.tareas.php");
    $tarea_descripcion = $_POST['tarea'];
	$tarea = new tareas();
	echo $tarea->insertarTarea($tarea_descripcion);
?>