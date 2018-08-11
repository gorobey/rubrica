<?php

class escalas extends MySQL
{
	
	var $code = "";
	var $id_periodo_lectivo = "";
	var $ec_cualitativa = "";
	var $ec_cuantitativa = "";
	var $ec_nota_minima = "";
	var $ec_nota_maxima = "";
	var $ec_orden = "";
	
	function existeRecomendacionesQuimestrales($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_quimestrales WHERE id_escala_calificaciones = $id");
		return (parent::num_rows($consulta) > 0);
	}

	function existeRecomendacionesAnuales($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_anuales WHERE id_escala_calificaciones = $id");
		return (parent::num_rows($consulta) > 0);
	}

	function obtenerDatosEscala($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_escala_calificaciones WHERE id_escala_calificaciones = " . $id);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function obtenerEscalasCalificaciones()
	{
		$registros = parent::consulta("SELECT ec_nota_minima, ec_nota_maxima, ec_equivalencia FROM sw_escala_calificaciones WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ec_orden");

		while ($reg=mysql_fetch_array($registros))
		{
		  $vec[]=$reg;
		}

		require('../funciones/JSON.php');
		$json=new Services_JSON();
		$cad=$json->encode($vec);
		echo $cad;
		
	}

	function obtenerEscalasCalificacionesClub()
	{
		$registros = parent::consulta("SELECT ec_nota_minima, ec_nota_maxima, ec_abreviatura FROM sw_escala_proyectos ORDER BY id_escala_proyectos");

		while ($reg=mysql_fetch_array($registros))
		{
		  $vec[]=$reg;
		}

		require('../funciones/JSON.php');
		$json=new Services_JSON();
		$cad=$json->encode($vec);
		echo $cad;
		
	}

	function listarEscalas()
	{
		$consulta = parent::consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ec_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($escala = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $escala["id_escala_calificaciones"];
				$cualitativa = $escala["ec_cualitativa"];
				$cuantitativa = $escala["ec_cuantitativa"];
				$minima = $escala["ec_nota_minima"];
				$maxima = $escala["ec_nota_maxima"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"18%\" align=\"left\">$cualitativa</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$cuantitativa</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$minima</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$maxima</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarEscala(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarEscala(".$code.",'".$cualitativa."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Escalas de Calificaci&oacute;n en este Per&iacute;odo Lectivo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarEscala($id_periodo_lectivo, $cualitativa, $cuantitativa, $minima, $maxima, $equivalencia, $orden)
	{
		$qry = "INSERT INTO sw_escala_calificaciones (id_periodo_lectivo, ec_cualitativa, ec_cuantitativa, ec_nota_minima, ec_nota_maxima, ec_equivalencia, ec_orden) VALUES (";
		$qry .= $id_periodo_lectivo . ",";
		$qry .= "'" . $cualitativa . "',";
		$qry .= "'" . $cuantitativa . "',";
		$qry .= $minima . ",";
		$qry .= $maxima . ",";
		$qry .= "'" . $equivalencia . "',";
		$qry .= $orden . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Escala de Calificaci&oacute;n insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la Escala de Calificaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarEscala($id, $cualitativa, $cuantitativa, $minima, $maxima, $equivalencia, $orden)
	{
		$qry = "UPDATE sw_escala_calificaciones SET ";
		$qry .= "ec_cualitativa = '" . $cualitativa . "',";
		$qry .= "ec_cuantitativa = '" . $cuantitativa . "',";
		$qry .= "ec_nota_minima = " . $minima . ",";
		$qry .= "ec_nota_maxima = " . $maxima . ",";
		$qry .= "ec_equivalencia = '" . $equivalencia . "',";
		$qry .= "ec_orden = " . $orden;
		$qry .= " WHERE id_escala_calificaciones = " . $id;
		$consulta = parent::consulta($qry);
		$mensaje = "Escala de calificaciones [" . $cualitativa . "] actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Escala de calificaciones...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarEscala()
	{
		if (!$this->existeRecomendacionesQuimestrales($this->code) && !$this->existeRecomendacionesAnuales($this->code)) {
			$qry = "DELETE FROM sw_escala_calificaciones WHERE id_escala_calificaciones=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Escala de calificaciones eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar la Escala de calificaciones...Error: " . mysql_error();
		} else {
			$mensaje = "No se puede eliminar la escala de calificaciones porque tiene recomendaciones asociadas...";
		}
		return $mensaje;
	}

} 
?>