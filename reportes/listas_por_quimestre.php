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
	$w=$pdf->GetStringWidth($nombrePeriodoLectivo);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombrePeriodoLectivo,0,0,'C');
	$pdf->Ln(7);
	$title2="LISTAS DEL ".$nombrePeriodoEvaluacion;
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
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Cell(10,6," ",1,0,'C');
			$pdf->Ln();
		}
	}
	$pdf->Ln(4);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)_________________________________",0,0,'L');
	$pdf->Output();
?>
