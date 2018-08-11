<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		cargarAsignaturasDocente();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").find('option:selected').val();
			if(id_periodo_evaluacion!=0) {
				$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
			}
		});
	});

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

	function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
			document.getElementById("cboPeriodosEvaluacion").focus();
		} else {
			$("#lista_estudiantes_paralelo").html("");
			$("#tituloNomina").html("COMPORTAMIENTO QUIMESTRAL [" + asignatura + " - " + curso + " " + paralelo + "]");
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader-red-dog.GIF' alt='procesando...' />");
			$.post("docentes/listar_estudiantes_paralelo.php", 
				{ 
					id_paralelo: id_paralelo,
					id_periodo_evaluacion: id_periodo_evaluacion,
					id_asignatura: id_asignatura
				},
				function(resultado)
				{
					$("#lista_estudiantes_paralelo").html(resultado);
				}
			);
		}		
	}	

	function sel_texto(input) {
		$(input).select();
	}

	function editarCalificacion(obj,id_estudiante,id_paralelo,id_asignatura,id_indice_evaluacion)
	{
		var calificacion = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		var suma_total = 0; 
		var promedio_comportamiento = 0;
		var contador_calificaciones = 0;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var frmFormulario = document.forms["formulario_comportamiento"];
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
						if(id_elem.substr(0, id_elem.indexOf("_")) == "puntaje") {
							//Aqui calculo la suma de las calificaciones de cada estudiante
							suma_total += parseFloat(objElemento.value);
							contador_calificaciones++;
						} else {
							//Aqui calculo el promedio del comportamiento y salto
							promedio_comportamiento = suma_total / contador_calificaciones;
							document.getElementById("promedio_"+fila_elem).value = Math.round(promedio_comportamiento * 100) / 100;
							//Aqui debo calcular la equivalencia
							if(promedio_comportamiento==10)
								equivalencia = "A";
							else if(promedio_comportamiento>8.99 && promedio_comportamiento<10)
								equivalencia = "B";
							else if(promedio_comportamiento>6.99 && promedio_comportamiento<9)
								equivalencia = "C";
							else if(promedio_comportamiento>4.99 && promedio_comportamiento<7)
								equivalencia = "D";
							else
								equivalencia = "E";
							document.getElementById("equivalencia_"+fila_elem).value = equivalencia;	
						}
					}
				}
			}
			$.post("docentes/editar_calificacion_comportamiento.php",
				{
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_indice_evaluacion: id_indice_evaluacion,
					id_periodo_evaluacion: id_periodo_evaluacion,
					co_calificacion: calificacion
				},
				function(resultado)
				{
					if(resultado) { // Solo si existe resultado
						$("#mensaje").html(resultado);
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
    	<?php echo $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboPeriodosEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>
            <td width="*"> <div id="mensaje" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div>
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
    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
    <div class="cabeceraTabla">
    	<table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
        	<tr class="cabeceraTabla">
            	<td width="35px">Nro.</td>
                <td width="350px" align="center">N&oacute;mina</td>
                <td width="60px" align="center">VALORES</td>
                <td width="60px" align="center">NORMAS</td>
                <td width="60px" align="center">PUNTUAL</td>
                <td width="60px" align="center">PRESENT</td>
                <td width="60px" align="center">TOTAL</td>
                <td width="60px" align="center">PROM.</td>
                <td width="60px" align="center">EQUIV.</td>
                <td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
            </tr>
        </table>
    </div>
    <form id="formulario_comportamiento" action="" method="post">
	    <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
    </form>
</div>
</body>
</html>
