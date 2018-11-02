<?php

require_once("class.funciones.php");
require_once("../funciones/funciones_sitio.php");

class paralelos extends MySQL
{
	var $code = "";
	var $id_curso = 0;
	var $pa_nombre = "";
	var $id_usuario = 0;
	var $id_paralelo = 0;
	var $id_estudiante = 0;
	var $id_asignatura = 0;
	var $id_periodo_lectivo = 0;
	var $id_aporte_evaluacion = 0;
	var $id_periodo_evaluacion = 0;

	function truncar($numero, $digitos)
	{
		$truncar = pow(10,$digitos);
		return intval($numero * $truncar) / $truncar;
	}

	function equivalencia($promedio) {
		$record = parent::consulta("SELECT ec_equivalencia"
					. " FROM sw_escala_comportamiento"
					. " WHERE ec_nota_minima <= " . $promedio
					. " AND ec_nota_maxima >= " . $promedio);
		$escala = parent::fetch_assoc($record);
		return $escala["ec_equivalencia"];            
	}

	function obtenerParalelo()
	{
		$consulta = parent::consulta("SELECT id_paralelo, p.id_curso, es_figura, cu_nombre, pa_nombre FROM sw_paralelo p, sw_curso c, sw_especialidad e WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND id_paralelo = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
	
	function obtenerTipoEducacion($id)
	{
		$consulta = parent::consulta("SELECT te_bachillerato FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND p.id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->te_bachillerato;
	}
        
	function obtenerIdCurso($id_paralelo)
	{
		$consulta = parent::consulta("SELECT cu.id_curso FROM sw_curso cu, sw_paralelo pa WHERE cu.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo");
		$resultado = parent::fetch_object($consulta);
		return $resultado->id_curso;
	}

	function obtenerNombreParalelo($id)
	{
		$consulta = parent::consulta("SELECT es_figura, cu_nombre, pa_nombre FROM sw_especialidad es, sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND cu.id_especialidad = es.id_especialidad AND pa.id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->cu_nombre . " \"" . $paralelo->pa_nombre . "\" ". $paralelo->es_figura;
	}

	function obtenerNomCurso($id)
	{
		$consulta = parent::consulta("SELECT cu_nombre, pa_nombre FROM sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}
		
	function obtenerNomParalelo($id)
	{
		$consulta = parent::consulta("SELECT pa_nombre FROM sw_paralelo WHERE id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return " \"" . $paralelo->pa_nombre . "\"";
	}

	function getNombreParalelo($id_paralelo)
	{
		$qry = "SELECT CONCAT(cu_abreviatura, pa_nombre, ' ', es_abreviatura) AS paralelo
				  FROM sw_especialidad e,
				  	   sw_curso c, 
					   sw_paralelo p
				 WHERE e.id_especialidad = c.id_especialidad
				   AND c.id_curso = p.id_curso
				   AND p.id_paralelo = $id_paralelo";
		$consulta = parent::consulta($qry);
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->paralelo;
	}

	function obtenerNombreCurso($id)
	{
		$consulta = parent::consulta("SELECT cu_nombre FROM sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return (parent::fetch_object($consulta)->cu_nombre);
	}

	function getNombreCurso($id)
	{
		$consulta = parent::consulta("SELECT cu_shortname FROM sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return (parent::fetch_object($consulta)->cu_shortname);
	}

	function insertarParalelo()
	{
		$qry = "SELECT secuencial_paralelo_periodo_lectivo(" . $this->id_periodo_lectivo . ") AS secuencial";
		$consulta = parent::consulta($qry);
		$secuencial = parent::fetch_object($consulta)->secuencial;
		$qry = "INSERT INTO sw_paralelo (id_curso, pa_nombre, pa_orden) VALUES (";
		$qry .= $this->id_curso . ",";
		$qry .= "'" . $this->pa_nombre . "',";
		$qry .= $secuencial . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Paralelo insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el Paralelo...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarParalelo()
	{
		$qry = "UPDATE sw_paralelo SET ";
		$qry .= "id_curso = " . $this->id_curso . ",";
		$qry .= "pa_nombre = '" . $this->pa_nombre . "'";
		$qry .= " WHERE id_paralelo = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Paralelo actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Paralelo...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarParalelo()
	{
		$qry = "SELECT COUNT(*) AS num_estudiantes FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_estudiantes = parent::fetch_object($consulta)->num_estudiantes;
		if ($num_estudiantes > 0) {
			$mensaje = "No se puede eliminar el Paralelo porque tiene estudiantes matriculados.";
		} else {
			$qry = "DELETE FROM sw_paralelo WHERE id_paralelo = ". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Paralelo eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el Paralelo...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function listarParalelos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_paralelo WHERE id_curso = " . $this->code . " ORDER BY id_paralelo ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarParalelo(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarParalelo(".$code.",'".$name."')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido paralelos asociados a este curso...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarParalelos()
	{
		$consulta = parent::consulta("SELECT id_paralelo, pa_nombre, es_figura AS especialidad, cu_nombre AS curso FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_tipo_educacion tp WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND tp.id_tipo_educacion = e.id_tipo_educacion AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY pa_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros > 0)
		{
			$contador = 0;
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $paralelo["id_paralelo"];
				$name = $paralelo["pa_nombre"];
				$especialidad = $paralelo["especialidad"];
				$curso = $paralelo["curso"];
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$especialidad</td>\n";
				$cadena .= "<td>$curso</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-warning' onclick=\"editParalelo(".$code.")\">Editar</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"deleteParalelo(".$code.")\">Eliminar</button></td>";
				if($contador == 1) {
					if($num_total_registros > 1) {
						$disabled_subir = "disabled";
						$disabled_bajar = "";
					} else {
						$disabled_subir = "disabled";
						$disabled_bajar = "disabled";
					}
				} else if($contador == $num_total_registros) {
					$disabled_subir = "";
					$disabled_bajar = "disabled";
				} else {
					$disabled_subir = "";
					$disabled_bajar = "";
				}
				$cadena .= "<td><button class='btn btn-block btn-info' onclick=\"subirParalelo(".$code.")\" $disabled_subir>Subir</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-primary' onclick=\"bajarParalelo(".$code.")\" $disabled_bajar>Bajar</button></td>";
				$cadena .= "</tr>\n";			
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='8' align='center'>No se han definido paralelos en este periodo lectivo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		return $cadena;
	}

	function contarAsignaturas($id_paralelo)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular WHERE id_paralelo = $id_paralelo");
		$registro = parent::fetch_assoc($consulta);
		return $registro["total_registros"];
	}

	function listarAsignaturas()
	{
		$consulta = parent::consulta("SELECT id_paralelo_asignatura, cu_nombre, pa_nombre, a.id_asignatura, as_nombre, us_titulo, us_fullname FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_curso c, sw_asignatura_curso ac, sw_asignatura a, sw_usuario u WHERE pa.id_paralelo = p.id_paralelo AND p.id_curso = ac.id_curso AND ac.id_curso = c.id_curso AND pa.id_asignatura = ac.id_asignatura AND ac.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND pa.id_paralelo = " . $this->code . " ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_paralelo_asignatura"];
				$paralelo = $paralelos["cu_nombre"] . " - " . $paralelos["pa_nombre"];
				//$asignatura = "[".$paralelos["id_asignatura"]."]-".$paralelos["as_nombre"];
				$asignatura = $paralelos["as_nombre"];
				$docente = $paralelos["us_titulo"] . " " . $paralelos["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				//$cadena .= "<td width=\"2.5%\">$code</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">$paralelo</td>\n";	
				$cadena .= "<td width=\"40%\" align=\"left\">$asignatura</td>\n";
				$cadena .= "<td width=\"19%\" align=\"left\">$docente</td>\n";
				$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado asignaturas a este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarAsignaturasCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT id_asignatura_curso, c.id_curso, cu_nombre, a.id_asignatura, as_nombre, ac_orden FROM sw_asignatura_curso ac, sw_curso c, sw_asignatura a WHERE ac.id_curso = c.id_curso AND ac.id_asignatura = a.id_asignatura AND ac.id_curso = $id_curso ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_asignatura_curso"];
				$id_curso = $paralelos["id_curso"];
				$curso = $paralelos["cu_nombre"];
				$id_asignatura = $paralelos["id_asignatura"];
				//$asignatura = "[" . $code . "][" . $paralelos["ac_orden"] . "] - " . $paralelos["as_nombre"];
				$asignatura = $paralelos["as_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"38%\" align=\"left\">$curso</td>\n";	
				$cadena .= "<td width=\"39%\" align=\"left\">$asignatura</td>\n";
				if($contador == 1) {
					if($num_total_registros > 1) {
						$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.",".$id_curso.")\">eliminar</a></td>\n";
						$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"bajarAsociacion(".$code.",".$id_curso.")\">bajar</a></td>\n";
					} else {
						$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.",".$id_curso.")\">eliminar</a></td>\n";
					}
				} else if($contador == $num_total_registros) {
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.",".$id_curso.")\">eliminar</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"subirAsociacion(".$code.",".$id_curso.")\">subir</a></td>\n";
				} else {
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.",".$id_curso.")\">eliminar</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"subirAsociacion(".$code.",".$id_curso.")\">subir</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"bajarAsociacion(".$code.",".$id_curso.")\">bajar</a></td>\n";
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar las columnas
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han asociado asignaturas a este curso...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarAsignaturasCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT id_asignatura_curso, 
											 es_figura,
											 c.id_curso, 
											 cu_nombre, 
											 a.id_asignatura, 
											 as_nombre, 
											 ac_orden 
										FROM sw_asignatura_curso ac, 
											 sw_curso c, 
											 sw_especialidad e,
											 sw_asignatura a 
									   WHERE e.id_especialidad = c.id_especialidad
									     AND ac.id_curso = c.id_curso 
										 AND ac.id_asignatura = a.id_asignatura 
										 AND ac.id_curso = $id_curso 
									ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = ""; $contador = 0;
		if($num_total_registros > 0)
		{
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $paralelos["id_asignatura_curso"];
				$id_curso = $paralelos["id_curso"];
				$curso = $paralelos["es_figura"] . " - " . $paralelos["cu_nombre"];
				$id_asignatura = $paralelos["id_asignatura"];
				$asignatura = $paralelos["as_nombre"];
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$curso</td>\n";	
				$cadena .= "<td width=\"39%\" align=\"left\">$asignatura</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarAsociacion(".$code.",".$id_curso.")\">Eliminar</button></td>";
				if($contador == 1) {
					if($num_total_registros > 1) {
						$disabled_subir = "disabled";
						$disabled_bajar = "";
					} else {
						$disabled_subir = "disabled";
						$disabled_bajar = "disabled";
					}
				} else if($contador == $num_total_registros) {
					$disabled_subir = "";
					$disabled_bajar = "disabled";
				} else {
					$disabled_subir = "";
					$disabled_bajar = "";
				}
				$cadena .= "<td><button class='btn btn-block btn-info' onclick=\"subirAsociacion(".$code.",".$id_curso.")\" $disabled_subir>Subir</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-primary' onclick=\"bajarAsociacion(".$code.",".$id_curso.")\" $disabled_bajar>Bajar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='6' align='center'>No se han asociado asignaturas a este curso...</td>\n";
			$cadena .= "</tr>\n";	
		}	
		$datos = array('cadena' => $cadena, 
				       'total_asignaturas' => $contador);
        return json_encode($datos);
	}

	function asociarAsignatura()
	{
		$qry = "INSERT INTO sw_paralelo_asignatura (id_paralelo, id_asignatura, id_usuario, id_periodo_lectivo) VALUES (";
		$qry .= $this->id_paralelo . ",";
		$qry .= $this->id_asignatura . ",";
		$qry .= $this->id_docente . ",";
		$qry .= $this->id_periodo_lectivo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}

	function asociarAsignaturaCurso()
	{
		// Verifico si ya existe la asociacion
		$consulta = parent::consulta("SELECT * FROM sw_asignatura_curso WHERE id_curso = ".$this->id_curso." AND id_asignatura = ".$this->id_asignatura);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$mensaje = "Ya existe la asociacion entre el curso y la asignatura seleccionados.";
		} else {
			// Aqui primero llamo a la funcion almacenada secuencial_curso_asignatura
			$consulta = parent::consulta("SELECT secuencial_curso_asignatura(".$this->id_curso.") AS secuencial");
			$ac_orden = parent::fetch_object($consulta)->secuencial;
			
			$qry = "INSERT INTO sw_asignatura_curso (id_curso, id_asignatura, id_periodo_lectivo, ac_orden) VALUES (";
			$qry .= $this->id_curso . ",";
			$qry .= $this->id_asignatura . ",";
			$qry .= $this->id_periodo_lectivo . ",";
			$qry .= $ac_orden . ")";
			$consulta = parent::consulta($qry);
			$mensaje = "Asignatura asociada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo asociar la Asignatura...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function eliminarAsignatura()
	{
		$qry = "DELETE FROM sw_paralelo_asignatura WHERE id_paralelo_asignatura =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura des-asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}	

	function eliminarAsignaturaCurso()
	{
		// Primero se actualiza el orden (decrementar en uno) de los registros mayores que el actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden - 1 WHERE id_curso = ". $this->id_curso . " AND id_asignatura_curso > ". $this->code;
		$consulta = parent::consulta($qry);
				
		$qry = "DELETE FROM sw_asignatura_curso WHERE id_asignatura_curso =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura des-asociada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar la Asignatura...Error: " . mysql_error();
		return $mensaje;
	}
	
	function subirParalelo()
	{
		// Primero obtengo el "orden" del paralelo actual
		$qry = "SELECT pa_orden AS orden FROM sw_paralelo WHERE id_paralelo = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden anterior
		$qry = "SELECT id_paralelo AS id"
			 . "  FROM sw_paralelo p,"
			 . "       sw_curso c,"
			 . "       sw_especialidad e,"
			 . "       sw_tipo_educacion te"
			 . " WHERE c.id_curso = p.id_curso"
			 . "   AND e.id_especialidad = c.id_especialidad"
			 . "   AND te.id_tipo_educacion = e.id_tipo_educacion"
			 . "   AND te.id_periodo_lectivo = " . $this->id_periodo_lectivo
			 . "   AND pa_orden = $orden - 1";
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (decrementar en uno) del registro actual...
		$qry = "UPDATE sw_paralelo SET pa_orden = pa_orden - 1 WHERE id_paralelo = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (incrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_paralelo SET pa_orden = pa_orden + 1 WHERE id_paralelo = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Paralelo \"subido\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"subir\" el Paralelo...Error: " . mysql_error();
		
		return $mensaje;
	}

	function bajarParalelo()
	{
		// Primero obtengo el "orden" del paralelo actual
		$qry = "SELECT pa_orden AS orden FROM sw_paralelo WHERE id_paralelo = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden posterior
		$qry = "SELECT id_paralelo AS id"
			 . "  FROM sw_paralelo p,"
			 . "       sw_curso c,"
			 . "       sw_especialidad e,"
			 . "       sw_tipo_educacion te"
			 . " WHERE c.id_curso = p.id_curso"
			 . "   AND e.id_especialidad = c.id_especialidad"
			 . "   AND te.id_tipo_educacion = e.id_tipo_educacion"
			 . "   AND te.id_periodo_lectivo = " . $this->id_periodo_lectivo
			 . "   AND pa_orden = $orden + 1";
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (incrementar en uno) del registro actual...
		$qry = "UPDATE sw_paralelo SET pa_orden = pa_orden + 1 WHERE id_paralelo = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (decrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_paralelo SET pa_orden = pa_orden - 1 WHERE id_paralelo = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Paralelo \"bajado\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"bajado\" el Paralelo...Error: " . mysql_error();
		
		return $mensaje;
	}

	function subirAsignaturaCurso()
	{
		// Primero obtengo el "orden" de la asignatura actual
		$qry = "SELECT ac_orden AS orden FROM sw_asignatura_curso WHERE id_asignatura_curso = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden anterior
		$qry = "SELECT id_asignatura_curso AS id FROM sw_asignatura_curso WHERE ac_orden = $orden - 1 AND id_curso = " .$this->id_curso;
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (decrementar en uno) del registro actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden - 1 WHERE id_asignatura_curso = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (incrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden + 1 WHERE id_asignatura_curso = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Asignatura \"subida\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"subir\" la Asignatura...Error: " . mysql_error();
		
		return $mensaje;
	}	

	function bajarAsignaturaCurso()
	{
		// Primero obtengo el "orden" de la asignatura actual
		$qry = "SELECT ac_orden AS orden FROM sw_asignatura_curso WHERE id_asignatura_curso = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden siguiente
		$qry = "SELECT id_asignatura_curso AS id FROM sw_asignatura_curso WHERE ac_orden = $orden + 1 AND id_curso = " .$this->id_curso;
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (incrementar en uno) del registro actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden + 1 WHERE id_asignatura_curso = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (decrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden - 1 WHERE id_asignatura_curso = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Asignatura \"bajada\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"bajar\" la Asignatura...Error: " . mysql_error();
		
		return $mensaje;
	}	

	function listarCalificacionesDeGraciaParalelo($id_paralelo, $id_asignatura, $id_periodo_lectivo, $tipo_supletorio)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, c.id_curso FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_curso c, sw_paralelo pa WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND pa.id_paralelo = p.id_paralelo AND pa.id_curso = c.id_curso AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0; 
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$id_curso = $paralelo["id_curso"];

				// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen de gracia
				$qry = parent::consulta("SELECT contar_remediales_no_aprobados($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS contador");
				$remediales = parent::fetch_assoc($qry);
				$c_remediales = $remediales["contador"];
				
				$qry = parent::consulta("SELECT determinar_asignatura_de_gracia($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS id_asignatura");
				$asignatura = parent::fetch_assoc($qry);
				$vid_asignatura = $asignatura["id_asignatura"];				
				
				if ($c_remediales == 1 && $vid_asignatura == $id_asignatura) {

					$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
					$num_total_registros = parent::num_rows($periodo_evaluacion);
					if($num_total_registros>0)
					{
						$suma_periodos = 0; $contador_periodos = 0; 
						$cadena1 = ""; $cadena2 = "";
						while($periodo = parent::fetch_assoc($periodo_evaluacion))
						{
						
							$contador_periodos++;
							$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					
							$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
							$aporte_evaluacion = parent::consulta($qry);
							//echo $qry . "<br>";
							$num_total_registros = parent::num_rows($aporte_evaluacion);
							if($num_total_registros>0)
							{
								// Aqui calculo los promedios y desplegar en la tabla
								$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
								while($aporte = parent::fetch_assoc($aporte_evaluacion))
								{
									$contador_aportes++;
									$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
									$total_rubricas = parent::num_rows($rubrica_evaluacion);
									if($total_rubricas>0)
									{
										$suma_rubricas = 0; $contador_rubricas = 0;
										while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
										{
											$contador_rubricas++;
											$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
											$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
											$total_registros = parent::num_rows($qry);
											if($total_registros>0) {
												$rubrica_estudiante = parent::fetch_assoc($qry);
												$calificacion = $rubrica_estudiante["re_calificacion"];
											} else {
												$calificacion = 0;
											}
											$suma_rubricas += $calificacion;
										}
									}
									// Aqui calculo el promedio del aporte de evaluacion
									$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
									if($contador_aportes <= $num_total_registros - 1) {
										$suma_promedios += $promedio;
									} else {
										$examen_quimestral = $promedio;
									}
								} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
							} // if($num_total_registros>0)
							// Aqui se calculan las calificaciones del periodo de evaluacion
							$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
							$ponderado_aportes = 0.8 * $promedio_aportes;
							$ponderado_examen = 0.2 * $examen_quimestral;
							$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
							$suma_periodos += $calificacion_quimestral;
							//echo $suma_periodos . "<br>";
							$cadena1 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"periodo_".$contador."\" disabled value=\"".number_format($calificacion_quimestral,2)."\" style=\"color:#666;\" /></td>\n";
						} // fin while $periodo_evaluacion
					} // fin if $periodo_evaluacion
					// Calculo la suma y el promedio de los dos quimestres
					$promedio_periodos = $suma_periodos / $contador_periodos;

					$cadena3 = $cadena1 . "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_periodos_".$contador."\" disabled value=\"".number_format($suma_periodos,2)."\" style=\"color:#666;\" /></td>\n";
					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_periodos_".$contador."\" disabled value=\"".number_format($promedio_periodos,2)."\" style=\"color:#666;\" /></td>\n";
				
					$qry = parent::consulta("SELECT id_rubrica_evaluacion, ac.ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = $id_curso AND r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 4");
					$registro = parent::fetch_assoc($qry);
					$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
					$estado_aporte = $registro["ap_estado"];
				
					$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
					$total_registros = parent::num_rows($qry);
					if($total_registros>0) {
						$rubrica_estudiante = parent::fetch_assoc($qry);
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = 0;
					}

					// Aqui formo el input para ingresar la calificacion del examen de gracia
					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"supletorio_".$contador."\"value=\"".number_format($calificacion,2)."\"";
					if($estado_aporte=='A') {
						$cadena3 .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$id_rubrica_personalizada.")\" /></td>\n";
					} else {
						$cadena3 .= " disabled /></td>\n";
					}

					// Aca calculo la suma del promedio de los quimestres mas el examen supletorio
					$suma_total = $promedio_periodos + $calificacion;
				
					// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
					if ($calificacion < 7) {
						$suma_total = $suma_periodos;
						$promedio_final = $promedio_periodos;
						$observacion = "NO APRUEBA";
					} else {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					}
					
					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_total_".$contador."\" disabled value=\"".number_format($suma_total,2)."\" style=\"color:#666;\" /></td>\n";

					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_final_".$contador."\" disabled value=\"".number_format($promedio_final,2)."\" style=\"color:#666;\" /></td>\n";

					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"fuente8\" size=\"10\" id=\"observacion_".$contador."\" disabled value=\"".$observacion."\" style=\"color:#666;\" /></td>\n";
		
					$contador++;
					$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
					$cadena0 = "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n" . $cadena2;
	
					$cadena0 .= "<td width=\"5%\">$contador</td>\n";	
					$cadena0 .= "<td width=\"5%\">$id_estudiante</td>\n";	
					$cadena0 .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
	
					$cadena2 .= $cadena0 . $cadena3 . "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
					$cadena2 .= "</tr>\n";
					$cadena .= $cadena2;
				}
			}
		}	
		$cadena .= "</table>";
		return $cadena;
	}

    function listarCalificacionesDeGraciaAsignatura($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		// Esta funcion devuelve un conjunto de datos con los estudiantes que tienen que rendir examen remedial
		// --------------------------------------------------
		// Primero obtengo todos los estudiantes del paralelo
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, c.id_curso FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_curso c, sw_paralelo pa WHERE c.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0; 
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				
				// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen de gracia
				$qry = parent::consulta("SELECT contar_remediales_no_aprobados($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS contador");
				$remediales = parent::fetch_assoc($qry);
				$c_remediales = $remediales["contador"];
				
				$qry = parent::consulta("SELECT determinar_asignatura_de_gracia($id_periodo_lectivo,$id_estudiante,$id_paralelo) AS id_asignatura");
				$asignatura = parent::fetch_assoc($qry);
				$vid_asignatura = $asignatura["id_asignatura"];				
				
				if ($c_remediales == 1 && $vid_asignatura == $id_asignatura) {

					$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
					$num_total_registros = parent::num_rows($periodo_evaluacion);
					if($num_total_registros>0)
					{
						$suma_periodos = 0; $contador_periodos = 0; 
						$cadena1 = ""; $cadena2 = "";
						while($periodo = parent::fetch_assoc($periodo_evaluacion))
						{
						
							$contador_periodos++;
							$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					
							$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
							$aporte_evaluacion = parent::consulta($qry);
							//echo $qry . "<br>";
							$num_total_registros = parent::num_rows($aporte_evaluacion);
							if($num_total_registros>0)
							{
								// Aqui calculo los promedios y desplegar en la tabla
								$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
								while($aporte = parent::fetch_assoc($aporte_evaluacion))
								{
									$contador_aportes++;
									$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
									$total_rubricas = parent::num_rows($rubrica_evaluacion);
									if($total_rubricas>0)
									{
										$suma_rubricas = 0; $contador_rubricas = 0;
										while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
										{
											$contador_rubricas++;
											$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
											$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
											$total_registros = parent::num_rows($qry);
											if($total_registros>0) {
												$rubrica_estudiante = parent::fetch_assoc($qry);
												$calificacion = $rubrica_estudiante["re_calificacion"];
											} else {
												$calificacion = 0;
											}
											$suma_rubricas += $calificacion;
										}
									}
									// Aqui calculo el promedio del aporte de evaluacion
									$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
									if($contador_aportes <= $num_total_registros - 1) {
										$suma_promedios += $promedio;
									} else {
										$examen_quimestral = $promedio;
									}
								} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
							} // if($num_total_registros>0)
							// Aqui se calculan las calificaciones del periodo de evaluacion
							$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
							$ponderado_aportes = 0.8 * $promedio_aportes;
							$ponderado_examen = 0.2 * $examen_quimestral;
							$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
							$suma_periodos += $calificacion_quimestral;
							//echo $suma_periodos . "<br>";
							$cadena1 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"periodo_".$contador."\" disabled value=\"".number_format($calificacion_quimestral,2)."\" style=\"color:#666;\" /></td>\n";
						} // fin while $periodo_evaluacion
					} // fin if $periodo_evaluacion
					// Calculo la suma y el promedio de los dos quimestres
					$promedio_periodos = $suma_periodos / $contador_periodos;

					$cadena3 = $cadena1 . "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_periodos_".$contador."\" disabled value=\"".number_format($suma_periodos,2)."\" style=\"color:#666;\" /></td>\n";
					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_periodos_".$contador."\" disabled value=\"".number_format($promedio_periodos,2)."\" style=\"color:#666;\" /></td>\n";
				
					$qry = parent::consulta("SELECT id_rubrica_evaluacion, ac.ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = $id_curso AND r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 4");
					$registro = parent::fetch_assoc($qry);
					$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
					$estado_aporte = $registro["ap_estado"];
				
					$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
					$total_registros = parent::num_rows($qry);
					if($total_registros>0) {
						$rubrica_estudiante = parent::fetch_assoc($qry);
						$calificacion = $rubrica_estudiante["re_calificacion"];
					} else {
						$calificacion = 0;
					}

					// Aqui formo el input para ingresar la calificacion del examen de gracia
					$cadena3 .= "<td width=\"60px\" align=\"left\">".number_format($calificacion,2)." disabled /></td>\n";

					// Aca calculo la suma del promedio de los quimestres mas el examen supletorio
					$suma_total = $promedio_periodos + $calificacion;
				
					// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
					if ($calificacion < 7) {
						$suma_total = $suma_periodos;
						$promedio_final = $promedio_periodos;
						$observacion = "NO APRUEBA";
					} else {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					}
					
					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_total_".$contador."\" disabled value=\"".number_format($suma_total,2)."\" style=\"color:#666;\" /></td>\n";

					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_final_".$contador."\" disabled value=\"".number_format($promedio_final,2)."\" style=\"color:#666;\" /></td>\n";

					$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"fuente8\" size=\"10\" id=\"observacion_".$contador."\" disabled value=\"".$observacion."\" style=\"color:#666;\" /></td>\n";
		
					$contador++;
					$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
					$cadena0 = "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n" . $cadena2;
	
					$cadena0 .= "<td width=\"5%\">$contador</td>\n";	
					$cadena0 .= "<td width=\"5%\">$id_estudiante</td>\n";	
					$cadena0 .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
	
					$cadena2 .= $cadena0 . $cadena3 . "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
					$cadena2 .= "</tr>\n";
					$cadena .= $cadena2;
				}
				
			}

		} else {
			// No se encontraron estudiantes
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesSupletoriosAsignatura($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		// Esta funcion devuelve un conjunto de datos con los estudiantes que tienen que rendir examen supletorio
		// --------------------------------------------------
		// Primero obtengo todos los estudiantes del paralelo
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, c.id_curso FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_curso c, sw_paralelo pa WHERE c.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
		
			$contador = 0; 
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				
				$query = parent::consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_anual");
				$registro = parent::fetch_assoc($query);
				$promedio_anual = $registro["promedio_anual"];
				
				if($promedio_anual >= 5 && $promedio_anual < 7) {
					$contador++;
					
					$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
					$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
					$cadena .= "<td width=\"5%\">$contador</td>\n";
					$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
					$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
					
					$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
					
					$suma = 0;
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$qry = "SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$calificacion = parent::fetch_assoc($resultado);
						$calificacion_quimestral = $calificacion["promedio"];
						
						$cadena .= "<td width=\"60px\" align=\"center\">".number_format($calificacion_quimestral,2)."</td>\n";
						$suma += $calificacion_quimestral;
					}
					
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($suma,2)."</td>\n";
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($promedio_anual,2)."</td>\n";
					
					// Obtener la calificacion del examen supletorio
					$qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";
					$resultado = parent::consulta($qry);
					$calificacion = parent::fetch_assoc($resultado);
					$supletorio = $calificacion["supletorio"];

					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($supletorio,2)."</td>\n";
					$suma_total = $suma + $supletorio;
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($suma,2)."</td>\n";

					// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
					if ($supletorio < 7) {
						$suma_total = $suma;
						$promedio_final = $promedio_anual;
						$observacion = "REMEDIAL";
					} else {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					}
					
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($promedio_final,2)."</td>\n";
					$cadena .= "<td width=\"*\" align=\"left\">$observacion</td>\n";
					
					$cadena .= "</tr>\n";
				}
				
			}

		} else {
			// No se encontraron estudiantes
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCalificacionesRemedialesAsignatura($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		// Esta funcion devuelve un conjunto de datos con los estudiantes que tienen que rendir examen remedial
		// --------------------------------------------------
		// Primero obtengo todos los estudiantes del paralelo
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, c.id_curso FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_curso c, sw_paralelo pa WHERE c.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
		
			$contador = 0; 
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				
				$query = parent::consulta("SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_anual");
				$registro = parent::fetch_assoc($query);
				$promedio_anual = $registro["promedio_anual"];
                                
                                $supletorio = 0;
				
				// Si tiene que rendir examen supletorio y no ha obtenido la calificacion de siete
				if($promedio_anual >= 5 && $promedio_anual < 7) {
					
					$query = parent::consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio");
					$registro = parent::fetch_assoc($query);
					$supletorio = $registro["supletorio"];
				
				}
				
				if(($promedio_anual > 0 && $promedio_anual < 5) || ($promedio_anual >= 5 && $promedio_anual < 7 && $supletorio < 7)) {
					$contador++;
					
					$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
					$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
					$cadena .= "<td width=\"5%\">$contador</td>\n";
					$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
					$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
					
					$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
					
					$suma = 0;
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$qry = "SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$calificacion = parent::fetch_assoc($resultado);
						$calificacion_quimestral = $calificacion["promedio"];
						
						$cadena .= "<td width=\"60px\" align=\"center\">".number_format($calificacion_quimestral,2)."</td>\n";
						$suma += $calificacion_quimestral;
					}
					
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($suma,2)."</td>\n";
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($promedio_anual,2)."</td>\n";
					
					// Obtener la calificacion del examen remedial
					$qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 3) AS remedial";
					$resultado = parent::consulta($qry);
					$calificacion = parent::fetch_assoc($resultado);
					$remedial = $calificacion["remedial"];

					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($remedial,2)."</td>\n";
					$suma_total = $suma + $remedial;
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($suma,2)."</td>\n";

					// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
					if ($remedial < 7) {
						$suma_total = $suma;
						$promedio_final = $promedio_anual;
						$observacion = "NO APRUEBA";
					} else {
						$promedio_final = 7;
						$observacion = "APRUEBA";
					}
					
					$cadena .= "<td width=\"60px\" align=\"center\">".number_format($promedio_final,2)."</td>\n";
					$cadena .= "<td width=\"*\" align=\"left\">$observacion</td>\n";
					
					$cadena .= "</tr>\n";
				}
				
			}

		} else {
			// No se encontraron estudiantes
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";	
		}
		
		$cadena .= "</table>";
		return $cadena;
	}
	
	function listarCalificacionesSupletoriosParalelo($id_paralelo, $id_asignatura, $id_periodo_lectivo, $tipo_supletorio)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 es_apellidos, 
											 es_nombres, 
											 c.id_curso 
										FROM sw_estudiante e, 
											 sw_estudiante_periodo_lectivo p, 
											 sw_curso c, 
											 sw_paralelo pa 
									   WHERE c.id_curso = pa.id_curso 
									     AND pa.id_paralelo = $id_paralelo 
										 AND e.id_estudiante = p.id_estudiante 
										 AND p.id_paralelo = $id_paralelo 
										 AND es_retirado = 'N' 
									ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros > 0)
		{
			$contador = 0; 
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$id_curso = $paralelo["id_curso"];

				// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
				$qry = "SELECT calcular_promedio_anual($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
				$resultado = parent::consulta($qry);
				$calificacion = parent::fetch_assoc($resultado);
				$calificacion_anual = $calificacion["promedio"];
				
				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion 
														  FROM sw_periodo_evaluacion 
														 WHERE id_periodo_lectivo = $id_periodo_lectivo 
														   AND pe_principal = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if($num_total_registros > 0)
				{
					$suma_periodos = 0; $contador_periodos = 0; 
					$cadena1 = ""; $cadena2 = "";
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						
						// Aqui voy a llamar a la funcion almacenada para obtener la calificacion quimestral correspondiente
						$qry = "SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$calificacion = parent::fetch_assoc($resultado);
						$calificacion_quimestral = $calificacion["promedio"];
						//echo $calificacion_quimestral . "<br>";
						$suma_periodos += $calificacion_quimestral;
						//echo $suma_periodos . "<br>";
						$cadena1 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"periodo_".$contador."\" disabled value=\"".number_format($calificacion_quimestral,2)."\" style=\"color:#666;\" /></td>\n";
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_periodos = $suma_periodos / $contador_periodos;
				$promedio_periodos = floor($promedio_periodos*100)/100;

				$cadena3 = $cadena1 . "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_periodos_".$contador."\" disabled value=\"".number_format($suma_periodos,2)."\" style=\"color:#666;\" /></td>\n";
				$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_periodos_".$contador."\" disabled value=\"".number_format($promedio_periodos,2)."\" style=\"color:#666;\" /></td>\n";
				
				$qry = parent::consulta("SELECT id_rubrica_evaluacion, ac.ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = $id_curso AND  r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = $tipo_supletorio");
				$registro = parent::fetch_assoc($qry);
				$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
				$estado_aporte = $registro["ap_estado"];
				
				$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
				$total_registros = parent::num_rows($qry);
				if($total_registros>0) {
					$rubrica_estudiante = parent::fetch_assoc($qry);
					$calificacion = $rubrica_estudiante["re_calificacion"];
				} else {
					$calificacion = 0;
				}

				// Aqui formo el input para ingresar la calificacion del examen supletorio
				$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"supletorio_".$contador."\"value=\"".number_format($calificacion,2)."\"";
				if($estado_aporte=='A') {
					$cadena3 .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$id_rubrica_personalizada.")\" /></td>\n";
				} else {
					$cadena3 .= " disabled /></td>\n";
				}

				// Aca calculo la suma del promedio de los quimestres mas el examen supletorio
				$suma_total = $promedio_periodos + $calificacion;
				
				// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
				if ($calificacion < 7) {
					$suma_total = $suma_periodos;
					$promedio_final = $promedio_periodos;
					$observacion = ($tipo_supletorio == 2) ? "REMEDIAL" : "NO APRUEBA";
				} else {
					$promedio_final = 7;
					$observacion = "APRUEBA";
				}
					
				$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_total_".$contador."\" disabled value=\"".number_format($suma_total,2)."\" style=\"color:#666;\" /></td>\n";

				$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_final_".$contador."\" disabled value=\"".number_format($promedio_final,2)."\" style=\"color:#666;\" /></td>\n";

				$cadena3 .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"fuente8\" size=\"10\" id=\"observacion_".$contador."\" disabled value=\"".$observacion."\" style=\"color:#666;\" /></td>\n";
		
				if ($tipo_supletorio == 2) { //Supletorio
					if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
						$contador++;
						$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
						$cadena0 = "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n" . $cadena2;
	
						$cadena0 .= "<td width=\"5%\">$contador</td>\n";	
						$cadena0 .= "<td width=\"5%\">$id_estudiante</td>\n";	
						$cadena0 .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
	
						$cadena2 .= $cadena0 . $cadena3 . "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
						$cadena2 .= "</tr>\n";
						$cadena .= $cadena2;
					}
				} else if ($tipo_supletorio == 3) {
					// Aqui obtengo la calificacion del examen supletorio si existe
					$qry = parent::consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio");
					$registro = parent::fetch_assoc($qry);
					$examen_supletorio = $registro["supletorio"];
					
					if (($promedio_periodos > 0 && $promedio_periodos < 5) || ($promedio_periodos >= 5 && $promedio_periodos < 7 && $examen_supletorio < 7)) {
						$contador++;
						$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
						$cadena0 = "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n" . $cadena2;
	
						$cadena0 .= "<td width=\"5%\">$contador</td>\n";	
						$cadena0 .= "<td width=\"5%\">$id_estudiante</td>\n";	
						$cadena0 .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
	
						$cadena2 .= $cadena0 . $cadena3 . "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
						$cadena2 .= "</tr>\n";
						$cadena .= $cadena2;
					}
				}
				$cadena2 = "";
				$cadena1 = "";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}
	
	function listarResumenAnual($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$retirado = $paralelo["es_retirado"];
				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"5%\" align=\"left\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\" align=\"left\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if($num_total_registros>0)
				{
					$suma_periodos = 0; $contador_periodos = 0;
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					
						$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
						$aporte_evaluacion = parent::consulta($qry);
						//echo $qry . "<br>";
						$num_total_registros = parent::num_rows($aporte_evaluacion);
						if($num_total_registros>0)
						{
							// Aqui calculo los promedios y desplegar en la tabla
							$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
							while($aporte = parent::fetch_assoc($aporte_evaluacion))
							{
								$contador_aportes++;
								$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = parent::num_rows($rubrica_evaluacion);
								if($total_rubricas>0)
								{
									$suma_rubricas = 0; $contador_rubricas = 0;
									while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
									{
										$contador_rubricas++;
										$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
										$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
										$total_registros = parent::num_rows($qry);
										if($total_registros>0) {
											$rubrica_estudiante = parent::fetch_assoc($qry);
											$calificacion = $rubrica_estudiante["re_calificacion"];
										} else {
											$calificacion = 0;
										}
										$suma_rubricas += $calificacion;
									}
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
								if($contador_aportes <= $num_total_registros - 1) {
									$suma_promedios += $promedio;
								} else {
									$examen_quimestral = $promedio;
								}
							}
						}
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $this->truncar($suma_promedios / ($contador_aportes - 1),2);
						$ponderado_aportes = $this->truncar(0.8 * $promedio_aportes,2);
						$ponderado_examen = $this->truncar(0.2 * $examen_quimestral,2);
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						$suma_periodos += $calificacion_quimestral;
						$cadena .= "<td width=\"5%\" align=\"left\">".number_format($calificacion_quimestral,2)."</td>\n";
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_quimestral = $this->truncar($suma_periodos / $contador_periodos,2);
				$promedio_periodos = $promedio_quimestral;
				$examen_supletorio = 0; $examen_remedial = 0; $examen_de_gracia = 0;
				if($promedio_periodos >= 5 && $promedio_periodos < 7) {
					// Obtencion de la calificacion del examen supletorio
					$examen_supletorio = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
					if ($examen_supletorio >= 7)
						$promedio_periodos = 7;
					else {
						// Obtencion de la calificacion del examen remedial
						$examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
						if ($examen_remedial >= 7)
							$promedio_periodos = 7;
						else {
							$examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
							if($examen_de_gracia >= 7) // Examen de Gracia
								$promedio_periodos = 7;
						}
					}
				} else if($promedio_periodos > 0 && $promedio_periodos < 5) {
					// Obtencion de la calificacion del examen remedial
					$examen_remedial = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
					if ($examen_remedial >= 7)
						$promedio_periodos = 7;
					else {
						$examen_de_gracia = funciones::obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
						if($examen_de_gracia >= 7) // Examen de Gracia
							$promedio_periodos = 7;
					}
				}
				$equiv_final = equiv_anual($promedio_periodos, $retirado, $terminacion);
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($suma_periodos,2)."</td>\n"; // Suma
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($promedio_quimestral,2)."</td>\n"; // Prom. Quim.
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($examen_supletorio,2)."</td>\n"; // Supletorio
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($examen_remedial,2)."</td>\n"; // Remedial
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($examen_de_gracia,2)."</td>\n"; // Gracia
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($promedio_periodos,2)."</td>\n"; // Promedio Final
				$cadena .= "<td width=\"20%\" align=\"left\">$equiv_final</td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

    function listarComportamientoAnual($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$retirado = $paralelo["es_retirado"];
				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"5%\" align=\"left\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\" align=\"left\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if($num_total_registros>0)
				{
					$suma_periodos = 0; $contador_periodos = 0;
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
                                                
						$qry = parent::consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
						//echo "SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion<br>";
                        $registro = parent::fetch_assoc($qry);
						$promedio = ceil($registro["calificacion"]);
                        //echo $promedio." ";
						$suma_periodos += $promedio;
						//Obtengo el correlativo de la escala de comportamiento
						$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cualitativo = $equivalencia["ec_equivalencia"];
						$cadena .= "<td width=\"5%\" align=\"left\">".$promedio_cualitativo."</td>\n";
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_quimestral = $this->truncar($suma_periodos / $contador_periodos,2);

                //$equiv_final = equiv_anual($promedio_periodos, $retirado, $terminacion);
				$cadena .= "<td width=\"5%\" align=\"left\">".number_format($suma_periodos,2)."</td>\n"; // Suma
				$cadena .= "<td width=\"10%\" align=\"left\">".number_format($promedio_quimestral,2)."</td>\n"; // Prom. Quim.
				$cadena .= "<td width=\"35%\" align=\"left\">".equiv_comportamiento($promedio_quimestral)."</td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarPromocionesSecretaria($id_paralelo, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador_gracia = 0;
		if($num_total_registros>0)
		{
			$contador = 0; 
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador_cero = 0;
				$contador_supletorios = 0;
				$contador_remediales = 0;
				
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";	
				$cadena .= "<td width=\"250px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if($total_asignaturas>0)
				{

					while ($asignatura = parent::fetch_assoc($asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if($num_total_registros>0)
						{
							$suma_periodos = 0; $contador_periodos = 0; 
							while($periodo = parent::fetch_assoc($periodo_evaluacion))
							{
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						
								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if($num_total_registros>0)
								{
									// Aqui calculo los promedios
									$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
									while($aporte = parent::fetch_assoc($aporte_evaluacion))
									{
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if($total_rubricas>0)
										{
											$suma_rubricas = 0; $contador_rubricas = 0;
											while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
											{
												$contador_rubricas++;
												$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
												$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
												$total_registros = parent::num_rows($qry);
												if($total_registros>0) {
													$rubrica_estudiante = parent::fetch_assoc($qry);
													$calificacion = $rubrica_estudiante["re_calificacion"];
												} else {
													$calificacion = 0;
												}
												$suma_rubricas += $calificacion;
											}
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del periodo de evaluacion
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;
						$observacion = "";

						if($promedio_periodos == 0) $contador_cero++;
						else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 2");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
							
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

							if($qry) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
								if($calificacion >= 7) {
									$promedio_periodos = 7;
								} else {
									$contador_supletorios++;
								}
								
							}

						}
						else if($promedio_periodos > 0 && $promedio_periodos < 5) {
							// Obtencion de la calificacion del examen remedial

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 3");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
							
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

							if($qry) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
								if($calificacion >= 7) {
									$promedio_periodos = 7;
								} else {
									$contador_remediales++;
								}
								
							}
						}
						
						$cadena .= "<td width=\"50px\" align=\"right\">".number_format($promedio_periodos,2)."</td>\n";
						if($contador_cero > 0 || $contador_supletorios > 0 || $contador_remediales > 0) {
							if($contador_remediales == 1) { // Tiene que dar examen de gracia
								// Obtencion de la calificacion del examen de gracia
								$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = 4");
								$registro = parent::fetch_assoc($qry);
								$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
								
								$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
	
								if($qry) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["re_calificacion"];
									if($calificacion >= 7) {
										$promedio_periodos = 7;
										$observacion = "APRUEBA";
									}
								}
							} else $observacion = "NO APRUEBA";
						} else $observacion = "APRUEBA";
						
					} // while ($asignatura = parent::fetch_assoc($asignaturas))
					
				}

				$cadena .= "<td width=\"80px\">$observacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCalificacionesSupletoriosSecretaria($id_paralelo, $id_periodo_lectivo, $tipo_supletorio)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0; 
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";	
				$cadena .= "<td width=\"250px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if($total_asignaturas>0)
				{

					while ($asignatura = parent::fetch_assoc($asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if($num_total_registros>0)
						{
							$suma_periodos = 0; $contador_periodos = 0; 
							while($periodo = parent::fetch_assoc($periodo_evaluacion))
							{
							
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						
								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								//echo $qry . "<br>";
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if($num_total_registros>0)
								{
									// Aqui calculo los promedios
									$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
									while($aporte = parent::fetch_assoc($aporte_evaluacion))
									{
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if($total_rubricas>0)
										{
											$suma_rubricas = 0; $contador_rubricas = 0;
											while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
											{
												$contador_rubricas++;
												$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
												$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
												$total_registros = parent::num_rows($qry);
												if($total_registros>0) {
													$rubrica_estudiante = parent::fetch_assoc($qry);
													$calificacion = $rubrica_estudiante["re_calificacion"];
												} else {
													$calificacion = 0;
												}
												$suma_rubricas += $calificacion;
											}
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del periodo de evaluacion
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;

						if($promedio_periodos >= 7) {

							$cadena .= "<td width=\"50px\" align=\"right\">&nbsp;</td>\n";

						} else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = $tipo_supletorio");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
							
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
							$total_registros = parent::num_rows($qry);
							if($total_registros>0) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}

							// Aqui desplego la calificacion del examen supletorio
							$cadena .= "<td width=\"50px\" align=\"right\">".number_format($calificacion,2)."</td>\n";

						} else if($promedio_periodos < 5) {
						
							$observacion = ($promedio_periodos == 0) ? "S/N" : "REMED.";
							$cadena .= "<td width=\"50px\" align=\"right\">$observacion</td>\n";
						}
												
					} // while ($asignatura = parent::fetch_assoc($asignaturas))

					$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

				} // if($total_asignaturas>0)
			
			} // while($paralelo = parent::fetch_assoc($consulta))
		} // if($num_total_registros>0)
		$cadena .= "</table>";	
		return $cadena;
	}
	
	function listarCuadroFinal($id_periodo_lectivo, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador_gracia = 0;
		if($num_total_registros>0)
		{
			$contador = 0; 
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador_cero = 0;
				$contador_supletorios = 0;
				$contador_remediales = 0;
				
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";	
				$cadena .= "<td width=\"250px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if($total_asignaturas>0)
				{

					while ($asignatura = parent::fetch_assoc($asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if($num_total_registros>0)
						{
							$suma_periodos = 0; $contador_periodos = 0; 
							while($periodo = parent::fetch_assoc($periodo_evaluacion))
							{
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						
								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if($num_total_registros>0)
								{
									// Aqui calculo los promedios
									$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
									while($aporte = parent::fetch_assoc($aporte_evaluacion))
									{
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if($total_rubricas>0)
										{
											$suma_rubricas = 0; $contador_rubricas = 0;
											while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
											{
												$contador_rubricas++;
												//$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
												$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = " . $rubricas["id_rubrica_evaluacion"]);
												//$total_registros = parent::num_rows($qry);
												//if($total_registros>0) {
												if(parent::num_rows($qry) > 0) {
													$rubrica_estudiante = parent::fetch_assoc($qry);
													$calificacion = $rubrica_estudiante["re_calificacion"];
												} else {
													$calificacion = 0;
												}
												$suma_rubricas += $calificacion;
											}
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del quimestre
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;
						$observacion = "";

						if($promedio_periodos == 0) $contador_cero++;
						else if($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							if (obtenerExamenSupRemGracia(2) >= 7)
								$promedio_periodos = 7;
							else
								$contador_supletorios++;

						}
						else if($promedio_periodos > 0 && $promedio_periodos < 5) {
							// Obtencion de la calificacion del examen remedial

							if (obtenerExamenSupRemGracia(3) >= 7)
								$promedio_periodos = 7;
							else
								$contador_remediales++;

						}
						
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_periodos,2)."</td>\n";
						if($contador_cero > 0 || $contador_supletorios > 0 || $contador_remediales > 0) {
							if($contador_remediales == 1) { // Tiene que dar examen de gracia
								// Obtencion de la calificacion del examen de gracia

								if (obtenerExamenSupRemGracia(4) >= 7) {
									$promedio_periodos = 7;
									$observacion = "APRUEBA";
								} else {
									$observacion = "NO APRUEBA";
								}
							} 
						} else {
							$observacion = "APRUEBA";
						}
						
					} // while ($asignatura = parent::fetch_assoc($asignaturas))
					
				}

				$cadena .= "<td width=\"80px\">$observacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}
	
	function listarCalificacionesAnuales($id_periodo_lectivo, $id_paralelo, $cantidad_registros, $numero_pagina)
	{
		$inicio = ($numero_pagina - 1) * $cantidad_registros;
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = $inicio;

			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				
				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";
				$retirado = $paralelo["es_retirado"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";	
				$cadena .= "<td width=\"240px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

				$contador_no_aprueba=0; 
				$contador_supletorio=0; 
				$contador_remedial=0; 
				
				$asignaturas = parent::consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = parent::num_rows($asignaturas);
				if($total_asignaturas>0)
				{
					
					while ($asignatura = parent::fetch_assoc($asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];
						
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if($num_total_registros>0)
						{
							$suma_periodos = 0; $contador_periodos = 0;
							while($periodo = parent::fetch_assoc($periodo_evaluacion))
							{
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								//echo $qry . "<br>";
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if($num_total_registros>0)
								{
									// Aqui calculo los promedios y desplegar en la tabla
									$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
									while($aporte = parent::fetch_assoc($aporte_evaluacion))
									{
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if($total_rubricas>0)
										{
											$suma_rubricas = 0; $contador_rubricas = 0;
											while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
											{
												$contador_rubricas++;
												$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
												$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
												$total_registros = parent::num_rows($qry);
												if($total_registros>0) {
													$rubrica_estudiante = parent::fetch_assoc($qry);
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
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // fin while $periodo_evaluacion
						} // fin if $periodo_evaluacion
						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;
						if($promedio_periodos==0)
							$contador_no_aprueba++;
						else if($promedio_periodos>0 && $promedio_periodos<5)
							$contador_remedial++;
						else if($promedio_periodos>=5 && $promedio_periodos<7)
							$contador_supletorio++;
						// Aqui desplegar el promedio de los quimestres
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_periodos,2)."</td>\n";
					}
				}
				$observacion="";
				if($retirado == "S")
					$observacion="RETIRAD" . $terminacion;
				else if($contador_no_aprueba>0)
					$observacion="NO APRUEBA";
				else if($contador_supletorio>0)
					$observacion="SUPLETORIO";
				else if($contador_remedial>0)
					$observacion="REMEDIAL";
				else $observacion="APRUEBA";
				$cadena .= "<td width=\"80px\" align=\"left\">&nbsp;&nbsp;$observacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";	
			}
			
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCalificacionesConsolidado($id_periodo_evaluacion, $cantidad_registros, $numero_pagina)
	{
		$inicio = ($numero_pagina - 1) * $cantidad_registros;
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = " . $this->id_paralelo . " ORDER BY es_apellidos, es_nombres ASC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = $inicio;
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";	
				$cadena .= "<td width=\"240px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = " . $this->id_paralelo . " ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if($total_asignaturas>0)
				{
					while ($asignatura = parent::fetch_assoc($asignaturas))
					{
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];
						$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
						$num_total_registros = parent::num_rows($aporte_evaluacion);
						if($num_total_registros>0)
						{
							// Aqui calculo los promedios y desplegar en la tabla
							$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
							while($aporte = parent::fetch_assoc($aporte_evaluacion))
							{
								$contador_aportes++;
								$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = parent::num_rows($rubrica_evaluacion);
								if($total_rubricas>0)
								{
									$suma_rubricas = 0; $contador_rubricas = 0;
									while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
									{
										$contador_rubricas++;
										$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
										$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
										$total_registros = parent::num_rows($qry);
										if($total_registros>0) {
											$rubrica_estudiante = parent::fetch_assoc($qry);
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
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						$cadena .= "<td width=\"50px\" align=\"right\">".number_format($calificacion_quimestral,2)."</td>\n";
					}					
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";	
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarCalificacionesParalelo($id_periodo_evaluacion, $tipo_reporte)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 c.id_curso, 
											 di.id_paralelo, 
											 di.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
										FROM sw_distributivo di, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
									   WHERE di.id_paralelo = ep.id_paralelo 
									     AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
										 AND ep.id_estudiante = e.id_estudiante 
										 AND di.id_asignatura = a.id_asignatura 
										 AND di.id_paralelo = p.id_paralelo 
										 AND p.id_curso = c.id_curso 
										 AND di.id_paralelo = " . $this->id_paralelo 
									 . " AND di.id_asignatura = " . $this->id_asignatura 
									 . " AND es_retirado <> 'S' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$id_curso = $paralelos["id_curso"];
				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
				$asignatura = $paralelos["as_nombre"];
				$curso = $paralelos["cu_nombre"];
				$paralelo = $paralelos["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT a.id_aporte_evaluacion, 
															  ap_tipo, 
															  ac.ap_estado 
														 FROM sw_periodo_evaluacion p, 
														      sw_aporte_evaluacion a, 
															  sw_aporte_curso_cierre ac 
														WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
														  AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
														  AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
														  AND ac.id_curso = $id_curso");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				if($num_total_registros>0)
				{
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_aportes = 0; $contador_aportes = 0; $suma_promedios = 0;
					while($aporte = parent::fetch_assoc($aporte_evaluacion))
					{
						$contador_aportes++;
						$tipo_aporte = $aporte["ap_tipo"];
						$estado_aporte = $aporte["ap_estado"];
						$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion 
																  FROM sw_rubrica_evaluacion r,
																  	   sw_asignatura a
																 WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
																   AND a.id_asignatura = $id_asignatura
																   AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
						$total_rubricas = parent::num_rows($rubrica_evaluacion);
						if($total_rubricas>0)
						{
							$suma_rubricas = 0; $contador_rubricas = 0;
							while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
							{
								$contador_rubricas++;
								$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
								$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
								$total_registros = parent::num_rows($qry);
								if($total_registros>0) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["re_calificacion"];
								} else {
									$calificacion = 0;
								}
								$suma_rubricas += $calificacion;
							}
						}
						$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
						if($contador_aportes < $num_total_registros)
						{
							if($tipo_reporte==1)
								$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio,2)."</td>";
							else
								$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_".$contador."\" disabled value=\"".number_format($promedio,2)."\" style=\"color:#666;\" /></td>\n";
							$suma_promedios += $promedio;
						} else {
							$examen_quimestral = $promedio;
						}
					}
					// Aqui debo calcular el ponderado de los promedios parciales
					$promedio_aportes = $this->truncar($suma_promedios / ($contador_aportes - 1),2);
					$ponderado_aportes = $this->truncar(0.8 * $promedio_aportes,2);
					$ponderado_examen = $this->truncar(0.2 * $examen_quimestral,2);
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
					if($tipo_reporte==1) 
					{
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($promedio_aportes,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_aportes,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($examen_quimestral,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($ponderado_examen,2)."</td>";
						$cadena .= "<td width=\"60px\" align=\"right\">".number_format($calificacion_quimestral,2)."</td>";
					}
					else
					{
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedioaportes_".$contador."\" disabled value=\"".number_format($promedio_aportes,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_".$contador."\" disabled value=\"".number_format($ponderado_aportes,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"examenquimestral_".$contador."\" value=\"".number_format($examen_quimestral,2)."\"";
						if($estado_aporte=='A') {
							$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$id_rubrica_evaluacion.",".$tipo_aporte.")\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_".$contador."\" disabled value=\"".number_format($ponderado_examen,2)."\" style=\"color:#666;\" /></td>\n";
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_".$contador."\" disabled value=\"".number_format($calificacion_quimestral,2)."\" style=\"color:#666;\" /></td>\n";
					}
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarEstudiantesComportamientoAnual($id_periodo_lectivo, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"45%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				// Aqui va el codigo para determinar el total, el promedio y la equivalencia de cada quimestre

				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND pe_principal = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if($num_total_registros > 0)
				{
					$suma_promedio = 0;
					while($periodo = parent::fetch_assoc($periodo_evaluacion))
					{
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$resultado = parent::consulta("SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio");

						#echo "SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio<br>";

						$registro = parent::fetch_assoc($resultado);
						$promedio = ceil($registro["promedio"]);
						$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cualitativo = $equivalencia["ec_equivalencia"];

						$cadena .= "<td width=\"15%\">" . $promedio_cualitativo . "</td>";

						$suma_promedio += $promedio;
					}
					$promedio_anual = ceil($suma_promedio / $num_total_registros);
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_anual");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td width=\"20%\">" . $promedio_cualitativo . "</td>";
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function listarEstudiantesCompInspector($id_paralelo, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"45%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				// Aqui se consultan los comportamientos de parciales ingresados por el Inspector
				$aportes_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND ap_tipo = 1");

				$suma_comportamiento_inspector = 0; $contador_aportes_evaluacion = 0;
				while($aporte_evaluacion = parent::fetch_assoc($aportes_evaluacion))
				{
					$contador_aportes_evaluacion++;
					$id_aporte_evaluacion = $aporte_evaluacion["id_aporte_evaluacion"];
					$query = parent::consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
					$inspectores = parent::fetch_assoc($query);
					$promedio_inspector = $inspectores["co_calificacion"];
					if ($promedio_inspector=="") {
						$promedio_cuantitativo = 0;
					} else {
						$query = parent::consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cuantitativo = $equivalencia["ec_correlativa"];
					}
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_cuantitativo");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td width=\"10%\" align=\"center\">" . $promedio_cualitativo . "</td>\n";
					$suma_comportamiento_inspector += $promedio_cuantitativo; 
				}

				$promedio_comportamiento = ceil($suma_comportamiento_inspector / $contador_aportes_evaluacion);
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
				$equivalencia = parent::fetch_assoc($query);
				$promedio_cualitativo = $equivalencia["ec_equivalencia"];
				$cadena .= "<td width=\"20%\" align=\"center\">" . $promedio_cualitativo . "</td>\n";

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		return $cadena;
	}
	
	function listarEstudiantesComportamiento($id_paralelo, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"45%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				// Aqui se consultan los indices de evaluacion para el comportamiento
				
				$asignaturas = parent::consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = parent::num_rows($asignaturas);
					
				if ($total_asignaturas > 0)
				{
					$suma_comp_asignatura = 0;
					$contador_asignaturas = 0;
						
					while($asignatura = parent::fetch_assoc($asignaturas))
					{
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];
								
						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = parent::consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
						$calificaciones = parent::fetch_assoc($calificaciones);
						$calificacion = ceil($calificaciones["calificacion"]);

						$suma_comp_asignatura += $calificacion;
					}
							
					$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td width=\"10%\" align=\"left\">" . $promedio_cualitativo . "</td>\n";
				}

				//Aqui viene el calculo del comportamiento por parte del inspector
				$aportes_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND ap_tipo = 1");

				$suma_comportamiento_inspector = 0; $contador_aportes_evaluacion = 0;
				while($aporte_evaluacion = parent::fetch_assoc($aportes_evaluacion))
				{
					$contador_aportes_evaluacion++;
					$id_aporte_evaluacion = $aporte_evaluacion["id_aporte_evaluacion"];
					$query = parent::consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
					$inspectores = parent::fetch_assoc($query);
					$promedio_inspector = $inspectores["co_calificacion"];
					if ($promedio_inspector=="") {
						$promedio_cuantitativo = 0;
					} else {
						$query = parent::consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cuantitativo = $equivalencia["ec_correlativa"];
					}
					$suma_comportamiento_inspector += $promedio_cuantitativo; 
				}

				$comportamiento_inspector = ceil($suma_comportamiento_inspector / $contador_aportes_evaluacion);
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $comportamiento_inspector");
				$equiv_comp_inspector = parent::fetch_assoc($query);
				$cadena .= "<td width=\"10%\" align=\"left\">" . $equiv_comp_inspector["ec_equivalencia"] . "</td>\n";

				$promedio_comportamiento = ceil(($promedio_comp + $comportamiento_inspector) / 2.0);
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
				$equiv_comp_promedio = parent::fetch_assoc($query);
				$cadena .= "<td width=\"10%\" align=\"left\">" . $equiv_comp_promedio["ec_equivalencia"] . "</td>\n";

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}		

	function listarEstudiantesComportamientoParciales($id_paralelo, $id_aporte_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"45%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				
				// Aqui se calcula el promedio del comportamiento asignado por los docentes
				
				$asignaturas = parent::consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = parent::num_rows($asignaturas);
					
				if ($total_asignaturas > 0)
				{
					$suma_comp_asignatura = 0;
					$contador_asignaturas = 0;
						
					while($asignatura = parent::fetch_assoc($asignaturas))
					{
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];
								
						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = parent::consulta("SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
						if(parent::num_rows($calificaciones) > 0) {
							$calificaciones = parent::fetch_assoc($calificaciones);
							$calificacion = $calificaciones["co_calificacion"];
						} else 
							$calificacion = 0;
						$suma_comp_asignatura += $calificacion;
					}
				}

				$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
				//Aqui despliego el promedio del comportamiento asentado por los docentes
				//Primero obtengo la equivalencia del promedio en forma cualitativa
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
				$resultado = parent::fetch_assoc($query);
				$comp_docentes = $resultado["ec_equivalencia"];

				$cadena .= "<td width=\"10%\" align=\"left\">" . $comp_docentes . "</td>\n";
                                
				//Aqui obtnego el comportamiento del inspector
				$query = parent::consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
				$inspectores = parent::fetch_assoc($query);
				$promedio_inspector = $inspectores["co_calificacion"];
				if ($promedio_inspector=='') {
					$promedio_inspector = 'S/N';
					$promedio_cuantitativo = 0;
				} else {
					$query = parent::consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cuantitativo = $equivalencia["ec_correlativa"];
				}
				$cadena .= "<td width=\"10%\" align=\"left\">" . $promedio_inspector . "</td>\n";
				
				$total = $promedio_comp + $promedio_cuantitativo;
				$promedio = ceil($total / 2);
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
				$equivalencia = parent::fetch_assoc($query);
				$promedio_cualitativo = $equivalencia["ec_equivalencia"];

				$cadena .= "<td width=\"30%\" align=\"left\">" . $promedio_cualitativo . "</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}		

	function listarEstudiantesComportamientoPorDocente($id_paralelo, $id_periodo_evaluacion, $id_asignatura)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($estudiante = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"35px\">$contador</td>\n";	
				$cadena .= "<td width=\"350px\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				// Aqui se consultan los indices de evaluacion para el comportamiento
				$query = "SELECT id_indice_evaluacion FROM sw_indice_evaluacion_def ORDER BY ie_orden";
				$comportamiento = parent::consulta($query);
				$total_indices = parent::num_rows($comportamiento);
				if($total_indices>0)
				{
					$total = 0; $contador_indices = 0;
					while($indice = parent::fetch_assoc($comportamiento))
					{
						$id_indice_evaluacion = $indice["id_indice_evaluacion"];
						// Aqui se consulta la calificacion del comportamiento
						$query = "SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_periodo_evaluacion = $id_periodo_evaluacion AND id_indice_evaluacion = $id_indice_evaluacion AND id_asignatura = $id_asignatura";
						$registro = parent::consulta($query);
						if(parent::num_rows($registro)>0)
						{
							$nota_comportamiento = parent::fetch_assoc($registro);
							$calificacion = $nota_comportamiento["co_calificacion"];
						} else {
							$calificacion = 0;
						}
						$total += $calificacion; $contador_indices++;
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_".$contador."\" class=\"inputPequenio\" value=\"".number_format($calificacion,2)."\" onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$id_indice_evaluacion.")\" /></td>\n";
					}	
				}
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"total_".$contador."\" class=\"inputPequenio\" value=\"".number_format($total,2)."\" disabled style=\"color:#666;\" /></td>\n";

				$promedio = $total / $contador_indices;
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"promedio_".$contador."\" class=\"inputPequenio\" value=\"".number_format($promedio,2)."\" disabled style=\"color:#666;\" /></td>\n";

				$equivalencia = equiv_comportamiento($promedio);
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"equivalencia_".$contador."\" class=\"inputPequenio\" value=\"$equivalencia\" disabled style=\"color:#666;\" /></td>\n";

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}		

	function listarCalificacionesAsignatura()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
										     c.id_curso, 
											 d.id_paralelo, 
											 d.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 es_retirado, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
										FROM sw_distributivo d, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
									   WHERE d.id_paralelo = ep.id_paralelo 
									     AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
										 AND ep.id_estudiante = e.id_estudiante 
										 AND d.id_asignatura = a.id_asignatura 
										 AND d.id_paralelo = p.id_paralelo 
										 AND p.id_curso = c.id_curso 
										 AND d.id_paralelo = " . $this->id_paralelo 
									 . " AND d.id_asignatura = " . $this->id_asignatura 
									 . " AND es_retirado <> 'S' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$retirado = $paralelos["es_retirado"];
				$id_curso = $paralelos["id_curso"];
				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
				$asignatura = $paralelos["as_nombre"];
				$curso = $paralelos["cu_nombre"];
				$paralelo = $paralelos["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				//Aca vamos a obtener el estado del aporte de evaluacion
				$query = parent::consulta("SELECT ac.ap_estado
											 FROM sw_aporte_evaluacion a,
												  sw_aporte_curso_cierre ac,
												  sw_curso c,
												  sw_paralelo p
											WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
											  AND c.id_curso = p.id_curso
											  AND ac.id_curso = p.id_curso
											  AND p.id_paralelo = " . $this->id_paralelo .
											" AND a.id_aporte_evaluacion = ". $this->id_aporte_evaluacion);
				$estado_aporte = parent::fetch_object($query)->ap_estado;
				//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
				if($id_tipo_asignatura==1){ //CUANTITATIVA
					// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
					$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion, 
																   ap_tipo, 
																   ac.ap_estado 
															  FROM sw_rubrica_evaluacion r, 
															       sw_aporte_evaluacion a, 
																   sw_aporte_curso_cierre ac,
																   sw_asignatura asignatura
															 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
															   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
															   AND asignatura.id_asignatura = $id_asignatura
															   AND r.id_aporte_evaluacion = " . $this->id_aporte_evaluacion
														   . " AND ac.id_curso = " . $this->id_curso);
					$num_total_registros = parent::num_rows($rubrica_evaluacion);
					if($num_total_registros>0)
					{
						$suma_rubricas = 0; $contador_rubricas = 0;
						while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
						{
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$tipo_aporte = $rubricas["ap_tipo"];
							$estado_aporte = $rubricas["ap_estado"];
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
							$num_total_registros = parent::num_rows($qry);
							$rubrica_estudiante = parent::fetch_assoc($qry);
							if($num_total_registros>0) {
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}
							$suma_rubricas += $calificacion;
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"".number_format($calificacion,2)."\" disabled /></td>\n";
						}
						$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_".$contador."\" disabled value=\"".number_format($promedio,2)."\" style=\"color:#666;\" /></td>\n";
					} else {
						$cadena .= "<tr>\n";	
						$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
						$cadena .= "</tr>\n";
					}
				}else{ //CUALITATIVA
					// Aqui va el codigo para obtener la calificacion cualitativa
					$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_aporte_evaluacion = ".$this->id_aporte_evaluacion);
					$num_total_registros = parent::num_rows($qry);
					$cualitativa = parent::fetch_assoc($qry);
					if($num_total_registros>0) {
						$calificacion = $cualitativa["rc_calificacion"];
					} else {
						$calificacion = '';
					}
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"".$calificacion."\" disabled /></td>\n";
				}
				
				// Aqui va el codigo para obtener el comportamiento
				$qry = parent::consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_aporte_evaluacion = ".$this->id_aporte_evaluacion);
				$num_total_registros = parent::num_rows($qry);
				$comportamiento = parent::fetch_assoc($qry);
				if($num_total_registros>0) {
					$calificacion = $comportamiento["co_cualitativa"];
				} else {
					$calificacion = '';
				}
				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"".$calificacion."\" disabled /></td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarEstudiantesParalelo()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
										     c.id_curso, 
											 d.id_paralelo, 
											 d.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 es_retirado, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
										FROM sw_distributivo d, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
									   WHERE d.id_paralelo = ep.id_paralelo 
									     AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
										 AND ep.id_estudiante = e.id_estudiante 
										 AND d.id_asignatura = a.id_asignatura 
										 AND d.id_paralelo = p.id_paralelo 
										 AND p.id_curso = c.id_curso 
										 AND d.id_paralelo = " . $this->id_paralelo 
									 . " AND d.id_asignatura = " . $this->id_asignatura 
									 . " AND es_retirado <> 'S' ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($paralelos = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$retirado = $paralelos["es_retirado"];
				$id_curso = $paralelos["id_curso"];
				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
				$asignatura = $paralelos["as_nombre"];
				$curso = $paralelos["cu_nombre"];
				$paralelo = $paralelos["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
				$cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";
				//Aca vamos a obtener el estado del aporte de evaluacion
				$query = parent::consulta("SELECT ac.ap_estado
											 FROM sw_aporte_evaluacion a,
												  sw_aporte_curso_cierre ac,
												  sw_curso c,
												  sw_paralelo p
											WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
											  AND c.id_curso = p.id_curso
											  AND ac.id_curso = p.id_curso
											  AND p.id_paralelo = " . $this->id_paralelo .
											" AND a.id_aporte_evaluacion = ". $this->id_aporte_evaluacion);
				$estado_aporte = parent::fetch_object($query)->ap_estado;
				//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
				if($id_tipo_asignatura==1){ //CUANTITATIVA
					// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
					$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion, 
																   ap_tipo, 
																   ac.ap_estado 
															  FROM sw_rubrica_evaluacion r, 
															       sw_aporte_evaluacion a, 
																   sw_aporte_curso_cierre ac,
																   sw_asignatura asignatura
															 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
															   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
															   AND asignatura.id_asignatura = $id_asignatura
															   AND r.id_aporte_evaluacion = " . $this->id_aporte_evaluacion
														   . " AND ac.id_curso = " . $this->id_curso);
					$num_total_registros = parent::num_rows($rubrica_evaluacion);
					if($num_total_registros>0)
					{
						$suma_rubricas = 0; $contador_rubricas = 0;
						while($rubricas = parent::fetch_assoc($rubrica_evaluacion))
						{
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$tipo_aporte = $rubricas["ap_tipo"];
							$estado_aporte = $rubricas["ap_estado"];
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_rubrica_personalizada = ".$id_rubrica_evaluacion);
							$num_total_registros = parent::num_rows($qry);
							$rubrica_estudiante = parent::fetch_assoc($qry);
							if($num_total_registros>0) {
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}
							$suma_rubricas += $calificacion;
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_".$contador."\" class=\"inputPequenio\" value=\"".number_format($calificacion,2)."\"";
							if($estado_aporte=='A' && $retirado=='N') {
								$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$id_rubrica_evaluacion.",".$tipo_aporte.")\" /></td>\n";
							} else {
								$cadena .= " disabled /></td>\n";
							}
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_".$contador."\" disabled value=\"".$this->truncar($promedio,2)."\" style=\"color:#666;\" /></td>\n";
					} else {
						$cadena .= "<tr>\n";	
						$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
						$cadena .= "</tr>\n";
					}
				}else{ //CUALITATIVA
					// Aqui va el codigo para obtener la calificacion cualitativa
					$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_aporte_evaluacion = ".$this->id_aporte_evaluacion);
					$num_total_registros = parent::num_rows($qry);
					$cualitativa = parent::fetch_assoc($qry);
					if($num_total_registros>0) {
						$calificacion = $cualitativa["rc_calificacion"];
					} else {
						$calificacion = '';
					}
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_".$contador."\" class=\"inputPequenio\" value=\"".$calificacion."\"";
					if($estado_aporte=='A' && $retirado=='N') {
						$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$this->id_aporte_evaluacion.")\" /></td>\n";
					} else {
						$cadena .= " disabled /></td>\n";
					}
				}
				
				// Aqui va el codigo para obtener el comportamiento
				$qry = parent::consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = ".$paralelos["id_estudiante"]." AND id_paralelo = ".$this->id_paralelo." AND id_asignatura = ".$this->id_asignatura. " AND id_aporte_evaluacion = ".$this->id_aporte_evaluacion);
				$num_total_registros = parent::num_rows($qry);
				$comportamiento = parent::fetch_assoc($qry);
				if($num_total_registros>0) {
					$calificacion = $comportamiento["co_cualitativa"];
				} else {
					$calificacion = '';
				}
				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"comportamiento_".$contador."\" class=\"inputPequenio\" value=\"".$calificacion."\"";
				if($estado_aporte=='A' && $retirado=='N') {
					$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionComportamiento(this,".$id_estudiante.",".$id_paralelo.",".$id_asignatura.",".$this->id_aporte_evaluacion.")\" /></td>\n";
				} else {
					$cadena .= " disabled /></td>\n";
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";	
		return $cadena;
	}
}
?>