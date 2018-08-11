<?php

    require_once "../../clases/Conexion.php";
    require_once "../../clases/Periodos_lectivos.php";
    $obj= new conectar();
    $conexion=$obj->conexion();

    if($conexion){
        $periodo_lectivo = new periodos_lectivos();
        echo $periodo_lectivo->cargarPeriodosLectivos($conexion);
    }else{
        echo "No se pudo conectar";
    }

?>