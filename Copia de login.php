<?php

include_once("funciones/funciones_sitio.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>SIAE-WEB Login</title>

<link href="estilos.css" rel="stylesheet" type="text/css" />

<link rel="shortcut icon" href="favicon.ico" />

<script type="text/javascript" src="js/jquery-1.9.1.js"></script>

<script type="text/javascript" src="js/funciones.js"></script>

<script type="text/javascript">

	$(document).ready(function(){

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

											'Usuario o password o perfil incorrectos.' +

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

                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>

                </td>

                <td valign="top">

                    <div class="fecha">

                        <!-- Aqui va la fecha del sistema generada mediante PHP -->

                        <?php echo fecha_actual(); ?>

                    </div>

                    <div>

                      <table id="tabla_login" width="100%" cellpadding="0" cellspacing="0" border="0">

                         <tr>

                            <td width="95%" align="right">

                              <div class="login">

                                 <a href="index.php">Cancelar</a>

                              </div>

                            </td>

                            <td width="*" align="right">

                              <div class="botones">

                                  <a href="index.php">

                                    <img src="imagenes/login_gnome.png" onmouseover="this.src='imagenes/login_gnome1.png'" onmouseout="this.src='imagenes/login_gnome.png'" alt="haga click para ir a la pagina principal..." title="ir al index..." />

                                  </a>

                              </div>

                            </td>  

                         </tr>

                      </table>   

                    </div>

                </td>

            </tr>

        </table>

      </td>

    </tr>

    <tr>

      <td height="*" align="center" valign="middle">

        <!-- Aqui va el formulario del login -->

        <form id="login-form" action="" method="post">

			<table border="0" cellpadding="0" cellspacing="0">

                    <tr>

                        <td width="12"><img src="imagenes/top_left.gif" width="12" height="13" border="0" alt="" /></td>

                        <td style="background-image: url('imagenes/top_top.gif'); "><img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>

                        <td width="12"><img src="imagenes/top_right.gif" width="12" height="13" border="0" alt="" /></td>

                    </tr>

                    <tr>

                        <td width="12" style="background-image: url('imagenes/left.gif'); "><img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>

                        <td style="color:#666666;" >



                            <table cellpadding="0" cellspacing="0" border="0">

                                <tr>

                                    <td><img src="imagenes/spacer.gif" border="0" width="20" height="20" alt="" /></td>

                                    <td><img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>

                                    <td><img src="imagenes/spacer.gif" border="0" width="60" height="1" alt="" /></td>

                                </tr>



                                <tr>

                                    <td colspan="2" align="center"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" /><span class="titulo_login">Introduzca usuario y contrase&ntilde;a</span></td>

                                </tr>

                                <tr>

                                    <td><img src="imagenes/spacer.gif" border="0" width="1" height="30" alt="" /></td>

                                </tr>

                                <tr>

                                    <td align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Usuario:&nbsp;</td>

                                    <td>

                                        <input type="text" class="inputGrande"  maxlength="40" name="uname" id="uname" style="text-transform:uppercase" />

                                    </td>

                                </tr>

                                <tr>

                                    <td align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" /></td>

                                    <td>

                                        <div id="lista" onmouseout="v=1;" onmouseover="v=0;"></div>

                                    </td>

                                </tr>

                                <tr>

                                    <td align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Clave:&nbsp;</td>

                                    <td><input type="password" class="inputGrande" maxlength="40" name="passwd" id="passwd" value="" /></td>

                                </tr>

                                <tr>

                                    <td colspan="3" align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" /></td>

                                </tr>

                                <tr>

                                    <td align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Periodo:&nbsp;</td>

                                    <td><select id="cboPeriodo" name="cboPeriodo" class="comboMedio">

                                           <option value="0">Seleccione...</option>

                                        </select>

                                    </td>

                                </tr>

                                <tr>

                                    <td align="right"><img src="imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Perfil:&nbsp;</td>

                                    <td><select id="cboPerfil" name="cboPerfil" class="comboMedio">

                                           <option value="0">Seleccione...</option>

                                        </select>

                                    </td>

                                </tr>

                                <tr>

                                    <td><img src="imagenes/spacer.gif" border="0" width="1" height="20" alt="" /></td>

                                </tr>

                                <tr>

                                    <td colspan="2" align="center">

                                        <img src="imagenes/spacer.gif" border="0" width="65" height="1" alt="" />

                                        <input type="hidden" name="submit" value="Login" />

                                        <input onmouseover='this.src="imagenes/login_off.png"' onmouseout='this.src="imagenes/login.png"' type="image" src="imagenes/login.png" name="submit" value="Login" /></td>



                                </tr>

                                <tr>

                                    <td><img src="imagenes/spacer.gif" border="0" width="20" height="20" alt="" /></td>

                                    <td><img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>

                                    <td><img src="imagenes/spacer.gif" border="0" width="60" height="1" alt="" /></td>

                                </tr>



                            </table>



                        </td>

                        <td width="12" style="background-image: url('imagenes/right.gif'); ">

                            <img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" />

                        </td>

                    </tr>

                    <tr>

                        <td width="12"><img src="imagenes/bottom_left.gif" width="12" height="12" border="0" alt="" /></td>

                        <td style="background-image: url('imagenes/down.gif'); "><img src="imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>

                        <td width="12"><img src="imagenes/bottom_right.gif" width="12" height="12" border="0" alt="" /></td>

                    </tr>

                </table>        

        </form>

      </td>

    </tr>

  </table>

  <div id="mensaje" class="actions">

  	 <!-- Aqui van los mensajes de error o la imagen del loader -->

  </div>   

</div>

</body>

</html>

