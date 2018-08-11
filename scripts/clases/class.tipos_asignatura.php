<?php

class tipos_asignatura extends MySQL
{
	
	var $code = "";
	var $ta_descripcion = "";
	
	function existeTipoAsignatura($descripcion)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_asignatura WHERE ta_descripcion = '$descripcion'");
		return ($parent::num_rows($consulta) > 0);
	}

	function obtenerDescripcionTipoAsignatura($id)
	{
		$consulta = parent::consulta("SELECT ta_descripcion FROM sw_tipo_asignatura WHERE id_tipo_asignatura = $id");
		$tipo_asignatura = parent::fetch_object($consulta);
		return $tipo_asignatura->ta_descripcion;
	}

	function obtenerIdTipoAsignatura($descripcion)
	{
		$consulta = parent::consulta("SELECT id_tipo_asignatura FROM sw_tipo_asignatura WHERE ta_descripcion = '$descripcion'");
		$tipo_asignatura = parent::fetch_object($consulta);
		return $tipo_asignatura->id_tipo_asignatura;
	}

	function obtenerTipoAsignatura($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_asignatura WHERE id_tipo_asignatura = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosTipoAsignatura()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_asignatura WHERE id_tipo_asignatura = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listar_tipos_asignatura()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_asignatura");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tipos_asignatura = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $tipos_asignatura["id_tipo_asignatura"];
				$name = $tipos_asignatura["ta_descripcion"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarTipoAsignatura(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarTipoAsignatura(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Tipos de Asignatura...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarTipoAsignatura()
	{
		$qry = "INSERT INTO sw_tipo_asignatura (ta_descripcion) VALUES (";
		$qry .= "'" . $this->ta_descripcion . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Asignatura insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Tipo de Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarTipoAsignatura()
	{
		$qry = "UPDATE sw_tipo_asignatura SET ";
		$qry .= "ta_descripcion = '" . $this->ta_descripcion . "'";
		$qry .= " WHERE id_tipo_asignatura = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Asignatura [" . $this->ta_descripcion . "] actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Tipo de Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarTipoAsignatura()
	{
		$qry = "DELETE FROM sw_tipo_asignatura WHERE id_tipo_asignatura=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Asignatura eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Tipo de Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

}
?>