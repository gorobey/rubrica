<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarPeriodosEvaluacion();	    
		$("#nuevo_periodo_evaluacion").click(function(e){
			e.preventDefault();
			nuevoPeriodoEvaluacion();
		});		
		$("#limpiarPeriodoEvaluacion").click(function(e){
			e.preventDefault();
			limpiarPeriodoEvaluacion();
		});		
	});

	function listarPeriodosEvaluacion()
	{
		$.get("periodos_evaluacion/listar_periodos_evaluacion.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("No se han definido periodos de evaluacion...");
				}
				else
				{
					$("#lista_periodos_evaluacion").html(resultado);
				}
			}
		);
	}

	function limpiarPeriodoEvaluacion()
	{
		document.getElementById("pe_nombre").value="";
		document.getElementById("pe_abreviatura").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("pe_nombre").focus();
	}

	function salirPeriodoEvaluacion(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		//document.getElementById("mensaje").innerHTML="";
		document.getElementById("nuevo_periodo_evaluacion").focus();
	}

	function nuevoPeriodoEvaluacion()
	{
		limpiarPeriodoEvaluacion();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarPeriodoEvaluacion()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("pe_nombre").focus();
	}

	function insertarPeriodoEvaluacion()
	{
		// Validación de la entrada de datos
		var pe_nombre = document.getElementById("pe_nombre").value;
		var pe_abreviatura = document.getElementById("pe_abreviatura").value;
		var pe_tipo = document.getElementById("cboTiposPeriodosEvaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pe_nombre=eliminaEspacios(pe_nombre);
		pe_abreviatura=eliminaEspacios(pe_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
    	if(!reg_texto.test(pe_nombre)) {
			var mensaje = "El nombre del periodo de evaluaci&oacute;n debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nombre").focus();
    	} else if(pe_abreviatura=="") {
			var mensaje = "Debe ingresar la abreviatura del periodo de evaluaci&oacute;n, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_abreviatura").focus();
		} else if(pe_tipo==0) {
			var mensaje = "Debe elegir el tipo de per&iacute;odo de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboTiposPeriodosEvaluacion").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "periodos_evaluacion/insertar_periodo_evaluacion.php",
					data: "pe_nombre="+pe_nombre+"&pe_abreviatura="+pe_abreviatura+"&pe_tipo="+pe_tipo,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarPeriodosEvaluacion();
						salirPeriodoEvaluacion(false);
				  }
			});			
		}	
	}

	function actualizarPeriodoEvaluacion()
	{
		// Validación de la entrada de datos
		var id_periodo_evaluacion = document.getElementById("id_periodo_evaluacion").value;
		var pe_nombre = document.getElementById("pe_nombre").value;
		var pe_abreviatura = document.getElementById("pe_abreviatura").value;
		var pe_tipo = document.getElementById("cboTiposPeriodosEvaluacion").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pe_nombre=eliminaEspacios(pe_nombre);
		pe_abreviatura=eliminaEspacios(pe_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (id_periodo_evaluacion==0) {
			var mensaje = "No se ha pasado el parámetro de id_periodo_evaluacion";
			$("#mensaje").html(mensaje);
			salirPeriodoEvaluacion();
		} else if(!reg_texto.test(pe_nombre)) {
			var mensaje = "El nombre del perfil debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_nombre").focus();
    	} else if(pe_abreviatura=="") {
			var mensaje = "Debe ingresar la abreviatura del periodo de evaluaci&oacute;n, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("pe_abreviatura").focus();
		} else if(pe_tipo==0) {
			var mensaje = "Debe elegir el tipo de per&iacute;odo de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboTiposPeriodosEvaluacion").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "periodos_evaluacion/actualizar_periodo_evaluacion.php",
					data: "id_periodo_evaluacion="+id_periodo_evaluacion+"&pe_nombre="+pe_nombre+"&pe_abreviatura="+pe_abreviatura+"&pe_tipo="+pe_tipo,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarPeriodosEvaluacion();
						salirPeriodoEvaluacion(false);
				  }
			});			
		}	
	}

	function eliminarPeriodoEvaluacion(id_periodo_evaluacion)
	{
		// Validación de la entrada de datos
		
		if (id_periodo_evaluacion==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_periodo_evaluacion...");
			salirPeriodoEvaluacion(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este periodo de evaluación?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "periodos_evaluacion/eliminar_periodo_evaluacion.php",
						data: "id_periodo_evaluacion="+id_periodo_evaluacion,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarPeriodosEvaluacion();
							salirPeriodoEvaluacion(false);
					  }
				});			
			}
		}	
	}

	function editarPeriodoEvaluacion(id_periodo_evaluacion)
	{
		limpiarPeriodoEvaluacion();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR PERIODO DE EVALUACION");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarPeriodoEvaluacion()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "periodos_evaluacion/obtener_periodo_evaluacion.php",
				data: "id_periodo_evaluacion="+id_periodo_evaluacion,
				success: function(resultado){
					var JSONPeriodoEvaluacion = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el periodo de evaluacion elegido
					document.getElementById("id_periodo_evaluacion").value=JSONPeriodoEvaluacion.id_periodo_evaluacion;
					document.getElementById("pe_nombre").value=JSONPeriodoEvaluacion.pe_nombre;
					document.getElementById("pe_abreviatura").value=JSONPeriodoEvaluacion.pe_abreviatura;
					
					var obj = document.getElementById("cboTiposPeriodosEvaluacion");

				    for (var opcombo=0;opcombo < obj.length;opcombo++){
					    if(obj[opcombo].value==JSONPeriodoEvaluacion.pe_principal){
					       obj.selectedIndex=opcombo;
					    }
					}   

					$("#formulario_nuevo").css("display", "block");
					document.getElementById("pe_nombre").focus();
			  }
		});			
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
            <td> <div id="nuevo_periodo_evaluacion" class="boton"> <a href="#"> Nuevo Periodo de Evaluaci&oacute;n </a> </div> </td>
         </tr>
      </table>
    </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Periodo de Evaluaci&oacute;n</div>
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
                  <td width="15%" align="right">Abreviatura:</td>
                  <td width="*">
                     <input id="pe_abreviatura" type="text" class="cajaPequenia" name="pe_abreviatura" maxlength="10" />
                  </td>
               </tr>
               <tr>
               	  <td width="15%" align="right">Tipo:</td>
                  <td width="*">
                     <select id="cboTiposPeriodosEvaluacion" class="comboPequenio"> 
                        <option value="0"> Seleccione... </option> 
                        <option value="1"> 	QUIMESTRE </option>
                        <option value="2"> 	SUPLETORIO </option>
                        <option value="3"> 	REMEDIAL </option>
                        <option value="4"> 	DE GRACIA </option>
                    </select>
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
                              <div id="limpiarPeriodoEvaluacion" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirPeriodoEvaluacion()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_periodo_evaluacion" name="id_periodo_evaluacion" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_periodo_evaluacion">
      <!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
      <div class="header2"> LISTA DE PERIODOS DE EVALUACION EXISTENTES </div>
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
      <div id="lista_periodos_evaluacion" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
