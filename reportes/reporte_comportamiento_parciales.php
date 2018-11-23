<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.paralelos.php');
    require('../scripts/clases/class.institucion.php');
	require('../scripts/clases/class.periodos_lectivos.php');
	require('../scripts/clases/class.aportes_evaluacion.php');
    require('../scripts/clases/class.inspectores.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
        var $nombreInstitucion = "";
        var $nombrePeriodoLectivo = "";
        var $nombreAporteEvaluacion = "";
		
		//Cabecera de pagina
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$title1=$this->nombreInstitucion;
			$w=$this->GetStringWidth($title1);
			$this->SetX((298-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="CALIFICACION DEL COMPORTAMIENTO DEL ".$this->nombreAporteEvaluacion;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="CURSO: ".$this->nombreParalelo. " (" .$this->nombrePeriodoLectivo. ")";
			$w=$this->GetStringWidth($title3);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
		}
		
		//Pie de pagina
		function Footer()
		{
			//Posicion: a 1,5 cm del final
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//Numero de pagina
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}

	// Variables enviadas mediante POST
	$id_paralelo = $_POST["id_paralelo"];
	$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
        
    //Obtener el nombre de la institución
    $institucion = new institucion();
    $nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$aporte_evaluacion = new aportes_evaluacion();
	$nombreAporteEvaluacion = $aporte_evaluacion->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);
	
	//Creacion del objeto de la clase heredada
	$pdf=new PDF('L');

	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombreInstitucion = $nombreInstitucion;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	$pdf->nombreAporteEvaluacion = $nombreAporteEvaluacion;		

	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetX(81);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(74,6,"NOMINA",1,0,'C');
	
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
			if($contador % 25 == 0) {
				$pdf->AddPage(); 
				$pdf->Ln(10);
				$pdf->SetX(81);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(74,6,"NOMINA",1,0,'C');
				
				// Aqui imprimo las cabeceras de cada asignatura
				$db = new MySQL();
				
				$pdf->Cell(18,6,"DOCENTES",1,0,'C');
				$pdf->Cell(18,6,"INSPECTOR",1,0,'C');
				$pdf->Cell(18,6,"PROMEDIO",1,0,'C');
				$pdf->Ln();
			}

			$pdf->SetX(81);
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(74,6,$nombre_completo,1,0,'L');

			// Aqui se calcula el promedio del comportamiento asignado por los docentes

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
					$calificaciones = $db->consulta("SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
					if($db->num_rows($calificaciones) > 0) {
						$calificaciones = $db->fetch_assoc($calificaciones);
						$calificacion = $calificaciones["co_calificacion"];
					} else 
						$calificacion = 0;
					$suma_comp_asignatura += $calificacion;
				}
						
				//Aqui despliego el promedio del comportamiento asentado por los docentes
                $promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
				//Aqui despliego el promedio del comportamiento asentado por los docentes
				//Primero obtengo la equivalencia del promedio en forma cualitativa
				$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
				$resultado = $db->fetch_assoc($query);
				$comp_docentes = $resultado["ec_equivalencia"];
				$pdf->Cell(18,6,$comp_docentes,1,0,'C');
                                
                //Aqui obtengo el comportamiento del inspector
				$query = $db->consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
				$inspectores = $db->fetch_assoc($query);
				$promedio_inspector = $inspectores["co_calificacion"];
				if ($promedio_inspector=='') {
					$promedio_inspector = 'S/N';
					$promedio_cuantitativo = 0;
				} else {
					$query = $db->consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
					$equivalencia = $db->fetch_assoc($query);
					$promedio_cuantitativo = $equivalencia["ec_correlativa"];
				}
                //Aqui despliego el promedio del comportamiento asentado por los inspectores
                $pdf->Cell(18,6,$promedio_inspector,1,0,'C');
					
            }

			$total = $promedio_comp + $promedio_cuantitativo;
			$promedio = ceil($total / 2);

			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
			$equivalencia = $db->fetch_assoc($query);
			$promedio_cualitativo = $equivalencia["ec_equivalencia"];
			$pdf->Cell(18,6,$promedio_cualitativo,1,0,'C');
			
			$pdf->Ln();
		}
	}
	$pdf->Output();
?>
