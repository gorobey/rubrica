<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.paralelos.php');
	require('../scripts/clases/class.institucion.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.periodos_evaluacion.php');

	function equiv_rendimiento($id_periodo_lectivo, $calificacion)
	{
		$db = new MySQL();
		// Determinacion de la letra de equivalencia que corresponde a la calificacion dada
		$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
		while ($escala = $db->fetch_assoc($escala_calificacion))
		{
			$nota_minima = $escala["ec_nota_minima"];
			$nota_maxima = $escala["ec_nota_maxima"];
			if ($calificacion >= $nota_minima && $calificacion <= $nota_maxima) {
				$equivalencia = $escala["ec_equivalencia"];
				break;
			}
		}
		return $equivalencia;
	}
	
	class PDF extends FPDF
	{
		var $nombreRector = "";
		var $nombreParalelo = "";
		var $nombreSecretario = "";
		var $nombreInstitucion = "";
		var $telefonoInstitucion = "";
		var $direccionInstitucion = "";
		var $nombrePeriodoLectivo = "";
		var $nombrePeriodoEvaluacion = "";
		
		//Cabecera de página
		function Header()
		{
			$this->SetFont('Arial','B',12);
			$title1="REPUBLICA DEL ECUADOR";
			$w=$this->GetStringWidth($title1);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',10);
			$title2="MINISTERIO DE EDUCACION";
			$w=$this->GetStringWidth($title2);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title2,0,0,'C');
			$this->Ln(10);
			$this->SetFont('Arial','B',14);
			$title3=$this->nombreInstitucion;
			$w=$this->GetStringWidth($title3);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title3,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','',10);
			$title4="QUITO-ECUADOR";
			$w=$this->GetStringWidth($title4);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title4,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','',8);
			$this->Cell(100,10,$this->direccionInstitucion,0,0,'L');
			$this->Cell(90,10,"TELEFONO: " . $this->telefonoInstitucion,0,0,'R');
			$this->Ln(10);
			$this->SetFont('Arial','B',14);
			$title5="INFORME DE EVALUACION DE ".$this->nombrePeriodoEvaluacion;
			$w=$this->GetStringWidth($title5);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title5,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title6=utf8_decode("AÑO LECTIVO: ".$this->nombrePeriodoLectivo);
			$w=$this->GetStringWidth($title6);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title6,0,0,'C');
			$this->Ln();
		}
		
		//Pie de página
		function Footer()
		{
			//Posición: a 3 cm del final
			$this->SetY(-30);
			//Arial italic 8
			$this->SetFont('Arial','',8);
			//Aqui van las firmas de rectora y secretaria
			$this->Cell(0,10,'___________________________',0,0,'L');
			$titulo1 = '___________________________';
			$w=$this->GetStringWidth($titulo1);
			$this->SetX(200-$w);
			$this->Cell($w,8,$titulo1,0,0,'R');
			$this->Ln(5);
			$this->Cell(0,10,'      '.$this->nombreRector,0,0,'L');
			$titulo2 = '            '.$this->nombreSecretario;
			$w=$this->GetStringWidth($titulo2);
			$this->SetX(190-$w);
			$this->Cell($w,8,$titulo2,0,0,'R');
			$this->Ln(5);
			$this->SetFont('Arial','B',8);
			$this->Cell(0,10,'            RECTOR(A)',0,0,'L');
			$titulo3 = '            SECRETARIO(A)';
			$w=$this->GetStringWidth($titulo3);
			$this->SetX(185-$w);
			$this->Cell($w,8,$titulo3,0,0,'R');
		}
	}

	// Variables enviadas mediante POST
	$id_paralelo = $_POST["id_paralelo"];
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$periodo_evaluacion = new periodos_evaluacion();
	$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);
	
	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();
	$direccionInstitucion = $institucion->obtenerDireccionInstitucion();
	$telefonoInstitucion = $institucion->obtenerTelefonoInstitucion();
	$nombreRector = $institucion->obtenerNombreRector();
	$nombreSecretario = $institucion->obtenerNombreSecretario();

	//Creación del objeto de la clase heredada
	$pdf=new PDF();

	$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	$pdf->nombreInstitucion = $nombreInstitucion;
	$pdf->direccionInstitucion = $direccionInstitucion;
	$pdf->telefonoInstitucion = $telefonoInstitucion;
	$pdf->nombreRector = $nombreRector;
	$pdf->nombreSecretario = $nombreSecretario;

	$db = new MySQL();
	
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',10);
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$nombre = "ESTUDIANTE: " . $nombre_completo;
			$w=$pdf->GetStringWidth($nombre);
			$pdf->Cell($w,8,$nombre,0,0,'L');
			$w=$pdf->GetStringWidth($nombreParalelo);
			$pdf->SetX(200-$w);
			$pdf->Cell($w,8,$nombreParalelo,0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(60,6,"AREAS DE ESTUDIO",1,0,'C');
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(22,6,"PRIMER PARCIAL",1,0,'C');
			$pdf->Cell(22,6,"SEGUNDO PARCIAL",1,0,'C');
			$pdf->Cell(22,6,"TERCER PARCIAL",1,0,'C');
			$pdf->Cell(16,6,"PROM. (80%)",1,0,'C');
			$pdf->Cell(12,6,"EXAMEN",1,0,'C');
			$pdf->Cell(12,6,"(20%)",1,0,'C');
			$pdf->Cell(26,6,$nombrePeriodoEvaluacion,1,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial','',6);
			$id_estudiante = $paralelo["id_estudiante"];
			// Aqui va el codigo para imprimir cada asignatura
			$asignaturas = $db->consulta("SELECT a.id_asignatura, as_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_asignatura a WHERE pa.id_paralelo = p.id_paralelo AND pa.id_asignatura = a.id_asignatura AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
			$contador_asignaturas = 0;
			$promedios[0] = 0;
			$promedios[1] = 0;
			$promedios[2] = 0;
			$promedios[3] = 0;
			while($asignatura = $db->fetch_assoc($asignaturas))
			{
				$contador_asignaturas++;
				$nombreAsignatura = substr($asignatura["as_nombre"],0,44);
				$pdf->Cell(60,6,$nombreAsignatura,1,0,'L');
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
				$num_total_registros = $db->num_rows($aporte_evaluacion);
				if($num_total_registros>0)
				{
					// Aqui calculo los promedios y desplegar en la tabla
					$contador_aportes = 0; $suma_promedios = 0; 
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
								if($total_registros>0) 
								{
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
						if($contador_aportes <= $num_total_registros - 1) 
						{
							$pdf->Cell(11,6,number_format($promedio,2),1,0,'C');
							$pdf->Cell(11,6,equiv_rendimiento($id_periodo_lectivo, $promedio),1,0,'C');
							$suma_promedios += $promedio;
							$promedios[$contador_aportes-1] += $promedio;
						} else {
							$examen_quimestral = $promedio;
						}
					}
				}
				// Aqui se calculan las calificaciones del periodo de evaluacion
				$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
				$ponderado_aportes = 0.8 * $promedio_aportes;
				$ponderado_examen = 0.2 * $examen_quimestral;
				$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
				$pdf->Cell(16,6,number_format($ponderado_aportes,2),1,0,'C');
				$pdf->Cell(12,6,number_format($examen_quimestral,2),1,0,'C');
				$pdf->Cell(12,6,number_format($ponderado_examen,2),1,0,'C');
				$pdf->Cell(13,6,number_format($calificacion_quimestral,3),1,0,'C');
				$promedios[3] += $calificacion_quimestral;
				$pdf->Cell(13,6,equiv_rendimiento($id_periodo_lectivo, $calificacion_quimestral),1,0,'C');
				$pdf->Ln();
			}
			// Promedio de rendimiento academico
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(60,6,"PROMEDIO DE RENDIMIENTO",1,0,'C');
			$promedio_aporte1 = $promedios[0] / $contador_asignaturas;
			$pdf->Cell(11,6,number_format($promedio_aporte1,3),1,0,'C');
			$pdf->Cell(11,6,equiv_rendimiento($id_periodo_lectivo, $promedio_aporte1),1,0,'C');
			$promedio_aporte2 = $promedios[1] / $contador_asignaturas;
			$pdf->Cell(11,6,number_format($promedio_aporte2,3),1,0,'C');
			$pdf->Cell(11,6,equiv_rendimiento($id_periodo_lectivo, $promedio_aporte2),1,0,'C');
			$promedio_aporte3 = $promedios[2] / $contador_asignaturas;
			$pdf->Cell(11,6,number_format($promedio_aporte3,3),1,0,'C');
			$pdf->Cell(11,6,equiv_rendimiento($id_periodo_lectivo, $promedio_aporte3),1,0,'C');
			$pdf->Cell(40,6,'',1,0,'C');
			$promedio_quimestral = $promedios[3] / $contador_asignaturas;
			$pdf->Cell(13,6,number_format($promedio_quimestral,3),1,0,'C');
			$pdf->Cell(13,6,equiv_rendimiento($id_periodo_lectivo, $promedio_quimestral),1,0,'C');
			
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(100,6,"ESCALA DE RENDIMIENTO ACADEMICO",0,0,'L');
			$pdf->Ln();
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(100,6,"ESCALA CUALITATIVA",1,0,'C');
			$pdf->Cell(30,6,"SIMBOLOGIA",1,0,'C');
			$pdf->Cell(30,6,"ESCALA CUANTITATIVA",1,0,'C');
			$pdf->Ln();
			
			// Aqui se recuperan las escalas de calificaciones de la base de datos
			
			$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
			while ($escala = $db->fetch_assoc($escala_calificacion))
			{
				$ec_cualitativa = utf8_decode($escala["ec_cualitativa"]);
				$ec_equivalencia = $escala["ec_equivalencia"];
				$ec_cuantitativa = $escala["ec_cuantitativa"];
				$pdf->Cell(100,6,$ec_cualitativa,1,0,'L');
				$pdf->Cell(30,6,$ec_equivalencia,1,0,'C');
				$pdf->Cell(30,6,$ec_cuantitativa,1,0,'C');
				$pdf->Ln();
			}
			
			$pdf->Ln(5);
			$pdf->SetFont('Arial','B',6);
			$titulo7 = "EVALUACION DEL COMPORTAMIENTO";
			$w=$pdf->GetStringWidth($titulo7);
			$pdf->SetX((210-$w)/2);
			$pdf->Cell($w,6,$titulo7,0,0,'C');
			$pdf->Ln();
			// Aqui va el codigo para imprimir el comportamiento
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(72,6,"TUTOR",1,0,'C');
			$pdf->Cell(72,6,"INSPECTOR",1,0,'C');
			$pdf->Cell(36,6,$nombrePeriodoEvaluacion,1,0,'C');
			$pdf->Ln();
			$pdf->Cell(18,6,"VALORES",1,0,'C');
			$pdf->Cell(18,6,"NORMAS",1,0,'C');
			$pdf->Cell(18,6,"PUNTUALIDAD",1,0,'C');
			$pdf->Cell(18,6,"PRESENTACION",1,0,'C');
			$pdf->Cell(18,6,"VALORES",1,0,'C');
			$pdf->Cell(18,6,"NORMAS",1,0,'C');
			$pdf->Cell(18,6,"PUNTUALIDAD",1,0,'C');
			$pdf->Cell(18,6,"PRESENTACION",1,0,'C');
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(18,6,"CUANTITATIVO",1,0,'C');
			$pdf->Cell(18,6,"CUALITATIVO",1,0,'C');
			$pdf->Ln();
			$comportamiento = $db->consulta("SELECT i.* FROM sw_comportamiento c, sw_indice_evaluacion i WHERE c.id_indice_evaluacion = i.id_indice_evaluacion AND c.id_paralelo = $id_paralelo AND c.id_estudiante = $id_estudiante AND c.id_periodo_evaluacion = $id_periodo_evaluacion");
			$total_indices = $db->num_rows($comportamiento);
			if($total_indices>0)
			{
				$indice = $db->fetch_assoc($comportamiento);
				$pdf->Cell(18,6,number_format($indice["valores_t"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["cum_norma_t"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["pun_asiste_t"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["presentacion_t"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["valores_i"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["cum_norma_i"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["pun_asiste_i"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["presentacion_i"],2),1,0,'C');
				$pdf->Cell(18,6,number_format($indice["promedio"],2),1,0,'C');
				$pdf->Cell(18,6,$indice["equivalencia"],1,0,'C');
			}
			$pdf->Ln();
		}
	
	} else {
		$pdf->Cell(100,10,"No se han matriculado estudiantes en este paralelo...",0,0,'L');
	}
	$pdf->Output();
?>
