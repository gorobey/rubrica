<?php
	include_once("../funciones/funciones_sitio.php");
	//Datos de conexion para la instancia mysql.
	$host = "localhost";
	$usuario_bd = "colegion_1";
	$pass_bd = "AQSWDE123";
	$database = "colegion_1";
	//Datos de acceso para el usuario administrador del sistema.
	$usu_admin = "administrador";
	$pass_admin = "Gp67M24";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB INSTALADOR</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../favicon.ico" />
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#img_loader").hide();
		$("#user_admin").focus();
		$('#install-form').submit(function(e) {
			e.preventDefault();
			var nom_servidor = document.getElementById("nom_servidor").value;
			var user_bd = document.getElementById("user_bd").value;
			var pass_bd = document.getElementById("pass_bd").value;
			var nom_bd = document.getElementById("nom_bd").value;
			var user_admin = document.getElementById("user_admin").value;
			var pass_admin = document.getElementById("pass_admin").value;
			var pass = document.getElementById("pass").value;
			// Saco los espacios en blanco al comienzo y al final de la cadena
			user_admin=eliminaEspacios(user_admin);
			pass_admin=eliminaEspacios(pass_admin);
			// Valido con una expresion regular el contenido de lo que el usuario ingresa
			var reg=/(^[a-zA-Z0-9]{4,24}$)/;
			if(!reg.test(user_admin)) {
				var mensaje = "El nombre del usuario administrador debe contener al menos cuatro caracteres alfanum&eacute;ricos";
				$("#mensaje").html(mensaje);
				document.getElementById("user-admin").focus();
			} else if(!reg.test(pass_admin)) {
				var mensaje = "La contrase&ntilde;a del usuario administrador debe contener al menos cuatro caracteres alfanum&eacute;ricos";
				$("#mensaje").html(mensaje);
				document.getElementById("pass-admin").focus();
			} else {
				$("#mensaje").html("");
				$("#img_loader").show();
				$.post("../scripts/crear_tablas.php", 
					{
						nom_servidor: nom_servidor,
						user_bd: user_bd,
						pass_bd: pass_bd,
						nom_bd: nom_bd,
						user_admin: user_admin,
						pass_admin: pass_admin,
						pass: pass
					}, 
					function(resp) {
						$("#img_loader").hide();
						$("#mensaje").html(resp);
					}
				);				
			}
		});
	});
</script>
<style type="text/css">
<!--
.subtitulo {font-size: 10pt}
-->
</style>
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
                    <div class="titulo3" style="padding-right:2px;">
						INSTALADOR DEL S.I.A.E.
                    </div>
                </td>
            </tr>
        </table>
      </td>
    </tr>
    <tr>
      <div id="cuerpo">
        <!-- Aqui va el cuerpo de la pagina en si-->
        <table id="cuerpo" width="100%" border="0" cellpadding="0" cellspacing="0">
           <tr>
              <td>
                  <!-- Aqui va el cuerpo del instalador -->
                  <form id="install-form" action="" method="post">
                     <center>
                  		<br>
                        <table width="60%" cellpadding=15 cellspacing=5 border=10>
                        	<tr>
                           	  <td>
                               	 <div id="titulo_instalador" class="titulo-instalador"> INSTALADOR DEL S.I.A.E. </div>
                              </td>
                            </tr>
                            <tr>
                              <td>
                              	 <span class="subtitulo">Verifique si los siguientes datos son correctos:</span><br><br>
                                 <table width="100%" border="1">
                                    <tr class="cabeceraTabla">
                                      <td> Nombre </td>
                                      <td> Variable </td>
                                      <td> Valor </td>
                                    </tr>
                                    <tr class="itemImparTabla">
                                      <td> Servidor </td>
                                      <td> $host </td>
                                      <td> <input type="text" id="nom_servidor" name="nom_servidor" value="<?php echo $host; ?>" disabled="disabled" /> </td>
                                    </tr>
                                    <tr class="itemParTabla">
                                      <td> Usuario de Base de datos </td>
                                      <td> $usuario_bd </td>
                                      <td> <input type="text" id="user_bd" name="user_bd" value="<?php echo $usuario_bd; ?>" disabled="disabled" /> </td>
                                    </tr>
                                    <tr class="itemImparTabla">
                                      <td> Password usuario de BD </td>
                                      <td> $pass_bd </td>
                                      <td> <input type="text" id="pass_bd" name="pass_bd" value="<?php echo $pass_bd; ?>" disabled="disabled" /> </td>
                                    </tr>
                                    <tr class="itemParTabla">
                                      <td> Esquema de Bd </td>
                                      <td> $database </td>
                                      <td> <input type="text" id="nom_bd" name="nom_bd" value="<?php echo $database; ?>" disabled="disabled" /> </td>
                                    </tr>
                                    <tr class="itemImparTabla">
                                      <td> Usuario administrador </td>
                                      <td> $usu_admin </td>
                                      <td> <input type="text" id="user_admin" name="user_admin" value="<?php echo $usu_admin; ?>" /> </td>
                                    </tr>
                                    <tr class="itemParTabla">
                                      <td> Password usuario administrador </td>
                                      <td> $pass_admin </td>
                                      <td> <input type="text" id="pass_admin" name="pass_admin" value="<?php echo $pass_admin; ?>" /> </td>
                                    </tr>
                                 </table>
                              </td>
                            </tr>
                        </table>
                        <br><br>
                        <span class="subtitulo">Ingrese la contraseña del usuario administrador del MySQL <b>(root)</b> para crear la instancia</span><br>
                        <span class="subtitulo">y las tablas del sistema:</span>
                        <br><br><br>
                        <span class="subtitulo">Contraseña del root:</span> <input type="password" name="pass" id="pass">
                        <br><br>
                        <center>
                        <input type="submit" value="Crear">
                        </center>
                     </center>
                  </form>
              </td>
           </tr>   
        </table>
      </div>
    </tr>
  </table>
  <div id="img_loader" style="text-align:center">
	<img src="../imagenes/ajax-loader.gif" alt="Procesando...">  
  </div>
  <div id="mensaje" class="error">
  	 <!-- Aqui van los mensajes de error -->
  </div>   
</div>
</body>
</html>
