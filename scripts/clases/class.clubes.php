<?php

class clubes extends MySQL
{
	
	var $code = "";
	var $id_club = "";
	var $cl_nombre = "";
	var $es_nombres = "";
	var $es_apellidos = "";
	var $id_estudiante = "";
	var $cl_abreviatura = "";
	var $id_periodo_lectivo = "";
	
	function existeClub($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_club WHERE cl_nombre = '$nombre'");
		return ($parent::num_rows($consulta) > 0);
	}

	function obtenerNombreClub($id)
	{
		$consulta = parent::consulta("SELECT cl_nombre FROM sw_club WHERE id_club = $id");
		$club = parent::fetch_object($consulta);
		return $club->cl_nombre;
	}

	function obtenerIdClub($nombre)
	{
		$consulta = parent::consulta("SELECT id_club FROM sw_club WHERE cl_nombre = '$nombre'");
		$club = parent::fetch_object($consulta);
		return $club->id_club;
	}

	function obtenerClub($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_club WHERE id_club = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosClub()
	{
		$consulta = parent::consulta("SELECT * FROM sw_club WHERE id_club = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listar_clubes()
	{
		$consulta = parent::consulta("SELECT * FROM sw_club ORDER BY cl_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($clubes = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $clubes["id_club"];
				$name = $clubes["cl_nombre"];
				$abrev = $clubes["cl_abreviatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$abrev</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarClub(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarClub(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Clubes...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarClub()
	{
		$qry = "INSERT INTO sw_club (cl_nombre, cl_abreviatura) VALUES (";
		$qry .= "'" . $this->cl_nombre . "',";
		$qry .= "'" . $this->cl_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Club insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Club...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarClub()
	{
		$qry = "UPDATE sw_club SET ";
		$qry .= "cl_nombre = '" . $this->cl_nombre . "',";
		$qry .= "cl_abreviatura = '" . $this->cl_abreviatura . "'";
		$qry .= " WHERE id_club = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Club [" . $this->cl_nombre . "] actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Club...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarClub()
	{
		$qry = "DELETE FROM sw_club WHERE id_club=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Club eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el Club...Error: " . mysql_error();
		return $mensaje;
	}

	function asociarClubDocente()
	{
		$qry = "INSERT INTO sw_club_docente (id_club, id_usuario, id_periodo_lectivo) VALUES (";
		$qry .= $this->id_club . ",";
		$qry .= $this->id_usuario . ",";
		$qry .= $this->id_periodo_lectivo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Club asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar el Club...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarClubDocente()
	{
		$qry = "DELETE FROM sw_club_docente WHERE id_club_docente =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Docente des-asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar el Docente...Error: " . mysql_error();
		return $mensaje;
	}	

	function listarClubesDocentes()
	{
		$consulta = parent::consulta("SELECT id_club_docente, cl_nombre, us_titulo, us_fullname FROM sw_club_docente cd, sw_club c, sw_usuario u WHERE cd.id_club = c.id_club AND cd.id_usuario = u.id_usuario AND cd.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY cl_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($club = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $club["id_club_docente"];
				$nombre_club = $club["cl_nombre"];
				$docente = $club["us_titulo"] . " " . $club["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				//$cadena .= "<td width=\"2.5%\">$code</td>\n";
				$cadena .= "<td width=\"37%\" align=\"left\">$nombre_club</td>\n";	
				$cadena .= "<td width=\"38%\" align=\"left\">$docente</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado docentes a este club...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarEstudiantes()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado FROM sw_estudiante e, sw_estudiante_club c WHERE e.id_estudiante = c.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_club = " . $this->id_club . " ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiantes = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$codigo = $estudiantes["id_estudiante"];
				$apellidos = $estudiantes["es_apellidos"];
				$nombres = $estudiantes["es_nombres"];
				$retirado = $estudiantes["es_retirado"];
				$checked = ($retirado == "N") ? "" : "checked";
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$codigo</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$apellidos</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$nombres</td>\n";
				$cadena .= "<td width=\"8%\" align=\"center\"> <input type=\"checkbox\" name=\"chkretirado_" . $contador . "\" $checked onclick=\"actualizar_estado_retirado(this,". $codigo . ")\"> </td>\n";
				$cadena .= "<td width=\"10%\" class=\"link_table\"><a href=\"#\" onclick=\"quitarEstudiante(".$codigo.")\">quitar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han matriculado estudiantes para este club...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function contarEstudiantesClub()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_estudiante e, sw_estudiante_club c WHERE e.id_estudiante = c.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_club = " . $this->id_club);
		return json_encode(parent::fetch_assoc($consulta));	
	}

	function contarClubesDocente()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_club_docente WHERE id_usuario = " . $this->id_usuario . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		return json_encode(parent::fetch_assoc($consulta));	
	}

	function insertarEstudianteSeleccionado()
	{
		// Falta comprobar que no se ingrese un estudiante que pertenezca a otro club...
		$qry = "INSERT INTO sw_estudiante_club (id_estudiante, id_periodo_lectivo, id_club, es_retirado) VALUES (";
		$qry .= $this->id_estudiante . ",";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= $this->id_club . ",'N')";
		$consulta = parent::consulta($qry);
		$mensaje = "Estudiante " . $this->es_apellidos . " " . $this->es_nombres . " insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Estudiante...Error: " . mysql_error();
		return $mensaje;
	}

	function quitarEstudianteClub()
	{
		$qry = "DELETE FROM sw_estudiante_club WHERE id_estudiante=". $this->id_estudiante ." AND id_club = " . $this->id_club . " AND id_periodo_lectivo = ". $this->id_periodo_lectivo;
		$consulta = parent::consulta($qry);
		$mensaje = "Estudiante des-matriculado exitosamente...";
		if (!$consulta) {
			$mensaje = "No se pudo des-matricular al Estudiante...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function paginarClubesDocente($cantidad_registros,$numero_pagina,$total_registros)
	{
		$total_paginas = ceil($total_registros / $cantidad_registros);
		$mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarClubesDocente(".$cantidad_registros.",1,".$total_registros.")'> Primero </a> </span>";
		if (($numero_pagina - 1) > 0) {
			$mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarClubesDocente(".$cantidad_registros.",".($numero_pagina-1).",".$total_registros.")'>Anterior</a></span>";
		} else {
			$mensaje .= "<span> < Anterior</span>";
		}
		for ($i=1; $i <= $total_paginas; $i++) {
			if ($numero_pagina == $i) {
				$mensaje .= "<b> P&aacute;gina ".$numero_pagina."</b>";
			} else {
				$mensaje .= "<span class='link_table'> <a href='#' onclick='paginarClubesDocente(".$cantidad_registros.",".$i.",".$total_registros.")'>$i</a></span>";
			}
		}
		if (($numero_pagina+1) <= $total_paginas) {
			$mensaje .= " <span class='link_table'><a href='#' onclick='paginarClubesDocente(".$cantidad_registros.",".($numero_pagina+1).",".$total_registros.")'>Siguiente</a> > </span>";
		} else {
			$mensaje .= " <span>Siguiente</a> > </span>";
		}
		$mensaje .= " <span class='link_table'><a href='#' onclick='paginarClubesDocente(".$cantidad_registros.",".$total_paginas.",".$total_registros.")'>Ultimo</a></span> >>"; 
		return $mensaje;
	}

	function listarClubesDocente($cantidad_registros, $numero_pagina)
	{
		$inicio = ($numero_pagina - 1) * $cantidad_registros;
		$consulta = parent::consulta("SELECT c.id_club, cl_nombre, cl_abreviatura FROM sw_club c, sw_club_docente cd WHERE c.id_club = cd.id_club AND cd.id_usuario = " . $this->id_usuario . " AND cd.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY cl_nombre ASC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = $inicio;
			while($club = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$codigo = $club["id_club"];
				$nombre = $club["cl_nombre"];
				$abreviatura = $club["cl_abreviatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"39%\" align=\"left\">$nombre</td>\n";
				$cadena .= "<td width=\"38%\" align=\"left\">$abreviatura</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_form\" align=\"center\"><a href=\"#\" onclick=\"seleccionarClub(".$codigo.",'".$nombre."')\">Seleccionar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado clubes a este docente...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function equivalencia_proyectos($calificacion, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT ec_abreviatura FROM sw_escala_proyectos WHERE $calificacion >= ec_nota_minima AND $calificacion <= ec_nota_maxima");
		$equivalencia = parent::fetch_assoc($consulta);
		return $equivalencia["ec_abreviatura"];
	}

	function listarEstudiantesClub()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, ec.id_club, e.es_apellidos, e.es_nombres, ep.es_retirado, cl_nombre FROM sw_estudiante_club ec, sw_estudiante e, sw_club c, sw_estudiante_periodo_lectivo ep WHERE ec.id_club = c.id_club AND ec.id_estudiante = e.id_estudiante AND e.id_estudiante = ep.id_estudiante AND ec.id_club = " . $this->id_club . " AND ec.id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND ep.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY es_apellidos, es_nombres ASC"); //LIMIT $inicio, $cantidad_registros
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($club = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $club["id_estudiante"];
				$apellidos = $club["es_apellidos"];
				$nombres = $club["es_nombres"];
				$retirado = $club["es_retirado"];
				$id_club = $club["id_club"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
				$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion, ap_tipo, ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND r.id_aporte_evaluacion = ".$this->id_aporte_evaluacion);
				$num_total_registros = parent::num_rows($rubrica_evaluacion);
				if($num_total_registros>0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubrica = parent::fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];
						$tipo_aporte = $rubrica["ap_tipo"];
						$estado_aporte = $rubrica["ap_estado"];
						$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_club WHERE id_estudiante = " . $club["id_estudiante"] . " AND id_club = ".$this->id_club . " AND id_rubrica_evaluacion = " . $id_rubrica_evaluacion);
						$num_total_registros = parent::num_rows($qry);
						$rubrica_estudiante = parent::fetch_assoc($qry);
						if($num_total_registros>0) {
							$calificacion = $rubrica_estudiante["rc_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_".$contador."\" class=\"inputPequenio\" value=\"".number_format($calificacion,2)."\"";
						//if($estado_aporte=='A' && $retirado=='N') {
							$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_club.",".$id_rubrica_evaluacion.",".$tipo_aporte.")\" /></td>\n";
						//} else {
							//$cadena .= " disabled /></td>\n";
						//}
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_".$contador."\" disabled value=\"".number_format($promedio,2)."\" style=\"color:#666;\" /></td>\n";
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"equivalencia_".$contador."\" disabled value=\"".$this->equivalencia_proyectos(number_format($promedio,2),$this->id_periodo_lectivo)."\" style=\"color:#666;\" /></td>\n";
					$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
					$cadena .= "</tr>\n";
				} else {
					$cadena .= "<tr>\n";
					$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
					$cadena .= "</tr>\n";
				}
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han matriculado estudiantes en este club...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCalificacionesClub($id_periodo_evaluacion, $tipo_reporte)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, ec.id_club, e.es_apellidos, e.es_nombres, cl_nombre FROM sw_estudiante_club ec, sw_estudiante e, sw_club c WHERE ec.id_club = c.id_club AND ec.id_estudiante = e.id_estudiante AND ec.id_club = " . $this->id_club . " AND ec.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($club = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $club["id_estudiante"];
				$apellidos = $club["es_apellidos"];
				$nombres = $club["es_nombres"];
				$id_club = $club["id_club"];
				$club = $club["cl_nombre"];
				$curso = $club["cu_nombre"];
				$paralelo = $club["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion, ap_tipo, ap_estado FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				if($num_total_registros>0)
				{
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
					while($aporte = parent::fetch_assoc($aporte_evaluacion))
					{
						$contador_aportes++;
						$tipo_aporte = $aporte["ap_tipo"];
						$estado_aporte = $aporte["ap_estado"];
						$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
						$total_rubricas = parent::num_rows($rubrica_evaluacion);
						if($total_rubricas>0)
						{
							$suma_rubricas = 0; $contador_rubricas = 0;
							while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
							{
								$contador_rubricas++;
								$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
								$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_club WHERE id_estudiante = $id_estudiante AND id_club = ".$this->id_club." AND id_rubrica_evaluacion = ".$id_rubrica_evaluacion);
								$total_registros = parent::num_rows($qry);
								if($total_registros>0) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["rc_calificacion"];
								} else {
									$calificacion = 0;
								}
								$suma_rubricas += $calificacion;
							}
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						if($contador_aportes <= $num_total_registros - 1)
						{
							if($tipo_reporte==1)
								$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>";
							else
								$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_".$contador."\" disabled value=\"".number_format($promedio,2)."\" style=\"color:#666;\" /></td>\n";
							$suma_promedios += $promedio;
						} else {
							$examen_quimestral = $promedio;
						}
					}
					// Aqui debo calcular el ponderado de los promedios parciales
					$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
					$ponderado_aportes = 0.8 * $promedio_aportes;
					$ponderado_examen = 0.2 * $examen_quimestral;
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					if($tipo_reporte==1) 
					{
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_aportes,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_aportes,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($examen_quimestral,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_examen,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion_quimestral,2)."</td>";
					}
					else
					{
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedioaportes_".$contador."\" disabled value=\"".number_format($promedio_aportes,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_".$contador."\" disabled value=\"".number_format($ponderado_aportes,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"examenquimestral_".$contador."\" value=\"".number_format($examen_quimestral,2)."\"";
						//if($estado_aporte=='A') {
							$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_club.",".$id_rubrica_evaluacion.",".$tipo_aporte.")\" /></td>\n";
						//} else {
						//	$cadena .= " disabled /></td>\n";
						//}
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_".$contador."\" disabled value=\"".number_format($ponderado_examen,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_".$contador."\" disabled value=\"".number_format($calificacion_quimestral,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"equivalencia_".$contador."\" disabled value=\"".$this->equivalencia_proyectos(number_format($calificacion_quimestral,2),$this->id_periodo_lectivo)."\" style=\"color:#666;\" /></td>\n";
					}
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

}
?>