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
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			document.getElementById("id_periodo_evaluacion").value = $(this).val();
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			document.getElementById("id_paralelo").value = $(this).val();
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
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <form id="formulario_libretas" action="reportes/reporte_libretas.php" method="post" target="_blank">
          <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
             <tr>
                <td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
                <td width="5%"> 
                    <select id="cboPeriodosEvaluacion" class="fuente8"> 
                        <option value="0"> Seleccione... </option> 
<!--                        <option value="1"> PRIMER QUIMESTRE </option> 
                        <option value="2"> SEGUNDO QUMIESTRE </option> 
-->                    </select> 
                </td>
                <td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
                <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="5%"> <input type="submit" value="Ver Reporte" /> </td>
                <td width="*">&nbsp;  </td>
             </tr>
          </table>
          <input id="id_paralelo" name="id_paralelo" type="hidden" />
		  <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
	  </form>
    </div>
</div>
</body>
</html>
