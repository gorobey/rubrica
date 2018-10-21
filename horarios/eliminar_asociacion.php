<?php
	include("../scripts/clases/class.mysql.php");
	$db = new mysql();
	if(isset($_POST["checkbox_value"]))
	{
		for($count = 0; $count < count($_POST["checkbox_value"]); $count++)
		{
			$query = $db->consulta("DELETE FROM sw_horario WHERE id_horario = '".$_POST['checkbox_value'][$count]."'");
		}
	}
?>
