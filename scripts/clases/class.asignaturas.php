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
		$consulta = parent::consulta("SELECT c.id_curso, 
											 d.id_paralelo, 
											 d.id_asignatura, 
											 as_nombre, 
											 es_figura, 
											 cu_nombre, 
											 pa_nombre 
										FROM sw_asignatura a, 
											 sw_distributivo d, 
											 sw_paralelo pa, 
											 sw_curso c, 
											 sw_especialidad e 
									   WHERE a.id_asignatura = d.id_asignatura 
									     AND d.id_paralelo = pa.id_paralelo 
										 AND pa.id_curso = c.id_curso 
										 AND c.id_especialidad = e.id_especialidad 
										 AND d.id_usuario = " . $this->id_usuario 
									 . " AND d.id_periodo_lectivo = " . $this->id_periodo_lectivo 
									 . " AND as_curricular = 1"
								. " ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC LIMIT $inicio, $cantidad_registros");
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
	
	function listarEscalaCalificacionesAnual($id_periodo_lectivo, $id_paralelo, $id_asignatura)
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
		$estudiantes = parent::consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND es_retirado = 'N'");
		$num_total_estudiantes = parent::num_rows($estudiantes);
		if($num_total_estudiantes > 0)
		{
            while($estudiante = parent::fetch_assoc($estudiantes))
			{
				$id_estudiante = $estudiante["id_estudiante"];
				$query = parent::consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
                $calificacion = parent::fetch_assoc($query);
				$calificacion_anual = $calificacion["calificacion"];
				
				// Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
				for ($i = 0; $i < count($escala); $i++) {
					$nota_minima = $escala[$i]['minima'];
					$nota_maxima = $escala[$i]['maxima'];
					if ($calificacion_anual >= $nota_minima && $calificacion_anual <= $nota_maxima) {
						$escala[$i]['contador'] = $escala[$i]['contador'] + 1;
					}
				}
			} // while($estudiante = parent::fetch_assoc($estudiantes))

			// Calculo de porcentajes de acuerdo a la escala de calificaciones				
			for($i = 0; $i < count($escala); $i++) {
				if($escala[$i]['contador'] == 1)
					$terminacion = "";
				else
					$terminacion = "s";
				$datos[] = array('escala' => $escala[$i]['escala']." (".$escala[$i]['contador']." estudiante".$terminacion.")",
								 'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100);
			}
				
		} // if($num_total_estudiantes > 0)

		return json_encode($datos);
		
	}

	function listarEscalaCalificacionesQuimestrales($id_periodo_evaluacion, $id_paralelo, $id_asignatura, $id_periodo_lectivo)
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
			while($estudiante = parent::fetch_assoc($estudiantes))
			{	
                $id_estudiante = $estudiante["id_estudiante"];
                $query = parent::consulta("SELECT calcular_promedio_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS calificacion");
                $calificacion = parent::fetch_assoc($query);
				$calificacion_quimestral = $calificacion["calificacion"];
				
				// Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
				for ($i = 0; $i < count($escala); $i++) {
					$nota_minima = $escala[$i]['minima'];
					$nota_maxima = $escala[$i]['maxima'];
					if ($calificacion_quimestral >= $nota_minima && $calificacion_quimestral <= $nota_maxima) {
						$escala[$i]['contador'] = $escala[$i]['contador'] + 1;
					}
				}
			}

			// Calculo de porcentajes de acuerdo a la escala de calificaciones				
			for($i = 0; $i < count($escala); $i++) {
				if($escala[$i]['contador'] == 1)
					$terminacion = "";
				else
					$terminacion = "s";
				$datos[] = array('escala' => $escala[$i]['escala']." (".$escala[$i]['contador']." estudiante".$terminacion.")",
								 'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100);
			}
		}

		return json_encode($datos);

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
			while($estudiante = parent::fetch_assoc($estudiantes))
			{
				// Consulta de las calificaciones correspondientes al aporte de evaluacion					
				$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
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
					// Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
					for ($i = 0; $i < count($escala); $i++) {
						$nota_minima = $escala[$i]['minima'];
						$nota_maxima = $escala[$i]['maxima'];
						if ($promedio >= $nota_minima && $promedio <= $nota_maxima) {
							$escala[$i]['contador'] = $escala[$i]['contador'] + 1;
						}
					}
				}
			}
                        
			// Calculo de porcentajes de acuerdo a la escala de calificaciones				
			for($i = 0; $i < count($escala); $i++) {
				if($escala[$i]['contador'] == 1)
					$terminacion = "";
				else
					$terminacion = "s";
				$datos[] = array('escala' => $escala[$i]['escala']." (".$escala[$i]['contador']." estudiante".$terminacion.")",
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
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros 
										FROM sw_distributivo di,
										     sw_asignatura a 
									   WHERE a.id_asignatura = di.id_asignatura
										 AND id_usuario = " . $this->id_usuario . " AND id_periodo_lectivo = " . $this->id_periodo_lectivo
									 . " AND as_curricular = 1");
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

	function obtenerNombreArea($id_asignatura)
	{
		$qry = "SELECT ar_nombre FROM sw_area ar, sw_asignatura a WHERE ar.id_area = a.id_area AND id_asignatura = $id_asignatura";
		$consulta = parent::consulta($qry);
		$area = parent::fetch_object($consulta);
		return $area->ar_nombre;
	}
	
}
?>