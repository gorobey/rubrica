<?php

class generar_menus extends MySQL
{
	
	function generarMenuHorizontal($id_usuario, $id_perfil)
	{
		$qry_menus = parent::consulta("SELECT * FROM sw_menu WHERE id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");
		$num_total_menus = parent::num_rows($qry_menus);
		$cadena = "<ul id='coolMenu'>";
		if ($num_total_menus > 0)
		{
			for ($idx_menu = 0; $idx_menu < $num_total_menus; $idx_menu++) {
				//Obtengo los submenus si existen
				$menu[$idx_menu] = parent::fetch_object($qry_menus);
				$id_menu = $menu[$idx_menu]->id_menu;
				$qry_submenus = parent::consulta("SELECT * FROM sw_menu WHERE mnu_padre = $id_menu ORDER BY mnu_orden");
				$num_total_submenus = parent::num_rows($qry_submenus);
				$cadena .= "<li>";
				if ($num_total_submenus > 0) {
 					$cadena .= "<a href='#'>" . $menu[$idx_menu]->mnu_texto . "</a>";
					$cadena .= "<ul class='noJS'>";
					for ($idx_sbmenu = 0; $idx_sbmenu < $num_total_submenus; $idx_sbmenu++) {
						$sbmenu[$idx_sbmenu] = parent::fetch_object($qry_submenus);
						$cadena .= "<li><a href='admin.php?id_usuario=" . $id_usuario . "&id_menu=" . $sbmenu[$idx_sbmenu]->id_menu . "&nivel=" . $sbmenu[$idx_sbmenu]->mnu_nivel . "&id_perfil=$id_perfil'>" . $sbmenu[$idx_sbmenu]->mnu_texto . "</a></li>";
					}
					$cadena .= "</ul>";
				} else {
					$cadena .= "<a href='admin.php?id_usuario=" . $id_usuario . "&id_menu=" . $menu[$idx_menu]->id_menu . "&nivel=" . $menu[$idx_menu]->mnu_nivel . "&id_perfil=$id_perfil'>" . $menu[$idx_menu]->mnu_texto . "</a>";
				}
				$cadena .= "</li>";
			}
		}
		$cadena .= "</ul>";
		return $cadena;
	}

	function generarMenuVertical($id_usuario, $id_perfil)
	{
		$qry_menus = parent::consulta("SELECT * FROM sw_menu WHERE id_perfil = $id_perfil ORDER BY mnu_orden");
		$num_total_menus = parent::num_rows($qry_menus);
		$cadena = "<ul class='navegador'>";
		if ($num_total_menus > 0)
		{
			for ($idx_menu = 0; $idx_menu < $num_total_menus; $idx_menu++) {
				//Obtengo los submenus si existen
				$menu[$idx_menu] = parent::fetch_object($qry_menus);
				$id_menu = $menu[$idx_menu]->id_menu;
				$qry_submenus = parent::consulta("SELECT * FROM sw_submenu WHERE id_menu = $id_menu ORDER BY sbmnu_orden");
				$num_total_submenus = parent::num_rows($qry_submenus);
				$cadena .= "<li>";
				if ($num_total_submenus > 0) {
 					$cadena .= "<a href='#' class='desplegable'>" . $menu[$idx_menu]->mnu_texto . "</a>";
					$cadena .= "<ul class='subnavegador'>";
					for ($idx_sbmenu = 0; $idx_sbmenu < $num_total_submenus; $idx_sbmenu++) {
						$sbmenu[$idx_sbmenu] = parent::fetch_object($qry_submenus);
						$cadena .= "<li><a href='admin.php?id_usuario=" . $id_usuario . "&id_menu=" . $sbmenu[$idx_sbmenu]->id_submenu . "&nivel=" . $sbmenu[$idx_sbmenu]->sbmnu_nivel . "'>" . $sbmenu[$idx_sbmenu]->sbmnu_texto . "</a></li>";				
					}
					$cadena .= "</ul>";
				} else {
					$cadena .= "<a href='admin.php?id_usuario=" . $id_usuario . "&id_menu=" . $menu[$idx_menu]->id_menu . "&nivel=" . $menu[$idx_menu]->mnu_nivel . "'>" . $menu[$idx_menu]->mnu_texto . "</a>";
				}
				$cadena .= "</li>";
			}
		}
		$cadena .= "</ul>";
		return $cadena;
	}

	//funcion que obtiene los datos de una pagina especifica
	function obtenerDatosPagina($consulta) {
		$resultado = parent::consulta($consulta) or die("No se pudo realizar la consulta " . mysql_error());
		return parent::fetch_object($resultado);
	}

}
?>