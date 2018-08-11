<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.tareas.php");
    $id = $_POST['id'];
	$tarea = new tareas();
	echo $tarea->eliminarTarea($id);
?>