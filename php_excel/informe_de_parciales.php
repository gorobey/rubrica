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
require_once '../scripts/clases/class.institucion.php';
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

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$usuario = new usuarios();
$nombreUsuario = $usuario->obtenerNombreUsuario($id_usuario);

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);

$aporte = new aportes_evaluacion();
$nombreAporte = $aporte->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "Informe-de-Parciales";
$objPHPExcel = $objReader->load("../plantillas/".$baseFilename.".xls");

$objPHPExcel->getActiveSheet()->setCellValue('B1', $nombreInstitucion)
							  ->setCellValue('B2', 'PERIODO LECTIVO '.$nombrePeriodoLectivo)
							  ->setCellValue('B4', $nombreAporte)
							  ->setCellValue('C6', $nombreAsignatura)
							  ->setCellValue('C7', $nombreParalelo)
							  ->setCellValue('C8', $nombreUsuario);

$contadores[0] = 0;$porcentajes[0] = 0;
$contadores[1] = 0;$porcentajes[1] = 0;
$contadores[2] = 0;$porcentajes[2] = 0;
$contadores[3] = 0;$porcentajes[3] = 0;
$contadores[4] = 0;$porcentajes[4] = 0;
// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
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
			// Calculo de porcentajes de acuerdo a la escala de calificaciones
			$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
			$cont_escala = 0;
			while ($escala = $db->fetch_assoc($escala_calificacion))
			{
				$nota_minima = $escala["ec_nota_minima"];
				$nota_maxima = $escala["ec_nota_maxima"];
				if ($promedio >= $nota_minima && $promedio <= $nota_maxima)
					$contadores[$cont_escala] = $contadores[$cont_escala] + 1;
				$cont_escala++;
			}
		}
	}
	// Calculo de porcentajes de acuerdo a la escala de calificaciones				
	for($cont=0;$cont<$cont_escala;$cont++)
		//$porcentajes[$cont]=ceil($contadores[$cont]/$num_total_estudiantes);
		$porcentajes[$cont]=$contadores[$cont]/$num_total_estudiantes;
}

$consulta = $db->consulta("SELECT id_paralelo_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo and id_asignatura = $id_asignatura");
$registro = $db->fetch_assoc($consulta);
$id_paralelo_asignatura = $registro["id_paralelo_asignatura"];
$consulta = $db->consulta("SELECT id_escala_calificaciones, ec_cualitativa, ec_cuantitativa FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden");
$num_total_registros = $db->num_rows($consulta);
if($num_total_registros>0)
{
	$contador = 0; $row = 10;
	while($escalas = $db->fetch_assoc($consulta))
	{
		$contador++; $row++;
		$id_escala_calificaciones = $escalas["id_escala_calificaciones"];
		$cualitativa = $escalas["ec_cualitativa"];
		$cuantitativa = $escalas["ec_cuantitativa"];
		$qry = $db->consulta("SELECT re_plan_de_mejora FROM sw_recomendaciones WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
		$registro = $db->fetch_assoc($qry);
		//$recomendaciones = $registro["re_recomendaciones"];
		$plan_de_mejora = $registro["re_plan_de_mejora"];
		
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $cualitativa)
									  ->setCellValue('C'.$row, $cuantitativa)
									  ->setCellValue('D'.$row, $contadores[$contador - 1])
									  ->setCellValue('E'.$row, $porcentajes[$contador - 1])
									  ->setCellValue('F'.$row, $plan_de_mejora);
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_aporte_evaluacion . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . $baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_aporte_evaluacion . ".xls" . "\"" );
	readfile($baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_aporte_evaluacion . ".xls");

?>