<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		cargarAsignaturasDocente();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			cargarRubricasEvaluacion();
		});
		$("#cboRubricasEvaluacion").change(function(e){
			$("#form_rubrica_estudiante").css("display","none");
			$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
		});
		$("#boton_salir").click(function(e){
			e.preventDefault();
			$("#mensaje_rubrica").html("");
			$("#form_rubrica_estudiante").css("display","none");
		});
		$("#form_rubrica_estudiante").css("display","none");
	});

	function sel_texto(input) {
		$(input).select();
	}

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
					$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
				}
			}
		);
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		document.getElementById("cboRubricasEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				$("#cboAportesEvaluacion").append(resultado);
				$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
				//salirCriterio(false);
			}
		);
	}

	function cargarRubricasEvaluacion()
	{
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		document.getElementById("cboRubricasEvaluacion").options.length=1;
		$.get("scripts/cargar_rubricas_evaluacion.php", { id_aporte_evaluacion: id_aporte_evaluacion },
			function(resultado)
			{
				$("#cboRubricasEvaluacion").append(resultado);
				$("#lista_estudiantes_paralelo").html("Debe elegir una r&uacute;brica de evaluaci&oacute;n...");
				//salirCriterio(false);
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
		var cantidad_registros = $("#cantidad_registros").val();
		var numero_pagina = $("#numero_pagina").val();
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
	
	function cargarEstudiantesParalelo(id_paralelo, id_asignatura)
	{
		contarEstudiantesParalelo(id_paralelo, id_asignatura);	//Esta funcion desencadena las demas funciones de paginacion 
	}

	function contarEstudiantesParalelo(id_paralelo, id_asignatura)
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
					paginarEstudiantesParalelo(1,total_registros,id_paralelo,id_asignatura);
				}
			}
		);
	}

	function paginarEstudiantesParalelo(numero_pagina,total_registros,id_paralelo,id_asignatura)
	{
		$("#paginacion_estudiantes").html("");
		document.getElementById("numero_pagina").value = numero_pagina;
		$.post("calificaciones/paginar_estudiantes_paralelo.php",
			{
				cantidad_registros: 7,
				numero_pagina: numero_pagina,
				total_registros: total_registros,
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
				$("#paginacion_estudiantes").html(resultado);
			}
		);
		listarEstudiantesParalelo(numero_pagina,id_paralelo,id_asignatura);
	}
	
	function listarEstudiantesParalelo(numero_pagina,id_paralelo,id_asignatura)
	{
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		$("#form_rubrica_estudiante").css("display","none");
		$.post("calificaciones/listar_estudiantes_paralelo.php", 
			{ 
				cantidad_registros: 7,
				numero_pagina: numero_pagina,
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_rubrica_evaluacion: id_rubrica_evaluacion
			},
			function(resultado)
			{
				$("#lista_estudiantes_paralelo").html(resultado);
			}
		);
	}

	function seleccionarParalelo(id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		if (id_rubrica_evaluacion == 0) {
			$("#mensaje").html("Debe elegir una r&uacute;brica de evaluaci&oacute;n...");
		} else {
			$("#mensaje").html("");
			$("#tituloNomina").html("NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]");
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			cargarEstudiantesParalelo(id_paralelo, id_asignatura);
		}
	}

	function editarRubricaEstudiante(id_estudiante,id_paralelo,id_asignatura,id_rubrica_personalizada,apellidos,nombres)
	{
		var id_rubrica_evaluacion = document.getElementById("cboRubricasEvaluacion").value;
		document.getElementById("id_estudiante").value=id_estudiante;
		document.getElementById("id_paralelo").value=id_paralelo;
		document.getElementById("id_asignatura").value=id_asignatura;
		document.getElementById("id_rubrica_personalizada").value=id_rubrica_personalizada;
		$.post("calificaciones/buscar_rubrica_estudiante.php",
			{
				id_estudiante: id_estudiante,
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_rubrica_evaluacion: id_rubrica_evaluacion,
			},
			function(resultado)
			{
				var JSONRubrica = eval('(' + resultado + ')');
				editar_rubrica_estudiante(apellidos,nombres,JSONRubrica.existe);
			}
		);	
	}

	function editar_rubrica_estudiante(apellidos,nombres,existe)
	{
		var id_rubrica_evaluacion = $("#cboRubricasEvaluacion").val();
		var id_estudiante = $("#id_estudiante").val();
		var id_asignatura = $("#id_asignatura").val();
		var id_paralelo = $("#id_paralelo").val();
		if (!existe) {
			$("#tituloForm").html("NUEVA RUBRICA [" + apellidos + " " + nombres + "]");
			html = "<div class=\"link_form\"><a href=\"#\" onclick=\"operarRubricaEstudiante(1)\">Insertar</a></div>";
		} else {
			$("#tituloForm").html("EDITAR RUBRICA [" + apellidos + " " + nombres + "]");
			html = "<div class=\"link_form\"><a href=\"#\" onclick=\"operarRubricaEstudiante(2)\">Actualizar</a></div>";
		}
		$("#boton_accion").html(html);
		$.post("calificaciones/obtener_criterio_personalizado.php",
			{
				id_rubrica_evaluacion: id_rubrica_evaluacion,
				id_estudiante: id_estudiante,
				id_asignatura: id_asignatura,
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				if(resultado) { // Solo si existe resultado
					$("#lst_criterios_estudiantes").html(resultado);
				}
			}
		);
		$("#form_rubrica_estudiante").fadeIn("slow");
	}

	function editarCriterioEstudiante(obj) {
		// Aqui va el codigo para actualizar la calificacion de cada criterio personalizado
		// Primero debemos validar el rango permitido
		var id = obj.id;
		var valor = parseFloat(obj.value);
		if (isNaN(obj.value)) {
			alert("Debe digitar una calificacion valida");
			obj.value="0.00";
		} 
		if (valor < 0 || valor > 10) {
			alert("Calificacion fuera de rango (Debe ser mayor que cero y menor o igual a diez)");
			obj.value="0.00";
		} 
		var posfijo = id.substr(id.length-1, 1);
		var ponderacion = document.getElementById("ponderacion"+posfijo);
		var total = document.getElementById("total"+posfijo);
		total.value = obj.value * ponderacion.value;
		var total_rubrica = document.getElementById("total_rubrica");
		var suma_total_rubrica = 0;
		var frmFormulario = document.forms["formulario_rubrica"];
		for (var iCont=0; iCont < frmFormulario.length; iCont++) {
			var objElemento=frmFormulario.elements[iCont];
			if(objElemento.type=='text') {
				var cadena = objElemento.id;
				if(cadena.substr(0, cadena.length-1) == "total") {
					suma_total_rubrica += parseFloat(objElemento.value);
				}
			}
		}
		total_rubrica.value = suma_total_rubrica;
	}
	
	function operarRubricaEstudiante(accion)
	{
		//Aqui va el codigo para insertar/actualizar la rubrica evaluada del estudiante
		var id_estudiante = document.getElementById("id_estudiante").value;
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("id_asignatura").value;
		var id_rubrica_personalizada = document.getElementById("id_rubrica_personalizada").value;
		var re_calificacion = document.getElementById("total_rubrica").value;
		var re_fec_entrega = document.getElementById("re_fec_entrega").value;
		var id_rubrica_estudiante = document.getElementById("id_rubrica_estudiante").value;
		var numero_pagina = document.getElementById("numero_pagina").value;
		
		document.getElementById("mensaje").innerHTML=""; // borro cualquier mensaje anterior
		document.getElementById("mensaje_rubrica").innerHTML=""; // borro cualquier mensaje anterior

		if(re_calificacion==0) {
			$("#mensaje_rubrica").html("La calificacion de la rubrica no puede ser cero...");
			$("#puntaje1").focus();
			$("#puntaje1").select();
		} else {
			// Recorrer el formulario para formar la cadena de puntajes
			var cadena_puntajes = "";
			var cadena_ids_personalizado = "";
			var cadena_ids_estudiante = "";
			var frmFormulario = document.forms["formulario_rubrica"];
			for (var iCont=0; iCont < frmFormulario.length; iCont++) {
				var objElemento=frmFormulario.elements[iCont];
				if(objElemento.type=='text') {
					var cadena = objElemento.id;
					if(cadena.substr(0, cadena.length-1) == "puntaje") 
						cadena_puntajes += objElemento.value + ",";
				}
				if(objElemento.type=='hidden') {
					var cadena = objElemento.id;
					if(cadena.substr(0, cadena.length-1) == "id_criterio_personalizado") 
						cadena_ids_personalizado += objElemento.value + ",";
					if(cadena.substr(0, cadena.length-1) == "id_criterio_estudiante") 
						cadena_ids_estudiante += objElemento.value + ",";
				}
			}
			cadena_puntajes = cadena_puntajes.substr(0, cadena_puntajes.length-1);
			cadena_ids_personalizado = cadena_ids_personalizado.substr(0, cadena_ids_personalizado.length-1);
			cadena_ids_estudiante = cadena_ids_estudiante.substr(0, cadena_ids_estudiante.length-1);

			$("#form_rubrica_estudiante").fadeOut("slow", function() {
				$("#mensaje_rubrica").html("<img src='imagenes/ajax-loader5.gif' alt='procesando...' />");
				$.post("calificaciones/editar_rubrica_estudiante.php",
					{
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_rubrica_personalizada: id_rubrica_personalizada,
						re_calificacion: re_calificacion,
						re_fec_entrega: re_fec_entrega,
						cadena_puntajes: cadena_puntajes,
						cadena_ids_personalizado: cadena_ids_personalizado,
						cadena_ids_estudiante: cadena_ids_estudiante,
						id_rubrica_estudiante: id_rubrica_estudiante,
						accion: accion
					},
					function(resultado)
					{
						if(resultado) { // Solo si existe resultado
							$("#mensaje_rubrica").html(resultado);
							listarEstudiantesParalelo(numero_pagina,id_paralelo,id_asignatura);
						}					
					}
				);
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
			<td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> Aporte:&nbsp; </td>
            <td width="5%"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> R&uacute;brica:&nbsp; </td>
            <td width="5%"> <select id="cboRubricasEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="id_estudiante" type="hidden" />
      <input id="id_paralelo" type="hidden" />
      <input id="id_asignatura" type="hidden" />
      <input id="id_rubrica_personalizada" type="hidden" />
      <input id="numero_pagina" type="hidden" />
    </div>
   <div id="mensaje" class="error"></div>
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
   <form id="formulario_rubrica" action="" method="post">
       <div id="form_rubrica_estudiante" class="form_nuevo">
          <div id="tituloForm" class="header"><!--Editar R&uacute;brica Estudiante--></div>
          <div id="fec_entrega" class="fuente8" style="text-align:center;padding:2px;">
             Fecha de Entrega:&nbsp;
             <input id="re_fec_entrega" name="re_fec_entrega" type="text" class="cajaPequenia" value="<?php echo date("Y-m-d"); ?>" onclick="this.select()" />
             <img src="imagenes/calendario.png" id="calendario1" name="calendario1" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor=cursor" />
             <script type="text/javascript">
                Calendar.setup(
                  {
                    inputField : "re_fec_entrega",
                    ifFormat   : "%Y-%m-%d",
                    button     : "calendario1"
                  }
                );
             </script>
          </div>
          <div id="lst_criterios_estudiantes"><!--Aqui iran los criterios de evaluacion para cada estudiante--></div>
          <div id="botones">
             <table width="100%" cellpadding="2px" cellspacing="0px">
                <tr>
                   <td width="50%" align="right">
                      <div id="boton_accion"> </div>
                   </td>
                   <td>
                      <div id="boton_salir" class="link_form"><a href="#">Salir</a></div>
                   </td>
                </tr>
             </table> 
          </div>
       </div>
   </form>
   <div id="mensaje_rubrica" class="error" style="text-align:center"></div>
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
                <td width="24%" align="left">Apellidos</td>
                <td width="24%" align="left">Nombres</td>
                <td width="21%" align="center">Calificaci&oacute;n</td>
                <td width="21%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
