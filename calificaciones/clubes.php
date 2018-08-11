<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rúbricas Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	// Declaracion de una matriz 5 x 3
	var array = new Array(5);
	for (var i = 0; i < 5; i++) {
		array[i] = [' ', ' ', ' '];
	}	
	$(document).ready(function(){
		cargarClubesDocente();
		cargarPeriodosEvaluacion();
		cargarEscalaCalificaciones(); //Procedimiento para almacenar en una matriz las escalas de calificaciones
		$("#cboPeriodosEvaluacion").change(function(e){
			cargarAportesEvaluacion();
			$("#mensaje_rubrica").html("");
			$("#ver_reporte").hide();
			$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
			$("#paginacion_estudiantes").html("");
			$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
		});
		$("#cboAportesEvaluacion").change(function(e){
			mostrarTitulosRubricas();
			document.getElementById('id_aporte_evaluacion').value = $(this).val();
			$("#mensaje_rubrica").html("");
			$("#ver_reporte").hide();
			$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
			$("#paginacion_estudiantes").html("");
			$("#lista_estudiantes_club").addClass("error");
			$("#lista_estudiantes_club").html("Debe seleccionar un club...");
			$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
		});
		$("#mostrar_ocultar_clubes").on('click',function(){
			$("#pag_clubes").slideToggle();
		});
	});

	function cargarEscalaCalificaciones()
	{
		$.post("scripts/obtener_escalas_calificaciones.php",
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					// Aqui se procesa el resultado mediante JSON
					var datos=eval("(" + resultado + ")");
					for(f=0;f<datos.length;f++)
					{
						array[f][0] = datos[f].ec_nota_minima;
						array[f][1] = datos[f].ec_nota_maxima;
						array[f][2] = datos[f].ec_abreviatura;
					}
				}
			}
		);
	}

	function equivalencia(promedio)
	{
		for(i=0;i<array.length;i++)
			if(promedio>=array[i][0] && promedio<=array[i][1]) {
				return array[i][2];
				break;
			}
	}

	function mostrarTitulosRubricas()
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		$.post("calificaciones/mostrar_titulos_rubricas_club.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
				id_aporte_evaluacion: id_aporte_evaluacion,
				alineacion: "center"
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#txt_rubricas").html(resultado);
				}
			}
		);
	}

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion_principales.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
					$("#lista_estudiantes_club").addClass("error");
					$("#lista_estudiantes_club").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
				}
			}
		);
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if (resultado == false) 
				{
					$("#lista_estudiantes_club").addClass("error");
					$("#lista_estudiantes_club").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#lista_estudiantes_club").addClass("error");
					$("#lista_estudiantes_club").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
			}
		);
	}

	function cargarClubesDocente()
	{
		contarClubesDocente(); //Esta funcion desencadena las demas funciones de paginacion
	}

	function contarClubesDocente()
	{
		$.post("calificaciones/contar_clubes_docente.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONNumRegistros = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistros.num_registros;
					$("#num_asignaturas").html("N&uacute;mero de Clubes encontrados: "+total_registros);
					paginarClubesDocente(4,1,total_registros);
				}
			}
		);
	}

	function sel_texto(input) {
		$(input).select();
	}
	
	function paginarClubesDocente(cantidad_registros, num_pagina, total_registros)
	{
		$.post("calificaciones/paginar_clubes_docente.php",
			{
				cantidad_registros: cantidad_registros,
				num_pagina: num_pagina,
				total_registros: total_registros
			},
			function(resultado)
			{
				$("#paginacion_asignaturas").html(resultado);
			}
		);
		listarClubesDocente(num_pagina);
	}

	function listarClubesDocente(numero_pagina)
	{
		$.post("scripts/cargar_clubes_docente.php", 
			{
				cantidad_registros: 4,
				numero_pagina: numero_pagina
			},
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

	function seleccionarClub(id_club, club)
	{
		document.getElementById("id_club").value = id_club;
		document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_club").addClass("error");
			$("#lista_estudiantes_club").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		} else if (id_aporte_evaluacion == 0 && $('#div_combo_aportes').is(':visible')) {
			$("#lista_estudiantes_club").addClass("error");
			$("#lista_estudiantes_club").html("Debe elegir un aporte de evaluaci&oacute;n...");
		} else {
			$("#mensaje").html("");
			document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + club + "]";
			$("#lista_estudiantes_club").removeClass("error");
			$("#lista_estudiantes_club").html("");
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			cargarEstudiantesClub(id_club);
			$("#ver_reporte").css("display","block");
		}
	}

	function cargarEstudiantesClub(id_club)
	{
		contarEstudiantesClub(id_club);	//Esta funcion desencadena las demas funciones de paginacion 
	}

	function contarEstudiantesClub(id_club)
	{
		$.post("calificaciones/contar_estudiantes_club.php", {id_club: id_club },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONNumRegistrosEstudiantes = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistrosEstudiantes.num_registros;
					$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: "+total_registros);
					listarEstudiantesClub(id_club);
				}
			}
		);
	}

	function listarEstudiantesClub(id_club)
	{
		var id_aporte_evaluacion = document.getElementById("id_aporte_evaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		$("#lista_estudiantes_club").html("<img src='imagenes/ajax-loader.gif' alt='Procesando...'/>");
		$.post("calificaciones/listar_estudiantes_club.php", 
			{ 
				id_club: id_club,
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				$("#lista_estudiantes_club").html(resultado);
				document.getElementById("mensaje_rubrica").innerHTML="";
			}
		);
	}

	function editarCalificacion(obj,id_estudiante,id_club,id_rubrica_evaluacion,tipo_aporte)
	{
		var calificacion = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		var suma_total_aporte = 0; 
		var promedio_aporte = 0;
		var contador_calificaciones = 0;
		var suma_ponderados = 0;
		var ponderado_examen = 0;
		var promedio_quimestral = 0;
		var numero_pagina = document.getElementById("numero_pagina").value;
		var frmFormulario = document.forms["formulario_rubrica"];
		//Validacion de la calificacion
		if(calificacion < 0 || calificacion > 10) {
			alert("La calificacion debe estar en el rango de 0 a 10");
		} else {
			//Aqui va el codigo para calcular el promedio
			for (var iCont=0; iCont < frmFormulario.length; iCont++) {
				var objElemento=frmFormulario.elements[iCont];
				if(objElemento.type=='text') {
					var id_elem = objElemento.id;
					var fila_elem = id_elem.substr(id_elem.indexOf("_")+1);
					if(fila_elem==fila) {
						if(tipo_aporte==1) {
							if(id_elem.substr(0, id_elem.indexOf("_")) == "puntaje") {
								//Aqui calculo la suma de las calificaciones de cada estudiante
								suma_total_aporte += parseFloat(objElemento.value);
								contador_calificaciones++;
							} else {
								//Aqui calculo el promedio del aporte y salto
								promedio_aporte = Math.round(suma_total_aporte / contador_calificaciones * 100) / 100;
								document.getElementById("promedio_"+fila_elem).value = promedio_aporte;
								//Aqui determino la equivalencia correspondiente al promedio del parcial
								document.getElementById("equivalencia_"+fila_elem).value = equivalencia(promedio_aporte);
							}
						} else if(tipo_aporte==2) {
							if(id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoaportes" || id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoexamen") {
								//Aqui calculo la suma de los ponderados de los aportes y del examen
								suma_ponderados += parseFloat(objElemento.value);
							} else if(id_elem.substr(0, id_elem.indexOf("_")) == "examenquimestral") {
								//Aqui calculo el ponderado del examen quimestral
								ponderado_examen = parseFloat(objElemento.value) * 0.2;
								document.getElementById("ponderadoexamen_"+fila_elem).value = Math.round(ponderado_examen * 100) / 100;
							} else if(id_elem.substr(0, id_elem.indexOf("_")) == "calificacionquimestral") {
								promedio_quimestral = Math.round(suma_ponderados * 100) / 100;
								document.getElementById("calificacionquimestral_"+fila_elem).value = promedio_quimestral;
								//Aqui determino la equivalencia correspondiente al promedio del parcial
								document.getElementById("equivalencia_"+fila_elem).value = equivalencia(promedio_quimestral);
							}
						}
					}
				}
			}
			$.post("calificaciones/editar_calificacion_club.php",
				{
					id_estudiante: id_estudiante,
					id_club: id_club,
					id_rubrica_evaluacion: id_rubrica_evaluacion,
					rc_calificacion: calificacion
				},
				function(resultado)
				{
					if(resultado) { // Solo si existe resultado
						$("#mensaje_rubrica").html(resultado);
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
    	<?php echo "INGRESAR CALIFICACIONES " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="5%" class="fuente9" align="right"> <div id="label_combo_aportes"> Aporte:&nbsp; </div> </td>
            <td width="5%"> <div id="div_combo_aportes"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </div> </td>
            <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
      <input id="id_estudiante" type="hidden" />
      <input id="id_rubrica_evaluacion" type="hidden" />
      <input id="numero_pagina" type="hidden" />
   </div>
   <div id="mensaje" class="error"></div>
   <!-- Aqui va la paginacion de clubes asociados al docente -->
   <div id="mensaje_slideToggle" class="paginacion">
       <div id="mostrar_ocultar_clubes" class="link_form" style="text-align:right;padding-right:2px;">
            <!-- Aqui va el hiperenlace para mostrar u ocultar la lista de asignaturas-->
            <a href="#">Mostrar u ocultar la lista de clubes</a>
       </div>
   </div>
   <div id="pag_clubes">
      <!-- Aqui va la paginacion de los clubes asociados al docente -->
      <div id="total_registros" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_asignaturas">&nbsp;N&uacute;mero de Clubes encontrados:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_clubes"> 
                    	<!-- Aqui va la cantidad de clubes encontrados --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div class="header2"> LISTA DE CLUBES ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="39%" align="left">Club</td>
                <td width="38%" align="left">Abreviatura</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_clubes" style="text-align:center"> </div>
   </div>
   <!--<div id="mensaje_rubrica" class="error" style="text-align:center"></div>-->
   <div id="pag_nomina_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="total_registros_estudiantes" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes encontrados:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_estudiantes"> 
                    	<!-- Aqui va la paginacion de estudiantes --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="30%" align="left">N&oacute;mina</td>
                <td width="60%" align="left"><div id="txt_rubricas">Calificaciones</div></td>
                <!-- <td width="18%" align="center">Acciones</td> -->
            </tr>
        </table>
	  </div>
      <form id="formulario_rubrica" action="reportes/reporte_por_aporte_club.php" method="post" target="_blank">
      	 <div id="lista_estudiantes_club" style="text-align:center; overflow:auto"> </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_asignatura" name="id_asignatura" type="hidden" />
	        <input id="id_club" name="id_club" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
         </div>
      </form>
   </div>
</div>
</body>
</html>
