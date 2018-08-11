<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosEvaluacion();
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			listarRubricasEvaluacion();
		});
	});

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
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if (resultado == false) 
				{
					// Aqui va el mensaje de error de carga
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
				}
			}
		);
	}

	function listarRubricasEvaluacion()
	{
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		$.get("planificaciones/listar_rubricas.php", { id_aporte_evaluacion: id_aporte_evaluacion },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_rubricas_evaluacion").html(resultado);
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
                <td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
                <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="5%" class="fuente9" align="right"> <div id="label_combo_aportes"> Aporte:&nbsp; </div> </td>
                <td width="5%"> <div id="div_combo_aportes"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </div> </td>
                <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
            </tr>        
        </table>
    </div>
    <div id="mensaje" class="error"></div>
    <div id="pag_rubrica_evaluacion">
      <!-- Aqui va la paginacion de las rubricas de evaluacion encontradas -->
      <div class="header2"> LISTA DE RUBRICAS DE EVALUACION EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="36%" align="left">Nombre</td>
                <td width="36%" align="left">Abreviatura</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_rubricas_evaluacion" style="text-align:center"> Debe elegir un per&iacute;odo de evaluaci&oacute;n... </div>
    </div>
</div>
</body>
</html>
