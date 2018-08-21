<?php

class especialidades extends MySQL
{
	var $code = "";
	var $id_tipo_educacion = 0;
	var $id_especialidad = 0;
	var $es_nombre = "";
	var $es_figura = "";
	var $es_abreviatura = "";
	
	function obtenerIdEspecialidad($id_paralelo)
	{
		// Obtencion del Id de la Especialidad que corresponde al paralelo pasado como parametro
		$consulta = parent::consulta("SELECT e.id_especialidad FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->id_especialidad;
	}
	
	function obtenerEspecialidad()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_especialidad = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function obtenerNombreEspecialidad($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_nombre FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->es_nombre;
	}

	function obtenerNombreFiguraProfesional($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_figura FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->es_figura;
	}

	function eliminarEspecialidad()
	{
		$qry = "SELECT id_curso FROM sw_curso WHERE id_especialidad = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$mensaje = "No se puede eliminar la especialidad porque tiene cursos asociados.";
		} else {
			$qry = "DELETE FROM sw_especialidad WHERE id_especialidad=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Especialidad eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar la Especialidad...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function insertarEspecialidad()
	{
		$qry = "INSERT INTO sw_especialidad (id_tipo_educacion, es_nombre, es_figura, es_abreviatura) VALUES (";
		$qry .= $this->id_tipo_educacion . ",";
		$qry .= "'" . $this->es_nombre . "',";
		$qry .= "'" . $this->es_figura . "',";
		$qry .= "'" . $this->es_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Especialidad insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la Especialidad...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarEspecialidad()
	{
		$qry = "UPDATE sw_especialidad SET ";
		$qry .= "id_tipo_educacion = " . $this->id_tipo_educacion . ",";
		$qry .= "es_nombre = '" . $this->es_nombre . "',";
		$qry .= "es_figura = '" . $this->es_figura . "',";
		$qry .= "es_abreviatura = '" . $this->es_abreviatura . "'";
		$qry .= " WHERE id_especialidad = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Especialidad actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Especialidad...Error: " . mysql_error();
		return $mensaje;
	}

	function listarEspecialidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_tipo_educacion = " . $this->code . " ORDER BY id_especialidad ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($especialidades = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $especialidades["id_especialidad"];
				$name = $especialidades["es_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarEspecialidad(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarEspecialidad(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Especialidades asociadas a este tipo de educaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarEspecialidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_tipo_educacion = " . $this->id_tipo_educacion . " ORDER BY es_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($especialidades = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $especialidades["id_especialidad"];
				$name = $especialidades["es_nombre"];
				$cadena .= "<td>$id</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button onclick='editEspecialidad(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteEspecialidad(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Especialidades asociadas a este nivel de educaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

}
?>