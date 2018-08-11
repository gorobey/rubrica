<?php

class respuestas extends MySQL
{
	
	var $code = "";
	var $id_usuario = "";
	var $id_tema = "";
	var $re_texto = "";
	var $re_autor = "";
	var $re_perfil = "";
	
	function obtenerDatosRespuesta()
	{
		$consulta = parent::consulta("SELECT * FROM sw_respuesta WHERE id_respuesta = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarRespuestas()
	{
		$consulta = parent::consulta("SELECT id_respuesta, id_foro, re_texto, re_autor, re_perfil, us_login, pe_nombre FROM sw_respuesta r, sw_tema t, sw_usuario u, sw_perfil p WHERE r.id_tema = t.id_tema AND r.re_autor = u.id_usuario AND u.id_perfil = p.id_perfil AND r.id_tema = " . $this->id_tema . " ORDER BY id_respuesta ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($respuesta = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\" valign=\"top\">\n";
				$id_foro = $respuesta["id_foro"];
				$code = $respuesta["id_respuesta"];
				$texto = $respuesta["re_texto"];
				$id_usuario = $respuesta["re_autor"];
				$autor = $respuesta["us_login"];
				$perfil = $respuesta["pe_nombre"];
				$cadena .= "<td width=\"3%\">$contador</td>\n";	
				$cadena .= "<td width=\"61%\" align=\"left\">".nl2br($texto)."</td>\n";
				$cadena .= "<td width=\"12%\" align=\"center\">$autor</td>\n";
				$cadena .= "<td width=\"12%\" align=\"center\">$perfil</td>\n";
				$cadena .= "<td width=\"12%\" class=\"link_table\"><a href=\"#\" onclick=\"verTemas(".$id_foro.")\">Ver Temas</a></td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido respuestas para este tema...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function insertarRespuesta()
	{
		// Aqui consultar el perfil asociado
		$consulta = parent::consulta("SELECT pe_nombre FROM sw_perfil p, sw_usuario u WHERE u.id_perfil = p.id_perfil AND id_usuario = " . $this->re_autor);
		$resultado = parent::fetch_object($consulta);
		$re_perfil = $resultado->pe_nombre;
		$qry = "INSERT INTO sw_respuesta (id_tema, re_texto, re_autor, re_perfil) VALUES (";
		$qry .= $this->id_tema . ",";
		$qry .= "'" . $this->re_texto . "',";
		$qry .= $this->re_autor . ",'";
		$qry .= $this->re_perfil . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Respuesta insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la respuesta...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarTema()
	{
		$qry = "UPDATE sw_tema SET ";
		$qry .= "te_titulo = '" . $this->te_titulo . "',";
		$qry .= "re_texto = '" . $this->re_texto . "'";
		$qry .= " WHERE id_tema = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tema [" . $this->te_titulo . "] actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el tema...Error: " . mysql_error();
		return $mensaje;
	}
	
	function tieneRespuestas($id_tema)
	{
		$consulta = parent::consulta("SELECT * FROM sw_respuesta WHERE id_tema = $id_tema");
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

	function eliminarTema()
	{
		$qry = "DELETE FROM sw_tema WHERE id_tema = ". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tema eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el tema...Error: " . mysql_error();
		return $mensaje;
	}

}
?>