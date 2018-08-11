<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	session_start();
	$selects = new selects();
        $id_periodo_lectivo = $_POST['id_periodo_lectivo'];
        $id_usuario = $_POST['id_usuario'];
	echo $selects->cargarParalelosDocente($id_periodo_lectivo, $id_usuario);
?>