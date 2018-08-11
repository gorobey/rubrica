<?php
	include_once("../funciones/funciones_sitio.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB Consulta de Calificaciones Estudiantiles</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<link href="../css/coolMenu.css" rel="stylesheet" type="text/css" media="screen"/>
<link rel="shortcut icon" href="../favicon.ico" />
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		limpiarBusqueda();
		$("#img_loader").hide();
		cargar_periodos_lectivos();
		$("#pag_estudiantes").hide();
		$("#cboPeriodosLectivos").change(function(e){
			e.preventDefault();
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			$("#txt_apellidos").focus();
		});
	});

	function crear_enlaces_quimestres()
	{
		var id_periodo_lectivo = $("#cboPeriodosLectivos").find(":selected").val();
		if (id_periodo_lectivo == 0)
		{
			$("#lista_calificaciones").html("Debe seleccionar un per&iacute;odo lectivo...");
			$("#cboPeriodosLectivos").focus();
		} else {
			$.post("crear_enlaces_quimestres.php",
				{ id_periodo_lectivo: id_periodo_lectivo },
				function(resultado) {
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						// Aqui va el codigo para presentar los enlaces
						$("#pag_estudiantes").show();
						$("#enlaces").html(resultado);
					}
				}
			);
		}
	}

	function cargar_periodos_lectivos()
	{
		$.get("../periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPeriodosLectivos').append(resultado);			
			}
		});	
	}

	function limpiarBusqueda()
	{
		$("input").val("");
		$("#pag_estudiantes").hide();
		$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
		$("#txt_apellidos").focus();
	}
	
	function soloLetras(elEvento) { // 1
		//var digitos = "0123456789";
		var caracteres = " abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
		var teclas_especiales = [8, 9, 37, 39, 46];
		// 8 = BackSpace, 9 = Tab, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha
		//if (tecla==109) return true; // menos
			//if (tecla==110) return true; // punto
		//if (tecla==189) return true; // guion
//		if (e.ctrlKey && tecla==86) { return true}; //Ctrl v
//		if (e.ctrlKey && tecla==67) { return true}; //Ctrl c
//		if (e.ctrlKey && tecla==88) { return true}; //Ctrl x
//		if (tecla>=96 && tecla<=105) { return true;} //numpad

		// Obtener la tecla pulsada 
		var evento = elEvento || window.event;
		var codigoCaracter = evento.charCode || evento.keyCode;
		var caracter = String.fromCharCode(codigoCaracter);

		// Comprobar si la tecla pulsada es alguna de las teclas especiales
		// (teclas de borrado y flechas horizontales)
		var tecla_especial = false;
		for(var i in teclas_especiales) {
			if(codigoCaracter == teclas_especiales[i]) {
				tecla_especial = true;
				break;
			}
		}
	
		// Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
		// o si es una tecla especial
		return caracteres.indexOf(caracter) != -1 || tecla_especial;
		 
//		patron = /[0-9]/; // patron
//		 
//		te = String.fromCharCode(tecla);
//		return patron.test(te); // prueba
	}

	function consultarEstudiante()
	{
		var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
		var txt_apellidos = $("#txt_apellidos").val();
		var txt_nombres = $("#txt_nombres").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_apellidos=eliminaEspacios(txt_apellidos);
		txt_nombres=eliminaEspacios(txt_nombres);
		
		if (id_periodo_lectivo==0) {
			$("#lista_calificaciones").html("Debe elegir un per&iacute;odo lectivo...");
			document.getElementById("cboPeriodosLectivos").focus();
		} else if (txt_apellidos=="") {
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			document.getElementById("txt_apellidos").focus();
		} else if (txt_nombres=="") {
			$("#lista_calificaciones").html("Debe ingresar los nombres del estudiante...");
			document.getElementById("txt_nombres").focus();
		} else {
			$("#img_loader").show();
			$("#lista_calificaciones").html("");
			$.post("existe_estudiante.php", 
				{ txt_apellidos: txt_apellidos,
				  txt_nombres: txt_nombres,
				  id_periodo_lectivo: id_periodo_lectivo
				 },
				function(resp)
				{
					$("#img_loader").hide();
					if (!resp.error) {
						$("#id_estudiante").val(resp['id_estudiante']);
						crear_enlaces_quimestres();
						$.post("obtener_calificaciones_anuales_id.php", 
							{ id_estudiante: $("#id_estudiante").val(),
							  id_periodo_lectivo: id_periodo_lectivo
							},
							function(resultado)
							{
								$("#pag_estudiantes").show();
								$("#lista_calificaciones").html(resultado);
							}
						);
					} else {
					    //No existe el usuario
						var error = '<span class="error">' +
										'No existe el usuario solicitado...' +
									'</span>';
						$("#lista_calificaciones").html(error);
						document.getElementById("txt_apellidos").focus();
					}
				}, 'json'
			);
		}
	}
