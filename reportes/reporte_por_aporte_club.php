<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.clubes.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.aportes_evaluacion.php');

	// Variables enviadas mediante POST
	$id_club = $_POST["id_club"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];

	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	$usuario = new usuarios();
	$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

	$club = new clubes();
	$nombreClub = $club->obtenerNombreClub($id_club);

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
	$w=$pdf->GetStringWidth($nombreClub);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombreClub,0,0,'C');
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(10,6,"Nro.",1,0,'C');
	$pdf->Cell(80,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada rubrica de evaluacion
	$db = new MySQL();
	$consulta = $db->consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		while($titulo_rubrica = $db->fetch_assoc($consulta))
		{
			$pdf->Cell(14,6,$titulo_rubrica["ru_abreviatura"],1,0,'C');
		}
	}
	// Cabecera para el promedio de las rubricas
	$pdf->Cell(14,6,"PROM",1,0,'C');
	$pdf->Cell(14,6,"EQUIV",1,0,'C');
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante e, sw_estudiante_club c WHERE e.id_estudiante = c.id_estudiante AND c.id_club = $id_club ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(10,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(80,6,$nombre_completo,1,0,'L');
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
					$qry = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_club WHERE id_estudiante = ".$paralelo["id_estudiante"]." AND id_club = $id_club AND id_rubrica_evaluacion = ".$id_rubrica_evaluacion);
					$num_total_registros = $db->num_rows($qry);
					$rubrica_estudiante = $db->fetch_assoc($qry);
					if($num_total_registros>0) {
						$calificacion = $rubrica_estudiante["rc_calificacion"];
					} else {
						$calificacion = 0;
					}
					$suma_rubricas += $calificacion;
					$pdf->Cell(14,6,number_format($calificacion,2),1,0,'C');
				}
				$promedio = $suma_rubricas / $contador_rubricas;
				$pdf->Cell(14,6,number_format($promedio,2),1,0,'C');
				$pdf->Cell(14,6,$club->equivalencia_proyectos(number_format($promedio,2),$id_periodo_lectivo),1,0,'C');
			}
			$pdf->Ln();
		}
	}
	$pdf->Ln(2);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(80,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)___________________________",0,0,'L');
	$pdf->Output();
?>
