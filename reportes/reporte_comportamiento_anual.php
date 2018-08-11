<?php
	require('../fpdf16/fpdf.php');
	require('../scripts/clases/class.mysql.php');
	require('../scripts/clases/class.usuarios.php');
	require('../scripts/clases/class.paralelos.php');
	require('../scripts/clases/class.institucion.php');
	require('../scripts/clases/class.periodos_lectivos.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
		var $nombreInstitucion = "";
		var $nombrePeriodoLectivo = "";
		
		//Cabecera de pagina
		function Header()
		{
			$this->SetFont('Arial','B',16);
			$w=$this->GetStringWidth($this->nombreInstitucion);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$this->nombreInstitucion,0,0,'C');
			$this->Ln(5);
			$this->SetFont('Arial','B',12);
			$title2="CALIFICACION DEL COMPORTAMIENTO DEL PERIODO LECTIVO";
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
			//Numero de pagina
			$this->Cell(0,10,'PAGINA '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}

	// Variables enviadas mediante POST
	$id_paralelo = $_POST["id_paralelo"];
	
	session_start();
	$id_usuario = $_SESSION["id_usuario"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

    //Obtener el nombre de la institución
    $institucion = new institucion();
	$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());
	
	$usuario = new usuarios();
	$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	//Creacion del objeto de la clase heredada
	$pdf=new PDF();

	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombreInstitucion = $nombreInstitucion;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,6," ",0,0,'C');
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(80,6,"NOMINA",1,0,'C');
	$pdf->Cell(20,6,"1ER.Q.",1,0,'C');
	$pdf->Cell(20,6,"2DO.Q.",1,0,'C');
	$pdf->Cell(20,6,"ANUAL",1,0,'C');
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
			if($contador % 40 == 0) {
				$pdf->AddPage(); 
				$pdf->Ln(10);
				$pdf->Cell(20,6," ",0,0,'C');
				$pdf->Cell(8,6,"Nro.",1,0,'C');
				$pdf->Cell(80,6,"NOMINA",1,0,'C');
				$pdf->Cell(20,6,"1ER.Q.",1,0,'C');
				$pdf->Cell(20,6,"2DO.Q.",1,0,'C');
				$pdf->Cell(20,6,"ANUAL",1,0,'C');
				$pdf->Ln();
			}

			$pdf->Cell(20,6," ",0,0,'C');
			$pdf->Cell(8,6,$contador,1,0,'C');
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$pdf->Cell(80,6,$nombre_completo,1,0,'L');
			
			// Aqui va el codigo para determinar el total, el promedio y la equivalencia de cada quimestre
			
			$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = $db->num_rows($periodo_evaluacion);
			if($num_total_registros > 0)
			{
				$suma_anual = 0;
                                
				while($periodo = $db->fetch_assoc($periodo_evaluacion))
				{
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

					$resultado = $db->consulta("SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio");

					#echo "SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio<br>";

					$registro = $db->fetch_assoc($resultado);
					$promedio = ceil($registro["promedio"]);
					$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
					$equivalencia = $db->fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];

                    $pdf->Cell(20,6,$promedio_cualitativo,1,0,'C');

					$suma_anual = $suma_anual + $promedio;
				}
                                
			}
                        
			$promedio_anual = ceil($suma_anual / $num_total_registros);
			
			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_anual");
			$equivalencia = $db->fetch_assoc($query);
			$promedio_cualitativo = $equivalencia["ec_equivalencia"];

            $pdf->Cell(20,6,$promedio_cualitativo,1,0,'C');
			$pdf->Ln();
		} 
	}

	$pdf->Ln(2);
	$pdf->Cell(20,6," ",0,0,'C');
	$pdf->Cell(18,6,"Inspector: ",0,0,'L');
	$pdf->Cell(90,6,$nombreUsuario,0,0,'L');
	$pdf->Cell(25,6,"f.)________________________",0,0,'L');
	$pdf->Output();
?>
