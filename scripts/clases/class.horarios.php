<?php

class horarios extends MySQL
{
	
	var $code = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $id_hora_clase = "";
	var $id_periodo_lectivo = "";
        
    var $id_dia_semana = "";
	
	function listarHorarioDocente($id_usuario, $id_dia_semana)
	{
		$cadena = "";
		// Primero debo obtener las horas clase del dia de la semana...
		$consulta1 = parent::consulta("SELECT hd.id_hora_clase FROM sw_hora_dia hd, sw_hora_clase hc WHERE hc.id_hora_clase = hd.id_hora_clase AND id_dia_semana = $id_dia_semana ORDER BY hc_ordinal");
		$num_total_registros = parent::num_rows($consulta1);
		if($num_total_registros>0)
		{
			while($hora_clase = parent::fetch_assoc($consulta1))
			{
				$id_hora_clase = $hora_clase["id_hora_clase"];
				$consulta2 = parent::consulta("SELECT id_horario, 
													  ho.id_hora_clase, 
													  hc_nombre, 
													  DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
													  DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin, 
													  as_nombre, 
													  pa_nombre, 
													  cu_nombre, 
													  es_figura 
												 FROM sw_horario ho, 
												 	  sw_hora_clase hc, 
													  sw_asignatura a, 
													  sw_distributivo d, 
													  sw_paralelo p, 
													  sw_curso c, 
													  sw_especialidad e 
												WHERE ho.id_hora_clase = hc.id_hora_clase 
												  AND ho.id_asignatura = a.id_asignatura 
												  AND a.id_asignatura = d.id_asignatura 
												  AND ho.id_paralelo = d.id_paralelo 
												  AND p.id_paralelo = d.id_paralelo 
												  AND c.id_curso = p.id_curso 
												  AND e.id_especialidad = c.id_especialidad 
												  AND d.id_usuario = $id_usuario 
												  AND ho.id_hora_clase = $id_hora_clase
												  AND ho.id_dia_semana = $id_dia_semana");
				while($horario = parent::fetch_assoc($consulta2))
				{
					$cadena .= "<tr>\n";
					$name = $horario["hc_nombre"] . " (" . $horario["hora_inicio"] . " - " . $horario["hora_fin"] . ")";
					$cadena .= "<td>$name</td>\n";
					$asignatura = $horario["as_nombre"];
					$cadena .= "<td>$asignatura</td>\n";
					$paralelo = $horario["cu_nombre"] . " " . $horario["pa_nombre"] . " - " . $horario["es_figura"];
					$cadena .= "<td>$paralelo</td>\n";
					$cadena .= "</tr>\n";
				}
			}
		}
		return $cadena;
	}

	function listarHorarioParalelo($id_paralelo, $id_dia_semana)
	{
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		// Primero debo obtener las horas clase del dia de la semana...
		$consulta1 = parent::consulta("SELECT hd.id_hora_clase FROM sw_hora_dia hd, sw_hora_clase hc WHERE hc.id_hora_clase = hd.id_hora_clase AND id_dia_semana = $id_dia_semana ORDER BY hc_ordinal");
		$num_total_registros = parent::num_rows($consulta1);
		if($num_total_registros > 0)
		{
			while($hora_clase = parent::fetch_assoc($consulta1))
			{
				$id_hora_clase = $hora_clase["id_hora_clase"];
				$consulta2 = parent::consulta("SELECT id_horario, 
													  ho.id_hora_clase, 
													  hc_nombre, 
													  DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
													  DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin, 
													  as_nombre, 
													  a.id_asignatura
												 FROM sw_horario ho, 
												      sw_hora_clase hc, 
													  sw_asignatura a
												WHERE ho.id_hora_clase = hc.id_hora_clase 
												  AND ho.id_asignatura = a.id_asignatura 
												  AND ho.id_dia_semana = $id_dia_semana 
												  AND ho.id_hora_clase = $id_hora_clase 
												  AND id_paralelo = $id_paralelo");
				$num_total_registros = parent::num_rows($consulta2);
				if($num_total_registros > 0)
				{
					while($horario = parent::fetch_assoc($consulta2))
					{
						$cadena .= "<tr>\n";
						$code = $horario["id_horario"];
						$name = $horario["hc_nombre"] . " (" . $horario["hora_inicio"] . " - " . $horario["hora_fin"] . ")";
						$id_asignatura = $horario["id_asignatura"];
						$asignatura = $horario["as_nombre"];
						$cadena .= "<td><input type='checkbox' class='delete_checkbox' value='$code'></td>\n";
						$cadena .= "<td>$name</td>\n";
						$cadena .= "<td>$asignatura</td>\n";
						// Obtengo el docente que imparte esta asignatura
						$query = parent::consulta("SELECT CONCAT(us_titulo,' ',us_apellidos,' ',us_nombres) AS docente 
													 FROM sw_asignatura a, 
													 	  sw_distributivo d,
														  sw_usuario u 
													WHERE a.id_asignatura = d.id_asignatura
													  AND u.id_usuario = d.id_usuario
													  AND d.id_asignatura = $id_asignatura 
													  AND d.id_paralelo = $id_paralelo");
						$obj = parent::fetch_object($query);
						$docente = $obj->docente;
						$cadena .= "<td>$docente</td>\n";
						$cadena .= "</tr>\n";
					}
				}
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Horas Clase para el D&iacute;a de la Semana elegido...</td>\n";
			$cadena .= "</tr>\n";	
		}
		
		$cadena .= "</table>";	
		return $cadena;
	}

	function existeAsignaturaHoraClase($id_paralelo, $id_dia_semana, $id_hora_clase)
	{
		$consulta = parent::consulta("SELECT id_horario FROM sw_horario WHERE id_paralelo = $id_paralelo AND id_dia_semana = $id_dia_semana AND id_hora_clase = $id_hora_clase");
		$num_total_registros = parent::num_rows($consulta);
		return $num_total_registros > 0;
	}

	function asociarAsignaturaHoraClase()
	{
		$qry = "INSERT INTO sw_horario (id_paralelo, id_asignatura, id_dia_semana, id_hora_clase) VALUES (";
		$qry .= $this->id_paralelo . ",";
		$qry .= $this->id_asignatura . ",";
                $qry .= $this->id_dia_semana . ",";
		$qry .= $this->id_hora_clase . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarAsignaturaHoraClase()
	{
		$qry = "DELETE FROM sw_horario WHERE id_horario =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura des-asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}	
	
}
?>