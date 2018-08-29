<?php

class distributivos extends MySQL
{
	var $code = "";
	var $id_malla_curricular = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $id_usuario = "";

	function insertarDistributivo()
	{
		$consulta = parent::consulta("SELECT id_malla_curricular, 
                                             ma_horas_presenciales, 
                                             ma_horas_autonomas, 
                                             ma_horas_tutorias, 
                                             ma_subtotal 
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
                $horas_presenciales = $registro["ma_horas_presenciales"];
                $horas_autonomas = $registro["ma_horas_autonomas"];
                $horas_tutorias = $registro["ma_horas_tutorias"];
                $subtotal = $registro["ma_subtotal"];
                // Ahora si procedemos a insertar...
                $qry = "INSERT INTO sw_distributivo(";
                $qry .= "id_malla_curricular,";
                $qry .= "id_paralelo,";
                $qry .= "id_asignatura,";
                $qry .= "id_usuario,";
                $qry .= "di_horas_presenciales,";
                $qry .= "di_horas_autonomas,";
                $qry .= "di_horas_tutorias,";
                $qry .= "di_subtotal) VALUES(";
                // id_malla_curricular
                $qry .= $id_malla_curricular . ",";
                // id_paralelo
                $qry .= $this->id_paralelo . ",";
                // id_asignatura
                $qry .= $this->id_asignatura . ",";
                // id_usuario
                $qry .= $this->id_usuario . ",";
                // di_horas_presenciales
                $qry .= $horas_presenciales . ",";
                // di_horas_autonomas
                $qry .= $horas_autonomas . ",";
                // di_horas_tutorias
                $qry .= $horas_tutorias . ",";
                // di_subtotal
                $qry .= $subtotal . ")";
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
                                             pa_nombre,
                                             cu_abreviatura,
                                             es_abreviatura, 
                                             as_nombre,
                                             cu_orden,
                                             ac_orden 
                                        FROM sw_distributivo d, 
                                             sw_paralelo p, 
                                             sw_curso c,
                                             sw_especialidad e, 
                                             sw_asignatura_curso ac, 
                                             sw_asignatura a 
                                       WHERE e.id_especialidad = c.id_especialidad
                                         AND c.id_curso = p.id_curso 
                                         AND p.id_paralelo = d.id_paralelo 
                                         AND c.id_curso = ac.id_curso 
                                         AND a.id_asignatura = d.id_asignatura 
                                         AND d.id_asignatura = ac.id_asignatura 
                                         AND d.id_usuario = " . $this->id_usuario 
                                   . " ORDER BY cu_orden, ac_orden, pa_nombre");
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
                $paralelo = $malla["es_abreviatura"]." ".$malla["cu_abreviatura"].$malla["pa_nombre"];
				$asignatura = $malla["as_nombre"];
                $presenciales = $malla["di_horas_presenciales"];
                $autonomas = $malla["di_horas_autonomas"];
                $tutorias = $malla["di_horas_tutorias"];
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