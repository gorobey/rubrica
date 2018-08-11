<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
            cargarPeriodosEvaluacion();
            listarAportesEvaluacion();	    
            $("#cboPeriodosEvaluacion").change(function(e){
                    e.preventDefault();
                    listarAportesEvaluacion();
            });		
            $("#nuevo_aporte_evaluacion").click(function(e){
                    e.preventDefault();
                    nuevoAporteEvaluacion();
            });		
            $("#limpiarAporteEvaluacion").click(function(e){
                    e.preventDefault();
                    limpiarAporteEvaluacion();
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
                            }
                    }
            );
	}
	
	function listarAportesEvaluacion()
	{
            var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
            $.post("aportes_evaluacion/listar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
                    function(resultado)
                    {
                            if(resultado == false)
                            {
                                    alert("Error");
                            }
                            else
                            {
                                    $("#lista_periodos_evaluacion").html(resultado);
                            }
                    }
            );
	}

	function limpiarAporteEvaluacion()
	{
		document.getElementById("ap_nombre").value="";
                document.getElementById("ap_abreviatura").value = "";
                document.getElementById("ap_fecha_inicio").value = "";
                document.getElementById("ap_fecha_fin").value = "";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("ap_nombre").focus();
	}

	function salirAporteEvaluacion(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("mensaje").innerHTML="";		
		document.getElementById("nuevo_aporte_evaluacion").focus();
	}

	function nuevoAporteEvaluacion()
	{
		limpiarAporteEvaluacion();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarAporteEvaluacion()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("ap_nombre").focus();
	}

	function insertarAporteEvaluacion()
	{
		// Validación de la entrada de datos
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var ap_nombre = document.getElementById("ap_nombre").value;
		var ap_abreviatura = document.getElementById("ap_abreviatura").value;
		var ap_tipo = document.getElementById("cboTiposAporteEvaluacion").value;
                var ap_fecha_inicio = document.getElementById("ap_fecha_inicio").value;
                var ap_fecha_fin = document.getElementById("ap_fecha_fin").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ap_nombre=eliminaEspacios(ap_nombre);
		ap_abreviatura=eliminaEspacios(ap_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (id_periodo_evaluacion == 0) {
			var mensaje = "Debe elegir un periodo de evaluaci&oacute;n";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPeriodosEvaluacion").focus();
                } else if (!reg_texto.test(ap_nombre)) {
			var mensaje = "El nombre del aporte de evaluaci&oacute;n debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ap_nombre").focus();
		} else if (ap_abreviatura == "") {
			var mensaje = "La abreviatura del aporte de evaluaci&oacute;n es obligatoria";
			$("#mensaje").html(mensaje);
			document.getElementById("ap_abreviatura").focus();
		} else if (ap_abreviatura.length > 8) {
			var mensaje = "La abreviatura del aporte de evaluaci&oacute;n no puede contener m&aacute;s de ocho caracteres";
			$("#mensaje").html(mensaje);
			document.getElementById("ap_abreviatura").focus();
		} else if (ap_tipo == 0) {
			var mensaje = "Debe seleccionar un tipo de aporte de evaluaci&oacute;n";
			$("#mensaje").html(mensaje);
			document.getElementById("cboTiposAporteEvaluacion").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
                            type: "POST",
                            url: "aportes_evaluacion/insertar_aporte_evaluacion.php",
                            data: "id_periodo_evaluacion="+id_periodo_evaluacion+"&ap_nombre="+ap_nombre+"&ap_abreviatura="+ap_abreviatura+"&ap_tipo="+ap_tipo+"&ap_fecha_inicio="+ap_fecha_inicio+"&ap_fecha_fin="+ap_fecha_fin,
                            success: function(resultado){
                                    $("#img-loader").html("");
                                    $("#mensaje").html(resultado);
                                    listarAportesEvaluacion();
                                    salirAporteEvaluacion(false);
                            }
			});			
		}	
	}

	function actualizarAporteEvaluacion()
	{
		// Validación de la entrada de datos
		var id_aporte_evaluacion = document.getElementById("id_aporte_evaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var ap_nombre = document.getElementById("ap_nombre").value;
		var ap_abreviatura = document.getElementById("ap_abreviatura").value;
                var ap_fecha_inicio = document.getElementById("ap_fecha_inicio").value;
                var ap_fecha_fin = document.getElementById("ap_fecha_fin").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ap_nombre=eliminaEspacios(ap_nombre);
		ap_abreviatura=eliminaEspacios(ap_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (id_aporte_evaluacion == "") {
			var mensaje = "No se ha pasado el par&aacute;metro id_aporte_evaluacion";
			$("#mensaje").html(mensaje);
		} else if (id_periodo_evaluacion == 0) {
			var mensaje = "Debe elegir un periodo de evaluaci&oacute;n";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPeriodosEvaluacion").focus();
                } else if (!reg_texto.test(ap_nombre)) {
			var mensaje = "El nombre del aporte de evaluaci&oacute;n debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ap_nombre").focus();
		} else if (ap_abreviatura == "") {
			var mensaje = "Debe ingresar la abreviatura del aporte de evaluaci&oacute;n...";
			$("#mensaje").html(mensaje);
			document.getElementById("ap_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
                            type: "POST",
                            url: "aportes_evaluacion/actualizar_aporte_evaluacion.php",
                            data: "id_aporte_evaluacion="+id_aporte_evaluacion+"&id_periodo_evaluacion="+id_periodo_evaluacion+"&ap_nombre="+ap_nombre+"&ap_abreviatura="+ap_abreviatura+"&ap_fecha_inicio="+ap_fecha_inicio+"&ap_fecha_fin="+ap_fecha_fin,
                            success: function(resultado){
                                    $("#img-loader").html("");
                                    $("#mensaje").html(resultado);
                                    listarAportesEvaluacion();
                                    salirAporteEvaluacion(false);
			    }
			});			
		}	
	}

	function eliminarAporteEvaluacion(id_aporte_evaluacion)
	{
		// Validación de la entrada de datos
		
		if (id_aporte_evaluacion=="") {
			$("#mensaje").html("No se ha pasado el parámetro de id_aporte_evaluacion...");
			salirAporteEvaluacion(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este aporte de evaluaci&oacute;n?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
                                    type: "POST",
                                    url: "aportes_evaluacion/eliminar_aporte_evaluacion.php",
                                    data: "id_aporte_evaluacion="+id_aporte_evaluacion,
                                    success: function(resultado){
                                            $("#mensaje").html(resultado);
                                            listarAportesEvaluacion();
                                            salirAporteEvaluacion(false);
                                    }
				});			
			}
		}	
	}

	function editarAporteEvaluacion(id_aporte_evaluacion)
	{
		limpiarAporteEvaluacion();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR APORTE DE EVALUACION");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarAporteEvaluacion()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
                    type: "POST",
                    url: "aportes_evaluacion/obtener_aporte_evaluacion.php",
                    data: "id_aporte_evaluacion="+id_aporte_evaluacion,
                    success: function(resultado){
                            var JSONAporteEvaluacion = eval('(' + resultado + ')');
                            $("#mensaje").html("");
                            //Aqui se va a pintar el aporte de evaluacion elegido
                            document.getElementById("id_aporte_evaluacion").value=JSONAporteEvaluacion.id_aporte_evaluacion;
                            document.getElementById("ap_nombre").value=JSONAporteEvaluacion.ap_nombre;
                            document.getElementById("ap_abreviatura").value=JSONAporteEvaluacion.ap_abreviatura;
                            document.getElementById("ap_fecha_inicio").value=JSONAporteEvaluacion.ap_fecha_inicio;
                            document.getElementById("ap_fecha_fin").value=JSONAporteEvaluacion.ap_fecha_fin;

                            var obj = document.getElementById("cboTiposAporteEvaluacion");

                            for (var opcombo=0;opcombo < obj.length;opcombo++){
                                if(obj[opcombo].value==JSONAporteEvaluacion.ap_tipo){
                                   obj.selectedIndex=opcombo;
                                }
                            }   

                            $("#formulario_nuevo").css("display", "block");
                            document.getElementById("ap_nombre").focus();
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
			<td width="25%"> &nbsp;Per&iacute;odos de Evaluaci&oacute;n:&nbsp; </td>
			<td> <select id="cboPeriodosEvaluacion" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
            <td> <div id="nuevo_aporte_evaluacion" class="boton"> <a href="#"> Nuevo Aporte de Evaluaci&oacute;n </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Aporte de Evaluaci&oacute;n</div>
      <div id="frmNuevo" align="left">
          <form id="form_nuevo" action="" method="post">
              <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                  <tr>
                      <td width="15%" align="right">Nombre:</td>
                      <td width="*">
                          <input id="ap_nombre" type="text" class="cajaGrande" name="ap_nombre" maxlength="40" />
                      </td>
                  </tr>
                  <tr>
                      <td width="15%" align="right">Abreviatura:</td>
                      <td width="*">
                          <input id="ap_abreviatura" type="text" class="cajaMedia" name="ap_abreviatura" maxlength="8" />
                      </td>
                  </tr>
                  <tr>
                      <td width="15%" align="right">Tipo:</td>
                      <td width="*">
                          <select id="cboTiposAporteEvaluacion" class="comboMedio"> 
                              <option value="0"> Seleccione... </option> 
                              <option value="1"> 	PARCIAL </option>
                              <option value="2"> 	EXAMEN QUIMESTRAL </option>
                              <option value="3"> 	SUPLETORIO </option>
                          </select>
                      </td>
                  </tr>
                  <tr>
                      <td width="15%" align="right">Fecha Inicio:</td>
                      <td width="*">
                          <input id="ap_fecha_inicio" type="text" class="cajaPequenia" name="ap_fecha_inicio" maxlength="40" disabled />
                          <img src="imagenes/calendario.png" id="calendario1" name="calendario1" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor = cursor"/> 
                          <script type="text/javascript">
                              Calendar.setup(
                                {
                                    inputField: "ap_fecha_inicio",
                                    ifFormat: "%Y-%m-%d",
                                    button: "calendario1"
                                }
                              );
                          </script>
                      </td>
                  </tr>
                  <tr>
                      <td width="15%" align="right">Fecha Fin:</td>
                      <td width="*">
                          <input id="ap_fecha_fin" type="text" class="cajaPequenia" name="ap_fecha_fin" maxlength="40" disabled />
                          <img src="imagenes/calendario.png" id="calendario2" name="calendario2" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor = cursor"/> 
                          <script type="text/javascript">
                              Calendar.setup(
                                {
                                    inputField: "ap_fecha_fin",
                                    ifFormat: "%Y-%m-%d",
                                    button: "calendario2"
                                }
                              );
                          </script>
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
                                      <div id="limpiarAporteEvaluacion" class="link_form"><a href="#">Limpiar</a></div>
                                  </td>
                                  <td width="5%" align="right">
                                      <div class="link_form"><a href="#" onclick="salirAporteEvaluacion()">Salir</a></div>
                                  </td>
                                  <td width="*">
                                      <div id="img-loader" style="padding-left:2px"></div>
                                  </td>
                              </tr>
                          </table>
                      </td>
                  </tr>
              </table>
              <input type="hidden" id="id_aporte_evaluacion" name="id_aporte_evaluacion" />
              <input type="hidden" id="id_periodo_lectivo" value= <?php echo $_SESSION["id_periodo_lectivo"]; ?> />
          </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_periodo_evaluacion">
      <!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
      <div class="header2"> LISTA DE APORTES DE EVALUACION EXISTENTES </div>
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
