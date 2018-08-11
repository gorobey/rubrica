<?php

class asistencias extends MySQL
{
	
	var $code = "";
	var $id_estudiante = "";
	var $id_asignatura = "";
        var $id_paralelo = "";
        var $id_dia_semana = "";
        var $id_hora_clase = "";
        var $id_inasistencia = "";
        var $ae_fecha = "";
	
	function listarInasistenciaParalelo()
	{
            $consulta = parent::consulta("SELECT e.id_estudiante, "
                                              . "c.id_curso, "
                                              . "pa.id_paralelo, "
                                              . "pa.id_asignatura, "
                                              . "e.es_apellidos, "
                                              . "e.es_nombres, "
                                              . "es_retirado, "
                                              . "as_nombre, "
                                              . "cu_nombre, "
                                              . "pa_nombre "
                                              . "FROM sw_paralelo_asignatura pa, "
                                              . "sw_estudiante_periodo_lectivo ep, "
                                              . "sw_estudiante e, "
                                              . "sw_asignatura a, "
                                              . "sw_curso c, "
                                              . "sw_paralelo p "
                                              . "WHERE pa.id_paralelo = ep.id_paralelo "
                                              . "AND pa.id_periodo_lectivo = ep.id_periodo_lectivo "
                                              . "AND ep.id_estudiante = e.id_estudiante "
                                              . "AND pa.id_asignatura = a.id_asignatura "
                                              . "AND pa.id_paralelo = p.id_paralelo "
                                              . "AND p.id_curso = c.id_curso "
                                              . "AND pa.id_paralelo = " . $this->id_paralelo 
                                              . " AND pa.id_asignatura = " . $this->id_asignatura
                                              . " AND es_retirado <> 'S'"
                                              . " ORDER BY es_apellidos, es_nombres ASC"); //LIMIT $inicio, $cantidad_registros
            $num_total_registros = parent::num_rows($consulta);
            $cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
            if($num_total_registros>0)
            {
                $contador = 0;
                while($paralelos = parent::fetch_assoc($consulta))
                {
                        $contador++;
                        $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                        $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                        $id_estudiante = $paralelos["id_estudiante"];
                        $apellidos = $paralelos["es_apellidos"];
                        $nombres = $paralelos["es_nombres"];
                        $retirado = $paralelos["es_retirado"];
                        $id_curso = $paralelos["id_curso"];
                        $id_paralelo = $paralelos["id_paralelo"];
                        $id_asignatura = $paralelos["id_asignatura"];
                        $asignatura = $paralelos["as_nombre"];
                        $curso = $paralelos["cu_nombre"];
                        $paralelo = $paralelos["pa_nombre"];
                        $cadena .= "<td width=\"5%\">$contador</td>\n";	
                        $cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
                        $cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
                        // Aqui se consultan las inasistencias definidas en la base de datos
                        $tipo_inasistencia = parent::consulta("SELECT * FROM sw_inasistencia ORDER BY id_inasistencia");
                        $num_total_registros = parent::num_rows($tipo_inasistencia);
                        if($num_total_registros>0)
                        {
                                $contador_inasistencias = 0;
                                while($inasistencia = parent::fetch_assoc($tipo_inasistencia))
                                {
                                        $contador_inasistencias++;
                                        $id_inasistencia = $inasistencia["id_inasistencia"];
                                        $nombre = $inasistencia["in_nombre"];
                                        $abreviatura = $inasistencia["in_abreviatura"];
                                        $qry = parent::consulta("SELECT *"
                                                               . " FROM sw_asistencia_estudiante"
                                                               . " WHERE id_estudiante = ".$id_estudiante
                                                               . " AND id_paralelo = ".$this->id_paralelo
                                                               . " AND id_asignatura = ".$this->id_asignatura
                                                               . " AND id_hora_clase = ".$this->id_hora_clase
                                                               . " AND id_inasistencia = ".$id_inasistencia
                                                               . " AND ae_fecha = '".$this->ae_fecha."'");
                                        $num_total_registros = parent::num_rows($qry);
                                        $asistencia_estudiante = parent::fetch_assoc($qry);
                                        $checked=($num_total_registros>0)?"checked":"";
                                        $disabled=($retirado=='S')?"disabled":"";
                                        $cadena .= "<td width=\"60px\" align=\"left\">&nbsp;".$abreviatura.":&nbsp;<input type=\"radio\" name=\"asistencia_".$contador."\" value=\"$id_inasistencia\" ".$checked." ".$disabled." onclick=\"actualizar_asistencia(this,".$id_estudiante.")\"/></td>\n";
                                }
                                $cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
                                $cadena .= "</tr>\n";	
                        } else {
                                $cadena .= "<tr>\n";	
                                $cadena .= "<td>No se han definido tipos de asistencia...</td>\n";
                                $cadena .= "</tr>\n";
                        }
                    }
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}
        
	function insertarInasistenciaEstudiante()
	{
            $qry = "INSERT INTO sw_asistencia_estudiante (id_estudiante, id_asignatura, id_paralelo, id_dia_semana, id_hora_clase, id_inasistencia, ae_fecha) VALUES (";
            $qry .= $this->id_estudiante . ",";
            $qry .= $this->id_asignatura . ",";
            $qry .= $this->id_paralelo . ",";
            $qry .= $this->id_dia_semana . ",";
            $qry .= $this->id_hora_clase . ",";
            $qry .= $this->id_inasistencia . ",";
            $qry .= "'" . $this->ae_fecha . "')";
            $consulta = parent::consulta($qry);
            $mensaje = "Inasistencia insertada exitosamente...";
            if (!$consulta)
                $mensaje = "No se pudo insertar la Inasistencia...Error: " . mysql_error();
            return $mensaje;
	}

	function consultarInasistenciaEstudiante()
	{
            $consulta = parent::consulta("SELECT COUNT(*) AS contador"
                                        . " FROM sw_asistencia_estudiante"
                                        . " WHERE id_estudiante = " . $this->id_estudiante
                                        . "   AND id_paralelo = " . $this->id_paralelo
                                        . "   AND id_asignatura = " . $this->id_asignatura
                                        . "   AND id_hora_clase = " . $this->id_hora_clase
                                        . "   AND ae_fecha = '" . $this->ae_fecha . "'");
            return json_encode(parent::fetch_assoc($consulta));
	}
	
	function actualizarInasistenciaEstudiante()
	{
            $qry = "UPDATE sw_asistencia_estudiante SET ";
            $qry .= "id_inasistencia = " . $this->id_inasistencia;
            $qry .= " WHERE id_estudiante = " . $this->id_estudiante;
            $qry .= " AND id_asignatura = " . $this->id_asignatura;
            $qry .= " AND id_paralelo = " . $this->id_paralelo;
            $qry .= " AND id_hora_clase = " . $this->id_hora_clase;
            $qry .= " AND ae_fecha = '" . $this->ae_fecha . "'";
            $consulta = parent::consulta($qry);
            $mensaje = "Inasistencia actualizada exitosamente...";
            if (!$consulta)
                    $mensaje = "No se pudo actualizar la Inasistencia...Error: " . mysql_error();
            return $mensaje;
	}
	
	function eliminarInasistencia()
	{
            $qry = "DELETE FROM sw_inasistencia WHERE id_inasistencia = ". $this->code;
            $consulta = parent::consulta($qry);
            $mensaje = "Inasistencia eliminada exitosamente...";
            if (!$consulta)
                    $mensaje = "No se pudo eliminar la Inasistencia...Error: " . mysql_error();
            return $mensaje;
	}

	function mostrarInasistencia($alineacion)
	{
            if(!isset($alineacion)) $alineacion = "center";

            $mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
            $mensaje .= "<tr>\n";

            $consulta = parent::consulta("SELECT in_nombre, in_abreviatura FROM sw_inasistencia ORDER BY in_abreviatura");
            $num_total_registros = parent::num_rows($consulta);
            if($num_total_registros>0)
            {
                    while($inasistencia = parent::fetch_assoc($consulta))
                    {
                            $mensaje .= "<td width=\"105px\" align=\"".$alineacion."\">" . $inasistencia["in_abreviatura"] . ": " . $inasistencia["in_nombre"] . "</td>\n";
                    }
            }

            $mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
            $mensaje .= "</tr>\n";
            $mensaje .= "</table>\n";
            return $mensaje;
	}
		
}
?>