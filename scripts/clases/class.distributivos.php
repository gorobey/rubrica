<?php

class distributivos extends MySQL
{
    var $code = "";
    var $id_periodo_lectivo = "";
	var $id_malla_curricular = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $id_usuario = "";

	function insertarDistributivo()
	{
		$consulta = parent::consulta("SELECT id_malla_curricular 
                                        FROM sw_malla_curricular
                                       WHERE id_paralelo = " . $this->id_paralelo
                                    . "  AND id_asignatura = " . $this->id_asignatura);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros == 0)
		{
			$mensaje = "No se han asociado items a la malla con el paralelo y la asignatura seleccionados...";
        }
        else
        {
            $consulta2 = parent::consulta("SELECT * FROM sw_distributivo WHERE id_paralelo = "
                                        . $this->id_paralelo
                                        . " AND id_asignatura = "
                                        . $this->id_asignatura);
            $num_total_registros = parent::num_rows($consulta2);
            if($num_total_registros > 0) {
                $mensaje = "Ya existe la asociacion entre el paralelo y asignatura seleccionados.";
            } else {
                $registro = parent::fetch_assoc($consulta);
                $id_malla_curricular = $registro["id_malla_curricular"];
                // Ahora si procedemos a insertar...
                $qry = "INSERT INTO sw_distributivo(";
                $qry .= "id_periodo_lectivo,";
                $qry .= "id_malla_curricular,";
                $qry .= "id_paralelo,";
                $qry .= "id_asignatura,";
                $qry .= "id_usuario) VALUES(";
                // id_periodo_lectivo
                $qry .= $this->id_periodo_lectivo . ",";
                // id_malla_curricular
                $qry .= $id_malla_curricular . ",";
                // id_paralelo
                $qry .= $this->id_paralelo . ",";
                // id_asignatura
                $qry .= $this->id_asignatura . ",";
                // id_usuario
                $qry .= $this->id_usuario . ")";
                $consulta = parent::consulta($qry);
                if (!$consulta) {
                    $mensaje = "No se pudo insertar el item del distributivo. Error: " . mysql_error();
                } else {
                    $mensaje = "Insercion exitosa.";
                }
            }
        }
		return $mensaje;
    }
    function eliminarDistributivo()
    {
        $consulta = parent::consulta("DELETE FROM sw_distributivo WHERE id_distributivo = " . $this->code);
        if (!$consulta) {
            $mensaje = "No se pudo eliminar el item del distributivo. Error: " . mysql_error();
        } else {
            $mensaje = "Item del Distributivo eliminado exitosamente.";
        }
        return $mensaje;
    }
    function listarDistributivo()
	{
		$consulta = parent::consulta("SELECT d.*,
                                             m.*, 
                                             pa_nombre,
                                             cu_abreviatura,
                                             es_abreviatura, 
                                             as_nombre,
                                             pa_orden,
                                             ac_orden 
                                        FROM sw_distributivo d, 
                                             sw_malla_curricular m,
                                             sw_paralelo p, 
                                             sw_curso c,
                                             sw_especialidad e, 
                                             sw_asignatura_curso ac, 
                                             sw_asignatura a 
                                       WHERE m.id_malla_curricular = d.id_malla_curricular
                                         AND e.id_especialidad = c.id_especialidad
                                         AND c.id_curso = p.id_curso 
                                         AND p.id_paralelo = d.id_paralelo 
                                         AND c.id_curso = ac.id_curso 
                                         AND a.id_asignatura = d.id_asignatura 
                                         AND d.id_asignatura = ac.id_asignatura 
                                         AND d.id_usuario = " . $this->id_usuario 
                                   . "   AND d.id_periodo_lectivo = " . $this->id_periodo_lectivo
                                   . " ORDER BY pa_orden, ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros > 0)
		{
            $suma_horas_presenciales = 0;
            $suma_horas_tutorias = 0;
            $suma_horas_totales = 0;
			while($malla = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
                $code = $malla["id_distributivo"];
                $paralelo = $malla["cu_abreviatura"].$malla["pa_nombre"]." ".$malla["es_abreviatura"];
				$asignatura = $malla["as_nombre"];
                $presenciales = $malla["ma_horas_presenciales"];
                $autonomas = $malla["ma_horas_autonomas"];
                $tutorias = $malla["ma_horas_tutorias"];
                $suma_horas_presenciales = $suma_horas_presenciales + $presenciales;
                $suma_horas_tutorias = $suma_horas_tutorias + $tutorias;
                $suma_horas_totales = $suma_horas_totales + $presenciales + $tutorias;
                $subtotal = $presenciales + $tutorias;
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$paralelo</td>\n";
                $cadena .= "<td>$asignatura</td>\n";
                $cadena .= "<td>$presenciales</td>\n";
                $cadena .= "<td>$autonomas</td>\n";
                $cadena .= "<td>$tutorias</td>\n";
                $cadena .= "<td>$subtotal</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarDistributivo(".$code.")\">Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='8' align='center'>No se han definido items asociados a este docente...</td>\n";
			$cadena .= "</tr>\n";	
        }
        $datos = array('cadena' => $cadena, 
                       'horas_presenciales' => $suma_horas_presenciales,
                       'horas_tutorias' => $suma_horas_tutorias,
				       'total_horas' => $suma_horas_totales);
        return json_encode($datos);
	}
}
?>