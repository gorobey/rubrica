<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.paralelos.php');
	require('../scripts/clases/class.institucion.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.periodos_evaluacion.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
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
			$title2="REPORTE CONSOLIDADO DEL ".$this->nombrePeriodoEvaluacion;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);

			$title3="CURSO: ".$this->nombreParalelo. " (" .$this->nombrePeriodoLectivo. ")";
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

	//Creación del objeto de la clase heredada
	$pdf=new PDF('L');

	$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	
	$pdf->nombreInstitucion = $nombreInstitucion;

	$pdf->AliasNbPages();
	$pdf->AddPage();
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(75,6,"Nómina",1,0,'C');

	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
	$num_asignaturas = $db->num_rows($asignaturas);
	while($titulo_asignatura = $db->fetch_assoc($asignaturas))
		$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
	if ($impresion_para_juntas == 0)
		$pdf->Cell(13,6,"PROM.",1,0,'C');
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_estudiantes = $db->num_rows($consulta);
	$suma_promedios_estudiantes = 0;
	if($num_total_estudiantes > 0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$id_estudiante = $paralelo["id_estudiante"];
			$suma_asignaturas = 0;
			
			$contador++;
			if($contador % 25 == 0) {
				$pdf->AddPage(); 
				$pdf->Ln(10);
				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(75,6,"Nómina",1,0,'C');
				// Aqui imprimo las cabeceras de cada asignatura
				$db = new MySQL();
				$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");	
				while($titulo_asignatura = $db->fetch_assoc($asignaturas))
					$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
				if ($impresion_para_juntas == 0)
					$pdf->Cell(13,6,"PROM.",1,0,'C');
				$pdf->Ln();
			}
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(75,6,$nombre_completo,1,0,'L');

			$asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
			$total_asignaturas = $db->num_rows($asignaturas);
			if($total_asignaturas>0)
			{
				$contador_asignaturas = 0;
				
				while ($asignatura = $db->fetch_assoc($asignaturas))
				{
					$contador_asignaturas++;
					// Aqui proceso los promedios de cada asignatura
					$id_asignatura = $asignatura["id_asignatura"];
					
					// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
					
					$query = $db->consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
					$calificacion = $db->fetch_assoc($query);
					$promedio_quimestral = $calificacion["promedio"];

					$pdf->Cell(13,6,number_format($promedio_quimestral,2),1,0,'C');
					$suma_asignaturas += $promedio_quimestral;
				}
				$promedio_asignaturas = $suma_asignaturas / $contador_asignaturas;
				$suma_promedios_estudiantes += $promedio_asignaturas;
				$pdf->Cell(13,6,number_format($promedio_asignaturas,2),1,0,'C');
			}
			
			$pdf->Ln();
		}
	}
	// Impresion de la linea del promedio general
	$pdf->Cell(83 + $num_asignaturas * 13,6,'PROMEDIO GENERAL: ',1,0,'R');
	$pdf->Cell(13,6,number_format($suma_promedios_estudiantes / $num_total_estudiantes,2),1,0,'C');
	$pdf->Output();
?>
