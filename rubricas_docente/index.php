<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		cargarAsignaturasDocente();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			cargarRubricasEvaluacion();
		});
		$("#cboRubricasEvaluacion").change(function(e){
			e.preventDefault();
			salirCriterio(true);
			salirRubrica(true);
			$("#lista_criterios_evaluacion").html("Debe elegir una asignatura...");
		});
		$("#limpiarRubrica").click(function(e){
			e.preventDefault();
			limpiarRubrica();
		});		
		$("#salirRubrica").click(function(e){
			e.preventDefault();
			salirRubrica(true);
		});		
		$("#limpiarCriterio").click(function(e){
			e.preventDefault();
			limpiarCriterio();
		});		
		$("#salirCriterio").click(function(e){
			e.preventDefault();
			salirCriterio(true);
		});		
	});

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
					$("#lista_criterios_evaluacion").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
				}
			}
		);
	}

	function cargarAsignaturasDocente()
	{
		$.get("rubricas_docente/cargar_asignaturas_docente.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_asignaturas").html(resultado);
				}
			}
		);
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		document.getElementById("cboRubricasEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				$("#cboAportesEvaluacion").append(resultado);
				$("#lista_criterios_evaluacion").html("Debe elegir un aporte de evaluaci&oacute;n...");
				salirCriterio(false);
			}
		);
	}

	function cargarRubricasEvaluacion()
	{
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		document.getElementById("cboRubricasEvaluacion").options.length=1;
		$.get("scripts/cargar_rubricas_evaluacion.php", { id_aporte_evaluacion: id_aporte_evaluacion },
			function(resultado)
			{
				$("#cboRubricasEvaluacion").append(resultado);
				$("#lista_criterios_evaluacion").html("Debe elegir una r&uacute;brica de evaluaci&oacute;n...");
				salirCriterio(false);
			}
		);
	}
	
	function listarCriteriosEvaluacion()
	{
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		var id_asignatura = document.getElementById("id_asignatura").value;
		var id_paralelo = document.getElementById("id_paralelo").value;
		$.post("rubricas_docente/listar_criterios.php", 
			{ 
				id_rubrica_evaluacion: id_rubrica_evaluacion, 
				id_asignatura: id_asignatura,
				id_paralelo: id_paralelo 
			},
			function(resultado)
			{
				$("#lista_criterios_evaluacion").html(resultado);
			}
		);
	}

	function personalizarRubrica(id_asignatura,id_paralelo,asignatura,curso,paralelo)
	{
		document.getElementById("id_asignatura").value=id_asignatura;
		document.getElementById("id_paralelo").value=id_paralelo;
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		if (id_rubrica_evaluacion == 0) {
			$("#mensaje").html("Debe elegir una r&uacute;brica de evaluaci&oacute;n...");
		} else {
			// Bueno aqui debo llamar a listar los criterios de evaluacion...
			personalizarCriteriosEvaluacion(id_asignatura,id_paralelo,asignatura,curso,paralelo);
		}
	}

	function personalizarCriteriosEvaluacion(id_asignatura,id_paralelo,asignatura,curso,paralelo)
	{
		// Primero buscar criterios de evaluacion personalizados
		//   Si existen criterios de evaluacion predefinidos entonces 
		//      insertar criterios de evaluacion predefinidos y 
		//      listar criterios de evaluacion personalizados
		//   sino
		//      mensaje no existen criterios de evaluacion predefinidos
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		$("#img-loader-principal").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.post("rubricas_docente/buscar_rubrica_personalizada.php", 
			{ 
				id_rubrica_evaluacion: id_rubrica_evaluacion, 
				id_asignatura: id_asignatura,
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				$("#img-loader-principal").html("");
				var JSONRubrica = eval('(' + resultado + ')');
				if (JSONRubrica.existe) {
					// No hay error... listar los criterios personalizados
					editar_rubrica_personalizada(id_rubrica_evaluacion,id_asignatura,id_paralelo,asignatura,curso,paralelo);
					$("#nuevoCriterioPersonalizado").css("display","block");
				} else {
					// No existen rubricas personalizadas...
					nueva_rubrica_personalizada(id_asignatura,id_paralelo,asignatura,curso,paralelo);
					$("#nuevoCriterioPersonalizado").css("display","none");
				}
				salirCriterio(true);
				listarCriteriosEvaluacion();
			}
		);
	}

	function limpiarRubrica()
	{
		document.getElementById("rp_tema").value="";
		document.getElementById("rp_fec_envio").value="";
		document.getElementById("rp_fec_evaluacion").value="";
		document.getElementById("img-loader-rubrica").innerHTML="";
		document.getElementById("rp_tema").focus();
	}

	function salirRubrica(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_rubrica").css("display", "none");
	}

	function nueva_rubrica_personalizada(id_asignatura,id_paralelo,asignatura,curso,paralelo)
	{
		limpiarRubrica();
		$("#tituloFormRubrica").html("NUEVA RUBRICA PERSONALIZADA ["+asignatura+" - "+curso+" "+paralelo+"]");
		html = "<div id=\"insertarRubrica\" class=\"link_form\"><a href=\"#\" onclick=\"insertarRubrica("+id_asignatura+","+id_paralelo+")\">Insertar</a></div>";
		$("#boton_rubrica").html(html);
		$("#mensaje").html("");
		$("#mensaje").css("display","block");
		$("#formulario_rubrica").css("display","block");
		document.getElementById("rp_tema").focus();
	}

	function insertarRubrica(id_asignatura,id_paralelo)
	{
		// Validación de la entrada de datos
		var rp_tema = document.getElementById("rp_tema").value;
		var rp_fec_envio = document.getElementById("rp_fec_envio").value;
		var rp_fec_evaluacion = document.getElementById("rp_fec_evaluacion").value;
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		rp_tema=eliminaEspacios(rp_tema);
		rp_fec_envio=eliminaEspacios(rp_fec_envio);
		rp_fec_evaluacion=eliminaEspacios(rp_fec_evaluacion);

		var reg_texto = /^([a-zA-Z0-9.: ñáéíóúÑÁÉÍÓÚ]{4,36})$/i;
		var reg_fecha = /^\d{4}-\d{2}-\d{2}$/i;
		
		$("#mensaje").css("display","block");

		if(id_rubrica_evaluacion==0) {
			var mensaje = "Debe elegir una r&uacute;brica de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboRubricasEvaluacion").focus();
    	} else if(!reg_texto.test(rp_tema)) {
			var mensaje = "El tema de la r&uacute;brica no coincide con la expresi&oacute;n regular...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_tema").focus();
		} else if(!reg_fecha.test(rp_fec_envio)) {
			var mensaje = "El formato para fechas es aaaa-mm-dd ...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_fec_envio").focus();
		} else if(!reg_fecha.test(rp_fec_evaluacion)) {
			var mensaje = "El formato para fechas es aaaa-mm-dd ...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_fec_evaluacion").focus();
		} else {
			$("#img-loader-rubrica").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_docente/insertar_rubrica.php",
					data: "id_rubrica_evaluacion="+id_rubrica_evaluacion+"&id_asignatura="+id_asignatura+"&id_paralelo="+id_paralelo+"&rp_tema="+rp_tema+"&rp_fec_envio="+rp_fec_envio+"&rp_fec_evaluacion="+rp_fec_evaluacion,
					success: function(resultado){
						$("#img-loader-rubrica").html("");
						$("#lista_criterios_evaluacion").html(resultado);
						salirRubrica(true);
				  }
			});			
		}	
	}

	function editar_rubrica_personalizada(id_rubrica_evaluacion,id_asignatura,id_paralelo,asignatura,curso,paralelo)
	{
		//salirRubrica(false);
		$("#nuevoCriterioPersonalizado").css("display", "block");
		$("#tituloFormRubrica").html("EDITAR RUBRICA PERSONALIZADA ["+asignatura+" - "+curso+" "+paralelo+"]");
		//$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarRubrica()\">Actualizar</a></div>";
		$("#boton_rubrica").html(html);
		$.ajax({
				type: "POST",
				url: "rubricas_docente/obtener_rubrica.php",
				data: "id_rubrica_evaluacion="+id_rubrica_evaluacion+"&id_asignatura="+id_asignatura+"&id_paralelo="+id_paralelo,
				success: function(resultado){
					var JSONRubrica = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar la rubrica elegido
					document.getElementById("id_rubrica_personalizada").value=JSONRubrica.id_rubrica_personalizada;
					document.getElementById("rp_tema").value=JSONRubrica.rp_tema;
					document.getElementById("rp_fec_envio").value=JSONRubrica.rp_fec_envio;
					document.getElementById("rp_fec_evaluacion").value=JSONRubrica.rp_fec_evaluacion;
					$("#formulario_rubrica").css("display", "block");
					document.getElementById("rp_tema").focus();
			  }
		});			
	}

	function actualizarRubrica()
	{
		// Validación de la entrada de datos
		var rp_tema = document.getElementById("rp_tema").value;
		var rp_fec_envio = document.getElementById("rp_fec_envio").value;
		var rp_fec_evaluacion = document.getElementById("rp_fec_evaluacion").value;
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		var id_rubrica_personalizada = document.getElementById("id_rubrica_personalizada").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		rp_tema=eliminaEspacios(rp_tema);
		rp_fec_envio=eliminaEspacios(rp_fec_envio);
		rp_fec_evaluacion=eliminaEspacios(rp_fec_evaluacion);

		var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{4,24})$/i;
		var reg_fecha = /^\d{4}-\d{2}-\d{2}$/i;

		if(id_rubrica_evaluacion==0) {
			var mensaje = "Debe elegir una r&uacute;brica de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboRubricasEvaluacion").focus();
    	} else if(!reg_texto.test(rp_tema)) {
			var mensaje = "El tema de la r&uacute;brica debe contener al menos cuatro caracteres alfab&eacute;ticos...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_tema").focus();
		} else if(!reg_fecha.test(rp_fec_envio)) {
			var mensaje = "El formato para fechas es aaaa-mm-dd ...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_fec_envio").focus();
		} else if(!reg_fecha.test(rp_fec_evaluacion)) {
			var mensaje = "El formato para fechas es aaaa-mm-dd ...";
			$("#mensaje").html(mensaje);
			document.getElementById("rp_fec_evaluacion").focus();
		} else {
			$("#img-loader-rubrica").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_docente/actualizar_rubrica.php",
					data: "id_rubrica_personalizada="+id_rubrica_personalizada+"&id_rubrica_evaluacion="+id_rubrica_evaluacion+"&rp_tema="+rp_tema+"&rp_fec_envio="+rp_fec_envio+"&rp_fec_evaluacion="+rp_fec_evaluacion,
					success: function(resultado){
						$("#img-loader-rubrica").html("");
						$("#mensaje").html(resultado);
						salirRubrica(false);
				  }
			});			
		}	
	}
	
	function limpiarCriterio()
	{
		document.getElementById("cr_descripcion").value="";
		document.getElementById("cr_ponderacion").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader-detalle").innerHTML="";
		document.getElementById("cr_descripcion").focus();
	}

	function salirCriterio(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
	}

	function nuevoCriterioPersonalizado()
	{
		limpiarCriterio();
		$("#tituloForm").html("NUEVO CRITERIO PERSONALIZADO DE EVALUACION");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarCriterioPersonalizado()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#mensaje").html("");
		$("#mensaje").css("display","block");
		$("#formulario_nuevo").css("display","block");
		document.getElementById("cr_descripcion").focus();
	}

	function insertarCriterioPersonalizado()
	{
		// Validación de la entrada de datos
		var cr_descripcion = document.getElementById("cr_descripcion").value;
		var cr_ponderacion = document.getElementById("cr_ponderacion").value;
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		var id_asignatura = document.getElementById("id_asignatura").value;

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
			$("#mensaje").html(mensaje);
    	} else if(!reg_texto.test(cr_descripcion)) {
			var mensaje = "La descripci&oacute;n del criterio debe contener al menos cuatro caracteres alfab&eacute;ticos...";
			$("#mensaje").html(mensaje);
			document.getElementById("cr_descripcion").focus();
		} else if(!reg_ponderacion.test(cr_ponderacion)) {
			var mensaje = "El rango de la ponderaci&oacute;n debe estar enter 0 y 1 (con dos decimales de precisi&oacute;n)...";
			$("#mensaje").html(mensaje);
			document.getElementById("cr_ponderacion").focus();
		} else {
			$("#img-loader-detalle").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_docente/insertar_criterio.php",
					data: "id_rubrica_evaluacion="+id_rubrica_evaluacion+"&cr_descripcion="+cr_descripcion+"&cr_ponderacion="+cr_ponderacion+"&id_asignatura="+id_asignatura,
					success: function(resultado){
						$("#img-loader-detalle").html("");
						$("#mensaje").html(resultado);
						listarCriteriosEvaluacion();
						salirCriterio(false);
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

	function editarCriterioEvaluacion(id_criterio)
	{
		salirCriterio(false);
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR CRITERIO DE EVALUACION");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarCriterio()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "rubricas_docente/obtener_criterio.php",
				data: "id_criterio="+id_criterio,
				success: function(resultado){
					var JSONCriterio = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el criterio elegido
					document.getElementById("id_criterio_evaluacion").value=JSONCriterio.id_criterio_evaluacion;
					//document.getElementById("id_rubrica_evaluacion").value=JSONCriterio.id_rubrica_evaluacion;
					document.getElementById("cr_descripcion").value=JSONCriterio.cr_descripcion;
					document.getElementById("cr_ponderacion").value=JSONCriterio.cr_ponderacion;
					setearIndice("cboRubricasEvaluacion",JSONCriterio.id_rubrica_evaluacion);
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("cr_descripcion").focus();
			  }
		});			
	}

	function actualizarCriterio()
	{
		// Validación de la entrada de datos
		var id_criterio_evaluacion = document.getElementById("id_criterio_evaluacion").value;
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
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
			$("#mensaje").html(mensaje);
		} else if(id_rubrica_evaluacion==0) {
			var mensaje = "Debe elegir una r&uacute;brica de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboRubricasEvaluacion").focus();
    	} else if(!reg_texto.test(cr_descripcion)) {
			var mensaje = "La descripci&oacute;n del criterio debe contener al menos cuatro caracteres alfab&eacute;ticos...";
			$("#mensaje").html(mensaje);
			document.getElementById("cr_descripcion").focus();
		} else if(!reg_ponderacion.test(cr_ponderacion)) {
			var mensaje = "El rango de la ponderaci&oacute;n debe estar enter 0 y 1 (con dos decimales de precisi&oacute;n)...";
			$("#mensaje").html(mensaje);
			document.getElementById("cr_ponderacion").focus();
		} else {
			$("#img-loader-detalle").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "rubricas_docente/actualizar_criterio.php",
					data: "id_criterio_evaluacion="+id_criterio_evaluacion+"&id_rubrica_evaluacion="+id_rubrica_evaluacion+"&cr_descripcion="+cr_descripcion+"&cr_ponderacion="+cr_ponderacion,
					success: function(resultado){
						$("#img-loader-detalle").html("");
						$("#mensaje").html(resultado);
						listarCriteriosEvaluacion();
						salirCriterio(false);
				  }
			});			
		}	
	}

	function eliminarCriterioEvaluacion(id_criterio)
	{
		// Validación de la entrada de datos
		
		if (id_criterio==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_criterio...");
			salirCriterio(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este criterio?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "rubricas_docente/eliminar_criterio.php",
						data: "id_criterio="+id_criterio,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarCriteriosEvaluacion();
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
			<td width="5%" class="fuente9" align="right"> R&uacute;brica:&nbsp; </td>
            <td width="5%"> <select id="cboRubricasEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="15%"> <div id="nuevoCriterioPersonalizado" class="link_form" style="display:none"> &nbsp;<a href="#" onclick="nuevoCriterioPersonalizado()">Nuevo Criterio Personalizado</a> </div> </td>
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_rubrica" class="hide">
      <div id="tituloFormRubrica" class="header">Nueva R&uacute;brica Personalizada</div>
      <div id="frmNuevaRubrica" align="left">
   	     <form id="form_nueva_rubrica" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="10%" align="right">Tema:</td>
                  <td width="*">
                     <input id="rp_tema" type="text" class="cajaGrande" name="rp_tema" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="10%" align="right">Fecha de Env&iacute;o:</td>
                  <td width="*">
                     <input id="rp_fec_envio" type="text" class="cajaPequenia" name="rp_fec_envio" maxlength="5" />
                     <img src="imagenes/calendario.png" id="calendario1" name="calendario1" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor=cursor" />
 					 <script type="text/javascript">
                        Calendar.setup(
                          {
                            inputField : "rp_fec_envio",
                            ifFormat   : "%Y-%m-%d",
                            button     : "calendario1"
                          }
                        );
                     </script>
                  </td>
               </tr>
               <tr>
                  <td width="10%" align="right">Fecha de Evaluaci&oacute;n:</td>
                  <td width="*">
                     <input id="rp_fec_evaluacion" type="text" class="cajaPequenia" name="rp_fec_evaluacion" maxlength="5" />
                     <img src="imagenes/calendario.png" id="calendario2" name="calendario2" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor=cursor" />
 					 <script type="text/javascript">
                        Calendar.setup(
                          {
                            inputField : "rp_fec_evaluacion",
                            ifFormat   : "%Y-%m-%d",
                            button     : "calendario2"
                          }
                        );
                     </script>
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_rubrica" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="10%" align="right">
							  <div id="boton_rubrica">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarRubrica" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div id="salirRubrica" class="link_form"><a href="#">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader-rubrica" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_asignatura" />
            <input type="hidden" id="id_paralelo" />
            <input type="hidden" id="id_rubrica_personalizada" />
         </form>
      </div>   
   </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Criterio de Evaluaci&oacute;n</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Descripci&oacute;n:</td>
                  <td width="*">
                     <input id="cr_descripcion" type="text" class="cajaGrande" name="cr_descripcion" maxlength="40" style="text-transform:uppercase" />
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
							  <div id="boton_accion">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarCriterio" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div id="salirCriterio" class="link_form"><a href="#">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader-detalle" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_criterio_evaluacion" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asignaturas">
      <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
      <div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="34%" align="left">Nombre</td>
                <td width="32%" align="left">Curso</td>
                <td width="6%" align="left">Paralelo</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_asignaturas" style="text-align:center"> </div>
   </div>
   <div id="pag_criterio_evaluacion">
      <!-- Aqui va la paginacion de los criterios de evaluacion encontrados -->
      <div class="header2">CRITERIOS PERSONALIZADOS</div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="18%" align="left">R&uacute;brica</td>
                <td width="18%" align="left">Asignatura</td>
                <td width="18%" align="left">Criterio</td>
                <td width="18%" align="left">Ponderaci&oacute;n</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_criterios_evaluacion" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
