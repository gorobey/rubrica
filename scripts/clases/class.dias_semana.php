<?php

class dias_semana extends MySQL
{
	
	var $code = "";
	var $ds_nombre = "";
	var $ds_ordinal = "";
	var $id_periodo_lectivo = "";

	var $id_dia_semana = "";
	var $id_hora_clase = "";

	function insertarHoraDia()
	{
		$qry = "SELECT * FROM sw_hora_dia WHERE id_dia_semana = " . $this->id_dia_semana . " AND id_hora_clase = " . $this->id_hora_clase;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "Ya existe la asociacion de hora clase y dia de la semana seleccionados...";
		} else {
			$qry = "INSERT INTO sw_hora_dia(id_dia_semana, id_hora_clase) VALUES(";
			$qry .= $this->id_dia_semana . ",";
			$qry .= $this->id_hora_clase . ")";
			$consulta = parent::consulta($qry);
			if (!$consulta) {
				$mensaje = "No se pudo insertar la asociacion. Error: " . mysql_error();
			} else {
				$mensaje = "Asociacion insertada exitosamente.";
			}
		}
		return $mensaje;
	}

	function listarHorasAsociadas()
	{
		$consulta = parent::consulta("SELECT id_hora_dia, 
											 ds_nombre,
                                             hc_nombre,
											 DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
											 DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
                                             hc_ordinal 
                                        FROM sw_hora_dia hd, 
                                             sw_dia_semana ds, 
                                             sw_hora_clase hc
                                       WHERE ds.id_dia_semana = hd.id_dia_semana
                                         AND hc.id_hora_clase = hd.id_hora_clase 
                                         AND hd.id_dia_semana = " . $this->id_dia_semana 
                                   . " ORDER BY hc_ordinal");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = ""; $contador = 0;
		if($num_total_registros > 0)
		{
			while($hora_asociada = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $hora_asociada["id_hora_dia"];
				$dia_semana = $hora_asociada["ds_nombre"];
                $hora_clase = $hora_asociada["ds_nombre"] . " - " . $hora_asociada["hc_nombre"] . " (" . $hora_asociada["hora_inicio"] . " - " . $hora_asociada["hora_fin"] . ")";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$dia_semana</td>\n";
                $cadena .= "<td>$hora_clase</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarAsociacion(".$code.")\">Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han asociado horas clase a este dia de la semana...</td>\n";
			$cadena .= "</tr>\n";	
        }
        $datos = array('cadena' => $cadena, 
				       'total_horas' => $contador);
        return json_encode($datos);
	}

	function eliminarHoraDia()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_rows FROM sw_asistencia_estudiante WHERE id_hora_dia = " . $this->id_hora_dia);
		$num_rows = parent::fetch_object($consulta)->num_rows;
		if ($num_rows > 0) {
			$mensaje = "No se puede eliminar la asociacion porque tiene asistencias relacionadas...";
		} else {
			$consulta = parent::consulta("DELETE FROM sw_hora_dia WHERE id_hora_dia = " . $this->id_hora_dia);
			if (!$consulta) {
				$mensaje = "No se pudo eliminar la asociacion. Error: " . mysql_error();
			} else {
				$mensaje = "Asociacion eliminada exitosamente.";
			}
		}
		return $mensaje;
	}
	
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
		$qry = "SELECT * FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND ds_nombre = '" . $this->ds_nombre . "'";
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "Ya existe el dia de la semana para el presente periodo lectivo...";
		} else {
			$qry = "INSERT INTO sw_dia_semana (id_periodo_lectivo, ds_nombre, ds_ordinal) VALUES (";
			$qry .= $this->id_periodo_lectivo .",";
			$qry .= "'" . $this->ds_nombre . "',";
			$qry .= "'" . $this->ds_ordinal . "')";
			$consulta = parent::consulta($qry);
			$mensaje = "D&iacute;a de la Semana insertado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo insertar el D&iacute;a de la Semana...Error: " . mysql_error();
		}
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
		$qry = "SELECT * FROM sw_hora_dia WHERE id_dia_semana = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "No se puede eliminar porque tiene horas clase asociadas...";
		} else {
			$qry = "DELETE FROM sw_dia_semana WHERE id_dia_semana = " . $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Dia de la Semana eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el Dia de la Semana...Error: " . mysql_error();
		}
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