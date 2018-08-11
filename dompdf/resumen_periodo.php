<?php
require_once("dompdf_config.inc.php");
require_once("../scripts/clases/class.mysql.php");
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.especialidades.php');
require_once('../scripts/clases/class.periodos_lectivos.php');
require_once('../scripts/clases/class.periodos_evaluacion.php');
require_once("../scripts/clases/class.aportes_evaluacion.php");
require_once("../funciones/funciones_sitio.php");

// Aca obtengo la fecha actual del sistema
$fecha_actual = fecha_actual();

// Aqui obtengo los parametros pasados mediante POST
$id_estudiante = $_POST["idestudiante"];
$id_periodo_lectivo = $_POST["idperiodolectivo"];
$id_periodo_evaluacion = $_POST["idperiodoevaluacion"];

// Nombre de la institucion educativa
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Nombre del Periodo Lectivo
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

// Nombre del Periodo de Evaluacion (Quimestre)
$periodo_evaluacion = new periodos_evaluacion();
$nombrePeriodoEvaluacion = $periodo_evaluacion->obtenerNombrePeriodoEvaluacion($id_periodo_evaluacion);

// Aqui obtengo el id_paralelo del estudiante
$db = new MySQL();
$consulta = $db->consulta("SELECT ep.id_paralelo, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
$registro = $db->fetch_assoc($consulta);
$id_paralelo = $registro["id_paralelo"];

// Nombre del curso y paralelo
$paralelo = new paralelos();
$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

// Nombre de la Figura Profesional
$especialidad = new especialidades();
$nombreFiguraProfesional = $especialidad->obtenerNombreFiguraProfesional($id_paralelo);
	
// Apellidos y Nombres del Estudiante
$nombreEstudiante = $registro["es_apellidos"] . " " . $registro["es_nombres"];

$html =
  '<html>
  <head>
  <style>
    .titulo1 {
	  font-family: Helvetica, Arial, serif;
	  font-size: 20px;
	  margin-top: 10px;
	  text-align: center;
	}
    .titulo2 {
	  font-family: Helvetica, Arial, serif;
	  font-size: 18px;
	  text-align: center;
	}
    .titulo3 {
	  font-family: Helvetica, Arial, serif;
	  font-size: 12px;
	  text-align: center;
	}
	.cabeceraTabla {
	  background-color:#5f5f5f;
	  color:#fff;
	  text-align:center;
	}
	.fuente8 {
	  font-family: Helvetica, Arial, serif;
	  font-size: 10px;
	}
  </style>
  </head>
  <body>
  <div class="titulo1">'.$nombreInstitucion.'</div>
  <div class="titulo2">REPORTE DEL ' . $nombrePeriodoEvaluacion. ': '. $nombrePeriodoLectivo. '</div>
  <div class="titulo3">'.$nombreFiguraProfesional.'</div>
  <div class="titulo3">ESTUDIANTE: '.utf8_decode($nombreEstudiante).'</div>
  <div class="titulo3">CURSO: '.$nombreParalelo.'</div><br>
  <div class="cabeceraTabla">
  	<table class="fuente8" width="100%" cellspacing="0" cellpadding="0" border="0">
	  <tr>
		<td width="5%" align="left">NRO.</td>
		<td width="35%" align="left">ASIGNATURA</td>';

	$consulta = $db->consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");

	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		$contador = 1;
		while($contador < $num_total_registros)
		{
			$contador++;
			$titulo_periodo = $db->fetch_assoc($consulta);
			$html .= '<td width="5%" align="right">' . $titulo_periodo["ap_abreviatura"] . '</td>';
		}
		
		$html .= '<td width="5%" align="right">PROM.</td>';
		$html .= '<td width="5%" align="right">80%</td>';
		$html .= '<td width="5%" align="right">EXAM.</td>';
		$html .= '<td width="5%" align="right">20%</td>';
		$html .= '<td width="25%" align="center">NOTA Q.</td>';
	}		
$html .= '</tr>	
	</table>
  </div>
  <div>';

	// Segundo debo consultar las asignaturas del estudiante
	$asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");

	$html .= '<table class="fuente8" width="100%" cellspacing="0" cellpadding="0" border="0">';
	$contador = 0;
	while($asignatura = $db->fetch_assoc($asignaturas))
	{
		$contador++; $contador_sin_examen = 0;
		$fondolinea = ($contador % 2 == 0) ? "#ccc" : "#f5f5f5";
		$html .= '<tr bgcolor="'.$fondolinea.'">';

		$id_asignatura = $asignatura["id_asignatura"];
		$nom_asignatura = $asignatura["as_nombre"];
		$html .= '<td width="5%" align="left">'.$contador.'</td>';
		$html .= '<td width="35%" align="left">'.$nom_asignatura.'</td>';
		
		//*************************************************************************************
		
		// Aqui se calculan los promedios de cada aporte de evaluacion
		$aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
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
				$promedio = $suma_rubricas / $contador_rubricas;
				if($contador_aportes <= $num_total_registros - 1)
				{
					$html .= '<td width="5%" align="right">'.number_format($promedio,2).'</td>';
					$suma_promedios += $promedio;
				} else {
					$examen_quimestral = $promedio;
				}
			}
			// Aqui debo calcular el ponderado de los promedios parciales
			$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
			$ponderado_aportes = 0.8 * $promedio_aportes;
			$ponderado_examen = 0.2 * $examen_quimestral;
			$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
			$html .= '<td width="5%" align="right">'.number_format($promedio_aportes,2).'</td>';
			$html .= '<td width="5%" align="right">'.number_format($ponderado_aportes,2).'</td>';
			$html .= '<td width="5%" align="right">'.number_format($examen_quimestral,2).'</td>';
			$html .= '<td width="5%" align="right">'.number_format($ponderado_examen,2).'</td>';
			$html .= '<td width="25%" align="center">'.number_format($calificacion_quimestral,2).'</td>';
		}
		
		//*************************************************************************************
		$html .= '</tr>';
	}
	$html .= '</table>';
$html .= '</div>
  <div>
  <table class="fuente8" border="0">
  <tr>
  <td>
  &nbsp;
  </td>
  </tr>
  <tr>
  <td>
  &nbsp;
  </td>
  </tr>
  <tr>
  <td>
  &nbsp;
  </td>
  </tr>
  <tr>
  <td>
  &nbsp;
  </td>
  </tr>
  <tr>
  <td>
  Firma del Tutor: __________________________________________
  </td>
  <td>
  Firma del Estudiante: __________________________________________
  </td>
  </tr>
  </table>
  </div>
  </div>
  <div class="titulo3">'.$fecha_actual.'</div>  
  </body></html>';

$dompdf = new DOMPDF();
$dompdf->set_paper("a4", "landscape");
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("resumen_periodo.pdf");

?>
