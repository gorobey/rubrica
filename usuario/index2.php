<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_perfiles();
		listarUsuarios(false);	    
		$("#nuevo_usuario").click(function(e){
			e.preventDefault();
			nuevoUsuario();
		});
		$("#cboPerfiles").change(function(){
			listarUsuarios(false);
			$("#formulario_nuevo").css("display","none");
		});		
	});

	function cargar_perfiles()
	{
		$.get("scripts/cargar_perfiles.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPerfiles').append(resultado);			
			}
		});	
	}

	function listarUsuarios(iDesplegar)
	{
		var id_perfil = document.getElementById("cboPerfiles").value;
		$.get("usuario/listar_usuarios.php", { id_perfil: id_perfil },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_usuarios").html(resultado);
				}
			}
		);
	}

	function limpiarUsuario()
	{
		document.getElementById("us_titulo").value="";
		document.getElementById("us_apellidos").value="";
		document.getElementById("us_nombres").value="";
		document.getElementById("us_fullname").value="";
		document.getElementById("us_login").value="";
		document.getElementById("us_password").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("us_titulo").focus();
	}

	function salirUsuario()
	{
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_usuario").focus();
	}

	function nuevoUsuario()
	{
		limpiarUsuario();
		$("#tituloForm").html("NUEVO USUARIO");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarUsuario()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("us_titulo").focus();
	}

	function insertarUsuario()
	{
		// Validación de la entrada de datos
		var id_perfil = document.getElementById("cboPerfiles").value;
		var us_titulo = document.getElementById("us_titulo").value;
		var us_apellidos = document.getElementById("us_apellidos").value;
		var us_nombres = document.getElementById("us_nombres").value;
		var us_fullname = document.getElementById("us_fullname").value;
		var us_login = document.getElementById("us_login").value;
		var us_password = document.getElementById("us_password").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		us_titulo=eliminaEspacios(us_titulo);
		us_apellidos=eliminaEspacios(us_apellidos);
		us_nombres=eliminaEspacios(us_nombres);
		us_fullname=eliminaEspacios(us_fullname);
		us_login=eliminaEspacios(us_login);
		us_password=eliminaEspacios(us_password);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_password = /^([a-zA-Z0-9]{4,12})$/i;
		var reg_titulo = /^([a-zA-Z.]{3,5})$/i;
		
		if (id_perfil==0) {
			$("#mensaje").html("Debe escoger el perfil...");
			document.getElementById("cboPerfiles").focus();
    	} else if(!reg_titulo.test(us_titulo)) {
			$("#mensaje").html("El t&iacute;tulo del usuario debe contener al menos tres caracteres alfab&eacute;ticos");
			document.getElementById("us_titulo").focus();
    	} else if(!reg_texto.test(us_apellidos)) {
			$("#mensaje").html("Los apellidos del usuario deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("us_apellidos").focus();
    	} else if(!reg_texto.test(us_nombres)) {
			$("#mensaje").html("Los nombres del usuario deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("us_nombres").focus();
    	} else if(!reg_texto.test(us_fullname)) {
			$("#mensaje").html("El nombre completo del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos");
			document.getElementById("us_fullname").focus();
		} else if(!reg_texto.test(us_login)) {
			$("#mensaje").html("El login del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos");
			document.getElementById("us_login").focus();
		} else if(!reg_password.test(us_password)) {
			$("#mensaje").html("La clave del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos");
			document.getElementById("us_password").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "usuario/insertar_usuario.php",
					data: "id_perfil="+id_perfil+"&us_titulo="+us_titulo+"&us_apellidos="+us_apellidos+"&us_nombres="+us_nombres+"&us_fullname="+us_fullname+"&us_login="+us_login+"&us_password="+us_password,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarUsuarios(true);
						salirUsuario();
				  }
			});			
		}	
	}

	function actualizarUsuario()
	{
		// Validación de la entrada de datos
		var id_usuario = document.getElementById("id_usuario").value;
		var id_perfil = document.getElementById("cboPerfiles").value;
		var us_titulo = document.getElementById("us_titulo").value;
		var us_apellidos = document.getElementById("us_apellidos").value;
		var us_nombres = document.getElementById("us_nombres").value;
		var us_fullname = document.getElementById("us_fullname").value;
		var us_login = document.getElementById("us_login").value;
		var us_password = document.getElementById("us_password").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		us_titulo=eliminaEspacios(us_titulo);
		us_apellidos=eliminaEspacios(us_apellidos);
		us_nombres=eliminaEspacios(us_nombres);
		us_fullname=eliminaEspacios(us_fullname);
		us_login=eliminaEspacios(us_login);
		us_password=eliminaEspacios(us_password);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_password = /(^[a-zA-Z0-9]{4,40}$)/;
		var reg_titulo = /^([a-zA-Z.]{3,5})$/i;
		
		if (id_perfil==0) {
			var mensaje = "Debe escoger el perfil...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPerfiles").focus();
    	} else if(!reg_titulo.test(us_titulo)) {
			$("#mensaje").html("El t&iacute;tulo del usuario debe contener al menos tres caracteres alfab&eacute;ticos");
			document.getElementById("us_titulo").focus();
    	} else if(!reg_texto.test(us_apellidos)) {
			$("#mensaje").html("Los apellidos del usuario deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("us_apellidos").focus();
    	} else if(!reg_texto.test(us_nombres)) {
			$("#mensaje").html("Los nombres del usuario deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("us_nombres").focus();
    	} else if(!reg_texto.test(us_fullname)) {
			var mensaje = "El nombre completo del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("us_fullname").focus();
		} else if(!reg_texto.test(us_login)) {
			var mensaje = "El login del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("us_login").focus();
		} else if(!reg_password.test(us_password)) {
			var mensaje = "La clave del usuario debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("us_password").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "usuario/actualizar_usuario.php",
					data: "id_usuario="+id_usuario+"&id_perfil="+id_perfil+"&us_titulo="+us_titulo+"&us_apellidos="+us_apellidos+"&us_nombres="+us_nombres+"&us_fullname="+us_fullname+"&us_login="+us_login+"&us_password="+us_password,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarUsuarios(true);
						salirUsuario();
				  }
			});			
		}	
	}

	function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
	}

	function editarUsuario(id_usuario)
	{
		limpiarUsuario();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR USUARIO");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarUsuario()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "usuario/obtener_usuario.php",
				data: "id_usuario="+id_usuario,
				success: function(resultado){
					var JSONUsuario = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el usuario elegido
					document.getElementById("id_usuario").value=JSONUsuario.id_usuario;
					document.getElementById("id_perfil").value=JSONUsuario.id_perfil;
					document.getElementById("us_titulo").value=JSONUsuario.us_titulo;
					document.getElementById("us_apellidos").value=JSONUsuario.us_apellidos;
					document.getElementById("us_nombres").value=JSONUsuario.us_nombres;
					document.getElementById("us_fullname").value=JSONUsuario.us_fullname;
					document.getElementById("us_login").value=JSONUsuario.us_login;
					document.getElementById("us_password").value=JSONUsuario.us_password;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("us_titulo").focus();
					setearIndice("cboPerfiles",JSONUsuario.id_perfil);
			  }
		});			
	}

	function eliminarUsuario(id_usuario,nombre)
	{
		// Validación de la entrada de datos
		
		if (id_usuario==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_usuario...");
			document.getElementById("cboPerfiles").focus();
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Usuario [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "usuario/eliminar_usuario.php",
						data: "id_usuario="+id_usuario,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarUsuarios(true);
							salirUsuario();
					  }
				});			
			}
		}	
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo $_SESSION['titulo_pagina'] ?>
    </div>
   <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td class="fuente9">&nbsp;Perfil: &nbsp;</td>
            <td> <select id="cboPerfiles" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="nuevo_usuario" class="boton"> <a href="#"> Nuevo Usuario </a> </div> </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Usuario</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">T&iacute;tulo:</td>
                  <td width="*">
                     <input id="us_titulo" type="text" class="cajaPequenia" name="us_titulo" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Apellidos:</td>
                  <td width="*">
                     <input id="us_apellidos" type="text" class="cajaGrande" name="us_apellidos" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Nombres:</td>
                  <td width="*">
                     <input id="us_nombres" type="text" class="cajaGrande" name="us_nombres" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Nombre Completo:</td>
                  <td width="*">
                     <input id="us_fullname" type="text" class="cajaGrande" name="us_fullname" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Login:</td>
                  <td width="*">
                     <input id="us_login" type="text" class="cajaGrande" name="us_login" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Clave:</td>
                  <td width="*">
                     <input id="us_password" type="text" class="cajaGrande" name="us_password" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
							  <div id="boton_accion">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="limpiarUsuario()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirUsuario()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_usuario" name="id_usuario" />
            <input type="hidden" id="id_perfil" name="id_perfil" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_usuarios">
      <!-- Aqui va la paginacion de los usuarios encontrados -->
      <div class="header2"> LISTA DE USUARIOS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="24%" align="left">Nombre Completo</td>
                <td width="24%" align="left">Login</td>
                <td width="24%" align="left">Perfil</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_usuarios" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
