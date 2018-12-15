<?php

class periodos_lectivos extends MySQL
{
	function getDiasLaborados($id_periodo_lectivo)
	{
		$qry = "SELECT pe_fecha_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$fecha_inicio = parent::fetch_object(parent::consulta($qry))->pe_fecha_inicio;
		$fecha1 = strtotime($fecha_inicio);
		$fecha2 = strtotime(date("Y-m-d"));
		$cont_dias = 0;
		$feriados = array('2018-10-08','2018-11-01','2018-11-02','2018-12-07','2018-12-24',
						  '2018-12-25','2018-12-26','2018-12-27','2018-12-28','2018-12-31',
						  '2019-01-01','2019-02-18','2019-02-19','2019-02-20','2019-02-21',
						  '2019-02-22','2019-04-19','2019-05-03','2019-05-24');
		for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
			if(date('w',$fecha1)!=0 && date('w',$fecha1)!=6 && !in_array(date('Y-m-d',$fecha1),$feriados)){
				$cont_dias++; 
			}
		}
		return $cont_dias;
	}

	function obtenerValorMes($id_periodo_lectivo, $mes)
	{
		$consulta = parent::consulta("SELECT vm_valor FROM sw_valor_mes WHERE id_periodo_lectivo = $id_periodo_lectivo AND vm_mes = $mes");
		return parent::fetch_object($consulta)->vm_valor;
	}
	
	function existePeriodoLectivo($anio_inicio,$anio_fin)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE pe_anio_inicio = $anio_inicio AND pe_anio_fin = $anio_fin");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	function obtenerIdPeriodoLectivo($anio_inicio,$anio_fin)
	{
		$consulta = parent::consulta("SELECT id_periodo_lectivo FROM sw_periodo_lectivo WHERE pe_anio_inicio = $anio_inicio AND pe_anio_fin = $anio_fin");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->id_periodo_lectivo;
	}

	function obtenerIdPeriodoLectivoActual()
	{
		$consulta = parent::consulta("SELECT id_periodo_lectivo FROM sw_periodo_lectivo ORDER BY id_periodo_lectivo DESC LIMIT 0,1");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->id_periodo_lectivo;
	}

	function obtenerPeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosPeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombrePeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
	}

	function insertarPeriodoLectivo($anio_inicial, $anio_final)
	{
		$consulta = parent::consulta("call sp_insertar_periodo_lectivo($anio_inicial,$anio_final)");
		$mensaje = "Per&iacute;odo Lectivo insertado exitosamente.";
		if (!$consulta)
			$mensaje = "No se pudo insertar el per&iacute;odo lectivo. Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarPeriodoLectivo($id_periodo_lectivo, $anio_inicial, $anio_final)
	{
		$consulta = parent::consulta("call sp_actualizar_periodo_lectivo($id_periodo_lectivo,$anio_inicial,$anio_final)");
		$mensaje = "Per&iacute;odo Lectivo actualizado exitosamente.";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el per&iacute;odo lectivo. Error: " . mysql_error();
		return $mensaje;
	}
	
	function cerrarPeriodoLectivo($id_periodo_lectivo)
	{
		$qry = parent::consulta("call sp_cerrar_periodo_terminado(" . $id_periodo_lectivo . ")");
		$mensaje = "Procedimiento almacenado ejecutado exitosamente.";
		if (!qry)
			$mensaje = "No se pudo ejecutar el procedimiento almacenado. Error: " . mysql_error();
		return $mensaje;
	}

	function listarPeriodosLectivos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo ORDER BY pe_anio_inicio DESC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($periodo_lectivo = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $periodo_lectivo["id_periodo_lectivo"];
				$pe_anio_inicio = $periodo_lectivo["pe_anio_inicio"];
				$pe_anio_fin = $periodo_lectivo["pe_anio_fin"];
				$pe_estado = $periodo_lectivo["pe_estado"];
				if ($pe_estado == 'A') $estado = "ACTUAL";
				else if ($pe_estado == 'C') $estado = "CERRADO";
				else if ($pe_estado == 'T') $estado = "TERMINADO";
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"24%\" align=\"left\">$pe_anio_inicio</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$pe_anio_fin</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$estado</td>\n";
				if ($pe_estado !== 'C') {
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarPeriodoLectivo(".$code.")\">editar</a></td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"cerrarPeriodoLectivo(".$code.")\">cerrar</a></td>\n";
				} else {
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarPeriodoLectivo(".$code.")\">editar</a></td>\n";
					$cadena .= "<td width=\"9%\">&nbsp;</td>\n";
				}
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido periodos lectivos...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}
	
	function crear_enlaces_quimestres($id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE pe_principal = 1 AND id_periodo_lectivo = $id_periodo_lectivo");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_enlaces_quimestres\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			$cadena .= "<tr>\n";
			while($periodo_evaluacion = parent::fetch_assoc($consulta))
			{
				$id_periodo_evaluacion = $periodo_evaluacion["id_periodo_evaluacion"];
				$nombre = $periodo_evaluacion["pe_nombre"];
				$cadena .= "<td align=\"center\" class=\"link_form\">\n";
				$cadena .= "<a href=\"#\" onclick=\"obtenerDetallePeriodoEvaluacion(".$id_periodo_evaluacion.")\">DETALLE DEL " . $nombre . "</a>";
				$cadena .= "</td>\n";
			}
			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
			$cadena .= "</tr>\n";
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido periodos de evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function crear_enlaces_parciales($id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND ap_tipo = 1 AND id_periodo_lectivo = $id_periodo_lectivo");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			$cadena .= "<tr>\n";
			while($aporte_evaluacion = parent::fetch_assoc($consulta))
			{
				$id_aporte_evaluacion = $aporte_evaluacion["id_aporte_evaluacion"];
				$nombre = $aporte_evaluacion["ap_nombre"];
				$cadena .= "<td align=\"center\" class=\"link_form\">\n";
				$cadena .= "<a href=\"#\" onclick=\"obtenerDetalleAporteEvaluacion(".$id_aporte_evaluacion.")\">DETALLE DEL " . $nombre . "</a>";
				$cadena .= "</td>\n";
			}
			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
			$cadena .= "</tr>\n";
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido aportes de evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function mostrarTitulosPeriodos($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		// Consulto el tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)
		$consulta = parent::consulta("SELECT pe_abreviatura, pe_principal FROM sw_periodo_evaluacion WHERE pe_principal = " . $this->pe_principal . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		//return "SELECT pe_abreviatura, pe_principal FROM sw_periodo_evaluacion WHERE pe_principal = " . $this->pe_principal . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo;
		$periodo = parent::fetch_assoc($consulta);
		$tipo_periodo = $periodo["pe_principal"];
		$abreviatura = $periodo["pe_abreviatura"];
		
		$mensaje = "<table id=\"titulos_periodos\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE pe_principal = 1 AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($titulo_periodo = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_periodo["pe_abreviatura"] . "</td>\n";
			}
		
			if($tipo_periodo > 1) {
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">".$abreviatura."</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM</td>\n";
				$mensaje .= "<td width=\"*\" align=\"".$alineacion."\">OBSERVACION</td>\n";
			} else {
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">SUP.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">REM.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">GRA.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">P.F.</td>\n";
				$mensaje .= "<td width=\"*\" align=\"".$alineacion."\">OBSERVACION</td>\n";
			}
		}		
		//$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

}
?>