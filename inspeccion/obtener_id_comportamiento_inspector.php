<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.inspectores.php");
    $id_paralelo = $_POST["id_paralelo"];
    $id_estudiante = $_POST["id_estudiante"];
    $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
    $inspector = new inspectores();
    echo $inspector->obtenerIdComportamientoInspector($id_paralelo, $id_estudiante, $id_aporte_evaluacion);
?>
