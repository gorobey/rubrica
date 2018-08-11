<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
			$("#barra_secundaria").css("display","none");
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			listarRubricasEvaluacion();
			salirRubrica(true);
			$("#barra_secundaria").css("display","none");
		});
		$("#nueva_rubrica_evaluacion").click(function(e){
			e.preventDefault();
			nuevaRubrica();
		});
		$("#limpiarCriterioEvaluacion").click(function(e){
			e.preventDefault();
			limpiarRubrica();
		});
	});

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion_principales.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
				}
			}
		);
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#lista_rubricas_evaluacion").html("Debe elegir un aporte de evaluaci&oacute;n...");
					salirRubrica(true);
				}
			}
		);
	}

	function listarRubricasEvaluacion()
	{
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		$.get("rubricas_proyecto/listar_rubricas.php", { id_aporte_evaluacion: id_aporte_evaluacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_rubricas_evaluacion").html(resultado);
				}
			}
		);
	}
	
	function limpiarRubrica()
	{
		document.getElementById("ru_nombre").value="";
		document.getElementById("ru_abreviatura").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
	}

	function salirRubrica(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nueva_rubrica_evaluacion").focus();
	}

	function nuevaRubrica()
	{
		limpiarRubrica();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarRubrica()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display","block");
		document.getElementById("ru_nombre").focus();
	}

	function insertarRubrica()
	{
		// Validación de la entrada de datos
		var ru_nombre = document.getElementById("ru_nombre").value;
		var ru_abreviatura = document.getElementById("ru_abreviatura").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ru_nombre=eliminaEspacios(ru_nombre);
		ru_abreviatura=eliminaEspacios(ru_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,24})$/i;
		var reg_abreviatura = /^([a-zA-Z0-9.]{1,6})$/i;
		
		if(id_periodo_evaluacion==0) {
			var mensaje = "Debe elegir un periodo de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPeriodosEvaluacion").focus();
		} else if(id_aporte_evaluacion==0) {
			var mensaje = "Debe elegir un aporte de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboAportesEvaluacion").focus();
    	} else if(!reg_texto.test(ru_nombre)) {
			var mensaje = "El nombre de la r&uacute;brica debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ru_nombre").focus();
		} else if(!reg_abreviatura.test(ru_abreviatura)) {
			var mensaje = "La abreviatura de la r&uacute;brica debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ru_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_proyecto/insertar_rubrica.php",
					data: "id_aporte_evaluacion="+id_aporte_evaluacion+"&ru_nombre="+ru_nombre+"&ru_abreviatura="+ru_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarRubricasEvaluacion();
						salirRubrica(false);
				  }
			});			
		}	
	}

	function editarRubricaEvaluacion(id_rubrica)
	{
		salirRubrica(false);
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR RUBRICA DE EVALUACION");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarRubrica()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "rubricas_evaluacion/obtener_rubrica.php",
				data: "id_rubrica="+id_rubrica,
				success: function(resultado){
					var JSONRubrica = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar la rubrica elegida
					document.getElementById("id_rubrica_evaluacion").value=JSONRubrica.id_rubrica_evaluacion;
					document.getElementById("ru_nombre").value=JSONRubrica.ru_nombre;
					document.getElementById("ru_abreviatura").value=JSONRubrica.ru_abreviatura;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("ru_nombre").focus();
			  }
		});			
	}

	function actualizarRubrica()
	{
		// Validación de la entrada de datos
		var id_rubrica_evaluacion = document.getElementById("id_rubrica_evaluacion").value;
		var ru_nombre = document.getElementById("ru_nombre").value;
		var ru_abreviatura = document.getElementById("ru_abreviatura").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ru_nombre=eliminaEspacios(ru_nombre);
		ru_abreviatura=eliminaEspacios(ru_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,24})$/i;
		var reg_abreviatura = /^([a-zA-Z0-9.]{1,6})$/i;
		
		if(id_rubrica_evaluacion=="") {
			var mensaje = "No se ha pasado el par&aacute;metro id_rubrica_evaluacion...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPeriodosEvaluacion").focus();
		} else if(id_periodo_evaluacion==0) {
			var mensaje = "Debe elegir un periodo de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPeriodosEvaluacion").focus();
		} else if(id_aporte_evaluacion==0) {
			var mensaje = "Debe elegir un aporte de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboAportesEvaluacion").focus();
    	} else if(!reg_texto.test(ru_nombre)) {
			var mensaje = "El nombre de la r&uacute;brica debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ru_nombre").focus();
		} else if(!reg_abreviatura.test(ru_abreviatura)) {
			var mensaje = "La abreviatura de la r&uacute;brica debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ru_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_evaluacion/actualizar_rubrica.php",
					data: "id_rubrica_evaluacion="+id_rubrica_evaluacion+"&id_aporte_evaluacion="+id_aporte_evaluacion+"&ru_nombre="+ru_nombre+"&ru_abreviatura="+ru_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarRubricasEvaluacion();
						salirRubrica(false);
				  }
			});			
		}	
	}

	function criteriosRubricaEvaluacion(id_rubrica_evaluacion)
	{
		$("#barra_secundaria").css("display","block");
		listarCriteriosEvaluacion(id_rubrica_evaluacion);
		document.getElementById("mensaje_detalle").innerHTML="";
		document.getElementById("id_rubrica_evaluacion").value = id_rubrica_evaluacion;
	}

	function listarCriteriosEvaluacion(id_rubrica_evaluacion)
	{
		$.get("rubricas_evaluacion/listar_criterios.php", { id_rubrica_evaluacion: id_rubrica_evaluacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_criterios_evaluacion").html(resultado);
				}
			}
		);
	}

	function limpiarCriterio()
	{
		document.getElementById("cr_descripcion").value="";
		document.getElementById("cr_ponderacion").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
	}

	function salirCriterio(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje_detalle").css("display", css_display);
		$("#formulario_detalle").css("display", "none");
		document.getElementById("nuevo_criterio_evaluacion").focus();
	}

	function nuevoCriterio()
	{
		limpiarCriterio();
		$("#tituloDetalle").html("NUEVO CRITERIO DE EVALUACION");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarCriterio()\">Insertar</a></div>";
		$("#boton_detalle").html(html);
		$("#mensaje_detalle").html("");
		$("#formulario_detalle").css("display","block");
		document.getElementById("cr_descripcion").focus();
	}

	function insertarCriterio()
	{
		// Validación de la entrada de datos
		var cr_descripcion = document.getElementById("cr_descripcion").value;
		var cr_ponderacion = document.getElementById("cr_ponderacion").value;
		var id_rubrica_evaluacion = document.getElementById("id_rubrica_evaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		cr_descripcion=eliminaEspacios(cr_descripcion);
		cr_ponderacion=eliminaEspacios(cr_ponderacion);

		var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{4,24})$/i;
		var reg_ponderacion = /^[0-9]+(\.[0-9]{1,2})$/i;

		// ^                   # Start of string.
		// [0-9]+              # Must have one or more numbers.
		// (                   # Begin optional group.
		//	\.              # The decimal point, . must be escaped, 
		//					# or it is treated as "any character".
		//	[0-9]{1,2}      # One or two numbers.
		// )?                  # End group, signify it's optional with ?
		// $                   # End of string.
		
		if(id_rubrica_evaluacion=="") {
			var mensaje = "No se ha pasado el par&aacute;metro id_rubrica_evaluacion...";
			$("#mensaje_detalle").html(mensaje);
    	} else if(!reg_texto.test(cr_descripcion)) {
			var mensaje = "La descripci&oacute;n del criterio debe contener al menos cuatro caracteres alfab&eacute;ticos...";
			$("#mensaje_detalle").html(mensaje);
			document.getElementById("cr_descripcion").focus();
		} else if(!reg_ponderacion.test(cr_ponderacion)) {
			var mensaje = "El rango de la ponderaci&oacute;n debe estar enter 0 y 1 (con dos decimales de precisi&oacute;n)...";
			$("#mensaje_detalle").html(mensaje);
			document.getElementById("cr_ponderacion").focus();
		} else {
			$("#img-loader-detalle").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_evaluacion/insertar_criterio.php",
					data: "id_rubrica_evaluacion="+id_rubrica_evaluacion+"&cr_descripcion="+cr_descripcion+"&cr_ponderacion="+cr_ponderacion+"&cr_tipo='D'",
					success: function(resultado){
						$("#img-loader-detalle").html("");
						$("#mensaje_detalle").html(resultado);
						listarCriteriosEvaluacion(id_rubrica_evaluacion);
						salirCriterio(false);
				  }
			});			
		}	
	}

	function editarCriterioEvaluacion(id_criterio)
	{
		salirCriterio(false);
		$("#formulario_detalle").css("display", "none");
		$("#tituloDetalle").html("EDITAR CRITERIO DE EVALUACION");
		$("#mensaje_detalle").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarCriterio()\">Actualizar</a></div>";
		$("#boton_detalle").html(html);
		$.ajax({
				type: "POST",
				url: "rubricas_evaluacion/obtener_criterio.php",
				data: "id_criterio="+id_criterio,
				success: function(resultado){
					var JSONCriterio = eval('(' + resultado + ')');
					$("#mensaje_detalle").html("");
					//Aqui se va a pintar el criterio elegido
					document.getElementById("id_criterio_evaluacion").value=JSONCriterio.id_criterio_evaluacion;
					document.getElementById("id_rubrica_evaluacion").value=JSONCriterio.id_rubrica_evaluacion;
					document.getElementById("cr_descripcion").value=JSONCriterio.cr_descripcion;
					document.getElementById("cr_ponderacion").value=JSONCriterio.cr_ponderacion;
					$("#formulario_detalle").css("display", "block");
					document.getElementById("cr_descripcion").focus();
			  }
		});			
	}

	function actualizarCriterio()
	{
		// Validación de la entrada de datos
		var id_criterio_evaluacion = document.getElementById("id_criterio_evaluacion").value;
		var id_rubrica_evaluacion = document.getElementById("id_rubrica_evaluacion").value;
		var cr_descripcion = document.getElementById("cr_descripcion").value;
		var cr_ponderacion = document.getElementById("cr_ponderacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		cr_descripcion=eliminaEspacios(cr_descripcion);
		cr_ponderacion=eliminaEspacios(cr_ponderacion);

		var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{4,24})$/i;
		var reg_ponderacion = /^[0-9]+(\.[0-9]{1,2})$/i;

		// ^                   # Start of string.
		// [0-9]+              # Must have one or more numbers.
		// (                   # Begin optional group.
		//	\.              # The decimal point, . must be escaped, 
		//					# or it is treated as "any character".
		//	[0-9]{1,2}      # One or two numbers.
		// )?                  # End group, signify it's optional with ?
		// $                   # End of string.
		
		if(id_criterio_evaluacion=="") {
			var mensaje = "No se ha pasado el par&aacute;metro id_criterio_evaluacion...";
			$("#mensaje_detalle").html(mensaje);
		} else if(id_rubrica_evaluacion=="") {
			var mensaje = "No se ha pasado el par&aacute;metro id_rubrica_evaluacion...";
			$("#mensaje_detalle").html(mensaje);
    	} else if(!reg_texto.test(cr_descripcion)) {
			var mensaje = "La descripci&oacute;n del criterio debe contener al menos cuatro caracteres alfab&eacute;ticos...";
			$("#mensaje_detalle").html(mensaje);
			document.getElementById("cr_descripcion").focus();
		} else if(!reg_ponderacion.test(cr_ponderacion)) {
			var mensaje = "El rango de la ponderaci&oacute;n debe estar enter 0 y 1 (con dos decimales de precisi&oacute;n)...";
			$("#mensaje_detalle").html(mensaje);
			document.getElementById("cr_ponderacion").focus();
		} else {
			$("#img-loader-detalle").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_evaluacion/actualizar_criterio.php",
					data: "id_criterio_evaluacion="+id_criterio_evaluacion+"&id_rubrica_evaluacion="+id_rubrica_evaluacion+"&cr_descripcion="+cr_descripcion+"&cr_ponderacion="+cr_ponderacion,
					success: function(resultado){
						$("#img-loader-detalle").html("");
						$("#mensaje_detalle").html(resultado);
						listarCriteriosEvaluacion(id_rubrica_evaluacion);
						salirCriterio(false);
				  }
			});			
		}	
	}

	function eliminarCriterioEvaluacion(id_criterio)
	{
		// Validación de la entrada de datos
		
		var id_rubrica_evaluacion = document.getElementById("id_rubrica_evaluacion").value;
		if (id_criterio==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_criterio...");
			salirCriterio(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este criterio?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "rubricas_evaluacion/eliminar_criterio.php",
						data: "id_criterio="+id_criterio,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarCriteriosEvaluacion(id_rubrica_evaluacion);
							salirCriterio(false);
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
			<td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> Aporte:&nbsp; </td>
            <td width="5%"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td> <div id="nueva_rubrica_evaluacion" class="boton" style="display:block"> <a href="#"> Nueva R&uacute;brica de Evaluaci&oacute;n </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nueva R&uacute;brica de Evaluaci&oacute;n</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="ru_nombre" type="text" class="cajaGrande" name="ru_nombre" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Abreviatura:</td>
                  <td width="*">
                     <input id="ru_abreviatura" type="text" class="cajaPequenia" name="ru_abreviatura" maxlength="6" />
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
                              <div id="limpiarRubricaEvaluacion" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirRubrica()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_rubrica_evaluacion" name="id_rubrica_evaluacion" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_rubrica_evaluacion">
      <!-- Aqui va la paginacion de las rubricas de evaluacion encontradas -->
      <div class="header2"> LISTA DE RUBRICAS DE EVALUACION EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="36%" align="left">Nombre</td>
                <td width="36%" align="left">Abreviatura</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_rubricas_evaluacion" style="text-align:center"> Debe elegir un per&iacute;odo de evaluaci&oacute;n... </div>
   </div>
   <div id="barra_secundaria">
      <table id="tabla_detalles" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td> <div id="nuevo_criterio_evaluacion" class="boton"> <a href="#"> Nuevo Criterio de Evaluaci&oacute;n </a> </div> </td>
         </tr>
      </table>
      <div id="formulario_detalle">
        <div id="tituloDetalle" class="header">Nuevo Criterio de Evaluaci&oacute;n</div>
        <div id="frmNuevo" align="left">
   	      <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Descripci&oacute;n:</td>
                  <td width="*">
                     <input id="cr_descripcion" type="text" class="cajaGrande" name="cr_descripcion" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Ponderaci&oacute;n:</td>
                  <td width="*">
                     <input id="cr_ponderacion" type="text" class="cajaPequenia" name="cr_ponderacion" maxlength="5" />
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
							  <div id="boton_detalle">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarCriterioEvaluacion" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirCriterio()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader-detalle" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_criterio_evaluacion" name="id_criterio_evaluacion" />
         </form>
       </div>   
     </div> <!-- fin div = "formulario_detalle" -->
     <div id="mensaje_detalle" class="error"></div>
     <div id="pag_criterio_evaluacion">
       <!-- Aqui va la paginacion de los criterios de evaluacion encontrados -->
       <div class="header2"> LISTA DE CRITERIOS DE EVALUACION EXISTENTES </div>
       <div class="cabeceraTabla">
         <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="24%" align="left">R&uacute;brica</td>
                <td width="24%" align="left">Criterio</td>
                <td width="24%" align="left">Ponderaci&oacute;n</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
         </table>
	   </div>
       <div id="lista_criterios_evaluacion" style="text-align:center"></div>
     </div>
   </div> <!-- fin div = "barra_secundaria" -->
</div>
</body>
</html>
