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

function truncar($numero, $digitos)
{
    $truncar = pow(10,$digitos);
    return intval($numero * $truncar) / $truncar;
}


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
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V');

// Aqui va el codigo para calcular los promedios de los parciales de cada estudiante
// Se utilizara el store procedure sp_calcular_prom_anual que tiene los siguientes parametros:
//    IdPeriodoLectivo : $id_periodo_lectivo (parametro SESSION)
//    IdParalelo : $id_paralelo (parametro POST)
$db = new MySQL();
$qry = "CALL sp_calcular_prom_anual($id_periodo_lectivo, $id_paralelo)";
$res = $db->consulta($qry);

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, ea_promedio FROM sw_estudiante e, sw_estudiante_prom_anual ea WHERE e.id_estudiante = ea.id_estudiante AND id_paralelo = $id_paralelo ORDER BY ea_promedio DESC");
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
		$promedio_anual_total = $estudiante["ea_promedio"];
		
		$contador++;

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $contador)
						  			  ->setCellValue('B'.$row, $apellidos." ".$nombres);

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

				// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
				$query = $db->consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
				$calificacion = $db->fetch_assoc($query);
				$promedio_anual = $calificacion["promedio"];

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, truncar($promedio_anual,2));
				
				$contAsignatura++;
			} // fin while $asignatura

			// Calculo e impresion del promedio de asignaturas
			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$rowAsignatura, 'PROM.');
			$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, truncar($promedio_anual_total,2));

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