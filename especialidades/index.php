<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_tipos_educacion();
		listarEspecialidades(false);	    
		$("#nueva_especialidad").click(function(e){
			e.preventDefault();
			nuevaEspecialidad();
		});
		$("#cboTipoEducacion").change(function(){
			listarEspecialidades(false);
		});		
	});

	function cargar_tipos_educacion()
	{
		$.get("scripts/cargar_tipos_educacion.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboTipoEducacion').append(resultado);			
			}
		});	
	}

	function listarEspecialidades(iDesplegar)
	{
		var id_tipo_educacion = document.getElementById("cboTipoEducacion").value;
		$.get("especialidades/listar_especialidades.php", { id_tipo_educacion: id_tipo_educacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_especialidades").html(resultado);
				}
			}
		);
	}

	function limpiarEspecialidad()
	{
		document.getElementById("es_nombre").value="";
		document.getElementById("es_figura").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("es_nombre").focus();
	}

	function salirEspecialidad()
	{
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nueva_especialidad").focus();
	}

	function nuevaEspecialidad()
	{
		limpiarEspecialidad();
		$("#tituloForm").html("NUEVA ESPECIALIDAD");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarEspecialidad()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("es_nombre").focus();
	}

	function insertarEspecialidad()
	{
		// Validación de la entrada de datos
		var id_tipo_educacion = document.getElementById("cboTipoEducacion").value;
		var es_nombre = document.getElementById("es_nombre").value;
		var es_figura = document.getElementById("es_figura").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		es_nombre=eliminaEspacios(es_nombre);
		es_figura=eliminaEspacios(es_figura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (id_tipo_educacion==0) {
			$("#mensaje").html("Debe escoger el tipo de educaci&oacute;n...");
			document.getElementById("cboTipoEducacion").focus();
                } else if(es_nombre.length==0) {
			$("#mensaje").html("El nombre de la especialidad es obligatorio");
			document.getElementById("es_nombre").focus();
		} else if(!reg_texto.test(es_nombre)) {
			$("#mensaje").html("El nombre de la especialidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombre").focus();
                } else if(es_figura.length==0) {
			$("#mensaje").html("La figura de la especialidad es obligatoria");
			document.getElementById("es_figura").focus();
		} else if(!reg_texto.test(es_nombre)) {
			$("#mensaje").html("El nombre de la especialidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "especialidades/insertar_especialidad.php",
					data: "id_tipo_educacion="+id_tipo_educacion+"&es_nombre="+es_nombre+"&es_figura="+es_figura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarEspecialidades(true);
						salirEspecialidad();
				  }
			});			
		}	
	}

	function actualizarEspecialidad()
	{
		// Validación de la entrada de datos
		
		var id_especialidad = document.getElementById("id_especialidad").value;
		var id_tipo_educacion = document.getElementById("cboTipoEducacion").value;
		var es_nombre = document.getElementById("es_nombre").value;
		var es_figura = document.getElementById("es_figura").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		es_nombre=eliminaEspacios(es_nombre);
		es_figura=eliminaEspacios(es_figura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (id_tipo_educacion==0) {
			$("#mensaje").html("Debe escoger el tipo de educaci&oacute;n...");
			document.getElementById("cboTipoEducacion").focus();
                } else if(es_nombre.length==0) {
			$("#mensaje").html("El nombre de la especialidad es obligatorio");
			document.getElementById("es_nombre").focus();
		} else if(!reg_texto.test(es_nombre)) {
			$("#mensaje").html("El nombre de la especialidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombre").focus();
                } else if(es_figura.length==0) {
			$("#mensaje").html("La figura de la especialidad es obligatoria");
			document.getElementById("es_figura").focus();
		} else if(!reg_texto.test(es_nombre)) {
			$("#mensaje").html("El nombre de la especialidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("es_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "especialidades/actualizar_especialidad.php",
					data: "id_especialidad="+id_especialidad+"&id_tipo_educacion="+id_tipo_educacion+"&es_nombre="+es_nombre+"&es_figura="+es_figura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarEspecialidades(true);
						salirEspecialidad();
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

	function editarEspecialidad(id_especialidad)
	{
		limpiarEspecialidad();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR ESPECIALIDAD");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarEspecialidad()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "especialidades/obtener_especialidad.php",
				data: "id_especialidad="+id_especialidad,
				success: function(resultado){
					var JSONEspecialidad = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el usuario elegido
					document.getElementById("id_especialidad").value=JSONEspecialidad.id_especialidad;
					document.getElementById("id_tipo_educacion").value=JSONEspecialidad.id_tipo_educacion;
					document.getElementById("es_nombre").value=JSONEspecialidad.es_nombre;
					document.getElementById("es_figura").value=JSONEspecialidad.es_figura;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("es_nombre").focus();
					setearIndice("cboTipoEducacion",JSONEspecialidad.id_tipo_educacion);
			  }
		});			
	}

	function eliminarEspecialidad(id_especialidad,nombre)
	{
		// Validación de la entrada de datos
		
		if (id_especialidad==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_especialidad...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar la Especialidad [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "especialidades/eliminar_especialidad.php",
						data: "id_especialidad="+id_especialidad,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarEspecialidades(true);
							salirEspecialidad();
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
            <td class="fuente9">&nbsp;Tipo de Educaci&oacute;n: &nbsp;</td>
            <td> <select id="cboTipoEducacion" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="nueva_especialidad" class="boton" style="display:block"> <a href="#"> Nueva Especialidad </a> </div> </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nueva Especialidad</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="es_nombre" type="text" class="cajaGrande" name="es_nombre" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Figura:</td>
                  <td width="*">
                     <input id="es_figura" type="text" class="cajaGrande" name="es_figura" maxlength="50" />
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
                              <div class="link_form"><a href="#" onclick="limpiarEspecialidad()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirEspecialidad(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_especialidad" name="id_especialidad" />
            <input type="hidden" id="id_tipo_educacion" name="id_tipo_educacion" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_especialidades">
      <!-- Aqui va la paginacion de los usuarios encontrados -->
      <div class="header2"> LISTA DE ESPECIALIDADES EXISTENTES </div>
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
      <div id="lista_especialidades" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
