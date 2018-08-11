<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.periodos_evaluacion.php');

	class PDF extends FPDF
	{
		var $nombrePeriodoEvaluacion = "";
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		
		//Cabecera de página
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$title1="COLEGIO NACIONAL NOCTURNO SALAMANCA";
			$w=$this->GetStringWidth($title1);
			$this->SetX((298-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="REPORTE DE CALIFICACIONES ERRONEAS DEL ".$this->nombrePeriodoEvaluacion;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="AÑO LECTIVO: ".$this->nombrePeriodoLectivo;
			$w=$this->GetStringWidth($title3);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
		}

		//Pie de página
		function Footer()
		{
			//Posición: a 3 cm del final
			$this->SetY(-30);
			//Arial italic 8
			$this->SetFont('Arial','',8);
			//Aqui van la firma del docente
			$raya = '_________________________________';
			$w=$this->GetStringWidth($raya);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$raya,0,0,'C');
			
			$this->Ln(5);

			$mensaje = 'FIRMA DEL DOCENTE';
			$w=$this->GetStringWidth($mensaje);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$mensaje,0,0,'C');

		}
	}

	// Variables enviadas mediante POST
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$periodo_evaluacion = new periodos_evaluacion();
	$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	//Creación del objeto de la clase heredada
	$pdf=new PDF('L');

	$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

	$pdf->AliasNbPages();
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$consulta = $db->consulta("SELECT as_nombre, es_apellidos, es_nombres, us_titulo, us_fullname, cu_nombre, pa_nombre, ap_nombre, ru_nombre, re_calificacion FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e, sw_paralelo_asignatura pa, sw_usuario u, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru, sw_curso cu, sw_paralelo p WHERE r.id_paralelo = pa.id_paralelo AND pa.id_paralelo = p.id_paralelo AND p.id_curso = cu.id_curso AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pe.id_periodo_evaluacion = $id_periodo_evaluacion AND (re_calificacion > 10 OR re_calificacion < 0) ORDER BY us_fullname, as_nombre");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($calificacion = $db->fetch_assoc($consulta))
		{
		
			$pdf->AddPage();
			$pdf->Ln(10);
			$mensaje = "Estimado compañero docente, por favor corregir la calificación mal ingresada al sistema, que se detalla a continuación.";
			$pdf->SetFont('Arial','',12);
			$w=$pdf->GetStringWidth($mensaje);
			$pdf->SetX((298-$w)/2);
			$pdf->Cell($w,10,$mensaje,0,0,'C');

			// Impresion de los titulos de cabecera
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',12);
			$docente = utf8_decode($calificacion["us_titulo"] . " " . $calificacion["us_fullname"]);
			$w=$pdf->GetStringWidth($docente);
			$pdf->SetX((298-$w)/2);
			$pdf->Cell($w,10,$docente,0,0,'C');

			$pdf->Ln(10);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(64,6,"ASIGNATURA",1,0,'C');
			$pdf->Cell(64,6,"ESTUDIANTE",1,0,'C');
			$pdf->Cell(50,6,"CURSO",1,0,'C');
			$pdf->Cell(30,6,"APORTE",1,0,'C');
			$pdf->Cell(42,6,"RUBRICA",1,0,'C');
			$pdf->Ln();

			$asignatura = utf8_decode($calificacion["as_nombre"]);
			$pdf->Cell(64,6,$asignatura,1,0,'L');
			
			$estudiante = utf8_decode($calificacion["es_apellidos"] . " " . $calificacion["es_nombres"]);
			$pdf->Cell(64,6,$estudiante,1,0,'L');

			$curso = utf8_decode($calificacion["cu_nombre"] . " \"". $calificacion["pa_nombre"] . "\"");
			$pdf->Cell(50,6,$curso,1,0,'L');
			
			$aporte = $calificacion["ap_nombre"];
			$pdf->Cell(30,6,$aporte,1,0,'L');

			$rubrica = $calificacion["ru_nombre"];
			$pdf->Cell(42,6,$rubrica,1,0,'L');
			
			$pdf->Ln();
			
			$pdf->SetFont('Arial','B',12);
			$nota_errada = "CALIFICACION A CORREGIR: " . $calificacion["re_calificacion"];
			$w=$pdf->GetStringWidth($nota_errada);
			$pdf->SetX((298-$w)/2);
			$pdf->Cell($w,10,$nota_errada,0,0,'C');

		}
	}
	$pdf->Output();
?>
