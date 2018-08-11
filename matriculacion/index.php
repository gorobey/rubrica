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
		cargar_paralelos();
		listarEstudiantesParalelo(0,false);
		$("#img-loader-busqueda").hide();
		$("#estudiante_nuevo").click(function(e){
			e.preventDefault();
			nuevoEstudiante();
		});
		$("#estudiante_antiguo").click(function(e){
			e.preventDefault();
			antiguoEstudiante();
		});
		$("#cboParalelos").change(function(){
			contarEstudiantesParalelo($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
			$("#formulario_nuevo").css("display","none");
		});
		$("#datos_representante").click(function(){
			//Aqui se van a realizar las instrucciones necesarias para actualizar los datos del representante del estudiante
			$("#datos_estudiante").show();
			obtenerRepresentante();
			$(this).hide();
		});
		$("#datos_estudiante").click(function(){
			//Aqui se van a realizar las instrucciones necesarias para actualizar los datos del representante del estudiante
			$("#datos_representante").show();
			$("#formulario_representante").hide();
			$("#formulario_nuevo").show();
			$(this).hide();		
			$("#datos_estudiante").show();
			$(this).hide();
		});
		$("#datos_estudiante").click(function(){
			//Aqui se va a presentar el formulario de actualizacion de datos
			$("#formulario_representante").hide();
			$("#formulario_nuevo").show();
			$("#datos_representante").show();
			$(this).hide();
		})
	});

	function cargar_paralelos()
	{
		$.get("scripts/cargar_paralelos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboParalelos').append(resultado);			
			}
		});	
	}

	function cargarNuevoParalelo()
	{
		$.get("scripts/cargar_paralelos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				document.getElementById('cboNuevoParalelo').length = 1;
				$('#cboNuevoParalelo').append(resultado);			
			}
		});	
	}

	function contarEstudiantesParalelo(id_paralelo)
	{
		var numero_pagina = $("#numero_pagina").val();
		$.post("calificaciones/contar_estudiantes_paralelo.php", { id_paralelo: id_paralelo },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONNumRegistros = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistros.num_registros;
					$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: "+total_registros);
					if (total_registros == 0) {
						$("#paginacion_estudiantes").html("");
						$("#lista_estudiantes").html("No existen estudiantes matriculados en este paralelo...");
					} else {
						//paginarEstudiantesParalelo(10,numero_pagina,total_registros,id_paralelo);
						listarEstudiantesParalelo(id_paralelo,false);
					}
				}
			}
		);
	}

	function listarEstudiantesParalelo(id_paralelo,iDesplegar)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		if (id_paralelo == 0) {
			document.getElementById("lista_estudiantes").innerHTML = "Debe elegir un paralelo...";
		} else {
			$.post("matriculacion/listar_estudiantes.php", 
				{ 
					id_paralelo: id_paralelo
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						//if (!iDesplegar) $("#mensaje").html("");
						$("#lista_estudiantes").html(resultado);
					}
				}
			);
		}
	}

	function limpiarEstudiante()
	{
		document.getElementById("es_apellidos").value="";
		document.getElementById("es_nombres").value="";
		document.getElementById("es_cedula").value="";
		document.getElementById("es_email").value="";
		document.getElementById("es_fec_nac").value="";
		document.getElementById("es_edad").value="";
		document.getElementById("es_direccion").value="";
		document.getElementById("es_sector").value="";
		document.getElementById("es_telefono").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		$("#formulario_representante").hide();
	}

	function limpiarRepresentante()
	{
		document.getElementById("re_cedula").value="";
		document.getElementById("re_parentesco").value="";
		document.getElementById("re_apellidos").value="";
		document.getElementById("re_nombres").value="";
		document.getElementById("re_email").value="";
		document.getElementById("re_direccion").value="";
		document.getElementById("re_sector").value="";
		document.getElementById("re_telefono").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		$("#formulario_nuevo").hide();
	}

	function salirEstudiante(iSalir)
	{
		if(iSalir) $("#mensaje").html("");
		$("#formulario_nuevo").css("display", "none");
		$("#datos_representante").hide();
		document.getElementById("estudiante_nuevo").focus();
	}

	function nuevoEstudiante()
	{
		limpiarEstudiante();
		$("#lblNuevoParalelo").hide();
		$("#comboNuevoParalelo").hide();
		$("#formulario_busqueda").hide();
		$("#formulario_representante").hide();
		$("#tituloForm").html("ESTUDIANTE NUEVO");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarEstudiante()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("es_cedula").focus();
	}

	function insertarEstudiante()
	{
		// Validación de la entrada de datos
		var id_paralelo = document.getElementById("cboParalelos").value;
		var es_apellidos = document.getElementById("es_apellidos").value;
		var es_nombres = document.getElementById("es_nombres").value;
		var es_cedula = document.getElementById("es_cedula").value;
		var es_email = document.getElementById("es_email").value;
		var es_genero = $('input:radio[name=Genero]:checked').val();
		var es_direccion = document.getElementById("es_direccion").value;
		var es_sector = document.getElementById("es_sector").value;
		var es_telefono = document.getElementById("es_telefono").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		es_apellidos=eliminaEspacios(es_apellidos);
		es_nombres=eliminaEspacios(es_nombres);
		es_cedula=eliminaEspacios(es_cedula);
		es_email=eliminaEspacios(es_email);
		es_direccion=eliminaEspacios(es_direccion);
		es_sector=eliminaEspacios(es_sector);
		es_telefono=eliminaEspacios(es_telefono);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
		var reg_cedula = /^([0-9]{0,10})$/i;
		var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
		
		if (id_paralelo==0) {
			$("#mensaje").html("Debe seleccionar un paralelo...");
			document.getElementById("cboParalelos").focus();
        } else if(!reg_cedula.test(es_cedula)) {
			$("#mensaje").html("La c&eacute;dula del estudiante debe contener diez caracteres num&eacute;ricos");
			document.getElementById("es_cedula").focus();
        } else if(!reg_texto.test(es_apellidos)) {
			$("#mensaje").html("Los apellidos del estudiante deben contener al menos tres caracteres alfab&eacute;ticos");
			document.getElementById("es_apellidos").focus();
        } else if(!reg_texto.test(es_nombres)) {
			$("#mensaje").html("Los nombres del estudiante deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombres").focus();
		} else if(!reg_email.test(es_email)) {
			$("#mensaje").html("Direcci&oacute;n de correo electr&oacute;nico no v&aacute;lida");
			document.getElementById("es_email").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
				type: "POST",
				url: "matriculacion/insertar_estudiante.php",
				data: "id_paralelo="+id_paralelo
						+"&es_apellidos="+es_apellidos
						+"&es_nombres="+es_nombres
						+"&es_cedula="+es_cedula
						+"&es_genero="+es_genero
						+"&es_email="+es_email
						+"&es_direccion="+es_direccion
						+"&es_sector="+es_sector
						+"&es_telefono="+es_telefono,
				dataType: "json",
				success: function(resultado){
					$("#img-loader").html("");
					contarEstudiantesParalelo(id_paralelo);
					$("#formulario_nuevo").hide();
					console.log(resultado);
					if(resultado.ok){
						$("#id_estudiante").val(resultado.id_estudiante);
						//Aqui va el proceso para ingresar los datos del representante
						$("#btnEditarRepresentante").html("Ingresar");
						$("#formulario_representante").show();
					}
					$("#mensaje").html(resultado.mensaje);
				}
			});			
		}	
	}

	function actualizarEstudiante()
	{
		// Validación de la entrada de datos
		var id_estudiante = document.getElementById("id_estudiante").value;
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_paralelo_nuevo = document.getElementById("cboNuevoParalelo").value;
		var es_apellidos = document.getElementById("es_apellidos").value;
		var es_nombres = document.getElementById("es_nombres").value;
		var es_cedula = document.getElementById("es_cedula").value;
		var es_email = document.getElementById("es_email").value;
		var es_direccion = document.getElementById("es_direccion").value;
		var es_sector = document.getElementById("es_sector").value;
		var es_telefono = document.getElementById("es_telefono").value;
		var es_genero = $('input:radio[name=Genero]:checked').val();

		// Saco los espacios en blanco al comienzo y al final de la cadena
		es_apellidos=eliminaEspacios(es_apellidos);
		es_nombres=eliminaEspacios(es_nombres);
		es_cedula=eliminaEspacios(es_cedula);
		es_email=eliminaEspacios(es_email);
		es_direccion=eliminaEspacios(es_direccion);
		es_sector=eliminaEspacios(es_sector);
		es_telefono=eliminaEspacios(es_telefono);
                
        //alert(es_email);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_cedula = /^([0-9]{0,10})$/i;
		var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
		
		if (id_estudiante=="") {
			$("#mensaje").html("No se ha pasado el par&aacute;metro id_estudiante...");
		} else if (id_paralelo==0) {
			$("#mensaje").html("Debe seleccionar un paralelo...");
			document.getElementById("cboParalelos").focus();
        } else if(!reg_texto.test(es_apellidos)) {
			$("#mensaje").html("Los apellidos del estudiante deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_apellidos").focus();
        } else if(!reg_texto.test(es_nombres)) {
			$("#mensaje").html("Los nombres del estudiante deben contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombres").focus();
		} else if(!reg_email.test(es_email)) {
			$("#mensaje").html("Direcci&oacute;n de correo electr&oacute;nico no v&aacute;lida");
			document.getElementById("es_email").focus();
		} else {
            if(id_paralelo_nuevo!=0){
                //Quiere decir que hay que cambiar de paralelo al estudiante
                id_paralelo = id_paralelo_nuevo;
            }
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
				type: "POST",
				url: "matriculacion/actualizar_estudiante.php",
				data: "id_estudiante="+id_estudiante
						+"&id_paralelo="+id_paralelo
						+"&es_apellidos="+es_apellidos
						+"&es_nombres="+es_nombres
						+"&es_cedula="+es_cedula
						+"&es_genero="+es_genero
						+"&es_email="+es_email
						+"&es_direccion="+es_direccion
						+"&es_sector="+es_sector
						+"&es_telefono="+es_telefono,
				success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						id_paralelo = document.getElementById("cboParalelos").value;
						contarEstudiantesParalelo(id_paralelo);
						salirEstudiante(false);
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

	function obtenerRepresentante()
	{
		//Funcion para recuperar los datos del representante
		var id_estudiante = $("#id_estudiante").val();
		limpiarRepresentante();
		$.ajax({
			type: "POST",
			url: "matriculacion/obtener_representante.php",
			data: "id_estudiante="+id_estudiante,
			success: function(resultado){
				//Si todo ha ido bien, tenemos los datos del representante en formato JSON
				var JSONRepresentante = eval('('+ resultado + ')');
				if(JSONRepresentante){
					//console.log(JSONRepresentante);
					//Aqui se va a pintar el representante recuperado
					document.getElementById("id_representante").value = JSONRepresentante.id_representante;
					document.getElementById("re_cedula").value = JSONRepresentante.re_cedula;
					document.getElementById("re_apellidos").value = JSONRepresentante.re_apellidos;
					document.getElementById("re_nombres").value = JSONRepresentante.re_nombres;
					document.getElementById("re_direccion").value = JSONRepresentante.re_direccion;
					document.getElementById("re_telefono").value = JSONRepresentante.re_telefono;
					document.getElementById("re_sector").value = JSONRepresentante.re_sector;
					document.getElementById("re_parentesco").value = JSONRepresentante.re_parentesco;
				}
				document.getElementById("re_cedula").focus();
			}
		});
		$("#formulario_representante").show();
	}

	function editarRepresentante()
	{
		// Procedimiento para actualizar los datos del representante
		var id_representante = $("#id_representante").val();
		var id_estudiante = $("#id_estudiante").val();
		var cedula = $("#re_cedula").val();
		var parentesco = $("#re_parentesco").val();
		var apellidos = $("#re_apellidos").val();
		var nombres = $("#re_nombres").val();
		var direccion = $("#re_direccion").val();
		var sector = $("#re_sector").val();
		var email = $("#re_email").val();
		var telefono = $("#re_telefono").val();
		var observacion = $("#re_observacion").val();
		// Elimino los espacios en blanco a la izquierda y a la derecha
		cedula = cedula.trim();
		parentesco = parentesco.trim();
		apellidos = apellidos.trim();
		nombres = nombres.trim();
		direccion = direccion.trim();
		sector = sector.trim();
		email = email.trim();
		telefono = telefono.trim();
		observacion = observacion.trim();
		if(parentesco==""){
			$("#mensaje").html("Debe ingresar el parentesco del representante...");
			$("#re_parentesco").focus();
			return false;
		}else if(apellidos==""){
			$("#mensaje").html("Debe ingresar los apellidos del representante...");
			$("#re_apellidos").focus();
			return false;
		}else if(nombres==""){
			$("#mensaje").html("Debe ingresar los nombres del representante...");
			$("#re_nombres").focus();
			return false;
		}else if(telefono==""){
			$("#mensaje").html("Debe ingresar el telefono del representante...");
			$("#re_telefono").focus();
			return false;
		}
		// Si se han ingresado los campos obligatorios se procede a la actualizacion de datos
		$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		alert(id_estudiante);
		$.ajax({
			type: "POST",
			url: "matriculacion/actualizar_representante.php",
			data: "id_representante="+id_representante
					+"&id_estudiante="+id_estudiante
					+"&re_apellidos="+apellidos
					+"&re_nombres="+nombres
					+"&re_nombre_completo="+apellidos+" "+nombres
					+"&re_cedula="+cedula
					+"&re_email="+email
					+"&re_direccion="+direccion
					+"&re_sector="+sector
					+"&re_telefono="+telefono
					+"&re_observacion="+observacion
					+"&re_parentesco="+parentesco,
			success: function(resultado){
					$("#img-loader").html("");
					$("#formulario_representante").hide();
					$("#datos_estudiante").hide();
					$("#datos_representante").hide();
					$("#mensaje").html(resultado);
			}
		});	
	}

	function editarEstudiante(id_estudiante)
	{
		limpiarEstudiante();
		$("#lblNuevoParalelo").show();
		$("#comboNuevoParalelo").show();
		$("#datos_representante").show();
		$("#datos_estudiante").hide();
		$("#id_estudiante").val(id_estudiante);
		cargarNuevoParalelo();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR ESTUDIANTE");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarEstudiante()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
			type: "POST",
			url: "matriculacion/obtener_estudiante.php",
			data: "id_estudiante="+id_estudiante,
			success: function(resultado){
				var JSONEstudiante = eval('(' + resultado + ')');
				$("#mensaje").html("");
				//Aqui se va a pintar el estudiante elegido
				document.getElementById("id_estudiante").value=JSONEstudiante.id_estudiante;
				document.getElementById("es_apellidos").value=JSONEstudiante.es_apellidos;
				document.getElementById("es_nombres").value=JSONEstudiante.es_nombres;
				document.getElementById("es_cedula").value=JSONEstudiante.es_cedula;
				document.getElementById("es_email").value=JSONEstudiante.es_email;
				document.getElementById("es_direccion").value=JSONEstudiante.es_direccion;
				document.getElementById("es_sector").value=JSONEstudiante.es_sector;
				document.getElementById("es_telefono").value=JSONEstudiante.es_telefono;
				$("input:radio[value='" + JSONEstudiante.es_genero + "']").attr('checked', true);
				$("#formulario_nuevo").css("display", "block");
				document.getElementById("es_cedula").focus();
				setearIndice("cboParalelos",JSONEstudiante.id_paralelo);
			}
		});			
	}

	function salirRepresentante()
	{
		$("#mensaje").html("");
		$("#formulario_representante").css("display", "none");
		$("#datos_estudiante").hide();
		document.getElementById("estudiante_nuevo").focus();
		$("#formulario_representante").hide();
		$("#mensaje").html("");
	}

	function quitarEstudiante(id_estudiante)
	{
		// Quitar al estudiante del paralelo
		
		var id_paralelo = $("#cboParalelos").val();
		
		if (id_estudiante=="") {
			$("#mensaje").html("No se ha pasado el parámetro de id_estudiante...");
			salirEstudiante(false);
		} else {
			var eliminar = confirm("¿Desea eliminar definitivamente este estudiante?")
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
				type: "POST",
				url: "matriculacion/quitar_estudiante.php",
				data: "id_estudiante="+id_estudiante+"&iTotal="+eliminar+"&id_paralelo="+id_paralelo,
				success: function(resultado){
					$("#mensaje").html(resultado);
					contarEstudiantesParalelo(id_paralelo);
					salirEstudiante(eliminar);
				}
			});			
		}			
	}

	function limpiarBusqueda()
	{
		document.getElementById("txt_patron").value="";
		document.getElementById("lista_busqueda").innerHTML = "";
		document.getElementById("txt_patron").focus();
	}

	function salirBusqueda(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_busqueda").css("display", css_display);
		$("#pag_busqueda").css("display", css_display);
		document.getElementById("estudiante_antiguo").focus();
	}

	function antiguoEstudiante()
	{
		limpiarBusqueda();
		salirEstudiante(true);
		$("#formulario_busqueda").css("display", "block");
		document.getElementById("txt_patron").focus();
	}
	
	function buscarEstudiantes()
	{
		//Aqui va el codigo para buscar estudiantes antiguos
		var patron = eliminaEspacios(document.getElementById("txt_patron").value);

		var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
		
		if (patron=="") {
			$("#mensaje").html("Debe ingresar el patr&oacute;n de b&uacute;squeda...");
			document.getElementById("txt_patron").focus();
        } else if(!reg_texto.test(patron)) {
			$("#mensaje").html("El patr&oacute;n de b&uacute;squeda debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("txt_patron").focus();
		} else {
			$("#mensaje").html("");
			$("#img-loader-busqueda").show();
			$.ajax({
				type: "POST",
				url: "matriculacion/buscar_estudiantes_antiguos.php",
				data: "patron="+patron,
				success: function(resultado){
					$("#img-loader-busqueda").hide();
					$("#lista_busqueda").html(resultado);
					$("#titulo_busqueda").css("display","block");
					$("#cabeceraBusqueda").css("display","block");
					$("#pag_busqueda").css("display", "block");
				}
			});			
		}
	}
	
	function seleccionarEstudiante(id_estudiante,es_apellidos,es_nombres)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		var matricular = confirm("¿Seguro que desea matricular el estudiante ["+es_apellidos+" "+es_nombres+"]?")
		if (matricular) {
			$("#img-loader-busqueda").show();
			$.post("matriculacion/seleccionar_estudiante.php", 
				{ 
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					es_apellidos: es_apellidos,
					es_nombres: es_nombres
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#img-loader-busqueda").hide();
						// Mensaje de salida
						$("#mensaje").html(resultado);
						contarEstudiantesParalelo(id_paralelo);
						$("#formulario_busqueda").css("display", "none");
						$("#pag_busqueda").css("display", "none");
						document.getElementById("lista_busqueda").innerHTML = "";
					}
				}
			);
		}
	}
	
	function actualizar_estado_retirado(obj, id_estudiante)
	{
        if(obj.checked) estado_retirado = "S";
        else estado_retirado = "N";
		$.ajax({
				type: "POST",
				url: "matriculacion/actualizar_estado_retirado.php",
				data: "id_estudiante="+id_estudiante+"&es_retirado="+estado_retirado,
				success: function(resultado){
					// No desplega nada... esto es solo para ejecutar el codigo php
			  }
		});			
	}
