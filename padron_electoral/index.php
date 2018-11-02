<div id="pagina">
    <div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div id="barra_principal">
      <form id="formulario_periodo" action="php_excel/reporte_padron_electoral.php" method="post">
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