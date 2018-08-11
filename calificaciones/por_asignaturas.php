<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		cargarParalelos();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			listarAportesEvaluacion();
			$("#lista_estudiantes_paralelo").hide();
			document.getElementById("cboParalelos").value = 0;
			$("#mensaje").html("Debe elegir un paralelo...");
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			cargarAsignaturas($(this).val());
			$("#lista_estudiantes_paralelo").hide();
			$("#mensaje").html("Debe elegir una asignatura...");
		});
		$("#cboAsignaturas").change(function(e){
			e.preventDefault();
			$("#lista_estudiantes_paralelo").hide();
			$("#mensaje").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
		});
		$("#mensaje").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
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

	function cargarParalelos()
	{
		$.get("scripts/cargar_paralelos.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboParalelos").append(resultado);
				}
			}
		);
	}

	function cargarAsignaturas(id_paralelo)
	{
		$.post("scripts/cargar_asignaturas_por_paralelo.php", 
			{
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					document.getElementById("cboAsignaturas").length = 1;
					$("#cboAsignaturas").append(resultado);
				}
			}
		);
	}

	function listarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		$.post("scripts/listado_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_periodos_evaluacion").html(resultado);
				}
			}
		);
	}

	function seleccionarAporteEvaluacion(id_aporte_evaluacion)
	{
		// Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
		if(id_aporte_evaluacion=="") {
			$("#mensaje").html("No se ha pasado el par&aacute;metro (id_aporte_evaluacion)...");
		} else if(id_periodo_evaluacion==0) {
			$("#mensaje").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
			$("#cboPeriodosEvaluacion").focus();
		} else if(id_paralelo==0) {
			$("#mensaje").html("Debe elegir un paralelo...");
			$("#cboParalelos").focus();
		} else if(id_asignatura==0) {
			$("#mensaje").html("Debe elegir una asignatura...");
			$("#cboAsignaturas").focus();
		} else {
			mostrarTitulosRubricas(id_aporte_evaluacion);
			// Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
			cargarEstudiantesParalelo(id_paralelo, id_asignatura);
		}
	}

	function mostrarTitulosRubricas(id_aporte_evaluacion)
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		$.post("calificaciones/mostrar_titulos_rubricas.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
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
					$("#txt_rubricas").html(resultado);
				}
			}
		);
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
					listarEstudiantesParalelo(id_paralelo,id_asignatura);
				}
			}
		);
	}

	function listarEstudiantesParalelo(id_paralelo,id_asignatura)
	{
		var id_aporte_evaluacion = document.getElementById("id_aporte_evaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		$("#form_rubrica_estudiante").css("display","none");
		$("#mensaje").html("");
		$("#img_loader").show();
		$("#lista_estudiantes_paralelo").hide();
		$.post("scripts/listar_estudiantes_paralelo.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_aporte_evaluacion: id_aporte_evaluacion,
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				$("#img_loader").hide();
				$("#lista_estudiantes_paralelo").show();
				$("#lista_estudiantes_paralelo").html(resultado);
			}
		);
	}
	
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="5%" class="fuente9"> &nbsp;Asignatura:&nbsp; </td>
            <td width="5%"> <select id="cboAsignaturas" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*">&nbsp;  </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" value="1" />
    </div>
    <div id="pag_periodo_evaluacion">
      <!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
      <div class="header2"> LISTA DE APORTES DE EVALUACION </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="72%" align="left">Nombre</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_periodos_evaluacion" style="text-align:center"> </div>
    </div>
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
      <div id="img_loader" style="display:none;text-align:center">
	     <img src="imagenes/ajax-loader.gif" alt="Procesando...">  
      </div>
      <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
      <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
   </div>
   <div id="mensaje" class="error" align="center"> </div>
</div>
</body>
</html>
