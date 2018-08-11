<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../scripts/clases/class.funciones.php');

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		var $nombreInstitucion = "";
		
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
			$title2="REPORTE DE EXAMENES DE GRACIA PERIODO LECTIVO ".$this->nombrePeriodoLectivo;
			$w=$this->GetStringWidth($title2);
			$this->SetX((298-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(5);
			$title3="CURSO: ".$this->nombreParalelo;
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
			$this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C');
		}
	}

	// Variables enviadas mediante POST	
	$id_paralelo = $_POST["id_paralelo"];

	session_start();
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	$periodo_lectivo = new periodos_lectivos();
	$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

	$paralelo = new paralelos();
	$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));
	
	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();

	$pdf=new PDF('L');
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	$pdf->nombreInstitucion = $nombreInstitucion;

	$pdf->AliasNbPages();
	$pdf->AddPage();

	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,6,"Nro.",1,0,'C');
	$pdf->Cell(70,6,"Nómina",1,0,'C');
	// Aqui imprimo las cabeceras de cada asignatura
	$db = new MySQL();
	$funciones = new funciones();
	$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");	
	while($titulo_asignatura = $db->fetch_assoc($asignaturas))
		$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
	$pdf->Ln();

	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0; 
		while($paralelo = $db->fetch_assoc($consulta))
		{

			$id_estudiante = $paralelo["id_estudiante"];
			$retirado = $paralelo["es_retirado"];
			
			$query = $db->consulta("SELECT contar_remediales_no_aprobados($id_periodo_lectivo, $id_estudiante, $id_paralelo) AS contador");
			$registro = $db->fetch_assoc($query);
			$c_remediales = $registro["contador"];
			
			if($c_remediales == 1) {

				$contador++;
	
				if($contador % 25 == 0) 
				{
					$pdf->AddPage(); 
					$pdf->Ln(10);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(8,6,"Nro.",1,0,'C');
					$pdf->Cell(70,6,"Nómina",1,0,'C');
					// Aqui imprimo las cabeceras de cada asignatura
					$asignaturas = $db->consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");	
					while($titulo_asignatura = $db->fetch_assoc($asignaturas))
						$pdf->Cell(13,6,$titulo_asignatura["as_abreviatura"],1,0,'C');
					$pdf->Ln();
				}
				
				$pdf->Cell(8,5,$contador,1,0,'C');
				$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
				$pdf->Cell(70,5,$nombre_completo,1,0,'L');

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$id_asignaturas = $db->consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = $db->num_rows($id_asignaturas);
				if($total_asignaturas>0)
				{

					while ($asignatura = $db->fetch_assoc($id_asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
												
						$query = $db->consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
						$registro = $db->fetch_assoc($query);
						$promedio_periodos = $registro["promedio"];

						if($promedio_periodos >= 7) {
							$pdf->Cell(13,5," ",1,0,'C');
						} else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio
							
							$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

							if($calificacion >= 7)
								$pdf->Cell(13,5," ",1,0,'C');
							else {
								// Obtencion de la calificacion del examen remedial
								$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

								if($calificacion >= 7)
									$pdf->Cell(13,5,"PASA",1,0,'C');
								else {
									// Obtencion de la calificacion del examen de gracia
									$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
									$pdf->Cell(13,5,number_format($calificacion,2),1,0,'C');	
								}	
									
							}
							
						} else if($promedio_periodos > 0 && $promedio_periodos < 5) {
							// Obtencion de la calificacion del examen remedial

							$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

							if($calificacion >= 7)
								$pdf->Cell(13,5," ",1,0,'C');	
							else {

								$calificacion = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
								
								$pdf->Cell(13,5,number_format($calificacion,2),1,0,'C');
							}
						}
					} // while ($asignatura = $db->fetch_assoc($asignaturas))
				
					$pdf->Ln();
		
				} // if($total_asignaturas>0)

			} // if($db->consulta("SELECT calcular_promedio_general($id_periodo_lectivo, $id_estudiante, $id_paralelo)") < 7)
			
		} // while($paralelo = $db->fetch_assoc($consulta))

	} // if($num_total_registros>0)

	$pdf->Output();
?>
