<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
	});

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
					//$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
				}
			}
		);
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE DE CALIFICACIONES " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div>
</div>
</body>
</html>
