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
$impresion_para_juntas = $_POST["impresion_para_juntas"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "CUADRO ANUAL.xls";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', $nombreInstitucion)
							  ->setCellValue('A2', 'REPORTE DEL PERIODO LECTIVO '.$nombrePeriodoLectivo)
							  ->setCellValue('A3', 'CURSO: '.$nombreParalelo);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');

// Aqui va el codigo para calcular los promedios de los parciales de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$row = 6; // fila base
	$contador = 0;
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$retirado = $estudiante["es_retirado"];
		
		$contador++;
		$contador_general_sin_examen = 0;
		
		$contador_no_aprueba=0; 
		$contador_supletorio=0; 
		$contador_remedial=0;

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $contador)
						  ->setCellValue('B'.$row, $apellidos." ".$nombres);
		//$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFill()->getStartColor()->setRGB('FF0000');

		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if($total_asignaturas > 0)
		{
			$rowAsignatura = 5; $contAsignatura = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas))
			{
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_abreviatura"];
				
				$contador_sin_examen=0;
				
				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$rowAsignatura, $asignatura);
				
				$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
				$num_total_registros = $db->num_rows($periodo_evaluacion);
				if($num_total_registros>0)
				{
					$suma_periodos = 0; $contador_periodos = 0;
					while($periodo = $db->fetch_assoc($periodo_evaluacion))
					{
						$contador_periodos++;
						
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

						$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
						$aporte_evaluacion = $db->consulta($qry);
						
						$num_total_registros = $db->num_rows($aporte_evaluacion);
						if($num_total_registros>0)
						{
							// Aqui calculo los promedios y desplegar en la tabla
							$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
							while($aporte = $db->fetch_assoc($aporte_evaluacion))
							{
								$contador_aportes++;
								$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = $db->num_rows($rubrica_evaluacion);
								if($total_rubricas>0)
								{
									$suma_rubricas = 0; $contador_rubricas = 0;
									while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
									{
$contador_rubricas++;
										$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
										$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
										$total_registros = $db->num_rows($qry);
										if($total_registros>0) {
											$rubrica_estudiante = $db->fetch_assoc($qry);
											$calificacion = $rubrica_estudiante["re_calificacion"];
										} else {
											$calificacion = 0;
										}
										$suma_rubricas += $calificacion;
									}
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = $suma_rubricas / $contador_rubricas;
								if($contador_aportes <= $num_total_registros - 1) {
									$suma_promedios += $promedio;
								} else {
									$examen_quimestral = $promedio;
								}
							} //while($aporte = $db->fetch_assoc($aporte_evaluacion))
							if ($examen_quimestral == 0) $contador_sin_examen++;
						} // if($num_total_registros>0)
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						$suma_periodos += $calificacion_quimestral;
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_periodos = $suma_periodos / $contador_periodos;
				if($promedio_periodos==0)
					$contador_no_aprueba++;
				//else if($promedio_periodos > 0 && $promedio_periodos < 5 && $contador_sin_examen == 0) {
				else if($promedio_periodos > 0 && $promedio_periodos < 5) {
					$contador_remedial++;
				} else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
					$contador_supletorio++;
				}
				
				// Aqui desplegar el promedio de los quimestres

				if ($contador_sin_examen > 0) $contador_general_sin_examen++;
				
				$promedio_periodos = ($contador_sin_examen > 0) ? 'S/E' : floor($promedio_periodos * 100)/100;
				
				if ($retirado == "S") {
					//$pdf->Cell(13,5,"-",1,0,'C');
					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, "-");
				} else if ($impresion_para_juntas == 0) {
								
					//$pdf->Cell(13,5,$promedio_periodos,1,0,'C');
					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, $promedio_periodos);
				} else { // Impresion para juntas
					if ($promedio_periodos < 7 && $promedio_periodos > 0) { // Tiene que dar examen supletorio
						$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, $promedio_periodos);
						#$objPHPExcel->getActiveSheet()->getStyle($colAsignaturas[$contAsignatura].$row)->getFill()->getStartColor()->setRGB('FF0000');
					} else if ($contador_sin_examen > 0) {
						//$pdf->Cell(13,5,"S/E",1,0,'C');
						$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, "S/E");
					} else { // No tiene problemas
						//$pdf->Cell(13,5," ",1,0,'C');
						$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, " ");
					}
				}

				$contAsignatura++;
			} // fin while $asignatura
		} // fin if $total_asignatura
	
		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("CUADRO ANUAL EXCEL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "CUADRO ANUAL EXCEL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"" );
	readfile("CUADRO ANUAL EXCEL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

?>