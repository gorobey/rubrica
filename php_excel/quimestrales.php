<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
		cargarPeriodosEvaluacion();
		$("#cboParalelos").on("change", function(e) {
			e.preventDefault();
			var id_paralelo = $("#cboParalelos").val();
			document.getElementById("id_paralelo").value = id_paralelo;
		});
		$("#cboPeriodosEvaluacion").on("change", function(e) {
			e.preventDefault();
			var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
			document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
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
    	<?php echo "REPORTES " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div id="barra_principal">
      <form id="formulario_periodo" action="php_excel/reporte_quimestral.php" method="post">
          <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
             <tr>
                <td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
                <td width="5%"> 
                    <select id="cboPeriodosEvaluacion" class="fuente8"> 
                        <option value="0"> Seleccione... </option> 
                    </select> 
                </td>
                <td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
                <td width="5%"> <select id="cboParalelos" name="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="*"> <input type="submit" value="Exportar a Excel" /> </td>
                <input type="hidden" id="id_paralelo" name="id_paralelo" />
                <input type="hidden" id="id_periodo_evaluacion" name="id_periodo_evaluacion" />
             </tr>
          </table>
      </form>
    </div>
   </div>
   <div id="mensaje" class="error"></div>
</div>
</body>
</html>
