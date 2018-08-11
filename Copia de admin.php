<?php
	session_start();
	include_once("funciones/funciones_sitio.php");
	require_once("scripts/clases/class.mysql.php");
	require_once("scripts/clases/class.usuarios.php");
	require_once("scripts/clases/class.perfiles.php");
	require_once("scripts/clases/class.periodos_lectivos.php");
	require_once("scripts/clases/class.generar_menus.php");
	require_once("scripts/clases/class.menus.php");
	$estaPagina->me_link = "central2.html"; //página por defecto si no existe alguna en el menu asociado
	if (!isset($_SESSION['usuario_logueado']) OR !$_SESSION['usuario_logueado'])
		header("Location: index.php");
	else {
		//Primero tengo que obtener el id_perfil para luego obtener el menu correspondiente
		$id_usuario = $_GET['id_usuario'];
		$usuarios = new usuarios();
		$usuario = $usuarios->obtenerUsuario($id_usuario);
		//Obtengo el nombre completo del usuario
		$nombreUsuario = $usuario->us_fullname;
		//Obtengo el id del perfil del usuario y el nombre del mismo
		$id_perfil = $usuario->id_perfil;
		$perfiles = new perfiles();
		$perfil = $perfiles->obtenerPerfil($id_perfil);
		$nombrePerfil = $perfil->pe_nombre;
		//Obtengo los años de inicio y de fin del periodo lectivo actual
		$periodos_lectivos = new periodos_lectivos();
		$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($_SESSION['id_periodo_lectivo']);
		$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
		//Ahora si obtengo el menu relacionado con el perfil
		$menus = new generar_menus();
		$menu_vertical = $menus->generarMenuVertical($id_usuario,$id_perfil);
		if (!isset($_GET['nivel'])) {
			$titulo = "SIAE-WEB Admin";
			$enlace = "central2.html";
		} else {
			if ($_GET['nivel']==1) { //Se trata de un menu de nivel 1
                $strqry = "select mnu_texto, mnu_enlace, mnu_nivel from sw_menu where id_menu = " . $_GET['id_menu'];
				$pagina = $menus->obtenerDatosPagina($strqry);
				$titulo = $pagina->mnu_texto;
				$enlace = $pagina->mnu_enlace;
				$nivel = $pagina->mnu_nivel;
			}
			else if ($_GET['nivel']==2) { //Se trata de un submenu de nivel 2
			    $strqry = "select sbmnu_texto, sbmnu_enlace, sbmnu_nivel from sw_submenu where id_submenu = " . $_GET['id_menu'];
				$pagina = $menus->obtenerDatosPagina($strqry);
				$titulo = $pagina->sbmnu_texto;
				$enlace = $pagina->sbmnu_enlace;
				$nivel = $pagina->sbmnu_nivel;
			} else if ($nivel==0) {  //Se trata de un menu de nivel 0
			    $strqry = "select mnu_enlace from sw_menu where id_menu = " . $_GET['id_menu'];
				$pagina = $menus->obtenerDatosPagina($strqry);
				$enlace = $pagina->mnu_enlace;
           	    header("Location: " . $enlace); 
			}
			$config = new menus();
			$dir_raiz = $config->obtenerDirectorioRaiz();
			$error = (file_exists($_SERVER['DOCUMENT_ROOT'] . $dir_raiz . $enlace)) ? false : true;
			$_SESSION['titulo_pagina'] = $titulo;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo "SIAE-WEB " . $titulo ?></title>
<link href="./estilos.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
<style title="text/css">
.icono_cerrar {
	background:#f5f5f5;
	height:21px;
	margin-top:2px;
	padding-top:2px;
}
.icono_cerrar a {
	text-decoration: underline;
	color: #FF0000;
}

.icono_cerrar a:hover {
	color: #0000FF;
}
</style>
<script src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://malsup.github.com/jquery.cycle.all.js"></script>
<script type="text/javascript">
	$(document).ready(function(){ // Script del Navegador
		$("ul.subnavegador").not('.selected').hide();
		
		$("a.desplegable").click(function(e){
		  var desplegable = $(this).parent().find("ul.subnavegador");
		  $('.desplegable').parent().find("ul.subnavegador").not(desplegable).slideUp('slow');
		  desplegable.slideToggle('slow');
		  e.preventDefault();
		});
		
/*		$("#div_notificaciones").html("");
		var html = "<marquee width=\"100%\" scrolldelay=\"100\" direction=\"right\" loop=\"3\" behavior=\"alternate\">" +
				   "Compañeros Docentes: Se encuentra cerrado el primer quimestre, no podrán modificar calificaciones " +
				   "de ese período...</marquee>";
		$("#div_notificaciones").html(html);
*/	

		$("#icono_cerrar").on('click',function(e) {
			e.preventDefault();
			$("#barra_notificaciones").slideUp();
		});
		
/*		var $container = $('#div_notificacion');
		 
		quotes = [
		{nombre: 'Administrador', mensaje: 'Compañeros Docentes: Se encuentra cerrado el primer quimestre, no podrán modificar calificaciones de ese período...'},
		{nombre: 'Administrador', mensaje: 'Por favor compañeros docentes, ingresar las calificaciones del primero y segundo parcial del segundo quimestre...'},
		{nombre: 'Administrador', mensaje: 'Por favor compañeros docentes, cambiar su contraseña periódicamente...'}
		];
		 
		$(quotes).each(function () {
			var quote = '<span>' + this.nombre + ' dice </span> - <span>' + this.mensaje + '</span>';
			$container.append('<div class="notificacion">' + quote + '</div>');
		});
		
		$container.cycle({
			fx: 'fade',
			speed: '2000',
			timeout: '7500',//1000=1seg
			cleartype: '1' // activar correcciones cleartype
		});
*/		
	});
</script>
</head>

<body>
<div id="pagina">
  <table id="contenido" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>  
        <table id="cabecera" class="tabla_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="70%">
                    <div class="titulo1">S I A E</div>
                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>
                </td>
                <td width="*" valign="top">
                  <table id="info" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                        <table id="tabla_fecha" width="100%" cellpadding="0" cellspacing="0" border="0">
                          <tr>
                            <td width="50%" align="center">
                                  <div id="usuario" class="perfil"><?php echo $nombrePeriodoLectivo ?></div>
                            </td>
                            <td width="*">
                                <div class="fecha">
                                   <!-- Aqui va la fecha del sistema generada mediante PHP -->
                                   <?php echo fecha_actual(); ?>
                                </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td>  
                          <table id="tabla_login" width="100%" cellpadding="0" cellspacing="0" border="0">
                             <tr>
                                <td width="50%" align="center">
                                  <div id="perfil" class="perfil"><?php echo $nombreUsuario ?></div>
                                  <div id="periodo_lectivo" class="perfil"><?php echo $nombrePerfil ?></div>
                                </td>
                                <td width="45%" align="right">
                                  <div class="login">
                                     <a href="logout.php">Salir</a>
                                  </div>
                                </td>
                                <td width="*" align="right">
                                  <div class="botones">
                                      <a href="logout.php">
                                        <img src="imagenes/login_gnome.png" onmouseover="this.src='imagenes/login_gnome1.png'" onmouseout="this.src='imagenes/login_gnome.png'" alt="haga click para salir..." title="salir del sistema..." />
                                      </a>
                                  </div>
                                </td>  
                             </tr>
                          </table>   
                      </td>    
                    </tr>
                  </table>   
                </td>  
            </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
         <div id="cuerpo">
            <!-- Aqui va el cuerpo de la pagina en si-->
            <table id="cuerpo" width="100%" border="0" cellpadding="0" cellspacing="0">
               <tr>
                  <td width="8%" valign="top">
                      <!-- Aqui va el menu generado mediante PHP -->
                        <div id="menu">
                          <?php echo $menu_vertical ?>
                        </div>
                  </td>
                  <td width="*" valign="top">
                      <!-- Aqui va la pagina asociada con el menu -->
                      <?php 
						   include($enlace); 
					  ?>
                  </td>
               </tr>
            </table>
         </div>   
      </td>
    </tr>
  </table>      
</div>
</body>
</html>
