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
		mostrarTitulosPeriodos();
		$("#cboAsignaturas").change(function(e) {
			e.preventDefault();
			listarEstudiantesParalelo();
		});		
	});

	function mostrarTitulosPeriodos()
	{
		$.post("calificaciones/mostrar_titulos_periodos.php", 
			{
				pe_principal: 3, // 3 indica que son examenes remediales
				alineacion: "center"
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
									$("#titulo_pagina").html("REMEDIALES DE " + resultado);
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
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		if (id_asignatura == 0) {
			$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
			$("#cboAsignaturas").focus();
		} else {
			// Aqui va la llamada con AJAX al procedimiento que desplegará las calificaciones
			cargarEstudiantesParalelo(id_paralelo, id_asignatura);
		}
	}

	function cargarEstudiantesParalelo(id_paralelo, id_asignatura)
	{
		$("#mensaje").html("");
		$("#img_loader").show();
		$("#lista_estudiantes_paralelo").hide();
		$.post("tutores/listar_remediales.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
				$("#img_loader").hide();
				$("#lista_estudiantes_paralelo").show();
				$("#lista_estudiantes_paralelo").html(resultado);
			}
		);
	}

	function imprimirReporte()
	{
		var id_paralelo = document.getElementById("id_paralelo").value;
		if (id_paralelo == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("No se pudo obtener el id del paralelo asociado...");
		} else {
			$("#lista_estudiantes_paralelo").removeClass("error");
			document.getElementById("formulario_reporte").submit();
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
            <td width="7%" class="fuente9" align="right"> &nbsp;Asignatura:&nbsp; </td>
            <td width="5%"> <select id="cboAsignaturas" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
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
      <div id="tituloNomina" class="header2"> RESUMEN REMEDIALES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="30%" align="left">N&oacute;mina</td>
                <td width="60%" align="left"><div id="txt_rubricas"></div></td>
            </tr>
        </table>
	  </div>
      <div id="img_loader" style="display:none;text-align:center">
	     <img src="imagenes/ajax-loader.gif" alt="Procesando...">  
      </div>
      <form id="formulario_reporte" action="reportes/reporte_remediales.php" method="post" target="_blank">
      	 <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> Debe seleccionar una asignatura... </div>
         <input id="id_paralelo" name="id_paralelo" type="hidden" value="<?php echo $id_paralelo; ?>" />
      </form>
   </div>
</div>
</body>
</html>
