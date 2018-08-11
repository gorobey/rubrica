<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.clubes.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.aportes_evaluacion.php');

	// Variables enviadas mediante POST
	$id_club = $_POST["id_club"];
	$id_paralelo = $_POST["id_paralelo"];
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
	$title2="LISTAS DEL ".$nombreAporteEvaluacion;
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
	$pdf->Cell(20,10,"PROYECTO: ".$nombreClub,0,0);
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->Cell(10,6,"Nro.",1,0,'C');
	$pdf->Cell(90,6,"Nómina",1,0,'C');
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
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, ec.id_club, e.es_apellidos, e.es_nombres, ep.es_retirado, cl_nombre FROM sw_estudiante_club ec, sw_estudiante e, sw_club c, sw_estudiante_periodo_lectivo ep WHERE ec.id_club = c.id_club AND ec.id_estudiante = e.id_estudiante AND e.id_estudiante = ep.id_estudiante AND ec.id_club = $id_club AND ec.id_periodo_lectivo = $id_periodo_lectivo AND ep.id_periodo_lectivo = $id_periodo_lectivo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(10,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(90,6,$nombre_completo,1,0,'L');
			// Ahora solamente imprimo las casillas en blanco
			$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
			$num_total_registros = $db->num_rows($rubrica_evaluacion);
			if($num_total_registros>0)
			{
				while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
				{
					$pdf->Cell(14,6,' ',1,0,'C');
				}
				$pdf->Cell(14,6,' ',1,0,'C');
			}
			$pdf->Ln();
		}
	}
	$pdf->Ln(2);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)___________________________",0,0,'L');
	$pdf->Output();
?>
