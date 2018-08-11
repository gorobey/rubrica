<?php

class recomendaciones extends MySQL
{

	var $id_periodo_evaluacion = "";
	var $id_periodo_lectivo = "";
	var $re_plan_de_mejora = "";

	function existeRecomendacionesQuimestrales($id_escala_calificaciones,$id_paralelo_asignatura,$id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_quimestrales WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_periodo_evaluacion = $id_periodo_evaluacion");
		$num_total_registros = parent::num_rows($consulta);
		return ($num_total_registros>0);
	}

	function editarRecomendacionesQuimestrales($id_escala_calificaciones,$id_paralelo_asignatura,$id_periodo_evaluacion)
	{
		if (!$this->existeRecomendacionesQuimestrales($id_escala_calificaciones,$id_paralelo_asignatura,$id_periodo_evaluacion)) {
			$qry = "INSERT INTO sw_recomendaciones_quimestrales (id_escala_calificaciones, id_paralelo_asignatura, id_periodo_evaluacion, re_plan_de_mejora_quimestral) VALUES (";
			$qry .= $id_escala_calificaciones . ",";
			$qry .= $id_paralelo_asignatura . ",";
			$qry .= $id_periodo_evaluacion . ",";
			$qry .= "'" . $this->re_plan_de_mejora . "')";
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones quimestrales insertadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudieron insertar las recomendaciones quimestrales...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		} else {
			$qry = "UPDATE sw_recomendaciones_quimestrales SET ";
			$qry .= "re_plan_de_mejora_quimestral = '" . $this->re_plan_de_mejora . "'";
			$qry .= " WHERE id_escala_calificaciones = $id_escala_calificaciones";
			$qry .= " AND id_paralelo_asignatura = $id_paralelo_asignatura";
			$qry .= " AND id_periodo_evaluacion = $id_periodo_evaluacion";
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones quimestrales actualizadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudieron actualizar las recomendaciones quimestrales...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		}
		//return $mensaje . "Query: " . $qry;
		return $mensaje;
	}

	function existeRecomendacionesAnuales($id_escala_calificaciones,$id_paralelo_asignatura)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_anuales WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		$num_total_registros = parent::num_rows($consulta);
		return ($num_total_registros>0);
	}

	function editarPlanMejoraAnual($id_escala_calificaciones,$id_paralelo_asignatura)
	{
		if (!$this->existeRecomendacionesAnuales($id_escala_calificaciones,$id_paralelo_asignatura)) {
			$qry = "INSERT INTO sw_recomendaciones_anuales (id_escala_calificaciones, id_paralelo_asignatura, id_periodo_lectivo, re_plan_de_mejora_anual) VALUES (";
			$qry .= $id_escala_calificaciones . ",";
			$qry .= $id_paralelo_asignatura . ",";
			$qry .= $this->id_periodo_lectivo . ",";
			$qry .= "'" . $this->re_plan_de_mejora . "')";
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones anuales insertadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudieron insertar las recomendaciones anuales...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		} else {
			$qry = "UPDATE sw_recomendaciones_anuales SET ";
			$qry .= "re_plan_de_mejora_anual = '" . $this->re_plan_de_mejora . "'";
			$qry .= " WHERE id_escala_calificaciones = $id_escala_calificaciones";
			$qry .= " AND id_paralelo_asignatura = $id_paralelo_asignatura";
			$qry .= " AND id_periodo_lectivo = " . $this->id_periodo_lectivo;
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones anuales actualizadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudieron actualizar las recomendaciones anuales...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		}
		return $mensaje;
	}

	function existeRecomendaciones($id_escala_calificaciones,$id_paralelo_asignatura,$id_aporte_evaluacion)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
		$num_total_registros = parent::num_rows($consulta);
		return ($num_total_registros>0);
	}

	function editarRecomendaciones($id_escala_calificaciones,$id_paralelo_asignatura,$id_aporte_evaluacion)
	{
		if (!$this->existeRecomendaciones($id_escala_calificaciones,$id_paralelo_asignatura,$id_aporte_evaluacion)) {
			$qry = "INSERT INTO sw_recomendaciones (id_escala_calificaciones,id_paralelo_asignatura,id_aporte_evaluacion,re_plan_de_mejora) VALUES (";
			$qry .= $id_escala_calificaciones . ",";
			$qry .= $id_paralelo_asignatura . ",";
			$qry .= $id_aporte_evaluacion . ",";
			$qry .= "'" . $this->re_plan_de_mejora . "')";
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones insertadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo insertar las recomendaciones...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		} else {
			$qry = "UPDATE sw_recomendaciones SET ";
			$qry .= "re_plan_de_mejora = '" . $this->re_plan_de_mejora . "'";
			$qry .= " WHERE id_escala_calificaciones = $id_escala_calificaciones";
			$qry .= " AND id_paralelo_asignatura = $id_paralelo_asignatura";
			$qry .= " AND id_aporte_evaluacion = $id_aporte_evaluacion";
			$consulta = parent::consulta($qry);
			$mensaje = "Recomendaciones actualizadas exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo actualizar las recomendaciones...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		}
		return $mensaje;
	}
}
?>