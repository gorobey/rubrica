<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_cursos();
		listarParalelos(false);	    
		$("#nuevo_paralelo").click(function(e){
			e.preventDefault();
			nuevoParalelo();
		});
		$("#cboCursos").change(function(){
			listarParalelos(false);
		});		
	});

	function cargar_cursos()
	{
		$.get("scripts/cargar_cursos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboCursos').append(resultado);			
			}
		});	
	}

	function listarParalelos(iDesplegar)
	{
		var id_curso = document.getElementById("cboCursos").value;
		$.get("paralelos/listar_paralelos.php", { id_curso: id_curso },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_paralelos").html(resultado);
				}
			}
		);
	}

	function limpiarParalelo()
	{
		document.getElementById("pa_nombre").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("pa_nombre").focus();
	}

	function salirParalelo()
	{
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_paralelo").focus();
	}

	function nuevoParalelo()
	{
		limpiarParalelo();
		$("#tituloForm").html("NUEVO PARALELO");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarParalelo()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("pa_nombre").focus();
	}

	function insertarParalelo()
	{
		// Validación de la entrada de datos
		var id_curso = document.getElementById("cboCursos").value;
		var pa_nombre = document.getElementById("pa_nombre").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pa_nombre=eliminaEspacios(pa_nombre);

		var reg_texto = /^([a-zA-Z0-9]{1,5})$/i;
		
		if (id_curso==0) {
			$("#mensaje").html("Debe escoger el curso...");
			document.getElementById("cboCursos").focus();
    	} else if(!reg_texto.test(pa_nombre)) {
			$("#mensaje").html("El nombre del paralelo debe contener al menos un caracter alfab&eacute;tico");
			document.getElementById("pa_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos/insertar_paralelo.php",
					data: "id_curso="+id_curso+"&pa_nombre="+pa_nombre,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarParalelos(true);
						salirParalelo();
				  }
			});			
		}	
	}

	function actualizarParalelo()
	{
		// Validación de la entrada de datos
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_curso = document.getElementById("cboCursos").value;
		var pa_nombre = document.getElementById("pa_nombre").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		pa_nombre=eliminaEspacios(pa_nombre);

		var reg_texto = /^([a-zA-Z0-9]{1,5})$/i;
		
		if (id_paralelo==0) {
			$("#mensaje").html("No se ha pasado el par&aacute;metro de id_paralelo...");
		} else if (id_curso==0) {
			$("#mensaje").html("Debe escoger el curso...");
			document.getElementById("cboCursos").focus();
    	} else if(!reg_texto.test(pa_nombre)) {
			$("#mensaje").html("El nombre del paralelo debe contener al menos un caracter alfab&eacute;tico");
			document.getElementById("pa_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos/actualizar_paralelo.php",
					data: "id_paralelo="+id_paralelo+"&id_curso="+id_curso+"&pa_nombre="+pa_nombre,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarParalelos(true);
						salirParalelo();
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

	function editarParalelo(id_paralelo)
	{
		limpiarParalelo();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR PARALELO");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarParalelo()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "paralelos/obtener_paralelo.php",
				data: "id_paralelo="+id_paralelo,
				success: function(resultado){
					var JSONParalelo = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el curso elegido
					document.getElementById("id_paralelo").value=JSONParalelo.id_paralelo;
					document.getElementById("id_curso").value=JSONParalelo.id_curso;
					document.getElementById("pa_nombre").value=JSONParalelo.pa_nombre;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("pa_nombre").focus();
					setearIndice("cboCursos",JSONParalelo.id_curso);
			  }
		});			
	}

	function eliminarParalelo(id_paralelo, nombre)
	{
		// Validación de la entrada de datos
		
		if (id_paralelo==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_paralelo...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Paralelo [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "paralelos/eliminar_paralelo.php",
						data: "id_paralelo="+id_paralelo,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarParalelos(true);
							salirParalelo();
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
            <td class="fuente9">&nbsp;Curso: &nbsp;</td>
            <td> <select id="cboCursos" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="nuevo_paralelo" class="boton" style="display:block"> <a href="#"> Nuevo Paralelo </a> </div> </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">NUEVO PARALELO</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="pa_nombre" type="text" class="cajaPequenia" name="pa_nombre" maxlength="40" />
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
                              <div class="link_form"><a href="#" onclick="limpiarParalelo()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirParalelo(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_paralelo" name="id_paralelo" />
            <input type="hidden" id="id_curso" name="id_curso" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_paralelos">
      <!-- Aqui va la paginacion de los paralelos encontrados -->
      <div class="header2"> LISTA DE PARALELOS EXISTENTES </div>
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
      <div id="lista_paralelos" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
