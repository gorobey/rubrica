<?php

class horas_clase extends MySQL
{
	
	var $code = "";
	var $hc_nombre = "";
	var $hc_ordinal = "";
	var $hc_hora_fin = "";
	var $id_dia_semana = "";
	var $hc_hora_inicio = "";

        var $id_asignatura = "";
        var $id_paralelo = "";
        
	function listar_horas_clase()
	{
		$consulta = parent::consulta("SELECT * FROM sw_hora_clase WHERE id_dia_semana = " . $this->id_dia_semana . " ORDER BY hc_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($hora_clase = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $hora_clase["id_hora_clase"];
				$name = $hora_clase["hc_nombre"];
				$hora_inicio = $hora_clase["hc_hora_inicio"];
				$hora_fin = $hora_clase["hc_hora_fin"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"24%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$hora_inicio</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$hora_fin</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarHoraClase(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarHoraClase(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Horas Clase para este D&iacute;a de la Semana...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarHoraClase()
	{
		// Aqui primero llamo a la funcion almacenada secuencial_hora_clase_dia_semana
		$consulta = parent::consulta("SELECT secuencial_hora_clase_dia_semana(".$this->id_dia_semana.") AS secuencial");
		$hc_ordinal = parent::fetch_object($consulta)->secuencial;
		
		$qry = "INSERT INTO sw_hora_clase (id_dia_semana, hc_nombre, hc_hora_inicio, hc_hora_fin, hc_ordinal) VALUES (";
		$qry .= $this->id_dia_semana . ",";
		$qry .= "'" . $this->hc_nombre . "',";
		$qry .= "'" . $this->hc_hora_inicio . "',";
		$qry .= "'" . $this->hc_hora_fin . "',";
		$qry .= $hc_ordinal . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Hora Clase insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la hora clase...Error: " . mysql_error();
		return $mensaje;
	}

	function obtenerHoraClase()
	{
		$consulta = parent::consulta("SELECT * FROM sw_hora_clase WHERE id_hora_clase = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
        
        function obtenerNombreHoraClase($id_hora_clase)
        {
            $consulta = parent::consulta("SELECT hc_nombre,
	                                         DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
	                                         DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
	                                         ds_nombre
                                            FROM sw_hora_clase hc,
	                                         sw_dia_semana di
                                           WHERE di.id_dia_semana = hc.id_dia_semana
                                             AND id_hora_clase = $id_hora_clase");
            $hora_clase = parent::fetch_assoc($consulta);
            return $hora_clase["ds_nombre"] . " - " . $hora_clase["hc_nombre"] . " (" . $hora_clase["hora_inicio"] . " - " . $hora_clase["hora_fin"] . ")";
        }
	
	function actualizarHoraClase()
	{
		$qry = "UPDATE sw_hora_clase SET ";
		$qry .= "hc_nombre = '" . $this->hc_nombre . "',";
		$qry .= "hc_hora_inicio = '" . $this->hc_hora_inicio . "',";
		$qry .= "hc_hora_fin = '" . $this->hc_hora_fin . "'";
		$qry .= " WHERE id_hora_clase = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Hora Clase " . $this->hc_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Hora Clase...Error: " . mysql_error();
		return $mensaje;
	}
	
	function eliminarHoraClase()
	{
		$qry = "DELETE FROM sw_hora_clase WHERE id_hora_clase = ". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Hora de Clase eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la Hora de Clase...Error: " . mysql_error();
		return $mensaje;
	}
	
        function obtenerHorasClase()
	{
		$consulta = parent::consulta("SELECT hc.id_hora_clase,
                                                     hc_nombre,
                                                     DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
                                                     DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
                                                     ds_nombre
                                                FROM sw_hora_clase hc,
                                                     sw_horario ho,
                                                     sw_dia_semana di
                                               WHERE hc.id_hora_clase = ho.id_hora_clase
                                                 AND di.id_dia_semana = hc.id_dia_semana
                                                 AND ho.id_asignatura = " . $this->id_asignatura .
                                               " AND ho.id_paralelo = " . $this->id_paralelo .
                                               " AND ho.id_dia_semana = " . $this->id_dia_semana .
                                               " ORDER BY hc.hc_ordinal");
            
//                $consulta = parent::consulta("SELECT hc.id_hora_clase,
//                                                     hc_nombre,
//                                                     DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
//                                                     DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
//                                                     ds_nombre
//                                                FROM sw_hora_clase hc,
//                                                     sw_dia_semana di
//                                               WHERE di.id_dia_semana = hc.id_dia_semana
//                                                 AND hc.id_dia_semana = " . $this->id_dia_semana .
//                                             " ORDER BY hc.hc_ordinal");
                
                $cadena = "";
                $num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($hora_clase = parent::fetch_assoc($consulta))
			{
				$code = $hora_clase["id_hora_clase"];
				$name = $hora_clase["ds_nombre"] . " - " . $hora_clase["hc_nombre"] . " (" . $hora_clase["hora_inicio"] . " - " . $hora_clase["hora_fin"] . ")";
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}
}
?>