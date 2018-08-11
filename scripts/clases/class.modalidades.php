<?php

class modalidades extends MySQL
{
	var $code = "";
	var $id_periodo_lectivo = 0;
	var $mo_nombre = "";
	
	function obtenerModalidad()
	{
		$consulta = parent::consulta("SELECT * FROM sw_modalidad WHERE id_modalidad = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function eliminarModalidad()
	{
		$qry = "DELETE FROM sw_modalidad WHERE id_modalidad = ". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Modalidad eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la Modalidad...Error: " . mysql_error();
		return $mensaje;
	}

	function insertarModalidad()
	{
		$qry = "INSERT INTO sw_modalidad (mo_nombre) VALUES (";
		$qry .= "'" . $this->mo_nombre . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Modalidad insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la Modalidad...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarModalidad()
	{
		$qry = "UPDATE sw_modalidad SET";
		$qry .= " mo_nombre = '" . $this->mo_nombre . "'";
		$qry .= " WHERE id_modalidad = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Modalidad actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Modalidad...Error: " . mysql_error();
		return $mensaje;
	}

	function listarModalidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_modalidad ORDER BY id_modalidad ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($cursos = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $cursos["id_modalidad"];
				$name = $cursos["mo_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarModalidad(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarModalidad(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido modalidades...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

}
?>