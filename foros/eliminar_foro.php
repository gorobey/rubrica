<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.foros.php");
	$foro = new foros();
	$foro->code = $_POST["id_foro"];
	if ($foro->tieneTemas($foro->code))
		echo "No se puede eliminar el Foro porque tiene temas asociados...";
	else 
		echo $foro->eliminarForo();
?>
