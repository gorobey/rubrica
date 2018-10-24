<?php
	include("scripts/clases/class.tutores.php");
	$tutor = new tutores();
	$id_usuario = $_SESSION["id_usuario"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_paralelo = $tutor->obtenerIdParalelo($id_usuario, $id_periodo_lectivo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

		cargarAsignaturas();
		cargarPeriodosEvaluacion();

		$("#cboPeriodosEvaluacion").change(function(e) {
			e.preventDefault();
			if ($(this).val() == 0) {
				$("#lista_estudiantes_paralelo").html("Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...");
			} else {				
				cargarAportesEvaluacion();
				$("#lista_estudiantes_paralelo").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
			}
		});
		
		$("#cboAportesEvaluacion").change(function(e) {
			e.preventDefault();
			if ($(this).val() == 0) {
				$("#lista_estudiantes_paralelo").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
			} else {
				$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
			}
		});
		
		$("#cboAsignaturas").change(function(e) {
			e.preventDefault();
			mostrarTitulosRubricas("center", $(this).val());
			listarEstudiantesParalelo();
		});

		$("#imprimir_para_juntas").click(function(e){
			var chequeado = 0;
			var checkbox = document.getElementById("impresion_para_juntas");
			if($(this).is(':checked'))
				chequeado = 1;
			else
				chequeado = 0;
			checkbox.value = chequeado;
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
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").find(":selected").val();
		$.get("scripts/cargar_aportes_evaluacion.php", 
			{ 
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					document.getElementById("cboAportesEvaluacion").length = 1;
					$("#cboAportesEvaluacion").append(resultado);
				}
			}
		);
	}

	function cargarAsignaturas()
	{
		// Primero tengo que obtener el id_paralelo asociado al tutor
		$.post("scripts/obtener_id_paralelo_tutor.php", 
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONIdParalelo = eval('(' + resultado + ')');
					$("#id_paralelo").val(JSONIdParalelo.id_paralelo);
					// Luego obtengo el nombre del paralelo
					$.post("tutores/obtener_nombre_paralelo.php",
						{
							id_paralelo: $("#id_paralelo").val()
						},
						function(resultado)
						{
							if(resultado == false)
							{
								alert("Error");
							}
							else
							{
								$("#titulo_pagina").html("PARCIALES DE " + resultado);
							}
						}
					);
					// Ahora obtengo el numero de estudiantes del paralelo
					$.post("calificaciones/contar_estudiantes_paralelo.php", 
						{
							id_paralelo: $("#id_paralelo").val()
						},
						function(resultado)
						{
							if(resultado == false)
							{
								alert("Error");
							}
							else
							{
								var JSONNumRegistrosEstudiantes = eval('(' + resultado + ')');
								var total_registros = JSONNumRegistrosEstudiantes.num_registros;
								$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: "+total_registros);
							}
						}
					);
					// Luego obtengo las asignaturas asociadas al paralelo
					$.post("scripts/cargar_asignaturas_por_paralelo.php", 
						{
							id_paralelo: $("#id_paralelo").val()
						},
						function(resultado)
						{
							if(resultado == false)
							{
								alert("Error");
							}
							else
							{
								document.getElementById("cboAsignaturas").length = 1;
								$("#cboAsignaturas").append(resultado);
							}
						}
					);
				}
			}
		);
	}

	function listarEstudiantesParalelo()
	{
		// Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...");
			$("#cboPeriodosEvaluacion").focus();
		} else if (id_aporte_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
			$("#cboAportesEvaluacion").focus();
		} else if (id_asignatura == 0) {
			$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
			$("#cboAsignaturas").focus();
		} else {
			// Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
			cargarEstudiantesParalelo(id_paralelo, id_asignatura, id_aporte_evaluacion, id_periodo_evaluacion);
		}
	}

	function cargarEstudiantesParalelo(id_paralelo, id_asignatura, id_aporte_evaluacion, id_periodo_evaluacion)
	{
		$("#mensaje").html("");
		$("#img_loader").show();
		$("#lista_estudiantes_paralelo").hide();
		// Primero obtengo el id_curso asociado al id_paralelo actual
		$.ajax({
			url: "tutores/obtener_id_curso.php",
			method: "post",
			data:
				{
					id_paralelo: id_paralelo
				},
			type: "json",
			success: function(resultado){
				// Obtengo el id_curso en formato json
				var id_curso = JSON.parse(resultado).id_curso;
				$.post("calificaciones/listar_estudiantes_paralelo_tutor.php", 
					{ 
						id_curso: id_curso,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: id_aporte_evaluacion,
						id_periodo_evaluacion: id_periodo_evaluacion
					},
					function(resultado)
					{
						$("#img_loader").hide();
						$("#lista_estudiantes_paralelo").show();
						$("#lista_estudiantes_paralelo").html(resultado);
					}
				);
			},
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
		});
	}

	function mostrarTitulosRubricas(alineacion, id_asignatura)
	{
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
		$.post("calificaciones/mostrar_titulos_rubricas.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
				id_aporte_evaluacion: id_aporte_evaluacion,
				alineacion: alineacion,
				id_asignatura: id_asignatura
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

	function imprimirReporte()
	{
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
		if (id_paralelo == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("No se pudo obtener el id del paralelo asociado...");
		} else if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...");
		} else if (id_aporte_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
		} else {
			$("#lista_estudiantes_paralelo").removeClass("error");
			document.getElementById("formulario_rubrica").submit();
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
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="5%" class="fuente9" align="right"> &nbsp;Aporte:&nbsp; </td>
            <td width="5%"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="7%" class="fuente9" align="right"> &nbsp;Asignatura:&nbsp; </td>
            <td width="5%"> <select id="cboAsignaturas" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="15%">
                <div id="input_checkbox" style="text-align:left;padding-left:10px;">
                    Impresi&oacute;n para juntas <input type="checkbox" id="imprimir_para_juntas" />
                </div>
            </td>
            <td width="*"> <div id="ver_consolidado" class="boton" style="display:block"> <a href="#" onclick="imprimirReporte()"> Ver Consolidado </a> </div> </td>
         </tr>
      </table>
      <input type="hidden" id="id_paralelo" />
    </div>
	<div id="pag_nomina_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div id="total_registros_estudiantes" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes encontrados:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_estudiantes"> 
                    	<!-- Aqui va la paginacion de estudiantes --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="30%" align="left">N&oacute;mina</td>
                <td width="60%" align="left"><div id="txt_rubricas">Calificaciones</div></td>
                <!-- <td width="18%" align="center">Acciones</td> -->
            </tr>
        </table>
	  </div>
      <div id="img_loader" style="display:none;text-align:center">
	     <img src="imagenes/ajax-loader.gif" alt="Procesando...">  
      </div>
      <form id="formulario_rubrica" action="php_excel/reporte_por_parcial.php" method="post" target="_self">
      	 <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> Debe seleccionar un per&iacute;odo de evaluaci&oacute;n... </div>
         <input id="id_paralelo" name="id_paralelo" type="hidden" value="<?php echo $id_paralelo; ?>" />
         <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
         <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
		 <input id="impresion_para_juntas" name="impresion_para_juntas" type="hidden" />
      </form>
   </div>
</div>
</body>
</html>
