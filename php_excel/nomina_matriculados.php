<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
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

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div id="barra_principal">
      <form id="formulario_periodo" action="php_excel/reporte_nomina_matriculados.php" method="post">
          <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
             <tr>
                <td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
                <td width="5%"> <select id="cboParalelos" name="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="*"> <input type="submit" value="Exportar a Excel" /> </td>
             </tr>
          </table>
      </form>
    </div>
   </div>
   <div id="mensaje" class="error"></div>
</div>
</body>
</html>
