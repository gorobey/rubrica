<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarInasistencias();	    
		$("#nueva_inasistencia").click(function(e){
			e.preventDefault();
			nuevaInasistencia();
		});
		$("#limpiarInasistencia").click(function(e){
			e.preventDefault();
			limpiarInasistencia();
		});
	});
	
	function listarInasistencias()
	{
		$.get("inasistencias/listar_inasistencias.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("No se han definido Inasistencias...");
				}
				else
				{
					$("#lista_inasistencias").html(resultado);
				}
			}
		);
	}

	function nuevaInasistencia()
	{
		limpiarInasistencia();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarInasistencia()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("in_nombre").focus();
	}

	function limpiarInasistencia()
	{
		document.getElementById("in_nombre").value="";
		document.getElementById("in_abreviatura").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("in_nombre").focus();
	}

	function salirInasistencia(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("nueva_inasistencia").focus();
	}

	function insertarInasistencia()
	{
		// Validación de la entrada de datos
		var in_nombre = document.getElementById("in_nombre").value;
		var in_abreviatura = document.getElementById("in_abreviatura").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		in_nombre=eliminaEspacios(in_nombre);
		in_abreviatura=eliminaEspacios(in_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,32})$/i;
		
    	if(!reg_texto.test(in_nombre)) {
			var mensaje = "El nombre de la Inasistencia debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("in_nombre").focus();
    	} else if(in_abreviatura=="") {
			var mensaje = "Debe ingresar la abreviatura de la inasistencia, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("in_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "inasistencias/insertar_inasistencia.php",
					data: "in_nombre="+in_nombre+"&in_abreviatura="+in_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarInasistencias();
						salirInasistencia(false);
				  }
			});
		}
	}

	function actualizarInasistencia()
	{
		// Validación de la entrada de datos
		var id_inasistencia = document.getElementById("id_inasistencia").value;
		var in_nombre = document.getElementById("in_nombre").value;
		var in_abreviatura = document.getElementById("in_abreviatura").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		in_nombre=eliminaEspacios(in_nombre);
		in_abreviatura=eliminaEspacios(in_abreviatura);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,32})$/i;
		
    	if(!reg_texto.test(in_nombre)) {
			var mensaje = "El nombre de la inasistencia debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("in_nombre").focus();
    	} else if(in_abreviatura=="") {
			var mensaje = "Debe ingresar la abrevaitura de la inasistencia, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("in_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "inasistencias/actualizar_inasistencia.php",
					data: "id_inasistencia="+id_inasistencia+"&in_nombre="+in_nombre+"&in_abreviatura="+in_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarInasistencias();
						salirInasistencia(false);
				  }
			});
		}
	}

	function editarInasistencia(id_inasistencia)
	{
		limpiarInasistencia();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR INASISTENCIA");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarInasistencia()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "inasistencias/obtener_inasistencia.php",
				data: "id_inasistencia="+id_inasistencia,
				success: function(resultado){
					var JSONInasistencia = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el periodo de evaluacion elegido
					document.getElementById("id_inasistencia").value=JSONInasistencia.id_inasistencia;
					document.getElementById("in_nombre").value=JSONInasistencia.in_nombre;
					document.getElementById("in_abreviatura").value=JSONInasistencia.in_abreviatura;

					$("#formulario_nuevo").css("display", "block");
					document.getElementById("in_nombre").focus();
			  }
		});
	}
	
	function eliminarInasistencia(id_inasistencia)
	{
		// Validación de la entrada de datos
		
		if (id_inasistencia==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_inasistencia...");
			salirInasistencia(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar esta inasistencia?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "inasistencias/eliminar_inasistencia.php",
						data: "id_inasistencia="+id_inasistencia,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarInasistencia();
							salirInasistencia(false);
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
            <td> <div id="nueva_inasistencia" class="boton"> <a href="#"> Nueva Inasistencia </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
    	<div id="tituloForm" class="header">Nueva Inasistencia</div>
        <div id="frmNuevo" align="left">
        	<form id="form_nuevo" action="" method="post">
            	<table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                	<tr>
                        <td width="15%" align="right">Nombre:</td>
                        <td width="*">
                        	<input id="in_nombre" type="text" class="cajaMedia" name="in_nombre" maxlength="40" />
                        </td>
					</tr>
                    <tr>   
                      	<td width="15%" align="right">Abreviatura:</td>
                      	<td width="*">
                         	<input id="in_abreviatura" type="text" class="cajaPequenia" name="in_abreviatura" maxlength="10" />
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
                              			<div id="limpiarInasistencia" class="link_form"><a href="#">Limpiar</a></div>
                           			</td>
                           			<td width="5%" align="right">
                              			<div class="link_form"><a href="#" onclick="salirInasistencia()">Salir</a></div>
                           			</td>
                           			<td width="*">
                              			<div id="img-loader" style="padding-left:2px"></div>
                           			</td>
                        		</tr>
                     		</table>
                  		</td>
               		</tr>     
                </table>
            	<input type="hidden" id="id_inasistencia" name="id_inasistencia" />
            </form>
        </div>
    </div>
   	<div id="mensaje" class="error"></div>
	<div id="pag_inasistencia">
      <!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
      <div class="header2"> LISTA DE INASISTENCIAS EXISTENTES </div>
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
      <div id="lista_inasistencias" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
