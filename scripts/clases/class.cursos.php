<?php

class cursos extends MySQL
{
	var $code = "";
	var $id_curso = 0;
	var $id_especialidad = 0;
	var $cu_nombre = "";
    var $bol_proyectos = "";
	var $cu_abreviatura = "";
	
	function obtenerCurso()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_curso = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
        
	function obtenerNombreCurso($id_curso, $tipo)
	{
		$consulta = parent::consulta("SELECT cu_nombre, es_nombre, es_figura FROM sw_curso cu, sw_especialidad es WHERE cu.id_especialidad = es.id_especialidad AND cu.id_curso = $id_curso");
		$resultado = parent::fetch_object($consulta);
		if($tipo==1) {
			return $resultado->cu_nombre . " DE " . $resultado->es_figura;
		} else {
			return $resultado->cu_nombre . " DE " . $resultado->es_nombre . ": " . $resultado->es_figura;
		}
	}
	
	function obtenerBolProyectos($id_curso)
	{
		$consulta = parent::consulta("SELECT bol_proyectos FROM sw_curso WHERE id_curso = $id_curso");
		$resultado = parent::fetch_object($consulta);
		return $resultado->bol_proyectos;
	}

	function eliminarCurso()
	{
		$qry = "SELECT id_paralelo FROM sw_paralelo WHERE id_curso = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$mensaje = "No se puede eliminar el curso porque tiene paralelos asociados.";
		} else {
			$qry = "DELETE FROM sw_curso WHERE id_curso=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Curso eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el Curso...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function insertarCurso()
	{
		$qry = "SELECT secuencial_curso_especialidad(" . $this->id_especialidad . ") AS secuencial";
		$consulta = parent::consulta($qry);
		$secuencial = parent::fetch_object($consulta)->secuencial;
		$qry = "INSERT INTO sw_curso (id_especialidad, cu_nombre, cu_orden, bol_proyectos, cu_abreviatura) VALUES (";
		$qry .= $this->id_especialidad . ",";
		$qry .= "'" . $this->cu_nombre . "',";
		$qry .= $secuencial . ",";
		$qry .= $this->bol_proyectos . ",";
		$qry .= "'" . $this->cu_abreviatura . "')";
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
		$qry .= "cu_abreviatura = '" . $this->cu_abreviatura . "',";
        $qry .= "bol_proyectos = " . $this->bol_proyectos;
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
			$cadena .= "<td align='center'>No se han definido cursos asociados a esta especialidad...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarCursos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_especialidad = " . $this->code . " ORDER BY cu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($curso = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $curso["id_curso"];
				$name = $curso["cu_nombre"];
				$cadena .= "<td>$id</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button onclick='editCurso(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteCurso(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han definido cursos asociados a esta especialidad...</td>\n";
			$cadena .= "</tr>\n";	
		}
		return $cadena;
	}

}
?>