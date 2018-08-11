<?php 

//$<span class="hiddenSpellError" pre=""-->sql = "SQL" //ejemplo frutería: SELECT id_fruta,nombre_fruta,cantidad FROM tabla_fruta;

function connectDB(){

        $server = "localhost";
        $user = "colegion_1";
        $pass = "AQSWDE123";
        $bd = "colegion_1";

    $conexion = mysqli_connect($server, $user, $pass,$bd);

        if($conexion){
            echo 'La conexion de la base de datos se ha hecho satisfactoriamente';
        }else{
            echo 'Ha sucedido un error inexperado en la conexion de la base de datos';
        }

    return $conexion;
}

function disconnectDB($conexion){

    $close = mysqli_close($conexion);

        if($close){
            echo 'La desconexion de la base de datos se ha hecho satisfactoriamente';
        }else{
            echo 'Ha sucedido un error inexperado en la desconexion de la base de datos';
        }   

    return $close;
}

function getArraySQL($sql){
    //Creamos la conexión con la función anterior
    $conexion = connectDB();

    //generamos la consulta

        mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    if(!$result = mysqli_query($conexion, $sql)) die(); //si la conexión cancelar programa

    $rawdata = array(); //creamos un array

    //guardamos en un array multidimensional todos los datos de la consulta
    $i=0;

    while($row = mysqli_fetch_array($result))
    {
        $rawdata[$i] = $row;
        $i++;
    }

    disconnectDB($conexion); //desconectamos la base de datos

    return $rawdata; //devolvemos el array
}

	$sql = "SELECT * FROM `sw_escala_calificaciones` WHERE id_periodo_lectivo = 2 ORDER BY ec_orden";
        $myArray = getArraySQL($sql);
        echo json_encode($myArray);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejemplo JSON Arrays</title>
</head>

<body>
</body>
</html>
