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
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "CUADRO QUIMESTRAL DOCENTE.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
							  ->setCellValue('A2', 'REPORTE DEL '.$nombrePeriodoEvaluacion)
							  ->setCellValue('A3', $nombrePeriodoLectivo)
							  ->setCellValue('A5', 'ASIGNATURA: '.$nombreAsignatura)
							  ->setCellValue('A6', 'CURSO: '.$nombreParalelo);

// Vectores de configuracion para las columnas
$colAportes = array('C', 'D', 'E', 'H');

// Aqui va el codigo para calcular los promedios de los parciales de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$row = 9; // fila base
	$contador = 0;
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		
		$contador++;

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $contador)
									  ->setCellValue('B'.$row, $apellidos." ".$nombres);

		// Aqui se calcula el promedio de cada parcial
		$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
		$num_total_registros = $db->num_rows($aporte_evaluacion);
		if($num_total_registros>0)
		{
			$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
			while($aporte = $db->fetch_assoc($aporte_evaluacion))
			{
				$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];

				$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
				$total_rubricas = $db->num_rows($rubrica_evaluacion);
				if($total_rubricas > 0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
						$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
						$total_registros = $db->num_rows($qry);
						if($total_registros > 0) {
							$rubrica_estudiante = $db->fetch_assoc($qry);
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
					}
				}
				
				$promedio = $suma_rubricas / $contador_rubricas;

				if($contador_aportes < $total_rubricas)
					$suma_promedios += $promedio;
				else 
					$examen_quimestral = $promedio;

				$objPHPExcel->getActiveSheet()->setCellValue($colAportes[$contador_aportes].$row, $paralelo->truncateFloat($promedio,2));
				$contador_aportes++;
			}
		}

		// Aqui debo calcular el ponderado de los promedios parciales
		$promedio_aportes = $paralelo->truncateFloat($suma_promedios / ($contador_aportes - 1),2);
		$ponderado_aportes = $paralelo->truncateFloat(0.8 * $promedio_aportes,2);
		$ponderado_examen = $paralelo->truncateFloat(0.2 * $examen_quimestral,2);
		
		$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $promedio_aportes)
							    	  ->setCellValue('G'.$row, $ponderado_aportes)
							    	  ->setCellValue('I'.$row, $ponderado_examen)
							    	  ->setCellValue('J'.$row, $calificacion_quimestral);

		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("CUADRO QUIMESTRAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "CUADRO QUIMESTRAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"" );
	readfile("CUADRO QUIMESTRAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

?>