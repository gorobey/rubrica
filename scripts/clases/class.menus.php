<?php

class menus extends MySQL
{
	var $code = "";
	var $id_perfil = 0;
	var $id_menu = 0;
	var $mnu_texto = "";
	var $mnu_enlace = "";
	var $mnu_nivel = 0;
	var $mnu_orden = 0;
	var $mnu_padre = 0;
	var $mnu_publicado = 0;

	function listarMenusNivel1($id_perfil)
	{
		return parent::consulta("SELECT * FROM sw_menu WHERE id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden ASC");
	}

	function listarMenusHijos($mnu_padre)
	{
		return parent::consulta("SELECT * FROM sw_menu WHERE mnu_padre = $mnu_padre ORDER BY mnu_orden ASC");
	}
	
	function cargarMenus()
	{
		$consulta = parent::consulta("SELECT id_menu, mnu_texto FROM sw_menu WHERE id_perfil = " . $this->code . " ORDER BY mnu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($menu = parent::fetch_assoc($consulta))
			{
				$code = $menu["id_menu"];
				$name = $menu["mnu_texto"];	
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarMenusNivel()
	{
		$consulta = parent::consulta("SELECT id_menu, mnu_texto FROM sw_menu WHERE id_perfil = " . $this->id_perfil . " AND mnu_nivel = " . $this->mnu_nivel . " ORDER BY mnu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($menu = parent::fetch_assoc($consulta))
			{
				$code = $menu["id_menu"];
				$name = $menu["mnu_texto"];	
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarNiveles()
	{
		$consulta = parent::consulta("SELECT DISTINCT(mnu_nivel) FROM sw_menu WHERE id_perfil = " . $this->id_perfil . " ORDER BY mnu_nivel ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($menu = parent::fetch_assoc($consulta))
			{
				$code = $menu["mnu_nivel"];
				$name = $menu["mnu_nivel"];	
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function obtenerMenu()
	{
		$consulta = parent::consulta("SELECT * FROM sw_menu WHERE id_menu = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerDatosPagina($strqry)
	{
		$consulta = parent::consulta($strqry);
		return parent::fetch_object($consulta);
	}

	function obtenerDirectorioRaiz()
	{
		$consulta = parent::consulta("SELECT dir_raiz FROM sw_config");
		$config = parent::fetch_object($consulta);
		return $config->dir_raiz;
	}

	function eliminarMenu()
	{
		$qry = "SELECT COUNT(*) AS num_submenus FROM sw_menu WHERE mnu_padre = ". $this->code;
		$consulta = parent::consulta($qry);
		$menu = parent::fetch_object($consulta);
		if ($menu->num_submenus > 0)
			$mensaje = "No se puede eliminar el men&uacute; porque existen submen&uacute;s asociados";
		else {	
			$qry = "DELETE FROM sw_menu WHERE id_menu=". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Men&uacute; eliminado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el men&uacute;...Error: " . mysql_error();
		}
		return $mensaje;
	}

	function insertarMenu()
	{
		// Aqui primero llamo a la funcion almacenada secuencial_menu_nivel_perfil_padre
		$consulta = parent::consulta("SELECT secuencial_menu_nivel_perfil_padre(".$this->mnu_nivel.",".$this->id_perfil.",".$this->mnu_padre.") AS secuencial");
		$mnu_orden = parent::fetch_object($consulta)->secuencial;
		
		$qry = "INSERT INTO sw_menu (id_perfil, mnu_texto, mnu_enlace, mnu_nivel, mnu_orden, mnu_padre, mnu_publicado) VALUES (";
		$qry .= $this->id_perfil . ",";
		$qry .= "'" . $this->mnu_texto . "',";
		$qry .= "'" . $this->mnu_enlace . "',";
		$qry .= $this->mnu_nivel . ",";
		$qry .= $mnu_orden . ",";
		$qry .= $this->mnu_padre . ",";
		$qry .= $this->mnu_publicado . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Men&uacute; insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el men&uacute;...Error: " . mysql_error();
		return $mensaje;
	}

	function insertarSubMenu()
	{
		// Aqui primero llamo a la funcion almacenada secuencial_menu_nivel_perfil_padre
		$consulta = parent::consulta("SELECT secuencial_menu_nivel_perfil_padre(".$this->mnu_nivel.",".$this->id_perfil.",".$this->mnu_padre.") AS secuencial");
		$mnu_orden = parent::fetch_object($consulta)->secuencial;
		
		$qry = "INSERT INTO sw_menu (id_perfil, mnu_texto, mnu_enlace, mnu_nivel, mnu_orden, mnu_padre) VALUES (";
		$qry .= $this->id_perfil . ",";
		$qry .= "'" . $this->mnu_texto . "',";
		$qry .= "'" . $this->mnu_enlace . "',";
		$qry .= $this->mnu_nivel . ",";
		$qry .= $mnu_orden . ",";
		$qry .= $this->mnu_padre . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Men&uacute; insertado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el men&uacute;...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarMenu()
	{
		$qry = "UPDATE sw_menu SET ";
		$qry .= "id_perfil = " . $this->id_perfil . ",";
		$qry .= "mnu_texto = '" . $this->mnu_texto . "',";
		$qry .= "mnu_enlace = '" . $this->mnu_enlace . "',";
		$qry .= "mnu_publicado = " . $this->mnu_publicado;
		$qry .= " WHERE id_menu = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Men&uacute; actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el men&uacute;...Error: " . mysql_error();
		return $mensaje;
	}

	function listarMenus()
	{
		$consulta = parent::consulta("SELECT * FROM sw_menu WHERE id_perfil = " . $this->id_perfil . " AND mnu_padre = 0 ORDER BY mnu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($menu = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $menu["id_menu"];
				$name = $menu["mnu_texto"];
				$publicado = ($menu["mnu_publicado"]==0)?"No":"Si";
				$id_perfil = $menu["id_perfil"];	
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$publicado</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-success' onclick=\"listarSubmenus(".$code.")\">Submenus</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-warning' onclick=\"editMenu(".$code.")\">Editar</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarMenu(".$code.")\">Eliminar</button></td>";
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
				$cadena .= "<td><button class='btn btn-block btn-info' onclick=\"subirMenu(".$code.",".$id_perfil.")\" $disabled_subir>Subir</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-primary' onclick=\"bajarMenu(".$code.",".$id_perfil.")\" $disabled_bajar>Bajar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido men&uacute;s asociados a este perfil...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function listarSubmenus()
	{
		$consulta = parent::consulta("SELECT * FROM sw_menu WHERE mnu_padre = " . $this->code . " ORDER BY mnu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($menu = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
				$code = $menu["id_menu"];
				$name = $menu["mnu_texto"];
				$publicado = ($menu["mnu_publicado"]==0)?"No":"Si";
				$mnu_padre = $menu["mnu_padre"];	
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$publicado</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-warning' onclick=\"editSubMenu(".$code.")\">Editar</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarSubMenu(".$code.")\">Eliminar</button></td>";
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
				$cadena .= "<td><button class='btn btn-block btn-info' onclick=\"subirSubmenu(".$code.",".$mnu_padre.")\" $disabled_subir>Subir</button></td>";
				$cadena .= "<td><button class='btn btn-block btn-primary' onclick=\"bajarSubmenu(".$code.",".$mnu_padre.")\" $disabled_bajar>Bajar</button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='6' align='center'>No se han definido submen&uacute;s asociados a este men&uacute;...</td>\n";
			$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function subirMenuPerfil()
	{
		// Primero obtengo el "orden" del menu actual
		$qry = "SELECT mnu_orden AS orden FROM sw_menu WHERE id_menu = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden anterior
		$qry = "SELECT id_menu AS id FROM sw_menu WHERE mnu_orden = $orden - 1 AND id_perfil = " .$this->id_perfil;
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (decrementar en uno) del registro actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden - 1 WHERE id_menu = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (incrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden + 1 WHERE id_menu = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Menu \"subido\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"subir\" el Menu...Error: " . mysql_error();
		
		return $mensaje;
	}

	function subirSubmenu()
	{
		// Primero obtengo el "orden" del submenu actual
		$qry = "SELECT mnu_orden AS orden FROM sw_menu WHERE id_menu = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;

		// Ahora obtengo el id del registro que tiene el orden anterior
		$qry = "SELECT id_menu AS id FROM sw_menu WHERE mnu_orden = $orden - 1 AND mnu_padre = " . $this->mnu_padre;
		$id = parent::fetch_object(parent::consulta($qry))->id;

		// Se actualiza el orden (decrementar en uno) del registro actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden - 1 WHERE id_menu = " . $this->code;
		$consulta = parent::consulta($qry);

		// Luego se actualiza el orden (incrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden + 1 WHERE id_menu = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Submenu \"subido\" exitosamente...";

		if (!$consulta)
			$mensaje = "No se pudo \"subir\" del Submenu...Error: " . mysql_error();

		return $mensaje;
	}

	function bajarSubmenu()
	{
		// Primero obtengo el "orden" del submenu actual
		$qry = "SELECT mnu_orden AS orden FROM sw_menu WHERE id_menu = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;

		// Ahora obtengo el id del registro que tiene el orden anterior
		$qry = "SELECT id_menu AS id FROM sw_menu WHERE mnu_orden = $orden + 1 AND mnu_padre = " . $this->mnu_padre;
		$id = parent::fetch_object(parent::consulta($qry))->id;

		// Se actualiza el orden (incrementar en uno) del registro actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden + 1 WHERE id_menu = " . $this->code;
		$consulta = parent::consulta($qry);

		// Luego se actualiza el orden (decrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden - 1 WHERE id_menu = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Submenu \"subido\" exitosamente...";

		if (!$consulta)
			$mensaje = "No se pudo \"subir\" del Submenu...Error: " . mysql_error();

		return $mensaje;
	}

	function bajarMenuPerfil()
	{
		// Primero obtengo el "orden" del menu actual
		$qry = "SELECT mnu_orden AS orden FROM sw_menu WHERE id_menu = " . $this->code;
		$orden = parent::fetch_object(parent::consulta($qry))->orden;
		
		// Ahora obtengo el id del registro que tiene el orden siguiente
		$qry = "SELECT id_menu AS id FROM sw_menu WHERE mnu_orden = $orden + 1 AND id_perfil = " .$this->id_perfil;
		$id = parent::fetch_object(parent::consulta($qry))->id;
		
		// Se actualiza el orden (incrementar en uno) del registro actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden + 1 WHERE id_menu = " . $this->code;
		$consulta = parent::consulta($qry);
		
		// Luego se actualiza el orden (decrementar en uno) del registro anterior al actual...
		$qry = "UPDATE sw_menu SET mnu_orden = mnu_orden - 1 WHERE id_menu = $id";
		$consulta = parent::consulta($qry);

		$mensaje = "Menu \"bajado\" exitosamente...";
		
		if (!$consulta)
			$mensaje = "No se pudo \"bajar\" el Menu...Error: " . mysql_error();
		
		return $mensaje;
	}	

}
?>