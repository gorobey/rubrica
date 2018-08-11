<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		obtenerDatosInstitucion();
		$("#img_loader").hide();
		$("#in_nombre").focus();
	});
	function obtenerDatosInstitucion()
	{
		$.ajax({
				type: "POST",
				url: "institucion/obtener_datos_institucion.php",
				success: function(resultado){
					var JSONInstitucion = eval('(' + resultado + ')');
					//Aqui se van a pintar los datos de la institucion educativa
					document.getElementById("in_nombre").value=(JSONInstitucion.in_nombre) ? JSONInstitucion.in_nombre : "";
					document.getElementById("in_direccion").value=(JSONInstitucion.in_direccion) ? JSONInstitucion.in_direccion : "";
					document.getElementById("in_telefono1").value=(JSONInstitucion.in_telefono1) ? JSONInstitucion.in_telefono1 : "";
					document.getElementById("in_nom_rector").value=(JSONInstitucion.in_nom_rector) ? JSONInstitucion.in_nom_rector : "";
					document.getElementById("in_nom_secretario").value=(JSONInstitucion.in_nom_secretario) ? JSONInstitucion.in_nom_secretario : "";
					document.getElementById("in_nombre").focus();
			  }
		});			
	}
	function limpiarInstitucion()
	{
		document.getElementById("in_nombre").value = "";
		document.getElementById("in_direccion").value = "";
		document.getElementById("in_telefono1").value = "";
		document.getElementById("in_nom_rector").value = "";
		document.getElementById("in_nom_secretario").value = "";
		document.getElementById("in_nombre").focus();
	}
	function actualizarInstitucion()
	{
		var in_nombre = eliminaEspacios(document.getElementById("in_nombre").value);
		var in_direccion = eliminaEspacios(document.getElementById("in_direccion").value);
		var in_telefono1 = eliminaEspacios(document.getElementById("in_telefono1").value);
		var in_nom_rector = eliminaEspacios(document.getElementById("in_nom_rector").value);
		var in_nom_secretario = eliminaEspacios(document.getElementById("in_nom_secretario").value);
		$("#img_loader").show();
		$.post("institucion/actualizar_datos_institucion.php", 
			{ 
				in_nombre: in_nombre,
				in_direccion: in_direccion,
				in_telefono1: in_telefono1,
				in_nom_rector: in_nom_rector,
				in_nom_secretario: in_nom_secretario
			},
			function(resultado)
			{
				$("#img_loader").hide();
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#mensaje").html(resultado);
					document.getElementById("in_nombre").focus();
				}
			}
		);
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "ADMINISTRAR " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="formulario" class="form_nuevo">
    	<div id="tituloForm" class="header">Datos de la Instituci&oacute;n</div>
        <form id="form_nuevo" action="" method="post">
           <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
              <tr>
                 <td width="15%" align="right">Nombre:</td>
                 <td width="*">
                    <input id="in_nombre" type="text" class="cajaGrande" name="in_nombre" maxlength="64" />
                 </td>
              </tr>
              <tr>
                 <td width="15%" align="right">Direcci&oacute;n:</td>
                 <td width="*">
                    <input id="in_direccion" type="text" class="cajaGrande" name="in_direccion" maxlength="64" />
                 </td>
              </tr>
              <tr>
                 <td width="15%" align="right">Tel&eacute;fono:</td>
                 <td width="*">
                    <input id="in_telefono1" type="text" class="cajaMedia" name="in_telefono1" maxlength="12" />
                 </td>
              </tr>
              <tr>
                 <td width="15%" align="right">Nombre del Rector(a):</td>
                 <td width="*">
                    <input id="in_nom_rector" type="text" class="cajaGrande" name="in_nom_rector" maxlength="36" />
                 </td>
              </tr>
              <tr>
                 <td width="15%" align="right">Nombre del Secretario(a):</td>
                 <td width="*">
                    <input id="in_nom_secretario" type="text" class="cajaMedia" name="in_nom_secretario" maxlength="36" />
                 </td>
              </tr>
              <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
							  <div id="boton_accion">
                                 <div id="actualizarInstitucion" class="link_form"><a href="#" onclick="actualizarInstitucion()">Actualizar</a></div>
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="limpiarInstitucion()">Limpiar</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
              </tr>
           </table>
        </form>
    </div>
	<div id="img_loader"> <img src="imagenes/ajax-loader.gif" alt="Procesando..." /> </div>
    <div id="mensaje"> </div>
</div>
</body>
</html>
