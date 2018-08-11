<?php

class mensajes extends MySQL
{
	var $code = "";
	var $me_texto = "";
	var $me_fecha = "";
	var $id_usuario = "";
	var $id_perfil = "";
	
	function obtenerNumeroMensajes()
	{
		// Funcion que retorna una cadena con el numero de mensajes
		$consulta = parent::consulta("SELECT COUNT(*) AS total_mensajes FROM sw_mensaje");
		$registro = parent::fetch_assoc($consulta);
		return "<a href=\"#\" title=\"mostrar u ocultar mensajes del administrador\">" . "MENSAJES (" . $registro["total_mensajes"] . ")" . "</a>";
	}
	
	function obtenerMensaje()
	{
		// Funcion que retorna los datos de un determinado mensaje de administrado
		$consulta = parent::consulta("SELECT m.*, pe_nombre, us_titulo, us_apellidos, us_nombres FROM sw_mensaje m, sw_perfil p, sw_usuario u WHERE p.id_perfil = m.id_perfil AND u.id_usuario = m.id_usuario AND id_mensaje = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerMensajes()
	{
		// Funcion que retorna todos los mensajes ingresados en la base de datos
		$consulta = parent::consulta("SELECT id_mensaje, CONCAT(us_apellidos,' ',us_nombres,',',us_titulo) AS usuario, me_texto, me_fecha FROM sw_mensaje m, sw_usuario u WHERE u.id_usuario = m.id_usuario ORDER BY me_fecha DESC");
		$arreglo["data"] = []; //devuelve un arreglo vacío por si no hay registros en la base de datos.
		while($mensaje = parent::fetch_assoc($consulta)){
			$arreglo["data"][]=$mensaje;
		}
		return json_encode($arreglo);
	}

	function listarMensajes()
	{

		// Esto es para formatear la fecha del comentario
		$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// Recupero el nivel de acceso del usuario actual
		
		$consulta = parent::consulta("SELECT pe_nivel_acceso FROM sw_usuario u, sw_perfil p WHERE u.id_perfil = p.id_perfil AND u.id_usuario = " . $this->id_usuario);
		$registro = parent::fetch_assoc($consulta);
		$pe_nivel_acceso = $registro["pe_nivel_acceso"];

		$consulta = parent::consulta("SELECT * FROM sw_mensaje ORDER BY me_fecha ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($mensaje = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $mensaje["id_mensaje"];
				$texto = $mensaje["me_texto"];
				$fechadb = $mensaje["me_fecha"];
				list($yy,$mm,$dd)=explode("-",$fechadb);
				$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
				$cadena .= "<td class=\"link_form\">\n";
				$cadena .= "<div class=\"div_nombre\"><span class=\"format_name\">ADMINISTRADOR</span> escribi&oacute; el $fecha_formateada.";
				if ($pe_nivel_acceso > 2)
					$cadena .= " [ <a href=\"#\" onclick=\"editarMensaje(".$code.")\">Editar</a> ] [ <a href=\"#\" onclick=\"eliminarMensaje(".$code.")\">Eliminar</a> ]";
				$cadena .= "</div>\n";
				$cadena .= "<div class=\"div_comentario\">$texto</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han ingresado mensajes todav&iacute;a...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarMensajesDocentes()
	{

		// Esto es para formatear la fecha del comentario
		$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		$consulta = parent::consulta("SELECT * FROM sw_mensaje ORDER BY me_fecha ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($mensaje = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $mensaje["id_mensaje"];
				$texto = $mensaje["me_texto"];
				$fechadb = $mensaje["me_fecha"];
				list($yy,$mm,$dd)=explode("-",$fechadb);
				$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
				$cadena .= "<td class=\"link_form\">\n";
				$cadena .= "<div class=\"div_nombre\"><span class=\"format_name\">ADMINISTRADOR</span> escribi&oacute; el $fecha_formateada.</div>\n";
				$cadena .= "<div class=\"div_comentario\">$texto</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han ingresado mensajes todav&iacute;a...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarMensaje()
	{
		$qry = "INSERT INTO sw_mensaje (me_texto, id_usuario, id_perfil) VALUES (";
		$qry .= "'" . $this->me_texto . "',";
		$qry .= $this->id_usuario . ",";
		$qry .= $this->id_perfil . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Mensaje insertado exitosamente...";
		$respuesta = "BIEN";
		if (!$consulta){
			$respuesta = "ERROR";
			$mensaje = "No se pudo insertar el mensaje...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		}
		$data = array("respuesta" => $respuesta, "mensaje" => $mensaje);
		return json_encode($data);
	}

	function actualizarMensaje()
	{
		$qry = "UPDATE sw_mensaje SET ";
		$qry .= "me_texto ='" . $this->me_texto . "' ";
		$qry .= "WHERE id_mensaje = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Mensaje actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el mensaje...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		return $mensaje;
	}

	function eliminarMensaje()
	{
		$qry = "DELETE FROM sw_mensaje WHERE id_mensaje = ". $this->code;
		return parent::consulta($qry);
		/* $consulta = parent::consulta($qry);
		$mensaje = "Mensaje eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el mensaje...Error: " . mysql_error();
		return $mensaje; */
	}
}
?>