<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarDiasSemana();
		$("#cboDiasSemana").change(function(e){
			e.preventDefault();
			listarHorasClase();
		});
		$("#nueva_hora_clase").click(function(e){
			e.preventDefault();
			nuevaHoraClase();
		});
	});

	function cargarDiasSemana()
	{
		$.get("scripts/cargar_dias_semana.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboDiasSemana").append(resultado);
				}
			}
		);
	}

	function limpiarHoraClase()
	{
		document.getElementById("hc_nombre").value="";
		document.getElementById("hc_hora_inicio").value="";
		document.getElementById("hc_hora_fin").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("hc_nombre").focus();
	}

	function salirHoraClase(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("mensaje").innerHTML="";		
		document.getElementById("nueva_hora_clase").focus();
	}
	
	function nuevaHoraClase()
	{
		limpiarHoraClase();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarHoraClase()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("hc_nombre").focus();
	}

	function insertarHoraClase()
	{
		// Validación de la entrada de datos
		var id_dia_semana = document.getElementById("cboDiasSemana").value;
		var hc_nombre = document.getElementById("hc_nombre").value;
		var hc_hora_inicio = document.getElementById("hc_hora_inicio").value;
		var hc_hora_fin = document.getElementById("hc_hora_fin").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		hc_nombre=eliminaEspacios(hc_nombre);
		hc_hora_inicio=eliminaEspacios(hc_hora_inicio);
		hc_hora_fin=eliminaEspacios(hc_hora_fin);

		var reg_texto = /^([a-zA-Z0-9. ñáéíóúÑÁÉÍÓÚ]{1,10})$/i;
		var reg_numero = /^([0-9]{1,2})$/i;
		
		if (id_dia_semana == 0) {
			var mensaje = "Debe elegir un d&iacute;a de la semana...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboDiasSemana").focus();
    	} else if (!reg_texto.test(hc_nombre)) {
			var mensaje = "El nombre de la hora clase debe contener al menos un caracter alfanum&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_nombre").focus();
		} else if (hc_hora_inicio=="") {
			var mensaje = "La Hora de Inicio es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_hora_inicio").focus();
		} else if (hc_hora_fin=="") {
			var mensaje = "La Hora de Fin es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_hora_fin").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/insertar_hora_clase.php",
					data: "id_dia_semana="+id_dia_semana+"&hc_nombre="+hc_nombre+"&hc_hora_inicio="+hc_hora_inicio+"&hc_hora_fin="+hc_hora_fin,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarHorasClase();
						salirHoraClase(false);
				  }
			});			
		}	
	}

	function actualizarHoraClase()
	{
		// Validación de la entrada de datos
		var id_hora_clase = document.getElementById("id_hora_clase").value;
		var id_dia_semana = document.getElementById("cboDiasSemana").value;
		var hc_nombre = document.getElementById("hc_nombre").value;
		var hc_hora_inicio = document.getElementById("hc_hora_inicio").value;
		var hc_hora_fin = document.getElementById("hc_hora_fin").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		hc_nombre=eliminaEspacios(hc_nombre);
		hc_hora_inicio=eliminaEspacios(hc_hora_inicio);
		hc_hora_fin=eliminaEspacios(hc_hora_fin);

		var reg_texto = /^([a-zA-Z0-9. ñáéíóúÑÁÉÍÓÚ]{1,10})$/i;
		
		if (id_dia_semana == "") {
			var mensaje = "No se ha pasado el par&aacute;metro id_dia_semana";
			$("#mensaje").html(mensaje);
		} else if (id_dia_semana == 0) {
			var mensaje = "Debe elegir un d&iacute;a de la semana...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboDiasSemana").focus();
    	} else if (!reg_texto.test(hc_nombre)) {
			var mensaje = "El nombre de la hora clase debe contener al menos un caracteres alfanum&eacute;rico...";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_nombre").focus();
		} else if (hc_hora_inicio == "") {
			var mensaje = "Debe ingresar la hora de inicio de la hora clase...";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_hora_inicio").focus();
		} else if (hc_hora_fin == "") {
			var mensaje = "Debe ingresar la hora de fin de la hora clase...";
			$("#mensaje").html(mensaje);
			document.getElementById("hc_hora_fin").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
				type: "POST",
				url: "horarios/actualizar_hora_clase.php",
				data: "id_hora_clase="+id_hora_clase+"&id_dia_semana="+id_dia_semana+"&hc_nombre="+hc_nombre+"&hc_hora_inicio="+hc_hora_inicio+"&hc_hora_fin="+hc_hora_fin,
				success: function(resultado){
					$("#img-loader").html("");
					$("#mensaje").html(resultado);
					listarHorasClase();
					salirHoraClase(false);
			    }
			});
		}	
	}

	function editarHoraClase(id_hora_clase)
	{
		limpiarHoraClase();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR HORA CLASE");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarHoraClase()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "horarios/obtener_hora_clase.php",
				data: "id_hora_clase="+id_hora_clase,
				success: function(resultado){
					var JSONAporteEvaluacion = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar la hora clase elegida
					document.getElementById("id_hora_clase").value=JSONAporteEvaluacion.id_hora_clase;
					document.getElementById("hc_nombre").value=JSONAporteEvaluacion.hc_nombre;
					document.getElementById("hc_hora_inicio").value=JSONAporteEvaluacion.hc_hora_inicio;
					document.getElementById("hc_hora_fin").value=JSONAporteEvaluacion.hc_hora_fin;

					var obj = document.getElementById("cboDiasSemana");

				    for (var opcombo=0;opcombo < obj.length;opcombo++){
					    if(obj[opcombo].value==JSONAporteEvaluacion.id_dia_semana){
					       obj.selectedIndex=opcombo;
					    }
					}   

					$("#formulario_nuevo").css("display", "block");
					document.getElementById("hc_nombre").focus();
			  }
		});			
	}
	
	function listarHorasClase()
	{
		var id_dia_semana = document.getElementById("cboDiasSemana").value;
		$.post("horarios/listar_horas_clase.php", { id_dia_semana: id_dia_semana },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_horas_clase").html(resultado);
				}
			}
		);
	}
	
	function eliminarHoraClase(id_hora_clase)
	{
		if (id_hora_clase == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado correctamente el par&aacute;metros id_hora_clase...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/eliminar_hora_clase.php",
					data: "id_hora_clase="+id_hora_clase,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listarHorasClase();
				  }
			});			
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
         	<td width="15%"> D&iacute;a de la Semana: </td>
            <td width="15%"> 
            	<select id="cboDiasSemana" class="comboPequenio"> 
                	<option value="0"> Seleccione... </option> 
                </select>
            </td>
            <td width="*"> <div id="nueva_hora_clase" class="boton"> <a href="#"> Nueva Hora Clase </a> </div> </td>
         </tr>
      </table>
    </div>

	<div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nueva Hora Clase</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="hc_nombre" type="text" class="cajaMedia" name="hc_nombre" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Hora de Inicio:</td>
                  <td width="*">
                     <input id="hc_hora_inicio" type="text" class="cajaMedia" name="hc_hora_inicio" maxlength="8" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Hora de Fin:</td>
                  <td width="*">
                     <input id="hc_hora_fin" type="text" class="cajaMedia" name="hc_hora_fin" maxlength="8" />
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
                              <div id="limpiarHoraClase" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirHoraClase()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_hora_clase" name="id_hora_clase" />
         </form>
      </div>   
    </div>

	<div id="mensaje" class="error"></div>

    <div id="pag_horas_clase">
      <!-- Aqui va la paginacion de las horas clase -->
      <div class="header2"> LISTA DE HORAS CLASE EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="24%" align="left">Hora</td>
                <td width="24%" align="left">Hora de Inicio</td>
                <td width="24%" align="left">Hora de Fin</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_horas_clase" style="text-align:center"> Debe elegir un d&iacute;a de la semana... </div>
   </div>
</div>
</body>
</html>
