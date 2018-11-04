<?php
	session_start();
	include_once("../funciones/funciones_sitio.php");
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	require_once("../scripts/clases/class.periodos_lectivos.php");

	if (!isset($_SESSION['usuario_logueado']) OR !$_SESSION['usuario_logueado'])
		header("Location: index.php");
	else {
		//Primero tengo que obtener el id_estudiante para obtener los datos correspondientes
		$id_estudiante = $_GET['id_estudiante'];
		//print_r($id_estudiante);
		$estudiante = new estudiantes();
		$nombreEstudiante = $estudiante->obtenerEstudianteId($_SESSION["id_estudiante"], $_SESSION["id_periodo_lectivo"]);
		$nombreParalelo = $estudiante->obtenerCursoParaleloEstudianteId($_SESSION["id_estudiante"], $_SESSION["id_periodo_lectivo"]);
		//Obtengo los años de inicio y de fin del periodo lectivo actual
		$periodos_lectivos = new periodos_lectivos();
		$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($_SESSION['id_periodo_lectivo']);
		$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
		$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
		//echo $id_periodo_lectivo;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIAE-WEB Consulta de Calificaciones Estudiantiles</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
<style type="text/css">
	.mensaje_principal {
		background:#f5f5f5;
		font-size:12px;
		height:18px;
		padding-top:3px;
		padding-bottom:1px;
	}
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
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
<script src="../js/charCount.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#txt_mensaje").charCount({
			allowed: 250,		
			warning: 20,
			counterText: 'Caracteres restantes: '	
		});
		$("#nuevo_comentario").on('click',function(){
			$("#img-loader").hide();
			$("#mensaje_comentario").html("(*)");
			$("#msg_nuevo_comentario").slideUp();
			$("#formulario_comentario").slideDown();
			document.getElementById("form_comentario").reset();
			document.getElementById("txt_mensaje").focus();
		});
		$("#cancelar_comentario").on('click',function(){
			$("#formulario_comentario").slideUp();
			$("#msg_nuevo_comentario").slideDown();
		});
		$("#img_loader").hide();
		$("#img-loader").hide();
		$("#formulario_comentario").hide();
		obtenerDatosEstudiante();
		cargarPeriodosEvaluacion();

		$("#cboPeriodosEvaluacion").change(function(e){
			cargarAportesEvaluacion();
		});
		$("#ver_reporte").hide();		
		listarComentarios(); // Esta funcion sirve para listar los comentarios existentes
	});

	function cargarPeriodosEvaluacion()
	{
		$.get("../scripts/cargar_periodos_evaluacion_principales.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
				}
			}
		);
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("../scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if (resultado == false) 
				{
					alert("Error");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
				}
			}
		);
	}

	function obtenerDatosEstudiante()
	{
		// Esta funcion sirve para recuperar los apellidos y nombres del estudiante
		// y almacenarlos en dos input hidden para procesos posteriores
		$.post("obtener_datos_estudiante.php",
			{
				id_estudiante: $("#id_estudiante").val(),
			  	id_periodo_lectivo: $("#id_periodo_lectivo").val()
			 },
			function(resp)
			{
				var JSONEstudiante = eval('(' + resp + ')');
				$("#txt_apellidos").val(JSONEstudiante.es_apellidos);
				$("#txt_nombres").val(JSONEstudiante.es_nombres);
			}
		);
	}

	function listarComentarios()
	{
		$.post("../scripts/listar_comentarios_estudiante.php",
			function(resp) {
				$("#lista_comentarios").html(resp);
			}
		);
	}

	function validar_formulario()
	{
		var txt_mensaje = $("#txt_mensaje").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_mensaje=eliminaEspacios(txt_mensaje);

		if (txt_mensaje=="") {
			$("#mensaje_comentario").html("(*) Debes ingresar tu mensaje");
			document.getElementById("txt_mensaje").focus();
			return false;
		} else { 
			return true;
		}
		
	}

	function dejarComentario()
	{
		if (validar_formulario()) {
			// Aca utilizo Ajax para enviar el mensaje del estudiante
			var txt_apellidos = $("#txt_apellidos").val();
			var txt_nombres = $("#txt_nombres").val();
			var txt_mensaje = $("#txt_mensaje").val();
			
			// Saco los espacios en blanco al comienzo y al final de la cadena
			txt_apellidos=eliminaEspacios(txt_apellidos);
			txt_nombres=eliminaEspacios(txt_nombres);
			txt_mensaje=eliminaEspacios(txt_mensaje);

			// Existe el estudiante se guarda el comentario en la base de datos
			var co_id_usuario = $("#id_estudiante").val();
			var co_perfil = "ESTUDIANTE";
			var co_nombre = txt_apellidos + " " + txt_nombres;
			$("#img-loader").show();
			$("#mensaje_error").html("");
			// Aca utilizo Ajax para insertar el comentario
			$.post("../scripts/insertar_comentario.php",
				{ 
					co_id_usuario: co_id_usuario,
					co_tipo: 1,
					co_perfil: co_perfil,
					co_nombre: co_nombre,
					co_texto: txt_mensaje
				},
				function(resp) {
					$("#formulario_comentario").slideUp();
					$("#msg_nuevo_comentario").slideDown();
					$("#mensaje_insercion").html(resp);
					listarComentarios();
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

    function mostrarLeyendasRubricas(id_aporte_evaluacion){
        
		$.post("mostrar_leyendas_rubricas.php", 
			{
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_tipo_asignatura: 1
			},
			function(resultado){
				$("#leyendas_rubricas").html(resultado);
			}
        );
    }

	function mostrarTitulosPeriodosEvaluacion()
	{
		$.post("../scripts/mostrar_titulos_periodos_evaluacion.php", 
			{
				id_periodo_lectivo: $("#id_periodo_lectivo").val(),
				pe_principal: 1,
				alineacion: "left"
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#txt_encabezados").html(resultado);
				}
			}
		);
	}

	function mostrarTitulosAportesEvaluacion(id_periodo_evaluacion)
	{
		$.post("../scripts/mostrar_titulos_aportes_evaluacion.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
				alineacion: "right"
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#txt_encabezados").html(resultado);
				}
			}
		);
	}

	function mostrarTitulosRubricasEvaluacion(id_aporte_evaluacion)
	{
		$.post("../scripts/mostrar_titulos_rubricas_evaluacion.php", 
			{
				id_aporte_evaluacion: id_aporte_evaluacion,
				alineacion: "right"
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#txt_encabezados").html(resultado);
				}
			}
		);
	}

	function consultarEstudiante()
	{
		// Aqui se cambia el valor de "action" del formulario
		form_califica.action = "../dompdf/resumen_anual.php";
		
		$("#ver_reporte").hide();
		
		// Esto es para pasar los parametros al reporte en PDF
		$("#idperiodolectivo").val($("#id_periodo_lectivo").val());
		$("#idestudiante").val($("#id_estudiante").val());
		
		var txt_apellidos = $("#txt_apellidos").val();
		var txt_nombres = $("#txt_nombres").val();
		
		$("#img_loader").show();
		$("#lista_calificaciones").html("");
		$("#lista_calificaciones").removeClass("error");
		mostrarTitulosPeriodosEvaluacion();
		$.post("obtener_curso_paralelo_id.php",
			{ id_estudiante: $("#idestudiante").val(),
			  id_periodo_lectivo: $("#idperiodolectivo").val()
			 },
			function(resp)
			{
				$("#titulo").html("RESUMEN ANUAL [" + resp + "]");
			}
		);
		$.post("obtener_calificaciones_anuales_id.php", 
			{ id_estudiante: $("#id_estudiante").val(),
			  id_periodo_lectivo: $("#id_periodo_lectivo").val()
			},
			function(resultado)
			{
				$("#img_loader").hide();
				$("#lista_calificaciones").html(resultado);
				$("#ver_reporte").show();
			}
		);
	}
	
	function obtenerDetallePeriodoEvaluacion()
	{
		// Aqui se cambia el valor de "action" del formulario
		form_califica.action = "../dompdf/resumen_periodo.php";
		
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		
		if (id_periodo_evaluacion==0) {
			alert("Debes seleccionar un quimestre...");
		} else {
		    $("#mensaje_error").hide();
			$("#ver_reporte").show();
			
			$("#idperiodoevaluacion").val(id_periodo_evaluacion);
			$("#idperiodolectivo").val($("#id_periodo_lectivo").val());
			$("#idestudiante").val($("#id_estudiante").val());
		
			$("#img_loader").show();
			$("#lista_calificaciones").html("");
			mostrarTitulosAportesEvaluacion(id_periodo_evaluacion);
			$.post("obtener_curso_paralelo_periodo_evaluacion.php",
				{ 
					id_estudiante: $("#id_estudiante").val(),
					id_periodo_evaluacion: id_periodo_evaluacion
				 },
				function(resp)
				{
					var JSONEstudiante = eval('(' + resp + ')');
					if(!JSONEstudiante.error)
						$("#titulo").html("DETALLE DEL " + JSONEstudiante.pe_nombre + " [" + JSONEstudiante.cu_nombre + " " + JSONEstudiante.pa_nombre + "]");
				}
			);
			$.post("obtener_calificaciones_quimestrales_id.php", 
				{ 
					id_estudiante: $("#id_estudiante").val(),
					id_periodo_lectivo: $("#id_periodo_lectivo").val(),
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado)
				{
					$("#img_loader").hide();
					$("#lista_calificaciones").html(resultado);
					$("#ver_reporte").show();
				}
			);
		}
	}

	function obtenerDetalleAporteEvaluacion()
	{
		// Aqui se cambia el valor de "action" del formulario
		form_califica.action = "../dompdf/resumen_aporte.php";
		
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		
		if (id_periodo_evaluacion==0) {
			alert("Debes seleccionar un quimestre...");
		} else if (id_aporte_evaluacion==0) {
			alert("Debes seleccionar un parcial...");
		} else {
		    $("#mensaje_error").hide();
			$("#ver_reporte").show();
			
			$("#idaporteevaluacion").val(id_aporte_evaluacion);
			$("#idperiodolectivo").val($("#id_periodo_lectivo").val());
			$("#idestudiante").val($("#id_estudiante").val());
	
			$("#img_loader").show();
			$("#lista_calificaciones").html("");
			mostrarLeyendasRubricas(id_aporte_evaluacion);
			mostrarTitulosRubricasEvaluacion(id_aporte_evaluacion);
			$.post("obtener_curso_paralelo_aporte_evaluacion.php",
				{ 
					id_estudiante: $("#id_estudiante").val(),
					id_aporte_evaluacion: id_aporte_evaluacion
				 },
				function(resp)
				{
					var JSONEstudiante = eval('(' + resp + ')');
					if(!JSONEstudiante.error)
						$("#titulo").html("DETALLE DEL " + JSONEstudiante.ap_nombre + " [" + JSONEstudiante.cu_nombre + " " + JSONEstudiante.pa_nombre + "]");
				}
			);
			$.post("obtener_calificaciones_parciales_id.php", 
				{ 
					id_estudiante: $("#id_estudiante").val(),
					id_periodo_lectivo: $("#id_periodo_lectivo").val(),
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado)
				{
					$("#img_loader").hide();
					$("#lista_calificaciones").html(resultado);
					$("#ver_reporte").show();
				}
			);
		}
	}
