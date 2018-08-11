<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#img_loader").hide();
		listarEscalas();
		$("#nueva_escala").click(function(e) {
			e.preventDefault();
			nuevaEscala();
		});		
	});

	function listarEscalas()
	{
		$.get("escalas/listar_escalas.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_escalas").html(resultado);
				}
			}
		);
	}

	function limpiarEscala()
	{
		$("input").val("");
		$("#mensaje").html("");
		$("#cualitativa").focus();
	}

	function salirEscala()
	{
		$("input").val("");
		$("#mensaje").html("");
		$("#formulario_nuevo").hide();
	}

	function editarEscala(id)
	{
		//Procedimiento para editar una nueva escala de calificacion
		document.getElementById("id_escala_calificaciones").value = id;
		$("#tituloForm").html("EDITAR ESCALA DE CALIFICACIONES");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		var html = "<div id='editarEscala' class='link_form'><a href='#' onclick='actualizarEscala()'>Actualizar</a></div>"; 
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "escalas/obtener_escala.php",
				data: "id="+id,
				success: function(resultado){
					var JSONPeriodoLectivo = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar la escala de calificacion
					document.getElementById("id_escala_calificaciones").value=JSONPeriodoLectivo.id_escala_calificaciones;
					document.getElementById("ec_cualitativa").value=JSONPeriodoLectivo.ec_cualitativa;
					document.getElementById("ec_cuantitativa").value=JSONPeriodoLectivo.ec_cuantitativa;
					document.getElementById("ec_nota_minima").value=JSONPeriodoLectivo.ec_nota_minima;
					document.getElementById("ec_nota_maxima").value=JSONPeriodoLectivo.ec_nota_maxima;
					document.getElementById("ec_equivalencia").value=JSONPeriodoLectivo.ec_equivalencia;
					document.getElementById("ec_orden").value=JSONPeriodoLectivo.ec_orden;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("ec_cualitativa").focus();
			  }
		});			
	}

	function actualizarEscala()
	{	
		//Procedimiento para actualizar una escala de calificacion
		var id = $("#id_escala_calificaciones").val();
		var cualitativa = eliminaEspacios($("#ec_cualitativa").val());
		var cuantitativa = eliminaEspacios($("#ec_cuantitativa").val());
		var minima = eliminaEspacios($("#ec_nota_minima").val());
		var maxima = eliminaEspacios($("#ec_nota_maxima").val());
		var equivalencia = eliminaEspacios($("#ec_equivalencia").val());
		var orden = eliminaEspacios($("#ec_orden").val());
		
		//Validaciones necesarias
		if(cualitativa=="") {
			$("#mensaje").html("Debe ingresar la descripci&oacute;n de la escala cualitativa...");
			$("#ec_cualitativa").focus();
		} else if(cuantitativa=="") {
			$("#mensaje").html("Debe ingresar la descripci&oacute;n de la escala cuantitativa...");
			$("#ec_cuantitativa").focus();
		} else if(minima == "") {
			$("#mensaje").html("Debe ingresar un valor para la nota m&iacute;nima...");
			$("#ec_nota_minima").focus();
		} else if(maxima == "") {
			$("#mensaje").html("Debe ingresar un valor para la nota m&aacute;xima...");
			$("#ec_nota_maxima").focus();
		} else if(equivalencia == "") {
			$("#mensaje").html("Debe ingresar la equivalencia...");
			$("#ec_equivalencia").focus();
		} else if(parseFloat(orden) == 0) {
			$("#mensaje").html("Debe ingresar un valor para el orden...");
			$("#ec_orden").focus();
		} else if(parseFloat(minima) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_nota_minima").focus();
		} else if(parseFloat(maxima) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_nota_maxima").focus();
		} else if(parseFloat(orden) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_orden").focus();
		} else {
			//OK aqui toca ingresar la nueva escala de calificacion
			
			$("#mensaje").html("");
			$("#img_loader").show();
			
			$.post("escalas/actualizar_escala.php", 
				{ 
					id: id,
					cualitativa: cualitativa,
					cuantitativa: cuantitativa,
					minima: minima,
					maxima: maxima,
					equivalencia: equivalencia,
					orden: orden
				},
				function(resultado)
				{
					$("#img_loader").hide();
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#mensaje").html(resultado);
						listarEscalas();
						$("input").val("");
						$("#formulario_nuevo").hide();
					}
				}
			);
		}
	}

	function eliminarEscala(id, cualitativa)
	{
		// Validación de la entrada de datos
		
		if (id==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_escala_calificaciones...");
			salirEscala();
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar la escala [" + cualitativa + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "escalas/eliminar_escala.php",
						data: "id="+id,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarEscalas();
							salirEscala();
					  }
				});			
			}
		}	
	}

	function nuevaEscala()
	{
		//Procedimiento para ingresar una nueva escala de calificacion
		var html = "<div id='insertarEscala' class='link_form'><a href='#' onclick='insertarEscala()'>Insertar</a></div>"; 
		$("#boton_accion").html(html);
		$("#mensaje").html("");
		$("#formulario_nuevo").show();
		$("#ec_cualitativa").focus();
	}

	function insertarEscala()
	{	
		//Procedimiento para ingresar una nueva escala de calificacion
		var cualitativa = eliminaEspacios($("#ec_cualitativa").val());
		var cuantitativa = eliminaEspacios($("#ec_cuantitativa").val());
		var minima = eliminaEspacios($("#ec_nota_minima").val());
		var maxima = eliminaEspacios($("#ec_nota_maxima").val());
		var equivalencia = eliminaEspacios($("#ec_equivalencia").val());
		var orden = eliminaEspacios($("#ec_orden").val());
		
		//Validaciones necesarias
		if(cualitativa=="") {
			$("#mensaje").html("Debe ingresar la descripci&oacute;n de la escala cualitativa...");
			$("#ec_cualitativa").focus();
		} else if(cuantitativa=="") {
			$("#mensaje").html("Debe ingresar la descripci&oacute;n de la escala cuantitativa...");
			$("#ec_cuantitativa").focus();
		} else if(minima == "") {
			$("#mensaje").html("Debe ingresar un valor para la nota m&iacute;nima...");
			$("#ec_nota_minima").focus();
		} else if(maxima == "") {
			$("#mensaje").html("Debe ingresar un valor para la nota m&aacute;xima...");
			$("#ec_nota_maxima").focus();
		} else if(parseFloat(orden) == 0) {
			$("#mensaje").html("Debe ingresar un valor para el orden...");
			$("#ec_orden").focus();
		} else if(parseFloat(minima) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_nota_minima").focus();
		} else if(parseFloat(maxima) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_nota_maxima").focus();
		} else if(parseFloat(orden) < 0) {
			$("#mensaje").html("Debe ingresar un valor mayor o igual a cero...");
			$("#ec_orden").focus();
		} else if(equivalencia == "") {
			$("#mensaje").html("Debe ingresar la equivalencia...");
			$("#ec_equivalencia").focus();
		} else {
			//OK aqui toca ingresar la nueva escala de calificacion
			
			$("#mensaje").html("");
			$("#img_loader").show();
			
			$.post("escalas/insertar_escala.php", 
				{ 
					cualitativa: cualitativa,
					cuantitativa: cuantitativa,
					minima: minima,
					maxima: maxima,
                                        equivalencia: equivalencia,
					orden: orden
				},
				function(resultado)
				{
					$("#img_loader").hide();
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#mensaje").html(resultado);
						listarEscalas();
						$("input").val("");
						$("#formulario_nuevo").hide();
					}
				}
			);
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
            <td> <div id="nueva_escala" class="boton" style="display:block"> <a href="#"> Nueva Escala </a> </div> </td>
         </tr>
      </table>
    </div>
	<div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nueva Escala de Calificaci&oacute;n</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Escala Cualitativa:</td>
                  <td width="*">
                     <input id="ec_cualitativa" type="text" class="cajaExtraGrande" name="ec_cualitativa" maxlength="80" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Escala Cuantitativa:</td>
                  <td width="*">
                     <input id="ec_cuantitativa" type="text" class="cajaExtraGrande" name="ec_cuantitativa" maxlength="80" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Nota M&iacute;nima:</td>
                  <td width="*">
                     <input id="ec_nota_minima" type="text" class="cajaPequenia" name="ec_nota_minima" maxlength="5" onkeypress="return permite(event,'num')" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Nota M&aacute;xima:</td>
                  <td width="*">
                     <input id="ec_nota_maxima" type="text" class="cajaPequenia" name="ec_nota_maxima" maxlength="5" onkeypress="return permite(event,'num')" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Equivalencia:</td>
                  <td width="*">
                     <input id="ec_equivalencia" type="text" class="cajaPequenia" name="ec_equivalencia" maxlength="1" />
                  </td>
               </tr>
            <tr>
                  <td width="15%" align="right">Orden:</td>
                  <td width="*">
                     <input id="ec_orden" type="text" class="cajaPequenia" name="ec_orden" maxlength="1" onkeypress="return permite(event,'num')" />
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
                              <div class="link_form"><a href="#" onclick="limpiarEscala()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirEscala()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_escala_calificaciones" name="id_escala_calificaciones" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error" align="center"></div>
   <div id="img_loader"> 
     <div align="center"><img src="imagenes/ajax-loader-min.GIF" alt="Procesando..." /> </div>
   </div>
<div id="paginacion_escalas">
      <!-- Aqui va la paginacion de los periodos lectivos encontrados -->
      <div class="header2"> LISTA DE ESCALAS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">NRO.</td>
                <td width="5%">ID.</td>
                <td width="18%" align="left">CUALITATIVA</td>
                <td width="18%" align="left">CUANTITATIVA</td>
                <td width="18%" align="left">MINIMA</td>
                <td width="18%" align="left">MAXIMA</td>
                <td width="18%" align="center">ACCIONES</td>
            </tr>
        </table>
	  </div>
      <div id="lista_escalas" style="text-align:center"> </div>
    </div>
</div>
</body>
</html>
