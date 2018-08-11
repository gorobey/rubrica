<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script src="js/charCount.js"></script>
<style>
	textarea {
		width: 562px;
		height: 60px;
		font:8pt helvetica;
    	text-transform:uppercase;
    	color:#000;
    	border: 1px solid #696969;
	}
	label{
		display:block;
	}
	form div{position:relative;}
	form .counter{
		position:absolute;
		right:0;
		top:0;
		color:#000000;
	}
	form .warning{color:#600;}	
	form .exceeded{color:#e00;}	
	.div_nombre {
		text-align: left;
		padding-left: 4px;
	}
	.div_comentario {
		text-align: justify;
		padding: 0px 10px 4px 16px;
		text-transform: uppercase;
		margin-top: 2px;
	}
	.format_name {
		color: #FF0000;
		text-transform: uppercase;
	}
</style>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#formulario_ingreso").hide();
		$("#img_loader").hide();
		// Aqui va la funcion para listar los mensajes
		listarMensajes();
		$("#txt_mensaje").charCount({
			allowed: 250,		
			warning: 20,
			counterText: 'Caracteres restantes: '	
		});
		$("#nuevo_mensaje").on('click',function(){
			$("#img-loader").hide();
			$("#mensaje").html("(*)");
			$("#insertar_mensaje").html("<a href='#' onclick='insertarMensaje()'>Insertar</a>");
			$("#msg_nuevo_mensaje").slideUp();
			$("#formulario_ingreso").slideDown();
			document.getElementById("form_ingreso").reset();
			document.getElementById("txt_mensaje").focus();
		});
		$("#cancelar_mensaje").on('click',function(){
			$("#formulario_ingreso").slideUp();
			$("#msg_nuevo_mensaje").slideDown();
		});
	});
	
	function listarMensajes()
	{
		$.post("scripts/listar_mensajes.php",
			function(resp) {
				$("#listar_mensajes").html(resp);
			}
		);
	}
	
	function insertarMensaje()
	{
		var txt_mensaje = $("#txt_mensaje").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_mensaje=eliminaEspacios(txt_mensaje);
		
		if (txt_mensaje=="") {
			$("#mensaje").html("(*) Se debe ingresar el mensaje");
			$("#txt_mensaje").focus();
		} else {
			$("#img-loader").show();
			$("#mensaje_error").html("");
			// Aca utilizo Ajax para insertar el mensaje
			$.post("scripts/insertar_mensaje.php",
				{ me_texto: txt_mensaje },
				function(resp) {
					$("#formulario_ingreso").slideUp();
					$("#msg_nuevo_mensaje").slideDown();
					$("#mensaje_insercion").html(resp);
					listarMensajes();
				}
			); 
		}
	}

	function actualizarMensaje()
	{
		var id_mensaje = $("#id_mensaje").val();
		var txt_mensaje = $("#txt_mensaje").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_mensaje=eliminaEspacios(txt_mensaje);
		
		if (txt_mensaje=="") {
			$("#mensaje").html("(*) Se debe ingresar el mensaje");
			$("#txt_mensaje").focus();
		} else {
			$("#img-loader").show();
			$("#mensaje_error").html("");
			// Aca utilizo Ajax para insertar el mensaje
			$.post("scripts/actualizar_mensaje.php",
				{ 
					id_mensaje: id_mensaje,
					me_texto: txt_mensaje 
				},
				function(resp) {
					$("#formulario_ingreso").slideUp();
					$("#msg_nuevo_mensaje").slideDown();
					$("#mensaje_insercion").html(resp);
					listarMensajes();
				}
			); 
		}
	}
	
	function editarMensaje(id_mensaje)
	{
		$("#img_loader").show();
		$.post("scripts/obtener_mensaje.php",
			{ id_mensaje: id_mensaje },
			function (resultado) {
				$("#img_loader").hide();
				$("#msg_nuevo_mensaje").slideUp();
				var JSONMensaje = eval('(' + resultado + ')');
				document.getElementById("id_mensaje").value=JSONMensaje.id_mensaje;
				document.getElementById("txt_mensaje").value=JSONMensaje.me_texto;
				$("#img-loader").hide();
				$("#insertar_mensaje").html("<a href='#' onclick='actualizarMensaje()'>Actualizar</a>");
				$("#formulario_ingreso").show();
				$("#txt_mensaje").focus();
			}
		);
	}
	
	function eliminarMensaje(id_mensaje)
	{
		// Validación de la entrada de datos
		
		if (id_mensaje=="") {
			$("#mensaje").html("No se ha pasado el parámetro de id_mensaje...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar este mensaje?")
			if (eliminar) {
				$("#img_loader").show();
				$.ajax({
						type: "POST",
						url: "scripts/eliminar_mensaje.php",
						data: "id_mensaje="+id_mensaje,
						success: function(resultado){
							$("#img_loader").hide();
							$("#mensaje_insercion").html(resultado);
							$("#formulario_ingreso").slideUp();
							$("#msg_nuevo_mensaje").slideDown();
							listarMensajes();
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
    	MENSAJES DEL ADMINISTRADOR
    </div>
    <div id="msg_nuevo_mensaje" class="paginacion">
		<div id="nuevo_mensaje" class="link_form" style="text-align:left;padding-left:2px;">
        	<a href="#">A&ntilde;adir un mensaje</a>
        </div>
    </div>
    <!-- div para el ingreso del mensaje -->
	<div id="formulario_ingreso" align="left" class="form_nuevo">
      <div id="frmBusqueda" align="left">
   	     <form id="form_ingreso" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
               	  <td width="30px" align="right" valign="top">&nbsp;</td>
                  <td width="120px"> 
                     <div>
                         <label for="txt_mensaje"> Ingresar aqu&iacute; el mensaje: </label> 
                         <textarea id="txt_mensaje" name="txt_mensaje"></textarea> 
                     </div>
                  </td>
                  <td align="left" valign="top"> &nbsp;<span id="mensaje" class="error">(*)</span> </td>
               </tr>
               <tr>
                  <td colspan="4">
                      <table width="100%">
                        <tr>
                          <td width="20%" align="left">
                             <span class="error">&nbsp;(*) Campos Obligatorios</span>
                          </td>
                          <td width="20%" align="right">
                             <div id="insertar_mensaje" class="link_form"><a href="#" onclick="insertarMensaje()">Insertar</a></div>
                          </td>
                          <td width="20%" align="right">
                             <div id="cancelar_mensaje" class="link_form"><a href="#">Cancelar</a></div>
                          </td>
                          <td width="40%" align="center">
                             <div id="img-loader"><img src="imagenes/ajax-loader.gif" alt="procesando..."></div>
                             <div id="mensaje_error"> <!-- Aqui va el mensaje de error si existiere --> </div>
                          </td>
                        </tr>
                      </table>
                  </td>  
               </tr>
            </table>
            <input type="hidden" id="id_mensaje" name="id_mensaje" />
         </form>
      </div>   
   </div>
   <div id="img_loader"><img src="imagenes/ajax-loader.gif" alt="procesando..."></div>
   <div id="mensaje_insercion" class="paginacion"> <!-- Aqui va el mensaje de insercion --> </div>
   <div class="header2"> LISTA DE MENSAJES </div>
   <div id="listar_mensajes" style="text-align:center">
     <!-- Aqui van los mensajes insertados por parte del administrador -->
   </div>
</div>
</body>
</html>
