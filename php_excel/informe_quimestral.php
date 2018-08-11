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
require_once '../scripts/clases/class.periodos_evaluacion.php';

// Variables enviadas mediante POST	
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_asignatura = $_POST["id_asignatura"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_SESSION["id_usuario"];

$usuario = new usuarios();
$nombreUsuario = $usuario->obtenerNombreUsuario($id_usuario);

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$paralelo = new paralelos();
$nombreCursoCompleto = $paralelo->obtenerNombreParalelo($id_paralelo);

$pos_comillas = strrpos($nombreCursoCompleto," ",-1);
$nombreCurso = substr($nombreCursoCompleto, 0, $pos_comillas + 1);
$nombreParalelo = substr($nombreCursoCompleto, $pos_comillas + 1, strlen($nombreCursoCompleto) - $pos_comillas - 1);

$periodo = new periodos_evaluacion();
$nombrePeriodo = $periodo->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

$asignatura = new asignaturas();
$nombreAsignatura = $asignatura->obtenerNombreAsignatura($id_asignatura);

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$baseFilename = "Informe-Quimestral";
$objPHPExcel = $objReader->load("../plantillas/".$baseFilename.".xls");

$objPHPExcel->getActiveSheet()->setCellValue('B2', 'PERIODO LECTIVO '.$nombrePeriodoLectivo)
							  ->setCellValue('B4', $nombrePeriodo)
							  ->setCellValue('C6', $nombreAsignatura)
							  ->setCellValue('C7', $nombreCursoCompleto)
							  ->setCellValue('C8', $nombreUsuario);

//____________________________________________________________________

$contadores[0] = 0;$suma_parciales[0] = 0;$promedio_parciales[0] = 0;$suma_examen[0] = 0;$promedio_examen[0] = 0;
$contadores[1] = 0;$suma_parciales[1] = 0;$promedio_parciales[1] = 0;$suma_examen[1] = 0;$promedio_examen[1] = 0;
$contadores[2] = 0;$suma_parciales[2] = 0;$promedio_parciales[2] = 0;$suma_examen[2] = 0;$promedio_examen[2] = 0;
$contadores[3] = 0;$suma_parciales[3] = 0;$promedio_parciales[3] = 0;$suma_examen[3] = 0;$promedio_examen[3] = 0;
$contadores[4] = 0;$suma_parciales[4] = 0;$promedio_parciales[4] = 0;$suma_examen[4] = 0;$promedio_examen[4] = 0;
// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$db = new MySQL();
$estudiantes = $db->consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND es_retirado = 'N'");
$num_total_estudiantes = $db->num_rows($estudiantes);
if($num_total_estudiantes > 0)
{
	while($estudiante = $db->fetch_assoc($estudiantes))
	{
		// Consulta de las calificaciones correspondientes al periodo de evaluacion
		$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
		$num_total_registros = $db->num_rows($aporte_evaluacion);
		if($num_total_registros>0)
		{
			// Aqui calculo los promedios de los parciales
			$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
			while($aporte = $db->fetch_assoc($aporte_evaluacion))
			{
				$contador_aportes++;
				$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
			
				$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
				$total_rubricas = $db->num_rows($rubrica_evaluacion);
				if($total_rubricas>0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
						$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$estudiante["id_estudiante"]." AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
						$num_total_registros = $db->num_rows($qry);
						$rubrica_estudiante = $db->fetch_assoc($qry);
						if($num_total_registros>0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
					} // while($rubricas = $db->fetch_assoc($rubrica_evaluacion))
					// Aqui calculo el promedio del aporte de evaluacion
					$promedio = $suma_rubricas / $contador_rubricas;
					if($contador_aportes <= $total_rubricas - 1) {
						$suma_promedios += $promedio;
					} else {
						$examen_quimestral = $promedio;
					}
					//echo "suma_promedios = " . $suma_promedios . "<br><br>";
				} // if($num_total_registros>0)
				
			} // while($aporte = $db->fetch_assoc($aporte_evaluacion))

			// Aqui se calculan las calificaciones del periodo de evaluacion
			$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
			$ponderado_aportes = 0.8 * $promedio_aportes;
			$ponderado_examen = 0.2 * $examen_quimestral;
			$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
			
			// Calculo de promedios 80% 20% de acuerdo a la escala de calificaciones
			$escala_calificacion = $db->consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
			$cont_escala = 0;
			while ($escala = $db->fetch_assoc($escala_calificacion))
			{
				$nota_minima = $escala["ec_nota_minima"];
				$nota_maxima = $escala["ec_nota_maxima"];
				if ($calificacion_quimestral >= $nota_minima && $calificacion_quimestral <= $nota_maxima) {
					$contadores[$cont_escala] = $contadores[$cont_escala] + 1;
					$suma_parciales[$cont_escala] += $ponderado_aportes;
					$suma_examen[$cont_escala] += $ponderado_examen;
				}
				$cont_escala++;
			}
		}
	}
	// Calculo de los promedios 80% 20% de acuerdo a la escala de calificaciones				
	for($cont=0;$cont<5;$cont++) {
		if($contadores[$cont]>0) {
			$promedio_parciales[$cont]=$suma_parciales[$cont]/$contadores[$cont];
			$promedio_examen[$cont]=$suma_examen[$cont]/$contadores[$cont];
		} else {
			$promedio_parciales[$cont]=0;
			$promedio_examen[$cont]=0;
		}
	}
}

$consulta = $db->consulta("SELECT id_paralelo_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo and id_asignatura = $id_asignatura");
$registro = $db->fetch_assoc($consulta);
$id_paralelo_asignatura = $registro["id_paralelo_asignatura"];
$consulta = $db->consulta("SELECT id_escala_calificaciones, ec_cualitativa, ec_cuantitativa FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden");
$num_total_registros = $db->num_rows($consulta);
$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
if($num_total_registros>0)
{
	$contador = 0; $row = 10;
	while($escalas = $db->fetch_assoc($consulta))
	{
		$contador++; $row++;
		$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
		$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
		$id_escala_calificaciones = $escalas["id_escala_calificaciones"];
		$cualitativa = $escalas["ec_cualitativa"];
		$cuantitativa = $escalas["ec_cuantitativa"];
		$qry = $db->consulta("SELECT re_plan_de_mejora_quimestral FROM sw_recomendaciones_quimestrales WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_periodo_evaluacion = $id_periodo_evaluacion");
		$registro = $db->fetch_assoc($qry);
		$plan_de_mejora = $registro["re_plan_de_mejora_quimestral"];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $cualitativa)
									  ->setCellValue('C'.$row, $cuantitativa)
									  ->setCellValue('D'.$row, $contadores[$contador - 1])
									  ->setCellValue('E'.$row, $promedio_parciales[$contador - 1])
									  ->setCellValue('F'.$row, $promedio_examen[$contador - 1])
									  ->setCellValue('G'.$row, $plan_de_mejora);

	}
}


//____________________________________________________________________

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_periodo_evaluacion . ".xls");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . $baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_periodo_evaluacion . ".xls" . "\"" );
	readfile($baseFilename. $id_usuario . $id_paralelo . $id_asignatura . $id_periodo_lectivo . $id_periodo_evaluacion . ".xls");

?>