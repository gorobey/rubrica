<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarTiposDeAsignatura();	    
		$("#nuevo_tipo_asignatura").click(function(e){
			e.preventDefault();
			nuevoTipoAsignatura();
		});		
	});

	function listarTiposDeAsignatura()
	{
		$.get("tipos_asignatura/listar_tipos_de_asignatura.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_tipos_de_asignatura").html(resultado);
				}
			}
		);
	}

	function limpiarTipoAsignatura()
	{
		document.getElementById("ta_descripcion").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("ta_descripcion").focus();
	}

	function salirTipoAsignatura(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_tipo_asignatura").focus();
	}

	function nuevoTipoAsignatura()
	{
		limpiarTipoAsignatura();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarTipoAsignatura()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("ta_descripcion").focus();
	}

	function insertarTipoAsignatura()
	{
		// Validación de la entrada de datos
		var ta_descripcion = document.getElementById("ta_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ta_descripcion=eliminaEspacios(ta_descripcion);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
    	if(!reg_texto.test(ta_descripcion)) {
			var mensaje = "La descripci&oacute;n del tipo de asignatura debe contener al menos 4 caracteres alfab&eacute;ticos y m&aacute;ximo 64...";
			$("#mensaje").html(mensaje);
			document.getElementById("ta_descripcion").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "tipos_asignatura/insertar_tipo_asignatura.php",
					data: "ta_descripcion="+ta_descripcion,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarTiposDeAsignatura();
						salirTipoAsignatura(false);
				  }
			});			
		}	
	}

	function editarTipoAsignatura(id_tipo_asignatura)
	{
		limpiarTipoAsignatura();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR TIPO DE ASIGNATURA");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarTipoAsignatura()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "tipos_asignatura/obtener_tipo_asignatura.php",
				data: "id_tipo_asignatura="+id_tipo_asignatura,
				success: function(resultado){
					var JSONTipoAsignatura = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el tipo de asignatura escogido
					document.getElementById("id_tipo_asignatura").value=JSONTipoAsignatura.id_tipo_asignatura;
					document.getElementById("ta_descripcion").value=JSONTipoAsignatura.ta_descripcion;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("ta_descripcion").focus();
			  }
		});			
	}

	function actualizarTipoAsignatura()
	{
		// Validación de la entrada de datos
		var id_tipo_asignatura = document.getElementById("id_tipo_asignatura").value;
		var ta_descripcion = document.getElementById("ta_descripcion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ta_descripcion=eliminaEspacios(ta_descripcion);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
    	if(!reg_texto.test(ta_descripcion)) {
			var mensaje = "La descripci&oacute;n del tipo de asignatura debe contener al menos 4 caracteres alfab&eacute;ticos y m&aacute;ximo 64...";
			$("#mensaje").html(mensaje);
			document.getElementById("ta_descripcion").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "tipos_asignatura/actualizar_tipo_asignatura.php",
					data: "id_tipo_asignatura="+id_tipo_asignatura+"&ta_descripcion="+ta_descripcion,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarTiposDeAsignatura();
						salirTipoAsignatura(false);
				  }
			});			
		}	
	}

	function eliminarTipoAsignatura(id_tipo_asignatura,descripcion)
	{
		// Validación de la entrada de datos
		
		if (id_tipo_asignatura==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_tipo_asignatura...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Tipo de Asignatura [" + descripcion + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "tipos_asignatura/eliminar_tipo_asignatura.php",
						data: "id_tipo_asignatura="+id_tipo_asignatura,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarTiposDeAsignatura(true);
							salirTipoAsignatura();
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
            <td> <div id="nuevo_tipo_asignatura" class="boton"> <a href="#"> Nuevo Tipo De Asignatura </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Tipo De Asignatura</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Descripci&oacute;n:</td>
                  <td width="*">
                     <input id="ta_descripcion" type="text" class="cajaGrande" name="ta_descripcion" maxlength="64" />
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
                              <div id="limpiar_tipo_asignatura" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirTipoAsignatura(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_tipo_asignatura" name="id_tipo_asignatura" />
         </form>
      </div>   
    </div>
    <div id="mensaje" class="error" align="center"></div>
    <div id="pag_tipos_de_asignatura">
      <!-- Aqui va la paginacion de los tipos de asignatura encontrados -->
      <div class="header2"> LISTA DE TIPOS DE ASIGNATURA EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="72%" align="left">Descripci&oacute;n</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_tipos_de_asignatura" style="text-align:center"> </div>
    </div>
</div>
</body>
</html>
