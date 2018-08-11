<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.inspectores.php");
    $id_comportamiento_inspector = $_POST["id_comportamiento_inspector"];
    $inspector = new inspectores();
    echo $inspector->eliminarCalifComportamiento($id_comportamiento_inspector);
?>
