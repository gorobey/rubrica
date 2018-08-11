<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.funciones.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		var $nombreInstitucion = "";
		
		//Cabecera de página
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$title1=$this->nombreInstitucion;
			$w=$this->GetStringWidth($title1);
			$this->SetX((298-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="REPORTE DE SUPLETORIOS PERIODO LECTIVO ".$this->nombrePeriodoLectivo;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="CURSO: ".$this->nombreParalelo;
			$w=$this->GetStringWidth($title3);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
		}
		
		//Pie de página
		function Footer()
		{
			//Posición: a 1,5 cm del final
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//Número de página
			$this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C');
		}
	}

	// Variables enviadas mediante POST	
	$id_paralelo = $_POST["id_paralelo"];

	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));
	
	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	$pdf=new PDF('L');
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	$pdf->nombreInstitucion = $nombreInstitucion;

	$pdf->AliasNbPages();
	$pdf->AddPage();

	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(70,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$funciones = new funciones();
	$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");	
	while($titulo_asignatura = $db->fetch_assoc($asignaturas))
		$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
	$pdf->Ln();

	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0; 
		while($paralelo = $db->fetch_assoc($consulta))
		{

			$id_estudiante = $paralelo["id_estudiante"];
			$retirado = $paralelo["es_retirado"];
			
			$query = $db->consulta("SELECT aprueba_todas_asignaturas($id_periodo_lectivo, $id_estudiante, $id_paralelo) AS aprueba");
			$registro = $db->fetch_assoc($query);
			$aprueba = $registro["aprueba"];
			
			if(!$aprueba) {

				$contador++;
	
				if($contador % 25 == 0) 
				{
					$pdf->AddPage(); 
					$pdf->Ln(10);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(8,6,"Nro.",1,0,'C');
					$pdf->Cell(70,6,"Nómina",1,0,'C');
					// Aqui imprimo las cabeceras de cada asignatura
					$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");	
					while($titulo_asignatura = $db->fetch_assoc($asignaturas))
						$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
					$pdf->Ln();
				}
				
				$pdf->Cell(8,5,$contador,1,0,'C');
				$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
				$pdf->Cell(70,5,$nombre_completo,1,0,'L');

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$id_asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = $db->num_rows($id_asignaturas);
				if($total_asignaturas>0)
				{

					while ($asignatura = $db->fetch_assoc($id_asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
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
									// Aqui calculo los promedios
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
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = $db->fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
							
								// Aqui se calculan las calificaciones del periodo de evaluacion
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = $db->fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)
					
						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;

						if($promedio_periodos >= 7) {
							$pdf->Cell(13,5," ",1,0,'C');
						} else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio
	
							$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
							
							// Aqui desplego la calificacion del examen supletorio
							$pdf->Cell(13,5,number_format($calificacion,2),1,0,'C');

						} else if($promedio_periodos < 5 && $retirado == 'N') {
							$observacion = ($promedio_periodos == 0) ? "S/N" : "REMED.";
							$pdf->Cell(13,5,$observacion,1,0,'C');
						} 

					} // while ($asignatura = $db->fetch_assoc($asignaturas))
				
					$pdf->Ln();
		
				} // if($total_asignaturas>0)

			} // if($db->consulta("SELECT calcular_promedio_general($id_periodo_lectivo, $id_estudiante, $id_paralelo)") < 7)
			
		} // while($paralelo = $db->fetch_assoc($consulta))

	} // if($num_total_registros>0)
	
	$pdf->AddPage();
	$pdf->Ln();
	$pdf->SetFont('Arial','B',14);
	$title1="LISTA DE ASIGNATURAS";
	$w=$pdf->GetStringWidth($title1);
	$pdf->SetX((298-$w)/2);
	$pdf->Cell($w,10,$title1,0,0,'C');
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(20,6,"Abreviatura",1,0,'C');
	$pdf->Cell(150,6,"Nombre",1,0,'C');
	$pdf->Ln();

	// Aqui obtengo el recordset de las asignaturas del paralelo
	$asignaturas = $db->consulta("SELECT as_nombre, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
	$contador = 0;
	while ($asignatura = $db->fetch_assoc($asignaturas)) {
		$contador++;
	    $pdf->Cell(8,5,$contador,1,0,'C');
		$pdf->Cell(20,5,$asignatura["as_abreviatura"],1,0,'C');
		$pdf->Cell(150,5,$asignatura["as_nombre"],1,0,'L');
		$pdf->Ln();
	}
	$pdf->Output();
?>
