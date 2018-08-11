<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarTiposDeEducacion();	    
		$("#nuevo_tipo_educacion").click(function(e){
			e.preventDefault();
			nuevoTipoEducacion();
		});		
	});

	function listarTiposDeEducacion()
	{
		$.get("tipo_educacion/listar_tipos_de_educacion.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_tipos_de_educacion").html(resultado);
				}
			}
		);
	}

	function limpiarTipoEducacion()
	{
		document.getElementById("te_nombre").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("te_nombre").focus();
	}

	function salirTipoEducacion(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_tipo_educacion").focus();
	}

	function nuevoTipoEducacion()
	{
		limpiarTipoEducacion();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarTipoEducacion()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("te_nombre").focus();
	}

	function insertarTipoEducacion()
	{
		// Validación de la entrada de datos
		var te_nombre = document.getElementById("te_nombre").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		te_nombre=eliminaEspacios(te_nombre);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,48})$/i;
		
    	if(!reg_texto.test(te_nombre)) {
			var mensaje = "El nombre del tipo de educaci&oacute;n debe contener al menos 4 caracteres alfab&eacute;ticos y m&aacute;ximo 32...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "tipo_educacion/insertar_tipo_educacion.php",
					data: "te_nombre="+te_nombre,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarTiposDeEducacion();
						salirTipoEducacion(false);
				  }
			});			
		}	
	}

	function editarTipoEducacion(id_tipo_educacion)
	{
		limpiarTipoEducacion();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR TIPO DE EDUCACION");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarTipoEducacion()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "tipo_educacion/obtener_tipo_educacion.php",
				data: "id_tipo_educacion="+id_tipo_educacion,
				success: function(resultado){
					var JSONPerfil = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el perfil elegido
					document.getElementById("id_tipo_educacion").value=JSONPerfil.id_tipo_educacion;
					document.getElementById("te_nombre").value=JSONPerfil.te_nombre;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("te_nombre").focus();
			  }
		});			
	}

	function actualizarTipoEducacion()
	{
		// Validación de la entrada de datos
		var id_tipo_educacion = document.getElementById("id_tipo_educacion").value;
		var te_nombre = document.getElementById("te_nombre").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		te_nombre=eliminaEspacios(te_nombre);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,48})$/i;
		
    	if(!reg_texto.test(te_nombre)) {
			var mensaje = "El nombre del tipo de educaci&oacute;n debe contener al menos 4 caracteres alfab&eacute;ticos y m&aacute;ximo 32...";
			$("#mensaje").html(mensaje);
			document.getElementById("te_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "tipo_educacion/actualizar_tipo_educacion.php",
					data: "id_tipo_educacion="+id_tipo_educacion+"&te_nombre="+te_nombre,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarTiposDeEducacion();
						salirTipoEducacion(false);
				  }
			});			
		}	
	}

	function eliminarTipoEducacion(id_tipo_educacion,nombre)
	{
		// Validación de la entrada de datos
		
		if (id_tipo_educacion==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_tipo_educacion...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Tipo de Educación [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "tipo_educacion/eliminar_tipo_educacion.php",
						data: "id_tipo_educacion="+id_tipo_educacion,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarTiposDeEducacion(true);
							salirTipoEducacion();
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
            <td> <div id="nuevo_tipo_educacion" class="boton" style="display:block"> <a href="#"> Nuevo Tipo De Educaci&oacute;n </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Tipo De Educaci&oacute;n</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="te_nombre" type="text" class="cajaGrande" name="te_nombre" maxlength="48" />
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
                              <div id="limpiar_tipo_educacion" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirTipoEducacion(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_tipo_educacion" name="id_tipo_educacion" />
         </form>
      </div>   
    </div>
    <div id="mensaje" class="error" align="center"></div>
    <div id="pag_tipos_de_educacion">
      <!-- Aqui va la paginacion de los tipo_educaciones encontrados -->
      <div class="header2"> LISTA DE TIPOS DE EDUCACION EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="72%" align="left">Nombre</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_tipos_de_educacion" style="text-align:center"> </div>
    </div>
</div>
</body>
</html>
