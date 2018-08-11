<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.tareas.php");
    $id = $_POST['id'];
    $tarea_descripcion = $_POST['tarea'];
	$tarea = new tareas();
	echo $tarea->actualizarTarea($id, $tarea_descripcion);
?>