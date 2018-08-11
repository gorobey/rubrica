<?php
	require('../fpdf16/fpdf.php');
	require_once("../funciones/funciones_sitio.php");
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.paralelos.php');
        require('../scripts/clases/class.comportamientos.php');
	require('../scripts/clases/class.periodos_lectivos.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		
		//Cabecera de p�gina
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$title1="COLEGIO NACIONAL NOCTURNO SALAMANCA";
			$w=$this->GetStringWidth($title1);
			$this->SetX((298-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="CALIFICACION DEL COMPORTAMIENTO DEL PERIODO LECTIVO";
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="CURSO: ".$this->nombreParalelo. " (" .$this->nombrePeriodoLectivo. ")";
			$w=$this->GetStringWidth($title3);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
		}
		
		//Pie de p�gina
		function Footer()
		{
			//Posici�n: a 1,5 cm del final
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//N�mero de p�gina
			$this->Cell(0,10,'PAGINA '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}

	// Variables enviadas mediante POST
	$id_paralelo = $_POST["id_paralelo"];
	
	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	//Creaci�n del objeto de la clase heredada
	$pdf=new PDF('L');

	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(88,6,"",0,0,'C');
	$pdf->Cell(60,6,"PRIMER QUIMESTRE",1,0,'C');
	$pdf->Cell(60,6,"SEGUNDO QUIMESTRE",1,0,'C');
	$pdf->Cell(60,6,"PROMEDIOS ANUALES",1,0,'C');
	$pdf->Ln();
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(80,6,"NOMINA",1,0,'C');
	$pdf->Cell(20,6,"TOTAL",1,0,'C');
	$pdf->Cell(20,6,"PROME",1,0,'C');
	$pdf->Cell(20,6,"EQUIV",1,0,'C');
	$pdf->Cell(20,6,"TOTAL",1,0,'C');
	$pdf->Cell(20,6,"PROME",1,0,'C');
	$pdf->Cell(20,6,"EQUIV",1,0,'C');
	$pdf->Cell(20,6,"TOTAL",1,0,'C');
	$pdf->Cell(20,6,"PROME",1,0,'C');
	$pdf->Cell(20,6,"EQUIV",1,0,'C');
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
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(88,6,"",0,0,'C');
				$pdf->Cell(60,6,"PRIMER QUIMESTRE",1,0,'C');
				$pdf->Cell(60,6,"SEGUNDO QUIMESTRE",1,0,'C');
				$pdf->Cell(60,6,"PROMEDIOS ANUALES",1,0,'C');
				$pdf->Ln();
				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(80,6,"NOMINA",1,0,'C');
				$pdf->Cell(20,6,"TOTAL",1,0,'C');
				$pdf->Cell(20,6,"PROME",1,0,'C');
				$pdf->Cell(20,6,"EQUIV",1,0,'C');
				$pdf->Cell(20,6,"TOTAL",1,0,'C');
				$pdf->Cell(20,6,"PROME",1,0,'C');
				$pdf->Cell(20,6,"EQUIV",1,0,'C');
				$pdf->Cell(20,6,"TOTAL",1,0,'C');
				$pdf->Cell(20,6,"PROME",1,0,'C');
				$pdf->Cell(20,6,"EQUIV",1,0,'C');
				$pdf->Ln();
			}
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(80,6,$nombre_completo,1,0,'L');
			
			// Aqui va el codigo para determinar el total, el promedio y la equivalencia de cada quimestre
			
			$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = $db->num_rows($periodo_evaluacion);
			if($num_total_registros > 0)
			{
				$suma_total = 0;
				$suma_anual = 0;
                                $suma_promedio = 0;
                                
				while($periodo = $db->fetch_assoc($periodo_evaluacion))
				{
                                    $id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
                                    $asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
                                    $total_asignaturas = $db->num_rows($asignaturas);
                                    $suma_quimestre = 0;
                                    
                                    if ($total_asignaturas > 0)
                                    {
                                        $cont_asignaturas = 0;
                                        
                                        while($asignatura = $db->fetch_assoc($asignaturas))
                                        {
                                            $id_asignatura = $asignatura["id_asignatura"];
                                            $comportamiento = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
                                            $comp_quimestre = $db->fetch_assoc($comportamiento);
                                            $promedio = $comp_quimestre["calificacion"];  // Promedio Quimestral del comportamiento
                                            
                                            $cont_asignaturas++;
                                            $suma_quimestre += $promedio;                                            
                                            
                                        }
                                        
                                        $promedio_quimestral = $suma_quimestre / $cont_asignaturas;
                                        $pdf->Cell(20,6,number_format($suma_quimestre, 2),1,0,'C');
                                        $pdf->Cell(20,6,number_format($promedio_quimestral, 2),1,0,'C');
                                        $equivalencia = equiv_comportamiento($promedio_quimestral);
                                        $pdf->Cell(20,6,$equivalencia,1,0,'C');
                                        
                                        $suma_anual = $suma_anual + $suma_quimestre;
                                        $suma_promedio = $suma_promedio + $promedio_quimestral;
                                        
                                    }
				}
                                
			}
                        
                        $promedio_anual = $suma_promedio / $num_total_registros;
                        $pdf->Cell(20,6,number_format($suma_anual, 2),1,0,'C');
                        $pdf->Cell(20,6,number_format($promedio_anual, 2),1,0,'C');
//                        $consulta = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE $promedio_anual >= ec_nota_minima AND $promedio_anual <= ec_nota_maxima");
//                        $resultado = $db->fetch_assoc($consulta);
//                        $pdf->Cell(20,6,$resultado["ec_equivalencia"],1,0,'C');
                        $equiv_final = equiv_comportamiento($promedio_anual);
                        $pdf->Cell(20,6,$equiv_final,1,0,'C');
			$pdf->Ln();
		} 
	}
	$pdf->Output();
?>
