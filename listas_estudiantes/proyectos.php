<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		cargarClubesDocente();
		$("#cboPeriodosEvaluacion").change(function(e){
			cargarAportesEvaluacion();
			$("#lista_estudiantes_paralelo").html("");
		});
		$("#cboAportesEvaluacion").change(function(e){
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un proyecto...");
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
					alert("Error...");
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
					$("#num_asignaturas").html("N&uacute;mero de Proyectos encontrados: "+total_registros);
					//paginarAsignaturasDocente(4,1,total_registros);
					listarClubesDocente(1);
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
		//listarAsignaturasDocente(num_pagina);
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
					$("#lista_asignaturas").html(resultado);
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
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		} else if (id_aporte_evaluacion == 0 && $('#div_combo_aportes').is(':visible')) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
		} else {
			$("#lista_estudiantes_paralelo").html("");
			document.getElementById("formulario_rubrica").submit();
		}
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "LISTAS DE ESTUDIANTES " . $_SESSION['titulo_pagina'] ?>
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
        <input id="numero_pagina" type="hidden" value="1" />
      </div>
      <div class="header2"> LISTA DE PROYECTOS ASOCIADOS </div>
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
      <div id="lista_asignaturas" style="text-align:center"> </div>
      <form id="formulario_rubrica" action="reportes/listas_por_parcial_proyectos.php" method="post" target="_blank">
      	 <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_club" name="id_club" type="hidden" />
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <!--<input type="submit" value="Ver Reporte" />-->
         </div>
      </form>
    </div>
</div>
</body>
</html>
