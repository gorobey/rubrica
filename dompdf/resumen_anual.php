<?php
require_once("dompdf_config.inc.php");
require_once("../scripts/clases/class.mysql.php");
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.especialidades.php');
require_once('../scripts/clases/class.periodos_lectivos.php');
require_once("../funciones/funciones_sitio.php");
require_once("../scripts/clases/class.funciones.php");

// Aca obtengo la fecha actual del sistema
$fecha_actual = fecha_actual();

// Aqui obtengo los parametros pasados mediante POST
$id_estudiante = $_POST["idestudiante"];
$id_periodo_lectivo = $_POST["idperiodolectivo"];

// Aqui instanciamos la clase funciones
$funciones = new funciones();

// Nombre de la instituci�n educativa
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Nombre del Periodo Lectivo
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

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
  <div class="titulo2">REPORTE DEL PERIODO LECTIVO: '.$nombrePeriodoLectivo.'</div>
  <div class="titulo3">'.$nombreFiguraProfesional.'</div>
  <div class="titulo3">ESTUDIANTE: '.utf8_decode($nombreEstudiante).'</div>
  <div class="titulo3">CURSO: '.$nombreParalelo.'</div><br>
  <div class="cabeceraTabla">
  	<table width="100%" cellspacing=0 cellpadding=0 border=0>
	  <tr class="fuente8">
		<td width="5%" align="left">NRO.</td>
		<td width="35%" align="left">ASIGNATURA</td>';

	$consulta = $db->consulta("SELECT pe_abreviatura, pe_principal FROM sw_periodo_evaluacion WHERE pe_principal = 1 AND id_periodo_lectivo = " . $id_periodo_lectivo);

	$num_total_registros = $db->num_rows($consulta);
	if($num_total_registros>0)
	{
		while($titulo_periodo = $db->fetch_assoc($consulta))
		{
			$html .= '<td width="5%" align="left">' . $titulo_periodo["pe_abreviatura"] . '</td>';
		}
		
		$html .= '<td width="5%" align="left">SUMA</td>';
		$html .= '<td width="5%" align="left">PROM</td>';
		$html .= '<td width="5%" align="left">SUP.</td>';
		$html .= '<td width="5%" align="left">REM.</td>';
		$html .= '<td width="5%" align="left">GRA.</td>';
		$html .= '<td width="5%" align="left">P.F.</td>';
		$html .= '<td width="20%" align="left">OBSERVACION</td>';
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
					}
				}
				// Aqui se calculan las calificaciones del periodo de evaluacion
				if ($examen_quimestral == 0) $contador_sin_examen++;
				$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
				$ponderado_aportes = 0.8 * $promedio_aportes;
				$ponderado_examen = 0.2 * $examen_quimestral;
				$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
				$suma_periodos += $calificacion_quimestral;
				$html .= '<td width="5%" align="left">'.number_format($calificacion_quimestral,2).'</td>';
			} // fin while $periodo_evaluacion
		} // if($num_total_registros>0)
		
		// Calculo la suma y el promedio de los dos quimestres
		$promedio_periodos = $suma_periodos / $contador_periodos;
		$promedio_final = $promedio_periodos;
		$examen_supletorio = 0; $examen_remedial = 0; $examen_de_gracia = 0;
					
		if ($promedio_periodos >= 7) {
			 $equiv_final = "APRUEBA";
		} else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
			$equiv_final = "SUPLETORIO";
			if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
				// Obtencion de la calificacion del examen supletorio
				$examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
				if ($examen_supletorio >= 7) {
				   $promedio_final = 7;
				   $equiv_final = "APRUEBA";
				} else {
				   $equiv_final = "REMEDIAL";
				   if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
						// Obtencion de la calificacion del examen remedial
						$examen_remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
						if ($examen_remedial >= 7) {
							$promedio_final = 7;
							$equiv_final = "APRUEBA";
						} else {
							$equiv_final = "DE GRACIA";
							if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
								// Obtencion de la calificacion del examen remedial
								$examen_de_gracia = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
								if ($examen_de_gracia >= 7) {
									$promedio_final = 7;
									$equiv_final = "APRUEBA";
								} else {
									$equiv_final = "NO APRUEBA";
								}
							}
						}
					}
				}
			} else {
				// Caso contrario se determina si debe dar examen remedial, considerando la fecha de cierre del examen supletorio
				$fecha_actual = new DateTime("now");
				$fecha_cierre = new DateTime($funciones->obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));
				if ($fecha_actual < $fecha_cierre) {
					$equiv_final = "SUPLETORIO";
				} else {
					$equiv_final = "REMEDIAL";
					// Obtencion de la calificacion del examen remedial
					$examen_remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
					if ($examen_remedial >= 7) {
						$promedio_final = 7;
						$equiv_final = "APRUEBA";
					} else {
						$examen_de_gracia = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
						if ($examen_de_gracia >= 7) {
							$promedio_final = 7;
							$equiv_final = "APRUEBA";
						} else {
						   $equiv_final = "NO APRUEBA";
						}
					}    
				}
			}
		} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
			$equiv_final = "REMEDIAL";
			if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
			   // Obtencion de la calificacion del examen remedial
			   $examen_remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
			   if ($examen_remedial >= 7) {
				   $promedio_final = 7;
				   $equiv_final = "APRUEBA";
			   } else {
				   $equiv_final = "DE GRACIA";
				   if ($funciones->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
					   // Obtencion de la calificacion del examen remedial
					   $examen_de_gracia = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
					   if ($examen_de_gracia >= 7) {
						   $promedio_final = 7;
						   $equiv_final = "APRUEBA";
					   } else {
						   $equiv_final = "NO APRUEBA";
					   }				           	
				   }
			   }
			} else {
				// Caso contrario se determina si debe dar examen de gracia, considerando la fecha de cierre del examen remedial
				$fecha_actual = new DateTime("now");
				$fecha_cierre = new DateTime($funciones->obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo));
				if ($fecha_actual < $fecha_cierre) {
					$equiv_final = "REMEDIAL";
				} else {
					$equiv_final = "DE GRACIA";
				}
			}
		} else {
		   $equiv_final = "NO APRUEBA";
		}

		if ($contador_sin_examen > 0) $equiv_final = "SIN EXAMEN";

		$html .= '<td width="5%" align="left">'.number_format($suma_periodos,2).'</td>'; // Suma
		$html .= '<td width="5%" align="left">'.number_format($promedio_periodos,2).'</td>'; // Prom. Quim.
		$html .= '<td width="5%" align="left">'.number_format($examen_supletorio,2).'</td>'; // Supletorio
		$html .= '<td width="5%" align="left">'.number_format($examen_remedial,2).'</td>'; // Remedial
		$html .= '<td width="5%" align="left">'.number_format($examen_de_gracia,2).'</td>'; // Gracia
		$html .= '<td width="5%" align="left">'.number_format($promedio_final,2).'</td>'; // Promedio Final
		$html .= '<td width="20%" align="left">'.$equiv_final.'</td>';
		$html .= "</tr>\n";
		
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
  <div class="titulo3">'.$fecha_actual.'</div>
  </body></html>';

$dompdf = new DOMPDF();
$dompdf->set_paper("a4", "landscape");
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("resumen_anual.pdf");

?>
