<?php

class rubricas_evaluacion extends MySQL
{
	
	var $code = "";
	var $ru_nombre = "";
	var $ru_abreviatura = "";
	var $id_aporte_evaluacion = "";
	var $id_tipo_rubrica = "";
	// Para rubricas personalizadas
	var $id_rubrica_evaluacion = "";
	var $id_estudiante = "";
	var $id_usuario = "";
	var $id_asignatura = "";
	var $id_paralelo = "";
	var $rp_tema = "";
	var $rp_fec_envio = "";
	var $rp_fec_evaluacion = "";
	var $id_rubrica_personalizada = "";
	var $re_calificacion = "";
	var $re_fec_entrega = "";
	// Para comportamiento
	var $id_indice_evaluacion = 0;
	var $id_periodo_evaluacion = 0;
	var $nombre_campo = "";
	var $calificacion = 0;
	var $total = 0;
	var $promedio = 0;
	var $equivalencia = "";
	// Para clubes
	var $id_club = "";
	var $rc_calificacion = "";
	var $rc_fec_entrega = "";

	function editarComportamiento()
	{
		$qry = "UPDATE sw_indice_evaluacion SET ";
		$qry .= $this->nombre_campo . " = " . $this->calificacion;
		$qry .= ", total = " . $this->total;
		$qry .= ", promedio = " . $this->promedio;
		$qry .= ", equivalencia = '". $this->equivalencia . "'";
		$qry .= " WHERE id_indice_evaluacion = " . $this->id_indice_evaluacion;
		$exec = parent::consulta($qry);
		return $qry;
	}
	
