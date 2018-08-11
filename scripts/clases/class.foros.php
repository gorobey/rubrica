<?php

class foros extends MySQL
{
	
	var $code = "";
	var $id_usuario = "";
	var $fo_titulo = "";
	var $fo_descripcion = "";
	
	function obtenerTituloForo()
	{
		$consulta = parent::consulta("SELECT fo_titulo FROM sw_foro WHERE id_foro = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerDatosForo()
	{
		$consulta = parent::consulta("SELECT * FROM sw_foro WHERE id_foro = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarForos()
	{
		$consulta = parent::consulta("SELECT id_foro, fo_titulo, fo_descripcion, u.id_usuario, us_login FROM sw_foro f, sw_usuario u WHERE f.id_usuario = u.id_usuario ORDER BY id_foro ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($foro = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $foro["id_foro"];
				$titulo = $foro["fo_titulo"];
				$descripcion = $foro["fo_descripcion"];
				$id_usuario = $foro["id_usuario"];
				$autor = $foro["us_login"];
				$query = parent::consulta("SELECT * FROM sw_tema WHERE id_foro = $code");
				$num_temas = parent::num_rows($query);
				$cadena .= "<td width=\"12%\" align=\"left\">$autor</td>\n";
				$cadena .= "<td width=\"24%\" class=\"link_form\" align=\"left\"><a href=\"#\" onclick=\"verTemas(".$code.")\">$titulo</a></td>\n";
				$cadena .= "<td width=\"34%\" align=\"left\">$descripcion</td>\n";
				$cadena .= "<td width=\"6%\" align=\"left\">$num_temas</td>\n";
				if($this->id_usuario == $id_usuario) {
					$cadena .= "<td width=\"8%\" class=\"link_table\"><a href=\"#\" onclick=\"nuevoTema(".$code.")\">Nuevo tema</a></td>\n";
					$cadena .= "<td width=\"8%\" class=\"link_table\"><a href=\"#\" onclick=\"editarForo(".$code.")\">Editar</a></td>\n";
					$cadena .= "<td width=\"8%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarForo(".$code.")\">Eliminar</a></td>\n";
				} else {
					$cadena .= "<td width=\"24%\" colspan=\"3\" class=\"link_table\" align=\"center\"><a href=\"#\" onclick=\"nuevoTema(".$code.")\">Nuevo Tema</a></td>\n";
				}
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido foros...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function insertarForo()
	{
		$qry = "INSERT INTO sw_foro (id_usuario, fo_titulo, fo_descripcion) VALUES (";
		$qry .= $this->id_usuario . ",";
		$qry .= "'" . $this->fo_titulo . "','";
		$qry .= $this->fo_descripcion . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Foro insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el foro...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarForo()
	{
		$qry = "UPDATE sw_foro SET ";
		$qry .= "fo_titulo = '" . $this->fo_titulo . "',";
		$qry .= "fo_descripcion = '" . $this->fo_descripcion . "'";
		$qry .= " WHERE id_foro = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Foro [" . $this->fo_titulo . "] actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el foro...Error: " . mysql_error();
		return $mensaje;
	}
	
	function tieneTemas($id_foro)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tema WHERE id_foro = $id_foro");
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

	function eliminarforo()
	{
		$qry = "DELETE FROM sw_foro WHERE id_foro = ". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Foro eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el foro...Error: " . mysql_error();
		return $mensaje;
	}

}
?>