</script>
</head>

<body>
<div id="pagina">
  <table id="contenido" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="25%">  
        <table class="tabla_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="50%">
                    <div class="titulo1">S I A E</div>
                    <div class="titulo2">Sistema Integrado de Administraci&oacute;n Estudiantil</div>
                </td>
                <td width="*" valign="top">
                	<table id="info" width="100%" cellpadding="0" cellspacing="0" border="0">
                    	<tr>
                        	<td>
                            	<table id="tabla_fecha" width="100%" cellpadding="0" cellspacing="0" border="0">
                                	<tr>
                                    	<td width="50%" align="center">
                                        	<div class="perfil"><?php echo $nombrePeriodoLectivo ?></div>
                                        </td>
                                        <td width="*">
                                        	<div class="fecha">
                                            	<!-- Aqui va la fecha del sistema generada mediante PHP -->
                        						<?php echo fecha_actual(); ?>
                    						</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	<table id="tabla_login" width="100%" cellpadding="0" cellspacing="0" border="0">
                                	<tr>
                                    	<td width="50%" align="center">
                                        	<div class="perfil"><?php echo $nombreEstudiante ?></div>
                                            <div class="perfil"><?php echo $nombreParalelo ?></div>
                                        </td>
                                        <td width="45%" align="right">
                                        	<div class="login">
                                            	<a href="logout.php">Salir</a>
                                            </div>
                                        </td>
                                        <td width="*" align="right">
                                        	<div class="botones">
                                            	<a href="logout.php">
                                                	<img src="../imagenes/login_gnome.png" onmouseover="this.src='../imagenes/login_gnome1.png'" onmouseout="this.src='../imagenes/login_gnome.png'" alt="haga click para salir..." title="Salir del Sistema..." />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
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
                 <div id="pag_estudiantes">
                     <div id="barra_principal" style="margin: 1px">
                     	<table id="tabla_periodos_evaluacion" class="fuente8" width="100%" cellpadding="0" cellspacing="0" border="0">
                        	<tr>
                                <td width="5%" class="fuente9" align="right"> Quimestre:&nbsp; </td>
                                <td width="5%"><select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select></td>
                                <td width="5%" class="fuente9" align="right"> Parcial:&nbsp; </td>
                                <td width="5%"><select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select></td>
                                <td width="5%" align="center">
                                   <div id="boton_accion" class="link_form">
                                      <!-- Aqui va el enlace para consultar las calificaciones parciales del estudiante -->
                                      <a href="#" onclick="obtenerDetalleAporteEvaluacion()">Consultar</a>
                                   </div>   
                                </td>
                                <td width="10%" align="center">
                                   <div id="boton_accion" class="link_form">
                                      <!-- Aqui va el enlace para consultar las calificaciones quimestrales del quimestrales estudiante -->
                                      <a href="#" onclick="obtenerDetallePeriodoEvaluacion()">Consulta Quimestral</a>
                                   </div>   
                                </td>
                                <td width="10%" align="center">
                                   <div id="boton_accion" class="link_form">
                                      <!-- Aqui va el enlace para consultar las calificaciones anuales del estudiante -->
                                      <a href="#" onclick="consultarEstudiante()">Consulta Anual</a>
                                   </div>   
                                </td>
                                <td width="*"> <div id="mensaje_consulta" class="error" style="text-align:center"></div> </td>
                            </tr>
                        </table>
                     </div>
                     <div id="mensaje_error" class="error">
                     	<!-- Aqui va el mensaje de error -->
                     </div>
                     <div id="titulo" class="header2"> CALIFICACIONES DEL ESTUDIANTE </div>
					 <div id="leyendas_rubricas" class="paginacion">
						<!-- Aqui van las leyendas de las rubricas de evaluacion -->
					 </div>
                     <div class="cabeceraTabla">
                        <table width="100%" cellspacing=0 cellpadding=0 border=0>
                           <tr class="fuente8">
                              <td width="5%" align="left">Nro.</td>
                              <td width="35%" align="left">Asignatura</td>
                              <td width="60%" align="left"> 
                              	<div id="txt_encabezados"> 
                              	  <!-- Aqui van los titulos de los quimestres, parciales o rúbricas --> 
                                </div> 
                              </td>
                           </tr>
                        </table>
                     </div>
                 </div>
                 <div id="ver_reporte">
                     <form id="form_califica" name="form_califica" action="" method="post">
                        <div id="img_loader" align="center"> <img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' /> </div>
                        <div id="lista_calificaciones" style="text-align:center; overflow:auto">
                            <!-- Aqui se desplegan las calificaciones del estudiante -->
                        </div>
                        <div id="ver_reporte" style="text-align:center;margin-top:2px">
                            <input type="hidden" id="idestudiante" name="idestudiante" />
                            <input type="hidden" id="idperiodolectivo" name="idperiodolectivo" />
                            <input type="hidden" id="idperiodoevaluacion" name="idperiodoevaluacion" />
                            <input type="hidden" id="idaporteevaluacion" name="idaporteevaluacion" />
                            <input type="submit" value="Ver Reporte" />
                        </div>
                     </form>
                 </div>
                 <div id="pag_comentarios">
                     <div id="titulo_pagina">COMENTARIOS</div>
                     <div id="msg_nuevo_comentario" class="paginacion">
                        <div id="nuevo_comentario" class="link_form" style="text-align:left;padding-left:2px;">
                            <a href="#">A&ntilde;adir un comentario</a>
                        </div>
                     </div>

                     <!-- div para el ingreso del comentario -->
    
                     <div id="formulario_comentario" align="left" class="form_nuevo">
                         <form id="form_comentario" action="" method="post">
                            <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                               <tr>
                                  <td width="30px" align="right" valign="top">&nbsp;</td>
                                  <td width="120px"> 
                                     <div>
                                         <label for="txt_mensaje"> Ingresa aqu&iacute; tu mensaje: </label> 
                                         <textarea id="txt_mensaje" name="txt_mensaje"></textarea> 
                                     </div>       
                                  </td>
                                  <td align="left" valign="top"> &nbsp;<span id="mensaje_comentario" class="error">(*)</span> </td>
                               </tr>
                               <tr>
                                  <td colspan="4">
                                      <table width="100%">
                                        <tr>
                                          <td width="20%" align="left">
                                             <span class="error">&nbsp;(*) Campos Obligatorios</span>
                                          </td>
                                          <td width="20%" align="right">
                                             <div id="dejar_comentario" class="link_form"><a href="#" onclick="dejarComentario()">Deja tu comentario</a></div>
                                          </td>
                                          <td width="20%" align="right">
                                             <div id="cancelar_comentario" class="link_form"><a href="#">Cancelar</a></div>
                                          </td>
                                          <td width="40%" align="center">
                                             <div id="img-loader"><img src="../imagenes/ajax-loader.gif" alt="procesando..."></div>
                                             <div id="mensaje_error"> <!-- Aqui va el mensaje de error si existiere --> </div>
                                          </td>
                                        </tr>
                                      </table>
                                  </td>  
                               </tr>
                            </table>
                            <input type="hidden" id="id_estudiante" name="id_estudiante" value="<?php echo $id_estudiante ?>" />
                            <input type="hidden" id="id_periodo_lectivo" name="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>" />
                            <input type="hidden" id="txt_apellidos" name="txt_apellidos" />
                            <input type="hidden" id="txt_nombres" name="txt_nombres" />
                         </form>
                      </div>
                      <div id="mensaje_insercion" class="paginacion" style="padding-left: 7px;"> 
                      	 <!-- Aqui va el mensaje de insercion --> 
                         Estimado(a) estudiante: Se pide un mínimo de educación en el lenguaje. Todos los comentarios tienen su autor.
                      </div>
                      <div class="header2"> LISTA DE COMENTARIOS </div>
                      <div id="lista_comentarios" style="text-align:center">
                         <!-- Aqui va el listado de los comentarios existentes -->
                      </div>
                     
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
