<?php
include_once("../funciones/funciones_sitio.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB Login</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
    <style type="text/css">
		.captcha {
			background-color: #bbb;
			color: #fff;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 1.2em;
			text-align: center;
			width: 60px;
		}
		.inputCaptcha {
			border: 1px solid #000;
			font: 9pt helvetica;
			margin-top: 1px;
			width: 75px;
		}
	</style>
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_periodos();
		//generar_captcha();
		//$("input").val("");
		$("#txt_cedula").focus();
		
		$("#refresh").on("click", function(e) {
			e.preventDefault();
			//generar_captcha();
			document.location.reload();
			$("#txt_cedula").focus();
		});
		
		$('#login-form').submit(function(e) {
		
			var cedula = document.getElementById("txt_cedula").value;
			var id_periodo_lectivo = document.getElementById("cboPeriodo").value;
			var captcha = document.getElementById("captcha").value;

			e.preventDefault();

			// Saco los espacios en blanco al comienzo y al final de la cadena
			cedula=eliminaEspacios(cedula);
			captcha=eliminaEspacios(captcha);

			// Valido con una expresion regular el contenido de lo que el usuario ingresa
			var reg=/(^[0-9]{5,10}$)/;
			if(!reg.test(cedula))
			{
				var mensaje = "Debes ingresar al menos 5 caracteres num&eacute;ricos";
				$("#mensaje").html(mensaje);
				document.getElementById("txt_cedula").focus();
			}
			else if(id_periodo_lectivo==0)
			{
				var mensaje = "Debes seleccionar un periodo lectivo";
				$("#mensaje").html(mensaje);
				document.getElementById("cboPeriodo").focus();
			}
			else if(captcha=="")
			{
				var mensaje = "Debes ingresar el c&oacute;digo captcha generado";
				$("#mensaje").html(mensaje);
				document.getElementById("captcha").focus();
			}
			else
			{
				$("#mensaje").html('');
				$("#img_loader").css("display","block");
				$.post("../scripts/verificar_login_estudiante.php", 
					{ 
						cedula: cedula,
						id_periodo_lectivo: id_periodo_lectivo,
						captcha: captcha
					}, 
					function(resp) {
					
						if (!resp.error) {
							
							//No hay error se redirecciona al admin
							location.href = "admin.php?id_estudiante=" + resp['id_estudiante'];
							
						} else {
						
							//No existe el usuario
							var error = '<span class="error">' +
												'No existe el estudiante solicitado o el c&oacute;digo captcha no coincide.' +
											'</span>';
							$("#img_loader").css("display","none");
							$("#mensaje").html(error);
							document.getElementById("txt_cedula").focus();
						
						}
				}, 'json');
			}
		});	
	});

	function cargar_periodos()
	{
		$.get("../periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){
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
	
//	function generar_captcha() {
//		$.get("../scripts/captcha.php", function(respuesta) {
//			$("#captcha_generated").html(respuesta);
//		});	
//	}

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
                                    <img src="../imagenes/login_gnome.png" onmouseover="this.src='../imagenes/login_gnome1.png'" onmouseout="this.src='../imagenes/login_gnome.png'" alt="haga click para ir a la pagina principal..." title="Ir al Inicio..." />                                  </a>                              </div>
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
                        <td width="12"><img src="../imagenes/top_left.gif" width="12" height="13" border="0" alt="" /></td>
                        <td style="background-image: url('../imagenes/top_top.gif'); "><img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>
                        <td width="12"><img src="../imagenes/top_right.gif" width="12" height="13" border="0" alt="" /></td>
                    </tr>
                    <tr>
                        <td width="12" style="background-image: url('../imagenes/left.gif'); "><img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>
                        <td style="color:#666666;" >

                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="20" height="20" alt="" /></td>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="60" height="1" alt="" /></td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="center"><img src="../imagenes/spacer.gif" border="0" width="50" height="1" alt="" /><span class="titulo_login">Introduce tus datos personales</span></td>
                                </tr>
                                <tr>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="1" height="30" alt="" /></td>
                                </tr>
                                <tr id="div_usuario">
                                    <td align="right"><img src="../imagenes/spacer.gif" border="0" width="50" height="1" alt="" />C&eacute;dula:&nbsp;</td>
                                    <td>
                                        <input type="text" class="inputGrande"  maxlength="10" name="txt_cedula" id="txt_cedula" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><img src="../imagenes/spacer.gif" border="0" width="50" height="1" alt="" /></td>
                                </tr>
                                <tr>
                                    <td align="right"><img src="../imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Periodo:&nbsp;</td>
                                    <td><select id="cboPeriodo" name="cboPeriodo" class="comboMedio">
                                           <option value="0">Seleccione...</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><img src="../imagenes/spacer.gif" border="0" width="50" height="1" alt="" />Captcha:&nbsp;  </td>
                                    <td>
                                    	<table width="100%" cellpadding="1" cellspacing="0" border="0">
                                        	<tr>
                                            	<td>
                                                	<div id="captcha_generated" class="captcha">
                                                    	<img src="captcha.php" alt="captcha" title="captcha" />
                                                    </div>
                                                </td>
                                                <td>
                                                	<img id="refresh" src="../imagenes/refresh.png" alt="refresh" title="actualizar" />
                                                </td>
                                                <td>
                                                    <input type="text" class="inputCaptcha" id="captcha" />
                                                </td>
                                                <td width="*">&nbsp;
                                                	
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="1" height="20" alt="" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">
                                        <img src="../imagenes/spacer.gif" border="0" width="65" height="1" alt="" />
                                        <input type="hidden" name="submit" value="Login" />
                                        <input src="../imagenes/Login.png" onmouseover='this.src="../imagenes/login_off.png"' onmouseout='this.src="../imagenes/Login.png"' type="image" name="submit" value="Login" /></td>

                                </tr>
                                <tr>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="20" height="20" alt="" /></td>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>
                                    <td><img src="../imagenes/spacer.gif" border="0" width="60" height="1" alt="" /></td>
                                </tr>

                            </table>

                        </td>
                        <td width="12" style="background-image: url('../imagenes/right.gif'); ">
                            <img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" />
                        </td>
                    </tr>
                    <tr>
                        <td width="12"><img src="../imagenes/bottom_left.gif" width="12" height="12" border="0" alt="" /></td>
                        <td style="background-image: url('../imagenes/down.gif'); "><img src="../imagenes/spacer.gif" border="0" width="1" height="1" alt="" /></td>
                        <td width="12"><img src="../imagenes/bottom_right.gif" width="12" height="12" border="0" alt="" /></td>
                    </tr>
                </table>
        </form>
      </td>
    </tr>
  </table>
  <div id="img_loader" style="display:none;text-align:center">
	<img src="../imagenes/ajax-loader.gif" alt="Procesando...">  
  </div>
  <div id="mensaje" class="actions">
  	 <!-- Aqui van los mensajes de error -->
  </div>
</div>
</body>
</html>
