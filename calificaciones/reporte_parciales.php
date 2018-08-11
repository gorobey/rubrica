<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
		cargarPeriodosEvaluacion();
		$("#input_checkbox").hide();
		$("#ver_reporte").hide();
		$("#mensaje").html("Debe seleccionar un Per&iacute;odo...");
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
			$("#input_checkbox").hide();
			$("#ver_reporte").hide();
			$("#cboAportes").val(0);
			$("#cboParalelos").val(0);
			$("#mensaje").html("Debe elegir un aporte de evaluaci&oacute;n...");
			$("#id_periodo_evaluacion").val($(this).find(":selected").val());
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			if ($("#cboAportesEvaluacion").val()==0) {
				$("#mensaje").html("Debe seleccionar un Aporte de Evaluaci&oacute;n...");
				$("#cboAportesEvaluacion").focus();
			} else {
				$("#id_aporte_evaluacion").val($(this).val());
				$("#mensaje").html("Debe seleccionar un Paralelo...");
			}
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			if ($("#cboPeriodosEvaluacion").val()==0) {
				$("#mensaje").html("Debe seleccionar un Per&iacute;odo...");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				$("#id_paralelo").val($(this).val());
				if ($(this).val()==0) {
					$("#input_checkbox").hide();
					$("#ver_reporte").hide();
				} else {
					$("#input_checkbox").show();
					$("#ver_reporte").show();
				}
			}
		});
		$("#imprimir_para_juntas").click(function(e){
			var chequeado = 0;
			var checkbox = document.getElementById("impresion_para_juntas");
			if($(this).is(':checked'))
				chequeado = 1;
			else
				chequeado = 0;
			checkbox.value = chequeado;
		});
	});

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
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").find(":selected").val();
		$.get("scripts/cargar_aportes_evaluacion.php", 
			{ 
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					document.getElementById("cboAportesEvaluacion").length = 1;
					$("#cboAportesEvaluacion").append(resultado);
				}
			}
		);
	}

</script>
<style type="text/css">
	.estilo_barra_principal {
		background:#f5f5f5;
		height:25px;
		padding-top:4px;
	}
</style>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div class="estilo_barra_principal">
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
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="40%" class="fuente9">
              <form id="formulario_periodo" action="php_excel/reporte_por_parcial.php" method="post" target="_self">
                 <table width="100%" cellpadding="0" cellspacing="0" border="0">
                 	<tr>
                        <td>
                 			<div id="ver_reporte" style="text-align:left;">
                                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                                <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                                <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                                <input type="submit" value="Ver Reporte" />
                 			</div>
                        <td>
                    </tr>
                 </table>        
              </form>
            </td>
            <td width="*">&nbsp;</td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" value="1" />
    </div>
    <div id="mensaje" class="error"></div>
</div>
</body>
</html>
