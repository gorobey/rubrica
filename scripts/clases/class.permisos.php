<?php

class permisos extends MySQL
{
    function getPermisos($menu, $rol)
    {
        $consulta = parent::consulta("SELECT * FROM sw_permiso WHERE id_menu = $menu AND id_perfil = $rol");
        return parent::fetch_object($consulta);
    }
}