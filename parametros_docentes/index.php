<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		$("#cboPeriodosEvaluacion").change(function(e){
			cargarAportesEvaluacion();
		});
		$("#cboAportesEvaluacion").change(function(e){
			cargarRubricasEvaluacion();
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
					$("#mensaje").addClass("error");
					$("#mensaje").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
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
					$("#mensaje").addClass("error");
					$("#mensaje").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#mensaje").addClass("error");
					$("#mensaje").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
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
				if (resultado == false) 
				{
					$("#mensaje").addClass("error");
					$("#mensaje").html("No existen r&uacute;bricas de evaluaci&oacute;n asociadas a este aporte de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboRubricasEvaluacion").append(resultado);
					$("#mensaje").addClass("error");
					$("#mensaje").html("Debe elegir una r&uacute;brica de evaluaci&oacute;n...");
				}
			}
		);
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
            <td width="5%" class="fuente9" align="right"> <div id="label_combo_aportes"> Aporte:&nbsp; </div> </td>
            <td width="5%"> <div id="div_combo_aportes"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </div> 
            </td>
            <td width="5%" class="fuente9" align="right"> R&uacute;brica:&nbsp; </td>
            <td width="5%"> <select id="cboRubricasEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
   </div>
   <div id="mensaje" style="text-align:center; overflow:auto"> </div>
</div>
</body>
</html>