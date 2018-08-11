<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
		$("#ver_reporte").hide();
		$("#cboParalelos").change(function(e) {
			e.preventDefault();
			document.getElementById('id_paralelo').value = $(this).val();
			if($(this).val()==0) 
				$("#ver_reporte").hide();
			else
				$("#ver_reporte").show();
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
	
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
    <form id="formulario_periodo" action="reportes/reporte_de_gracia.php" method="post" target="_blank">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*"> 
               <div id="ver_reporte" style="margin-left:2px;"> 
                  <input type="submit" value="Ver Reporte" /> 
               </div> 
            </td>
         </tr>
      </table>
      <input id="id_paralelo" name="id_paralelo" type="hidden" />
    </form>
</div>
</body>
</html>
