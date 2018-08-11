<?php
	require_once('../fpdf16/fpdf.php');
	require_once('../scripts/clases/class.mysql.php');
	require_once('../scripts/clases/class.asignaturas.php');
	require_once('../scripts/clases/class.paralelos.php');
	require_once('../scripts/clases/class.periodos_lectivos.php');
	require_once('../scripts/clases/class.especialidades.php');
	require_once('../scripts/clases/class.tipos_educacion.php');
	require_once('../scripts/clases/class.institucion.php');
	require_once('../funciones/funciones_sitio.php');
	
	function equiv_letras($promedio)
	{
		$parte_entera = substr($promedio,0,strrpos($promedio,'.'));
		$parte_decimal = substr($promedio,strrpos($promedio,'.')+1);
		$cadena = "";
		switch ($parte_entera) {
			case '0' : $cadena .= 'CERO'; break;
			case '1' : $cadena .= 'UNO'; break;
			case '2' : $cadena .= 'DOS'; break;
			case '3' : $cadena .= 'TRES'; break;
			case '4' : $cadena .= 'CUATRO'; break;
			case '5' : $cadena .= 'CINCO'; break;
			case '6' : $cadena .= 'SEIS'; break;
			case '7' : $cadena .= 'SIETE'; break;
			case '8' : $cadena .= 'OCHO'; break;
			case '9' : $cadena .= 'NUEVE'; break;
			case '10' : $cadena .= 'DIEZ'; break;
		}
		$parte_decimal_1 = substr($parte_decimal,0,1);
		$parte_decimal_2 = substr($parte_decimal,1,1);
		//$cadena .= $parte_decimal_1 . $parte_decimal_2;
		if ($parte_decimal_1 != 0 || $parte_decimal_2 != 0) {
			$cadena .= " CON ";
			if ($parte_decimal_1 == '0') {
				$cadena .= "CERO ";
				switch ($parte_decimal_2) {
					case '0' : $cadena .= 'CERO'; break;
					case '1' : $cadena .= 'UNO'; break;
					case '2' : $cadena .= 'DOS'; break;
					case '3' : $cadena .= 'TRES'; break;
					case '4' : $cadena .= 'CUATRO'; break;
					case '5' : $cadena .= 'CINCO'; break;
					case '6' : $cadena .= 'SEIS'; break;
					case '7' : $cadena .= 'SIETE'; break;
					case '8' : $cadena .= 'OCHO'; break;
					case '9' : $cadena .= 'NUEVE'; break;
				}
			} else {
				if ($parte_decimal >= 10 && $parte_decimal < 20) {
					switch ($parte_decimal) {
						case '10' : $cadena .= 'DIEZ'; break;
						case '11' : $cadena .= 'ONCE'; break;
						case '12' : $cadena .= 'DOCE'; break;
						case '13' : $cadena .= 'TRECE'; break;
						case '14' : $cadena .= 'CATORCE'; break;
						case '15' : $cadena .= 'QUINCE'; break;
						case '16' : $cadena .= 'DIECISEIS'; break;
						case '17' : $cadena .= 'DIECISIETE'; break;
						case '18' : $cadena .= 'DIECIOCHO'; break;
						case '19' : $cadena .= 'DIECINUEVE'; break;
					}
				} else if ($parte_decimal >= 20 && $parte_decimal <= 99) {
					if ($parte_decimal % 10 == 0) {
						switch ($parte_decimal / 10) {
							case '2' : $cadena .= 'VEINTE'; break;
							case '3' : $cadena .= 'TREINTA'; break;
							case '4' : $cadena .= 'CUARENTA'; break;
							case '5' : $cadena .= 'CINCUENTA'; break;
							case '6' : $cadena .= 'SESENTA'; break;
							case '7' : $cadena .= 'SETENTA'; break;
							case '8' : $cadena .= 'OCHENTA'; break;
							case '9' : $cadena .= 'NOVENTA'; break;
						}
					} else {
						switch ($parte_decimal_1) {
							case '2' : $cadena .= 'VEINTI'; break;
							case '3' : $cadena .= 'TREINTA Y '; break;
							case '4' : $cadena .= 'CUARENTA Y '; break;
							case '5' : $cadena .= 'CINCUENTA Y '; break;
							case '6' : $cadena .= 'SESENTA Y '; break;
							case '7' : $cadena .= 'SETENTA Y '; break;
							case '8' : $cadena .= 'OCHENTA Y '; break;
							case '9' : $cadena .= 'NOVENTA Y '; break;
						}
						switch ($parte_decimal_2) {
							case '1' : $cadena .= 'UNO'; break;
							case '2' : $cadena .= 'DOS'; break;
							case '3' : $cadena .= 'TRES'; break;
							case '4' : $cadena .= 'CUATRO'; break;
							case '5' : $cadena .= 'CINCO'; break;
							case '6' : $cadena .= 'SEIS'; break;
							case '7' : $cadena .= 'SIETE'; break;
							case '8' : $cadena .= 'OCHO'; break;
							case '9' : $cadena .= 'NUEVE'; break;
						}
					}
				} 
			}
		}
		return $cadena;
	}

	class PDF extends FPDF
	{
		var $nombreParalelo = "";
		var $nombrePeriodoLectivo = "";
		var $nombreInstitucion = "";
		var $nombreRector = "";
		var $nombreSecretario = "";
		
		//Cabecera de página
		function Header()
		{
			$this->Image('ecuador.png',10,8,20);
			//$this->Image('ministerio.png',168,18,32);
			//$this->Ln();
			$this->SetFont('Arial','B',16);
			$title1=$this->nombreInstitucion;
			$w=$this->GetStringWidth($title1);
			$this->SetX((210-$w)/2);
			$this->Cell($w,10,$title1,0,0,'C');
			$this->Image('salamanca.png',168,18,32);
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial','B',14);
			$title2="CERTIFICADO DE PROMOCIÓN";
			$w=$this->GetStringWidth($title2);
			$this->SetX((210-$w)/2);
			$this->Cell($w,9,$title2,0,0,'C');
			$this->Ln(7);
			$this->SetFont('Arial','B',10);
			$title3="PERIODO LECTIVO ".$this->nombrePeriodoLectivo;
			$w=$this->GetStringWidth($title3);
			$this->SetX((210-$w)/2);
			$this->Cell($w,9,$title3,0,0,'C');
			$this->Ln(3);
			$title4="JORNADA NOCTURNA";
			$w=$this->GetStringWidth($title4);
			$this->SetX((210-$w)/2);
			$this->Cell($w,9,$title4,0,0,'C');
			$this->Ln(7);
			$this->SetFont('Arial','',10);
			$txt = "De conformidad con lo prescrito en el Art. 197 del Reglamento a la Ley Orgánica de Educación Intercultural y demás normativas vigentes certifica que el/la estudiante:";
			$this->MultiCell(0,5,$txt);
		}
		
		//Pie de página
		function Footer()
		{
			//Posición: a 3 cm del final
			$this->SetY(-90);
			//Arial italic 8
			$this->SetFont('Arial','',8);
			//Aqui van las firmas de rectora y secretaria
			$this->Cell(0,10,'___________________________',0,0,'L');
			$titulo1 = '___________________________';
			$w=$this->GetStringWidth($titulo1);
			$this->SetX(195-$w);
			$this->Cell($w,8,$titulo1,0,0,'R');
			$this->Ln(5);
			$this->Cell(0,10,'      '.$this->nombreRector,0,0,'L');
			$titulo2 = '      '.$this->nombreSecretario;
			$w=$this->GetStringWidth($titulo2);
			$this->SetX(190-$w);
			$this->Cell($w,8,$titulo2,0,0,'R');
			$this->Ln(3);
			$this->SetFont('Arial','B',8);
			$this->Cell(0,10,'   RECTOR(A) DEL PLANTEL',0,0,'L');
			$titulo3 = '                      SECRETARIO(A) DEL PLANTEL';
			$w=$this->GetStringWidth($titulo3);
			$this->SetX(195-$w);
			$this->Cell($w,8,$titulo3,0,0,'R');
			$this->Ln();
			$this->SetFont('Arial','B',7);
			$this->Cell(185,6,"Espacio para legalización","LTR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LR",1,"C");
			$this->Cell(185,6," ","LBR",0,"C");
			$this->Ln(5);
			//Arial italic 8
			$this->SetFont('Arial','I',7);
			//Número de página
			$this->Cell(0,10,'Reporte generado por SIAE 2014 - '.pathinfo(__FILE__, PATHINFO_BASENAME),0,0,'R');
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
	$tipoEducacion = $paralelo->obtenerTipoEducacion($id_paralelo);
	
	$tipo_educacion = new tipos_educacion();
	$nombreTipoEducacion = $tipo_educacion->obtenerNombreTipoEducacion($id_paralelo);

	$especialidad = new especialidades();
	//$nombreEspecialidad = $especialidad->obtenerNombreEspecialidad($id_paralelo);
	$nombreFiguraProfesional = $especialidad->obtenerNombreFiguraProfesional($id_paralelo);
	
	$institucion = new institucion();
	$nombreInstitucion = $institucion->obtenerNombreInstitucion();
	$nombreRector = $institucion->obtenerNombreRector();
	$nombreSecretario = $institucion->obtenerNombreSecretario();

	$pdf=new PDF('P');
	$pdf->nombreParalelo = $nombreParalelo;
	$pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
	$pdf->nombreInstitucion = $nombreInstitucion;
	$pdf->nombreRector = $nombreRector;
	$pdf->nombreSecretario = $nombreSecretario;

	$pdf->AliasNbPages();
	//$pdf->AddPage();

	// Impresion de los titulos de cabecera
	$pdf->Ln(10);
	// Aqui va el codigo para imprimir las calificaciones de los estudiantes
	$db = new MySQL();
	$consulta = $db->consulta("SELECT e.id_estudiante, e.es_apellidos, e.es_nombres FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE ep.id_estudiante = e.id_estudiante AND ep.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 0;
		while($paralelo = $db->fetch_assoc($consulta))
		{
			$id_estudiante = $paralelo["id_estudiante"];
			
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',10);
			$nombre_completo = utf8_decode($paralelo["es_apellidos"])." ".utf8_decode($paralelo["es_nombres"]);
			$w=$pdf->GetStringWidth($nombre_completo);
			$pdf->SetX((210-$w)/2);
			$pdf->Cell($w,9,$nombre_completo,0,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial','',10);
			if($tipoEducacion==1) // Se trata de bachillerato
				$txt = "Del " . utf8_decode($nombreParalelo) . "; FIGURA PROFESIONAL: " . utf8_decode($nombreFiguraProfesional) . ", obtuvo las siguientes calificaciones durante el año lectivo " . $nombrePeriodoLectivo . ".";
			else
				$txt = "De " . utf8_decode($nombreParalelo) . " de " . utf8_decode($nombreFiguraProfesional) . ", obtuvo las siguientes calificaciones durante el año lectivo " . $nombrePeriodoLectivo . ".";
			$pdf->MultiCell(0,5,$txt);
			$pdf->Ln(7);
			$pdf->Cell(82,6,"ASIGNATURAS",1,0,'C');
			$pdf->Cell(20,6,"NUMEROS",1,0,'C');
			$pdf->Cell(78,6,"LETRAS",1,0,'C');
			$pdf->Ln();
			$asignaturas = $db->consulta("SELECT p.id_asignatura, as_nombre FROM sw_paralelo_asignatura p, sw_asignatura a WHERE p.id_asignatura = a.id_asignatura AND p.id_paralelo = $id_paralelo ORDER BY as_orden");
			$numero_asignaturas = $db->num_rows($asignaturas);
			$suma_promedios = 0; $contador_no_aprueba = 0;
			while($asignatura = $db->fetch_assoc($asignaturas))
			{
				$id_asignatura = $asignatura["id_asignatura"];
				$nombreAsignatura = substr($asignatura["as_nombre"],0,35);
				$pdf->Cell(82,5,$nombreAsignatura,1,0,'L');
				$query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
				$registro = $db->fetch_assoc($query);
				$promedio_final = $registro["promedio_final"];
				if($promedio_final < 7) $contador_no_aprueba++;
				$suma_promedios += $promedio_final;
				$pdf->Cell(20,5,number_format($promedio_final,2),1,0,'C');
				$pdf->Cell(78,5,equiv_letras(number_format($promedio_final,2)),1,0,'L');
				$pdf->Ln();
			}
			$pdf->Ln();
			$pdf->Cell(42,5," ",0,0,'C');
			$promedio_general = $suma_promedios / $numero_asignaturas;
			$pdf->Cell(60,5,"PROMEDIO GENERAL",1,0,'C');
			$pdf->Cell(20,5,number_format($promedio_general,2),1,0,'C');
			$pdf->Ln();
			$pdf->Cell(42,5," ",0,0,'C');
			$pdf->Cell(60,5,"COMPORTAMIENTO",1,0,'C');

			// Aqui calculo el promedio anual del comportamiento
			
			$periodo_eval_comp = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
			$num_total_registros = $db->num_rows($periodo_eval_comp);
			if($num_total_registros > 0)
			{
				$suma_total = 0;
				$suma_promedio = 0;
				while($per_comp = $db->fetch_assoc($periodo_eval_comp))
				{
					$id_periodo_evaluacion = $per_comp["id_periodo_evaluacion"];
					$comportamiento = $db->consulta("SELECT i.* FROM sw_comportamiento c, sw_indice_evaluacion i WHERE c.id_indice_evaluacion = i.id_indice_evaluacion AND c.id_paralelo = $id_paralelo AND c.id_estudiante = $id_estudiante AND c.id_periodo_evaluacion = $id_periodo_evaluacion");
					$total_indices = $db->num_rows($comportamiento);
					if($total_indices > 0)
					{
						$indice = $db->fetch_assoc($comportamiento);
						$total = $indice["total"];
						$promedio = $indice["promedio"];
						$equivalencia = $indice["equivalencia"];
					} else {
						$total = 0;
						$promedio = 0;
						$equivalencia = "";
					}
					$suma_total += $total;
					$suma_promedio += $promedio;
				}
				$promedio_anual = $suma_promedio / $num_total_registros;
			}
			$pdf->Cell(20,5,equiv_comportamiento($promedio_anual),1,0,'C');
			$pdf->Ln(); $pdf->Ln();
			$cadena_aprueba = ($contador_no_aprueba > 0) ? "NO" : "";
			$pdf->MultiCell(0,5,"Por lo tanto $cadena_aprueba es promovido/a al siguiente grado/curso. Para certificar suscriben en unidad de acto el Rector(a) con la Secretaria General del Plantel.");
			$pdf->Ln();
		}
	}

	$pdf->Output();
?>
