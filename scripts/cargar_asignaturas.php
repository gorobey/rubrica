<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$select = new selects();
	echo $select->cargarAsignaturas();
?>