	function existeRubricaEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion WHERE ru_nombre = '$nombre'");
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros>0);
	}

	function existeRubricaPersonalizada($id_rubrica_evaluacion, $id_usuario, $id_asignatura, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_personalizada WHERE id_rubrica_evaluacion = $id_rubrica_evaluacion AND id_usuario = $id_usuario AND id_asignatura = $id_asignatura AND id_paralelo = $id_paralelo");
		$num_total_registros = parent::num_rows($consulta);
		return ($num_total_registros > 0);
	}

	function existeRubricaEstudiante()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_estudiante WHERE id_estudiante = " . $this->id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_rubrica_personalizada = " . $this->id_rubrica_personalizada);
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros > 0);
	}

	function existeRubricaEstudianteClub()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_club WHERE id_estudiante = " . $this->id_estudiante . " AND id_club = " . $this->id_club . " AND id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros > 0);
	}

	function existeRubricaEstudianteComportamiento()
	{
		$consulta = parent::consulta("SELECT * FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $this->id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros > 0);
	}

	function obtenerIdRubricaEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE ru_nombre = '$nombre'");
		$rubrica_evaluacion = parent::fetch_object($consulta);
		return $rubrica_evaluacion->id_rubrica_evaluacion;
	}

	function obtenerRubricaEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion WHERE id_rubrica_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerRubricaProyecto()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion_club WHERE id_rubrica_evaluacion_club = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarRubricasEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion . " ORDER BY id_rubrica_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($rubricas_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $rubricas_evaluacion["id_rubrica_evaluacion"];
				$name = $rubricas_evaluacion["ru_nombre"];
				$abrev = $rubricas_evaluacion["ru_abreviatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$abrev</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarRubricaEvaluacion(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarRubricaEvaluacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido R&uacute;bricas de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarRubricasEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion . " ORDER BY id_rubrica_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($rubricas_evaluacion = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $rubricas_evaluacion["id_rubrica_evaluacion"];
				$name = $rubricas_evaluacion["ru_nombre"];
				$abrev = $rubricas_evaluacion["ru_abreviatura"];
				$cadena .= "<td>$id</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$abrev</td>\n";
				$cadena .= "<td><button onclick='editRubEval(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
				$cadena .= "<td><button onclick='deleteRubEval(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='5' align='center'>No se han definido R&uacute;bricas de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		return $cadena;
	}

	function listarRubricasEvaluacionProyectos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion_club WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion . " ORDER BY id_rubrica_evaluacion_club ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($rubricas_evaluacion = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $rubricas_evaluacion["id_rubrica_evaluacion_club"];
				$name = $rubricas_evaluacion["rc_nombre"];
				$abrev = $rubricas_evaluacion["rc_abreviatura"];	
				$cadena .= "<td>$id</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$abrev</td>\n";
				$cadena .= "<td><button onclick='editRubProy(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
				$cadena .= "<td><button onclick='deleteRubProy(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han definido R&uacute;bricas de Proyectos...</td>\n";
			$cadena .= "</tr>\n";	
		}	
		return $cadena;
	}

	function listarRubricasEvaluacionDocentes()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion . " ORDER BY id_rubrica_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($rubricas_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $rubricas_evaluacion["id_rubrica_evaluacion"];
				$name = $rubricas_evaluacion["ru_nombre"];
				$abrev = $rubricas_evaluacion["ru_abreviatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$abrev</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarRubricaEvaluacion(".$code.")\">seleccionar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido R&uacute;bricas de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarRubricaEvaluacion()
	{
		$qry = "INSERT INTO sw_rubrica_evaluacion (id_aporte_evaluacion, id_tipo_rubrica, ru_nombre, ru_abreviatura) VALUES (";
		$qry .= $this->id_aporte_evaluacion .",";
		$qry .= $this->id_tipo_rubrica . ",";
		$qry .= "'" . $this->ru_nombre . "',";
		$qry .= "'" . $this->ru_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "R&uacute;brica de Evaluaci&oacute;n insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la R&uacute;brica de Evaluaci&oacute;n...Error: " . mysql_error();
		return $mensaje;
	}

	function insertarRubricaProyecto()
	{
		$qry = "INSERT INTO sw_rubrica_evaluacion_club (id_aporte_evaluacion, rc_nombre, rc_abreviatura) VALUES (";
		$qry .= $this->id_aporte_evaluacion .",";
		$qry .= "'" . $this->rc_nombre . "',";
		$qry .= "'" . $this->rc_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "R&uacute;brica de Proyecto insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la R&uacute;brica de Proyecto...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarRubricaEvaluacion()
	{
		$qry = "UPDATE sw_rubrica_evaluacion SET ";
		$qry .= "id_aporte_evaluacion = " . $this->id_aporte_evaluacion . ",";
		$qry .= "ru_nombre = '" . $this->ru_nombre . "',";
		$qry .= "ru_abreviatura = '" . $this->ru_abreviatura . "',";
		$qry .= "id_tipo_rubrica = " . $this->id_tipo_rubrica;
		$qry .= " WHERE id_rubrica_evaluacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "R&uacute;brica de Evaluacion " . $this->ru_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la R&uacute;brica de Evaluacion...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarRubricaProyecto()
	{
		$qry = "UPDATE sw_rubrica_evaluacion_club SET ";
		$qry .= "id_aporte_evaluacion = " . $this->id_aporte_evaluacion . ",";
		$qry .= "rc_nombre = '" . $this->rc_nombre . "',";
		$qry .= "rc_abreviatura = '" . $this->rc_abreviatura . "'";
		$qry .= " WHERE id_rubrica_evaluacion_club = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "R&uacute;brica de Proyecto " . $this->rc_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la R&uacute;brica de Proyecto...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarRubricaEvaluacion()
	{
		$qry = "SELECT id_rubrica_estudiante FROM sw_rubrica_estudiante WHERE id_rubrica_personalizada = " .$this->code;
		$consulta = parent::consulta($qry);
		if (parent::num_rows($consulta) > 0){
			$mensaje = "No se puede eliminar la R&uacute;brica de Evaluaci&oacute;n porque tiene calificaciones asociadas...";
		} else {
			$qry = "DELETE FROM sw_rubrica_evaluacion WHERE id_rubrica_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "R&uacute;brica de Evaluacion eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar la R&uacute;brica de Evaluacion...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function eliminarRubricaProyecto()
	{
		$qry = "SELECT id_rubrica_club FROM sw_rubrica_club WHERE id_rubrica_evaluacion = " .$this->code;
		$consulta = parent::consulta($qry);
		if (parent::num_rows($consulta) > 0){
			$mensaje = "No se puede eliminar la R&uacute;brica de Proyecto porque tiene calificaciones asociadas...";
		} else {
			$qry = "DELETE FROM sw_rubrica_evaluacion_club WHERE id_rubrica_evaluacion_club = ". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "R&uacute;brica de Proyecto eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar la R&uacute;brica de Proyecto...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function insertarRubricaPersonalizada()
	{
		$qry = "INSERT INTO sw_rubrica_personalizada (id_rubrica_evaluacion, id_usuario, id_asignatura, id_paralelo, rp_tema, rp_fec_envio, rp_fec_evaluacion) VALUES (";
		$qry .= $this->id_rubrica_evaluacion .",";
		$qry .= $this->id_usuario .",";
		$qry .= $this->id_asignatura .",";
		$qry .= $this->id_paralelo .",";
		$qry .= "'" . $this->rp_tema . "',";
		$qry .= "'" . $this->rp_fec_envio . "',";
		$qry .= "'" . $this->rp_fec_evaluacion . "')";
		$consulta = parent::consulta($qry);
		if ($consulta)
			return true;
		else
			return false;
	}

	function insertarRubricaEstudiante()
	{
		$qry = "INSERT INTO sw_rubrica_estudiante SET ";
		$qry .= "id_estudiante = " . $this->id_estudiante . ",";
		$qry .= "id_paralelo = " . $this->id_paralelo . ",";
		$qry .= "id_asignatura = " . $this->id_asignatura . ",";
		$qry .= "id_rubrica_personalizada = " . $this->id_rubrica_personalizada . ",";
		$qry .= "re_calificacion = " . $this->re_calificacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la inserci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

	function insertarRubricaEstudianteClub()
	{
		$qry = "INSERT INTO sw_rubrica_club SET ";
		$qry .= "id_estudiante = " . $this->id_estudiante . ",";
		$qry .= "id_club = " . $this->id_club . ",";
		$qry .= "id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion . ",";
		$qry .= "rc_calificacion = " . $this->rc_calificacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la inserci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

	function insertarRubricaEstudianteComportamiento()
	{
		$qry = "INSERT INTO sw_calificacion_comportamiento SET ";
		$qry .= "id_estudiante = " . $this->id_estudiante . ",";
		$qry .= "id_paralelo = " . $this->id_paralelo . ",";
		$qry .= "id_asignatura = " . $this->id_asignatura . ",";
		$qry .= "id_aporte_evaluacion = " . $this->id_aporte_evaluacion . ",";
		$qry .= "co_cualitativa = '" . $this->calificacion . "'";
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n de comportamiento insertada exitosamente...";
		if (!$consulta)
		{
			$mensaje = "No se pudo realizar la inserci&oacute;n... Error: " . mysql_error();
			return $mensaje;
		}
		else 
		{
			//Ahora actualizamos el campo co_calificacion con la calificacion correlativa de la tabla sw_escala_comportamiento
			$qry = "SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '" . $this->calificacion . "'";
			$consulta = parent::consulta($qry);
			if (!$consulta)
			{
				$mensaje = "No se pudo realizar la consulta... Error: " . mysql_error();
				return $mensaje;
			}
			else
			{
				$registro = parent::fetch_assoc($consulta);
				$co_calificacion = $registro["ec_correlativa"];
				$qry = "UPDATE sw_calificacion_comportamiento SET co_calificacion = " . $co_calificacion;
				$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
				$qry .= " AND id_paralelo = " . $this->id_paralelo;
				$qry .= " AND id_asignatura = " . $this->id_asignatura;
				$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
				$consulta = parent::consulta($qry);
				if (!$consulta)
				{
					$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
					return $mensaje;
				}
				else
				{
					$mensaje = "Comportamiento insertado exitosamente.";
					return $mensaje;
				}
			}
		}
	}

	function actualizarRubricaEstudiante()
	{
		$qry = "UPDATE sw_rubrica_estudiante SET ";
		$qry .= "re_calificacion = " . $this->re_calificacion;
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_paralelo = " . $this->id_paralelo;
		$qry .= " AND id_asignatura = " . $this->id_asignatura;
		$qry .= " AND id_rubrica_personalizada = " . $this->id_rubrica_personalizada;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarRubricaEstudianteClub()
	{
		$qry = "UPDATE sw_rubrica_club SET ";
		$qry .= "rc_calificacion = " . $this->rc_calificacion;
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_club = " . $this->id_club;
		$qry .= " AND id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarRubricaEstudianteComportamiento()
	{
		$qry = "DELETE FROM sw_calificacion_comportamiento ";
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_paralelo = " . $this->id_paralelo;
		$qry .= " AND id_asignatura = " . $this->id_asignatura;
		$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Comportamiento eliminado exitosamente...";
		if (!$consulta)
		{
			$mensaje = "No se pudo realizar la eliminaci&oacute;n... Error: " . mysql_error();
			return $mensaje;
		}
		return $mensaje;
	}

	function actualizarRubricaEstudianteComportamiento()
	{
		$qry = "UPDATE sw_calificacion_comportamiento SET ";
		$qry .= "co_cualitativa = '" . $this->calificacion . "'";
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_paralelo = " . $this->id_paralelo;
		$qry .= " AND id_asignatura = " . $this->id_asignatura;
		$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Comportamiento actualizado exitosamente...";
		if (!$consulta)
		{
			$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
			return $mensaje;
		}
		else 
		{
			//Ahora actualizamos el campo co_calificacion con la calificacion correlativa de la tabla sw_escala_comportamiento
			$qry = "SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '" . $this->calificacion . "'";
			$consulta = parent::consulta($qry);
			if (!$consulta)
			{
				$mensaje = "No se pudo realizar la consulta... Error: " . mysql_error();
				return $mensaje;
			}
			else
			{
				$registro = parent::fetch_assoc($consulta);
				$co_calificacion = $registro["ec_correlativa"];
				$qry = "UPDATE sw_calificacion_comportamiento SET co_calificacion = " . $co_calificacion;
				$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
				$qry .= " AND id_paralelo = " . $this->id_paralelo;
				$qry .= " AND id_asignatura = " . $this->id_asignatura;
				$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
				$consulta = parent::consulta($qry);
				if (!$consulta)
				{
					$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysql_error();
					return $mensaje;
				}
				else
				{
					$mensaje = "Comportamiento actualizado exitosamente.";
					return $mensaje;
				}
			}
		}
	}

	function actualizarRubricaPersonalizada()
	{
		$qry = "UPDATE sw_rubrica_personalizada SET ";
		$qry .= "id_rubrica_evaluacion = " . $this->id_rubrica_evaluacion .",";
		$qry .= "id_usuario = " . $this->id_usuario .",";
		$qry .= "rp_tema = '" . $this->rp_tema . "',";
		$qry .= "rp_fec_envio = '" . $this->rp_fec_envio . "',";
		$qry .= "rp_fec_evaluacion = '" . $this->rp_fec_evaluacion . "'";
		$qry .= " WHERE id_rubrica_personalizada = " . $this->code;
		$consulta = parent::consulta($qry);
		if ($consulta)
			$mensaje = "R&uacute;brica personalizada actualizada exitosamente...";
		else
			$mensaje = "No se pudo actualizar la r&uacute;brica personalizada... Error: " . mysql_error();
		return $mensaje;
	}

	function obtenerRubricaPersonalizada()
	{
		$consulta = parent::consulta("SELECT * FROM sw_rubrica_personalizada WHERE id_rubrica_evaluacion = " . $this->code . " AND id_usuario = " . $this->id_usuario . " AND id_asignatura = " . $this->id_asignatura . " AND id_paralelo = " . $this->id_paralelo);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerRubricaEstudiante()
	{
		$consulta = parent::consulta("SELECT id_rubrica_estudiante, re_calificacion FROM sw_rubrica_estudiante WHERE id_rubrica_estudiante = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function actualizarCalificacionErronea()
	{
		$qry = "UPDATE sw_rubrica_estudiante SET ";
		$qry .= "re_calificacion = " . $this->re_calificacion;
		$qry .= " WHERE id_rubrica_estudiante = " . $this->code;
		$consulta = parent::consulta($qry);
		if ($consulta)
			$mensaje = "R&uacute;brica actualizada exitosamente...";
		else
			$mensaje = "No se pudo actualizar la r&uacute;brica... Error: " . mysql_error();
		return $mensaje;
	}

	function mostrarTitulosRubricas($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($titulo_rubrica = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_rubrica["ru_abreviatura"] . "</td>\n";
			}
        }
		
		$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM.</td>\n";
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
}
?>