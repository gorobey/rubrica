<?php
    require_once("../scripts/clases/class.mysql.php");
    require_once("../scripts/clases/class.inasistencias.php");
    $inasistencia = new inasistencias();
    echo $inasistencia->mostrarInasistencia("center");
?>