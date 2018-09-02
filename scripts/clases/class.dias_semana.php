<?php

class dias_semana extends MySQL
{
	
	var $code = "";
	var $ds_nombre = "";
	var $ds_ordinal = "";
	var $id_periodo_lectivo = "";
	
	function listar_dias_semana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ds_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($dia_semana = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $dia_semana["id_dia_semana"];
				$name = $dia_semana["ds_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarDiaSemana(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarDiaSemana(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido D&iacute;as de la Semana...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarDiasSemana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ds_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($dia_semana = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$code = $dia_semana["id_dia_semana"];
				$name = $dia_semana["ds_nombre"];
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button onclick='editDiaSemana(".$code.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteDiaSemana(".$code.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han definido D&iacute;as de la Semana...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function insertardiaSemana()
	{
		$qry = "INSERT INTO sw_dia_semana (id_periodo_lectivo, ds_nombre, ds_ordinal) VALUES (";
		$qry .= $this->id_periodo_lectivo .",";
		$qry .= "'" . $this->ds_nombre . "',";
		$qry .= "'" . $this->ds_ordinal . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "D&iacute;a de la Semana insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el D&iacute;a de la Semana...Error: " . mysql_error();
		return $mensaje;
	}

	function obtenerDiaSemana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_dia_semana = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
        function obtenerIdDiaSemana()
	{
            $consulta = parent::consulta("SELECT id_dia_semana FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo .
                                         " AND ds_ordinal = " . $this->ds_ordinal);
            return json_encode(parent::fetch_assoc($consulta));
	}
        
	function actualizarDiaSemana()
	{
		$qry = "UPDATE sw_dia_semana SET ";
		$qry .= "ds_nombre = '" . $this->ds_nombre . "',";
		$qry .= "ds_ordinal = " . $this->ds_ordinal;
		$qry .= " WHERE id_dia_semana = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "D&iacute;a de la Semana " . $this->ds_nombre . " actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el D&iacute;a de la Semana...Error: " . mysql_error();
		return $mensaje;
	}
	
	function eliminarDiaSemana()
	{
		$qry = "DELETE FROM sw_dia_semana WHERE id_dia_semana=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Dia de la Semana eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Dia de la Semana...Error: " . mysql_error();
		return $mensaje;
	}

	function mostrarDiasSemana($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT ds_nombre FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ds_ordinal");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($dia_semana = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"75px\" align=\"".$alineacion."\">" . $dia_semana["ds_nombre"] . "</td>\n";
			}
		}
		
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
		
}
?>