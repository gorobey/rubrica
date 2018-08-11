<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.paralelos.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.periodos_evaluacion.php');

	class PDF extends FPDF
	{
		var $nombrePeriodoEvaluacion = "";
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		
		//Cabecera de pagina
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$title1="COLEGIO NACIONAL NOCTURNO SALAMANCA";
			$w=$this->GetStringWidth($title1);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="CALIFICACION DEL COMPORTAMIENTO DEL ".$this->nombrePeriodoEvaluacion;
			$w=$this->GetStringWidth($title2);
			$this->SetX((210-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="CURSO: ".$this->nombreParalelo. " (" .$this->nombrePeriodoLectivo. ")";
			$w=$this->GetStringWidth($title3);
			$this->SetX((210-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
		}
		
		//Pie de pagina
		function Footer()
		{
			//Posicion: a 1,5 cm del final
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//Fecha de Impresion
			$this->Cell(0,10,date('l jS \of F Y h:i:s A'),0,0,'L');
			//Numero de pagina
			$this->Cell(0,10,'PAG. '.$this->PageNo().'/{nb}',0,0,'R');
		}
	}

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
	
	//Creacion del objeto de la clase heredada
	$pdf = new PDF();

	//Establecemos los márgenes izquierda, arriba y derecha: 
	$pdf->SetMargins(38, 17 , 30); 

	//Establecemos el margen inferior: 
	//$pdf->SetAutoPageBreak(true, 15);

	$pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(72,6,"NOMINA",1,0,'C');
	
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();

	$pdf->Cell(18,6,"DOCENTES",1,0,'C');
	$pdf->Cell(18,6,"INSPECTOR",1,0,'C');
	$pdf->Cell(18,6,"PROMEDIO",1,0,'C');

	// Aqui van las cabeceras de las calificaciones del comportamiento
	$pdf->Ln();
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$id_estudiante = $paralelo["id_estudiante"];
			
			$contador++;
			if($contador % 38 == 0) {
				$pdf->AddPage(); 
				$pdf->Ln(10);
				$pdf->SetFont('Arial','',8);

				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(72,6,"NOMINA",1,0,'C');
				
				// Aqui imprimo las cabeceras de cada asignatura
				$db = new MySQL();

				$pdf->Cell(18,6,"DOCENTES",1,0,'C');
				$pdf->Cell(18,6,"INSPECTOR",1,0,'C');
				$pdf->Cell(18,6,"PROMEDIO",1,0,'C');

				$pdf->Ln();
			}
			
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(72,6,$nombre_completo,1,0,'L');

			// Aqui se consultan los indices de evaluacion para el comportamiento

			$asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
			$total_asignaturas = $db->num_rows($asignaturas);
				
			if ($total_asignaturas > 0)
			{
				$suma_comp_asignatura = 0;
				$contador_asignaturas = 0;
					
				while($asignatura = $db->fetch_assoc($asignaturas))
					{
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];
								
						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
						$calificaciones = $db->fetch_assoc($calificaciones);
						$calificacion = ceil($calificaciones["calificacion"]);

						$suma_comp_asignatura += $calificacion;
					}
							
					$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
					$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
					$equivalencia = $db->fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$pdf->Cell(18,6,$promedio_cualitativo,1,0,'C');
			}

			//Aqui viene el calculo del comportamiento por parte del inspector
			$aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND ap_tipo = 1");

			$suma_comportamiento_inspector = 0; $contador_aportes_evaluacion = 0;
			while($aporte_evaluacion = $db->fetch_assoc($aportes_evaluacion))
			{
				$contador_aportes_evaluacion++;
				$id_aporte_evaluacion = $aporte_evaluacion["id_aporte_evaluacion"];
				$query = $db->consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
				$inspectores = $db->fetch_assoc($query);
				$promedio_inspector = $inspectores["co_calificacion"];
				if ($promedio_inspector=="") {
					$promedio_cuantitativo = 0;
				} else {
					$query = $db->consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
					$equivalencia = $db->fetch_assoc($query);
					$promedio_cuantitativo = $equivalencia["ec_correlativa"];
				}
				$suma_comportamiento_inspector += $promedio_cuantitativo; 
			}

			$comportamiento_inspector = ceil($suma_comportamiento_inspector / $contador_aportes_evaluacion);
			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $comportamiento_inspector");
			$equiv_comp_inspector = $db->fetch_assoc($query);
			$pdf->Cell(18,6,$equiv_comp_inspector["ec_equivalencia"],1,0,'C');

			$promedio_comportamiento = ceil(($promedio_comp + $comportamiento_inspector) / 2.0);
			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
			$equiv_comp_promedio = $db->fetch_assoc($query);
			$pdf->Cell(18,6,$equiv_comp_promedio["ec_equivalencia"],1,0,'C');
			
			$pdf->Ln();
		}
	}
	$pdf->Output();
?>
