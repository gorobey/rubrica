<?php
	if (!isset($_SESSION['usuario_logueado']) OR !$_SESSION['usuario_logueado'])
		header("Location: index.php");
	else {
		if (!isset($_SESSION['id_usuario']) OR !$_SESSION['id_usuario'])
			header("Location: index.php");
		else {
			require_once("scripts/clases/class.mysql.php");
			require_once("scripts/clases/class.usuarios.php");
			$usuario = new usuarios();
			$usuarios = $usuario->obtenerUsuario($_SESSION['id_usuario']);
			$us_fullname = $usuarios->us_fullname;
			$password = $usuarios->us_password;
		}
	}
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;bricas Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		document.getElementById("clave_actual").focus();
		$("#actualizarClave").click(function(e){
			e.preventDefault();
			cambiarClave();
		});		
		$("#limpiarFormulario").click(function(e){
			e.preventDefault();
			limpiarFormulario();
		});		
	});

	function cambiarClave()
	{
		var clave_actual = document.getElementById("clave_actual").value;
		var clave_nueva = document.getElementById("clave_nueva").value;	
		var clave_confirmada = document.getElementById("clave_confirmada").value;
		
		clave_actual = eliminaEspacios(clave_actual);
		clave_nueva = eliminaEspacios(clave_nueva);
		clave_confirmada = eliminaEspacios(clave_confirmada);
		
        var reg=/(^[a-zA-Z0-9]{4,40}$)/;
		
		if(!reg.test(clave_actual)) {
			$("#mensaje").html("Debe ingresar al menos cuatro caracteres entre letras y numeros.");
			document.getElementById("clave_actual").focus();
		} else if(!reg.test(clave_nueva)) {
			$("#mensaje").html("Debe ingresar al menos cuatro caracteres entre letras y numeros.");
			document.getElementById("clave_nueva").focus();
		} else if(!reg.test(clave_confirmada)) {
			$("#mensaje").html("Debe ingresar al menos cuatro caracteres entre letras y numeros.");
			document.getElementById("clave_confirmada").focus();
		} else if(clave_confirmada!=clave_nueva) {
			$("#mensaje").html("Clave nueva y clave confirmada no coinciden. Reintente nuevamente.");
			document.getElementById("clave_confirmada").focus();
		} else {

			//Aqui va el script para actualizar la clave mediante ajax
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "scripts/actualizar_clave.php",
					data: "clave="+clave_nueva+"&clave_actual="+clave_actual,
					success: function(resultado){
						$("#mensaje").html(resultado);
				  }
			});			
		}
	}

	function limpiarFormulario()
	{
		document.getElementById("clave_actual").value="";
		document.getElementById("clave_nueva").value="";
		document.getElementById("clave_confirmada").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("clave_actual").focus();
	}
</script>
</head>

<body>
<div id="pagina">
   <div id="titulo_pagina">
   	  <?php echo $_SESSION['titulo_pagina'] . " [" . $us_fullname . "]" 
	  ?>
   </div>
   <div id="frmNuevo">
      <form id="form_cambiar_clave" action="" method="post">
         <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
            <tr>
               <td width="50%" align="right">
                  Clave Actual:
               </td>
               <td width="*" align="left">
                  <input id="clave_actual" type="password" class="cajaGrande" name="clave_actual" maxlength="40" />
               </td>
            </tr>
            <tr>
               <td width="50%" align="right">
                  Clave Nueva:
               </td>
               <td width="*" align="left">
                  <input id="clave_nueva" type="password" class="cajaGrande" name="clave_nueva" maxlength="40" />
               </td>
            </tr>
            <tr>
               <td width="50%" align="right">
                  Confirmar Clave:
               </td>
               <td width="*" align="left">
                  <input id="clave_confirmada" type="password" class="cajaGrande" name="clave_confirmada" maxlength="40" />
               </td>
            </tr>
            <tr>
               <td width="50%" align="right">
                  <div id="actualizarClave" class="link_form"><a href="#">Actualizar</a></div>
               </td>
               <td width="*" align="left">
                  <div id="limpiarFormulario" class="link_form"><a href="#">Limpiar</a></div>
               </td>
            </tr>
         </table>
      </form>
   </div>
   <div id="mensaje" class="actions"></div>
</div>
</body>
</html>
