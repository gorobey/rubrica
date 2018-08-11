<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../aportes_evaluacion/calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../aportes_evaluacion/js/funciones.js"></script>
<script type="text/JavaScript" language="javascript" src="../aportes_evaluacion/calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="../aportes_evaluacion/calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="../aportes_evaluacion/calendario/calendar-setup.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarPeriodosLectivos();
		$("#cboPeriodosLectivos").change(function(e){
			e.preventDefault();
			var id_periodo_lectivo = $(this).val();
			if(id_periodo_lectivo==0) {
				$("#mensaje").html("Debe seleccionar un Periodo Lectivo...");
			} else {
				// Aquí toca cargar los cierres definidos para los periodo lectivos existentes
				listarCierresPeriodosLectivos(false);
			}
		});
	});

	function sel_texto(input) {
		$(input).select();
	}

	function cargarPeriodosLectivos()
	{
		$.get("scripts/cargar_periodos_lectivos.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosLectivos").append(resultado);
				}
			}
		);
	}
	
	function cargarAportesEvaluacion(iDesplegar)
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_curso = $("#cboCurso").val();
		if(id_periodo_evaluacion==0) {
			$("#mensaje").css("color","red");
			$("#mensaje").html("Debe seleccionar un Per&iacute;odo de Evaluaci&oacute;n...");
			$("#lista_aportes_evaluacion").html("");
			$("#cboPeriodosEvaluacion").focus();
		} else if(id_curso==0) {
			$("#mensaje").css("color","red");
			$("#mensaje").html("Debe seleccionar un Curso...");
			$("#lista_aportes_evaluacion").html("");
			$("#cboCurso").focus();

		} else {
			$.ajax({
				type: "POST",
				url: "aportes_evaluacion/listar_aportes_estados.php",
				data: "id_periodo_evaluacion="+id_periodo_evaluacion+"&id_curso="+id_curso,
				success: function(resultado){
					if(!iDesplegar) $("#mensaje").html("");
					$("#lista_aportes_evaluacion").html(resultado);
				}
			});
		}
	}

	function cerrarAporteEvaluacion(id, estado)
	{
		// Procedimiento para cerrar un aporte de evaluacion
		if(estado=='A') {
			estado = 'C';
			mensaje = 'cerrar';
		} else {
			estado = 'A';
			mensaje = 'reabrir';
		}
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_curso = $("#cboCurso").val();
		if(id_periodo_evaluacion==0) {
			$("#mensaje").css("color","red");
			$("#mensaje").html("Debe seleccionar un peri&oacute;do de evaluaci&oacute;n...");
			$("#cboPeriodosEvaluacion").focus();
		} else if(id==0) {
			$("#mensaje").css("color","red");
			$("#mensaje").html("Error al pasar el par&aacute;metro id_aporte_evaluacion...");
		} else {
			var cerrar = confirm("¿Seguro que desea " + mensaje + " este aporte?")
			if (cerrar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
					type: "POST",
					url: "aportes_evaluacion/cerrar_aporte_evaluacion.php",
					data: "id_aporte_evaluacion="+id+"&id_curso="+id_curso+"&estado="+estado,
					success: function(resultado){
						$("#mensaje").css("color","blue");
						$("#mensaje").html(resultado);
						cargarAportesEvaluacion(true);
					}
				});
			}
		}
	}

	function actualizarAporteEvaluacion(id, nombre, fecha_apertura, fecha_cierre)
	{
		var txt_fecha_apertura = fecha_apertura.value;
		var txt_fecha_cierre = fecha_cierre.value;
		var id_curso = $("#cboCurso").val();
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.ajax({
			type: "POST",
			url: "aportes_evaluacion/actualizar_fechas_aporte_evaluacion.php",
			data: "id_aporte_evaluacion="+id+"&id_curso="+id_curso+"&ap_nombre="+nombre+"&fecha_apertura="+txt_fecha_apertura+"&fecha_cierre="+txt_fecha_cierre,
			success: function(resultado){
				$("#mensaje").css("color","blue");
				$("#mensaje").html(resultado);
				cargarAportesEvaluacion(true);
			}
		});
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
			<td width="7%" class="fuente9" align="right"> A&ntilde;o Lectivo:&nbsp; </td>
            <td width="10%"> <select id="cboPeriodosLectivos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
         </tr>
      </table>
    </div>
    <div id="pag_periodo_lectivo">
      <!-- Aqui va la paginacion de los periodos lectivos encontrados -->
      <div class="header2"> LISTA DE PERIODOS LECTIVOS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="16%" align="left">Nombre</td>
                <td width="16%" align="left">Fecha de Apertura</td>
                <td width="16%" align="left">Fecha de Cierre</td>
                <td width="24%" align="left">Estado</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_periodos_lectivos" style="text-align:center"> </div>
    </div>
    <div id="mensaje" class="mensaje">Debe seleccionar un Per&iacute;odo Lectivo...</div>
</div>
</body>
</html>
