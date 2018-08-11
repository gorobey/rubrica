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
require_once '../funciones/funciones_sitio.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo);

switch ($numAsignaturas) {
	case 6: $colComportamiento = 'W'; break;
	case 7: $colComportamiento = 'Z'; break;
	case 8: $colComportamiento = 'AC'; break;
	case 9: $colComportamiento = 'AF'; break;
	case 10: $colComportamiento = 'AI'; break;
	case 11: $colComportamiento = 'AL'; break;
	case 12: $colComportamiento = 'AO'; break;
	case 13: $colComportamiento = 'AR'; break;
	case 14: $colComportamiento = 'AU'; break;
        case 15: $colComportamiento = 'AX'; break;
}

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "CUADRO ANUAL - ";
$objPHPExcel = $objReader->load("../plantillas/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'PERIODO LECTIVO '.$nombrePeriodoLectivo)
							  ->setCellValue('A5', $nombreParalelo);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'F', 'I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ', 'AM', 'AP', 'AS');
$colPrimerPeriodo = array('C', 'F', 'I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ', 'AM', 'AP', 'AS');
$colSegundoPeriodo = array('D', 'G', 'J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK', 'AN', 'AQ', 'AT');

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$row = 8; // fila base
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $apellidos." ".$nombres);
		
		$asignaturas = $db->consulta("SELECT p.id_asignatura, as_nombre FROM sw_paralelo_asignatura p, sw_asignatura a WHERE p.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY p.id_asignatura");
		$total_asignaturas = $db->num_rows($asignaturas);
		if($total_asignaturas > 0)
		{
			$rowAsignatura = 6; $contAsignatura = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas))
			{
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_nombre"];
				
				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$rowAsignatura, $asignatura);
				
				$periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
				$num_total_registros = $db->num_rows($periodo_evaluacion);
				if($num_total_registros > 0)
				{
					$contador_periodos = 0;
					while($periodo = $db->fetch_assoc($periodo_evaluacion))
					{
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

						$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
						$aporte_evaluacion = $db->consulta($qry);
						//echo $qry . "<br>";
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
							}
						}
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						
						$colPeriodo = ($contador_periodos==1) ? $colPrimerPeriodo[$contAsignatura] : $colSegundoPeriodo[$contAsignatura];
						
						$objPHPExcel->getActiveSheet()->setCellValue($colPeriodo.$row, number_format($calificacion_quimestral,2));
							
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
							$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento.$row, equiv_comportamiento($promedio_anual));
						}

						//
												
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				$contAsignatura++;
			} // fin while $asignatura
		} // fin if $total_asignatura
		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("CUADRO ANUAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "CUADRO ANUAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"" );
	readfile("CUADRO ANUAL " . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

?>