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
		$("#ver_reporte").hide();
		$("#mensaje").html("Debe seleccionar un Per&iacute;odo...");
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			$("#ver_reporte").hide();
			$("#cboParalelos").val(0);
			$("#mensaje").html("Debe elegir un paralelo...");
			$("#id_periodo_evaluacion").val($(this).find(":selected").val());
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			if ($("#cboPeriodosEvaluacion").val()==0) {
				$("#mensaje").html("Debe seleccionar un Per&iacute;odo...");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				$("#id_paralelo").val($(this).val());
				if ($(this).val()==0) {
					$("#ver_reporte").hide();
				} else {
					$("#ver_reporte").show();
				}
			}
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
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="40%" class="fuente9">
              <form id="formulario_periodo" action="php_excel/reporte_quimestral_autoridad.php" method="post">
                 <table width="100%" cellpadding="0" cellspacing="0" border="0">
                 	<tr>
                        <td>
                 			<div id="ver_reporte" style="text-align:left;">
                                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                                <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
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
