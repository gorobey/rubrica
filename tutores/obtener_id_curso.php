<?php
	include("../scripts/clases/class.mysql.php");
    $db = new mysql();
    $id_paralelo = $_POST["id_paralelo"];
    $consulta = $db->consulta("SELECT c.id_curso FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
    echo json_encode($db->fetch_assoc($consulta));
?>
