<?php
	require_once("funciones/funciones_sitio.php");
	$con = mysql_connect("localhost","root","sa");
	mysql_select_db("rubrica", $con);
	$sql = "select distinct (us_login) from sw_usuario order by us_login";
	$res = mysql_query($sql);
	$arreglo_php = array();
	if(mysql_num_rows($res)==0)
	   array_push($arreglo_php, "No hay datos");
	else{
	  while($palabras = mysql_fetch_array($res)){
		array_push($arreglo_php, $palabras["us_login"]);
	  }
	}
	// Libera la memoria del resultado
	mysql_free_result($res);
	
	// Cierra la conexión con la base de datos 
	mysql_close($con);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB Login</title>
<link href="estilos.css" rel="stylesheet" type="text/css" />
<link href="css/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<link rel="shortcut icon" href="favicon.ico" />
<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/background.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
        var autocompletar = new Array();
        <?php //Esto es un poco de php para obtener lo que necesitamos
          for($p = 0;$p < count($arreglo_php); $p++){ //usamos count para saber cuantos elementos hay ?>
            autocompletar.push('<?php echo $arreglo_php[$p]; ?>');
        <?php } ?>
        $("#uname").autocomplete({ //Usamos el ID de la caja de texto donde lo queremos
           source: autocompletar //Le decimos que nuestra fuente es el arreglo
        });
		//para cambiar el color de fondo de los input
		$("input").setBackground({
			'background-color' : 'AQUA'
		});
		cargar_periodos();
		cargar_perfiles();
		document.getElementById("uname").value="";
		document.getElementById("uname").focus();
		$('#login-form').submit(function(e) {
		
			var login = document.getElementById("uname").value;
			var passwd = document.getElementById("passwd").value;
			var id_periodo_lectivo = document.getElementById("cboPeriodo").value;
			var id_perfil = document.getElementById("cboPerfil").value;

			e.preventDefault();

			// Saco los espacios en blanco al comienzo y al final de la cadena
			login=eliminaEspacios(login);
			passwd=eliminaEspacios(passwd);

			// Valido con una expresion regular el contenido de lo que el usuario ingresa
			var reg=/(^[a-zA-Z0-9]{4,40}$)/;
			if(!reg.test(login))
			{
				var mensaje = "El nombre del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos";
				$("#mensaje").html(mensaje);
				document.getElementById("uname").focus();
			}
			else if(!reg.test(passwd))
			{
				var mensaje = "La clave del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos";
				$("#mensaje").html(mensaje);
				document.getElementById("passwd").focus();
			}
			else if(id_periodo_lectivo==0)
			{
				var mensaje = "Debe elegir el periodo lectivo";
				$("#mensaje").html(mensaje);
				document.getElementById("cboPeriodo").focus();
			}
			else if(id_perfil==0)
			{
				var mensaje = "Debe elegir el perfil";
				$("#mensaje").html(mensaje);
				document.getElementById("cboPerfil").focus();
			}
			else
			{
				$("#mensaje").html('<img src="imagenes/ajax-loader-world.gif">');
				$.post("scripts/verificar_login.php", $(this).serialize(), function(resp) {
					
					if (!resp.error) {
						
						//No hay error se redirecciona al admin
						location.href = "admin.php?id_usuario=" + resp['id_usuario'] + "&id_menu=0";
						
					} else {
					
					    //No existe el usuario
						var error = '<span class="error">' +
										'Datos incorrectos...' +
									'</span>';
						$("#mensaje").html(error);
						document.getElementById("uname").focus();
					
					}
				}, 'json');
			}
		});	
	});
	
	function cargar_periodos()
	{
		$.get("periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPeriodo').append(resultado);			
			}
		});	
	}

	function cargar_perfiles()
	{
		$.get("scripts/cargar_perfiles.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPerfil').append(resultado);			
			}
		});	
	}
	
</script>
</head>

<body>
<div id="pagina">
  <table id="contenido" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="25%">  
        <table class="tabla_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="70%">
                    <div class="titulo1">S I A E</div>
                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>                </td>
                <td valign="top">
                    <div class="fecha">
                        <!-- Aqui va la fecha del sistema generada mediante PHP -->
                        <?php echo fecha_actual(); ?>                    </div>
                    <div>
                      <table id="tabla_login" width="100%" cellpadding="0" cellspacing="0" border="0">
                         <tr>
                            <td width="95%" align="right">
                              <div class="login">
                                 <a href="index.php">Cancelar</a>                              </div>                            </td>
                            <td width="*" align="right">
                              <div class="botones">
                                  <a href="index.php">
                                    <img src="imagenes/login_gnome.png" onmouseover="this.src='imagenes/login_gnome1.png'" onmouseout="this.src='imagenes/login_gnome.png'" alt="haga click para ir a la pagina principal..." title="ir al index..." />                                  </a>                              </div>                            </td>  
                         </tr>
                      </table>   
                    </div>                </td>
            </tr>
        </table>      </td>
    </tr>
    <tr>
      <td height="*" align="center" valign="middle">
        <!-- Aqui va el formulario del login -->
        <form id="login-form" action="" method="post">
			<!-- Aqui va el contenido del formulario -->
            <div class="redondo">
            	<span class="titulo_login">Introduzca usuario y contrase&ntilde;a</span>
                <div class="izquierda">
                	<span>Usuario:&nbsp;</span>
                    <br />
                    <div style="margin-top:8px">Clave:&nbsp;</div>
                    <div style="margin-top:8px"><span>Periodo:&nbsp;</span></div>
                    <div style="margin-top:8px">Perfil:&nbsp;</div>
                </div>
                <div class="derecha">
                  <span>
                    <input type="text" class="inputGrande" maxlength="40" name="uname" id="uname" value="" style="text-transform:uppercase" />
                  </span><br />
                    <div style="margin-top:1px"><input type="password" class="inputGrande" maxlength="40" name="passwd" id="passwd" value="" /></span>
                    <div style="margin-top:1px"><select id="cboPeriodo" name="cboPeriodo" class="comboMedio"> <option value="0">Seleccione...</option> </select></div>
                    <div style="margin-top:1px"><select id="cboPerfil" name="cboPerfil" class="comboMedio"> <option value="0">Seleccione...</option> </select></div>
                </div>
            </div>
  <div style="clear:both; margin-top:2px;">
                <input onmouseover='this.src="imagenes/login_off.png"' onmouseout='this.src="imagenes/login.png"' type="image" src="imagenes/login.png" name="submit" value="Login" />
            </div>
        </form>      </td>
    </tr>
  </table>
 <div id="mensaje" class="actions">
  	 <!-- Aqui van los mensajes de error o la imagen del loader -->
  </div>   
</div>
</body>
</html>
