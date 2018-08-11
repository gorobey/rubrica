<?php
	session_start();
	include_once("funciones/funciones_sitio.php");
	require_once("scripts/clases/class.mysql.php");
	require_once("scripts/clases/class.usuarios.php");
	require_once("scripts/clases/class.perfiles.php");
	require_once("scripts/clases/class.periodos_lectivos.php");
	require_once("scripts/clases/class.generar_menus.php");
	require_once("scripts/clases/class.menus.php");
	require_once("scripts/clases/class.comentarios.php");
	require_once("scripts/clases/class.mensajes.php");
	
	// Esto es para recuperar el numero de comentarios ingresados al sistema
	$comentarios = new comentarios();
	$num_comentarios = $comentarios->obtenerNumeroComentarios();
	
	// Esto es para recuperar el numero de mensajes del administrador
	$mensajes = new mensajes();
	$num_mensajes = $mensajes->obtenerNumeroMensajes();
	
	if (!isset($_SESSION['usuario_logueado']))
		header("Location: index.php");
	else {
		//Primero tengo que obtener el id_perfil para luego obtener el menu correspondiente
		$id_usuario = $_GET['id_usuario'];
		$usuarios = new usuarios();
		$usuario = $usuarios->obtenerUsuario($id_usuario);
		//Obtengo el nombre completo del usuario
		$nombreUsuario = $usuario->us_fullname;
		//Obtengo el id del perfil del usuario y el nombre del mismo
		//$id_perfil = $usuario->id_perfil;
        $id_perfil = $_GET['id_perfil'];
		$perfiles = new perfiles();
		$perfil = $perfiles->obtenerPerfil($id_perfil);
		$nombrePerfil = $perfil->pe_nombre;
		//Obtengo los años de inicio y de fin del periodo lectivo actual
		$periodos_lectivos = new periodos_lectivos();
		$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($_SESSION['id_periodo_lectivo']);
		$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
		//Ahora si obtengo el menu relacionado con el perfil
		$menus = new generar_menus();
		$menu_horizontal = $menus->generarMenuHorizontal($id_usuario,$id_perfil);
		if (!isset($_GET['nivel'])) {
			$titulo = "SIAE-WEB Admin";
			$enlace = "central2.html";
		} else {
			$strqry = "select mnu_texto, mnu_enlace, mnu_nivel from sw_menu where id_menu = " . $_GET['id_menu'];
			$pagina = $menus->obtenerDatosPagina($strqry);
			$titulo = $pagina->mnu_texto;
			$enlace = $pagina->mnu_enlace;
			$nivel = $pagina->mnu_nivel;
			$_SESSION['titulo_pagina'] = $titulo;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $titulo ?></title>
<link href="./estilos.css" rel="stylesheet" type="text/css" />
<link href="./css/coolMenu.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="./css/styles.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="./fonts.css" rel="stylesheet" type="text/css" />
<!-- Bootstrap CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="shortcut icon" href="favicon.ico" />
<style title="text/css">
.icono_cerrar {
	background:#f5f5f5;
	height:21px;
	margin-top:2px;
	padding-top:2px;
	padding-right:4px;
	text-align:right;
}
.icono_cerrar a {
	text-decoration: underline;
	color: #FF0000;
}

.icono_cerrar a:hover {
	color: #0000FF;
}

.div_nombre {
	text-align: left;
	padding-left: 4px;
}

.div_comentario {
	text-align: justify;
	padding: 0px 10px 4px 16px;
	text-transform: uppercase;
	margin-top: 2px;
}

.format_name {
	color: #FF0000;
	text-transform: uppercase;
}

.notificacion {
	height: 21px;
	background: #f5f5f5;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	width: 99%;
}

.barra_menu {
	background-color:#333;
	height:24px;
	border-bottom: 1px solid #232323;
}
.app-form input{
	border-radius: 0;
}
.taskDone {
	text-decoration: line-through;
}
</style>
<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-migrate-1.2.1.min.js"></script>
<!--<script type="text/javascript" src="http://malsup.github.com/jquery.cycle.all.js"></script> -->
<!--<script src="js/jquery.cycle.all.js"></script>-->
<script type="text/javascript">
	$(document).ready(function(){ // Script del Navegador
//		$("ul.subnavegador").not('.selected').hide();
//		
//		$("a.desplegable").click(function(e){
//		  var desplegable = $(this).parent().find("ul.subnavegador");
//		  $('.desplegable').parent().find("ul.subnavegador").not(desplegable).slideUp('slow');
//		  desplegable.slideToggle('slow');
//		  e.preventDefault();
//		});
		
//		$("#div_notificacion").html("");
//		//var html = "<marquee width=\"100%\" scrolldelay=\"100\" direction=\"right\" loop=\"3\" behavior=\"alternate\">" +
//		var html = 'Según el Art. 216 numeral 4 del Reglamento a la LOEI: "Las calificaciones, una vez anotadas en Secretaría, no pueden ser alteradas. Sólo en caso de error o cálculo de apreciación, o de recalificación justificada y aprobada, el Rector o Director puede autorizar el cambio del registro de las calificaciones."';
//				   //+ "</marquee>";
//		$("#div_notificacion").addClass("notificacion");
//		$("#div_notificacion").html(html);

//		$("#icono_cerrar").on('click',function(e) {
//			e.preventDefault();
//			$("#barra_notificaciones").slideUp();
//		});
//		
//		var $container = $('#div_notificacion');
//		 
//		quotes = [
//		{nombre: 'Administrador', mensaje: 'Compañeros docentes, se encuentra abierto el sistema desde el 19 de agosto...'},
//		{nombre: 'Administrador', mensaje: 'Compañeros docentes, solamente se podrán ingresar calificaciones de exámenes remediales...'},
//		{nombre: 'Administrador', mensaje: 'Por favor compañeros docentes, cambiar su contraseña periódicamente...'}
//		];
//		 
//		$(quotes).each(function () {
//			var quote = '<span>' + this.nombre + ' dice </span> - <span>' + this.mensaje + '</span>';
//			$container.append('<div class="notificacion">' + quote + '</div>');
//		});
//		
//		$container.cycle({
//			fx: 'fade',
//			speed: '2000',
//			timeout: '7500',//1000=1seg
//			cleartype: '1' // activar correcciones cleartype
//		});
		
		// Aqui va la funcion para listar los comentarios

		$("#comentarios").on('click',function(){
			cargarComentarios();
			$("#div_mensajes").slideUp();
			$("#div_comentarios").slideToggle();
		});

		// Aqui va la funcion para listar los mensajes del administrador

		$("#mensajes").on('click',function(){
			listarMensajes();
			$("#div_comentarios").slideUp();
			$("#div_mensajes").slideToggle();
			//$("#barra_notificaciones").slideDown();
		});
		
		// Por default tengo que presentar el numero de mensajes del administrador
		//mostrarNumeroMensajes();

		// Aqui va el codigo para llamar a la función que muestra los mensajes del administrador
		// cada x número de milisegundos (se va a estar llamando la función cada cierto tiempo)
		
		//setInterval(mostrarNumeroMensajes, 1000);
		
	});
	
	function mostrarNumeroMensajes() {
		$.get("scripts/mostrar_numero_mensajes.php",
			function(resultado) {
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#mensajes").html(resultado);
				}
			}
		);
	}
	
	function listarComentarios(numero_pagina)
	{
		$.post("scripts/cargar_comentarios.php", 
			{
				cantidad_registros: 4,
				numero_pagina: numero_pagina
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_comentarios").html(resultado);
				}
			}
		);
	}

	function cargarComentarios()
	{
		contarComentarios(); //Esta funcion desencadena las demas funciones de paginacion
	}

	function contarComentarios()
	{
		$.post("scripts/contar_comentarios.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONNumRegistros = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistros.num_registros;
					$("#num_comentarios").html("N&uacute;mero de Comentarios encontrados: "+total_registros);
					paginarComentarios(4,1,total_registros);
				}
			}
		);
	}

	function paginarComentarios(cantidad_registros, num_pagina, total_registros)
	{
		$.post("scripts/paginar_comentarios.php",
			{
				cantidad_registros: cantidad_registros,
				num_pagina: num_pagina,
				total_registros: total_registros
			},
			function(resultado)
			{
				$("#paginacion_comentarios").html(resultado);
			}
		);
		listarComentarios(num_pagina);
	}

	function listarMensajes()
	{
		$.post("scripts/listar_mensajes_docentes.php",
			function(resp) {
				$("#lista_mensajes").html(resp);
			}
		);
	}

