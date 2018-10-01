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
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nombreParalelo = $paralelo->obtenerNombreParalelo($id_paralelo);
$nomParalelo = $paralelo->getNombreParalelo($id_paralelo);

$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Primero busco la plantilla adecuada de acuerdo al numero de asignaturas del paralelo
$numAsignaturas = $paralelo->contarAsignaturas($id_paralelo);
//if($tipoEducacion==0) $numAsignaturas++;

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "CUADRO QUIMESTRAL - ";
$objPHPExcel = $objReader->load("../templates/" . $baseFilename . $numAsignaturas . " ASIGNATURAS.xls");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A7', $nombreInstitucion)
							  ->setCellValue('A8', 'CUADRO DE CALIFICACIONES - '.$nombrePeriodoEvaluacion);

// Renombrar la hoja de calculo
$objPHPExcel->getActiveSheet()->setTitle($nombrePeriodoEvaluacion);

// Vectores de configuracion para las columnas
$colAsignaturas = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');

// Columna para escribir el promedio de las asignaturas
switch ($numAsignaturas) {
    case 6: $colPromedio = 'I'; $colNomPerLectivo = 'F'; $colComportamiento = 'J'; $colNomParalelo = 'I'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 7: $colPromedio = 'J'; $colNomPerLectivo = 'F'; $colComportamiento = 'K'; $colNomParalelo = 'J'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 8: $colPromedio = 'K'; $colNomPerLectivo = 'F'; $colComportamiento = 'M'; $colNomParalelo = 'K'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 9: $colPromedio = 'L'; $colNomPerLectivo = 'F'; $colComportamiento = 'M'; $colNomParalelo = 'L'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'D61'; $colNomSecre = 'O61'; break;
    case 10: $colPromedio = 'M'; $colNomPerLectivo = 'F'; $colComportamiento = 'O'; $colNomParalelo = 'M'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 11: $colPromedio = 'N'; $colNomPerLectivo = 'F'; $colComportamiento = 'P'; $colNomParalelo = 'N'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 12: $colPromedio = 'O'; $colNomPerLectivo = 'F'; $colComportamiento = 'P'; $colNomParalelo = 'O'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 13: $colPromedio = 'P'; $colNomPerLectivo = 'F'; $colComportamiento = 'Q'; $colNomParalelo = 'P'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 14: $colPromedio = 'Q'; $colNomPerLectivo = 'F'; $colComportamiento = 'R'; $colNomParalelo = 'Q'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 15: $colPromedio = 'R'; $colNomPerLectivo = 'I'; $colComportamiento = 'S'; $colNomParalelo = 'R'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 16: $colPromedio = 'S'; $colNomPerLectivo = 'I'; $colComportamiento = 'T'; $colNomParalelo = 'S'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 17: $colPromedio = 'T'; $colNomPerLectivo = 'F'; $colComportamiento = 'V'; $colNomParalelo = 'T'; $nombreCurso = $paralelo->getNombreCurso($id_paralelo); $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
}

$objPHPExcel->getActiveSheet()->setCellValue('A10', $nombreCurso);
$objPHPExcel->getActiveSheet()->setCellValue($colNomPerLectivo.'9', $nombrePeriodoLectivo);
$objPHPExcel->getActiveSheet()->setCellValue($colNomParalelo.'12', $nomParalelo);

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
// Se utilizara el store procedure sp_calcular_prom_quimestre que tiene los siguientes parametros:
//    IdPeriodoEvaluacion : $id_periodo_evaluacion (parametro POST)
//    IdParalelo : $id_paralelo (parametro POST)
$db = new MySQL();
$qry = "CALL sp_calcular_prom_quimestre($id_periodo_evaluacion, $id_paralelo)";
$res = $db->consulta($qry);

$estudiantes = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, eq_promedio FROM sw_estudiante e, sw_estudiante_prom_quimestral eq WHERE e.id_estudiante = eq.id_estudiante AND id_paralelo = $id_paralelo ORDER BY eq_promedio DESC");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$row = 15; // fila base
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];
		$promedio_quimestral_total = $estudiante["eq_promedio"];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $apellidos." ".$nombres);
		
		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_paralelo p, sw_asignatura a WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if($total_asignaturas > 0)
		{
			$rowAsignatura = 13; $contAsignatura = 0;
			while ($asignatura = $db->fetch_assoc($asignaturas))
			{
				// Aqui proceso los promedios de cada asignatura
				$id_asignatura = $asignatura["id_asignatura"];
				$asignatura = $asignatura["as_nombre"];
				
				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$rowAsignatura, $asignatura);
				
				// Aca voy a llamar a una funcion almacenada que calcula el promedio quimestral de la asignatura
				$query = $db->consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
				$calificacion = $db->fetch_assoc($query);
				$promedio_quimestral = $calificacion["promedio"];

                $objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, truncar($promedio_quimestral,2));
                
                $contAsignatura++;
			} // fin while $asignatura
			
			// Calculo e impresion del promedio de asignaturas
            $objPHPExcel->getActiveSheet()->setCellValue($colPromedio.$row, truncar($promedio_quimestral_total,2));
            
		} // fin if $total_asignatura
		$row++;
	}
}

// Aqui va el codigo para desplegar la lista de docentes del paralelo

$objPHPExcel->setActiveSheetIndex(1);
$docentes = $db->consulta("SELECT us_titulo, 
								  us_apellidos, 
								  us_nombres, 
								  as_nombre 
							 FROM sw_distributivo di,
							      sw_asignatura_curso ac, 
							 	  sw_usuario u, 
								  sw_asignatura a 
							WHERE u.id_usuario = di.id_usuario 
							  AND a.id_asignatura = di.id_asignatura
							  AND ac.id_asignatura = di.id_asignatura
							  AND ac.id_curso = $id_curso 
							  AND id_paralelo = $id_paralelo");
$num_total_docentes = $db->num_rows($docentes);
if ($num_total_docentes > 0) {
	$row = 4;
	while ($docente = $db->fetch_object($docentes)) {
		$asignatura = $docente->as_nombre;
		$profesor = $docente->us_titulo . " " . $docente->us_apellidos . " " . $docente->us_nombres;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $asignatura);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $profesor);
		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save("CUADRO QUIMESTRAL "  . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoEvaluacion . "(" . $nombrePeriodoLectivo . ").xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "CUADRO QUIMESTRAL "  . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoEvaluacion . "(" . $nombrePeriodoLectivo . ").xls" . "\"" );
	readfile("CUADRO QUIMESTRAL "  . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoEvaluacion . "(" . $nombrePeriodoLectivo . ").xls");

?>