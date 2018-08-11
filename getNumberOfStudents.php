<?php
    require_once("scripts/clases/class.mysql.php");
    require_once("scripts/clases/class.estudiantes.php");
    $id_periodo_lectivo = $_POST["id_periodo_lectivo"];
    $estudiante = new estudiantes();
    $resultado = $estudiante->getNumeroEstudiantesPorParalelo($id_periodo_lectivo);
    echo $resultado;
?>