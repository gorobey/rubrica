<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarAsignaturas();
		cargar_tipos_asignatura();
		$("#nueva_asignatura").click(function(e){
			e.preventDefault();
			nuevaAsignatura();
		});		
		$("#limpiarAsignatura").click(function(e){
			e.preventDefault();
			limpiarAsignatura();
		});		
	});

	function cargar_tipos_asignatura()
	{
		$.get("scripts/cargar_tipos_asignatura.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboTiposAsignatura').append(resultado);			
			}
		});	
	}

	function listarAsignaturas()
	{
		$.post("asignaturas/listar_asignaturas.php", 
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

	function limpiarAsignatura()
	{
		$("input").val("");
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("as_nombre").focus();
	}

	function salirAsignatura(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nueva_asignatura").focus();
	}

	function nuevaAsignatura()
	{
		limpiarAsignatura();
		$("#tituloForm").html("NUEVA ASIGNATURA");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarAsignatura()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("as_nombre").focus();
	}

	function insertarAsignatura()
	{
		// Validación de la entrada de datos
		var id_tipo_asignatura = document.getElementById("cboTiposAsignatura").value;
		var as_nombre = document.getElementById("as_nombre").value;
		var as_abreviatura = document.getElementById("as_abreviatura").value;
		var as_carga_horaria = document.getElementById("as_carga_horaria").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		as_nombre=eliminaEspacios(as_nombre);
		as_abreviatura=eliminaEspacios(as_abreviatura);
		as_carga_horaria=eliminaEspacios(as_carga_horaria);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,84})$/i;
		var reg_abreviatura = /^([a-zA-Z.]{3,6})$/i;
		var reg_carga_horaria = /^([0-9]{1,2})$/i;
		
    	if(!reg_texto.test(as_nombre)) {
			var mensaje = "El nombre de la asignatura debe contener al menos cuatro caracteres alfab&eacute;ticos";
			$("#mensaje").html(mensaje);
			document.getElementById("as_nombre").focus();
		} else if(!reg_abreviatura.test(as_abreviatura)) {
			var mensaje = "La abreviatura de la asignatura debe contener al menos tres caracteres num&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("as_abreviatura").focus();
		} else if(!reg_carga_horaria.test(as_carga_horaria)) {
			var mensaje = "La carga horaria debe contener al menos un car&aacute;cter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("as_carga_horaria").focus();
		} else if(id_tipo_asignatura == 0) {
			var mensaje = "Debe elegir el tipo de asignatura a ser asociado la asignatura";
			$("#mensaje").html(mensaje);
			document.getElementById("cboTiposAsignatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas/insertar_asignatura.php",
					data: "id_tipo_asignatura="+id_tipo_asignatura+"&as_nombre="+as_nombre+"&as_abreviatura="+as_abreviatura+"&as_carga_horaria="+as_carga_horaria,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarAsignaturas();
						salirAsignatura(false);
				  }
			});
		}
	}

	function actualizarAsignatura()
	{
		// Validación de la entrada de datos
		var id_tipo_asignatura = document.getElementById("cboTiposAsignatura").value;
		var id_asignatura = document.getElementById("id_asignatura").value;
		var as_nombre = document.getElementById("as_nombre").value;
		var as_abreviatura = document.getElementById("as_abreviatura").value;
		var as_carga_horaria = document.getElementById("as_carga_horaria").value;
		var as_orden = document.getElementById("as_orden").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		as_nombre = eliminaEspacios(as_nombre);
		as_abreviatura = eliminaEspacios(as_abreviatura);
		as_carga_horaria = eliminaEspacios(as_carga_horaria);
		as_orden = eliminaEspacios(as_orden);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ,]{4,84})$/i;
		var reg_abreviatura = /^([a-zA-Z.]{3,6})$/i;
		var reg_numero = /^([0-9]{1,2})$/i;
		
    	if(!reg_texto.test(as_nombre)) {
			var mensaje = "El nombre de la asignatura debe contener al menos cuatro caracteres alfab&eacute;ticos";
			$("#mensaje").html(mensaje);
			document.getElementById("as_nombre").focus();
		} else if(!reg_abreviatura.test(as_abreviatura)) {
			var mensaje = "La abreviatura de la asignatura debe contener al menos tres caracteres num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("as_abreviatura").focus();
		} else if(!reg_numero.test(as_carga_horaria)) {
			var mensaje = "La carga horaria debe contener al menos un car&aacute;cter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("as_carga_horaria").focus();
		} else if(!reg_numero.test(as_orden)) {
			var mensaje = "El orden debe contener al menos un car&aacute;cter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("as_orden").focus();
		} else if(id_tipo_asignatura == 0) {
			var mensaje = "Debe elegir el tipo de asignatura a ser asociado la asignatura";
			$("#mensaje").html(mensaje);
			document.getElementById("cboTiposAsignatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas/actualizar_asignatura.php",
					data: "id_asignatura="+id_asignatura+"&id_tipo_asignatura="+id_tipo_asignatura+"&as_nombre="+as_nombre+"&as_abreviatura="+as_abreviatura+"&as_carga_horaria="+as_carga_horaria+"&as_orden="+as_orden,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarAsignaturas();
						salirAsignatura(false);
				  }
			});
		}
	}

	function eliminarAsignatura(id_asignatura)
	{
		// Validación de la entrada de datos
		
		if (id_asignatura==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_asignatura...");
			salirAsignatura(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar esta asignatura?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "asignaturas/eliminar_asignatura.php",
						data: "id_asignatura="+id_asignatura,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarAsignaturas();
							salirAsignatura(false);
					  }
				});			
			}
		}	
	}

	function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
	}

	function editarAsignatura(id_asignatura)
	{
		limpiarAsignatura();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR ASIGNATURA");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarAsignatura()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "asignaturas/obtener_asignatura.php",
				data: "id_asignatura="+id_asignatura,
				success: function(resultado){
					var JSONasignatura = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el asignatura elegido
					document.getElementById("id_asignatura").value=JSONasignatura.id_asignatura;
					document.getElementById("as_nombre").value=JSONasignatura.as_nombre;
					document.getElementById("as_abreviatura").value=JSONasignatura.as_abreviatura;
					document.getElementById("as_carga_horaria").value=JSONasignatura.as_carga_horaria;
					document.getElementById("as_orden").value=JSONasignatura.as_orden;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("as_nombre").focus();
					setearIndice("cboTiposAsignatura",JSONasignatura.id_tipo_asignatura);
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
            <td> <div id="nueva_asignatura" class="boton" style="display:block"> <a href="#"> Nueva Asignatura </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nueva Asignatura</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="as_nombre" type="text" class="cajaExtraGrande" name="as_nombre" maxlength="84" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Abreviatura:</td>
                  <td width="*">
                     <input id="as_abreviatura" type="text" class="cajaMedia" name="as_abreviatura" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Tipo de Asignatura:</td>
                  <td width="*">
                     <select id="cboTiposAsignatura" class="comboMedio"> </select>
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Carga Horaria:</td>
                  <td width="*">
                     <input id="as_carga_horaria" type="text" class="cajaPequenia" name="as_carga_horaria" maxlength="2" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Orden:</td>
                  <td width="*">
                     <input id="as_orden" type="text" class="cajaPequenia" name="as_orden" maxlength="2" />
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
                              <div id="limpiarAsignatura" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirAsignatura()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_asignatura" name="id_asignatura" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asignaturas">
      <!-- Aqui va la paginacion de las asignaturas encontradas -->
      <div class="header2"> LISTA DE ASIGNATURAS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="36%" align="left">Nombre</td>
                <td width="18%" align="left">Abreviatura</td>
                <td width="18%" align="left">Carga Horaria</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_asignaturas" style="text-align:center"> Debe elegir el curso... </div>
   </div>
</div>
</body>
</html>
