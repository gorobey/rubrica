<?php
	require_once("../scripts/clases/class.mysql.php");
	$db = new mysql();
    $id_asignatura = $_POST["id_asignatura"];
    $id_paralelo = $_POST["id_paralelo"];
    $consulta = $db->consulta("SELECT us_titulo,
                                      us_apellidos,
                                      us_nombres
                                 FROM sw_paralelo_asignatura pa,
                                      sw_usuario u
                                WHERE u.id_usuario = pa.id_usuario
                                  AND pa.id_asignatura = $id_asignatura
                                  AND pa.id_paralelo = $id_paralelo");
    $docente = $db->fetch_object($consulta);
	echo "DOCENTE: " . $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
?>
