<?php

require_once("class.funciones.php");

class estudiantes extends MySQL
{
	
	var $code = "";
	var $es_email = "";
	var $es_cedula = "";
	var $es_genero = "";
	var $es_nombres = "";
	var $id_paralelo = "";
	var $es_apellidos = "";
	var $id_periodo_lectivo = "";
	var $es_direccion = "";
	var $es_sector = "";
	var $es_telefono = "";

	var $id_representante = "";
	var $re_email = "";
	var $re_cedula = "";
	var $re_nombres = "";
	var $re_apellidos = "";
	var $re_direccion = "";
	var $re_sector = "";
	var $re_telefono = "";
	var $re_observacion = "";
	var $re_parentesco = "";

	function truncateFloat($number, $digitos) {
		$raiz = 10;
		$multiplicador = pow ($raiz,$digitos);
		$resultado = ((int)($number * $multiplicador)) / $multiplicador;
		return $resultado;
	}

	function existeEstudiante($cedula, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT * FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_cedula = '$cedula'");
		return (parent::num_rows($consulta) > 0);
	}

	function existeRepresentante($id_estudiante)
	{
		$consulta = parent::consulta("SELECT * FROM sw_representante WHERE id_estudiante = $id_estudiante");
		return (parent::num_rows($consulta) > 0);
	}	

	function existeNombreEstudiante($apellidos, $nombres, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT * FROM sw_estudiante e WHERE e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		return (parent::num_rows($consulta) > 0);
	}
	
