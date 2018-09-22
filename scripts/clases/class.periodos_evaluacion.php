<?php

class periodos_evaluacion extends MySQL
{
	
	var $code = "";
	var $pe_nombre = "";
	var $pe_principal = "";
	var $pe_abreviatura = "";
	var $pe_tipo = "";
	var $id_periodo_lectivo = "";
	var $id_curso = "";

	function truncateFloat($number, $digitos) {
		if ($number > 0)
			return round($number - 5 * pow(10, -($digitos + 1)), $digitos);
		else
			return $number;
	}
		
	function existePeriodoEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE pe_nombre = '$nombre'");
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

	function obtenerNombrePeriodoEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->pe_nombre;
	}

	function getNombrePeriodoEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_shortname FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->pe_shortname;
	}

	function obtenerIdPeriodoEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE pe_nombre = '$nombre'");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->id_periodo_evaluacion;
	}

	function obtenerPeriodoEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerTipoPeriodo()
	{
		$consulta = parent::consulta("SELECT pe_principal FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = ".$this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdAporte()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_periodo_evaluacion = ".$this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdAporteEvaluacionSupRemGracia()
	{
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac,       sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = " . $this->id_curso . " AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = " . $this->pe_principal);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function contarCalificacionesErroneas($id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_rubrica_estudiante r, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND pe.id_periodo_evaluacion = $id_periodo_evaluacion AND re_calificacion > 10");
		return json_encode(parent::fetch_assoc($consulta));	
	}

	function listarCalificacionesErroneas($id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT as_nombre, es_apellidos, es_nombres, us_titulo, us_fullname, cu_nombre, pa_nombre, ap_nombre, ru_nombre, re_calificacion FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e, sw_paralelo_asignatura pa, sw_usuario u, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru, sw_curso cu, sw_paralelo p WHERE r.id_paralelo = pa.id_paralelo AND pa.id_paralelo = p.id_paralelo AND p.id_curso = cu.id_curso AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pe.id_periodo_evaluacion = $id_periodo_evaluacion AND (re_calificacion > 10 OR re_calificacion < 0) ORDER BY us_fullname, as_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($periodos_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$asignatura = $periodos_evaluacion["as_nombre"];
				$docente = $periodos_evaluacion["us_titulo"] . " " . $periodos_evaluacion["us_fullname"];
				$estudiante = $periodos_evaluacion["es_apellidos"] . " " . $periodos_evaluacion["es_nombres"];
				$curso = $periodos_evaluacion["cu_nombre"] . " \"". $periodos_evaluacion["pa_nombre"] . "\"";
				$aporte = $periodos_evaluacion["ap_nombre"];
				$rubrica = $periodos_evaluacion["ru_nombre"];
				$calificacion = $periodos_evaluacion["re_calificacion"];
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"15%\">$asignatura</td>\n";
				$cadena .= "<td width=\"15%\">$docente</td>\n";
				$cadena .= "<td width=\"15%\">$estudiante</td>\n";
				$cadena .= "<td width=\"15%\">$curso</td>\n";
				$cadena .= "<td width=\"15%\">$aporte</td>\n";
				$cadena .= "<td width=\"15%\">$rubrica</td>\n";
				$cadena .= "<td width=\"5%\">$calificacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n";	
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han encontrado calificaciones err&oacute;neas...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listar_periodos_evaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY id_periodo_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($periodos_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarPeriodoEvaluacion(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarPeriodoEvaluacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Periodos de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargar_periodos_evaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY id_periodo_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($periodos_evaluacion = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];	
				$cadena .= "<td>$id</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button onclick='editPerEval(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deletePerEval(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han definido Periodos de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}	
		return $cadena;
	}

	function insertarPeriodoEvaluacion()
	{
		$qry = "INSERT INTO sw_periodo_evaluacion (id_periodo_lectivo, pe_nombre, pe_abreviatura, pe_principal) VALUES (";
		$qry .= $this->id_periodo_lectivo .",";
		$qry .= "'" . $this->pe_nombre . "',";
		$qry .= "'" . $this->pe_abreviatura . "',";
		$qry .= $this->pe_tipo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Periodo de Evaluaci&oacute;n insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Periodo de Evaluaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarPeriodoEvaluacion()
	{
		$qry = "UPDATE sw_periodo_evaluacion SET ";
		$qry .= "pe_nombre = '" . $this->pe_nombre . "',";
		$qry .= "pe_abreviatura = '" . $this->pe_abreviatura . "',";
		$qry .= "pe_principal = " . $this->pe_tipo;
		$qry .= " WHERE id_periodo_evaluacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Periodo de Evaluacion " . $this->pe_nombre . " actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Periodo de Evaluacion...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarPeriodoEvaluacion()
	{
		$qry = "SELECT * FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion=".$this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros > 0){
			$mensaje = "No se puede eliminar el Periodo de Evaluacion, porque tiene Aportes de Evaluacion asociados.";
		} else {
			$qry = "DELETE FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Periodo de Evaluacion eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el Periodo de Evaluacion...Error: " . mysql_error();
		}
		return $mensaje;
	}
	
	function mostrarTitulosPeriodos($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		// Consulto el tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)
		$consulta = parent::consulta("SELECT pe_abreviatura, pe_principal FROM sw_periodo_evaluacion WHERE pe_principal = " . $this->pe_principal . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
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
	
	function calcularPromedioQuimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) {
		// Primero se calcula el promedio de cada parcial
		$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
		$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
		while($aporte = parent::fetch_assoc($aporte_evaluacion))
		{
			$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
			$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
			$suma_rubricas = 0; $contador_rubricas = 0;
			$total_rubricas = parent::num_rows($rubrica_evaluacion);
			while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
			{
				$contador_rubricas++;
				$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
				$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
				$total_registros = parent::num_rows($qry);
				if($total_registros > 0) {
					$rubrica_estudiante = parent::fetch_assoc($qry);
					$calificacion = $rubrica_estudiante["re_calificacion"];
				} else {
					$calificacion = 0;
				}
				$suma_rubricas += $calificacion;
			}
			
			$promedio = $suma_rubricas / $contador_rubricas;

			if($contador_aportes < $total_rubricas)
				$suma_promedios += $promedio;
			else 
				$examen_quimestral = $promedio;
				
			$contador_aportes++;			
		}
		
		// Aqui debo calcular el ponderado de los promedios parciales
		$promedio_aportes = $this->truncateFloat($suma_promedios / ($contador_aportes - 1),2);
		$ponderado_aportes = $this->truncateFloat(0.8 * $promedio_aportes,2);
		$ponderado_examen = $this->truncateFloat(0.2 * $examen_quimestral,2);
		
		$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;		
		
		return $calificacion_quimestral;
	}
}
?>