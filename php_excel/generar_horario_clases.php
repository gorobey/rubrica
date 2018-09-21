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

require_once 'php_excel/Classes/PHPExcel/IOFactory.php';
require_once 'scripts/clases/class.mysql.php';
require_once 'scripts/clases/class.periodos_lectivos.php';

// Variables de sesión a utilizar	
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Obtener el nombre del periodo lectivo actual
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$baseFilename = "Leccionario.xlsx";
$objPHPExcel = $objReader->load("./templates/" . $baseFilename);

$objPHPExcel->setActiveSheetIndex(0);

// Aqui va el codigo para obtener la matriz del horario general de clases del periodo lectivo actual
$db = new MySQL();
$horarios = $db->consulta("SELECT hc_nombre, 
                                 ds_nombre,
                                 hc_hora_inicio,
                                 CONCAT(cu_shortname,\" \",'\"',pa_nombre,'\"') AS paralelo,
                                 as_shortname,
                                 us_shortname
                            FROM sw_hora_clase hc, 
                                 sw_dia_semana ds, 
                                 sw_hora_dia hd, 
                                 sw_curso c, 
                                 sw_paralelo p, 
                                 sw_horario ho,
                                 sw_asignatura a,
                                 sw_distributivo d,
                                 sw_usuario u
                           WHERE hc.id_hora_clase = hd.id_hora_clase 
                             AND ds.id_dia_semana = hd.id_dia_semana 
                             AND c.id_curso = p.id_curso
                             AND p.id_paralelo = ho.id_paralelo
                             AND hd.id_hora_clase = ho.id_hora_clase
                             AND hd.id_dia_semana = ho.id_dia_semana
                             AND a.id_asignatura = ho.id_asignatura
                             AND a.id_asignatura = d.id_asignatura
                             AND u.id_usuario = d.id_usuario
                             AND p.id_paralelo = d.id_paralelo
                             AND d.id_paralelo = ho.id_paralelo
                             AND ds.id_periodo_lectivo = $id_periodo_lectivo");
$num_total_registros = $db->num_rows($horarios);
if($num_total_registros > 0)
{
	$row = 2; // fila base
	while($horario = $db->fetch_assoc($horarios))
	{
		$Orden = $horario["hc_nombre"];
		$Dia = $horario["ds_nombre"];
		$Hora = $horario["hc_hora_inicio"];
        $Curso = $horario["paralelo"];
        $Materia = $horario["as_shortname"];
        $Docente = $horario["us_shortname"];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $Orden)
                                      ->setCellValue('C'.$row, $Dia)
                                      ->setCellValue('D'.$row, $Hora)
                                      ->setCellValue('E'.$row, $Curso)
                                      ->setCellValue('F'.$row, $Materia)
                                      ->setCellValue('G'.$row, $Docente);
	
		$row++;
	}
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("./php_excel/Leccionario " . $nombrePeriodoLectivo . ".xlsx");

// Codigo para abrir la caja de dialogo Abrir o Guardar Archivo

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . "Leccionario " . $nombrePeriodoLectivo . ".xlsx" . "\"" );
	readfile("Leccionario " . $nombrePeriodoLectivo . ".xlsx");

?>