	function existenCalificaciones()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS contador FROM sw_rubrica_estudiante WHERE id_estudiante = ".$this->code);
		$registro = parent::fetch_assoc($consulta);
		return ($registro["contador"] > 0);
	}
	
	function es_promocionado($id_estudiante, $id_periodo_lectivo, $id_paralelo)
	{
		$qry = parent::consulta("SELECT es_promocionado(" . $id_estudiante . ", " . $id_periodo_lectivo . ", " . $id_paralelo .") AS aprobado");
		$registro = parent::fetch_assoc($qry);
		return $registro["aprobado"];
	}

	function buscarEstudiantesAntiguos($patron)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 es_apellidos, 
											 es_nombres, 
											 cu_nombre, 
											 pa_nombre, 
											 p.id_paralelo, 
											 ep.id_periodo_lectivo 
										FROM sw_estudiante e, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_curso c, 
											 sw_paralelo p 
									   WHERE e.id_estudiante = ep.id_estudiante 
									     AND ep.id_paralelo = p.id_paralelo 
										 AND p.id_curso = c.id_curso 
										 AND e.es_nombre_completo LIKE '$patron%' 
										 AND ep.id_periodo_lectivo <= " . $this->id_periodo_lectivo . " - 1");
		$num_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_registros > 0) {
			$contador = 0; $qry = "";
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$id_periodo_lectivo = $estudiante["id_periodo_lectivo"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$curso = $estudiante["cu_nombre"];
				$paralelo = $estudiante["pa_nombre"];
				$id_paralelo = $estudiante["id_paralelo"];
				//echo $id_paralelo."</br>";
				$cadena .= "<td width=\"5%\" align=\"center\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" align=\"center\">$id_estudiante</td>\n";
				//*******************************************************************
				$aprobado = $this->es_promocionado($id_estudiante,$id_periodo_lectivo,$id_paralelo);
				//echo $id_estudiante . " " . $id_periodo_lectivo . " " . $id_paralelo . "</br>";
				//echo $aprobado."</br>";
				$c_aprobado = ($aprobado==1) ? 'APRUEBA' : 'NO APRUEBA';
				//*******************************************************************
				$cadena .= "<td width=\"18%\" align=\"left\">$apellidos</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$nombres</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$paralelo</td>\n";
				$cadena .= "<td width=\"9%\" align=\"center\">$c_aprobado</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\" align=\"center\"><a href=\"#\" onclick=\"seleccionarEstudiante(".$id_estudiante.",'".$apellidos."','".$nombres."')\">seleccionar</a></td>\n";
			}
		} else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td align=\"center\">No se han encontrado estudiantes...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function buscarEstudiantesMatriculados($patron)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, cu_nombre, pa_nombre FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_curso c, sw_paralelo p WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND (e.es_apellidos LIKE CONCAT('%" . $patron . "','%') OR e.es_nombres LIKE CONCAT('%" . $patron . "','%')) AND ep.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY es_apellidos, es_nombres");
		$num_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_registros > 0) {
			$contador = 0; $qry = "";
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$curso = $estudiante["cu_nombre"];
				$paralelo = $estudiante["pa_nombre"];
				$cadena .= "<td width=\"5%\" align=\"center\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" align=\"center\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$apellidos</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$nombres</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$paralelo</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\" align=\"center\"><a href=\"#\" onclick=\"seleccionarEstudiante(".$id_estudiante.",'".$apellidos."','".$nombres."')\">seleccionar</a></td>\n";
			}
		} else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td align=\"center\">No se han encontrado estudiantes...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function obtenerIdEstudiante($apellidos, $nombres, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT ep.id_estudiante FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		$estudiante = parent::fetch_object($consulta);
		return $estudiante->id_estudiante;
	}

	function obtenerIdEstudianteCedula($cedula, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT ep.id_estudiante FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_cedula = '$cedula'");
		$estudiante = parent::fetch_object($consulta);
		return $estudiante->id_estudiante;
	}

	function obtenerEstudiante()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_cedula, es_genero, id_paralelo, es_email, es_direccion, es_sector, es_telefono FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND e.id_estudiante = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerRepresentante()
	{
		$consulta = parent::consulta("SELECT * FROM sw_representante WHERE id_estudiante = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function obtenerEstudianteId($id_estudiante, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
		$estudiante = parent::fetch_object($consulta);
		//return utf8_decode($estudiante->es_apellidos)." ".utf8_decode($estudiante->es_nombres);
		return $estudiante->es_apellidos." ".$estudiante->es_nombres;
	}
	
	function obtenerCursoParaleloEstudiante($apellidos, $nombres, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT cu_nombre, pa_nombre FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_curso c, sw_paralelo p WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres' AND id_periodo_lectivo = $id_periodo_lectivo");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerCursoParaleloEstudianteId($id_estudiante, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT es_figura, cu_nombre, pa_nombre FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_especialidad es, sw_curso c, sw_paralelo p WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = es.id_especialidad AND e.id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");
		$estudiante = parent::fetch_assoc($consulta);
		return $estudiante["cu_nombre"] . " " . $estudiante["es_figura"] . " " . $estudiante["pa_nombre"];
	}

	function obtenerCursoParaleloEstudiantePeriodoEvaluacion($id_estudiante, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT cu_nombre, pa_nombre, pe_nombre FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_curso c, sw_paralelo p, sw_periodo_evaluacion pe WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND ep.id_periodo_lectivo = pe.id_periodo_lectivo AND e.id_estudiante = $id_estudiante AND id_periodo_evaluacion = $id_periodo_evaluacion");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerCursoParaleloEstudianteAporteEvaluacion($id_estudiante, $id_aporte_evaluacion)
	{
		$consulta = parent::consulta("SELECT cu_nombre, pa_nombre, ap_nombre FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_curso c, sw_paralelo p, sw_periodo_evaluacion pe, sw_aporte_evaluacion a WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND a.id_periodo_evaluacion = pe.id_periodo_evaluacion AND pe.id_periodo_lectivo = ep.id_periodo_lectivo AND e.id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function actualizarEstadoRetirado($estado_retirado)
	{
		// Procedimiento para actualizar el estado de retirado de un estudiante
		$consulta = parent::consulta("UPDATE sw_estudiante_periodo_lectivo SET es_retirado = '$estado_retirado' WHERE id_estudiante = " . $this->code . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		if($consulta) echo "Estado de retirado del estudiante actualizado correctamente.";
		else echo "Estado de retirado del estudiante no pudo actualizarse. Error: " . mysql_error();
	}

	function obtenerCalificacionesAnuales($apellidos, $nombres, $id_periodo_lectivo)
	{
		// Primero debo obtener el id_estudiante y el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_estudiante, ep.id_paralelo, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_estudiante = $paralelo["id_estudiante"];
		$id_paralelo = $paralelo["id_paralelo"];
		$retirado = $paralelo["es_retirado"];
		$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";
		$asignaturas = parent::consulta("SELECT a.id_asignatura, as_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_asignatura a WHERE pa.id_paralelo = p.id_paralelo AND pa.id_asignatura = a.id_asignatura AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++; $contador_sin_examen = 0;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"5%\" align=\"center\">$contador</td>\n";
			$cadena .= "<td width=\"35%\" align=\"left\">$nom_asignatura</td>\n";
			//*******************************************************************
			$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = parent::num_rows($periodo_evaluacion);
			//echo $num_total_registros;
			if($num_total_registros>0)
			{
				$suma_periodos = 0; $contador_periodos = 0;
				while($periodo = parent::fetch_assoc($periodo_evaluacion))
				{
					$contador_periodos++;
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				
					$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
					$aporte_evaluacion = parent::consulta($qry);
					//echo $qry . "<br>";
					$num_total_registros = parent::num_rows($aporte_evaluacion);
					if($num_total_registros>0)
					{
						// Aqui calculo los promedios y desplegar en la tabla
						$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
						while($aporte = parent::fetch_assoc($aporte_evaluacion))
						{
							$contador_aportes++;
							$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
							$total_rubricas = parent::num_rows($rubrica_evaluacion);
							if($total_rubricas>0)
							{
								$suma_rubricas = 0; $contador_rubricas = 0;
								while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
								{
									$contador_rubricas++;
									$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
									$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
									$total_registros = parent::num_rows($qry);
									if($total_registros>0) {
										$rubrica_estudiante = parent::fetch_assoc($qry);
										$calificacion = $rubrica_estudiante["re_calificacion"];
									} else {
										$calificacion = 0;
									}
									$suma_rubricas += $calificacion;
								}
							}
							// Aqui calculo el promedio del aporte de evaluacion
							$promedio = $suma_rubricas / $contador_rubricas;
							if($contador_aportes <= $num_total_registros - 1) {
								$suma_promedios += $promedio;
							} else {
								$examen_quimestral = $promedio;
							}
						}
					}
					// Aqui se calculan las calificaciones del periodo de evaluacion
					if ($examen_quimestral == 0) $contador_sin_examen++;
					$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
					$ponderado_aportes = 0.8 * $promedio_aportes;
					$ponderado_examen = 0.2 * $examen_quimestral;
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					$suma_periodos += $calificacion_quimestral;
					//echo $suma_periodos . "<br>";
					$cadena .= "<td width=\"60px\" align=\"left\">".number_format($calificacion_quimestral,2)."</td>\n";
				} // fin while $periodo_evaluacion
			} // if($num_total_registros>0)
			// Calculo la suma y el promedio de los dos quimestres
			$promedio_periodos = $suma_periodos / $contador_periodos;
			$promedio_final = $promedio_periodos;
			$examen_supletorio = 0; $examen_remedial = 0; $examen_de_gracia = 0;
                        
			if ($promedio_periodos >= 7) {
			     $equiv_final = "APRUEBA";
			} else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
                 $equiv_final = "SUPLETORIO";
                 if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
  			        // Obtencion de la calificacion del examen supletorio
                    $examen_supletorio = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
                    if ($examen_supletorio >= 7) {
     					$promedio_final = 7;
                       	$equiv_final = "APRUEBA";
                    } else {
                       	$equiv_final = "REMEDIAL";
				   		if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
				       		// Obtencion de la calificacion del examen remedial
				       		$examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
				       		if ($examen_remedial >= 7) {
	     				   		$promedio_final = 7;
	                            $equiv_final = "APRUEBA";
				       		} else {
				           		$equiv_final = "DE GRACIA";
				           		if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
					       			// Obtencion de la calificacion del examen remedial
					       			$examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
					       			if ($examen_de_gracia >= 7) {
		     				   			$promedio_final = 7;
		                                $equiv_final = "APRUEBA";
					       			} else {
					           			$equiv_final = "NO APRUEBA";
					       			}				           	
				           	}
				    	}
					}
       			 }
            } else {
                 // Caso contrario se determina si debe dar examen remedial, considerando la fecha de cierre del examen supletorio
                 $fecha_actual = new DateTime("now");
                 $fecha_cierre = new DateTime(funciones::obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));
                 if ($fecha_actual < $fecha_cierre) {
                   	$equiv_final = "SUPLETORIO";
                 } else {
                   	$equiv_final = "REMEDIAL";
                 	// Obtencion de la calificacion del examen remedial
				    $examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
				    if ($examen_remedial >= 7) {
				        $promedio_final = 7;
				        $equiv_final = "APRUEBA";
				    } else {
				        $examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
				        if ($examen_de_gracia >= 7) {
				            $promedio_final = 7;
				            $equiv_final = "APRUEBA";
				        } else {
				            $equiv_final = "NO APRUEBA";
				        }
				    }    
                 }
            }
                        } else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
			   $equiv_final = "REMEDIAL";
			   if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
			       // Obtencion de la calificacion del examen remedial
			       $examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
			       if ($examen_remedial >= 7) {
				   $promedio_final = 7;
			           $equiv_final = "APRUEBA";
			       } else {
			           $equiv_final = "DE GRACIA";
			           if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
				       // Obtencion de la calificacion del examen remedial
				       $examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
				       if ($examen_de_gracia >= 7) {
					   $promedio_final = 7;
			                   $equiv_final = "APRUEBA";
				       } else {
				           $equiv_final = "NO APRUEBA";
				       }				           	
			           }
			       }
			   } else {
                    // Caso contrario se determina si debe dar examen de gracia, considerando la fecha de cierre del examen remedial
                    $fecha_actual = new DateTime("now");
                    $fecha_cierre = new DateTime(funciones::obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo));
                    if ($fecha_actual < $fecha_cierre) {
                        $equiv_final = "REMEDIAL";
                    } else {
                        $equiv_final = "DE GRACIA";
                    }
			   }
            } else {
                $equiv_final = "NO APRUEBA";
            }

            if ($contador_sin_examen > 0) $equiv_final = "SIN EXAMEN";

			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($suma_periodos,2)."</td>\n"; // Suma
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($promedio_periodos,2)."</td>\n"; // Prom. Quim.
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_supletorio,2)."</td>\n"; // Supletorio
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_remedial,2)."</td>\n"; // Remedial
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_de_gracia,2)."</td>\n"; // Gracia
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($promedio_final,2)."</td>\n"; // Promedio Final
			$cadena .= "<td width=\"*\" align=\"left\">$equiv_final</td>\n";
			$cadena .= "</tr>\n";	
			//*******************************************************************
		} // while($asignatura = parent::fetch_assoc($asignaturas))
		$cadena .= "</table>\n";
		//echo $cadena;
		return $cadena;
	}

	function obtenerCalificacionesAnualesId($id_estudiante, $id_periodo_lectivo)
	{
		// Primero debo obtener el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_paralelo = $paralelo["id_paralelo"];
		$asignaturas = parent::consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++; $contador_sin_examen = 0;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"5%\" align=\"left\">$contador</td>\n";
			$cadena .= "<td width=\"35%\" align=\"left\">$nom_asignatura</td>\n";
			//*******************************************************************
			$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = parent::num_rows($periodo_evaluacion);
			//echo $num_total_registros;
			if($num_total_registros>0)
			{
				$suma_periodos = 0; $contador_periodos = 0;
				while($periodo = parent::fetch_assoc($periodo_evaluacion))
				{
					$contador_periodos++;
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				
					$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
					$aporte_evaluacion = parent::consulta($qry);
					//echo $qry . "<br>";
					$num_total_registros = parent::num_rows($aporte_evaluacion);
					if($num_total_registros>0)
					{
						// Aqui calculo los promedios y desplegar en la tabla
						$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
						while($aporte = parent::fetch_assoc($aporte_evaluacion))
						{
							$contador_aportes++;
							$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 1 AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
							$total_rubricas = parent::num_rows($rubrica_evaluacion);
							if($total_rubricas>0)
							{
								$suma_rubricas = 0; $contador_rubricas = 0;
								while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
								{
									$contador_rubricas++;
									$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
									$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
									$total_registros = parent::num_rows($qry);
									if($total_registros>0) {
										$rubrica_estudiante = parent::fetch_assoc($qry);
										$calificacion = $rubrica_estudiante["re_calificacion"];
									} else {
										$calificacion = 0;
									}
									$suma_rubricas += $calificacion;
								}
							}
							// Aqui calculo el promedio del aporte de evaluacion
							$promedio = $suma_rubricas / $contador_rubricas;
							if($contador_aportes <= $num_total_registros - 1) {
								$suma_promedios += $promedio;
							} else {
								$examen_quimestral = $promedio;
							}
						}
					}
					// Aqui se calculan las calificaciones del periodo de evaluacion
					if ($examen_quimestral == 0) $contador_sin_examen++;
					$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
					$ponderado_aportes = 0.8 * $promedio_aportes;
					$ponderado_examen = 0.2 * $examen_quimestral;
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					$suma_periodos += $calificacion_quimestral;
					//echo $suma_periodos . "<br>";
					$cadena .= "<td width=\"60px\" align=\"left\">".number_format($calificacion_quimestral,2)."</td>\n";
				} // fin while $periodo_evaluacion
			} // if($num_total_registros>0)
			// Calculo la suma y el promedio de los dos quimestres
			$promedio_periodos = $suma_periodos / $contador_periodos;
			$promedio_final = $promedio_periodos;
			$examen_supletorio = 0; $examen_remedial = 0; $examen_de_gracia = 0;
                        
			if ($promedio_periodos >= 7) {
			     $equiv_final = "APRUEBA";
			} else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
				$equiv_final = "SUPLETORIO";
                if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
  			        // Obtencion de la calificacion del examen supletorio
                    $examen_supletorio = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
                    if ($examen_supletorio >= 7) {
     				   $promedio_final = 7;
                       $equiv_final = "APRUEBA";
                    } else {
                       $equiv_final = "REMEDIAL";
				       if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
				       		// Obtencion de la calificacion del examen remedial
				       		$examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
				       		if ($examen_remedial >= 7) {
	     				   		$promedio_final = 7;
	                            $equiv_final = "APRUEBA";
				       		} else {
				           		$equiv_final = "DE GRACIA";
				           		if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
					       			// Obtencion de la calificacion del examen remedial
					       			$examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
					       			if ($examen_de_gracia >= 7) {
		     				   			$promedio_final = 7;
		                                $equiv_final = "APRUEBA";
					       			} else {
					           			$equiv_final = "NO APRUEBA";
					       			}
				            	}
				       		}
				    	}
                	}
            	} else {
                	// Caso contrario se determina si debe dar examen remedial, considerando la fecha de cierre del examen supletorio
                	$fecha_actual = new DateTime("now");
                	$fecha_cierre = new DateTime(funciones::obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));
                	if ($fecha_actual < $fecha_cierre) {
                		$equiv_final = "SUPLETORIO";
                	} else {
                    	$equiv_final = "REMEDIAL";
                    	// Obtencion de la calificacion del examen remedial
				    	$examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
				    	if ($examen_remedial >= 7) {
				    		$promedio_final = 7;
				        	$equiv_final = "APRUEBA";
				    	} else {
				        	$examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
				        	if ($examen_de_gracia >= 7) {
				        		$promedio_final = 7;
				            	$equiv_final = "APRUEBA";
				        	} else {
				               $equiv_final = "NO APRUEBA";
				        	}
				    	}    
                	}
            	}
         	} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
				$equiv_final = "REMEDIAL";
			   	if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
				   // Obtencion de la calificacion del examen remedial
				   $examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
				   if ($examen_remedial >= 7) {
				   	   $promedio_final = 7;
					   $equiv_final = "APRUEBA";
				   } else {
					   $equiv_final = "DE GRACIA";
					   if (funciones::existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
					   	   // Obtencion de la calificacion del examen remedial
					       $examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
					       if ($examen_de_gracia >= 7) {
						       $promedio_final = 7;
						       $equiv_final = "APRUEBA";
					       } else {
						       $equiv_final = "NO APRUEBA";
					       }				           	
				       }
				   }
			    } else {
					// Caso contrario se determina si debe dar examen de gracia, considerando la fecha de cierre del examen remedial
					$fecha_actual = new DateTime("now");
					$fecha_cierre = new DateTime(funciones::obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo));
					if ($fecha_actual < $fecha_cierre) {
						$equiv_final = "REMEDIAL";
					} else {
						$equiv_final = "DE GRACIA";
					}
			    }
			} else {
			   $equiv_final = "NO APRUEBA";
			}

            if ($contador_sin_examen > 0 && $promedio_periodos > 0) $equiv_final = "SIN EXAMEN";
			if ($promedio_periodos == 0) $equiv_final = "SIN CALIFICACIONES";

			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($suma_periodos,2)."</td>\n"; // Suma
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($promedio_periodos,2)."</td>\n"; // Prom. Quim.
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_supletorio,2)."</td>\n"; // Supletorio
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_remedial,2)."</td>\n"; // Remedial
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($examen_de_gracia,2)."</td>\n"; // Gracia
			$cadena .= "<td width=\"60px\" align=\"left\">".number_format($promedio_final,2)."</td>\n"; // Promedio Final
			$cadena .= "<td width=\"*\" align=\"left\">$equiv_final</td>\n";
			//$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
			$cadena .= "</tr>\n";	
			//*******************************************************************
		} // while($asignatura = parent::fetch_assoc($asignaturas))
		$cadena .= "</table>\n";
		//echo $cadena;
		return $cadena;
	}
	
	function obtenerPeriodosEstudiante($apellidos, $nombres, $id_periodo_lectivo, $id_periodo_evaluacion)
	{
		// Primero debo obtener el id_estudiante y el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_estudiante, ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_estudiante = $paralelo["id_estudiante"];
		$id_paralelo = $paralelo["id_paralelo"];
		$asignaturas = parent::consulta("SELECT a.id_asignatura, as_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_asignatura a WHERE pa.id_paralelo = p.id_paralelo AND pa.id_asignatura = a.id_asignatura AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"30px\">$contador</td>\n";
			$cadena .= "<td width=\"300px\" align=\"left\">$nom_asignatura</td>\n";
			//*******************************************************************
				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				if($num_total_registros>0)
				{
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
					while($aporte = parent::fetch_assoc($aporte_evaluacion))
					{
						$contador_aportes++;
						$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
						$total_rubricas = parent::num_rows($rubrica_evaluacion);
						if($total_rubricas>0)
						{
							$suma_rubricas = 0; $contador_rubricas = 0;
							while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
							{
								$contador_rubricas++;
								$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
								$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
								$total_registros = parent::num_rows($qry);
								if($total_registros>0) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["re_calificacion"];
								} else {
									$calificacion = 0;
								}
								$suma_rubricas += $calificacion;
							}
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						if($contador_aportes <= $num_total_registros - 1)
						{
							$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>";
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
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_aportes,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_aportes,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($examen_quimestral,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_examen,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion_quimestral,2)."</td>";
				}
			//*******************************************************************
			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function obtenerPeriodosEstudianteId($id_estudiante, $id_periodo_lectivo, $id_periodo_evaluacion)
	{
		// Primero debo obtener el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_paralelo = $paralelo["id_paralelo"];
		$asignaturas = parent::consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"35%\" align=\"left\">$nom_asignatura</td>\n";
			//*******************************************************************
				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				if($num_total_registros>0)
				{
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
					while($aporte = parent::fetch_assoc($aporte_evaluacion))
					{
						$contador_aportes++;
						$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
						$total_rubricas = parent::num_rows($rubrica_evaluacion);
						if($total_rubricas>0)
						{
							$suma_rubricas = 0; $contador_rubricas = 0;
							while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
							{
								$contador_rubricas++;
								$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
								$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
								$total_registros = parent::num_rows($qry);
								if($total_registros>0) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["re_calificacion"];
								} else {
									$calificacion = 0;
								}
								$suma_rubricas += $calificacion;
							}
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						if($contador_aportes <= $num_total_registros - 1)
						{
							$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>";
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
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_aportes,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_aportes,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($examen_quimestral,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_examen,2)."</td>";
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion_quimestral,2)."</td>";
				}
			//*******************************************************************
			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}
	
	function obtenerAportesEstudiante($apellidos, $nombres, $id_periodo_lectivo, $id_aporte_evaluacion)
	{
		// Primero debo obtener el id_estudiante y el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_estudiante, ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_estudiante = $paralelo["id_estudiante"];
		$id_paralelo = $paralelo["id_paralelo"];
		$asignaturas = parent::consulta("SELECT a.id_asignatura, as_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_asignatura a WHERE pa.id_paralelo = p.id_paralelo AND pa.id_asignatura = a.id_asignatura AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"30px\">$contador</td>\n";
			$cadena .= "<td width=\"300px\" align=\"left\">$nom_asignatura</td>\n";
				// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
				$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
				$num_total_registros = parent::num_rows($rubrica_evaluacion);
				if($num_total_registros>0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
						$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
						$num_total_registros = parent::num_rows($qry);
						$rubrica_estudiante = parent::fetch_assoc($qry);
						if($num_total_registros>0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion,2)."</td>\n";
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>\n";
					$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
					$cadena .= "</tr>\n";	
				} else {
					$cadena .= "<tr>\n";	
					$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
					$cadena .= "</tr>\n";
				}
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function obtenerAportesEstudianteId($id_estudiante, $id_periodo_lectivo, $id_aporte_evaluacion)
	{
		// Primero debo obtener el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
		$paralelo = parent::fetch_object($consulta);
		$id_paralelo = $paralelo->id_paralelo;
		// Aqui voy a consultar el tipo de educacion i.e. 0: Educacion Basica Superior  1: Bachillerato
		$consulta = parent::consulta("SELECT te_bachillerato FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND p.id_paralelo = $id_paralelo");
		$paralelo = parent::fetch_object($consulta);
		$tipoEducacion = $paralelo->te_bachillerato;
		// Segundo debo consultar las asignaturas del estudiante
		$asignaturas = parent::consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador = 0;
		while($asignatura = parent::fetch_assoc($asignaturas))
		{
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_asignatura = $asignatura["id_asignatura"];
			$nom_asignatura = $asignatura["as_nombre"];
			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"35%\" align=\"left\">$nom_asignatura</td>\n";
				// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
				$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
				$num_total_registros = parent::num_rows($rubrica_evaluacion);
				if($num_total_registros>0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
						$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
						$num_total_registros = parent::num_rows($qry);
						$rubrica_estudiante = parent::fetch_assoc($qry);
						if($num_total_registros>0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion,2)."</td>\n";
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>\n";
					$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
					$cadena .= "</tr>\n";	
				} else {
					$cadena .= "<tr>\n";	
					$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
					$cadena .= "</tr>\n";
				}
			$cadena .= "</tr>\n";
		}
		if($tipoEducacion==0) 
		{
			// Aqui obtengo el id_club del estudiante
			$qry = parent::consulta("SELECT ec.id_club, cl_nombre FROM sw_club c, sw_estudiante_club ec WHERE ec.id_club = c.id_club AND id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");
			$total_registros = parent::num_rows($qry);
			if($total_registros > 0) {
				$registro = parent::fetch_assoc($qry);
				$id_club = $registro["id_club"];
				$cl_nombre = $registro["cl_nombre"];
				
				// Aca calculo el promedio parcial del club al que pertenece el estudiante
				$query = parent::consulta("SELECT calcular_promedio_aporte_club($id_aporte_evaluacion, $id_estudiante, $id_club) AS promedio");
				$calificacion = parent::fetch_assoc($query);
				$promedio_parcial = $calificacion["promedio"];

				// Aqui obtengo la equivalencia cualitativa para el promedio parcial de clubes
				$qry = parent::consulta("SELECT ec_equivalencia FROM sw_escala_proyectos WHERE ec_nota_minima <= $promedio_parcial AND ec_nota_maxima >= $promedio_parcial");
				$registro = parent::fetch_assoc($qry);
				$equivalencia = $registro["ec_equivalencia"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"35%\" align=\"left\">$cl_nombre</td>\n";
				
				$contador_auxiliar = $contador_rubricas;
				
				while ($contador_auxiliar > 0)
				{
					$cadena .= "<td width=\"60px\" align=\"right\">&nbsp;</td>\n";
					$contador_auxiliar--;
				}
				
				$cadena .= "<td width=\"60px\" align=\"right\">$equivalencia</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas

				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>\n";
		return $cadena;
	}
	
	function obtenerRubricasEstudiante($apellidos, $nombres, $id_periodo_lectivo, $id_periodo_evaluacion, $id_rubrica_evaluacion)
	{
		// Primero debo obtener el id_estudiante y el id_paralelo
		$consulta = parent::consulta("SELECT ep.id_estudiante, ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.es_apellidos = '$apellidos' AND e.es_nombres = '$nombres'");
		// Segundo debo consultar las asignaturas del estudiante
		$paralelo = parent::fetch_assoc($consulta);
		$id_estudiante = $paralelo["id_estudiante"];
		$id_paralelo = $paralelo["id_paralelo"];
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_rubrica_personalizada = $id_rubrica_evaluacion");
		$num_registros = parent::num_rows($qry);
		if ($num_registros > 0) {
			$asignaturas = parent::consulta("SELECT a.id_asignatura, as_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_asignatura a WHERE pa.id_paralelo = p.id_paralelo AND pa.id_asignatura = a.id_asignatura AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
			$contador = 0;
			while($asignatura = parent::fetch_assoc($asignaturas))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_asignatura = $asignatura["id_asignatura"];
				$nom_asignatura = $asignatura["as_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"25%\" align=\"left\">$nom_asignatura</td>\n";
				// Aqui obtengo las calificaciones de las rubricas correspondientes
				$qry = parent::consulta("SELECT ru_nombre, ap_nombre, pe_nombre FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion ap, sw_periodo_evaluacion pe WHERE r.id_aporte_evaluacion = ap.id_aporte_evaluacion AND ap.id_periodo_evaluacion = pe.id_periodo_evaluacion AND id_rubrica_evaluacion = $id_rubrica_evaluacion");
				$nombres = parent::fetch_assoc($qry);
				$periodo = $nombres["pe_nombre"];
				$aporte = $nombres["ap_nombre"];
				$rubrica = $nombres["ru_nombre"];

				$qry = parent::consulta("SELECT re_calificacion, ru_nombre, ap_nombre, pe_nombre FROM sw_rubrica_estudiante re, sw_rubrica_evaluacion r, sw_aporte_evaluacion ap, sw_periodo_evaluacion pe WHERE re.id_rubrica_personalizada = r.id_rubrica_evaluacion AND r.id_aporte_evaluacion = ap.id_aporte_evaluacion AND ap.id_periodo_evaluacion = pe.id_periodo_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
				$num_registros = parent::num_rows($qry);
				if ($num_registros > 0) {
					$rubrica_estudiante = parent::fetch_assoc($qry);
					$calificacion = $rubrica_estudiante["re_calificacion"];
				} else {
					$calificacion = 0;
				}
				$cadena .= "<td width=\"20%\">$periodo</td>\n";
				$cadena .= "<td width=\"20%\">$aporte</td>\n";
				$cadena .= "<td width=\"20%\">$rubrica</td>\n";
				$cadena .= "<td width=\"10%\">".number_format($calificacion,2)."</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td align=\"center\">No existen calificaciones ingresadas para esta r&uacute;brica</td></tr>";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function paginarEstudiantes($cantidad_registros,$numero_pagina,$total_registros,$id_paralelo)
	{
		$total_paginas = ceil($total_registros / $cantidad_registros);
		$mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarEstudiantesParalelo(".$cantidad_registros.",1,".$total_registros.",".$id_paralelo.")'> Primero </a> </span>";
		if (($numero_pagina - 1) > 0) {
			$mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarEstudiantesParalelo(".$cantidad_registros.",".($numero_pagina-1).",".$total_registros.",".$id_paralelo.")'>Anterior</a></span>";
		} else {
			$mensaje .= "<span> < Anterior</span>";
		}
		for ($i=1; $i <= $total_paginas; $i++) {
			if ($numero_pagina == $i) {
				$mensaje .= "<b> P&aacute;gina ".$numero_pagina."</b>";
			} else {
				$mensaje .= "<span class='link_table'> <a href='#' onclick='paginarEstudiantesParalelo(".$cantidad_registros.",".$i.",".$total_registros.",".$id_paralelo.")'>$i</a></span>";
			}
		}
		if (($numero_pagina+1) <= $total_paginas) {
			$mensaje .= " <span class='link_table'><a href='#' onclick='paginarEstudiantesParalelo(".$cantidad_registros.",".($numero_pagina+1).",".$total_registros.",".$id_paralelo.")'>Siguiente</a> > </span>";
		} else {
			$mensaje .= " <span>Siguiente</a> > </span>";
		}
		$mensaje .= " <span class='link_table'><a href='#' onclick='paginarEstudiantesParalelo(".$cantidad_registros.",".$total_paginas.",".$total_registros.",".$id_paralelo.")'>Ultimo</a></span> >>";
		return $mensaje;
	}

	function listarEstudiantes()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado, es_cedula, es_telefono, es_email FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_paralelo = " . $this->id_paralelo . " ORDER BY es_apellidos, es_nombres ASC");
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
				$cedula = $estudiantes["es_cedula"];
				$telefono = $estudiantes["es_telefono"];
                $email = $estudiantes["es_email"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$codigo</td>\n";	
				$cadena .= "<td width=\"18%\" align=\"left\">$apellidos</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$nombres</td>\n";
				$cadena .= "<td width=\"12%\" align=\"left\">$cedula</td>\n";
				$cadena .= "<td width=\"12%\" align=\"left\">$telefono</td>\n";
                $cadena .= "<td width=\"12%\" align=\"left\">$email</td>\n";
				$cadena .= "<td width=\"8%\" align=\"center\"> <input type=\"checkbox\" name=\"chkretirado_" . $contador . "\" $checked onclick=\"actualizar_estado_retirado(this,". $codigo . ")\"> </td>\n";
				$cadena .= "<td width=\"5%\" class=\"link_table\"><a href=\"#\" onclick=\"editarEstudiante(".$codigo.")\">editar</a></td>\n";
				$cadena .= "<td width=\"5%\" class=\"link_table\"><a href=\"#\" onclick=\"quitarEstudiante(".$codigo.")\">quitar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes para este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarEstudiantesPromocion()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado, es_cedula FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_paralelo = " . $this->id_paralelo . " ORDER BY es_apellidos, es_nombres ASC");
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
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$codigo</td>\n";	
				$cadena .= "<td width=\"24%\" align=\"left\">$apellidos</td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$nombres</td>\n";
				//Aqui calculo el promedio general final del estudiante
				$qry = parent::consulta("SELECT calcular_promedio_general(".$this->id_periodo_lectivo.",".$codigo.",".$this->id_paralelo.") AS promedio_general");
				$reg = parent::fetch_assoc($qry);
				$pro = $reg["promedio_general"];
				$cadena .= "<td width=\"32%\" align=\"left\">$pro</td>\n";
				//$cadena .= "<td width=\"32%\" align=\"left\">0.00</td>\n";
				$cadena .= "<td width=\"10%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarEstudiante(".$codigo.")\">seleccionar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes para este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarEstudiante()
	{
        // Primero calculo cual es el maximo numero de matricula
		$qry = "SELECT calc_max_nro_matricula() AS max_nro_matricula";
		$consulta = parent::consulta($qry);
		if (!$consulta)
			$mensaje = "No se pudo obtener el maximo numero de matricula...Error: " . mysql_error();
		else {
			$maximo = parent::fetch_object($consulta);
			$max_nro_matricula = $maximo->max_nro_matricula;
		}
                    
		$qry = "INSERT INTO sw_estudiante (es_nro_matricula, "
                        . "es_apellidos, es_nombres, es_cedula, "
                        . "es_genero, es_email, es_nombre_completo, es_direccion, es_sector, es_telefono) VALUES (";
        $qry .= "'" . $max_nro_matricula . "',";
		$qry .= "'" . $this->es_apellidos . "',";
		$qry .= "'" . $this->es_nombres . "',";
		$qry .= "'" . $this->es_cedula . "',";
		$qry .= "'" . $this->es_genero ."',";
		$qry .= "'" . $this->es_email ."',";
		$qry .= "'" . $this->es_apellidos ." ".$this->es_nombres."',";
		$qry .= "'" . $this->es_direccion . "',";
		$qry .= "'" . $this->es_sector . "',";
		$qry .= "'" . $this->es_telefono . "')";
        //echo $qry;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo insertar el Estudiante...Error: " . mysql_error();
			$datos = array(
				'ok' => false,
				'mensaje' => $mensaje,
				'id_estudiante' => 0
			);
		} else {
		    $consulta = parent::consulta("SELECT MAX(id_estudiante) AS max_id_estudiante FROM sw_estudiante");
			$estudiante = parent::fetch_object($consulta);
			$id_estudiante = $estudiante->max_id_estudiante;
			$qry = "INSERT INTO sw_estudiante_periodo_lectivo (id_estudiante, id_periodo_lectivo, id_paralelo, es_estado) VALUES (";
			$qry .= $id_estudiante . ",";
			$qry .= $this->id_periodo_lectivo . ",";
			$qry .= $this->id_paralelo . ",'N')";
			$consulta = parent::consulta($qry);
			$mensaje = "Estudiante " . $this->es_apellidos . " " . $this->es_nombres . " insertado exitosamente...";
			if (!$consulta) {
				$mensaje = "No se pudo insertar el Estudiante...Error: " . mysql_error();
				$datos = array(
					'ok' => false,
					'mensaje' => $mensaje,
					'id_estudiante' => 0
				);
			} else {
				$datos = array(
					'ok' => true,
					'mensaje' => $mensaje,
					'id_estudiante' => $id_estudiante
				);
			}
		}
		//return $mensaje;
		//Seteamos el header de "content-type" como "JSON" para que jQuery lo reconozca como tal
		header('Content-Type: application/json');
		//Devolvemos el array pasado a JSON como objeto
		return json_encode($datos, JSON_FORCE_OBJECT);
	}
	
	function insertarEstudianteSeleccionado()
	{
		// Falta comprobar que no se ingrese un estudiante de un paralelo que no corresponde...
		$qry = "INSERT INTO sw_estudiante_periodo_lectivo (id_estudiante, id_periodo_lectivo, id_paralelo, es_estado) VALUES (";
		$qry .= $this->code . ",";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= $this->id_paralelo . ",'N')";
		$consulta = parent::consulta($qry);
		$mensaje = "Estudiante " . $this->es_apellidos . " " . $this->es_nombres . " insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Estudiante...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarEstudiante()
	{
		$qry = "UPDATE sw_estudiante SET ";
		$qry .= "es_apellidos = '" . $this->es_apellidos . "',";
		$qry .= "es_nombres = '" . $this->es_nombres . "',";
		$qry .= "es_cedula = '" . $this->es_cedula . "',";
		$qry .= "es_genero = '" . $this->es_genero . "',";
		$qry .= "es_email = '" . $this->es_email . "',";
		$qry .= "es_direccion = '" . $this->es_direccion . "',";
		$qry .= "es_sector = '" . $this->es_sector . "',";
		$qry .= "es_telefono = '" . $this->es_telefono . "'";
		$qry .= " WHERE id_estudiante = " . $this->code;
		//echo $qry;
		$consulta = parent::consulta($qry);
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Estudiante...Error: " . mysql_error();
		else {
			$qry = "UPDATE sw_estudiante_periodo_lectivo SET ";
			$qry .= "id_paralelo = " . $this->id_paralelo;
			$qry .= " WHERE id_estudiante = " . $this->code;
			$qry .= " AND id_periodo_lectivo = " . $this->id_periodo_lectivo;
			$consulta = parent::consulta($qry);
			$mensaje = "Estudiante " . $this->es_apellidos . " " . $this->es_nombres . " actualizado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo actualizar el Estudiante...Error: " . mysql_error();
		}	
		return $mensaje;
	}

	function insertarRepresentante()
	{
                    
		$qry = "INSERT INTO sw_representante (id_estudiante, re_apellidos, re_nombres, re_cedula, ";
		$qry .= "re_email, re_nombre_completo, re_direccion, re_sector, re_telefono, re_parentesco) VALUES (";
		$qry .= $this->code . ","; //id_estudiante
		$qry .= "'" . $this->re_apellidos . "',";
		$qry .= "'" . $this->re_nombres . "',";
		$qry .= "'" . $this->re_cedula . "',";
		$qry .= "'" . $this->re_email ."',";
		$qry .= "'" . $this->re_apellidos ." ".$this->re_nombres."',";
		$qry .= "'" . $this->re_direccion . "',";
		$qry .= "'" . $this->re_sector . "',";
		$qry .= "'" . $this->re_telefono . "',";
		$qry .= "'" . $this->re_parentesco . "')";
		//echo $qry;
		$consulta = parent::consulta($qry);
		if (!$consulta)
			$mensaje = "No se pudo insertar el Representante...Error: " . mysql_error();
		else {
			$mensaje = "Representante insertado exitosamente...";
		}
		return $mensaje;
	}
	
	function actualizarRepresentante()
	{
		$qry = "UPDATE sw_representante SET ";
		$qry .= "re_apellidos = '" . $this->re_apellidos . "',";
		$qry .= "re_nombres = '" . $this->re_nombres . "',";
		$qry .= "re_nombre_completo = '" . $this->re_nombre_completo . "',";
		$qry .= "re_cedula = '" . $this->re_cedula . "',";
		$qry .= "re_email = '" . $this->re_email . "',";
		$qry .= "re_direccion = '" . $this->re_direccion . "',";
		$qry .= "re_sector = '" . $this->re_sector . "',";
		$qry .= "re_telefono = '" . $this->re_telefono . "',";
		$qry .= "re_observacion = '" . $this->re_observacion . "',";
		$qry .= "re_parentesco = '" . $this->re_parentesco . "'";
		$qry .= " WHERE id_representante = " . $this->id_representante;
		//echo $qry;
		$consulta = parent::consulta($qry);
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Representante...Error: " . mysql_error();
		else {
			$mensaje = "Representante " . $this->re_apellidos . " " . $this->re_nombres . " actualizado exitosamente...";
		}	
		return $mensaje;
	}

	function eliminarEstudiante()
	{
		// Antes de eliminar al estudiante tengo que comprobar que no tenga calificaciones asociadas
		if ($this->existenCalificaciones()) {
			$mensaje = "No se puede eliminar el Estudiante porque tiene calificaciones asociadas...";
		} else {
			$qry = "DELETE FROM sw_estudiante_periodo_lectivo WHERE id_estudiante=". $this->code ." AND id_periodo_lectivo = ". $this->id_periodo_lectivo;
			$consulta = parent::consulta($qry);
			if (!$consulta) {
				$mensaje = "No se pudo eliminar el Estudiante...Error: " . mysql_error();
			} else {
				$qry = "DELETE FROM sw_estudiante WHERE id_estudiante=". $this->code;
				$consulta = parent::consulta($qry);
				$mensaje = "Estudiante eliminado exitosamente...";
				if (!$consulta)
					$mensaje = "No se pudo eliminar el Estudiante...Error: " . mysql_error();
			}
		}
		return $mensaje;
	}
	
	function quitarEstudiante($iTotal)
	{
		//Aqui vamos a eliminar los datos de las tablas relacionadas
		$qry = "DELETE FROM sw_representante WHERE id_estudiante=". $this->code;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo eliminar el representante asociado...Error: " . mysql_error();
			return $mensaje;
		}
		$qry = "DELETE FROM sw_rubrica_estudiante WHERE id_estudiante=". $this->code ." AND id_paralelo=". $this->id_paralelo;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo eliminar las calificaciones asociadas...Error: " . mysql_error();
			return $mensaje;
		}
		$qry = "DELETE FROM sw_calificacion_comportamiento WHERE id_estudiante=". $this->code ." AND id_paralelo=". $this->id_paralelo;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo eliminar las calificaciones asociadas...Error: " . mysql_error();
			return $mensaje;
		}
		$qry = "DELETE FROM sw_comportamiento_inspector WHERE id_estudiante=". $this->code ." AND id_paralelo=". $this->id_paralelo;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo eliminar las calificaciones asociadas...Error: " . mysql_error();
			return $mensaje;
		}
		$qry = "DELETE FROM sw_asistencia_estudiante WHERE id_estudiante=". $this->code ." AND id_paralelo=". $this->id_paralelo;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$mensaje = "No se pudo eliminar las calificaciones asociadas...Error: " . mysql_error();
			return $mensaje;
		}

		$qry = "DELETE FROM sw_estudiante_periodo_lectivo WHERE id_estudiante=". $this->code ." AND id_periodo_lectivo = ". $this->id_periodo_lectivo;
		$consulta = parent::consulta($qry);
		$mensaje = "Estudiante des-matriculado exitosamente...";
		if (!$consulta) {
			$mensaje = "No se pudo des-matricular al Estudiante...Error: " . mysql_error();
		} else {
			if ($iTotal)
				$mensaje .= $this->eliminarEstudiante();
		}
		return $mensaje;
	}

	function getNumeroEstudiantesPorParalelo($id_periodo_lectivo)
	{
		$query = "SELECT ep.id_paralelo, 
						 CONCAT(cu_abreviatura, pa_nombre, ' ', es_abreviatura) AS paralelo, 
						 COUNT(*) AS numero 
					FROM sw_estudiante_periodo_lectivo ep, 
						 sw_paralelo p, 
						 sw_curso c, 
						 sw_especialidad e 
				   WHERE p.id_paralelo = ep.id_paralelo 
				     AND c.id_curso = p.id_curso 
					 AND e.id_especialidad = c.id_especialidad 
					 AND id_periodo_lectivo = $id_periodo_lectivo 
				GROUP BY ep.id_paralelo 
				ORDER BY pa_orden";
		$result = parent::consulta($query);				
		$datos = array();

		while($dato = parent::fetch_assoc($result)){
			$datos[] = array('paralelo' => $dato['paralelo'], 
				         	 'numero' => $dato['numero']);
		}
		
		return json_encode($datos);
	}

	function contarEstudiantesParalelo()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_paralelo = " . $this->id_paralelo);
		return json_encode(parent::fetch_assoc($consulta));	
	}

	function paginarEstudiantesParalelo($cantidad_registros,$numero_pagina,$total_registros,$id_paralelo,$id_asignatura)
	{
		$total_paginas = ceil($total_registros / $cantidad_registros);
		$mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarEstudiantesParalelo(1,".$total_registros.",".$id_paralelo.",".$id_asignatura.")'> Primero </a> </span>";
		if (($numero_pagina - 1) > 0) {
			$mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarEstudiantesParalelo(".($numero_pagina-1).",".$total_registros.",".$id_paralelo.",".$id_asignatura.")'>Anterior</a></span>";
		} else {
			$mensaje .= "<span> < Anterior</span>";
		}
		for ($i=1; $i <= $total_paginas; $i++) {
			if ($numero_pagina == $i) {
				$mensaje .= "<span style='font-weight:bold;color:#f00'> P&aacute;gina ".$numero_pagina."</span>";
			} else {
				$mensaje .= "<span class='link_table'> <a href='#' onclick='paginarEstudiantesParalelo(".$i.",".$total_registros.",".$id_paralelo.",".$id_asignatura.")'>$i</a></span>";
			}
		}
		if (($numero_pagina+1) <= $total_paginas) {
			$mensaje .= " <span class='link_table'><a href='#' onclick='paginarEstudiantesParalelo(".($numero_pagina+1).",".$total_registros.",".$id_paralelo.",".$id_asignatura.")'>Siguiente</a> > </span>";
		} else {
			$mensaje .= " <span>Siguiente</a> > </span>";
		}
		$mensaje .= " <span class='link_table'><a href='#' onclick='paginarEstudiantesParalelo(".$total_paginas.",".$total_registros.",".$id_paralelo.",".$id_asignatura.")'>Ultimo</a></span> >>";
		return $mensaje;
	}

}
?>