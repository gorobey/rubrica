<?php

class asignaturas extends MySQL
{
	
	var $code = "";
	var $id_area = "";
	var $id_tipo_asignatura = "";
	var $as_nombre = "";
	var $as_abreviatura = "";
	var $as_carga_horaria = "";
	var $as_orden = "";
	var $id_usuario = "";
	var $id_periodo_lectivo = "";
	
	function existeAsignatura($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_asignatura WHERE as_nombre = '$nombre'");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	function obtenerIdAsignatura($nombre)
	{
		$consulta = parent::consulta("SELECT id_asignatura FROM sw_asignatura WHERE as_nombre = '$nombre'");
		$asignatura = parent::fetch_object($consulta);
		return $asignatura->id_asignatura;
	}

	function obtenerAsignatura($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_asignatura WHERE id_asignatura = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosAsignatura()
	{
		$consulta = parent::consulta("SELECT a.*, ar_nombre FROM sw_asignatura a, sw_area ar WHERE ar.id_area = a.id_area AND id_asignatura = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function obtenerNombreAsignatura($id)
	{
		$consulta = parent::consulta("SELECT as_nombre FROM sw_asignatura WHERE id_asignatura = $id");
		$asignatura = parent::fetch_object($consulta);
		return $asignatura->as_nombre;
	}

	function listarAsignaturas()
	{
		$consulta = parent::consulta("SELECT * FROM sw_asignatura ORDER BY as_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($asignaturas = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$codigo = $asignaturas["id_asignatura"];
				$nombre = $asignaturas["as_nombre"];
				$abreviatura = $asignaturas["as_abreviatura"];
				$carga = $asignaturas["as_carga_horaria"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$codigo</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$nombre</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$abreviatura</td>\n";
				$cadena .= "<td width=\"18%\" align=\"left\">$carga</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarAsignatura(".$codigo.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsignatura(".$codigo.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido asignaturas para este curso...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarAsignaturas()
	{
		$consulta = parent::consulta("SELECT id_asignatura, as_nombre, as_abreviatura, ar_nombre FROM sw_asignatura a, sw_area ar WHERE ar.id_area = a.id_area ORDER BY ar_nombre, as_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros > 0)
		{
			while($asignatura = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $asignatura["id_asignatura"];
				$area = $asignatura["ar_nombre"];
				$nombre = $asignatura["as_nombre"];
				$abreviatura = $asignatura["as_abreviatura"];
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$area</td>\n";
				$cadena .= "<td>$nombre</td>\n";
				$cadena .= "<td>$abreviatura</td>\n";
				$cadena .= "<td><button onclick='editAsignatura(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteAsignatura(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='5' align='center'>No se han definido asignaturas...</td>\n";
			$cadena .= "</tr>\n";	
		}
		return $cadena;
	}

	function listarAsignaturasDocente($cantidad_registros, $numero_pagina)
	{
		$inicio = ($numero_pagina - 1) * $cantidad_registros;
		$consulta = parent::consulta("SELECT c.id_curso, d.id_paralelo, d.id_asignatura, as_nombre, es_figura, cu_nombre, pa_nombre FROM sw_asignatura a, sw_distributivo d, sw_paralelo pa, sw_curso c, sw_especialidad e WHERE a.id_asignatura = d.id_asignatura AND d.id_paralelo = pa.id_paralelo AND pa.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND d.id_usuario = " . $this->id_usuario . " AND d.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = $inicio;
			while($asignaturas = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_curso = $asignaturas["id_curso"];
				$codigo = $asignaturas["id_paralelo"];
				$id_asignatura = $asignaturas["id_asignatura"];
				$nombre = $asignaturas["as_nombre"];
                                $figura = $asignaturas["es_figura"];
				$curso = $asignaturas["cu_nombre"] . " " . $asignaturas["es_figura"];
				$paralelo = $asignaturas["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"39%\" align=\"left\">$nombre</td>\n";
				$cadena .= "<td width=\"32%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"6%\" align=\"left\">$paralelo</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_form\" align=\"center\"><a href=\"#\" onclick=\"seleccionarParalelo(".$id_curso.",".$codigo.",".$id_asignatura.",'".$nombre."','".$curso."','".$paralelo."')\">Seleccionar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado asignaturas a este docente...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}
	
	function obtenerIdParaleloAsignatura($id_paralelo, $id_asignatura)
	{
		$consulta = parent::consulta("SELECT id_paralelo_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
		$registro = parent::fetch_object($consulta);
		return $registro->id_paralelo_asignatura;
	}
	
	function listarEscalaCalificacionesAnual($id_paralelo, $id_asignatura)
	{
		$contadores[0] = 0;$porcentajes[0] = 0;
		$contadores[1] = 0;$porcentajes[1] = 0;
		$contadores[2] = 0;$porcentajes[2] = 0;
		$contadores[3] = 0;$porcentajes[3] = 0;
		$contadores[4] = 0;$porcentajes[4] = 0;
                // Arrays para almacenar los nombres completos y los promedios quimestrales de los estudiantes de acuerdo a la escala de calificación que corresponda
                for($i = 0;$i < 50;$i++) {
                    $codigo_estudiante[$i] = 0;
                    $promedio_quimestre[$i] = 0;
                }
		// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
		$estudiantes = parent::consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND es_retirado = 'N'");
		$num_total_estudiantes = parent::num_rows($estudiantes);
		if($num_total_estudiantes > 0)
		{
			$cont_estudiante = 0;
                        while($estudiante = parent::fetch_assoc($estudiantes))
			{
				// Consulta de las calificacione correspondientes al a�o lectivo
				$id_estudiante = $estudiante["id_estudiante"];
				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND pe_principal = 1");
				$suma_periodos = 0; $contador_periodos = 0; 
				while($periodo = parent::fetch_assoc($periodo_evaluacion))
				{
					$contador_periodos++;
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
					$num_total_aportes = parent::num_rows($aporte_evaluacion);
					$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
					while($aporte = parent::fetch_assoc($aporte_evaluacion))
					{
						$contador_aportes++;
						$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
						$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
						$suma_rubricas = 0; $contador_rubricas = 0;
						while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
						{
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
							$num_total_registros = parent::num_rows($qry);
							$rubrica_estudiante = parent::fetch_assoc($qry);
							if($num_total_registros>0) {
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}
							$suma_rubricas += $calificacion;
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						//echo $promedio."<br>";
						if($contador_aportes <= $num_total_aportes - 1) {
							$suma_promedios += $promedio;
						} else {
							$examen_quimestral = $promedio;
						}
					} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
					// Aqui se calculan las calificaciones del periodo de evaluacion
					$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
					$ponderado_aportes = 0.8 * $promedio_aportes;
					$ponderado_examen = 0.2 * $examen_quimestral;
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					$suma_periodos += $calificacion_quimestral;
					
				} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_periodos = $suma_periodos / $contador_periodos;
				//echo $promedio_periodos . "<br>";
				
				// Calculo de porcentajes de acuerdo a la escala de calificaciones
				if ($promedio_periodos==10)
					$contadores[0] = $contadores[0] + 1;
				else if ($promedio_periodos >= 9 && $promedio_periodos < 10)
					$contadores[1] = $contadores[1] + 1;
				else if ($promedio_periodos >= 7 && $promedio_periodos < 9)
					$contadores[2] = $contadores[2] + 1;
				else if ($promedio_periodos > 4 && $promedio_periodos < 7)
					$contadores[3] = $contadores[3] + 1;
				else
					$contadores[4] = $contadores[4] + 1;
			} // while($estudiante = parent::fetch_assoc($estudiantes))

			// Calculo de porcentajes de acuerdo a la escala de calificaciones				
			for($cont=0;$cont<5;$cont++)
				$porcentajes[$cont]=$contadores[$cont]/$num_total_estudiantes*100;
	
		} // if($num_total_estudiantes > 0)

		$consulta = parent::consulta("SELECT id_paralelo_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo and id_asignatura = $id_asignatura");
		$registro = parent::fetch_assoc($consulta);
		$id_paralelo_asignatura = $registro["id_paralelo_asignatura"];
		$consulta = parent::consulta("SELECT id_escala_calificaciones, ec_cualitativa, ec_cuantitativa FROM sw_escala_calificaciones ORDER BY ec_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($escalas = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_escala_calificaciones = $escalas["id_escala_calificaciones"];
				$cualitativa = $escalas["ec_cualitativa"];
				$cuantitativa = $escalas["ec_cuantitativa"];
				$qry = parent::consulta("SELECT re_plan_de_mejora_anual FROM sw_recomendaciones_anuales WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura");
				$registro = parent::fetch_assoc($qry);
				$plan_de_mejora = $registro["re_plan_de_mejora_anual"];
				$cadena .= "<td width=\"20%\" align=\"center\">$cualitativa</td>\n";
				$cadena .= "<td width=\"20%\" align=\"center\">$cuantitativa</td>\n";
				$cadena .= "<td width=\"10%\" align=\"center\">".$contadores[$contador - 1]."</td>\n";
				$cadena .= "<td width=\"20%\" align=\"center\">".number_format($porcentajes[$contador - 1],2)."%</td>\n";
				$cadena .= "<td width=\"30%\" align=\"center\"><textarea id=\"txtplandemejora_".$contador."\" >".$plan_de_mejora."</textarea></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		$cadena .= "</table>";	
		return $cadena;
		
	}

	function listarEscalaCalificacionesQuimestrales($id_periodo_evaluacion, $id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		$contadores[0] = 0;$suma_parciales[0] = 0;$promedio_parciales[0] = 0;$suma_examen[0] = 0;$promedio_examen[0] = 0;
		$contadores[1] = 0;$suma_parciales[1] = 0;$promedio_parciales[1] = 0;$suma_examen[1] = 0;$promedio_examen[1] = 0;
		$contadores[2] = 0;$suma_parciales[2] = 0;$promedio_parciales[2] = 0;$suma_examen[2] = 0;$promedio_examen[2] = 0;
		$contadores[3] = 0;$suma_parciales[3] = 0;$promedio_parciales[3] = 0;$suma_examen[3] = 0;$promedio_examen[3] = 0;
		$contadores[4] = 0;$suma_parciales[4] = 0;$promedio_parciales[4] = 0;$suma_examen[4] = 0;$promedio_examen[4] = 0;
                // Arrays para almacenar los nombres completos y los promedios quimestrales de los estudiantes de acuerdo a la escala de calificación que corresponda
                for($i = 0;$i < 50;$i++) {
                    $codigo_estudiante[$i] = 0;
                    $promedio_quimestre[$i] = 0;
                }
		// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
		$estudiantes = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE e.id_estudiante = ep.id_estudiante AND id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres");
		$num_total_estudiantes = parent::num_rows($estudiantes);
                
		if($num_total_estudiantes > 0)
		{
                        //echo $num_total_estudiantes . "<br>";
                        
                        $cont_estudiante = 0;
                        
			while($estudiante = parent::fetch_assoc($estudiantes))
			{
					
                            $id_estudiante = $estudiante["id_estudiante"];

                            $query = parent::consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS calificacion");

                            //echo "SELECT calcular_promedio_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS calificacion";

                            $calificacion = parent::fetch_assoc($query);
                            $calificacion_quimestral = $calificacion["calificacion"];
                                        
                            $promedio_quimestre[$cont_estudiante] = $calificacion_quimestral;
                            $codigo_estudiante[$cont_estudiante] = $id_estudiante;
                            
                            //echo $codigo_estudiante[$cont_estudiante] . " - ";

                            // Calculo de promedios 80% 20% de acuerdo a la escala de calificaciones
                            $escala_calificacion = parent::consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
                            $cont_escala = 0;
                            while ($escala = parent::fetch_assoc($escala_calificacion))
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
                            $cont_estudiante++;
			}

                        //echo "<br>";
                        // Esto es solamente para verificar si está ordenado el vector
                        //for($i=0;$i<$num_total_estudiantes;$i++)
                        //    echo $i . "> " . $codigo_estudiante[$i] . " -";
                        
                        // Procedimiento para ordenar el array de calificaciones de mayor a menor
                        for($i=0;$i<$num_total_estudiantes-1;$i++)
                            for($j=$i+1;$j<$num_total_estudiantes;$j++)
                                if($promedio_quimestre[$i]<$promedio_quimestre[$j]) {
                                    $temporal = $promedio_quimestre[$i];
                                    $temp_codigo = $codigo_estudiante[$i];
                                    $promedio_quimestre[$i] = $promedio_quimestre[$j];
                                    $codigo_estudiante[$i] = $codigo_estudiante[$j];
                                    $promedio_quimestre[$j] = $temporal;
                                    $codigo_estudiante[$j] = $temp_codigo;
                                }
                                
                        //echo "<br>" . $num_total_estudiantes . "<br>";
                        
                        // Esto es solamente para verificar si está ordenado el vector
                        //for($i=0;$i<$num_total_estudiantes;$i++)
                        //    echo $i . "> " . $codigo_estudiante[$i] . " -";
                        
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

		$consulta = parent::consulta("SELECT id_paralelo_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo and id_asignatura = $id_asignatura");
		$registro = parent::fetch_assoc($consulta);
		$id_paralelo_asignatura = $registro["id_paralelo_asignatura"];
		$consulta = parent::consulta("SELECT id_escala_calificaciones, ec_cualitativa, ec_cuantitativa FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($escalas = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_escala_calificaciones = $escalas["id_escala_calificaciones"];
				$cualitativa = $escalas["ec_cualitativa"];
				$cuantitativa = $escalas["ec_cuantitativa"];
				$qry = parent::consulta("SELECT re_plan_de_mejora_quimestral FROM sw_recomendaciones_quimestrales WHERE id_escala_calificaciones = $id_escala_calificaciones AND id_paralelo_asignatura = $id_paralelo_asignatura AND id_periodo_evaluacion = $id_periodo_evaluacion");
				$registro = parent::fetch_assoc($qry);
				$plan_de_mejora = $registro["re_plan_de_mejora_quimestral"];
				$cadena .= "<td width=\"0%\"><input type=\"hidden\" id=\"id_".$id_escala_calificaciones."\" value=\"$id_escala_calificaciones\"></td>\n";
				$cadena .= "<td width=\"20%\" align=\"center\">$cualitativa</td>\n";
				$cadena .= "<td width=\"20%\" align=\"center\">$cuantitativa</td>\n";
				$cadena .= "<td width=\"10%\" align=\"center\">".$contadores[$contador - 1]."</td>\n";
				$cadena .= "<td width=\"10%\" align=\"center\">".number_format($promedio_parciales[$contador - 1],2)."</td>\n";
				$cadena .= "<td width=\"10%\" align=\"center\">".number_format($promedio_examen[$contador - 1],2)."</td>\n";
				$cadena .= "<td width=\"30%\" align=\"center\"><textarea id=\"txtplandemejora_".$contador."\" >".$plan_de_mejora."</textarea></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarEscalaCalificaciones($id_aporte_evaluacion, $id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		// Primero consulto las escalas de calificaciones
		$query = "SELECT ec_cuantitativa, ec_nota_minima, ec_nota_maxima FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden";
		$result = parent::consulta($query);
		$escala = array();
		while($dato = parent::fetch_array($result)){
			$escala[] = array('escala' => $dato['ec_cuantitativa'],
							 'minima' => $dato['ec_nota_minima'],
							 'maxima' => $dato['ec_nota_maxima'],
							 'contador' => 0);
		}
		// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
		$datos = array();
		$estudiantes = parent::consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND id_periodo_lectivo = $id_periodo_lectivo AND es_retirado = 'N'");
		$num_total_estudiantes = parent::num_rows($estudiantes);
		if($num_total_estudiantes > 0)
		{
            $cont_estudiante = 0;
			while($estudiante = parent::fetch_assoc($estudiantes))
			{
				// Consulta de las calificaciones correspondientes al aporte de evaluacion					
				$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
				$num_total_registros = parent::num_rows($rubrica_evaluacion);
				if($num_total_registros>0)
				{
					$suma_rubricas = 0; $contador_rubricas = 0;
					while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
					{
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
						$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$estudiante["id_estudiante"]." AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
						$num_total_registros = parent::num_rows($qry);
						$rubrica_estudiante = parent::fetch_assoc($qry);
						if($num_total_registros>0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						$suma_rubricas += $calificacion;
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$promedio_parcial[$cont_estudiante] = $promedio;
					$codigo_estudiante[$cont_estudiante] = $id_estudiante;
					// Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
					for ($i = 0; $i < count($escala); $i++) {
						$nota_minima = $escala[$i]['minima'];
						$nota_maxima = $escala[$i]['maxima'];
						if ($promedio >= $nota_minima && $promedio <= $nota_maxima) {
							$escala[$i]['contador'] = $escala[$i]['contador'] + 1;
						}
					}
				}
                $cont_estudiante++;
			}
                        
			// Calculo de porcentajes de acuerdo a la escala de calificaciones				
			for($i = 0; $i < count($escala); $i++) {
				$datos[] = array('escala' => $escala[$i]['escala'],
								 'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100);
			}
		}

		return json_encode($datos);

	}

	function cargarAsignaturasDocente()
	{
		$consulta = parent::consulta("SELECT p.id_paralelo, a.id_asignatura, as_nombre, cu_nombre, pa_nombre FROM sw_asignatura a, sw_paralelo_asignatura p, sw_paralelo pa, sw_curso c WHERE a.id_asignatura = p.id_asignatura AND p.id_paralelo = pa.id_paralelo AND pa.id_curso = c.id_curso AND p.id_usuario = " . $this->id_usuario . " AND p.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($asignaturas = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_asignatura = $asignaturas["id_asignatura"];
				$id_paralelo = $asignaturas["id_paralelo"];
				$nombre = $asignaturas["as_nombre"];
				$curso = $asignaturas["cu_nombre"];
				$paralelo = $asignaturas["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_asignatura</td>\n";	
				$cadena .= "<td width=\"34%\" align=\"left\">$nombre</td>\n";
				$cadena .= "<td width=\"32%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"6%\" align=\"left\">$paralelo</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_form\" align=\"center\"><a href=\"#\" onclick=\"personalizarRubrica(".$id_asignatura.",".$id_paralelo.",'".$nombre."','".$curso."','".$paralelo."')\">Editar R&uacute;brica</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado asignaturas a este docente...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarAsignatura()
	{
		$qry = "INSERT INTO sw_asignatura (id_area, id_tipo_asignatura, as_nombre, as_abreviatura) VALUES (";
		$qry .= $this->id_area . ",";
		$qry .= $this->id_tipo_asignatura . ",";
		$qry .= "'" . $this->as_nombre . "',";
		$qry .= "'" . $this->as_abreviatura . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura " . $this->as_nombre . " insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarAsignatura()
	{
		$qry = "UPDATE sw_asignatura SET ";
		$qry .= "id_tipo_asignatura = " . $this->id_tipo_asignatura . ",";
		$qry .= "id_area = " . $this->id_area . ",";
		$qry .= "as_nombre = '" . $this->as_nombre . "',";
		$qry .= "as_abreviatura = '" . $this->as_abreviatura . "'";
		$qry .= " WHERE id_asignatura = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura " . $this->as_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarAsignatura()
	{
		$qry = "SELECT COUNT(id_rubrica_estudiante) AS num_calificaciones FROM sw_rubrica_estudiante WHERE id_asignatura = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_calificaciones = parent::fetch_object($consulta)->num_calificaciones;
		if ($num_calificaciones > 0) {
			$mensaje = "No se puede eliminar la Asignatura porque tiene calificaciones asociadas.";
		} else {
			$qry = "DELETE FROM sw_asignatura WHERE id_asignatura=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Asignatura eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar la Asignatura...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function contarAsignaturasDocente()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_distributivo WHERE id_usuario = " . $this->id_usuario . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
		return json_encode(parent::fetch_assoc($consulta));	
	}
	
	function paginarAsignaturasDocente($cantidad_registros,$numero_pagina,$total_registros)
	{
		$total_paginas = ceil($total_registros / $cantidad_registros);
		$mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarAsignaturasDocente(".$cantidad_registros.",1,".$total_registros.")'> Primero </a> </span>";
		if (($numero_pagina - 1) > 0) {
			$mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarAsignaturasDocente(".$cantidad_registros.",".($numero_pagina-1).",".$total_registros.")'>Anterior</a></span>";
		} else {
			$mensaje .= "<span> < Anterior</span>";
		}
		for ($i=1; $i <= $total_paginas; $i++) {
			if ($numero_pagina == $i) {
				$mensaje .= "<b> P&aacute;gina ".$numero_pagina."</b>";
			} else {
				$mensaje .= "<span class='link_table'> <a href='#' onclick='paginarAsignaturasDocente(".$cantidad_registros.",".$i.",".$total_registros.")'>$i</a></span>";
			}
		}
		if (($numero_pagina+1) <= $total_paginas) {
			$mensaje .= " <span class='link_table'><a href='#' onclick='paginarAsignaturasDocente(".$cantidad_registros.",".($numero_pagina+1).",".$total_registros.")'>Siguiente</a> > </span>";
		} else {
			$mensaje .= " <span>Siguiente</a> > </span>";
		}
		$mensaje .= " <span class='link_table'><a href='#' onclick='paginarAsignaturasDocente(".$cantidad_registros.",".$total_paginas.",".$total_registros.")'>Ultimo</a></span> >>"; 
		return $mensaje;
	}

	function mostrarTitulosPromocion($id_paralelo, $alineacion)
	{
		$consulta = parent::consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_paralelo_asignatura p WHERE a.id_asignatura = p.id_asignatura AND id_paralelo = $id_paralelo");
		$mensaje = "<table id=\"titulos_asignaturas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($titulo_asignatura = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"50px\" align=\"right\">" . $titulo_asignatura["as_abreviatura"] . "</td>\n";
			}
		}
		//$mensaje .= "<td width=\"60px\" align=\"center\">PROM.</td>\n";
		$mensaje .= "<td width=\"80px\" align=\"center\">OBSERVACION</td>\n";
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n";
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
	
	function mostrarTitulosAsignaturas($tipo_reporte,$alineacion)
	{
		if(!isset($tipo_reporte)) $tipo_reporte = 1;
		if(!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = " . $this->code . " ORDER BY ac_orden");
		$mensaje = "<table id=\"titulos_asignaturas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($titulo_asignatura = parent::fetch_assoc($consulta))
			{
				$ancho_col = ($tipo_reporte == 1) ? "60px" : "50px";
				$mensaje .= "<td width=\"$ancho_col\" align=\"$alineacion\">" . $titulo_asignatura["as_abreviatura"] . "</td>\n";
			}
		}
		$mensaje .= "<td width=\"80px\" align=\"center\">OBSERVACION</td>\n"; // Esto es para la columna de observaciones
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n";
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function mostrarTitulosAsignaturasTutor($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = " . $this->code . " ORDER BY ac_orden");
		$mensaje = "<table id=\"titulos_asignaturas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr class=\"cabeceraTabla\">\n";
		$mensaje .= "<td width=\"35px\">Nro.</td>\n";
		$mensaje .= "<td width=\"350px\" align=\"left\">N&oacute;mina</td>\n";
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			while($titulo_asignatura = parent::fetch_assoc($consulta))
			{
				$mensaje .= "<td width=\"50px\" align=\"$alineacion\">" . $titulo_asignatura["as_abreviatura"] . "</td>\n";
			}
		}
		$mensaje .= "<td width=\"50px\" align=\"left\">TOTAL</td>\n";
		$mensaje .= "<td width=\"50px\" align=\"left\">PROM.</td>\n";
		$mensaje .= "<td width=\"50px\" align=\"left\">EQUIV.</td>\n";
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
	
}
?>