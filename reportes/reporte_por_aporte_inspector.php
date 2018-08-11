<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.usuarios.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.aportes_evaluacion.php');

	// Variables enviadas mediante POST
	$id_paralelo = $_POST["id_paralelo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_usuario = $_SESSION["id_usuario"];

	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();
	
	$usuario = new usuarios();
	$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

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
	$pdf->Cell(20,10,"CURSO: ".$nombreParalelo." (".$nombrePeriodoLectivo.")",0,0);
	// Impresion de los titulos de cabecera
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',10);
	$pdf->Cell(10,6,"Nro.",1,0,'C');
    $pdf->Cell(90,6,"NOMINA",1,0,'C');
    $pdf->Cell(36,6,"COMPORTAMIENTO",1,0,'C');
    $pdf->Cell(36,6,"DESCRIPCION",1,0,'C');
    $pdf->Ln();
    $db = new MySQL();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, 
                                      e.es_apellidos, 
                                      e.es_nombres 
                                 FROM sw_estudiante_periodo_lectivo ep, 
                                      sw_estudiante e 
                                WHERE ep.id_estudiante = e.id_estudiante 
                                  AND ep.id_paralelo = $id_paralelo 
                                  AND es_retirado = 'N' 
                                ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros > 0)
	{
		$contador = 0; $suma_promedios = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(10,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(90,6,$nombre_completo,1,0,'L');
            // Consulta del comportamiento de cada estudiante
            $qry = $db->consulta("SELECT co_calificacion, "
                               . "       id_escala_comportamiento "
                               . "  FROM sw_comportamiento_inspector "
                               . " WHERE id_estudiante = " . $paralelo["id_estudiante"]
                               . "   AND id_paralelo = " . $id_paralelo
                               . "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
            $num_total_registros = $db->num_rows($qry);
            $comportamiento_estudiante = $db->fetch_assoc($qry);
            if($num_total_registros > 0) {
                $calificacion = $comportamiento_estudiante["co_calificacion"];
                $id_escala_comportamiento = $comportamiento_estudiante["id_escala_comportamiento"];
            } else {
                $calificacion = "";
                $id_escala_comportamiento = 0;
            }
            $pdf->Cell(36,6,$calificacion,1,0,'C');
            $qry = $db->consulta("SELECT ec_relacion FROM sw_escala_comportamiento WHERE id_escala_comportamiento = $id_escala_comportamiento");
            if($db->num_rows($qry) > 0) {
                $escala_comportamiento = $db->fetch_assoc($qry);
                $descripcion = $escala_comportamiento["ec_relacion"];
            } else {
                $descripcion = "";
            }
			$pdf->Cell(36,6,$descripcion,1,0,'L');
			
			$pdf->Ln();
		}
	}

	$pdf->Ln(2);
	$pdf->Cell(18,6,"Inspector: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)___________________________",0,0,'L');
	$pdf->Output();
?>