</script>
<style type="text/css">
.mensaje_principal {
	background:#f5f5f5;
	font-size:12px;
	height: 20px;
	padding-left:20px;
	padding-top:6px;
}
</style>
</head>

<body>
<div id="pagina">
  <table id="contenido" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="25%">  
        <table class="tabla_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="70%">
                    <div class="titulo1">S I A E</div>
                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>
                </td>
                <td valign="top">
                    <div class="fecha">
                        <!-- Aqui va la fecha del sistema generada mediante PHP -->
                        <?php echo fecha_actual(); ?>
                    </div>
                    <div class="titulo3" style="padding-right:2px;">
						Consulta de Calificaciones Estudiantiles
                    </div>
                </td>
            </tr>
        </table>
      </td>
    </tr>
    <tr>
      <div id="cuerpo">
        <!-- Aqui va el cuerpo de la pagina en si-->
        <table id="cuerpo" width="100%" border="0" cellpadding="0" cellspacing="0">
           <tr>
              <td>
                  <!-- <div id="mensaje_principal" class="mensaje_principal">
                      Para consultar el detalle de alg&uacute;n quimestre, dar clic sobre el encabezado del mismo...
                  </div> -->
                  <div id="formulario_busqueda" style="display:block">
                  	<div id="tituloBusqueda" class="header">DATOS DEL ESTUDIANTE</div>
                    <div id="frmBusqueda" align="left">
                    	<form id="form_busqueda" action="" method="post">
                        	<table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                            	<tr>
                                	<td width="7%" align="right"> A&ntilde;o Lectivo:&nbsp; </td>
                                    <td width="5%"> <select id="cboPeriodosLectivos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                                    <td width="5%" align="right">Apellidos:&nbsp;</td>
                                    <td width="10%">
                                    	<input id="txt_apellidos" type="text" class="cajaGrande" name="txt_apellidos" maxlength="40" onkeypress="return soloLetras(event)" onpaste="return false" style="text-transform:uppercase" />
                                    </td>
                                    <td  width="5%" align="right">Nombres:&nbsp;</td>
                                    <td width="10%">
                                        <input id="txt_nombres" type="text" class="cajaGrande" name="txt_nombres" maxlength="40" onkeypress="return soloLetras(event)" onpaste="return false" style="text-transform:uppercase" />
                                    </td>
                                    <td width="5%" align="right">
                                    	<div id="consultar" class="link_form"><a href="#" onclick="consultarEstudiante()">Consultar</a></div>
                                    </td>
                                    <td width="5%" align="right">
                                    	<div id="limpiar" class="link_form"><a href="#" onclick="limpiarBusqueda()">Limpiar</a></div>
                                    </td>
                                    <td width="*">
                                    	<div id="img-loader" style="padding-left:2px"></div>
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" id="id_estudiante" />
                        </form>
                    </div>
                 </div>
                 <div id="mensaje" class="error"> </div>
                 <div id="pag_estudiantes">
                     <!-- Aqui va la paginacion de los estudiantes encontrados -->
                     <div id="enlaces" class="mensaje_principal">
                     	<!-- Aqui van los enlaces a los detalles de calificaciones de cada quimestre -->
                     </div>
                     <div class="header2"> CALIFICACIONES DEL ESTUDIANTE </div>
                     <div class="cabeceraTabla">
                        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                           <tr class="cabeceraTabla">
                              <td width="5%" align="left">Nro.</td>
                              <td width="35%" align="left">Asignatura</td>
                              <td width="5%" align="left">1ER.Q.</td>
                              <td width="5%" align="left">2DO.Q.</td>
                              <td width="5%" align="left">Suma</td>
                              <td width="5%" align="left">Prom.</td>
                              <td width="5%" align="left">SUP.</td>
                              <td width="5%" align="left">REM.</td>
                              <td width="5%" align="left">GRA.</td>
                              <td width="5%" align="left">P.F.</td>
                              <td width="20%" align="left">Observaci&oacute;n</td>
                           </tr>
                        </table>
                     </div>
                 </div>
                 <div id="img_loader" align="center"> <img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' /> </div>
                 <div id="lista_calificaciones" style="text-align:center">
                    <!-- Aqui se desplegan las calificaciones del estudiante -->
                 </div>
              </td>
           </tr>   
        </table>
      </div>
    </tr>
  </table>
</div>
</body>
</html>
