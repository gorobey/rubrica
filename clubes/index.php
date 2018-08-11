<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarClubes();	    
		$("#nuevo_club").click(function(e){
			e.preventDefault();
			nuevoClub();
		});		
	});

	function listarClubes()
	{
		$.get("clubes/listar_clubes.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_clubes").html(resultado);
				}
			}
		);
	}

	function limpiarClub()
	{
		$("input").val("");
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("cl_nombre").focus();
	}

	function salirClub(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_club").focus();
	}

	function nuevoClub()
	{
		limpiarClub();
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarClub()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("cl_nombre").focus();
	}

	function insertarClub()
	{
		// Validación de la entrada de datos
		var cl_nombre = eliminaEspacios(document.getElementById("cl_nombre").value);
		var cl_abreviatura = eliminaEspacios(document.getElementById("cl_abreviatura").value);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ\/]{6,32})$/i;
		
    	if(!reg_texto.test(cl_nombre)) {
			var mensaje = "El nombre del club debe contener al menos 6 caracteres alfab&eacute;ticos y m&aacute;ximo 32...";
			$("#mensaje").html(mensaje);
			document.getElementById("cl_nombre").focus();
		} else if(cl_abreviatura == "") {
			var mensaje = "La abreviatura del club es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("cl_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "clubes/insertar_club.php",
					data: "cl_nombre="+cl_nombre+"&cl_abreviatura="+cl_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarClubes();
						salirClub(false);
				  }
			});			
		}	
	}

	function editarClub(id_club)
	{
		limpiarClub();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR CLUB");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarClub()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "clubes/obtener_club.php",
				data: "id_club="+id_club,
				success: function(resultado){
					var JSONClub = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el club escogido
					document.getElementById("id_club").value=JSONClub.id_club;
					document.getElementById("cl_nombre").value=JSONClub.cl_nombre;
					document.getElementById("cl_abreviatura").value=JSONClub.cl_abreviatura;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("cl_nombre").focus();
			  }
		});			
	}

	function actualizarClub()
	{
		// Validación de la entrada de datos
		var id_club = document.getElementById("id_club").value;
		var cl_nombre = eliminaEspacios(document.getElementById("cl_nombre").value);
		var cl_abreviatura = eliminaEspacios(document.getElementById("cl_abreviatura").value);

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ\/]{6,32})$/i;
		
    	if(!reg_texto.test(cl_nombre)) {
			var mensaje = "El nombre del club debe contener al menos 6 caracteres alfab&eacute;ticos y m&aacute;ximo 32...";
			$("#mensaje").html(mensaje);
			document.getElementById("cl_nombre").focus();
		} else if(cl_abreviatura == "") {
			var mensaje = "La abreviatura del club es obligatoria...";
			$("#mensaje").html(mensaje);
			document.getElementById("cl_abreviatura").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "clubes/actualizar_club.php",
					data: "id_club="+id_club+"&cl_nombre="+cl_nombre+"&cl_abreviatura="+cl_abreviatura,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarClubes();
						salirClub(false);
				  }
			});
		}	
	}

	function eliminarClub(id_club,nombre)
	{
		// Validación de la entrada de datos
		
		if (id_club==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_club...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Club [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "clubes/eliminar_club.php",
						data: "id_club="+id_club,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarClubes(true);
							salirClub();
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
            <td> <div id="nuevo_club" class="boton"> <a href="#"> Nuevo Club </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Tipo De Asignatura</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="cl_nombre" type="text" class="cajaGrande" name="cl_nombre" maxlength="32" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">Abreviatura:</td>
                  <td width="*">
                     <input id="cl_abreviatura" type="text" class="cajaPequenia" name="cl_abreviatura" maxlength="6" />
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
                              <div id="limpiar_club" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirClub(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_club" name="id_club" />
         </form>
      </div>   
    </div>
    <div id="mensaje" class="error" align="center"></div>
    <div id="pag_clubes">
      <!-- Aqui va la paginacion de los clubes encontrados -->
      <div class="header2"> LISTA DE CLUBES EXISTENTES </div>
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
      <div id="lista_clubes" style="text-align:center"> </div>
    </div>
</div>
</body>
</html>
