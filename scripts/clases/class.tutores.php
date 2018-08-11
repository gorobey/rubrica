<?php

class tutores extends MySQL
{
	
	var $code = "";
	var $id_paralelo = "";
	var $id_periodo_lectivo = "";
	
	function asociarParaleloTutor()
	{
		$qry = "INSERT INTO sw_paralelo_tutor (id_paralelo, id_usuario, id_periodo_lectivo) VALUES (";
		$qry .= $this->id_paralelo . ",";
		$qry .= $this->id_usuario . ",";
		$qry .= $this->id_periodo_lectivo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Tutor asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar el Tutor...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarParaleloTutor()
	{
		$qry = "DELETE FROM sw_paralelo_tutor WHERE id_paralelo_tutor =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tutor des-asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar el Tutor...Error: " . mysql_error();
		return $mensaje;
	}	

	function listarParalelosTutores()
	{
		$consulta = parent::consulta("SELECT id_paralelo_tutor, cu_nombre, es_figura, pa_nombre, us_titulo, us_fullname FROM sw_paralelo_tutor pt, sw_paralelo p, sw_curso c, sw_especialidad e, sw_usuario u WHERE pt.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND pt.id_usuario = u.id_usuario AND pt.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY c.id_especialidad, c.id_curso, pa_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tutor = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $tutor["id_paralelo_tutor"];
				$paralelo = $tutor["cu_nombre"] . " " . $tutor["pa_nombre"] . " - [" . $tutor["es_figura"] . "]";
				$tutor = $tutor["us_titulo"] . " " . $tutor["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"37%\" align=\"left\">$paralelo</td>\n";	
				$cadena .= "<td width=\"38%\" align=\"left\">$tutor</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se ha asociado tutores...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function obtenerIdParaleloTutor($id_usuario, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_paralelo FROM sw_paralelo_tutor WHERE id_usuario = $id_usuario AND id_periodo_lectivo = $id_periodo_lectivo");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdParalelo($id_usuario, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_paralelo FROM sw_paralelo_tutor WHERE id_usuario = $id_usuario AND id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($consulta);
		return $registro["id_paralelo"];
	}

	function obtenerNombreParalelo($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_figura, cu_nombre, pa_nombre FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$paralelo = parent::fetch_assoc($consulta);
		return $paralelo["cu_nombre"] . " " . $paralelo["pa_nombre"] . " " . $paralelo["es_figura"];
	}

}
?>