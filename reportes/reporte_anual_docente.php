<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.periodos_evaluacion.php');
	require_once('../scripts/clases/class.funciones.php');

	// Variables enviadas mediante POST	
	$id_paralelo = $_POST["id_paralelo"];
	$id_asignatura = $_POST["id_asignatura"];

	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];

	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	$usuario = new usuarios();
	$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$asignatura = new asignaturas();
	$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

	$pdf=new FPDF('P');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$w=$pdf->GetStringWidth($nombreInstitucion);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombreInstitucion,0,0,'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','B',12);
	$title2="REPORTE DEL PERIODO LECTIVO ".$nombrePeriodoLectivo;
	$w=$pdf->GetStringWidth($title2);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,9,$title2,0,0,'C');
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,10,"ASIGNATURA: ".$nombreAsignatura,0,0);
	$pdf->Ln(5);
	$pdf->Cell(20,10,"CURSO: ".$nombreParalelo,0,0);
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(70,6,"NOMINA",1,0,'C');
	// Aqui imprimo las cabeceras de cada periodo de evaluacion
	$pdf->Cell(11,6,"1ER. Q.",1,0,'C');
	$pdf->Cell(11,6,"2DO. Q.",1,0,'C');
	$pdf->Cell(11,6,"SUMA",1,0,'C');
	$pdf->Cell(11,6,"PROM",1,0,'C');
	$pdf->Cell(11,6,"SUP.",1,0,'C');
	$pdf->Cell(11,6,"REM.",1,0,'C');
	$pdf->Cell(11,6,"GRA.",1,0,'C');
	$pdf->Cell(11,6,"P.F.",1,0,'C');
	$pdf->Cell(24,6,"OBSERVACION",1,0,'C');
	$pdf->Ln();
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$funciones = new funciones();
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($estudiante = $db->fetch_assoc($consulta))
		{
			$id_estudiante = $estudiante["id_estudiante"];
			$apellidos = $estudiante["es_apellidos"];
			$nombres = $estudiante["es_nombres"];
			
			$terminacion = ($estudiante["es_genero"] == "M") ? "O" : "A";
			$retirado = $estudiante["es_retirado"];

			$contador++; $contador_ceros = 0;

			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($estudiante["es_apellidos"])." ".utf8_decode($estudiante["es_nombres"]);
			$pdf->Cell(70,6,$nombre_completo,1,0,'L');
			
			$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = $db->num_rows($periodo_evaluacion);
			if($num_total_registros>0)
			{
				$suma_periodos = 0; $contador_periodos = 0;
				while($periodo = $db->fetch_assoc($periodo_evaluacion))
				{
					$contador_periodos++;
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
				
					$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
					$aporte_evaluacion = $db->consulta($qry);
					$num_total_registros = $db->num_rows($aporte_evaluacion);
					if($num_total_registros>0)
					{
						// Aqui calculo los promedios y desplegar en la tabla
						$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
						while($aporte = $db->fetch_assoc($aporte_evaluacion))
						{
							$contador_aportes++;
							$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
							$total_rubricas = $db->num_rows($rubrica_evaluacion);
							if($total_rubricas>0)
							{
								$suma_rubricas = 0; $contador_rubricas = 0;
								while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
								{
									$contador_rubricas++;
									$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
									$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
									$total_registros = $db->num_rows($qry);
									if($total_registros>0) {
										$rubrica_estudiante = $db->fetch_assoc($qry);
										$calificacion = $rubrica_estudiante["re_calificacion"];
									} else {
										$calificacion = 0;
									}
									$suma_rubricas += $calificacion;
								}
							}
							// Aqui calculo el promedio del aporte de evaluacion
							$promedio = $paralelo->truncateFloat($suma_rubricas / $contador_rubricas,2);
							if($promedio==0) $contador_ceros++;
							if($contador_aportes <= $num_total_registros - 1) {
								$suma_promedios += $promedio;
							} else {
								$examen_quimestral = $promedio;
							}
						}
					}
					// Aqui se calculan las calificaciones del periodo de evaluacion
					$promedio_aportes = $paralelo->truncateFloat($suma_promedios / ($contador_aportes - 1),2);
					$ponderado_aportes = $paralelo->truncateFloat(0.8 * $promedio_aportes,2);
					$ponderado_examen = $paralelo->truncateFloat(0.2 * $examen_quimestral,2);
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					$suma_periodos += $calificacion_quimestral;
					//echo $suma_periodos . "<br>";
					$pdf->Cell(11,6,number_format($calificacion_quimestral,2),1,0,'C');
				} // fin while $periodo_evaluacion
			} // fin if $periodo_evaluacion
			// Calculo la suma y el promedio de los dos quimestres
			$promedio_periodos = $paralelo->truncateFloat($suma_periodos / $contador_periodos,2);
			$promedio_final = $promedio_periodos;
			$supletorio = " "; $remedial = " "; $de_gracia = " ";
			if($retirado == "S") {
				$observacion = "RETIRAD" . $terminacion;
			} else if ($promedio_periodos >= 7 && $promedio_periodos <= 10) {
				$observacion = "APRUEBA";
			} else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
				$observacion = "SUPLETORIO";
				if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
					$supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
					if ($supletorio >= 7) {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					} else {
                                            if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
                                                $remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                                                if ($remedial >= 7) {
                                                    $promedio_final = 7;
                                                    $observacion = "APRUEBA";
                                                } else {
                                                    $observacion = "NO APRUEBA";
                                                }
                                            } else {
                                                $observacion = "REMEDIAL";
                                            }
					}
				}
			} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
				$observacion = "REMEDIAL";
				if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
					$remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
					if ($remedial >= 7) {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					} else {
						$observacion = "NO APRUEBA";
					}
				}
			} else {
				$observacion = "SIN NOTAS";
			}
                        
                        // Aca reviso si tiene examen de gracia
                        $query = $db->consulta("SELECT contar_remediales_no_aprobados($id_periodo_lectivo, $id_estudiante, $id_paralelo) AS contador");
			$registro = $db->fetch_assoc($query);
			$c_remediales = $registro["contador"];
                        
                        $qry = $db->consulta("SELECT determinar_asignatura_de_gracia($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS id_asignatura");
                        $asignatura = $db->fetch_assoc($qry);
                        $vid_asignatura = $asignatura["id_asignatura"];
                        
                        if($c_remediales == 1 && $vid_asignatura == $id_asignatura) {
                            // Si tiene que dar examen de gracia entonces obtengo la calificacion respectiva
                            $calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
                            if($calificacion == 0) {
                                $de_gracia = "SE";
                            } else {
                                $de_gracia = $calificacion;
                                $promedio_final = 7;
                                $observacion = "APRUEBA";
                            }
                        }
                        
			$pdf->Cell(11,6,number_format($suma_periodos,2),1,0,'C');
			$pdf->Cell(11,6,number_format($promedio_periodos,2),1,0,'C');
			$pdf->Cell(11,6,$supletorio,1,0,'C');
			$pdf->Cell(11,6,$remedial,1,0,'C');
			$pdf->Cell(11,6,$de_gracia,1,0,'C');
			$pdf->Cell(11,6,number_format($promedio_final,2),1,0,'C');
			$pdf->Cell(24,6,$observacion,1,0,'C');
			$pdf->Ln();
		}
	}
	$pdf->Ln(4);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)_________________________________",0,0,'L');
	$pdf->Output();
?>
