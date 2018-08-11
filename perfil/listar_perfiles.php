<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.perfiles.php");
	$perfiles = new perfiles();
	echo $perfiles->listarPerfiles();
?>
