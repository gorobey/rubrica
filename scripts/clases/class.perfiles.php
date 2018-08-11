<?php

class perfiles extends MySQL
{
	
	var $code = "";
	var $pe_nombre = "";
	var $pe_nivel_acceso = "";
	
	function existePerfil($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_perfil WHERE pe_nombre = '$nombre'");
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

	function obtenerIdPerfil($nombre)
	{
		$consulta = parent::consulta("SELECT id_perfil FROM sw_perfil WHERE pe_nombre = '$nombre'");
		$perfil = parent::fetch_object($consulta);
		return $perfil->id_perfil;
	}

	function obtenerPerfil($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_perfil WHERE id_perfil = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerPerfiles()
	{
		// Funcion que retorna todos los mensajes ingresados en la base de datos
		return parent::consulta("SELECT *
								   FROM sw_perfil
								 ORDER BY pe_nombre");
	}

	function obtenerDatosPerfil()
	{
		$consulta = parent::consulta("SELECT * FROM sw_perfil WHERE id_perfil = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

    function cargarPerfiles(){
        // Funcion que retorna todas los perfiles ingresados en la base de datos
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_perfil ORDER BY pe_nombre");
        if(parent::num_rows($consulta) > 0){
            while($perfil = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $nombre = $perfil["pe_nombre"];
                $id = $perfil["id_perfil"];
                $cadena .= "<td>".$id."</td>";
                $cadena .= "<td>".$nombre."</td>";
                $cadena .= "<td><button onclick='editPerfil(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deletePerfil(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado perfiles todavia...</td></tr>";
        }
        return $cadena;
    }	

	function listarPerfiles()
	{
		$consulta = parent::consulta("SELECT * FROM sw_perfil ORDER BY id_perfil ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($perfiles = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $perfiles["id_perfil"];
				$name = $perfiles["pe_nombre"];
				$nivel_enlace = $perfiles["pe_nivel_acceso"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";	
				$cadena .= "<td width=\"5%\">$code</td>\n";	
				$cadena .= "<td width=\"36%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"36%\" align=\"left\">$nivel_enlace</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarPerfil(".$code.")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarPerfil(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido perfiles...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarPerfil()
	{
		$qry = "INSERT INTO sw_perfil (pe_nombre) VALUES (";
		$qry .= "'" . $this->pe_nombre . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Perfil insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el perfil...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarPerfil()
	{
		$qry = "UPDATE sw_perfil SET ";
		$qry .= "pe_nombre = '" . $this->pe_nombre . "'";
		$qry .= " WHERE id_perfil = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Perfil " . $this->pe_nombre . " actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el perfil...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarPerfil()
	{
		// Primero compruebo si existen Usuarios asociados
		$qry = "SELECT * FROM sw_usuario_perfil WHERE id_perfil = ". $this->code;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "No se puede eliminar este Perfil, porque tiene Usuarios asociados.";
		} else {
			$qry = "DELETE FROM sw_perfil WHERE id_perfil=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Perfil eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el perfil...Error: " . mysql_error();	
		}
		return $mensaje;
	}

}
?>