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
require_once '../scripts/clases/class.usuarios.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.aportes_evaluacion.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
$id_asignatura = $_POST["id_asignatura"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

$usuario = new usuarios();
$nombreUsuario = $usuario->obtenerNombreUsuario($id_usuario);

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->getNombreParalelo($id_paralelo);

$aporte = new aportes_evaluacion();
$nombreAporte = $aporte->getNombreAporte($id_aporte_evaluacion);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);
$nombreArea = $asignatura->obtenerNombreArea($id_asignatura);

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "INFORME PARCIAL DE APRENDIZAJES";
$objPHPExcel = $objReader->load("../templates/".$baseFilename.".xls");

$objPHPExcel->getActiveSheet()->setCellValue('B6', $nombrePeriodoLectivo)
							  ->setCellValue('C8', $nombreArea)
							  ->setCellValue('F8', $nombreAporte)
							  ->setCellValue('C9', $nombreUsuario)
							  ->setCellValue('F9', $nombreParalelo)
							  ->setCellValue('C10', $nombreAsignatura)
							  ->setCellValue('C59', $nombreUsuario);

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, 
									 es_apellidos, 
									 es_nombres 
								FROM sw_estudiante e,
									 sw_estudiante_periodo_lectivo ep 
							   WHERE e.id_estudiante = ep.id_estudiante
							     AND es_retirado = 'N'
								 AND id_paralelo = $id_paralelo
							ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$fila_base = 25;
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$nombreEstudiante = $estudiante["es_apellidos"]." ".$estudiante["es_nombres"];
		// Consulta de las calificaciones correspondientes al aporte de evaluacion
		$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
		$num_total_registros = $db->num_rows($rubrica_evaluacion);
		if($num_total_registros > 0)
		{
			$suma_rubricas = 0; $contador_rubricas = 0;
			while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
			{
				$contador_rubricas++;
				$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
				$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$estudiante["id_estudiante"]." AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
				$num_total_registros = $db->num_rows($qry);
				$rubrica_estudiante = $db->fetch_assoc($qry);
				if($num_total_registros > 0) {
					$calificacion = $rubrica_estudiante["re_calificacion"];
				} else {
					$calificacion = 0;
				}
				$suma_rubricas += $calificacion;
			}
			$promedio = $suma_rubricas / $contador_rubricas;
			// Desplegar los estudiantes con promedio menor que siete
			if($promedio < 7)
			{
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila_base, $nombreEstudiante);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila_base, $promedio);
				$fila_base++;
			}
		}
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($baseFilename . " " . $nombreParalelo . " " . $nombreAsignatura . " " . $nombreAporte . " PARCIAL " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . $baseFilename . " " . $nombreParalelo . " " . $nombreAsignatura . " " . $nombreAporte . " PARCIAL " . $nombrePeriodoLectivo . ".xls" . "\"" );
	readfile($baseFilename . " " . $nombreParalelo . " " . $nombreAsignatura . " " . $nombreAporte . " PARCIAL " . $nombrePeriodoLectivo . ".xls");

?>