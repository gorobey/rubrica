<?php
    sleep(1);
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.horas_clase.php");
    $hora_clase = new horas_clase();
    $hora_clase->id_asignatura = $_POST["id_asignatura"];
    $hora_clase->id_paralelo = $_POST["id_paralelo"];
    $hora_clase->id_dia_semana = $_POST["id_dia_semana"];
    echo $hora_clase->obtenerHorasClase();
?>
