<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.clubes.php');
	require('../scripts/clases/class.institucion.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.periodos_evaluacion.php');
	require('../scripts/clases/class.aportes_evaluacion.php');

	class PDF extends FPDF
	{
		var $nombreClub = "";
		var $nombreInstitucion = "";
		var $nombrePeriodoLectivo = "";
		var $nombrePeriodoEvaluacion = "";
		var $nombreAporteEvaluacion = "";
		
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
			$title2="REPORTE CONSOLIDADO DEL ".$this->nombreAporteEvaluacion;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);

			$title3="CLUB: ".$this->nombreClub. " (" .$this->nombrePeriodoLectivo. ")";
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
			//Número de página
			$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
	
	ini_set('max_execution_time', 360);
	set_time_limit(0); // Para procesos muy grandes que requieren mayor tiempo de conexión
	
	// Variables enviadas mediante POST
	$id_club = $_POST["id_club"]; 
	$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"]; 
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$club = new clubes();
	$nombreClub = $club->obtenerNombreClub($id_club);

	$periodo_evaluacion = new periodos_evaluacion();
	$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

	$aporte_evaluacion = new aportes_evaluacion();
	$nombreAporteEvaluacion = $aporte_evaluacion->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	//Creación del objeto de la clase heredada
	$pdf=new PDF('L');

	$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
	$pdf->nombreAporteEvaluacion = $nombreAporteEvaluacion;
	$pdf->nombreClub = $nombreClub;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	
	$pdf->nombreInstitucion = $nombreInstitucion;

	$pdf->AliasNbPages();
	$pdf->AddPage();
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(75,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada rúbrica
	$db = new MySQL();
	$rubricas = $db->consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
	$num_rubricas = $db->num_rows($rubricas);
	while($titulo_rubrica = $db->fetch_assoc($rubricas))
		$pdf->Cell(13,6,$titulo_rubrica["ru_abreviatura"],1,0,'C');
	$pdf->Cell(13,6,"PROM.",1,0,'C');
	$pdf->Ln();

	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, ec.id_club, e.es_apellidos, e.es_nombres, cl_nombre FROM sw_estudiante_club ec, sw_estudiante e, sw_club c WHERE ec.id_club = c.id_club AND ec.id_estudiante = e.id_estudiante AND ec.id_club = $id_club AND ec.id_periodo_lectivo = $id_periodo_lectivo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_estudiantes = $db->num_rows($consulta);
	$suma_promedios_estudiantes = 0;
	$calificaciones_validas = 0;
	if($num_total_estudiantes > 0)
	{ 
		$contador = 0;
		while($club = $db->fetch_assoc($consulta))
		{
			$id_estudiante = $club["id_estudiante"];
			$suma_rubricas = 0;
			
			$contador++;
			if($contador % 25 == 0) {
				$pdf->AddPage(); 
				$pdf->Ln(10);
				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(75,6,"Nómina",1,0,'C');
				// Aqui imprimo las cabeceras de cada rubrica
				$db = new MySQL();
				$rubricas = $db->consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");	
				while($titulo_rubrica = $db->fetch_assoc($rubricas))
					$pdf->Cell(13,6,$titulo_rubrica["ru_abreviatura"],1,0,'C');
				$pdf->Cell(13,6,"PROM.",1,0,'C');
				$pdf->Ln();
			}
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($club["es_apellidos"])." ".utf8_decode($club["es_nombres"]);
			$pdf->Cell(75,6,$nombre_completo,1,0,'L');

			$rubricas = $db->consulta("SELECT id_rubrica_evaluacion, ap_tipo, ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND r.id_aporte_evaluacion = $id_aporte_evaluacion");
			$total_rubricas = $db->num_rows($rubricas);
			if($total_rubricas>0)
			{ 
				$contador_rubricas = 0;
				
				while ($rubrica = $db->fetch_assoc($rubricas))
				{ 
					$contador_rubricas++;
					// Aqui proceso los promedios de cada rúbrica
					$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];
					
					// Aca voy a llamar a una funcion almacenada que calcula el promedio del parcial de la asignatura
					
					$query = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_club WHERE id_estudiante = $id_estudiante AND id_club = $id_club AND id_rubrica_evaluacion = $id_rubrica_evaluacion");
					$num_total_registros = $db->num_rows($query);
					$rubrica_estudiante = $db->fetch_assoc($query);
					if($num_total_registros>0) {
						$calificacion = $rubrica_estudiante["rc_calificacion"];
					} else {
						$calificacion = 0;
					}
					$suma_rubricas += $calificacion;
					$pdf->Cell(13,6,number_format($calificacion,2),1,0,'C');
				}
					
				$promedio_rubrica = $suma_rubricas / $total_rubricas;
				
				if($promedio_rubrica > 0) $calificaciones_validas++;
				
				$pdf->Cell(13,6,number_format($promedio_rubrica,2),1,0,'C');
				$suma_promedios_estudiantes += $promedio_rubrica;
					
			}
			
			$pdf->Ln();
		}
	}
	// Impresion de la linea del promedio general
	$pdf->Cell(83 + $num_rubricas * 13,6,'PROMEDIO GENERAL: ',1,0,'R');
	$pdf->Cell(13,6,number_format($suma_promedios_estudiantes / $calificaciones_validas,2),1,0,'C'); 
	$pdf->Output();
?>
