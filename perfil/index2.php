<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarPerfiles();	    
		$("#nuevo_perfil").click(function(e){
			e.preventDefault();
			nuevoPerfil();
		});		
		$("#limpiarPerfil").click(function(e){
			e.preventDefault();
			limpiarPerfil();
		});		
	});

	function listarPerfiles()
	{
		$.get("perfil/listar_perfiles.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_perfiles").html(resultado);
				}
			}
		);
	}

	function limpiarPerfil()
	{
		document.getElementById("pe_nombre").value="";
		document.getElementById("pe_nivel_acceso").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("pe_nombre").focus();
	}

	function salirPerfil(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").slideUp();
		document.getElementById("nuevo_perfil").focus();
	}

	function nuevoPerfil()
	{
		limpiarPerfil();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarPerfil()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").slideDown();
		document.getElementById("pe_nombre").focus();
	}

	function insertarPerfil()
	{
		// Validación de la entrada de datos
		var pe_nombre = document.getElementById("pe_nombre").value;
		var pe_nivel_acceso = document.getElementById("pe_nivel_acceso").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pe_nombre=eliminaEspacios(pe_nombre);
		pe_nivel_acceso=eliminaEspacios(pe_nivel_acceso);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_digit = /^([0-9]{1,2})$/i;
		
    	if(!reg_texto.test(pe_nombre)) {
			var mensaje = "El nombre del perfil debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nombre").focus();
		} else if(!reg_digit.test(pe_nivel_acceso)) {
			var mensaje = "El nivel de acceso del perfil debe contener al menos un caracter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nivel_acceso").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "perfil/insertar_perfil.php",
					data: "pe_nombre="+pe_nombre+"&pe_nivel_acceso="+pe_nivel_acceso,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarPerfiles();
						salirPerfil(false);
				  }
			});			
		}	
	}

	function actualizarPerfil()
	{
		// Validación de la entrada de datos
		var id_perfil = document.getElementById("id_perfil").value;
		var pe_nombre = document.getElementById("pe_nombre").value;
		var pe_nivel_acceso = document.getElementById("pe_nivel_acceso").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pe_nombre=eliminaEspacios(pe_nombre);
		pe_nivel_acceso=eliminaEspacios(pe_nivel_acceso);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_digit = /^([0-9]{1,2})$/i;
		
		if (id_perfil==0) {
			var mensaje = "No se ha pasado el parámetro de id_perfil";
			$("#mensaje").html(mensaje);
			salirPerfil();
		} else if(!reg_texto.test(pe_nombre)) {
			var mensaje = "El nombre del perfil debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nombre").focus();
		} else if(!reg_digit.test(pe_nivel_acceso)) {
			var mensaje = "El nivel de acceso del perfil debe contener al menos un caracter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nivel_acceso").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "perfil/actualizar_perfil.php",
					data: "id_perfil="+id_perfil+"&pe_nombre="+pe_nombre+"&pe_nivel_acceso="+pe_nivel_acceso,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarPerfiles();
						salirPerfil(false);
				  }
			});			
		}	
	}

	function editarPerfil(id_perfil)
	{
		limpiarPerfil();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR PERFIL");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarPerfil()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "perfil/obtener_perfil.php",
				data: "id_perfil="+id_perfil,
				success: function(resultado){
					var JSONPerfil = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el perfil elegido
					document.getElementById("id_perfil").value=JSONPerfil.id_perfil;
					document.getElementById("pe_nombre").value=JSONPerfil.pe_nombre;
					document.getElementById("pe_nivel_acceso").value=JSONPerfil.pe_nivel_acceso;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("pe_nombre").focus();
			  }
		});			
	}

	function eliminarPerfil(id_perfil)
	{
		// Validación de la entrada de datos
		
		if (id_perfil==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_perfil...");
			salirPerfil(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este perfil?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "perfil/eliminar_perfil.php",
						data: "id_perfil="+id_perfil,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarPerfiles();
							salirPerfil(false);
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
            <td> <div id="nuevo_perfil" class="boton"> <a href="#"> Nuevo Perfil </a> </div> </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Perfil</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="pe_nombre" type="text" class="cajaGrande" name="pe_nombre" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Nivel de Acceso:</td>
                  <td width="*">
                     <input id="pe_nivel_acceso" type="text" class="cajaGrande" name="pe_nivel_acceso" maxlength="40" />
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
                              <div id="limpiarPerfil" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirPerfil()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_perfil" name="id_perfil" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_perfiles">
      <!-- Aqui va la paginacion de los perfiles encontrados -->
      <div class="header2"> LISTA DE PERFILES EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="36%" align="left">Nombre</td>
                <td width="36%" align="left">Nivel de Acceso</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_perfiles" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
