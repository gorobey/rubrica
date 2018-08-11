<?php

class inasistencias extends MySQL
{
	
	var $code = "";
	var $in_nombre = "";
	var $in_abreviatura = "";
	
	function listar_inasistencias()
	{
		$consulta = parent::consulta("SELECT * FROM sw_inasistencia ORDER BY in_abreviatura ASC");
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
				$code = $dia_semana["id_inasistencia"];
				$nombre = $dia_semana["in_nombre"];
				$abreviatura = $dia_semana["in_abreviatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$nombre</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$abreviatura</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarInasistencia(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarInasistencia(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Inasistencias...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarInasistencia()
	{
		$qry = "INSERT INTO sw_inasistencia (in_nombre, in_abreviatura) VALUES (";
		$qry .= "'" . $this->in_nombre . "',";
		$qry .= "'" . $this->in_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Inasistencia insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la Inasistencia...Error: " . mysql_error();
		return $mensaje;
	}

	function obtenerInasistencia()
	{
		$consulta = parent::consulta("SELECT * FROM sw_inasistencia WHERE id_inasistencia = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function actualizarInasistencia()
	{
		$qry = "UPDATE sw_inasistencia SET ";
		$qry .= "in_nombre = '" . $this->in_nombre . "',";
		$qry .= "in_abreviatura = '" . $this->in_abreviatura . "'";
		$qry .= " WHERE id_inasistencia = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Inasistencia " . $this->ds_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Inasistencia...Error: " . mysql_error();
		return $mensaje;
	}
	
	function eliminarInasistencia()
	{
		$qry = "DELETE FROM sw_inasistencia WHERE id_inasistencia = ". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Inasistencia eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la Inasistencia...Error: " . mysql_error();
		return $mensaje;
	}

	function mostrarInasistencia($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT in_nombre, in_abreviatura FROM sw_inasistencia ORDER BY in_abreviatura");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($inasistencia = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"105px\" align=\"".$alineacion."\">" . $inasistencia["in_abreviatura"] . ": " . $inasistencia["in_nombre"] . "</td>\n";
			}
		}
		
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
		
}
?>