</script>
</head>

<body>
<div id="pagina">
  <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>" />
  <table id="contenido" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>  
        <table id="cabecera" class="tabla_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="50%">
                    <div class="titulo1">S I A E</div>
                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>
                </td>
                <td width="25%">
                	<table id="col_left" widtn="100%" cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td align="center">
                                <div id="mensajes" class="perfil" style="float:left;text-align:center;width:266px;">
                                   <!-- Aqui va el enlace para ver los mensajes del adiministrador -->
                                   <?php echo $num_mensajes ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<div id="usuario" class="perfil"><?php echo $nombrePeriodoLectivo ?></div>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<div id="perfil" class="perfil"><?php echo $nombreUsuario ?></div>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<div id="periodo_lectivo" class="perfil"><?php echo $nombrePerfil ?></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="*" valign="top">
                  <table id="col_right" width="100%" cellpadding="0" cellspacing="0" border="0">
                     <tr>
                        <td align="center">
                           <div id="comentarios" class="perfil" style="margin-bottom:2px;padding-right:2px;text-align:right">
                              <!-- Aqui va el enlace para ver los comentarios de los estudiantes -->
                              <a href="#" title="mostrar u ocultar los comentarios"> <?php echo $num_comentarios ?> </a>
                           </div>
                        </td>
                     </tr>
                     <tr>
                       <td align="center">
                           <div class="fecha">
                              <!-- Aqui va la fecha del sistema generada mediante PHP -->
                              <?php echo fecha_actual(); ?>
                           </div>
                       </td>
                     </tr>
                     <tr>
                       <td>  
                          <table id="tabla_login" width="100%" cellpadding="0" cellspacing="0" border="0">
                             <tr>
                                <td width="95%" align="right">
                                  <div class="login">
                                     <a href="logout.php">Salir</a>
                                  </div>
                                </td>
                                <td width="*" align="right">
                                  <div class="botones">
                                      <a href="logout.php">
                                        <img src="imagenes/login_gnome.png" onmouseover="this.src='imagenes/login_gnome1.png'" onmouseout="this.src='imagenes/login_gnome.png'" alt="haga click para salir..." title="Salir del Sistema..." />
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
	<tr id="barra_de_notificaciones" style="background-color:#f5f5f5">
      <td>
        <div id="barra_notificaciones" class="hide">
            <table id="notificador" width="100%" border="0" cellpadding="0" cellspacing="0">
               <tr>
                   <td width="100%" align="left">
                      <div id="div_notificacion"> <!--<a href="#"> Reportar un error </a>--> </div>
                   </td>
                   <td width="*">
                      <div id="icono_cerrar" class="icono_cerrar"> <a href="#" title="cerrar notificaciones"> X </a> </div>
                  </td> 
               </tr>
            </table>
        </div>
      </td>
    </tr>
	<tr>
      <td>
        <div id="div_mensajes" style="display:none">
           <div class="header2"> LISTA DE MENSAJES DEL ADMINISTRADOR </div>
           <div id="lista_mensajes" style="text-align:center"> </div>
        </div>   
        <div id="div_comentarios" style="display:none">
           <!-- Aqui va la paginacion de los comentarios ingresados por los estudiantes -->
           <div id="total_registros" class="paginacion">
              <table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
                <tr>
                    <td>
                        <div id="num_comentarios">&nbsp;N&uacute;mero de Comentarios encontrados:&nbsp;</div>
                    </td>
                    <td>
                        <div id="paginacion_comentarios"> 
                            <!-- Aqui va la paginacion de los comentarios --> 
                        </div>
                    </td>
                </tr>
              </table>
           </div>
           <div class="header2"> LISTA DE COMENTARIOS INGRESADOS </div>
           <div id="lista_comentarios" style="text-align:center"> </div>
        </div>   
      </td>
    </tr>    
    <tr>
      <td>
         <div id="cuerpo">
            <!-- Aqui va el cuerpo de la pagina en si-->
			<div class="barra_menu">
            	<?php echo $menu_horizontal ?>
            </div>
			<div id="contenido">
                <?php include($enlace); ?>
            </div>
         </div>   
      </td>
    </tr>
  </table>      
</div>
</body>
</html>
