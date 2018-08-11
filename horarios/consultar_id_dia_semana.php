<?php
    sleep(1);
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.dias_semana.php");
    $dias_semana = new dias_semana();
    $dias_semana->ds_ordinal = $_POST["ds_ordinal"];
    $dias_semana->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
    echo $dias_semana->obtenerIdDiaSemana();
?>
