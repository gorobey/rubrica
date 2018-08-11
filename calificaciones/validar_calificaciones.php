<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			validar_calificaciones();
		});		
	});
	function validar_calificaciones()
	{
		var id_periodo = $("#cboPeriodosEvaluacion").val();
		document.getElementById("id_periodo_evaluacion").value = id_periodo;
		if (id_periodo==0) {
			$("#lista_calificaciones_erroneas").html("");
			$("#ver_reporte").css("display", "none");
			$("#num_calificaciones").html("N&uacute;mero de Calificaciones mal ingresadas: ");
			$("#mensaje").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
			$("#cboPeriodosEvaluacion").focus();
		} else {
			// Aqui se procesa la consulta para obtener las calificaciones erroneas
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.post("calificaciones/contar_calificaciones_erroneas.php", { id_periodo: id_periodo },
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						var JSONNumRegistros = eval('(' + resultado + ')');
						var total_registros = JSONNumRegistros.num_registros;
						$("#num_calificaciones").html("N&uacute;mero de Calificaciones err&oacute;neas: "+total_registros);
						if (total_registros == 0) {
							$("#lista_calificaciones_erroneas").html("");
							$("#ver_reporte").css("display", "none");
						} else {
							$("#ver_reporte").css("display", "block");
						}
					}
				}
			);
			$.post("scripts/validar_calificaciones.php",
				{
					id_periodo: id_periodo
				},
				function(resultado)
				{
					$("#mensaje").html("");
					$("#lista_calificaciones_erroneas").html(resultado);
				}
			);
		}
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
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboPeriodosEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                    <option value="1"> PRIMER QUIMESTRE </option> 
                    <option value="2"> SEGUNDO QUMIESTRE </option> 
                </select> 
            </td width="5%"> 
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div>
	<div id="pag_nomina_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="total_registros_estudiantes" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_calificaciones">N&uacute;mero de Calificaciones err&oacute;neas:</div>
                </td>
                <td>
                	<div id="paginacion_estudiantes"> 
                    	<!-- Aqui va la paginacion de estudiantes --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div id="tituloNomina" class="header2"> LISTA DE CALIFICACIONES ERRONEAS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="15%">Asignatura</td>
                <td width="15%">Docente</td>
                <td width="15%">Estudiante</td>
                <td width="15%">Curso</td>
                <td width="15%">Aporte</td>
                <td width="15%">R&uacute;brica</td>
                <td width="5%">Nota</td>
            </tr>
        </table>
	  </div>
      <form id="formulario_periodo" action="reportes/reporte_calificaciones_erroneas.php" method="post" target="_blank">
      	 <div id="lista_calificaciones_erroneas" style="text-align:center"> </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
         </div>
      </form>
   </div>
   <div id="mensaje" class="error"></div>
</div>
</body>
</html>
