<?php

class horas_clase extends MySQL
{
	
	var $code = "";
	var $hc_nombre = "";
	var $hc_ordinal = "";
	var $hc_hora_fin = "";
	var $id_dia_semana = "";
	var $hc_hora_inicio = "";

	var $id_periodo_lectivo = "";

    var $id_asignatura = "";
    var $id_paralelo = "";
        
	function listar_horas_clase()
	{
		$consulta = parent::consulta("SELECT * FROM sw_hora_clase WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY hc_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = ""; $contador = 0;
		if($num_total_registros > 0)
		{
			while($hora_clase = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $hora_clase["id_hora_clase"];
				$name = $hora_clase["hc_nombre"];
				$hora_inicio = $hora_clase["hc_hora_inicio"];
				$hora_fin = $hora_clase["hc_hora_fin"];
				$ordinal = $hora_clase["hc_ordinal"];
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$hora_inicio</td>\n";
				$cadena .= "<td>$hora_fin</td>\n";
				$cadena .= "<td>$ordinal</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-warning' onclick=\"editarHoraClase(".$code.")\">Editar</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarHoraClase(".$code.")\">Eliminar</button></td>";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='7' align='center'>No se han definido Horas Clase para este periodo lectivo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$datos = array('cadena' => $cadena, 
				       'total_horas' => $contador);
        return json_encode($datos);
	}

	function insertarHoraClase()
	{	
		$qry = "INSERT INTO sw_hora_clase (id_periodo_lectivo, hc_nombre, hc_hora_inicio, hc_hora_fin, hc_ordinal) VALUES (";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= "'" . $this->hc_nombre . "',";
		$qry .= "'" . $this->hc_hora_inicio . "',";
		$qry .= "'" . $this->hc_hora_fin . "',";
		$qry .= $this->hc_ordinal . ")";
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