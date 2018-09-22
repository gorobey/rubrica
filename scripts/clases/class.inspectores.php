<?php

class inspectores extends MySQL
{
	
	var $code = "";
    var $id_curso = "";
	var $id_usuario = "";
	var $id_paralelo = "";
    var $id_estudiante = "";
	var $id_periodo_lectivo = "";
    var $id_aporte_evaluacion = "";
    var $id_escala_comportamiento = "";
        
    function equivalencia($promedio) {
        $record = parent::consulta("SELECT ec_equivalencia"
                    . " FROM sw_escala_comportamiento"
                    . " WHERE ec_nota_minima <= " . $promedio
                    . " AND ec_nota_maxima >= " . $promedio);
        $escala = parent::fetch_assoc($record);
        return $escala["ec_equivalencia"];            
    }
    
    function obtenerEscalaComportamiento($calificacion) {
        $consulta = parent::consulta("SELECT * "
                                    . "  FROM sw_escala_comportamiento "
                                    . " WHERE ec_equivalencia = '" . $calificacion . "'");
        return json_encode(parent::fetch_assoc($consulta));
    }

    function obtenerIdComportamientoInspector($id_paralelo, $id_estudiante, $id_aporte_evaluacion) {
        $consulta = parent::consulta("SELECT id_comportamiento_inspector "
                                    . "  FROM sw_comportamiento_inspector "
                                    . " WHERE id_paralelo = " . $id_paralelo 
                                    . "   AND id_estudiante = " . $id_estudiante
                                    . "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
        if(parent::num_rows($consulta) > 0) {
            return json_encode(parent::fetch_assoc($consulta));
        } else {
            return json_encode(array('id_comportamiento_inspector' => 0));
        }
    }

	function truncateFloat($number, $digitos) {
		/*$base = 10;
		$numero = $number * pow($base, $digitos);
		$decimales = explode(".", $numero);
		return $decimales[0] / pow($base, $digitos);*/
		if ($number > 0)
			return round($number - 5 * pow(10, -($digitos + 1)), $digitos);
		else
			return $number;
	}
        
	function asociarParaleloInspector()
	{
		$qry = "INSERT INTO sw_paralelo_inspector (id_paralelo, id_usuario, id_periodo_lectivo) VALUES (";
		$qry .= $this->id_paralelo . ",";
		$qry .= $this->id_usuario . ",";
		$qry .= $this->id_periodo_lectivo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Inspector asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar el Inspector...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarParaleloInspector()
	{
		$qry = "DELETE FROM sw_paralelo_inspector WHERE id_paralelo_inspector =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Inspector des-asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar el Inspector...Error: " . mysql_error();
		return $mensaje;
	}	

	function listarParalelosInspectores()
	{
		$consulta = parent::consulta("SELECT id_paralelo_inspector, cu_nombre, es_figura, pa_nombre, us_titulo, us_fullname FROM sw_paralelo_inspector pi, sw_paralelo p, sw_curso c, sw_especialidad e, sw_usuario u WHERE pi.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND pi.id_usuario = u.id_usuario ORDER BY cu_orden, e.id_especialidad, pa_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($inspector = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $inspector["id_paralelo_inspector"];
				$paralelo = $inspector["cu_nombre"] . " " . $inspector["pa_nombre"] . " - [" . $inspector["es_figura"] . "]";
				$inspector = $inspector["us_titulo"] . " " . $inspector["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"37%\" align=\"left\">$paralelo</td>\n";	
				$cadena .= "<td width=\"38%\" align=\"left\">$inspector</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se ha asociado inspectores...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarParalelosInspectores()
	{
		$consulta = parent::consulta("SELECT id_paralelo_inspector, 
                                             cu_nombre, 
                                             es_figura, 
                                             pa_nombre, 
                                             us_titulo, 
                                             us_fullname 
                                        FROM sw_paralelo_inspector pi, 
                                             sw_paralelo p, 
                                             sw_curso c, 
                                             sw_especialidad e, 
                                             sw_usuario u 
                                       WHERE pi.id_paralelo = p.id_paralelo 
                                         AND p.id_curso = c.id_curso 
                                         AND c.id_especialidad = e.id_especialidad 
                                         AND pi.id_usuario = u.id_usuario 
                                         AND pi.id_periodo_lectivo = " . $this->id_periodo_lectivo
                                   . " ORDER BY cu_orden, e.id_especialidad, pa_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = ""; $total_inspectores = 0;
		if($num_total_registros>0)
		{
			while($inspector = parent::fetch_assoc($consulta))
			{
				$total_inspectores++;
				$cadena .= "<tr>\n";
				$code = $inspector["id_paralelo_inspector"];
				$paralelo = $inspector["cu_nombre"] . " " . $inspector["pa_nombre"] . " - [" . $inspector["es_figura"] . "]";
				$inspector = $inspector["us_titulo"] . " " . $inspector["us_fullname"];
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$paralelo</td>\n";
				$cadena .= "<td>$inspector</td>\n";
				$cadena .= "<td><button onclick='eliminarAsociacion(".$code.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han asociado inspectores...</td>\n";
			$cadena .= "</tr>\n";
		}
		$datos = array('cadena' => $cadena, 
				       'total_inspectores' => $total_inspectores);
        return json_encode($datos);
	}

	function listarParalelosInspector()
	{
            $consulta = parent::consulta("SELECT pi.id_paralelo, cu_nombre, pa_nombre FROM sw_paralelo_inspector pi, sw_paralelo p, sw_curso c WHERE pi.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND pi.id_usuario = " . $this->id_usuario);
            $num_total_registros = parent::num_rows($consulta);
            $cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
            if($num_total_registros>0)
            {
                $contador = 0;
                while($inspector = parent::fetch_assoc($consulta))
                {
                    $contador += 1;
                    $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                    $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                    $code = $inspector["id_paralelo"];
                    $curso = $inspector["cu_nombre"];
                    $paralelo = $inspector["pa_nombre"];
                    $cadena .= "<td width=\"6%\">$contador</td>\n";
                    $cadena .= "<td width=\"38%\" align=\"left\">$curso</td>\n";	
                    $cadena .= "<td width=\"38%\" align=\"left\">$paralelo</td>\n";
                    $cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarParalelo(".$code.",'".$curso."','".$paralelo."')\">seleccionar</a></td>\n";
                    $cadena .= "</tr>\n";
                }
            }
            else {
                $cadena .= "<tr>\n";
                $cadena .= "<td>No se ha asociado paralelos a este inspector...</td>\n";
                $cadena .= "</tr>\n";
            }
            $cadena .= "</table>";
            return $cadena;
	}

        function contarParalelosInspector()
	{
            $consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_paralelo_inspector WHERE id_usuario = " . $this->id_usuario . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
            return json_encode(parent::fetch_assoc($consulta));
	}

       	function paginarParalelosInspector($cantidad_registros,$numero_pagina,$total_registros)
	{
            $total_paginas = ceil($total_registros / $cantidad_registros);
            $mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarParalelosInspector(".$cantidad_registros.",1,".$total_registros.")'> Primero </a> </span>";
            if (($numero_pagina - 1) > 0) {
                $mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarParalelosInspector(".$cantidad_registros.",".($numero_pagina-1).",".$total_registros.")'>Anterior</a></span>";
            } else {
                $mensaje .= "<span> < Anterior</span>";
            }
            for ($i=1; $i <= $total_paginas; $i++) {
                if ($numero_pagina == $i) {
                    $mensaje .= "<b> P&aacute;gina ".$numero_pagina."</b>";
                } else {
                    $mensaje .= "<span class='link_table'> <a href='#' onclick='paginarParalelosInspector(".$cantidad_registros.",".$i.",".$total_registros.")'>$i</a></span>";
                }
            }
            if (($numero_pagina+1) <= $total_paginas) {
                $mensaje .= " <span class='link_table'><a href='#' onclick='paginarParalelosInspector(".$cantidad_registros.",".($numero_pagina+1).",".$total_registros.")'>Siguiente</a> > </span>";
            } else {
                $mensaje .= " <span>Siguiente</a> > </span>";
            }
            $mensaje .= " <span class='link_table'><a href='#' onclick='paginarParalelosInspector(".$cantidad_registros.",".$total_paginas.",".$total_registros.")'>Ultimo</a></span> >>"; 
            return $mensaje;
	}

	function listarParalelosPorInspector($cantidad_registros, $numero_pagina)
	{
            $inicio = ($numero_pagina - 1) * $cantidad_registros;
            $consulta = parent::consulta("SELECT c.id_curso, pa.id_paralelo, es_figura, cu_nombre, pa_nombre "
                                       . "FROM sw_paralelo_inspector p, sw_paralelo pa, sw_curso c, sw_especialidad e "
                                       . "WHERE p.id_paralelo = pa.id_paralelo AND "
                                       . "pa.id_curso = c.id_curso AND "
                                       . "c.id_especialidad = e.id_especialidad AND "
                                       . "p.id_usuario = " . $this->id_usuario 
                                       . " AND p.id_periodo_lectivo = " . $this->id_periodo_lectivo 
                                       . " ORDER BY c.id_curso, pa.id_paralelo ASC LIMIT $inicio, $cantidad_registros");
            $num_total_registros = parent::num_rows($consulta);
            $cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
            if($num_total_registros>0)
            {
                $contador = $inicio;
                while($paralelo = parent::fetch_assoc($consulta))
                {
                    $contador++;
                    $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                    $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                    $id_curso = $paralelo["id_curso"];
                    $codigo = $paralelo["id_paralelo"];
                    $figura = $paralelo["es_figura"];
                    $curso = $paralelo["cu_nombre"] . " " . $paralelo["es_figura"];
                    $paralelo = $paralelo["pa_nombre"];
                    $cadena .= "<td width=\"5%\">$contador</td>\n";	
                    $cadena .= "<td width=\"71%\" align=\"left\">$curso</td>\n";
                    $cadena .= "<td width=\"6%\" align=\"left\">$paralelo</td>\n";
                    $cadena .= "<td width=\"18%\" class=\"link_form\" align=\"center\"><a href=\"#\" onclick=\"seleccionarParalelo(".$id_curso.",".$codigo.",'".$curso."','".$paralelo."')\">Seleccionar</a></td>\n";
                    $cadena .= "</tr>\n";	
                }
            }
            else {
                $cadena .= "<tr>\n";	
                $cadena .= "<td>No se han asociado paralelos a este inspector...</td>\n";
                $cadena .= "</tr>\n";	
            }
            $cadena .= "</table>";	
            return $cadena;
	}

       	function mostrarTitulosIndices($alineacion)
	{
            if(!isset($alineacion)) $alineacion = "center";

            $mensaje = "<table id=\"titulos_indices\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
            $mensaje .= "<tr>\n";

            $consulta = parent::consulta("SELECT ie_abreviatura FROM sw_indice_evaluacion_def ORDER BY ie_orden");
            $num_total_registros = parent::num_rows($consulta);
            if($num_total_registros > 0)
            {
                while($titulo_indice = parent::fetch_assoc($consulta))
                {
                    $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_indice["ie_abreviatura"] . "</td>\n";
                }
                $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">TOTAL</td>\n";
                $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM.</td>\n";
                $mensaje .= "<td width=\"80px\" align=\"".$alineacion."\">EQUIV.</td>\n";
            }

            $mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
            $mensaje .= "</tr>\n";
            $mensaje .= "</table>\n";
            return $mensaje;
	}

       	function listarEstudiantesParaleloInspector($id_paralelo, $id_aporte_evaluacion, $id_curso)
	{
            $consulta = parent::consulta("SELECT e.id_estudiante, "
                                       . "       e.es_apellidos, "
                                       . "       e.es_nombres "
                                       . "  FROM sw_estudiante_periodo_lectivo ep, "
                                       . "       sw_estudiante e "
                                       . " WHERE ep.id_estudiante = e.id_estudiante "
                                       . "   AND ep.id_paralelo = " . $id_paralelo
                                       . "   AND es_retirado = 'N' "
                                       . " ORDER BY es_apellidos, es_nombres ASC");
            $num_total_registros = parent::num_rows($consulta);
            $cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
            if($num_total_registros > 0)
            {
                $contador = 0;
                while($paralelo = parent::fetch_assoc($consulta))
                {
                    $contador++;
                    $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                    $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                    $id_estudiante = $paralelo["id_estudiante"];
                    $apellidos = $paralelo["es_apellidos"];
                    $nombres = $paralelo["es_nombres"];
                    $cadena .= "<td width=\"5%\">$contador</td>\n";	
                    $cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
                    $cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
                    $qry = parent::consulta("SELECT co_calificacion, "
                                          . "       id_escala_comportamiento "
                                          . "  FROM sw_comportamiento_inspector "
                                          . " WHERE id_estudiante = " . $paralelo["id_estudiante"]
                                          . "   AND id_paralelo = " . $id_paralelo
                                          . "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
                    $num_total_registros = parent::num_rows($qry);
                    $comportamiento_estudiante = parent::fetch_assoc($qry);
                    if($num_total_registros>0) {
                        $calificacion = $comportamiento_estudiante["co_calificacion"];
                        $id_escala_comportamiento = $comportamiento_estudiante["id_escala_comportamiento"];
                    } else {
                        $calificacion = "";
                        $id_escala_comportamiento = 0;
                    }
                    $cadena .= "<td width=\"10%\" align=\"left\"><input type=\"text\" id=\"puntaje_".$contador."\" class=\"inputPequenio\" style=\"text-transform: uppercase;\" value=\"".$calificacion."\"";
                    $qry = parent::consulta("SELECT ac.ap_estado FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND a.id_aporte_evaluacion = $id_aporte_evaluacion AND ac.id_curso = $id_curso");
                    $aporte = parent::fetch_assoc($qry);
                    $estado_aporte = $aporte["ap_estado"];
                    if($estado_aporte=='A') {
                        $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_aporte_evaluacion.")\" /></td>\n";
                    } else {
                        $cadena .= " disabled /></td>\n";
                    }
                    $qry = parent::consulta("SELECT ec_relacion FROM sw_escala_comportamiento WHERE id_escala_comportamiento = $id_escala_comportamiento");
                    if (!$qry) {
                        die("NO SE HAN DEFINIDO LAS ESCALAS DE COMPORTAMIENTO!");
                    } else if(parent::num_rows($qry) > 0) {
                        $escala_comportamiento = parent::fetch_assoc($qry);
                        $descripcion = $escala_comportamiento["ec_relacion"];
                    } else {
                        $descripcion = "";
                    }
                    $cadena .= "<td width=\"50%\" align=\"left\"><input type=\"text\" id=\"equivalencia_".$contador."\" disabled value=\"".$descripcion."\" /></td>\n";
                    $cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
                    $cadena .= "</tr>\n";	
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

    function existeCalifComportamiento()
	{
        $consulta = parent::consulta("SELECT * FROM sw_comportamiento_inspector WHERE id_estudiante = " . $this->id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
        //echo "SELECT * FROM sw_comportamiento_inspector WHERE id_estudiante = " . $this->id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros > 0);
	}

       	function insertarCalifComportamiento()
	{
		$qry = "INSERT INTO sw_comportamiento_inspector SET ";
		$qry .= "id_estudiante = " . $this->id_estudiante . ",";
		$qry .= "id_paralelo = " . $this->id_paralelo . ",";
                $qry .= "id_escala_comportamiento = " . $this->id_escala_comportamiento . ",";
		$qry .= "id_aporte_evaluacion = " . $this->id_aporte_evaluacion . ",";
		$qry .= "co_calificacion = '" . $this->co_calificacion . "'";
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n insertada exitosamente...";
		if (!$consulta)
                    $mensaje = "No se pudo realizar la inserci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

        function actualizarCalifComportamiento()
	{
		$qry = "UPDATE sw_comportamiento_inspector SET ";
		$qry .= "co_calificacion = '" . $this->co_calificacion . "'";
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_paralelo = " . $this->id_paralelo;
        //$qry .= " AND id_escala_comportamiento = " . $this->id_escala_comportamiento;
		$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

        function eliminarCalifComportamiento($id_comportamiento_inspector)
	{
		$qry = "DELETE FROM sw_comportamiento_inspector ";
		$qry .= " WHERE id_comportamiento_inspector = " . $id_comportamiento_inspector;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n eliminada exitosamente...";
		if (!$consulta)
                    $mensaje = "No se pudo realizar la eliminaci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

}
?>