<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.post("scripts/obtener_id_paralelo_tutor.php", 
			function(resultado) {
				var JSONIdParalelo = eval('(' + resultado + ')');
				$("#id_paralelo").val(JSONIdParalelo.id_paralelo);
				// Luego obtengo el nombre del paralelo
				$.post("tutores/obtener_nombre_paralelo.php",
					{
						id_paralelo: $("#id_paralelo").val()
					},
					function(resultado)
					{
						if(resultado == false)
						{
							alert("Error");
						}
						else
						{
							$("#titulo_pagina").html("COMPORTAMIENTO DE PARCIALES DE " + resultado);
						}
					}
				);
			}
		);
		cargarPeriodosEvaluacion();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			var id_periodo_evaluacion = $(this).find('option:selected').val();
			if(id_periodo_evaluacion==0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display","none");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				cargarAportesEvaluacion();
			}
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			var id_aporte_evaluacion = $(this).find('option:selected').val();
			document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
			var id_paralelo = $("#id_paralelo").val();
			// Aqui va el codigo para desplegar las calificaciones del comportamiento
			listarEstudiantesParalelo(id_paralelo,id_aporte_evaluacion);
			$("#ver_reporte").css("display","block");
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

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_principales_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
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

	function sel_texto(input) {
		$(input).select();
	}
	
	function listarEstudiantesParalelo(id_paralelo,id_aporte_evaluacion)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		if(id_periodo_evaluacion==0) {
			$("#lista_estudiantes_paralelo").html("Debe escoger un per&iacute;odo de evaluaci&oacute;n...");
		} else if(id_paralelo==0) {
			$("#lista_estudiantes_paralelo").html("No se ha pasado correctamente el id_paralelo...");
		} else if(id_aporte_evaluacion==0) {
			$("#lista_estudiantes_paralelo").html("No se ha pasado correctamente el id_aporte_evaluacion...");
		} else {
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader-red-dog.GIF' alt='procesando...' />");
			$.post("inspeccion/listar_estudiantes_parciales_paralelo.php", 
				{ 
					id_paralelo: id_paralelo,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado)
				{
					console.log(resultado);
                    $("#lista_estudiantes_paralelo").removeClass("error");
					$("#lista_estudiantes_paralelo").html(resultado);
					$("#ver_reporte").css("display","block");
				}
			);
		}
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<!-- Aqui va el nombre del curso que corresponde al tutor -->
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
			<td width="5%" class="fuente9" align="right">Aporte:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboAportesEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>            
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div>
    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
    <div class="cabeceraTabla">
        <!--<table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="385px">&nbsp;</td>
                <td width="240px" align="center">TUTORES</td>
                <td width="240px" align="center">INSPECTORES</td>
                <td width="*">&nbsp;</td> -- Esto es para igualar las columnas --
            </tr>
        </table>--> 
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="45%" align="left">N&oacute;mina</td>
                <td width="10%" align="left">DOCENTES</td>
                <td width="10%" align="left">INSPECTOR</td>
                <td width="30%" align="left">PROMEDIO</td>
                <td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
            </tr>
        </table>
    </div>
    <form id="formulario_comportamiento" action="reportes/reporte_comportamiento_parciales.php" method="post" target="_blank">
		<div id="lista_estudiantes_paralelo" style="text-align:center"> Debe seleccionar un per&iacute;odo de evaluaci&oacute;n... </div>
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>
</body>
</html>