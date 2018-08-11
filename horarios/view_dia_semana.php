<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarDiasSemana();	    
		$("#nuevo_dia_semana").click(function(e){
			e.preventDefault();
			nuevoDiaSemana();
		});
		$("#limpiarDiaSemana").click(function(e){
			e.preventDefault();
			limpiarDiaSemana();
		});
	});
	
	function listarDiasSemana()
	{
		$.get("horarios/listar_dias_semana.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("No se han dias de la semana...");
				}
				else
				{
					$("#lista_dias_semana").html(resultado);
				}
			}
		);
	}

	function nuevoDiaSemana()
	{
		limpiarDiaSemana();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarDiaSemana()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("ds_nombre").focus();
	}

	function limpiarDiaSemana()
	{
		document.getElementById("ds_nombre").value="";
		document.getElementById("ds_ordinal").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("ds_nombre").focus();
	}

	function salirDiaSemana(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("nuevo_dia_semana").focus();
	}

	function insertarDiaSemana()
	{
		// Validación de la entrada de datos
		var ds_nombre = document.getElementById("ds_nombre").value;
		var ds_ordinal = document.getElementById("ds_ordinal").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ds_nombre=eliminaEspacios(ds_nombre);
		ds_ordinal=eliminaEspacios(ds_ordinal);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
    	if(!reg_texto.test(ds_nombre)) {
			var mensaje = "El nombre del periodo de evaluaci&oacute;n debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ds_nombre").focus();
    	} else if(ds_ordinal=="") {
			var mensaje = "Debe ingresar el orden del d&iacute;a de la semana, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("ds_ordinal").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/insertar_dia_semana.php",
					data: "ds_nombre="+ds_nombre+"&ds_ordinal="+ds_ordinal,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarDiasSemana();
						salirDiaSemana(false);
				  }
			});
		}
	}

	function actualizarDiaSemana()
	{
		// Validación de la entrada de datos
		var id_dia_semana = document.getElementById("id_dia_semana").value;
		var ds_nombre = document.getElementById("ds_nombre").value;
		var ds_ordinal = document.getElementById("ds_ordinal").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		ds_nombre=eliminaEspacios(ds_nombre);
		ds_ordinal=eliminaEspacios(ds_ordinal);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
    	if(!reg_texto.test(ds_nombre)) {
			var mensaje = "El nombre del periodo de evaluaci&oacute;n debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("ds_nombre").focus();
    	} else if(ds_ordinal=="") {
			var mensaje = "Debe ingresar el orden del d&iacute;a de la semana, no puede estar este campo vac&iacute;o";
			$("#mensaje").html(mensaje);
			document.getElementById("ds_ordinal").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/actualizar_dia_semana.php",
					data: "id_dia_semana="+id_dia_semana+"&ds_nombre="+ds_nombre+"&ds_ordinal="+ds_ordinal,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarDiasSemana();
						salirDiaSemana(false);
				  }
			});
		}
	}

	function editarDiaSemana(id_dia_semana)
	{
		limpiarDiaSemana();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR DIA DE LA SEMANA");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarDiaSemana()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "horarios/obtener_dia_semana.php",
				data: "id_dia_semana="+id_dia_semana,
				success: function(resultado){
					var JSONPeriodoEvaluacion = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el periodo de evaluacion elegido
					document.getElementById("id_dia_semana").value=JSONPeriodoEvaluacion.id_dia_semana;
					document.getElementById("ds_nombre").value=JSONPeriodoEvaluacion.ds_nombre;
					document.getElementById("ds_ordinal").value=JSONPeriodoEvaluacion.ds_ordinal;

					$("#formulario_nuevo").css("display", "block");
					document.getElementById("ds_nombre").focus();
			  }
		});			
	}
	
	function eliminarDiaSemana(id_dia_semana)
	{
		// Validación de la entrada de datos
		
		if (id_dia_semana==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_dia_semana...");
			salirDiaSemana(false);
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este dia de la semana?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "horarios/eliminar_dia_semana.php",
						data: "id_dia_semana="+id_dia_semana,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarDiasSemana();
							salirDiaSemana(false);
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
            <td> <div id="nuevo_dia_semana" class="boton"> <a href="#"> Nuevo D&iacute;a de la Semana </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
    	<div id="tituloForm" class="header">Nuevo D&iacute;a de la Semana</div>
        <div id="frmNuevo" align="left">
        	<form id="form_nuevo" action="" method="post">
            	<table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                	<tr>
                        <td width="15%" align="right">Nombre:</td>
                        <td width="*">
                        	<input id="ds_nombre" type="text" class="cajaMedia" name="ds_nombre" maxlength="40" />
                        </td>
					</tr>
                    <tr>   
                      	<td width="15%" align="right">Orden:</td>
                      	<td width="*">
                         	<input id="ds_ordinal" type="text" class="cajaPequenia" name="ds_ordinal" maxlength="10" />
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
                              			<div id="limpiarDiaSemana" class="link_form"><a href="#">Limpiar</a></div>
                           			</td>
                           			<td width="5%" align="right">
                              			<div class="link_form"><a href="#" onclick="salirDiaSemana()">Salir</a></div>
                           			</td>
                           			<td width="*">
                              			<div id="img-loader" style="padding-left:2px"></div>
                           			</td>
                        		</tr>
                     		</table>
                  		</td>
               		</tr>     
                </table>
            	<input type="hidden" id="id_dia_semana" name="id_dia_semana" />
            </form>
        </div>
    </div>
   	<div id="mensaje" class="error"></div>
	<div id="pag_dias_semana">
      <!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
      <div class="header2"> LISTA DE DIAS DE LA SEMANA EXISTENTES </div>
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
      <div id="lista_dias_semana" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
