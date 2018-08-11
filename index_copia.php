<?php
include_once("funciones/funciones_sitio.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB</title>
<link href="estilos.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
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
                                 <a href="login.php">Login</a>
                              </div>
                            </td>
                            <td width="*" align="right">
                              <div class="botones">
                                  <a href="login.php">
                                    <img src="imagenes/login_gnome.png" onmouseover="this.src='imagenes/login_gnome1.png'" onmouseout="this.src='imagenes/login_gnome.png'" alt="haga click para loguearse..." title="Login..." />
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
      <td height="60%" align="center">
        <img src="imagenes/escudo-salamanca.png" alt="Escudo de Salamanca" />      
      </td>
    </tr>
    <tr>
      <td height="15%">
        <div class="pie">
          Tecnolog&iacute;as utilizadas: Ajax, JQuery, PHP y MySQL<br/>
          .: &copy; <?php echo date("  Y"); ?> - Unidad Educativa PCEI Fiscal Salamanca :.
        </div>
      </td>
    </tr>
  </table>      
</div>
</body>
</html>
