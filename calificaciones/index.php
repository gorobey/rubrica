<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#mensaje_slideToggle").hide();
		$("#pag_asignaturas").hide();
		$("#pag_nomina_estudiantes").hide();
		$("#tituloNomina").hide();
		$("#cabecera_calificaciones").hide();

		cargarPeriodosEvaluacion();
		cargarAsignaturasDocente();
		verificarCalificaciones();

		$("#cboPeriodosEvaluacion").change(function(e){
			cargarAportesEvaluacion();
			$("#div_estado_rubrica").html("");
			$("#div_fecha_cierre").html("");
			$("#mensaje_rubrica").html("");
			$("#ver_reporte").hide();
			$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
			$("#paginacion_estudiantes").html("");
			$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
		});

		$("#cboAportesEvaluacion").change(function(e){
			$("#div_estado_rubrica").html("");
			$("#div_fecha_cierre").html("");
			$("#mensaje_rubrica").html("");
			document.getElementById('id_aporte_evaluacion').value = $(this).val();
			$("#mensaje_rubrica").html("");
			$("#ver_reporte").hide();
			$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
			$("#paginacion_estudiantes").html("");
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
			$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
		});		
	});

	function verificarCalificaciones()
	{
		//Aqui va el codigo para verificar si el docente tiene calificaciones mal ingresadas
		$("#img_loader").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
		$.post("calificaciones/contar_calificaciones_erroneas_docente.php", 
			function(resultado)
			{
				$("#img_loader").html("");
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONNumRegistros = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistros.num_registros;
					if (total_registros > 0) {
						$("#total_calificaciones_erroneas").html("&nbsp;N&uacute;mero de Calificaciones mal ingresadas: "+total_registros+". Por favor corregir estas calificaciones para continuar. D&eacute; clic sobre la nota para editarla.");
						$("#total_calificaciones_erroneas").show();
						$("#pag_asignaturas").hide();
						$("#pag_nomina_estudiantes").hide();
						$("#tituloCalificacionesErroneas").show();
						$("#cabecera_calificaciones").show();
						//Aqui va la llamada ajax para desplegar las calificaciones mal ingresadas
						$.post("calificaciones/listar_calificaciones_erroneas_docente.php", 
							function(resultado)
							{
								if(resultado == false)
								{
									alert("Error");
								}
								else
								{
									$("#lista_calificaciones_erroneas").html(resultado);
								}
							}
						);
					} else {
						$("#mensaje_slideToggle").show();
						$("#pag_asignaturas").show();
						$("#pag_nomina_estudiantes").show();
						$("#tituloNomina").show();
						$("#cabecera_calificaciones").show();
						$("#total_calificaciones_erroneas").hide();
						$("#tituloCalificacionesErroneas").hide();
						$("#cabecera_calificaciones").hide();
						$("#lista_calificaciones_erroneas").hide();
					}
				}
			}
		);
	}
	
	function limpiarCalificacionErronea()
	{
		document.getElementById("re_calificacion").value="";
		document.getElementById("re_calificacion").focus();
	}

	function salirEdicionCalificacion()
	{
		$("#formulario_nuevo").hide();
	}
	
	function editarCalificacionErronea(id_rubrica_estudiante)
	{
		// Validación de la entrada de datos
		$("#img_loader").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
		$.post("calificaciones/obtener_calificacion_erronea_docente.php", 
			{ id_rubrica_estudiante: id_rubrica_estudiante },
			function(resultado) {
				$("#img_loader").html("");
				var JSONRubricaEstudiante = eval('(' + resultado + ')');
				//Aqui se va a pintar la rubrica de estudiante elegida
				document.getElementById("id_rubrica_estudiante").value=JSONRubricaEstudiante.id_rubrica_estudiante;
				document.getElementById("re_calificacion").value=JSONRubricaEstudiante.re_calificacion;
				$("#formulario_nuevo").show();
				document.getElementById("re_calificacion").focus();
			}
		);	
	}
	
	function actualizarCalificacionErronea()
	{
		//Actualizar calificacion mal ingresada
		var calificacion=$("#re_calificacion").val();
		var id_rubrica_estudiante=$("#id_rubrica_estudiante").val();
		if(isNaN(calificacion)) {
			$("#img_loader").html("El texto ingresado no es un n&uacute;mero v&aacute;lido");
			$("#re_calificacion").focus();
		} else if(calificacion<0 || calificacion>10) {
			$("#img_loader").html("La calificaci&oacute;n debe estar en el rango de 0 a 10");
			$("#re_calificacion").focus();
		} else {
			$("#img_loader").html("");
			$.post("calificaciones/actualizar_calificacion_erronea_docente.php", 
				{ id_rubrica_estudiante: id_rubrica_estudiante,
				  re_calificacion: calificacion
				},
				function(resultado) {
					//Si todo va bien se vuelve a comprobar si el docente no tiene calificaciones mal ingresadas
					$("#img_loader").html(resultado);
					$("#formulario_nuevo").hide();
					verificarCalificaciones();
				}
			);	
		}
	}
	
	function mostrarEstadoRubrica(id_curso)
	{
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		
		$.post("calificaciones/mostrar_estado_rubrica.php",
			{
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_curso: id_curso
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#div_estado_rubrica").html(resultado);
				}
			}
		);
		
		$.post("calificaciones/obtener_fecha_cierre_aporte.php",
			{ 
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_curso: id_curso
			},
			function(resultado) 
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#div_fecha_cierre").html(resultado);
				}
			}
		);
			
	}

	function mostrarTitulosRubricas(alineacion, id_asignatura)
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		$.post("calificaciones/mostrar_titulos_rubricas.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
				id_aporte_evaluacion: id_aporte_evaluacion,
				alineacion: alineacion,
				id_asignatura: id_asignatura
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

	function sel_texto(input) {
		$(input).select();
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
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
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
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
			}
		);
	}

	function listarAsignaturasDocente(numero_pagina)
	{
		$.post("scripts/cargar_asignaturas_docente.php", 
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
					//console.log(resultado);
					$("#lista_asignaturas").html(resultado);
				}
			}
		);
	}

	function cargarAsignaturasDocente()
	{
		contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
	}

	function contarAsignaturasDocente()
	{
		$.post("calificaciones/contar_asignaturas_docente.php", { },
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
					$("#num_asignaturas").html("N&uacute;mero de Asignaturas encontradas: "+total_registros);
					paginarAsignaturasDocente(4,1,total_registros);
				}
			}
		);
	}
	
	function paginarAsignaturasDocente(cantidad_registros, num_pagina, total_registros)
	{
		$.post("calificaciones/paginar_asignaturas_docente.php",
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
		listarAsignaturasDocente(num_pagina);
	}
	
	function cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura)
	{
		contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);	//Esta funcion desencadena las demas funciones de paginacion 
	}

	function contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura)
	{
		$.post("calificaciones/contar_estudiantes_paralelo.php", {id_paralelo: id_paralelo },
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
					//Listar los estudiantes del paralelo
					listarEstudiantesParalelo(id_curso, id_paralelo,id_asignatura);
				}
			}
		);
	}

    function mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura){
        
		$.post("calificaciones/mostrar_leyendas_rubricas.php", 
			{
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_asignatura: id_asignatura
			},
			function(resultado){
				$("#leyendas_rubricas").html(resultado);
			}
        );
    }

	function listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura)
	{
		var id_aporte_evaluacion = document.getElementById("id_aporte_evaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		$("#lista_estudiantes_paralelo").empty();
		$("#img_loader_estudiantes").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
		$.post("calificaciones/listar_estudiantes_paralelo.php", 
			{ 
				id_curso: id_curso,
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				$("#img_loader_estudiantes").html("");
				//console.log(resultado);
				//anadir el resultado al DOM
				$("#lista_estudiantes_paralelo").html(resultado);
				document.getElementById("mensaje_rubrica").innerHTML="";
			}
		);
	}

	function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		mostrarTitulosRubricas("center", id_asignatura);
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
        var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		} else if (id_aporte_evaluacion == 0 && $('#div_combo_aportes').is(':visible')) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
		} else {
			mostrarEstadoRubrica(id_curso);
            mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura);
			$("#mensaje").html("");
			document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]";
			$("#lista_estudiantes_paralelo").removeClass("error");
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
			$("#ver_reporte").css("display","block");
		}
	}

	function truncateFloat(number, digitos) {
		var multiplicador = Math.pow (10, digitos);
		var resultado = (parseInt(number * multiplicador)) / multiplicador;
		return resultado;
	}

	function editarCalificacion(obj,id_estudiante,id_paralelo,id_asignatura,id_rubrica_personalizada,tipo_aporte)
	{
		var calificacion = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		var suma_total_aporte = 0; 
		var promedio_aporte = 0;
		var contador_calificaciones = 0;
		var suma_ponderados = 0;
		var ponderado_examen = 0;
		//var numero_pagina = document.getElementById("numero_pagina").value;
		var frmFormulario = document.forms["formulario_rubrica"];
		//Validacion de la calificacion
		if(calificacion == "")
			alert("El campo no puede ser nulo...");
		else if(calificacion < 0 || calificacion > 10) {
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
								promedio_aporte = suma_total_aporte / contador_calificaciones;
								//document.getElementById("promedio_"+fila_elem).value = Math.round((promedio_aporte * 100) / 100);
								document.getElementById("promedio_"+fila_elem).value = truncateFloat(promedio_aporte,2);
							}
						} else if(tipo_aporte==2) {
							if(id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoaportes" || id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoexamen") {
								//Aqui calculo la suma de los ponderados de los aportes y del examen
								suma_ponderados += parseFloat(objElemento.value);
							} else if(id_elem.substr(0, id_elem.indexOf("_")) == "examenquimestral") {
								//Aqui calculo el ponderado del examen quimestral
								ponderado_examen = parseFloat(objElemento.value) * 0.2;
								//document.getElementById("ponderadoexamen_"+fila_elem).value = Math.round((ponderado_examen * 100) / 100);
								document.getElementById("ponderadoexamen_"+fila_elem).value = truncateFloat(ponderado_examen,2);
							} else if(id_elem.substr(0, id_elem.indexOf("_")) == "calificacionquimestral") {
								//document.getElementById("calificacionquimestral_"+fila_elem).value = Math.round((suma_ponderados * 100) / 100);
								document.getElementById("calificacionquimestral_"+fila_elem).value = truncateFloat(suma_ponderados,2);
							}
						}
					}
				}
			}
			$.post("calificaciones/editar_calificacion.php",
				{
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_rubrica_personalizada: id_rubrica_personalizada,
					re_calificacion: calificacion
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
	
	function editarCalificacionComportamiento(obj,id_estudiante,id_paralelo,id_asignatura,id_aporte_evaluacion)
	{
		var str = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		//Validacion de la calificacion
		str = eliminaEspacios(str);
		var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
		var idx = permitidos.indexOf(str);
		//alert(str);
		if(str != '') { 
			if(idx == -1) {
				alert("La calificacion debe estar en el rango de A a E");
				obj.value = "";
			} else {
				$.post("docentes/editar_calificacion_comportamiento.php",
					{
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: id_aporte_evaluacion,
						co_calificacion: str.toUpperCase()
					},
					function(resultado)
					{
						if(resultado) { // Solo si existe resultado
							//alert(resultado);
							$("#mensaje_rubrica").html(resultado);
						}
					}
				);
			}
		} else {
			$.post("docentes/eliminar_calificacion_comportamiento.php",
				{
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado)
				{
					if(resultado) { // Solo si existe resultado
						//alert(resultado);
						$("#mensaje_rubrica").html(resultado);
					}
				}
			);
		}	
	}

	function editarCalificacionCualitativa(obj,id_estudiante,id_paralelo,id_asignatura,id_aporte_evaluacion)
	{
		var str = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		str = str.toUpperCase();
		//Validacion de la calificacion
		str = eliminaEspacios(str);
		var permitidos = ['EX', 'MB', 'B', 'R'];
		var idx = permitidos.indexOf(str);
		//alert(str);
		if(str != '') { 
			if(idx == -1) {
				alert("La calificacion debe estar en el conjunto EX MB B R");
				obj.value = "";
			} else {
				$.post("docentes/editar_calificacion_cualitativa.php",
					{
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: id_aporte_evaluacion,
						rc_calificacion: str
					},
					function(resultado)
					{
						//alert(resultado);
						$("#mensaje_rubrica").html(resultado);
					}
				);
			}
		} else {
			$.post("docentes/eliminar_calificacion_cualitativa.php",
				{
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado)
				{
					//alert(resultado);
					$("#mensaje_rubrica").html(resultado);
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
            <td width="5%"> <div id="div_combo_aportes"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </div> 
            </td>
            <td width="10%"> <div id="div_estado_rubrica" style="padding-left: 4px;"> </div> </td>
            <td width="20%"> <div id="div_fecha_cierre" style="padding-left: 4px;"> </div> </td>
            <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
      <input id="id_estudiante" type="hidden" />
      <input id="id_rubrica_personalizada" type="hidden" />
      <input id="numero_pagina" type="hidden" />
   </div>
   <div id="mensaje" class="error"></div>
   <div id="formulario_nuevo">
	 <div id="tituloForm" class="header">Editar Calificaci&oacute;n Mal Ingresada</div> 
     <div id="frmNuevo" align="left">
       <form id="form_nuevo" action="" method="post">
         <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
           <tr>
             <td width="15%" align="right">Nota:</td>
             <td width="*">
               <input id="re_calificacion" type="text" class="inputPequenio" name="re_calificacion" onkeypress="return permite(event,'num')" />
             </td>
           </tr>
           <tr>
             <td colspan="2">
               <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                 <tr>
                   <td width="15%" align="right">
                      <div id="boton_accion" class="link_form">
                         <!-- Aqui va el enlace para actualizar la calificacion mal ingresada -->
                         <a href="#" onclick="actualizarCalificacionErronea()">Actualizar</a>
                      </div>   
                   </td>
                   <td width="5%" align="right">
                      <div id="limpiarAporteEvaluacion" class="link_form">
                         <a href="#" onclick="limpiarCalificacionErronea()">Limpiar</a>
                      </div>
                   </td>
                   <td width="5%" align="right">
                      <div class="link_form"><a href="#" onclick="salirEdicionCalificacion()">Salir</a></div>
                   </td>
                   <td width="*">
                      <div id="img-loader" style="padding-left:2px"></div>
                   </td>
                 </tr>
               </table>
             </td>
           </tr>
         </table>
         <input type="hidden" id="id_rubrica_estudiante" name="id_rubrica_estudiante" />
       </form>
     </div>
   </div>
   <div id="img_loader" style="text-align:center"> </div>
   <div id="total_calificaciones_erroneas" class="paginacion" style="display:none;">
   	  &nbsp;N&uacute;mero de calificaciones mal ingresadas:
   </div>
   <div id="tituloCalificacionesErroneas" class="header2" style="display:none;"> LISTA DE CALIFICACIONES MAL INGRESADAS </div>
   <div id="cabecera_calificaciones" class="cabeceraTabla" style="display:none;">
     <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
        <tr class="cabeceraTabla">
            <td width="5%">Nro.</td>
            <td width="15%">Asignatura</td>
            <td width="15%">Estudiante</td>
            <td width="15%">Curso</td>
            <td width="15%">Per&iacute;odo</td>
            <td width="15%">Aporte</td>
            <td width="15%">R&uacute;brica</td>
            <td width="5%">Nota</td>
        </tr>
     </table>
   </div>
   <div id="lista_calificaciones_erroneas" style="text-align:center"> </div>
   <!-- <div id="mensaje_slideToggle" class="paginacion">
       <div id="mostrar_ocultar_asignaturas" class="link_form" style="text-align:right;padding-right:2px;">
            <a href="#">Ocultar la lista de asignaturas</a>
       </div>
   </div> -->
   <div id="pag_asignaturas">
      <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
      <div id="total_registros" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_asignaturas">&nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_asignaturas"> 
                    	<!-- Aqui va la paginacion de asignaturas --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="39%" align="left">Asignatura</td>
                <td width="32%" align="left">Curso</td>
                <td width="6%" align="left">Paralelo</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_asignaturas" style="text-align:center"> </div>
   </div>
   <!--<div id="mensaje_rubrica" class="error" style="text-align:center"></div>-->
   <div id="pag_nomina_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="total_registros_estudiantes" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes:&nbsp;</div>
                </td>
                <td>
                	<div id="leyendas_rubricas"> 
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
      <form id="formulario_rubrica" action="reportes/reporte_por_aporte.php" method="post" target="_blank">
      	 <div id="img_loader_estudiantes" style="text-align:center"> </div>
         <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_asignatura" name="id_asignatura" type="hidden" />
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
         </div>
      </form>
   </div>
</div>
</body>
</html>
