<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tutores.php");
	$tutor = new tutores();
	$tutor->code = $_POST["id_paralelo_tutor"];
	echo $tutor->eliminarParaleloTutor();
?>
