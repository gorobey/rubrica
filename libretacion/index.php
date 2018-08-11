<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_paralelos();
		cargarPeriodosEvaluacion();
	});

	function cargar_paralelos()
	{
		$.get("scripts/cargar_paralelos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboParalelos').append(resultado);			
			}
		});	
	}

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
            <td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
            <td> <select id="cboParalelos" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td class="fuente9">&nbsp;Per&iacute;odo: &nbsp;</td>
            <td> <select id="cboPeriodosEvaluacion" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="imprimir_libreta" class="boton" style="display:block"> <a href="#"> Imprimir Libreta </a> </div> </td>
         </tr>
      </table>
    </div>
</div>
</body>
</html>
