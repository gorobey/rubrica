<?php
	include_once("../funciones/funciones_sitio.php");
	if (!isset($_GET['enlace'])) 
		$enlace = "central2.html";
	else
		$enlace = $_GET['enlace'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB Consulta de Calificaciones Estudiantiles</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<link href="../css/coolMenu.css" rel="stylesheet" type="text/css" media="screen"/>
<link rel="shortcut icon" href="../favicon.ico" />
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
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
						Consulta de Calificaciones Estudiantiles
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
                  <!-- Aqui va el menu de opciones de consulta -->
                    <div style="background-color:#333;height:24px">
						<ul id="coolMenu">
                        	<li> <a href="index.php?enlace=por_rubrica.php"> Por R&uacute;brica </a> </li>
                            <li> <a href="index.php?enlace=por_aporte.php"> Por Parcial </a> </li>
                            <li> <a href="index.php?enlace=por_quimestre.php"> Por Quimestre </a> </li>
                            <li> <a href="index.php?enlace=por_anio_lectivo.php"> Por A&ntilde;o Lectivo </a> </li>
                            <li> <a href="index.php?enlace=comentarios.php"> Comentarios </a> </li>
                        </ul>
                    </div>
                    <div id="contenido">
                        <?php include($enlace); ?>
                    </div>
              </td>
           </tr>   
        </table>
      </div>
    </tr>
  </table>
</div>
</body>
</html>
