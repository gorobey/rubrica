<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;bricas Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_perfiles();
		$("#nuevo_submenu").hide();
		$("#cboPerfiles").change(function(){obtenerNiveles();});
		$("#cboNiveles").change(function(){obtenerMenus();});
		$("#cboMenus").change(function(){listarSubmenus();});
		$("#nuevo_submenu").click(function(e){
			e.preventDefault();
			nuevoSubmenu();
		});		
		$("#limpiarMenu").click(function(e){
			e.preventDefault();
			limpiarMenu();
		});
	});

	function cargar_perfiles()
	{
		$.get("scripts/cargar_perfiles.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPerfiles').append(resultado);
			}
		});	
	}

	function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
	}

	function obtenerNiveles()
	{
		var id_perfil = document.getElementById("cboPerfiles").value;
		document.getElementById("cboNiveles").options.length=1;
		$("#lista_menus").html("Debe elegir un nivel...");
		if (id_perfil == 0) {
			$("#lista_menus").html("Debe elegir un perfil...");
			$("#nuevo_submenu").css("display","none");
		} else {
			$.get("submenu/cargar_niveles.php", { id_perfil: id_perfil },
				function(resultado)
				{
					if(resultado == false)
					{
						$("#lista_menus").html("No se han definido menus para este perfil...");
					}
					else
					{
						$('#cboNiveles').append(resultado);			
					}
				}
			);
		}
	}

	function obtenerMenus()
	{
		var nivel = document.getElementById("cboNiveles").value;
		var id_perfil = document.getElementById("cboPerfiles").value;
		document.getElementById("cboMenus").options.length=1;

		if (id_perfil == 0) {
			$("#lista_menus").html("Debe elegir un perfil...");
			$("#nuevo_menu").css("display","none");			
		} else if (nivel == 0) {
			$("#lista_menus").html("Debe elegir un nivel...");
			$("#nuevo_menu").css("display","none");
		} else {
			$("#nuevo_submenu").css("display","block");
			$.get("submenu/cargar_menus_nivel.php", 
				{ 
					nivel: nivel,
					id_perfil: id_perfil
				},
				function(resultado)
				{
					if(resultado == false)
					{
						$("#lista_menus").html("No se han definido menus para el nivel y perfil requerido...");
					}
					else
					{
						$('#cboMenus').append(resultado);			
					}
				}
			);
		}
	}
	
	function listarSubmenus()
	{
		var code = $("#cboMenus").val();
		var mnu_nivel = $("#cboNiveles").val();
		var id_perfil = $("#cboPerfiles").val();
		if (code == 0) {
			$("#lista_menus").html("Debe elegir un men&uacute;...");
			$("#nuevo_submenu").css("display","none");
			$("#pag_submenus").css("display","none");
		} else {
			$("#nuevo_submenu").css("display","block");
			$("#pag_submenus").css("display","block");
     		$.get("submenu/listar_submenus.php", 
				{ 
					code: code,
					id_perfil: id_perfil,
					mnu_nivel: mnu_nivel
				},
				function(resultado)
				{
					if(resultado == false)
					{
						$("#lista_submenus").html("No se han definido submenus para este menu...");
					}
					else
					{
						$('#lista_submenus').html(resultado);			
					}
				}
			);
		}
	}
	
	function editarSubmenu(id_submenu)
	{
		limpiarMenu();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR SUBMENU");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarSubmenu()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.ajax({
				type: "POST",
				url: "submenu/obtener_submenu.php",
				data: "id_menu="+id_submenu,
				success: function(resultado){
					var JSONMenu = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el submenu elegido
					document.getElementById("id_submenu").value=JSONMenu.id_menu;
					document.getElementById("mnu_texto").value=JSONMenu.mnu_texto;
					document.getElementById("mnu_enlace").value=JSONMenu.mnu_enlace;
					document.getElementById("mnu_orden").value=JSONMenu.mnu_orden;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("mnu_texto").focus();
			  }
		});			
	}

	function nuevoSubmenu()
	{
		limpiarMenu();
		$("#tituloForm").html("Nuevo Submenu");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarSubmenu()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("mnu_texto").focus();
	}

	function salirMenu(iTodo)
	{
		var css_display = (iTodo) ? "none" : "block";
		$("#mensaje").css("display", css_display);
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_submenu").focus();
	}

	function actualizarSubmenu()
	{
		// Validación de la entrada de datos
		var id_menu = document.getElementById("cboMenus").value;
		var id_submenu = document.getElementById("id_submenu").value;
		var sbmnu_texto = document.getElementById("mnu_texto").value;
		var sbmnu_enlace = document.getElementById("mnu_enlace").value;
		var sbmnu_orden = document.getElementById("mnu_orden").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		sbmnu_texto=eliminaEspacios(sbmnu_texto);
		sbmnu_enlace=eliminaEspacios(sbmnu_enlace);		
		sbmnu_orden=eliminaEspacios(sbmnu_orden);		

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_digit = /^([0-9]{1,2})$/i;
		
		if (id_submenu==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_submenu...");
			document.getElementById("cboMenus").focus();
		} else if (id_menu==0) {
			var mensaje = "Debe escoger el menu...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboMenus").focus();
    	} else if(!reg_texto.test(sbmnu_texto)) {
			var mensaje = "El texto del men&uacute; debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("mnu_texto").focus();
		} else if(!reg_digit.test(sbmnu_orden)) {
			var mensaje = "El orden del men&uacute; debe contener al menos un caracter num&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("mnu_orden").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "submenu/actualizar_submenu.php",
					data: "id_menu="+id_submenu+"&mnu_texto="+sbmnu_texto+"&mnu_enlace="+sbmnu_enlace+"&mnu_orden="+sbmnu_orden,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarSubmenus();
						salirMenu(false);
				  }
			});			
		}	
	}

	function eliminarMenu(id_menu)
	{
		// Validación de la entrada de datos
		
		if (id_menu==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_menu...");
			document.getElementById("cboPerfiles").focus();
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este menu?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "menu/eliminar_menu.php",
						data: "id_menu="+id_menu,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarMenus();
							salirMenu(false);
					  }
				});			
			}
		}	
	}

	function eliminarSubmenu(id_submenu)
	{
		// Validación de la entrada de datos
		
		if (id_submenu==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_submenu...");
			document.getElementById("cboSubmenus").focus();
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este submenu?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "menu/eliminar_menu.php",
						data: "id_menu="+id_submenu,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarSubmenus();
							salirMenu(false);
					  }
				});			
			}
		}	
	}

	function limpiarMenu()
	{
		document.getElementById("mnu_texto").value="";
		document.getElementById("mnu_enlace").value="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mnu_texto").focus();
	}

	function insertarSubmenu()
	{
		// Validación de la entrada de datos
		var mnu_padre = document.getElementById("cboMenus").value;
		var id_perfil = document.getElementById("cboPerfiles").value;
		var mnu_texto = document.getElementById("mnu_texto").value;
		var mnu_enlace = document.getElementById("mnu_enlace").value;
		var mnu_nivel = document.getElementById("cboNiveles").value;

		// Saco los espacios en blanco al comienzo y al final de la cadena
		mnu_texto=eliminaEspacios(mnu_texto);
		mnu_enlace=eliminaEspacios(mnu_enlace);		

		var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		var reg_digit = /^([0-9]{1,2})$/i;
		
		if (mnu_padre==0) {
			var mensaje = "Debe escoger el men&uacute;...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboMenus").focus();
		} else if (id_perfil==0) {
			var mensaje = "Debe escoger el perfil...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboPerfiles").focus();
		} else if (mnu_nivel==0) {
			var mensaje = "Debe escoger el nivel...";
			$("#mensaje").html(mensaje);
			document.getElementById("cboNiveles").focus();
    	} else if(!reg_texto.test(mnu_texto)) {
			var mensaje = "El texto del submen&uacute; debe contener al menos cuatro caracteres alfanum&eacute;ricos";
			$("#mensaje").html(mensaje);
			document.getElementById("mnu_texto").focus();
    	} else if(mnu_enlace=="") {
			var mensaje = "El enlace del submen&uacute; debe contener al menos un caracter alfanum&eacute;rico";
			$("#mensaje").html(mensaje);
			document.getElementById("mnu_enlace").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "submenu/insertar_submenu.php",
					data: "id_perfil="+id_perfil+"&mnu_texto="+mnu_texto+"&mnu_enlace="+mnu_enlace+"&mnu_nivel="+mnu_nivel+"&mnu_padre="+mnu_padre,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarSubmenus();
						salirMenu(false);
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
            <td class="fuente9">&nbsp;Perfil: &nbsp;</td>
            <td> <select id="cboPerfiles" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>
            <td class="fuente9">&nbsp;Nivel: &nbsp;</td>
            <td> <select class="fuente9" id="cboNiveles"> <option value="0">Seleccione...</option> 
           </select> </td>
            <td class="fuente9">&nbsp;Men&uacute;: &nbsp;</td>
            <td> <select id="cboMenus" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>
            <td> <div id="nuevo_submenu" class="boton"> <a href="#"> Nuevo Submen&uacute; </a> </div> </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Menu</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="5%" align="right">Texto:</td>
                  <td width="*">
                     <input id="mnu_texto" type="text" class="cajaGrande" name="mnu_texto" maxlength="40" />
                  </td>
               </tr>
               <tr>
                  <td width="5%" align="right">Enlace:</td>
                  <td width="*">
                     <input id="mnu_enlace" type="text" class="cajaGrande" name="mnu_enlace" maxlength="64" />
                  </td>
               </tr>
               <tr>
                  <td width="5%" align="right">Orden:</td>
                  <td width="*">
                     <input id="mnu_orden" type="text" class="cajaPequenia" name="mnu_orden" maxlength="2" />
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="5%" align="right">
							  <div id="boton_accion">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div id="limpiarMenu" class="link_form"><a href="#">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirMenu(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_submenu" name="id_submenu" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_menus">
      <!-- Aqui va la paginacion de los menus encontrados -->
      <div class="header2"> LISTA DE SUBMENUS ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="20%" align="left">Nombre</td>
                <td width="20%" align="left">Enlace</td>
                <td width="16%" align="left">Nivel</td>
                <td width="16%" align="left">Orden</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_submenus" style="text-align:center"> Seleccione un perfil... </div>
   </div>
</div>
</body>
</html>
