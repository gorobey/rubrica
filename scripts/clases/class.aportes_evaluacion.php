<?php

class aportes_evaluacion extends MySQL
{
	
	var $code = "";
	var $id_curso = "";
	var $ap_nombre = "";
	var $ap_abreviatura = "";
	var $ap_tipo = "";
	var $ap_fecha_apertura = "";
	var $ap_fecha_cierre = "";
    var $ap_fecha_inicio = "";
    var $ap_fecha_fin = "";
	var $id_periodo_lectivo = "";
	var $id_periodo_evaluacion = "";
	var $id_asignatura = "";
	
	function existeAporteEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_aporte_evaluacion WHERE ap_nombre = '$nombre'");
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

	function obtenerIdAporteEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE ap_nombre = '$nombre'");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->id_periodo_evaluacion;
	}

	function obtenerTipoAporte()
	{
		$consulta = parent::consulta("SELECT ap_tipo FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = ".$this->code);
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->ap_tipo;
	}
	
	function obtenerAporteEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombreAporteEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_nombre, ap_nombre FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND id_aporte_evaluacion = $id");
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->pe_nombre . " - " . $aporte_evaluacion->ap_nombre;
	}

	function getNombreAporte($id)
	{
		$consulta = parent::consulta("SELECT ap_shortname FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id");
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->ap_shortname;
	}
	
	function listarAportesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($aportes_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador ++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"editarAporteEvaluacion(".$code.")\">editar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function cargarAportesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($aportes_evaluacion = parent::fetch_assoc($consulta))
			{
				$cadena .= "<tr>\n";
				$id = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button onclick='editApoEval(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteApoEval(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='4' align='center'>No se han definido Aportes de Evaluaci&oacute;n para este Per&iacute;odo de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		return $cadena;
	}
	
	function asociarAporteCurso()
	{
		//Primero compruebo que el registro no está insertado...
		$qry = "SELECT * FROM sw_aporte_curso_cierre WHERE id_curso = " . $this->id_curso . " AND id_aporte_evaluacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros > 0)
			return "El Aporte ya se encuentra asociado al Curso...";
		else {
			$qry = "INSERT INTO sw_aporte_curso_cierre (id_aporte_evaluacion, id_curso, ap_estado) VALUES (";
			$qry .= $this->code .",";
			$qry .= $this->id_curso . ",'C')";
			$consulta = parent::consulta($qry);
			$mensaje = "Aporte de Evaluaci&oacute;n asociado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo asociar el Aporte de Evaluaci&oacute;n...Error: " . mysql_error();
		}
		return $mensaje;
	}
	
	function eliminarAsociacionAporteCurso()
	{
		$qry = "DELETE FROM sw_aporte_curso_cierre WHERE id_curso = ". $this->id_curso . " AND id_aporte_evaluacion=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asociacion eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la Asociacion...Error: " . mysql_error();
		return $mensaje;
	}
	
	function listarAportesAsociados()
	{
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion, ap_nombre, ac.ap_estado, ac.id_curso, cu_nombre, es_nombre FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_curso c, sw_especialidad e WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND a.id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND ac.id_curso = " . $this->id_curso . " ORDER BY a.id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($aportes_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador ++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$estado = $aportes_evaluacion["ap_estado"];
				if($estado=='A') {
					$mensaje = 'ABIERTO';
					$accion = 'cerrar';
				} else {
					$mensaje = 'CERRADO';
					$accion = 'reabrir';
				}
				$id_curso = $aportes_evaluacion["id_curso"];
				$curso = "[" . $aportes_evaluacion["es_nombre"] . "] " . $aportes_evaluacion["cu_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"5%\" align=\"left\">$mensaje</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$id_curso.",".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listar_aportes_evaluacion($tipo_aporte)
	{
		if(!isset($tipo_aporte)) $tipo_aporte = 1;
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion, ap_nombre, ac.ap_estado, ac.ap_fecha_apertura, ac.ap_fecha_cierre FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND a.id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND id_curso = " . $this->id_curso . " ORDER BY a.id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($aportes_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador ++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$estado = $aportes_evaluacion["ap_estado"];
				$fecha_apertura = $aportes_evaluacion["ap_fecha_apertura"];
				$fecha_cierre = $aportes_evaluacion["ap_fecha_cierre"];
				if($estado=='A') {
					$mensaje = 'ABIERTO';
					$accion = 'cerrar';
				} else {
					$mensaje = 'CERRADO';
					$accion = 'reabrir';
				}
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";
				if($tipo_aporte==1) {
					$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarAporteEvaluacion(".$code.")\">editar</a></td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAporteEvaluacion(".$code.")\">eliminar</a></td>\n";
				} else if($tipo_aporte==2) {
					$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarAporteEvaluacion(".$code.")\">seleccionar</a></td>\n";
				} else {
					$cadena .= "<td width=\"16%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechaapertura_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_apertura\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_apertura".$contador."\" name=\"calendario_apertura".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechaapertura_".$contador."\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_apertura".$contador."\"
					}
				);
			</script></td>\n";
					$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechacierre_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_cierre\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_cierre".$contador."\" name=\"calendario_cierre".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechacierre_".$contador."\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_cierre".$contador."\"
					}
				);
			</script></td>\n";
					$cadena .= "<td width=\"24%\" align=\"left\">$mensaje</td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"cerrarAporteEvaluacion(".$code.",'".$estado."')\">$accion</a></td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"actualizarAporteEvaluacion(".$code.",'".$name."',document.getElementById('fechaapertura_".$contador."'),document.getElementById('fechacierre_".$contador."'))\">actualizar</a></td>\n";
				}	
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listar_aportes_evaluacion_clubes()
	{
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion, ap_nombre, ac.ap_estado, ac.ap_fecha_apertura, ac.ap_fecha_cierre FROM sw_aporte_evaluacion a, sw_aporte_club_cierre ac WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND a.id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND id_club = " . $this->id_club . " ORDER BY a.id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($aportes_evaluacion = parent::fetch_assoc($consulta))
			{
				$contador ++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$estado = $aportes_evaluacion["ap_estado"];
				$fecha_apertura = $aportes_evaluacion["ap_fecha_apertura"];
				$fecha_cierre = $aportes_evaluacion["ap_fecha_cierre"];
				if($estado=='A') {
					$mensaje = 'ABIERTO';
					$accion = 'cerrar';
				} else {
					$mensaje = 'CERRADO';
					$accion = 'reabrir';
				}
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"16%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechaapertura_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_apertura\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_apertura".$contador."\" name=\"calendario_apertura".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
				<script type=\"text/javascript\">
					Calendar.setup(
						{
						inputField : \"fechaapertura_".$contador."\",
						ifFormat   : \"%Y-%m-%d\",
						button     : \"calendario_apertura".$contador."\"
						}
					);
				</script></td>\n";
				$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechacierre_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_cierre\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_cierre".$contador."\" name=\"calendario_cierre".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
				<script type=\"text/javascript\">
					Calendar.setup(
						{
						inputField : \"fechacierre_".$contador."\",
						ifFormat   : \"%Y-%m-%d\",
						button     : \"calendario_cierre".$contador."\"
						}
					);
				</script></td>\n";
				$cadena .= "<td width=\"24%\" align=\"left\">$mensaje</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"cerrarAporteEvaluacion(".$code.",'".$estado."')\">$accion</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"actualizarAporteEvaluacion(".$code.",'".$name."',document.getElementById('fechaapertura_".$contador."'),document.getElementById('fechacierre_".$contador."'))\">actualizar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarAporteEvaluacion()
	{
            $qry = "INSERT INTO sw_aporte_evaluacion (id_periodo_evaluacion, ap_nombre, ap_abreviatura, ap_tipo) VALUES (";
            $qry .= $this->id_periodo_evaluacion .",";
            $qry .= "'" . $this->ap_nombre . "',";
            $qry .= "'" . $this->ap_abreviatura . "',";
            $qry .= $this->ap_tipo .")";
            $consulta = parent::consulta($qry);
            $mensaje = "Aporte de Evaluaci&oacute;n insertado exitosamente...";
            if (!$consulta)
                    $mensaje = "No se pudo insertar el Aporte de Evaluaci&oacute;n...Error: " . mysql_error();
            return $mensaje;
	}

	function actualizarAporteEvaluacion()
	{
            $qry = "UPDATE sw_aporte_evaluacion SET ";
            $qry .= "ap_nombre = '" . $this->ap_nombre . "', ";
            $qry .= "ap_abreviatura = '" . $this->ap_abreviatura . "',";
            $qry .= "ap_tipo = " . $this->ap_tipo;
            $qry .= " WHERE id_aporte_evaluacion = " . $this->code;
            $consulta = parent::consulta($qry);
            $mensaje = "Aporte de Evaluacion " . $this->ap_nombre . " actualizado exitosamente...";
            if (!$consulta)
                $mensaje = "No se pudo actualizar el Aporte de Evaluacion...Error: " . mysql_error();
            return $mensaje;
	}

	function actualizarFechasAporteEvaluacion()
	{
		$qry = "UPDATE sw_aporte_curso_cierre SET ";
		$qry .= "ap_fecha_apertura = '" . $this->ap_fecha_apertura . "',";
		$qry .= "ap_fecha_cierre = '" . $this->ap_fecha_cierre . "'";
		$qry .= " WHERE id_aporte_evaluacion = " . $this->code;
		$qry .= " AND id_curso = " . $this->id_curso;
		$consulta = parent::consulta($qry);

		// A ver... ahora si voy a actualizar el estado...
        date_default_timezone_set('America/Guayaquil');
		$fechaactual = Date("Y-m-d H:i:s");
		
		if ($fechaactual > $this->ap_fecha_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'A' WHERE id_curso = ". $this->id_curso . " AND id_aporte_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
		}

		if ($fechaactual > $this->ap_fecha_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'C' WHERE id_curso = ". $this->id_curso . " AND id_aporte_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
		}

		$mensaje = "Aporte de Evaluacion " . $this->ap_nombre . " actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el Aporte de Evaluacion...Error: " . mysql_error();
		return $mensaje;
		//return $qry;
	}

	function eliminarAporteEvaluacion()
	{
		$qry = "SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion=". $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros > 0){
			$mensaje = "No se puede eliminar este Aporte de Evaluacion porque tiene Rubricas de Evaluacion relacionadas...";
		} else {
			$qry = "DELETE FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Aporte de Evaluacion eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el Aporte de Evaluacion...Error: " . mysql_error();	
		}
		return $mensaje;
	}

	function cerrarAporteEvaluacion($estado)
	{
		($estado=='C') ? $accion = 'cerrado': $accion = 'reabierto';
		$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = '$estado' WHERE id_aporte_evaluacion=". $this->code . " AND id_curso = ". $this->id_curso;
		$consulta = parent::consulta($qry);
		$mensaje = "Aporte de Evaluaci&oacute;n $accion exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo cerrar o reabrir el Aporte de Evaluacion...Error: " . mysql_error();
		return $mensaje;
	}

	function mostrarEstadoRubrica()
	{
		// Y bueno primero tengo que "actualizar" el estado del aporte
		$consulta = parent::consulta("SELECT ap_fecha_apertura, ap_fecha_cierre, ap_estado FROM sw_aporte_curso_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_curso = " . $this->id_curso);
		if($consulta)
		{
			$aporte = parent::fetch_assoc($consulta);
			$f_apertura = $aporte["ap_fecha_apertura"];
			$f_cierre = $aporte["ap_fecha_cierre"];
			$estado = $aporte["ap_estado"];
		}
		else
		{
			return "FECHAS: NO DEFINIDAS";
		}
		
		// A ver... ahora si voy a actualizar el estado...
		date_default_timezone_set('America/Guayaquil');
		$fechaactual = Date("Y-m-d H:i:s");
		
		if ($fechaactual > $f_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'A' WHERE id_curso = ". $this->id_curso . " AND id_aporte_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
		}

		if ($fechaactual > $f_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'C' WHERE id_curso = ". $this->id_curso . " AND id_aporte_evaluacion=". $this->code;
			$consulta = parent::consulta($qry);
		}

		// Consulto el estado del aporte (A: Abierto; C: Cerrado)
		$consulta = parent::consulta("SELECT ap_estado FROM sw_aporte_curso_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_curso = " . $this->id_curso);
		if($consulta)
		{
			$aporte = parent::fetch_assoc($consulta);
			$estado = $aporte["ap_estado"];
			$estado = ($estado == 'A') ? 'ABIERTO' : 'CERRADO';
			return "ESTADO: " . $estado;
		}
		else
		{
			return "ESTADO: NO DEFINIDO";
		}
	}

	function mostrarFechaCierre()
	{
		// Consulto el estado del aporte (A: Abierto; C: Cerrado)
		$consulta = parent::consulta("SELECT ap_fecha_cierre FROM sw_aporte_curso_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_curso = " . $this->id_curso);
		if($consulta)
		{
			$aporte = parent::fetch_assoc($consulta);
			$fecha = $aporte["ap_fecha_cierre"];
			return "Fecha de cierre: " . $fecha . " 00H:00";
		}
		else
		{
			return "FECHA: NO DEFINIDA";
		}
	}

	function mostrarLeyendasRubricas($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT ru_nombre, 
											 ru_abreviatura
										FROM sw_rubrica_evaluacion r,
										     sw_asignatura a
									   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
									     AND a.id_asignatura = " . $this->id_asignatura
									  ." AND id_aporte_evaluacion = " . $this->code);
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
				while($rubrica = parent::fetch_assoc($consulta))
				{
					$mensaje .= "<td width=\"162px\" align=\"".$alineacion."\">" . $rubrica["ru_abreviatura"] . ": " . $rubrica["ru_nombre"] . ";</td>\n";
				}
		}
		$mensaje .= "<td width=\"150px\" align=\"".$alineacion."\">COMP: COMPORTAMIENTO</td>\n";
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
        
	function mostrarTitulosRubricas($alineacion)
	{
		// Consulto el tipo de aporte (1: aporte parcial; 2: examen quimestral; 3: supletorio/remedial/de gracia)
		$consulta = parent::consulta("SELECT ap_tipo FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = " . $this->code);
		$aporte = parent::fetch_assoc($consulta);
		$tipo_aporte = $aporte["ap_tipo"];
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		if($tipo_aporte==1)
		{
			$consulta = parent::consulta("SELECT ru_abreviatura 
											FROM sw_rubrica_evaluacion r,
											     sw_asignatura a
										   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
										     AND a.id_asignatura = " . $this->id_asignatura
									     . " AND id_aporte_evaluacion = " . $this->code);
			$num_total_registros = parent::num_rows($consulta);
			if($num_total_registros>0)
			{
				while($titulo_rubrica = parent::fetch_assoc($consulta))
				{
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_rubrica["ru_abreviatura"] . "</td>\n";
				}
			}
			$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM</td>\n";
			$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">COMP</td>\n";
		}
		else if($tipo_aporte==2) {
				$consulta = parent::consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
				$num_total_registros = parent::num_rows($consulta);
				if($num_total_registros>0)
				{
				  $contador_aportes = 0;
				  while($titulo_aporte = parent::fetch_assoc($consulta))
				  {
					  $contador_aportes++;
					  if($contador_aportes < $num_total_registros)
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
					  else 
					  {
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM.</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">80%</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">20%</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">NOTA Q.</td>\n";					  }
				  	  }
				  }
        }
		
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function mostrarTitulosRubricasClub($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		// Consulto el tipo de aporte (1: aporte parcial; 2: examen quimestral; 3: supletorio/remedial/de gracia)
		$consulta = parent::consulta("SELECT ap_tipo FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = " . $this->code);
		$aporte = parent::fetch_assoc($consulta);
		$tipo_aporte = $aporte["ap_tipo"];
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		if($tipo_aporte==1)
		{
			$consulta = parent::consulta("SELECT ru_abreviatura FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->code);
			$num_total_registros = parent::num_rows($consulta);
			if($num_total_registros>0)
			{
				while($titulo_rubrica = parent::fetch_assoc($consulta))
				{
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_rubrica["ru_abreviatura"] . "</td>\n";
				}
			}
			$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM</td>\n";
			$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">EQUIV</td>\n";
		}
		else if($tipo_aporte==2) {
				$consulta = parent::consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
				$num_total_registros = parent::num_rows($consulta);
				if($num_total_registros>0)
				{
				  $contador_aportes = 0;
				  while($titulo_aporte = parent::fetch_assoc($consulta))
				  {
					  $contador_aportes++;
					  if($contador_aportes < $num_total_registros)
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
					  else 
					  {
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM.</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">80%</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">20%</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">NOTA Q.</td>\n";
						 $mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">EQUIV</td>\n";
				  	  }
				  }
				}
        }
		
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function mostrarTitulosAportes($alineacion)
	{
		if(!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
		
		//return "SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion;
		
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			$contador_aportes = 0;
			while($titulo_aporte = parent::fetch_assoc($consulta))
			{
				$contador_aportes++;
				if($contador_aportes < $num_total_registros)
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
				else 
				{
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">PROM.</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">80%</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">20%</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"".$alineacion."\">NOTA Q.</td>\n";
				}
			}
        }
		
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

}
?>