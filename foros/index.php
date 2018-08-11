<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script src="js/funciones.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarForos();
		desplegar_barra_principal();
		$("#mostrar_enlace").hide();
		$("#nuevo_foro").click(function(e){
			e.preventDefault();
			nuevoForo();
		});		
		$("#limpiarForo").click(function(e){
			e.preventDefault();
			limpiarForo();
		});
		$("#volver_indice").click(function(e){
			e.preventDefault();
			$("#pag_temas").slideUp();
			$("#pag_respuestas").slideUp();
			$("#pag_foros").slideDown();
			$("#mostrar_enlace").hide();
			$("#formulario_nueva_respuesta").slideUp();
		});
	});
	
	function listarForos()
	{
		$.get("foros/listar_foros.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#formulario_nuevo").slideUp();
					$("#formulario_nuevo_tema").slideUp();
					$("#lista_foros").html(resultado);
				}
			}
		);
	}

	function desplegar_barra_principal()
	{
		$.post("foros/obtener_nivel_acceso.php",
			{ },
			function(resultado) {
				// recupero el nivel de acceso en formato json
				var JSONUsuario = eval('(' + resultado + ')');
				var nivel_acceso = parseInt(JSONUsuario.pe_nivel_acceso);
				// desplego la barra principal de acuerdo al nivel de acceso
				if(nivel_acceso > 1) { 
					$("#barra_principal").show();
					$("#titulo_pagina").html("ADMINISTRAR FOROS");
				} else {
					$("#barra_principal").hide();
					$("#titulo_pagina").html("NAVEGACION FOROS");
				}
			}
		);
	}

	function limpiarForo()
	{
		document.getElementById("fo_titulo").value="";
		document.getElementById("fo_descripcion").value="";
		document.getElementById("mensaje").innerHTML="";
		$("#img-loader").hide();
		document.getElementById("fo_titulo").focus();
	}

	function salirForo(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").slideUp();
		document.getElementById("nuevo_foro").focus();
	}

	function nuevoForo()
	{
		limpiarForo();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarForo()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo_tema").slideUp();
		$("#formulario_nuevo").slideDown();
		document.getElementById("fo_titulo").focus();
	}

	function insertarForo()
	{
		// Validación de la entrada de datos
		var fo_titulo = document.getElementById("fo_titulo").value;
		var fo_descripcion = document.getElementById("fo_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		fo_titulo = eliminaEspacios(fo_titulo);
		fo_descripcion = eliminaEspacios(fo_descripcion);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ.]{4,64})$/i;
		
    	if(!reg_texto.test(fo_titulo)) {
			var mensaje = "El t&iacute;tulo del foro debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("fo_titulo").focus();
		} else if(!reg_texto.test(fo_descripcion)) {
			var mensaje = "La descripci&oacute;n del foro debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("fo_descripcion").focus();
		} else {
			$("#img-loader").show();
			$.ajax({
					type: "POST",
					url: "foros/insertar_foro.php",
					data: "fo_titulo="+fo_titulo+"&fo_descripcion="+fo_descripcion,
					success: function(resultado){
						$("#img-loader").hide();
						$("#mensaje").html(resultado);
						listarForos();
						salirForo(false);
				  }
			});
		}
	}

	function actualizarForo()
	{
		// Validación de la entrada de datos
		var id_foro = document.getElementById("id_foro").value;
		var fo_titulo = document.getElementById("fo_titulo").value;
		var fo_descripcion = document.getElementById("fo_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		fo_titulo = eliminaEspacios(fo_titulo);
		fo_descripcion = eliminaEspacios(fo_descripcion);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ.]{4,64})$/i;
		
		if (id_foro==0) {
			var mensaje = "No se ha pasado el parámetro de id_foro";
			$("#mensaje").html(mensaje);
			salirForo();
    	} else if(!reg_texto.test(fo_titulo)) {
			var mensaje = "El t&iacute;tulo del foro debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("fo_titulo").focus();
		} else if(!reg_texto.test(fo_descripcion)) {
			var mensaje = "La descripci&oacute;n del foro debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("fo_descripcion").focus();
		} else {
			$("#img-loader").show();
			$.ajax({
					type: "POST",
					url: "foros/actualizar_foro.php",
					data: "id_foro="+id_foro+"&fo_titulo="+fo_titulo+"&fo_descripcion="+fo_descripcion,
					success: function(resultado){
						$("#img-loader").hide();
						$("#mensaje").html(resultado);
						listarForos();
						salirForo(false);
				  }
			});
		}
	}

	function editarForo(id_foro)
	{
		limpiarForo();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR FORO");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...'>");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarForo()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "foros/obtener_foro.php",
				data: "id_foro="+id_foro,
				success: function(resultado){
					var JSONForo = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el foro elegido
					document.getElementById("id_foro").value=JSONForo.id_foro;
					document.getElementById("fo_titulo").value=JSONForo.fo_titulo;
					document.getElementById("fo_descripcion").value=JSONForo.fo_descripcion;
					$("#formulario_nuevo_tema").slideUp();
					$("#formulario_nuevo").slideDown();
					document.getElementById("fo_titulo").focus();
			  }
		});
	}

	function eliminarForo(id_foro)
	{
		// Validación de la entrada de datos
		
		salirForo(false);
		
		if (id_foro==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_foro...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este foro?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "foros/eliminar_foro.php",
						data: "id_foro="+id_foro,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarForos();
					  }
				});
			}
		}
	}

	function listarTemas()
	{
		var id_foro = document.getElementById("id_foro").value;
		document.getElementById("mensaje").innerHTML="";
		$.post("foros/listar_temas.php", 
			{ id_foro: id_foro },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
				$("#formulario_nuevo").slideUp();
				$("#formulario_nuevo_tema").slideUp();
				$("#pag_foros").slideUp();
				$("#pag_respuestas").slideUp();
				$("#lista_temas").html(resultado);
				$("#mostrar_enlace").show();
				$("#pag_temas").slideDown();
				}
			}
		);
	}

	function limpiarTema()
	{
		document.getElementById("te_titulo").value="";
		document.getElementById("te_descripcion").value="";
		document.getElementById("mensaje").innerHTML="";
		$("#img-loader-tema").hide();
	}

	function salirTema(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo_tema").slideUp();
		document.getElementById("nuevo_foro").focus();
	}

	function nuevoTema(id_foro)
	{
		$("#formulario_nuevo").slideUp();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarTema("+id_foro+")\">Insertar</a></div>";
		$("#boton_accion_tema").html(html);
		limpiarTema();
		$("#formulario_nuevo_tema").slideDown();
		document.getElementById("te_titulo").focus();
	}

	function insertarTema(id_foro)
	{
		// Validación de la entrada de datos
		document.getElementById("id_foro").value = id_foro;
		var te_titulo = document.getElementById("te_titulo").value;
		var te_descripcion = document.getElementById("te_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		te_titulo = eliminaEspacios(te_titulo);
		te_descripcion = eliminaEspacios(te_descripcion);
		
    	if(te_titulo.length==0) {
			var mensaje = "El t&iacute;tulo del tema es obligatorio...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_titulo").focus();
		} else if(te_descripcion.length==0) {
			var mensaje = "La descripci&oacute;n del tema es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_descripcion").focus();
		} else {
			$("#img-loader-tema").show();
			$.ajax({
					type: "POST",
					url: "foros/insertar_tema.php",
					data: "id_foro="+id_foro+"&te_titulo="+te_titulo+"&te_descripcion="+te_descripcion,
					success: function(resultado){
						$("#img-loader-tema").hide();
						$("#mensaje").html(resultado);
						listarForos();
						salirTema(false);
				  }
			});
		}
	}
	
	function verTemas(id_foro)
	{
		// Aca listaremos los temas asociados a un determinado foro
		document.getElementById("id_foro").value = id_foro;
		$.post("foros/obtener_titulo_foro.php",
			{ id_foro: id_foro },
			function(resultado) {
				var JSONTituloForo = eval('(' + resultado + ')');
				$("#titulo_foro").html('LISTA DE TEMAS: FORO ['+JSONTituloForo.fo_titulo+']');
			}
		);
		listarTemas();
	}

	function editarTema(id_tema)
	{
		limpiarTema();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR TEMA");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...'>");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarTema("+id_tema+")\">Actualizar</a></div>";
		$("#boton_accion_tema").html(html);
		$.ajax({
				type: "POST",
				url: "foros/obtener_tema.php",
				data: "id_tema="+id_tema,
				success: function(resultado){
					var JSONTema = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el tema elegido
					document.getElementById("id_tema").value=JSONTema.id_tema;
					document.getElementById("te_titulo").value=JSONTema.te_titulo;
					document.getElementById("te_descripcion").value=JSONTema.te_descripcion;
					$("#formulario_nuevo_tema").slideDown();
					document.getElementById("te_titulo").focus();
			  }
		});
	}

	function actualizarTema(id_tema)
	{
		// Validación de la entrada de datos
		document.getElementById("id_tema").value = id_tema;
		var te_titulo = document.getElementById("te_titulo").value;
		var te_descripcion = document.getElementById("te_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		te_titulo = eliminaEspacios(te_titulo);
		te_descripcion = eliminaEspacios(te_descripcion);
		
    	if(te_titulo.length==0) {
			var mensaje = "El t&iacute;tulo del tema es obligatorio...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_titulo").focus();
		} else if(te_descripcion.length==0) {
			var mensaje = "La descripci&oacute;n del tema es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_descripcion").focus();
		} else {
			$("#img-loader-tema").show();
			$.ajax({
					type: "POST",
					url: "foros/actualizar_tema.php",
					data: "id_tema="+id_tema+"&te_titulo="+te_titulo+"&te_descripcion="+te_descripcion,
					success: function(resultado){
						$("#img-loader-tema").hide();
						$("#mensaje").html(resultado);
						listarTemas();
						salirTema(false);
				  }
			});
		}
	}

	function limpiarRespuesta()
	{
		document.getElementById("te_asunto").value="";
		document.getElementById("re_texto").value="";
		document.getElementById("mensaje").innerHTML="";
		$("#img-loader-respuesta").hide();
	}

	function salirRespuesta(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nueva_respuesta").slideUp();
		document.getElementById("nuevo_foro").focus();
	}

	function nuevaRespuesta(id_tema)
	{
		$("#formulario_nuevo").slideUp();
		$("#formulario_nuevo_tema").slideUp();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarRespuesta("+id_tema+")\">Insertar</a></div>";
		$("#boton_accion_respuesta").html(html);
		limpiarRespuesta();
		// Aqui obtengo el titulo del tema asociado
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...'>");
		$.post("foros/obtener_tema.php",
			{ id_tema: id_tema },
			function(resultado) {
				$("#mensaje").html("");
				var JSONTema = eval('(' + resultado + ')');
				document.getElementById("te_asunto").value=JSONTema.te_titulo;
				$("#formulario_nueva_respuesta").slideDown();
				document.getElementById("re_texto").focus();
			}
		);
	}

	function insertarRespuesta(id_tema)
	{
		// Validación de la entrada de datos
		document.getElementById("id_tema").value = id_tema;
		var re_texto = document.getElementById("re_texto").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		re_texto = eliminaEspacios(re_texto);
		
    	if(re_texto.length==0) {
			var mensaje = "El texto de la respuesta es obligatorio...";
			$("#mensaje").html(mensaje);
			document.getElementById("re_texto").focus();
		} else {
			$("#img-loader-respuesta").show();
			$.ajax({
					type: "POST",
					url: "foros/insertar_respuesta.php",
					data: "id_tema="+id_tema+"&re_texto="+re_texto,
					success: function(resultado){
						$("#img-loader-respuesta").hide();
						$("#mensaje").html(resultado);
						listarTemas();
						salirRespuesta(false);
				  }
			});
		}
	}

	function verRespuestas(id_tema)
	{
		// Aca listaremos las respuestas asociadas a un determinado tema
		document.getElementById("id_tema").value = id_tema;
		$.post("foros/obtener_titulo_tema.php",
			{ id_tema: id_tema },
			function(resultado) {
				var JSONTituloTema = eval('(' + resultado + ')');
				$("#titulo_tema").html('RESPUESTAS: TEMA ['+JSONTituloTema.te_titulo+']');
			}
		);
		listarRespuestas();
	}

	function listarRespuestas()
	{
		var id_tema = document.getElementById("id_tema").value;
		document.getElementById("mensaje").innerHTML="";
		$.post("foros/listar_respuestas.php", 
			{ id_tema: id_tema },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
				$("#formulario_nuevo").slideUp();
				$("#formulario_nuevo_tema").slideUp();
				$("#pag_temas").slideUp();
				$("#lista_respuestas").html(resultado);
				$("#pag_respuestas").slideDown();
				}
			}
		);
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	ADMINISTRAR FOROS
    </div>
    <div id="barra_principal" class="hide">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td> <div id="nuevo_foro" class="boton"> <a href="#"> Nuevo Foro </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Foro</div>
      <div id="frmNuevo" align="left">
         <form id="form_nuevo" action="" method="post">
            <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">T&iacute;tulo:</td>
                  <td width="*">
                     <input id="fo_titulo" type="text" class="cajaGrande" name="fo_titulo" maxlength="80" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Descripci&oacute;n:</td>
                  <td width="*">
                     <input id="fo_descripcion" type="text" class="cajaExtraGrande" name="fo_descripcion" maxlength="250" />
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
                              <div id="boton_accion">
                                 <!-- Aqui van los botones de acciones del formulario -->
                              </div>
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarForo" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirForo()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px">
                                 <img src='imagenes/ajax-loader.gif' alt='procesando...' />
                              </div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_foro" name="id_foro" />
         </form>
      </div>
    </div>
    <div id="formulario_nuevo_tema" class="form_nuevo hide">
      <div id="tituloForm" class="header">Nuevo Tema</div>
      <div id="frmNuevoTema" align="left">
         <form id="form_nuevo_tema" action="" method="post">
            <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Asunto:</td>
                  <td width="*">
                     <input id="te_titulo" type="text" class="cajaGrande" name="te_titulo" maxlength="80" />
                  </td>
               </tr>
               <tr valign="top">
                  <td width="15%" align="right">Mensaje:</td>
                  <td width="*">
                     <textarea id="te_descripcion" class="txtAreaGrande" name="te_descripcion"></textarea>
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
                              <div id="boton_accion_tema">
                                 <!-- Aqui van los botones de acciones del formulario -->
                              </div>
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarTema" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirTema()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader-tema" style="padding-left:2px">
                                 <img src='imagenes/ajax-loader.gif' alt='procesando...' />
                              </div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_tema" name="id_tema" />
         </form>
      </div>
    </div>
    <div id="formulario_nueva_respuesta" class="form_nuevo hide">
      <div id="tituloForm" class="header">Nueva Respuesta</div>
      <div id="frmNuevaRespuesta" align="left">
         <form id="form_nueva_respuesta" action="" method="post">
            <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Asunto:</td>
                  <td width="*">
                     <input id="te_asunto" type="text" class="cajaGrande" name="te_asunto" maxlength="80" disabled="disabled" />
                  </td>
               </tr>
               <tr valign="top">
                  <td width="15%" align="right">Mensaje:</td>
                  <td width="*">
                     <textarea id="re_texto" class="txtAreaGrande" name="re_texto"></textarea>
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
                              <div id="boton_accion_respuesta">
                                 <!-- Aqui van los botones de acciones del formulario -->
                              </div>
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarRespuesta" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirRespuesta(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader-respuesta" style="padding-left:2px">
                                 <img src='imagenes/ajax-loader.gif' alt='procesando...' />
                              </div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_respuesta" name="id_respuesta" />
         </form>
      </div>
    </div>
    <div id="mensaje" class="error"></div>
    <div id="pag_foros">
      <!-- Aqui va la paginacion de los foros existentes -->
      <div class="header2"> LISTA DE FOROS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="12%" align="left">Autor</td>
                <td width="24%" align="left">T&iacute;tulo</td>
                <td width="34%" align="left">Descripci&oacute;n</td>
                <td width="6%" align="left">Temas</td>
                <td width="24%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_foros" style="text-align:center"> </div>
    </div>

    <div id="mostrar_enlace" class="barra_principal">
      <div id="volver_indice" class="boton text-center">
         <a href="#">Volver al &iacute;ndice de foros</a>
      </div>
    </div>
    
    <div id="pag_temas" class="hide">
      <!-- Aqui va la paginacion de las categorias encontradas -->
      <div id="titulo_foro" class="header2"> LISTA DE TEMAS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="3%">Nro.</td>
                <td width="26%" align="center">Asunto</td>
                <td width="28%" align="center">Mensaje</td>
                <td width="12%" align="center">Autor</td>
                <td width="7%" align="center">Respuestas</td>
                <td width="24%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_temas" style="text-align:center"> </div>
    </div>

    <div id="pag_respuestas" class="hide">
      <!-- Aqui va la paginacion de las respuestas encontradas -->
      <div id="titulo_tema" class="header2"> LISTA DE RESPUESTAS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="3%">Nro.</td>
                <td width="61%" align="center">Mensaje</td>
                <td width="12%" align="center">Autor</td>
                <td width="12%" align="center">Perfil</td>
                <td width="12%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_respuestas" style="text-align:center"> </div>
    </div>
</div>
</body>
</html>
