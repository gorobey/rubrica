<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.periodos_evaluacion.php');

	// Variables enviadas mediante POST
	$id_asignatura = $_POST["id_asignatura"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];

	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	$usuario = new usuarios();
	$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

	$asignatura = new asignaturas();
	$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$periodo_evaluacion = new periodos_evaluacion();
	$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$w=$pdf->GetStringWidth($nombreInstitucion);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombreInstitucion,0,0,'C');
	$pdf->Ln(7);
	$pdf->SetFont('Arial','B',12);
	$title2="REPORTE DEL ".$nombrePeriodoEvaluacion;
	$w=$pdf->GetStringWidth($title2);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,9,$title2,0,0,'C');
	$pdf->Ln(5);
	$w=$pdf->GetStringWidth($nombrePeriodoLectivo);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombrePeriodoLectivo,0,0,'C');	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,10,"ASIGNATURA: ".$nombreAsignatura,0,0);
	$pdf->Ln(5);
	$pdf->Cell(20,10,"CURSO: ".$nombreParalelo,0,0);
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(84,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada aporte de evaluacion
	$pdf->Cell(10,6,"1ERP",1,0,'C');
	$pdf->Cell(10,6,"2DOP",1,0,'C');
	$pdf->Cell(10,6,"3ERP",1,0,'C');
	$pdf->Cell(10,6,"PROM",1,0,'C');
	$pdf->Cell(10,6,"80%",1,0,'C');
	$pdf->Cell(10,6,"EXAM",1,0,'C');
	$pdf->Cell(10,6,"20%",1,0,'C');
	$pdf->Cell(10,6,"CALIF",1,0,'C');
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(84,6,$nombre_completo,1,0,'L');
			// Consulta de las calificaciones correspondientes al periodo de evaluacion
			$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
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
							$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$paralelo["id_estudiante"]." AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
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
					$promedio = $suma_rubricas / $contador_rubricas;
					if($contador_aportes <= $num_total_registros - 1)
					{
						$pdf->Cell(10,6,number_format($promedio,2),1,0,'C');
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
				$pdf->Cell(10,6,number_format($promedio_aportes,2),1,0,'C');
				$pdf->Cell(10,6,number_format($ponderado_aportes,2),1,0,'C');
				$pdf->Cell(10,6,number_format($examen_quimestral,2),1,0,'C');
				$pdf->Cell(10,6,number_format($ponderado_examen,2),1,0,'C');
				$pdf->Cell(10,6,number_format($calificacion_quimestral,2),1,0,'C');
			}
			$pdf->Ln();
		}
	}
	$pdf->Ln(4);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)_________________________________",0,0,'L');
	$pdf->Output();
?>
