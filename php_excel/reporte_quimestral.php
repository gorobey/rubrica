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
require_once '../scripts/clases/class.cursos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.asignaturas.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
if(isset($_POST["impresion_para_juntas"])){
	$impresion_para_juntas = $_POST["impresion_para_juntas"];
}else{
	$impresion_para_juntas = 0;
}

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

$nombreRector = $institucion->obtenerNombreRector();
$nombreSecretario = $institucion->obtenerNombreSecretario();

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$id_curso = $paralelo->obtenerIdCurso($id_paralelo);
$nombreParalelo = $paralelo->obtenerNomParalelo($id_paralelo);
$nombreCurso = $paralelo->obtenerNombreParalelo($id_paralelo);
//$tipoEducacion = $paralelo->obtenerTipoEducacion($id_paralelo); // 0: Educacion Basica Superior  1: Bachillerato

$cursos = new cursos();
$bol_proyectos = $cursos->obtenerBolProyectos($id_curso);

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
    case 6: $colPromedio = 'I'; $colComportamiento = 'J'; $colNomParalelo = 'I'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 7: $colPromedio = 'J'; $colComportamiento = 'K'; $colNomParalelo = 'J'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 8: $colPromedio = 'K'; $colComportamiento = 'M'; $colNomParalelo = 'K'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 9: $colPromedio = 'L'; $colComportamiento = 'M'; $colNomParalelo = 'L'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 10: $colPromedio = 'M'; $colComportamiento = 'O'; $colNomParalelo = 'M'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 11: $colPromedio = 'N'; $colComportamiento = 'P'; $colNomParalelo = 'N'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 12: $colPromedio = 'O'; $colComportamiento = 'P'; $colNomParalelo = 'O'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 13: $colPromedio = 'P'; $colComportamiento = 'Q'; $colNomParalelo = 'P'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 14: $colPromedio = 'Q'; $colComportamiento = 'R'; $colNomParalelo = 'Q'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 15: $colPromedio = 'R'; $colComportamiento = 'S'; $colNomParalelo = 'R'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 16: $colPromedio = 'S'; $colComportamiento = 'T'; $colNomParalelo = 'S'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
    case 17: $colPromedio = 'T'; $colComportamiento = 'V'; $colNomParalelo = 'T'; $colNomRector = 'B67'; $colNomSecre = 'F67'; break;
}

$objPHPExcel->getActiveSheet()->setCellValue('A10', $nombreCurso);
$objPHPExcel->getActiveSheet()->setCellValue('F9', $nombrePeriodoLectivo);
$objPHPExcel->getActiveSheet()->setCellValue($colNomParalelo.'12', 'PARALELO '.str_replace('"','',$nombreParalelo));

$objPHPExcel->getActiveSheet()->setCellValue($colNomRector, $nombreRector);
$objPHPExcel->getActiveSheet()->setCellValue($colNomSecre, $nombreSecretario);

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT e.id_estudiante, 
									 es_apellidos, 
									 es_nombres 
								FROM sw_estudiante e, 
									 sw_estudiante_periodo_lectivo p 
							   WHERE e.id_estudiante = p.id_estudiante 
							     AND p.id_paralelo = $id_paralelo 
								 AND es_retirado = 'N' 
							ORDER BY es_apellidos, es_nombres");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	$row = 15; // fila base
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		$id_estudiante = $estudiante["id_estudiante"];
		$apellidos = $estudiante["es_apellidos"];
		$nombres = $estudiante["es_nombres"];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $apellidos." ".$nombres);
		
		$asignaturas = $db->consulta("SELECT a.id_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_paralelo p, sw_asignatura a WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND id_paralelo = $id_paralelo ORDER BY ac_orden");
		$total_asignaturas = $db->num_rows($asignaturas);
		if($total_asignaturas > 0)
		{
			$rowAsignatura = 13; $contAsignatura = 0; $sumaPromedios = 0; $sumaComportamiento = 0;
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
				$sumaPromedios += $promedio_quimestral;
				
				if ($impresion_para_juntas==1 && $promedio_quimestral>=7) {
					$promedio_quimestral = "";
				}

				$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, $promedio_quimestral);
                                
				$query = $db->consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS comportamiento");
				$calificacion = $db->fetch_assoc($query);
				$comportamiento = $calificacion["comportamiento"];
				$sumaComportamiento += $comportamiento;

				$contAsignatura++;
			} // fin while $asignatura
			
			// Calculo e impresion del promedio de asignaturas
			$promedioAsignaturas = $sumaPromedios / $total_asignaturas;
			$objPHPExcel->getActiveSheet()->setCellValue($colPromedio.$row, number_format($promedioAsignaturas,2));
			
            // Calculo e impresion del promedio de comportamiento
			$promedioComportamiento = $sumaComportamiento / $total_asignaturas;
			$promedio_comportamiento = ceil($promedioComportamiento);

			$query = $db->consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
			$equivalencia = $db->fetch_assoc($query);
			$objPHPExcel->getActiveSheet()->setCellValue($colComportamiento.$row, $equivalencia['ec_equivalencia']);
                        
			if($bol_proyectos==0) 
			{				
				// Aqui obtengo el id_club del estudiante
				$qry = $db->consulta("SELECT id_club FROM sw_estudiante_club WHERE id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");
				$total_registros = $db->num_rows($qry);
				if($total_registros > 0) {
					$registro = $db->fetch_assoc($qry);
					$id_club = $registro["id_club"];
				
					// Aca calculo el promedio quimestral del club al que pertenece el estudiante
					$query = $db->consulta("SELECT calcular_promedio_quimestre_club($id_periodo_evaluacion, $id_estudiante, $id_club) AS promedio");
					$calificacion = $db->fetch_assoc($query);
					$promedio_quimestral = $calificacion["promedio"];

					// Aqui obtengo la equivalencia cualitativa para el promedio quimestral de clubes
					$qry = $db->consulta("SELECT ec_equivalencia FROM sw_escala_proyectos WHERE ec_nota_minima <= $promedio_quimestral AND ec_nota_maxima >= $promedio_quimestral");
					$registro = $db->fetch_assoc($qry);
					$equivalencia = $registro["ec_equivalencia"];
										
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $equivalencia);
				} else {
//					$objPHPExcel->getActiveSheet()->setCellValue($colAsignaturas[$contAsignatura].$row, number_format(0,2));
				}
			}                        
                        
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
							  AND id_paralelo = $id_paralelo
							ORDER BY ac_orden");
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
$objWriter->save("CUADRO QUIMESTRAL " . $nombreCurso . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "CUADRO QUIMESTRAL " . $nombreCurso . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls" . "\"" );
	readfile("CUADRO QUIMESTRAL " . $nombreCurso . str_replace('"','',$nombreParalelo) . " " . $nombrePeriodoLectivo . ".xls");

?>