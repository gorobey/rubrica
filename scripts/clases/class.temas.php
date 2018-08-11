<?php

class temas extends MySQL
{
	
	var $code = "";
	var $id_usuario = "";
	var $id_foro = "";
	var $te_titulo = "";
	var $te_descripcion = "";
	
	function obtenerTituloTema()
	{
		$consulta = parent::consulta("SELECT te_titulo FROM sw_tema WHERE id_tema = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerTema($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tema WHERE id_tema = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosTema()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tema WHERE id_tema = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarTemas()
	{
		$consulta = parent::consulta("SELECT id_tema, te_titulo, te_descripcion, u.id_usuario, us_login FROM sw_tema t, sw_foro f, sw_usuario u WHERE t.id_foro = f.id_foro AND f.id_usuario = u.id_usuario AND t.id_foro = " . $this->id_foro . " ORDER BY id_tema ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tema = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\" valign=\"top\">\n";
				$code = $tema["id_tema"];
				$titulo = $tema["te_titulo"];
				$descripcion = $tema["te_descripcion"];
				$id_usuario = $tema["id_usuario"];
				$autor = $tema["us_login"];
				$query = parent::consulta("SELECT * FROM sw_respuesta WHERE id_tema = $code");
				$num_respuestas = parent::num_rows($query);
				$cadena .= "<td width=\"3%\">$contador</td>\n";	
				$cadena .= "<td width=\"26%\" align=\"left\">$titulo</td>\n";
				$cadena .= "<td width=\"28%\" align=\"left\">".nl2br($descripcion)."</td>\n";
				$cadena .= "<td width=\"12%\" align=\"center\">$autor</td>\n";
				$cadena .= "<td width=\"7%\" class=\"link_form\" align=\"center\"><a href=\"#\" onclick=\"verRespuestas(".$code.")\" title=\"Ver Respuestas\">$num_respuestas</a></td>\n";
				if($this->id_usuario == $id_usuario) {
					$cadena .= "<td width=\"12%\" class=\"link_table\"><a href=\"#\" onclick=\"editarTema(".$code.")\">Editar</a></td>\n";
					$cadena .= "<td width=\"12%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarTema(".$code.")\">Eliminar</a></td>\n";
				} else {
					$cadena .= "<td width=\"24%\" class=\"link_table\"><a href=\"#\" onclick=\"nuevaRespuesta(".$code.")\">Responder</a></td>\n";
				}
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido temas para este foro...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function insertarTema()
	{
		$qry = "INSERT INTO sw_tema (id_foro, te_titulo, te_descripcion) VALUES (";
		$qry .= $this->id_foro . ",";
		$qry .= "'" . $this->te_titulo . "','";
		$qry .= $this->te_descripcion . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Tema [" . $this->te_titulo . "] insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el tema...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarTema()
	{
		$qry = "UPDATE sw_tema SET ";
		$qry .= "te_titulo = '" . $this->te_titulo . "',";
		$qry .= "te_descripcion = '" . $this->te_descripcion . "'";
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