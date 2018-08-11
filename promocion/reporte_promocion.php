<?php
/*
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

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
	$cadena .= " COMA ";
	if ($parte_decimal_1 == 0 && $parte_decimal_2 == 0) {
		$cadena .= "CERO";
	} else if ($parte_decimal_1 != 0 || $parte_decimal_2 != 0) {
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

/* Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Guayaquil');

/* PHPExcel_IOFactory */

require_once '../php_excel/Classes/PHPExcel/IOFactory.php';
require_once '../scripts/clases/class.mysql.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.estudiantes.php';
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.especialidades.php');
require_once('../funciones/funciones_sitio.php');

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_estudiante = $_POST["id_estudiante"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);
$tipoEducacion = $paralelo->obtenerTipoEducacion($id_paralelo); // 0: Educacion Basica Superior  1: Bachillerato

$estudiante = new estudiantes();
$nombreEstudiante = $estudiante->obtenerEstudianteId($id_estudiante, $id_periodo_lectivo);

$especialidad = new especialidades();
$nombreFiguraProfesional = $especialidad->obtenerNombreFiguraProfesional($id_paralelo);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();
$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

if($tipoEducacion==1) // Se trata de bachillerato
	$txt = "del " . $nombreParalelo . "; FIGURA PROFESIONAL: " . $nombreFiguraProfesional . ", obtuvo las siguientes calificaciones durante el presente " . utf8_encode("año") . " lectivo.";
else // Se trata de Educación Básica Superior
	$txt = "del " . $nombreParalelo . " DE " . $nombreFiguraProfesional . ", obtuvo las siguientes calificaciones durante el presente " . utf8_encode("año") . " lectivo.";
	
// Primero busco la plantilla adecuada de acuerdo al tipo de educación => 0: Educación Básica Superior  1: Bachillerato
($tipoEducacion==0) ? $baseFilename = "CERTIFICADO DE PROMOCION EBG" : $baseFilename = "CERTIFICADO DE PROMOCION BGU";

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename . ".xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C2', $nombreInstitucion)
							  ->setCellValue('C4', utf8_encode('AÑO LECTIVO '.$nombrePeriodoLectivo))
							  ->setCellValue('C7', utf8_encode($nombreEstudiante))
							  ->setCellValue('C8', $txt);

//Aqui va el codigo para calcular el promedio final de cada asignatura

$db = new MySQL();

$asignaturas = $db->consulta("SELECT a.id_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_paralelo p, sw_asignatura a WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY ac_orden");
$numero_asignaturas = $db->num_rows($asignaturas);
$suma_promedios = 0; $contador_no_aprueba = 0; $row = 13; // fila base
while($asignatura = $db->fetch_assoc($asignaturas))
{
	$id_asignatura = $asignatura["id_asignatura"];
	$nombreAsignatura = substr($asignatura["as_nombre"],0,45);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, utf8_encode($nombreAsignatura));
	$query = $db->consulta("SELECT calcular_promedio_final($id_periodo_lectivo,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_final");
	$registro = $db->fetch_assoc($query);
	$promedio_final = $registro["promedio_final"];
	if($promedio_final < 7) $contador_no_aprueba++;
	$suma_promedios += $promedio_final;
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$row, $promedio_final);
	$objPHPExcel->getActiveSheet()->setCellValue('X'.$row, equiv_letras(number_format($promedio_final,2)));
	$row++;
}

$promedio_general = $suma_promedios / $numero_asignaturas;

// Calculo del comportamiento anual
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
$consulta = $db->consulta("SELECT ec_cualitativa FROM sw_escala_comportamiento WHERE $promedio_anual >= ec_nota_minima AND $promedio_anual <= ec_nota_maxima");
$equivalencia = $db->fetch_assoc($consulta);

//Aqui obtengo el nombre del curso superior asociado
$consulta = $db->consulta("SELECT cs_nombre FROM sw_curso_superior cs, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND cs.id_curso_superior = c.id_curso_superior AND p.id_paralelo = $id_paralelo");
$curso_superior = $db->fetch_assoc($consulta);

$nombreCursoSuperior = $curso_superior["cs_nombre"];

$cadena_aprueba = ($contador_no_aprueba > 0) ? "NO" : "";
$txt = "Por lo tanto $cadena_aprueba es promovido/a al $nombreCursoSuperior. Para certificar suscriben en unidad de acto el Rector(a) con la Secretaria General del Plantel.";

//Coloco los nombres del rector y secretario de la institucion
$nombreSecretario = $institucion->obtenerNombreSecretario();

if($tipoEducacion==0) {
	$objPHPExcel->getActiveSheet()->setCellValue('S22', $promedio_general);
	$objPHPExcel->getActiveSheet()->setCellValue('W22', equiv_letras(number_format($promedio_general,2)));

	$objPHPExcel->getActiveSheet()->setCellValue('S23', equiv_comportamiento($promedio_anual));
	$objPHPExcel->getActiveSheet()->setCellValue('W23', $equivalencia["ec_cualitativa"]);

	//Calculo del promedio anual de proyectos escolares
	$query = $db->consulta("SELECT calcular_promedio_anual_proyectos($id_periodo_lectivo,$id_estudiante) AS promedio_anual");
	$registro = $db->fetch_assoc($query);
	$promedio_anual = $registro["promedio_anual"];
	
	$query = $db->consulta("SELECT ec_equivalencia, ec_cualitativa FROM sw_escala_proyectos WHERE $promedio_anual >= ec_nota_minima AND $promedio_anual <= ec_nota_maxima");
	$proyecto = $db->fetch_assoc($query);
	
	$objPHPExcel->getActiveSheet()->setCellValue('S24', $proyecto["ec_equivalencia"]);
	$objPHPExcel->getActiveSheet()->setCellValue('W24', $proyecto["ec_cualitativa"]);

	$objPHPExcel->getActiveSheet()->setCellValue('C26', $txt);

	$objPHPExcel->getActiveSheet()->setCellValue('F34', $nombreRector);
	$objPHPExcel->getActiveSheet()->setCellValue('U34', $nombreSecretario);

} else {
	$objPHPExcel->getActiveSheet()->setCellValue('S28', $promedio_general);
	$objPHPExcel->getActiveSheet()->setCellValue('W28', equiv_letras(number_format($promedio_general,2)));

	$objPHPExcel->getActiveSheet()->setCellValue('S29', equiv_comportamiento($promedio_anual));
	$objPHPExcel->getActiveSheet()->setCellValue('W29', $equivalencia["ec_cualitativa"]);

	$objPHPExcel->getActiveSheet()->setCellValue('C31', $txt);

	$objPHPExcel->getActiveSheet()->setCellValue('F39', $nombreRector);
	$objPHPExcel->getActiveSheet()->setCellValue('U39', $nombreSecretario);

}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("PROMOCION " . $nombreEstudiante . " (" . $nombrePeriodoLectivo . ").xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "PROMOCION " . $nombreEstudiante . " (" . $nombrePeriodoLectivo . ").xls" . "\"" );
	readfile("PROMOCION " . $nombreEstudiante . " (" . $nombrePeriodoLectivo . ").xls");

?>