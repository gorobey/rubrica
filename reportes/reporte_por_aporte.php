<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.aportes_evaluacion.php');

	// Variables enviadas mediante POST
	$id_asignatura = $_POST["id_asignatura"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	
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

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$aporte_evaluacion = new aportes_evaluacion();
	$nombreAporteEvaluacion = $aporte_evaluacion->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);
	
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$w=$pdf->GetStringWidth($nombreInstitucion);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombreInstitucion,0,0,'C');
	$pdf->Ln(7);
	$pdf->SetFont('Arial','B',14);
	$title2="REPORTE DEL ".$nombreAporteEvaluacion;
	$w=$pdf->GetStringWidth($title2);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,9,$title2,0,0,'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','B',12);
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
	$pdf->Cell(10,6,"Nro.",1,0,'C');
	$pdf->Cell(90,6,"NOMINA",1,0,'C');
	// Aqui imprimo las cabeceras de cada rubrica de evaluacion
	$db = new MySQL();
	$consulta = $db->consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
	$num_total_registros = $db->num_rows($consulta);
	$calificaciones_validas = 0;
	if($num_total_registros>0)
	{
		while($titulo_rubrica = $db->fetch_assoc($consulta))
		{
			$pdf->Cell(14,6,$titulo_rubrica["ru_abreviatura"],1,0,'C');
		}
	}
	// Cabecera para el promedio de las rubricas
	$pdf->Cell(14,6,"PROM",1,0,'C');
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0; $suma_promedios = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(10,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(90,6,$nombre_completo,1,0,'L');
			// Consulta de las calificaciones correspondientes al aporte de evaluacion
			$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
			$num_total_registros = $db->num_rows($rubrica_evaluacion);
			if($num_total_registros>0)
			{
				$suma_rubricas = 0; $contador_rubricas = 0;
				while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
				{
					$contador_rubricas++;
					$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
					$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$paralelo["id_estudiante"]." AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
					$num_total_registros = $db->num_rows($qry);
					$rubrica_estudiante = $db->fetch_assoc($qry);
					if($num_total_registros>0) {
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = " ";
					}
					$suma_rubricas += $calificacion;
					$pdf->Cell(14,6,($calificacion==0)?" ":number_format($calificacion,2),1,0,'C');
				}
				$promedio = $suma_rubricas / $contador_rubricas;
				$suma_promedios += $promedio;
				$pdf->Cell(14,6,($promedio==0)?" ":number_format($promedio,2),1,0,'C');
				if($promedio > 0) $calificaciones_validas++;
			}
			$pdf->Ln();
		}
	}
	// Impresion de la linea del promedio general
	$pdf->Cell(100 + $contador_rubricas * 14,6,'PROMEDIO GENERAL: ',1,0,'R');
	$pdf->Cell(14,6,number_format($suma_promedios / $calificaciones_validas,2),1,0,'C');
	$pdf->Ln();

	$pdf->Ln(2);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)___________________________",0,0,'L');
	$pdf->Output();
?>
