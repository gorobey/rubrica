<?php

class cursos extends MySQL
{
	var $code = "";
	var $id_curso = 0;
	var $id_especialidad = 0;
	var $cu_nombre = "";
	var $cu_superior = "";
	
	function obtenerCurso()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_curso = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function eliminarCurso()
	{
		$qry = "DELETE FROM sw_curso WHERE id_curso=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Curso eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Curso...Error: " . mysql_error();
		return $mensaje;
	}

	function insertarCurso()
	{
		$qry = "INSERT INTO sw_curso (id_especialidad, cu_nombre, id_curso_superior) VALUES (";
		$qry .= $this->id_especialidad . ",";
		$qry .= "'" . $this->cu_nombre . "',";
		$qry .= $this->cu_superior . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Curso insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Curso...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarCurso()
	{
		$qry = "UPDATE sw_curso SET ";
		$qry .= "id_especialidad = " . $this->id_especialidad . ",";
		$qry .= "cu_nombre = '" . $this->cu_nombre . "',";
		$qry .= "id_curso_superior = " . $this->cu_superior;
		$qry .= " WHERE id_curso = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Curso actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Curso...Error: " . mysql_error();
		return $mensaje;
	}

	function listarCursos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_especialidad = " . $this->code . " ORDER BY id_curso ASC");
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
				$code = $cursos["id_curso"];
				$name = $cursos["cu_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCurso(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarCurso(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido cursos asociados a esta especialidad...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

}
?>