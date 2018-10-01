<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
		$("#cboParalelos").change(function(e) {
			var id_paralelo = $(this).val();
			document.getElementById("id_paralelo").value = id_paralelo;
			if (id_paralelo == 0) 
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
            <?php echo "REPORTES " . $_SESSION['titulo_pagina'] ?>
        </div>
        <div id="barra_principal">
        <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
                <td width="50%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="*">&nbsp;  </td>
            </tr>
        </table>
        </div>
        <form id="formulario_periodo" action="php_excel/reporte_anual_autoridad.php" method="post" target="_self">
            <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input type="submit" value="Ver Reporte" />
            </div>
        </form>
    </div>
</body>
</html>
