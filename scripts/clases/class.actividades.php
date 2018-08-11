<?php

class actividades extends MySQL
{
	
	var $code = "";
	
	function consultarActividades($fecha)
	{
		$consulta = parent::consulta("SELECT us_titulo, us_fullname, as_nombre, es_apellidos, es_nombres, cu_nombre, pa_nombre, pe_nombre, ap_nombre, ru_nombre, re_calificacion_nueva, re_calificacion_antigua, rl_accion FROM sw_rubrica_estudiante_log rl, sw_usuario u, sw_asignatura a, sw_estudiante e, sw_curso c, sw_paralelo p, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE rl.id_usuario = u.id_usuario AND rl.id_asignatura = a.id_asignatura AND rl.id_estudiante = e.id_estudiante AND rl.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND rl.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ru.id_aporte_evaluacion = ap.id_aporte_evaluacion AND ap.id_periodo_evaluacion = pe.id_periodo_evaluacion AND re_fecha_modificacion like '$fecha%'");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($actividad = parent::fetch_assoc($consulta))
			{
				$contador ++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$docente = $actividad["us_titulo"]. " " . $actividad["us_fullname"];
				$asignatura = $actividad["as_nombre"];
				$nombreEstudiante = $actividad["es_apellidos"] . " " . $actividad["es_nombres"];
				$nombreCurso = $actividad["cu_nombre"] . " " . $actividad["pa_nombre"];
				$nombrePeriodo = $actividad["pe_nombre"];
				$nombreAporte = $actividad["ap_nombre"];
				$nombreRubrica = $actividad["ru_nombre"];
				$calificacion_nueva = $actividad["re_calificacion_nueva"];
				$calificacion_antigua = $actividad["re_calificacion_antigua"];
				$accion = $actividad["rl_accion"];
				$cadena .= "<td width=\"10%\">$docente</td>\n";
				$cadena .= "<td width=\"10%\">$asignatura</td>\n";
				$cadena .= "<td width=\"10%\">$nombreEstudiante</td>\n";
				$cadena .= "<td width=\"10%\">$nombreCurso</td>\n";
				$cadena .= "<td width=\"10%\">$nombrePeriodo</td>\n";
				$cadena .= "<td width=\"10%\">$nombreAporte</td>\n";
				$cadena .= "<td width=\"10%\">$nombreRubrica</td>\n";
				$cadena .= "<td width=\"8%\">$calificacion_nueva</td>\n";
				$cadena .= "<td width=\"8%\">$calificacion_antigua</td>\n";
				$cadena .= "<td width=\"14%\">$accion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar las columnas
				$cadena .= "</tr>\n";
			}	
		}
		else 
		{
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han realizado actividades sobre las calificaciones...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}
}	
?>