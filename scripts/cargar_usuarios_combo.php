<?php
    require_once 'clases/class.mysql.php';
    require_once 'clases/class.combos.php';
    $selects = new selects();
    echo $selects->cargarUsuarios();
?>
