<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_periodos_lectivos();
		$("#cboPeriodosLectivos").change(function(e){
			e.preventDefault();
			cargarPeriodosEvaluacion();
			$("#lista_calificaciones").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		});
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
			$("#lista_calificaciones").html("Debe elegir un aporte de evaluaci&oacute;n...");
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			mostrarTitulosRubricas();
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			document.getElementById("txt_apellidos").focus();
		});
	});

	function mostrarTitulosRubricas()
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		$.post("../calificaciones/mostrar_titulos_rubricas.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
				id_aporte_evaluacion: id_aporte_evaluacion,
				alineacion: "right"
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#txt_rubricas").html(resultado);
				}
			}
		);
	}

	function cargar_periodos_lectivos()
	{
		$.get("../periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPeriodosLectivos').append(resultado);			
			}
		});	
	}

	function cargarPeriodosEvaluacion()
	{
		var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
		if (id_periodo_lectivo == 0) {
			alert("Debe seleccionar un A&ntilde;o Lectivo...");
			document.getElementById("cboPeriodosLectivos").focus();
		} else 
			$.post("cargar_periodos_evaluacion.php", { id_periodo_lectivo: id_periodo_lectivo },
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

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("../scripts/cargar_aportes_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				$("#cboAportesEvaluacion").append(resultado);
				//$("#lista_asignaturas_estudiante").html("Debe elegir un aporte de evaluaci&oacute;n...");
			}
		);
	}

	function limpiarBusqueda()
	{
		document.getElementById("txt_apellidos").value="";
		document.getElementById("txt_nombres").value="";
		document.getElementById("txt_apellidos").focus();
		$("#titulo").html("CALIFICACIONES DEL ESTUDIANTE");
		$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
	}
	
	function consultarEstudiante()
	{
		var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		var txt_apellidos = $("#txt_apellidos").val();
		var txt_nombres = $("#txt_nombres").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_apellidos=eliminaEspacios(txt_apellidos);
		txt_nombres=eliminaEspacios(txt_nombres);
		
		if (id_periodo_lectivo==0) {
			$("#lista_calificaciones").html("Debe elegir un per&iacute;odo lectivo...");
			document.getElementById("cboPeriodosLectivos").focus();
		} else if (id_periodo_evaluacion==0) {
			$("#lista_calificaciones").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
			document.getElementById("cboPeriodosEvaluacion").focus();
		} else if (id_aporte_evaluacion==0) {
			$("#lista_calificaciones").html("Debe elegir un aporte de evaluaci&oacute;n...");
			document.getElementById("cboAportesEvaluacion").focus();
		} else if (txt_apellidos=="") {
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			document.getElementById("txt_apellidos").focus();
		} else if (txt_nombres=="") {
			$("#lista_calificaciones").html("Debe ingresar los nombres del estudiante...");
			document.getElementById("txt_nombres").focus();
		} else {
			$.post("obtener_curso_paralelo.php",
				{ txt_apellidos: txt_apellidos,
				  txt_nombres: txt_nombres,
				  id_periodo_lectivo: id_periodo_lectivo
				 },
				function(resp)
				{
					var JSONEstudiante = eval('(' + resp + ')');
					if(!JSONEstudiante.error)
						$("#titulo").html("CALIFICACIONES DEL ESTUDIANTE [" + JSONEstudiante.cu_nombre + " " + JSONEstudiante.pa_nombre + "]");
				}
			);
			$.post("obtener_aportes_estudiante.php", 
				{ txt_apellidos: txt_apellidos,
				  txt_nombres: txt_nombres,
				  id_periodo_lectivo: id_periodo_lectivo,
				  id_aporte_evaluacion: id_aporte_evaluacion
				 },
				function(resultado)
				{
					$("#lista_calificaciones").html(resultado);
				}
			);
		}
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	Consultar Calificaciones por Aporte
    </div>
    <div class="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="8%" class="fuente9" align="right"> A&ntilde;o Lectivo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosLectivos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> &nbsp;Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> Aporte:&nbsp; </td>
            <td width="5%"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="*">&nbsp;</td>
         </tr>
      </table>
	</div>
	<div id="formulario_busqueda" style="display:block">
      <div id="tituloBusqueda" class="header">DATOS DEL ESTUDIANTE</div>
      <div id="frmBusqueda" align="left">
   	     <form id="form_busqueda" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="7%" align="right">Apellidos:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_apellidos" type="text" class="cajaGrande" name="txt_apellidos" maxlength="40" style="text-transform:uppercase" />
                  </td>
                  <td  width="7%" align="right">Nombres:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_nombres" type="text" class="cajaGrande" name="txt_nombres" maxlength="40" style="text-transform:uppercase" />
                  </td>
				  <td width="5%">
                  	 <div id="buscar_estudiante" class="link_form"><a href="#" onclick="consultarEstudiante()">Consultar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="limpiarBusqueda()">Limpiar</a></div>
                  </td>
                  <td width="*">
                     <div id="img-loader" style="padding-left:2px"></div>
                  </td>
               </tr>
            </table>
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="titulo" class="header2"> CALIFICACIONES DEL ESTUDIANTE </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="30px">Nro.</td>
                <td width="300px" align="center">Asignatura</td>
                <td width="*" align="left"><div id="txt_rubricas">Calificaciones</div></td>
                <!-- <td width="18%" align="center">Acciones</td> -->
            </tr>
        </table>
	  </div>
      <div id="lista_calificaciones" style="text-align:center">
      	Debe elegir un a&ntilde;o lectivo...
      </div>
   </div>
</div>
</body>
</html>
