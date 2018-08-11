<?php
	require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.tareas.php");
    $id = $_POST['id'];
    $hecho = $_POST['done'];
	$tarea = new tareas();
	echo $tarea->actualizarCampoHecho($id, $hecho);
?>