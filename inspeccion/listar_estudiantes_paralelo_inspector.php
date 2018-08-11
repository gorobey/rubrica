<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.paralelos.php");
	include("../scripts/clases/class.inspectores.php");
	$inspector = new inspectores();
	$id_curso = $_POST["id_curso"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
        echo $inspector->listarEstudiantesParaleloInspector($id_paralelo, $id_aporte_evaluacion, $id_curso);
?>
