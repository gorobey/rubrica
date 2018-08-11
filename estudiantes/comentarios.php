<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script src="../js/charCount.js"></script>
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
<script type="text/javascript">
	$(document).ready(function(){
		$("#formulario_busqueda").hide();
		// Aqui va la funcion para listar los comentarios
		listarComentarios();
		$("#txt_mensaje").charCount({
			allowed: 250,		
			warning: 20,
			counterText: 'Caracteres restantes: '	
		});
		$("#nuevo_comentario").on('click',function(){
			$("#img-loader").hide();
			$("#mensaje1").html("(*)");
			$("#mensaje2").html("(*)");
			$("#mensaje3").html("(*)");
			$("#msg_nuevo_comentario").slideUp();
			$("#formulario_busqueda").slideDown();
			document.getElementById("form_busqueda").reset();
			document.getElementById("txt_apellidos").focus();
		});
		$("#cancelar_comentario").on('click',function(){
			$("#formulario_busqueda").slideUp();
			$("#msg_nuevo_comentario").slideDown();
		});
	});
	
	function listarComentarios()
	{
		$.post("../scripts/listar_comentarios.php",
			function(resp) {
				$("#lista_comentario").html(resp);
			}
		);
	}
	
	function validar_formulario()
	{
		var txt_apellidos = $("#txt_apellidos").val();
		var txt_nombres = $("#txt_nombres").val();
		var txt_mensaje = $("#txt_mensaje").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_apellidos=eliminaEspacios(txt_apellidos);
		txt_nombres=eliminaEspacios(txt_nombres);
		txt_mensaje=eliminaEspacios(txt_mensaje);

		if (txt_apellidos=="" && txt_nombres=="" && txt_mensaje=="") {
			$("#mensaje1").html("(*) Debes ingresar tus apellidos");
			$("#mensaje2").html("(*) Debes ingresar tus nombres");
			$("#mensaje3").html("(*) Debes ingresar tu mensaje");
			document.getElementById("txt_apellidos").focus();
			return false;
		} 
		
		if (txt_apellidos=="" && txt_nombres!="" && txt_mensaje!="") {
			$("#mensaje1").html("(*) Debes ingresar tus apellidos");
			$("#mensaje2").html("(*)");
			$("#mensaje3").html("(*)");
			document.getElementById("txt_apellidos").focus();
			return false;
		} 
		
		if (txt_apellidos!="" && txt_nombres=="" && txt_mensaje!="") {
			$("#mensaje1").html("(*)");
			$("#mensaje2").html("(*) Debes ingresar tus nombres");
			$("#mensaje3").html("(*)");
			document.getElementById("txt_nombres").focus();
			return false;
		} 
		
		if (txt_apellidos!="" && txt_nombres!="" && txt_mensaje=="") {
			$("#mensaje1").html("(*)");
			$("#mensaje2").html("(*)");
			$("#mensaje3").html("(*) Debes ingresar tu mensaje");
			document.getElementById("txt_mensaje").focus();
			return false;
		}
		
		if (txt_apellidos!="" && txt_nombres=="" && txt_mensaje=="") {
			$("#mensaje1").html("(*)");
			$("#mensaje2").html("(*) Debes ingresar tus nombres");
			$("#mensaje3").html("(*) Debes ingresar tu mensaje");
			document.getElementById("txt_nombres").focus();
			return false;
		}

		if (txt_apellidos=="" && txt_nombres!="" && txt_mensaje=="") {
			$("#mensaje1").html("(*) Debes ingresar tus apellidos");
			$("#mensaje2").html("(*)");
			$("#mensaje3").html("(*) Debes ingresar tu mensaje");
			document.getElementById("txt_apellidos").focus();
			return false;
		}

		if (txt_apellidos=="" && txt_nombres=="" && txt_mensaje!="") {
			$("#mensaje1").html("(*) Debes ingresar tus apellidos");
			$("#mensaje2").html("(*) Debes ingresar tus nombres");
			$("#mensaje3").html("(*)");
			document.getElementById("txt_apellidos").focus();
			return false;
		}

		if (txt_apellidos!="" && txt_nombres!="" && txt_mensaje!="") {
			$("#mensaje1").html("(*)");
			$("#mensaje2").html("(*)");
			$("#mensaje3").html("(*)");
			return true;
		}
		
	}

	function dejarComentario()
	{
		if (validar_formulario()) {
			// Aca utilizo Ajax para validar los apellidos y nombres del estudiante
			var txt_apellidos = $("#txt_apellidos").val();
			var txt_nombres = $("#txt_nombres").val();
			var txt_mensaje = $("#txt_mensaje").val();
			
			// Saco los espacios en blanco al comienzo y al final de la cadena
			txt_apellidos=eliminaEspacios(txt_apellidos);
			txt_nombres=eliminaEspacios(txt_nombres);
			txt_mensaje=eliminaEspacios(txt_mensaje);

			$.post("../scripts/verificar_estudiante.php", 
				{ 
					txt_apellidos: txt_apellidos,
				  	txt_nombres: txt_nombres
				}, 
				function(resp) {			
					if (!resp.error) {
						// Existe el estudiante se guarda el comentario en la base de datos
						var co_id_usuario = resp.id_estudiante;
						var co_perfil = "ESTUDIANTE";
						var co_nombre = txt_apellidos + " " + txt_nombres;
						$("#img-loader").show();
						$("#mensaje_error").html("");
						// Aca utilizo Ajax para insertar el comentario
						$.post("../scripts/insertar_comentario.php",
							{ co_id_usuario: co_id_usuario,
							  co_tipo: 1,
							  co_perfil: co_perfil,
							  co_nombre: co_nombre,
							  co_texto: txt_mensaje
							},
							function(resp) {
								$("#formulario_busqueda").slideUp();
								$("#mensaje_insercion").html(resp);
								listarComentarios();
							}
						); 
					} else {
					
					    //No existe el estudiante
						var error = '<span class="error">' +
										'No existe el estudiante ingresado.' +
									'</span>';
						$("#mensaje_error").html(error);
						document.getElementById("txt_apellidos").focus();
					
					}
				}, 'json'
			);
		}
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	COMENTARIOS
    </div>
    <div id="msg_nuevo_comentario" class="paginacion">
		<div id="nuevo_comentario" class="link_form" style="text-align:left;padding-left:2px;">
        	<a href="#">A&ntilde;adir un comentario</a>
        </div>
    </div>
	<div id="formulario_busqueda" style="display:block">
      <div id="tituloBusqueda" class="header">DATOS DEL ESTUDIANTE</div>
      <div id="frmBusqueda" align="left">
   	     <form id="form_busqueda" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="30px" align="right">Apellidos:&nbsp;</td>
                  <td width="120px">
                     <input id="txt_apellidos" type="text" class="cajaGrande" name="txt_apellidos" maxlength="40" style="text-transform:uppercase" />                  </td>
                  <td>
                     &nbsp;<span id="mensaje1" class="error">(*)</span>                  
				  </td>
               </tr>
               <tr>
                  <td width="30px" align="right">Nombres:&nbsp;</td>
                  <td width="120px">
                     <input id="txt_nombres" type="text" class="cajaGrande" name="txt_nombres" maxlength="40" style="text-transform:uppercase" />                  </td>
                  <td>
                     &nbsp;<span id="mensaje2" class="error">(*)</span>                  
				  </td>
               </tr>
               <tr>
               	  <td width="30px" align="right" valign="top">&nbsp;</td>
                  <td> 
                     <div>
                         <label for="txt_mensaje"> Ingresa aqu&iacute; tu mensaje: </label> 
                         <textarea id="txt_mensaje" name="txt_mensaje"></textarea> 
                     </div>                  
                  </td>
                  <td width="*" align="left" valign="top"> &nbsp;<span id="mensaje3" class="error">(*)</span> </td>
               </tr>
               <tr>
                  <td colspan="3">
                      <table width="100%">
                        <tr>
                          <td width="20%" align="left">
                             <span class="error">&nbsp;(*) Campos Obligatorios</span>                          </td>
                          <td width="20%" align="right">
                             <div id="dejar_comentario" class="link_form"><a href="#" onclick="dejarComentario()">Deja tu comentario</a></div>                          </td>
                          <td width="20%" align="right">
                             <div id="cancelar_comentario" class="link_form"><a href="#" onclick="cancelarComentario()">Cancelar</a></div>                          </td>
                          <td width="40%" align="center">
                             <div id="img-loader"><img src="../imagenes/ajax-loader.gif" alt="procesando..."></div>
                          <div id="mensaje_error"> <!-- Aqui va el mensaje de error si existiere --> </div>                          </td>
                        </tr>
                      </table>                  
				  </td>  
               </tr>
            </table>
            <input type="hidden" id="id_usuario" name="id_usuario" />
         </form>
      </div>   
   </div>
   <div id="mensaje_insercion" class="paginacion"> <!-- Aqui va el mensaje de insercion --> </div>
   <div class="header2"> LISTA DE COMENTARIOS </div>
   <div id="lista_comentario" style="text-align:center">
     Debes ingresar tus apellidos y tus nombres...
   </div>
</div>
</body>
</html>
