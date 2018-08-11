<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.usuarios.php');
        require_once('../scripts/clases/class.horas_clase.php');
	require_once('../scripts/clases/class.inasistencias.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');

	// Variables enviadas mediante POST
	$id_asignatura = $_POST["id_asignatura"];
	$id_paralelo = $_POST["id_paralelo"];
	$id_hora_clase = $_POST["id_hora_clase"];
        $ae_fecha = $_POST["ae_fecha"];
	
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

	$hora_clase = new horas_clase();
	$nombreHoraClase = $hora_clase->obtenerNombreHoraClase($id_hora_clase);
	
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$w=$pdf->GetStringWidth($nombreInstitucion);
	$pdf->SetX((210-$w)/2);
	$pdf->Cell($w,10,$nombreInstitucion,0,0,'C');
	$pdf->Ln(7);
	$pdf->SetFont('Arial','B',14);
	$title2="REPORTE DE ASISTENCIA DE ".$ae_fecha;
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
        $pdf->Ln(5);
	$pdf->Cell(20,10,"HORA CLASE: ".$nombreHoraClase,0,0);
        // Impresion de los tipos de inasistencia
        $pdf->Ln(10);
        $mensaje = "Tipos de Inasistencia:";
        $db = new MySQL();
        $consulta = $db->consulta("SELECT in_abreviatura, in_nombre FROM sw_inasistencia ORDER BY id_inasistencia");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		while($titulo = $db->fetch_assoc($consulta))
		{
			$mensaje .= " " . $titulo["in_abreviatura"] . ": " . $titulo["in_nombre"];
		}
	}
        $w=$pdf->GetStringWidth($mensaje);
        $pdf->SetX((210-$w)/2);
        $pdf->Cell($w,10,$mensaje,0,0,'C');
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->Cell(10,6,"Nro.",1,0,'C');
	$pdf->Cell(90,6,"NOMINA",1,0,'C');
	// Aqui imprimo las cabeceras de cada rubrica de evaluacion
	$consulta = $db->consulta("SELECT in_abreviatura FROM sw_inasistencia ORDER BY id_inasistencia");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		while($titulo_inasistencia = $db->fetch_assoc($consulta))
		{
			$pdf->Cell(14,6,$titulo_inasistencia["in_abreviatura"],1,0,'C');
		}
	}
        $pdf->Ln();
	// Aqui va el codigo para imprimir las inasistencias de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros > 0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$contador++;
			$pdf->Cell(10,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(90,6,$nombre_completo,1,0,'L');
                        // Por cada id_inasistencia se compara si existe el registro
                        $inasistencia_estudiante = $db->consulta("SELECT id_inasistencia FROM sw_inasistencia ORDER BY id_inasistencia");
                        $num_total_registros = $db->num_rows($consulta);
                        if($num_total_registros > 0)
                        {
                            while($inasistencia = $db->fetch_assoc($inasistencia_estudiante))
                            {
                                // Consulta de la asistencia correspondiente a la fecha y hora clase pasadas como parametros
                                $asistencia_hora_clase = $db->consulta("SELECT *"
                                                                     . "  FROM sw_asistencia_estudiante"
                                                                     . " WHERE id_estudiante = " . $paralelo["id_estudiante"]
                                                                     . "   AND id_paralelo = " . $id_paralelo
                                                                     . "   AND id_asignatura = " . $id_asignatura
                                                                     . "   AND id_hora_clase = " . $id_hora_clase
                                                                     . "   AND id_inasistencia = " . $inasistencia["id_inasistencia"]
                                                                     . "   AND ae_fecha = '" . $ae_fecha . "'");
                                $num_total_registros = $db->num_rows($asistencia_hora_clase);
                                if($num_total_registros > 0)
                                {
                                    $pdf->Cell(14,6,'*',1,0,'C');
                                }
                                else
                                {
                                    $pdf->Cell(14,6,' ',1,0,'C');
                                }
                            }
                        }
			$pdf->Ln();
		}
	}

        $pdf->Ln();

	$pdf->Ln(2);
	$pdf->Cell(10,6,"Prof.: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(30,6,"f.)___________________________",0,0,'L');
	$pdf->Output();
?>
