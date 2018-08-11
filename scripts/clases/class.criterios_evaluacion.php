<?php

class criterios_evaluacion extends MySQL
{
	
	var $code = "";
	var $cr_descripcion = "";
	var $cr_ponderacion = "";
	var $id_criterio_evaluacion = "";
	var $id_rubrica_evaluacion = "";
	var $id_asignatura = "";
	var $id_usuario = "";
	var $id_paralelo = "";
	
	function existeCriterioEvaluacion($id_rubrica, $nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_criterio_evaluacion WHERE id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion . " AND cr_descripcion = '$nombre'");
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

	function existeCriterioPersonalizado($id_rubrica, $nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_criterio_personalizado WHERE id_rubrica_personalizada = " . $this->id_rubrica_evaluacion . " AND cp_descripcion = '$nombre'");
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

	function obtenerIdCriterioEvaluacion($nombre, $tipo)
	{
		$consulta = parent::consulta("SELECT id_criterio_evaluacion FROM sw_criterio_evaluacion WHERE cr_descripcion = '$nombre' AND cr_tipo = '$tipo'");
		$criterio_evaluacion = parent::fetch_object($consulta);
		return $criterio_evaluacion->id_criterio_evaluacion;
	}

	function obtenerCriterioEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_criterio_evaluacion WHERE id_criterio_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarCriteriosEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_criterio_evaluacion, ru_nombre, cr_descripcion, cr_ponderacion FROM sw_criterio_evaluacion c, sw_rubrica_evaluacion r WHERE c.id_rubrica_evaluacion = r.id_rubrica_evaluacion AND c.id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion . " ORDER BY id_criterio_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($criterios_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $criterios_evaluacion["id_criterio_evaluacion"];
				$rubrica = $criterios_evaluacion["ru_nombre"];
				$name = $criterios_evaluacion["cr_descripcion"];
				$ponderacion = $criterios_evaluacion["cr_ponderacion"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"24%\" align=\"left\">$rubrica</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$ponderacion</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCriterioEvaluacion(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarCriterioEvaluacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No existen criterios de evaluaci&oacute;n relacionados con esta r&uacute;brica...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCriteriosDocente()
	{
		$consulta = parent::consulta("SELECT c.id_criterio_personalizado, ru_nombre, as_nombre, cp_descripcion, cp_ponderacion FROM sw_criterio_personalizado c, sw_rubrica_docente r, sw_rubrica_evaluacion ru, sw_asignatura a WHERE r.id_rubrica_evaluacion = ru.id_rubrica_evaluacion AND r.id_criterio_personalizado = c.id_criterio_personalizado AND r.id_asignatura = a.id_asignatura AND c.id_rubrica_personalizada = " . $this->id_rubrica_evaluacion . " AND r.id_asignatura = " . $this->id_asignatura . " AND r.id_usuario = " . $this->id_usuario . " AND r.id_paralelo = " . $this->id_paralelo . " ORDER BY id_criterio_personalizado ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0) {
			$contador = 0;
			while($criterios_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $criterios_evaluacion["id_criterio_personalizado"];
				$rubrica = $criterios_evaluacion["ru_nombre"];
				$asignatura = $criterios_evaluacion["as_nombre"];
				$name = $criterios_evaluacion["cp_descripcion"];
				$ponderacion = $criterios_evaluacion["cp_ponderacion"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"18%\" align=\"left\">$rubrica</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$asignatura</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$ponderacion</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCriterioEvaluacion(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarCriterioEvaluacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		} else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No existen criterios de evaluaci&oacute;n personalizados relacionados con esta r&uacute;brica...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function personalizarCriteriosDocente()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_docente WHERE id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion . " AND id_usuario = " . $this->id_usuario . " AND id_asignatura = " . $this->id_asignatura . " AND id_paralelo = " . $this->id_paralelo);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros==0) {
			// No existen criterios personalizados... asociar los criterios predefinidos si es que existen
			$consulta = parent::consulta("SELECT r.id_rubrica_evaluacion, id_criterio_evaluacion, cr_descripcion, cr_ponderacion FROM sw_rubrica_evaluacion r, sw_criterio_evaluacion c WHERE r.id_rubrica_evaluacion = c.id_rubrica_evaluacion AND r.id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion);
			$num_total_registros = parent::num_rows($consulta);
			if($num_total_registros>0) {
				while($criterios_evaluacion = parent::fetch_assoc($consulta))
				{
					// Insercion en la tabla sw_criterio_personalizado
					$qry = "INSERT INTO sw_criterio_personalizado (id_rubrica_personalizada, cp_descripcion, cp_ponderacion) VALUES (";
					$qry .= $criterios_evaluacion["id_rubrica_evaluacion"] . ",";
					$qry .= "'" . $criterios_evaluacion["cr_descripcion"] . "',";
					$qry .= $criterios_evaluacion["cr_ponderacion"] . ")";
					$resultado = parent::consulta($qry);
					// Hallar el ultimo id_criterio_evaluacion recien insertado
					$maximo = parent::consulta("SELECT MAX(id_criterio_personalizado) AS max_id_criterio FROM sw_criterio_personalizado");
					$maximo = parent::fetch_assoc($maximo);
					// Insercion en la tabla sw_rubrica_docente
					$qry = "INSERT INTO sw_rubrica_docente (id_rubrica_evaluacion, id_criterio_personalizado, id_usuario, id_asignatura, id_paralelo) VALUES (";
					$qry .= $criterios_evaluacion["id_rubrica_evaluacion"] . ",";
					$qry .= $maximo["max_id_criterio"] . ",";
					$qry .= $this->id_usuario . ",";
					$qry .= $this->id_asignatura . ",";
					$qry .= $this->id_paralelo . ")";
					$resultado = parent::consulta($qry);
				}
				// Ahora si a listar los criterios recien asociados...
				$cadena = $this->listarCriteriosDocente();
			} else {
				$cadena = "No se han definido criterios de evaluaci&oacute;n para esta r&uacute;brica...";
			}
		} else {
			$cadena = $this->listarCriteriosDocente();
		}
		return $cadena;
	}

	function insertarCriterioEvaluacion()
	{
		// Primero verifico si la suma de las ponderaciones de los criterios personalizados no excede el valor de 1.0
		
		$qry = "SELECT SUM(cr_ponderacion) AS suma FROM sw_criterio_evaluacion WHERE id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion;

		$consulta = parent::consulta($qry);
		$suma_ponderacion = parent::fetch_object($consulta);
		
		if($suma_ponderacion->suma + $this->cr_ponderacion > 1)
			$mensaje = "No se pudo insertar el Criterio de Evaluaci&oacute;n (LA SUMA DE LAS PONDERACIONES EXCEDE EL 100%)...";
		else {
			$qry = "INSERT INTO sw_criterio_evaluacion (id_rubrica_evaluacion, cr_descripcion, cr_ponderacion) VALUES (";
			$qry .= $this->id_rubrica_evaluacion .",";
			$qry .= "'" . $this->cr_descripcion . "',";
			$qry .= $this->cr_ponderacion . ")";
			$consulta = parent::consulta($qry);
			$mensaje = "Criterio de Evaluaci&oacute;n insertado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo insertar la Criterio de Evaluaci&oacute;n...Error: " . mysql_error();
		}
		
		return $mensaje;
	}

	function insertarCriterioPersonalizado()
	{
		// Primero verifico si la suma de las ponderaciones de los criterios personalizados no excede el valor de 1.0
		
		$qry = "SELECT SUM(cp_ponderacion) AS suma FROM sw_criterio_personalizado WHERE id_rubrica_personalizada = " . $this->id_rubrica_evaluacion;

		$consulta = parent::consulta($qry);
		$suma_ponderacion = parent::fetch_object($consulta);
		
		if($suma_ponderacion->suma + $this->cr_ponderacion > 1)
			$mensaje = "No se pudo insertar el Criterio Personalizado (LA SUMA DE LAS PONDERACIONES EXCEDE EL 100%)...";
		else {
			$qry = "INSERT INTO sw_criterio_personalizado (id_rubrica_personalizada, cp_descripcion, cp_ponderacion) VALUES (";
			$qry .= $this->id_rubrica_evaluacion .",";
			$qry .= "'" . $this->cr_descripcion . "',";
			$qry .= $this->cr_ponderacion . ")";
			$consulta = parent::consulta($qry);
			$mensaje = "Criterio de Evaluaci&oacute;n Personalizado insertado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo insertar la Criterio de Evaluaci&oacute;n Personalizado...Error: " . mysql_error();
		}
		
		return $mensaje;
	}

	function actualizarCriterioEvaluacion()
	{
		$qry = "UPDATE sw_criterio_evaluacion SET ";
		$qry .= "id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion . ",";
		$qry .= "cr_descripcion = '" . $this->cr_descripcion . "',";
		$qry .= "cr_ponderacion = " . $this->cr_ponderacion;
		$qry .= " WHERE id_criterio_evaluacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Criterio de Evaluaci&oacute;n \"" . $this->cr_descripcion . "\" actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Criterio de Evaluaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarCriterioEvaluacion()
	{
		$qry = "DELETE FROM sw_rubrica_docente WHERE id_criterio_personalizado = ". $this->code;
		$consulta = parent::consulta($qry) or die(mysql_error());
		$qry = "DELETE FROM sw_criterio_evaluacion WHERE id_criterio_evaluacion = ". $this->code;
		$consulta = parent::consulta($qry) or die(mysql_error());
		$mensaje = "Criterio de Evaluaci&oacute;n eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Criterio de Evaluaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function obtenerRubricaPersonalizada($id_estudiante, $id_asignatura, $id_rubrica_evaluacion, $id_usuario, $id_paralelo)
	{
		$cadena = "<table id=\"tb1\" class=\"headerTable\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\" align=\"center\">\n";
		$cadena .= "<tr>\n";
		$cadena .= "<td width=\"55%\" align=\"center\">CRITERIO</td>\n";
		$cadena .= "<td width=\"15%\" align=\"center\">PUNTAJE</td>\n";
		$cadena .= "<td width=\"15%\" align=\"center\">PONDERACION</td>\n";
		$cadena .= "<td width=\"15%\" align=\"center\">TOTAL</td>\n";
		$cadena .= "</tr>\n";
		$cadena .= "</table>\n";
		$qry = "SELECT cp.id_criterio_personalizado, id_rubrica_personalizada, cp_descripcion, cp_ponderacion FROM sw_criterio_personalizado cp, sw_rubrica_docente rd WHERE cp.id_criterio_personalizado = rd.id_criterio_personalizado AND rd.id_rubrica_evaluacion = $id_rubrica_evaluacion AND rd.id_usuario = $id_usuario AND rd.id_asignatura = $id_asignatura AND rd.id_paralelo = $id_paralelo ORDER BY cp.id_criterio_personalizado";
		$consulta = parent::consulta($qry) or die(mysql_error());
		$num_total_registros = parent::num_rows($consulta);
		$cadena .= "<table class=\"fuente8\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\" align=\"center\">\n";
		if($num_total_registros>0) {
			$contador = 0; $total_rubrica = 0;
			while($criterios_evaluacion = parent::fetch_assoc($consulta))
			{
				// Obtención de los puntajes si existen
				$id_rubrica_personalizada = $criterios_evaluacion["id_rubrica_personalizada"];
				$id_criterio_personalizado = $criterios_evaluacion["id_criterio_personalizado"];
				$qry = "SELECT ce.id_criterio_estudiante, ce.id_rubrica_estudiante, ce_calificacion, re_calificacion, re_fec_entrega FROM sw_criterio_estudiante ce, sw_rubrica_estudiante re WHERE ce.id_rubrica_estudiante = re.id_rubrica_estudiante AND ce.id_criterio_personalizado = $id_criterio_personalizado AND re.id_estudiante = $id_estudiante AND re.id_paralelo = $id_paralelo AND re.id_asignatura = $id_asignatura AND re.id_rubrica_personalizada = $id_rubrica_personalizada";
				$resultado = parent::consulta($qry) or die(mysql_error());
				if($resultado) {
					$criterio_estudiante = parent::fetch_assoc($resultado);
					$puntaje = $criterio_estudiante["ce_calificacion"];
					$id_rubrica_estudiante = $criterio_estudiante["id_rubrica_estudiante"];
					$id_criterio_estudiante = $criterio_estudiante["id_criterio_estudiante"];
				} else {
					$puntaje = 0;
					$id_rubrica_estudiante = 0;
					$id_criterio_estudiante = 0;
				}
				$contador += 1;
				$criterio = $criterios_evaluacion["cp_descripcion"];
				$ponderacion = $criterios_evaluacion["cp_ponderacion"];
				$total = $puntaje * $ponderacion;
				$total_rubrica += $total;
				$id_criterio_personalizado = $criterios_evaluacion["id_criterio_personalizado"];
				$cadena .= "<input type=\"hidden\" id=\"id_criterio_personalizado".$contador."\" value=\"$id_criterio_personalizado\" />\n";
				$cadena .= "<input type=\"hidden\" id=\"id_criterio_estudiante".$contador."\" value=\"$id_criterio_estudiante\" />\n";
				$cadena .= "<td width=\"55%\" align=\"center\">$criterio</td>\n";	
				$cadena .= "<td width=\"15%\" align=\"center\"><input class=\"inputPequenio\" value=\"".number_format($puntaje,2)."\" onclick=\"sel_texto(this)\" onblur=\"editarCriterioEstudiante(this)\" id=\"puntaje".$contador."\" onkeypress=\"return permite(event,'num')\" name=\"puntaje\" /></td>\n";	
				$cadena .= "<td width=\"15%\" align=\"center\"><input class=\"inputPequenio\" value=\"".number_format($ponderacion,2)."\" id=\"ponderacion".$contador."\" disabled=\"disabled\" /></td>\n";
				$cadena .= "<td width=\"15%\" align=\"center\"><input class=\"inputPequenio\" value=\"".number_format($total,2)."\" id=\"total".$contador."\" disabled=\"disabled\" /></td>\n";
				$cadena .= "</tr>\n";	
			}
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan=\"3\" align=\"right\">TOTAL RUBRICA&nbsp;</td>\n";
			$cadena .= "<td align=\"center\"><input type=\"text\" class=\"inputPequenio\" value=\"".number_format($total_rubrica,2)."\" id=\"total_rubrica\" disabled=\"disabled\" /></td>\n";
			$cadena .= "<input type=\"hidden\" id=\"id_rubrica_estudiante\" value=\"$id_rubrica_estudiante\" />\n";
			$cadena .= "</tr>\n";
		} else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td align=\"center\">No existen criterios de evaluaci&oacute;n personalizados relacionados con esta r&uacute;brica...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>\n";
		return $cadena;
		//return $qry;
	}
}
?>