</script>
</head>

<body>
<div id="pagina">
	<input type="hidden" id="id_estudiante" name="id_estudiante" />
	<div id="titulo_pagina">
    	<?php echo "MATRICULACION DE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
            <td> <select id="cboParalelos" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="estudiante_nuevo" class="boton" style="display:block"> <a href="#"> Estudiante Nuevo </a> </div> </td>
            <td> <div id="estudiante_antiguo" class="boton" style="display:block"> <a href="#"> Estudiante Antiguo </a> </div> </td>
			<td> <div id="datos_representante" class="boton" style="display:none"> <a href="#"> Datos del Representante </a> </div> </td>
			<td> <div id="datos_estudiante" class="boton" style="display:none"> <a href="#"> Datos del Estudiante </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">ESTUDIANTE NUEVO</div>
      <div id="frmNuevo" align="left">
          <form id="form_nuevo" action="" method="post">
              <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                  <tr>
                      <td width="5%" align="right">C&eacute;dula:</td>
                      <td width="15%" align="left">
                      	  <input id="es_cedula" type="text" class="cajaGrande" name="es_cedula" maxlength="10" />
                      </td>
                      <td width="10%" align="right">Fecha de Nacimiento:</td>
                      <td width="15%" align="left">
                          <input id="es_fec_nac" type="text" class="cajaPequenia" name="es_fec_nac" maxlength="10" />
                          <img src="imagenes/calendario.png" id="calendario" name="calendario" width="16px" height="16px" title="calendario" alt="calendario" onmouseover="style.cursor=cursor" />
                          <script type="text/javascript">
                                Calendar.setup(
                                        {
                                        inputField : "es_fec_nac",
                                        ifFormat   : "%Y-%m-%d",
                                        button     : "calendario"
                                        }
                                );
                          </script>
                      </td>
                      <td width="5%" align="right">Edad:</td>
                      <td width="*" align="left">
                          <input type="text" id="es_edad" name="es_edad" disabled value="" class="cajaPequenia" />
                      </td>
                  </tr>
                  <tr>
                      <td width="5%" align="right">Apellidos:</td>
                      <td width="15%">
                          <input id="es_apellidos" type="text" class="cajaGrande" name="es_apellidos" maxlength="40" />
                      </td>
                      <td width="10%" align="right">Direccion:</td>
                      <td colspan="3">
                          <input id="es_direccion" type="text" class="cajaExtraGrande" name="es_direccion" maxlength="64" />
                      </td>
                  </tr>
                  <tr>
                      <td width="5%" align="right">Nombres:</td>
                      <td width="15%">
                          <input id="es_nombres" type="text" class="cajaGrande" name="es_nombres" maxlength="40" />
                      </td>
                      <td width="10%" align="right">Sector:</td>
                      <td colspan="3">
                          <input id="es_sector" type="text" class="cajaExtraGrande" name="es_sector" maxlength="64" />
                      </td>
                  </tr>
                  <tr>
                      <td width="5%" align="right">Email:</td>
                      <td width="15%">
                          <input id="es_email" type="text" class="cajaGrande" name="es_email" maxlength="40" />
                      </td>
                      <td width="10%" align="right">Tel&eacute;fono:</td>
                      <td colspan="3">
                          <input id="es_telefono" type="text" class="cajaExtraGrande" name="es_telefono" maxlength="64" />
                      </td>
                  </tr>
                  <tr>
                      <td width="5%" align="right">G&eacute;nero:</td>
                      <td width="15%">
                          <div id="divGenero">
                              <input name="Genero" type="radio" value="M" checked />&nbsp;Masculino&nbsp;
                              <input name="Genero" type="radio" value="F" />&nbsp;Femenino
                          </div>
                      </td>
                      <td width="10%" align="right">
                          <div id="lblNuevoParalelo">
                              Nuevo Paralelo:
                          </div>
                      </td>
                      <td colspan="3">
                          <div id="comboNuevoParalelo">
                              <select id="cboNuevoParalelo" class="fuente9"> <option value="0">Seleccione...</option> </select>
                          </div>
                      </td>
                  </tr>
                  <tr>
                      <td colspan="8">
                          <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                              <tr>
                                  <td width="15%" align="right">
                                      <div id="boton_accion">
                                          <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                                      </div>   
                                  </td>
                                  <td width="15%" align="right">
                                      <div class="link_form"><a href="#" onclick="limpiarEstudiante()">Limpiar</a></div>
                                  </td>
                                  <td width="15%" align="right">
                                      <div class="link_form"><a href="#" onclick="salirEstudiante()">Salir</a></div>
                                  </td>
                                  <td width="*">
                                      <div id="img-loader" style="padding-left:2px"></div>
                                  </td>
                              </tr>
                          </table>
                      </td>
                  </tr>
              </table>
          </form>
      </div>
    </div>
	<div id="formulario_representante" style="display:none">
		<div id="tituloForm" class="header">DATOS DEL REPRESENTANTE</div>
		<div id="frmNuevo" align="left">
			<form id="form_representante" action="" method="post">
				<table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
					<tr>
						<td width="5%" align="right">C&eacute;dula:</td>
						<td width="15%" align="left">
                      	  <input id="re_cedula" type="text" class="cajaGrande" name="re_cedula" maxlength="10" />
                        </td>
						<td width="10%" align="right">Parentesco:</td>
						<td width="*" align="left">
                      	  <input id="re_parentesco" type="text" class="cajaGrande" name="re_parentesco" maxlength="20" />
                        </td>
					</tr>
					<tr>
						<td width="5%" align="right">Apellidos:</td>
						<td width="15%" align="left">
                      	  <input id="re_apellidos" type="text" class="cajaGrande" name="re_apellidos" maxlength="40" />
                        </td>
						<td width="10%" align="right">Nombres:</td>
						<td width="*" align="left">
                      	  <input id="re_nombres" type="text" class="cajaGrande" name="re_nombres" maxlength="40" />
                        </td>
					</tr>
					<tr>
						<td width="5%" align="right">Direcci&oacute;n:</td>
						<td width="15%" align="left">
                      	  <input id="re_direccion" type="text" class="cajaGrande" name="re_direccion" maxlength="10" />
                        </td>
						<td width="10%" align="right">Sector:</td>
						<td width="*" align="left">
                      	  <input id="re_sector" type="text" class="cajaGrande" name="re_sector" maxlength="10" />
                        </td>
					</tr>
					<tr>
						<td width="5%" align="right">E-mail:</td>
						<td width="15%" align="left">
                      	  <input id="re_email" type="text" class="cajaGrande" name="re_email" maxlength="10" />
                        </td>
						<td width="10%" align="right">Tel&eacute;fono:</td>
						<td width="*" align="left">
                      	  <input id="re_telefono" type="text" class="cajaGrande" name="re_telefono" maxlength="10" />
                        </td>
					</tr>
					<tr>
                      	<td width="5%" align="right">&nbsp;</td>
                      	<td width="15%">
							<input type="checkbox" id="auto_representado"> 
							<label for="auto_representado"> Auto Representado </label>
                      	</td>
					  	<td width="10%" align="right">Observaci&oacute;n:</td>
						<td width="*" align="left">
                      	  <input id="re_observacion" type="text" class="cajaGrande" name="re_observacion" maxlength="10" />
                        </td>
					</tr>
					<tr>
						<td colspan="2" width="25%" align="right">
							<div class="link_form">
								&nbsp;<a id="btnEditarRepresentante" href="#" onclick="editarRepresentante()">Editar</a>
							</div>
						</td>
						<td widht="*" align="left">
							<div class="link_form">
								&nbsp;<a href="#" onclick="salirRepresentante()">Salir</a>
							</div>
						</td>
					</tr>
				</table>
				<input type="hidden" id="id_representante" name="id_representante" />
			</form>
		</div>
	</div>
    <div id="formulario_busqueda">
      <div id="tituloBusqueda" class="header">BUSCAR ESTUDIANTE</div>
      <div id="frmBusqueda" align="left">
   	     <form id="form_busqueda" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Ingrese Patr&oacute;n de B&uacute;squeda:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_patron" type="text" class="cajaGrande" style="text-transform:uppercase" name="txt_patron" maxlength="40" />
                  </td>
				  <td width="5%">
                  	 <div id="buscar_estudiante" class="link_form"><a href="#" onclick="buscarEstudiantes()">Buscar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="limpiarBusqueda()">Limpiar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="salirBusqueda(true)">Salir</a></div>
                  </td>
                  <td width="*">
                     <div id="img-loader-busqueda" style="padding-left:2px">
                     	<img src="imagenes/ajax-loader.gif" alt="procesando..." />
                     </div>
                  </td>
               </tr>
            </table>
         </form>
      </div>   
    </div>
    <div id="pag_busqueda">
    	<!-- Aqui val la paginacion de los estudiantes encontrados en la busqueda -->
        <div id="titulo_busqueda" class="header2" style="display:none"> LISTA DE ESTUDIANTES ENCONTRADOS </div> 
        <div id="cabeceraBusqueda" class="cabeceraTabla" style="display:none">
           <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
              <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="18%" align="left">Apellidos</td>
                <td width="18%" align="left">Nombres</td>
                <td width="18%" align="left">Curso</td>
                <td width="18%" align="left">Paralelo</td>
                <td width="9%" align="center">Aprobado</td>
                <td width="9%" align="center">Acciones</td>
              </tr>
           </table>
        </div>
        <div id="lista_busqueda">
            <!-- Aqui va el resultado de la busqueda de estudiantes antiguos -->
        </div>
    </div>
	<div id="mensaje" class="error" style="text-align:center"></div>
    <div id="pag_nomina_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="total_registros_estudiantes" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes encontrados:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_estudiantes"> 
                    	<!-- Aqui va la paginacion de estudiantes --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div id="pag_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div class="header2"> LISTA DE ESTUDIANTES MATRICULADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="18%" align="left">Apellidos</td>
                <td width="18%" align="left">Nombres</td>
                <td width="12%" align="left">C&eacute;dula</td>
                <td width="12%" align="left">Tel&eacute;fono</td>
                <td width="12%" align="left">Email</td>
                <td width="8%" align="center">Retirado</td>
                <td width="10%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_estudiantes" style="text-align:center">
      </div>
   </div>
</div>
</body>
</html>