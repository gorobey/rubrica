<?php

class comentarios extends MySQL
{
	var $code = "";
	var $co_id_usuario = "";
	var $co_tipo = "";
	var $co_perfil = "";
	var $co_nombre = "";
	var $co_texto = "";
	var $co_fecha = "";
	
	function obtenerNumeroComentarios()
	{
		// Funcion que retorna un mensaje con el numero de comentarios ingresados en el sistema
		$consulta = parent::consulta("SELECT COUNT(*) AS total_comentarios FROM sw_comentario");
		$registro = parent::fetch_assoc($consulta);
		return "COMENTARIOS (" . $registro["total_comentarios"] . ")";
	}

	function contarComentarios()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_comentario");
		return json_encode(parent::fetch_assoc($consulta));	
	}

	function paginarComentarios($cantidad_registros, $numero_pagina, $total_registros)
	{
		$total_paginas = ceil($total_registros / $cantidad_registros);
		$mensaje = "<< <span class='link_table'> <a href='#' onclick='paginarComentarios(".$cantidad_registros.",1,".$total_registros.")'> Primero </a> </span>";
		if (($numero_pagina - 1) > 0) {
			$mensaje .= "<span class='link_table'> < <a href='#' onclick='paginarComentarios(".$cantidad_registros.",".($numero_pagina-1).",".$total_registros.")'>Anterior</a></span>";
		} else {
			$mensaje .= "<span> < Anterior</span>";
		}
		for ($i=1; $i <= $total_paginas; $i++) {
			if ($numero_pagina == $i) {
				$mensaje .= "<b> P&aacute;gina ".$numero_pagina."</b>";
			} else {
				$mensaje .= "<span class='link_table'> <a href='#' onclick='paginarComentarios(".$cantidad_registros.",".$i.",".$total_registros.")'>$i</a></span>";
			}
		}
		if (($numero_pagina+1) <= $total_paginas) {
			$mensaje .= " <span class='link_table'><a href='#' onclick='paginarComentarios(".$cantidad_registros.",".($numero_pagina+1).",".$total_registros.")'>Siguiente</a> > </span>";
		} else {
			$mensaje .= " <span>Siguiente</a> > </span>";
		}
		$mensaje .= " <span class='link_table'><a href='#' onclick='paginarComentarios(".$cantidad_registros.",".$total_paginas.",".$total_registros.")'>Ultimo</a></span> >>";
		return $mensaje;
	}

	function listarComentarios($cantidad_registros, $numero_pagina)
	{

		// Esto es para formatear la fecha del comentario
		$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		$inicio = ($numero_pagina - 1) * $cantidad_registros;

		$consulta = parent::consulta("SELECT * FROM sw_comentario ORDER BY co_fecha DESC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = $inicio;
			while($comentario = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $comentario["id_comentario"];
				$name = $comentario["co_nombre"];
				$perfil = $comentario["co_perfil"];
				$texto = $comentario["co_texto"];
				$fechadb = $comentario["co_fecha"];
				list($yy,$mm,$dd)=explode("-",$fechadb);
				$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
				$cadena .= "<td>\n";
				$cadena .= "<div class=\"div_nombre\"><span class=\"format_name\">$name</span> coment&oacute; el $fecha_formateada</div>\n";
				$cadena .= "<div class=\"div_comentario\">$texto</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han ingresado comentarios todav&iacute;a...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarComentariosEstudiante()
	{

		// Esto es para formatear la fecha del comentario
		$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		$consulta = parent::consulta("SELECT * FROM sw_comentario ORDER BY co_fecha DESC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($comentario = parent::fetch_assoc($consulta))
			{
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $comentario["id_comentario"];
				$name = $comentario["co_nombre"];
				$perfil = $comentario["co_perfil"];
				$texto = $comentario["co_texto"];
				$fechadb = $comentario["co_fecha"];
				list($yy,$mm,$dd)=explode("-",$fechadb);
				$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
				$cadena .= "<td>\n";
				$cadena .= "<div class=\"div_nombre\"><span class=\"format_name\">$name</span> coment&oacute; el $fecha_formateada</div>\n";
				$cadena .= "<div class=\"div_comentario\">$texto</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han ingresado comentarios todav&iacute;a...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarComentario()
	{
		$qry = "INSERT INTO sw_comentario (co_id_usuario, co_tipo, co_perfil, co_nombre, co_texto) VALUES (";
		$qry .= $this->co_id_usuario . ",";
		$qry .= $this->co_tipo . ",";
		$qry .= "'" . $this->co_perfil . "',";
		$qry .= "'" . $this->co_nombre . "',";
		$qry .= "'" . $this->co_texto . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Comentario insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el comentario...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		return $mensaje;
	}

	function eliminarComentario()
	{
		$qry = "DELETE FROM sw_usuario WHERE id_usuario=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Usuario eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el usuario...Error: " . mysql_error();
		return $mensaje;
	}
}
?>