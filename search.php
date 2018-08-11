<?php
/**
 * Plugin  : Autocompletar con jQuery
 *   Autor : Lucas Forchino
 * WebSite : http://www.tutorialjquery.com
 * version : 1.0
 * Licencia: Pueden usar libremenete este código siempre y cuando no sea para 
 *           publicarlo como ejemplo de autocompletar en otro sitio.
 */

// inclusion de archivos de conexion con la base de datos
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.usuarios.php");

// limpio la palabra que se busca
$search= trim($_GET['search']);

// la busco 
echo search($search);

/**
 *  Funcion que busca en los datos un resultado  que tenga que ver
 *  con la busqueda, si los datos vinieran de base no seria necesario esto
 *  ya que lo podriamos resolver directamente por sql
 */
function search($searchWord)
{
	$usuarios = new usuarios();
	return $usuarios->buscarUsuario($searchWord);
}

?>