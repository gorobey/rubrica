<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	session_start();
	$paralelos = new paralelos();
        $id_curso = $_POST["id_curso"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];
	echo $paralelos->listarResumenAnual($id_paralelo, $id_asignatura, $_SESSION["id_periodo_lectivo"]);
?>
