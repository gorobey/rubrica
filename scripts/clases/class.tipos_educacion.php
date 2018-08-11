<?php

class tipos_educacion extends MySQL
{
	
	var $code = "";
	var $id_periodo_lectivo = "";
	var $te_nombre = "";
	
	function existeTipoEducacion($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE te_nombre = '$nombre'");
		return ($parent::num_rows($consulta) > 0);
	}

	function obtenerNombreTipoEducacion($id_paralelo)
	{
		$consulta = parent::consulta("SELECT te_nombre FROM sw_tipo_educacion te, sw_especialidad es, sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND cu.id_especialidad = es.id_especialidad AND es.id_tipo_educacion = te.id_tipo_educacion AND id_paralelo = $id_paralelo");
		$tipo_educacion = parent::fetch_object($consulta);
		return $tipo_educacion->te_nombre;
	}

	function obtenerIdTipoEducacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_tipo_educacion FROM sw_tipo_educacion WHERE te_nombre = '$nombre'");
		$tipo_educacion = parent::fetch_object($consulta);
		return $tipo_educacion->id_tipo_educacion;
	}

	function obtenerTipoEducacion($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_tipo_educacion = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosTipoEducacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_tipo_educacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listar_tipos_educacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_periodo_lectivo = " . $this->code . " ORDER BY id_tipo_educacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tipos_educacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $tipos_educacion["id_tipo_educacion"];
				$name = $tipos_educacion["te_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarTipoEducacion(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarTipoEducacion(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Tipos de Educaci&oacute;n en este Per&iacute;odo Lectivo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarTipoEducacion()
	{
		$qry = "INSERT INTO sw_tipo_educacion (id_periodo_lectivo, te_nombre) VALUES (";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= "'" . $this->te_nombre . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Educaci&oacute;n insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Tipo de Educaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarTipoEducacion()
	{
		$qry = "UPDATE sw_tipo_educacion SET ";
		$qry .= "te_nombre = '" . $this->te_nombre . "'";
		$qry .= " WHERE id_tipo_educacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Educaci&oacute;n [" . $this->te_nombre . "] actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Tipo de Educaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarTipoEducacion()
	{
		$qry = "DELETE FROM sw_tipo_educacion WHERE id_tipo_educacion=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tipo de Educaci&oacute;n eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Tipo de Educaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

}
?>