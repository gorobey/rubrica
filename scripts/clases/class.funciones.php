<?php

class funciones extends MySQL
{

	function calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura)
	{
		// Obtencion del promedio del aporte de evaluacion indicado por el parametro de la funcion
		$consulta = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
		$suma = 0; $contador = 0;
		while($rubrica = parent::fetch_assoc($consulta))
		{
			$contador++;
			$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];
			$qry = parent::consulta("SELECT IFNULL(re_calificacion, 0) AS re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
			$calificacion = parent::fetch_assoc($qry);
			$re_calificacion = $calificacion["re_calificacion"];
			$suma += $re_calificacion;			
		}
		return ($suma/$contador);
	}

	function calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura)
	{
		// Obtencion del promedio del periodo de evaluacion indicado por el parametro de la funcion
		$consulta = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
		$total_aportes = parent::num_rows($consulta);
		$suma = 0; $contador = 0;
		while($aporte = parent::fetch_assoc($consulta))
		{
			$contador++;
			$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
			$promedio_aporte = $this->calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura);
			if ($contador <= $total_aportes - 1)
				$suma += $promedio_aporte;
			else
				$examen = $promedio_aporte;
		}
		$promedio_aportes = $suma / ($total_aportes - 1);
		return (0.8 * $promedio_aportes + 0.2 * $examen);
	}

	function calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura)
	{
		// Obtencion del promedio del periodo lectivo indicado por el parametro de la funcion
		$consulta = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
		$suma = 0; $contador = 0;
		while($periodo = parent::fetch_assoc($consulta))
		{
			$contador++;
			$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
			$promedio_periodo = $this->calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura);
			$suma += $promedio_periodo;
		}
		return ($suma / $contador);
	}
	
	function obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
	{
		// Obtencion de la fecha de cierre del aporte indicado por el campo pe_principal
		$qry = parent::consulta("SELECT ac.ap_fecha_cierre FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_curso c, sw_paralelo pa WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND c.id_curso = pa.id_curso AND ac.id_curso = c.id_curso AND pa.id_paralelo = $id_paralelo AND pe_principal = $pe_principal AND id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($qry);
		return $registro["ap_fecha_cierre"];
	}
	
	function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
	{
		$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($qry);
		$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
		
		$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
		return (parent::num_rows($qry) > 0);		
	}
	
	function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
	{
		$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($qry);
		$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
		
		$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
	
		if($qry) {
			$rubrica_estudiante = parent::fetch_assoc($qry);
			$calificacion = $rubrica_estudiante["re_calificacion"];
		} else {
			$calificacion = 0;
		}
		
		return $calificacion;
	}

}
?>