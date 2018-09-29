<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacionPrincipales();
		cargarAsignaturasDocente();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			document.getElementById("cboAportesEvaluacion").value=0;
			cargarAportesEvaluacion();
			$("#ver_reporte").css("display","none");
			$("#escala_calificaciones").html("Debe elegir un aporte...");
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			$("#ver_reporte").css("display","none");
			$("#escala_calificaciones").html("Debe elegir una asignatura...");
		});
	});

	function cargarPeriodosEvaluacionPrincipales()
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

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_principales_evaluacion.php", 
			{ 
				id_periodo_evaluacion: id_periodo_evaluacion 
			},
			function(resultado)
			{
				$("#cboAportesEvaluacion").append(resultado);
				$("#escala_calificaciones").html("Debe elegir un aporte de evaluaci&oacute;n...");
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
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		if (id_aporte_evaluacion == 0) {
			$("#escala_calificaciones").html("Debe elegir un aporte de evaluaci&oacute;n...");
		} else {
			$("#escala_calificaciones").html("");
			$("#tituloNomina").html("ESCALA DE CALIFICACIONES [" + asignatura + " - " + curso + " " + paralelo + "]");
			$("#titulo").val("ESCALA DE CALIFICACIONES [" + asignatura + "]<br>" + curso + " " + paralelo);
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			cargarEscalaCalificaciones(id_paralelo, id_asignatura);
			$("#ver_reporte").css("display","block");
		}
	}
	
	function cargarEscalaCalificaciones(id_paralelo,id_asignatura)
	{
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
		$("#escala_calificaciones").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.ajax({
            url: "scripts/informe_parciales.php",
            type: "POST",
            data: {
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_aporte_evaluacion: id_aporte_evaluacion
			},
            dataType: "json",
            success: function(data){
				$("#escala_calificaciones").html("");
                var escalas = new Array();
                var porcentajes = new Array();
                $.each(data,function(key,value){
                    escalas.push(value.escala);
                    porcentaje = Number(value.porcentaje);
                    porcentajes.push(porcentaje);
                });
                graficar(escalas, porcentajes, "escala_calificaciones");
				$("#ver_reporte").css("display","block");
            }
        });
	}

	function graficar(escalas, porcentajes, idDiv)
	{
		var title = $("#titulo").val()
					+"<br>"
					+$("#cboPeriodosEvaluacion option:selected").text()
					+" - "
					+$("#cboAportesEvaluacion option:selected").text();
		var data = [{
			values: porcentajes,
			labels: escalas,
			hoverinfo: "label",
			type: 'pie',
			sort: false,
			marker: {
				colors: ['rgb(51, 153, 102)', 'rgb(255, 255, 0)', 'rgb(255, 192, 0)', 'rgb(255, 80, 80)']
			}
		}];

		var layout = {
            title: title,
			"titlefont": {
				"size": 12
			},
        };

		Plotly.newPlot(idDiv, data, layout);
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "INFORMES " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal" style="margin-top:2px">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboPeriodosEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>
			<td width="5%" class="fuente9" align="right"> &nbsp;Aporte:&nbsp; </td>
            <td width="5%"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*"> <div id="mensajes" class="error" style="margin-left:4px;"> </div> </td>
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
    <div id="mensaje_salida" class="text-center"></div>
    <div id="pag_nomina_estudiantes" style="margin-top:2px;">
      <div id="tituloNomina" class="header2 hidden"> ESCALA DE CALIFICACIONES </div>
	  <input type="hidden" id="titulo" value="">
      <form id="formulario_periodo" action="php_excel/informe_de_parciales.php" method="post">
      	 <div id="escala_calificaciones" style="text-align:center"> Debe elegir un per&iacute;odo de evaluaci&oacute;n.... </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_asignatura" name="id_asignatura" type="hidden" />
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input id="id_paralelo_asignatura" name="id_paralelo_asignatura" type="hidden"  />
            <input type="submit" value="Generar Informe" />
         </div>
      </form>
   </div>
</div>
</body>
</html>
