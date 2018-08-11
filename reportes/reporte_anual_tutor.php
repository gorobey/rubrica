<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.asignaturas.php');
	require('../scripts/clases/class.paralelos.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.institucion.php');

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
			$title2="REPORTE DEL PERIODO LECTIVO ".$this->nombrePeriodoLectivo;
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
			//Nombre del archivo
			$filename = pathinfo(__FILE__, PATHINFO_BASENAME);
			//Número de página
			$this->Cell(0,10,'Página '.$this->PageNo().' de {nb} - Archivo: '.$filename,0,0,'C');
		}
	}

	// Variables enviadas mediante POST	
	$id_paralelo = $_POST["id_paralelo"];

	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];

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
	$pdf->Cell(68,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_paralelo_asignatura p WHERE a.id_asignatura = p.id_asignatura AND id_paralelo = $id_paralelo");	
	while($titulo_asignatura = $db->fetch_assoc($asignaturas))
		$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
	$pdf->Cell(24,6,"Observaciones",1,0,'C');
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");

	$contador = 0;

	while($paralelo = $db->fetch_assoc($consulta))
	{
		$id_estudiante = $paralelo["id_estudiante"];
		$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";
		$retirado = $paralelo["es_retirado"];
		
		$contador++;
		$contador_general_sin_examen = 0;

		if($contador % 30 == 0) {
			$pdf->AddPage(); 
			$pdf->Ln(10);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(8,6,"Nro.",1,0,'C');
			$pdf->Cell(68,6,"Nómina",1,0,'C');
			// Aqui imprimo las cabeceras de cada asignatura
			$db = new MySQL();
			$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_paralelo_asignatura p WHERE a.id_asignatura = p.id_asignatura AND id_paralelo = $id_paralelo");	
			while($titulo_asignatura = $db->fetch_assoc($asignaturas))
				$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
			$pdf->Cell(24,6,"Observaciones",1,0,'C');
			$pdf->Ln();
		}

		$pdf->Cell(8,5,$contador,1,0,'C');
		$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
		$pdf->Cell(68,5,$nombre_completo,1,0,'L');

		$contador_no_aprueba=0; 
		$contador_supletorio=0; 
		$contador_remedial=0; 
		
		$asignaturas = $db->consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
		$total_asignaturas = $db->num_rows($asignaturas);
		if($total_asignaturas>0)
		{

			while ($asignatura = $db->fetch_assoc($asignaturas))
			{
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$contador_sin_examen=0;	
						
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
						//echo $qry . "<br>";
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
								$promedio = $suma_rubricas / $contador_rubricas;
								if($contador_aportes <= $num_total_registros - 1) {
									$suma_promedios += $promedio;
								} else {
									$examen_quimestral = $promedio;
								}
							} //while($aporte = $db->fetch_assoc($aporte_evaluacion))
							if ($examen_quimestral == 0) $contador_sin_examen++;
						} // if($num_total_registros>0)
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						$suma_periodos += $calificacion_quimestral;
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_periodos = $suma_periodos / $contador_periodos;
				if($promedio_periodos==0)
					$contador_no_aprueba++;
				else if($promedio_periodos > 0 && $promedio_periodos < 5 && $contador_sin_examen == 0) {
					// Recupero la calificacion del examen remedial si existe
					$qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 3");
					$registro = $db->fetch_assoc($qry);
					$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
						
					$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
					$total_registros = $db->num_rows($qry);
					if($total_registros>0) {
						$rubrica_estudiante = $db->fetch_assoc($qry);
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = 0;
					}
					if($calificacion < 7) 
						$contador_remedial++;
					else
						$promedio_periodos = 7;
				} else if($promedio_periodos >= 5 && $promedio_periodos < 7 && $contador_sin_examen == 0) {
					// Recupero la calificacion del examen supletorio si existe
					$qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 2");
					$registro = $db->fetch_assoc($qry);
					$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
						
					$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
					$total_registros = $db->num_rows($qry);
					if($total_registros>0) {
						$rubrica_estudiante = $db->fetch_assoc($qry);
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = 0;
					}
					if($calificacion < 7) 
						$contador_supletorio++;
					else
						$promedio_periodos = 7;
				}
				
				// Aqui desplegar el promedio de los quimestres

				if ($contador_sin_examen > 0) $contador_general_sin_examen++;
				
				if ($retirado == "S") {
					$pdf->Cell(13,5,"-",1,0,'C');
				} else if ($promedio_periodos == 0) {
					$pdf->Cell(13,5,"S/N",1,0,'C');
				} else if ($contador_sin_examen > 0) {
					$pdf->Cell(13,5,"S/E",1,0,'C');
				} else { // No tiene problemas
					$pdf->Cell(13,5,number_format($promedio_periodos,2),1,0,'C');
				}
				
			} // while ($asignatura = $db->fetch_assoc($asignaturas))
			//echo "<br>";
		} // if($total_asignaturas>0)
		
		$observacion="";
		/*if($retirado == "S")
			$observacion="RETIRAD" . $terminacion;
		else*/ 
		if($contador_no_aprueba > 0 || $retirado == "S")
			$observacion="NO APRUEBA";
		else if($contador_remedial > 0 && $contador_supletorio > 0) 
			$observacion="REM.(" . $contador_remedial . ") SUP.(" . $contador_supletorio . ")";
		else if($contador_remedial > 0) 
			$observacion="REM.(" . $contador_remedial . ")";
		else if($contador_supletorio > 0) 
			$observacion="SUP.(" . $contador_supletorio . ")";
		else if($contador_general_sin_examen > 0)
			$observacion="SIN EXAMEN";
		else $observacion="APRUEBA";
		$pdf->Cell(24,5,$observacion,1,0,'C');
		$pdf->Ln();
	}

	$pdf->Output();
?>
