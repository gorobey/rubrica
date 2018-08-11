<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarAsignaturasDocente();
	});

	function cargarAsignaturasDocente()
	{
		contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
	}

	function contarAsignaturasDocente()
	{
		$.post("calificaciones/contar_asignaturas_docente.php", { },
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
					$("#num_asignaturas").html("N&uacute;mero de Asignaturas encontradas: "+total_registros);
					paginarAsignaturasDocente(4,1,total_registros);
				}
			}
		);
	}
	
	function paginarAsignaturasDocente(cantidad_registros, num_pagina, total_registros)
	{
		$.post("calificaciones/paginar_asignaturas_docente.php",
			{
				cantidad_registros: cantidad_registros,
				num_pagina: num_pagina,
				total_registros: total_registros
			},
			function(resultado)
			{
				$("#paginacion_asignaturas").html(resultado);
			}
		);
		listarAsignaturasDocente(num_pagina);
	}

	function listarAsignaturasDocente(numero_pagina)
	{
		$.post("scripts/cargar_asignaturas_docente.php", 
			{
				cantidad_registros: 4,
				numero_pagina: numero_pagina
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_asignaturas").html(resultado);
				}
			}
		);
	}

	function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("numero_pagina").value = 1;
		$("#mensaje").html("");
		$("#tituloNomina").html("RESUMEN ANUAL [" + asignatura + " - " + curso + " " + paralelo + "]");
		//Aqui va la llamada a la paginacion de estudiantes
		contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
		$("#ver_reporte").css("display","block");
	}

	function contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura)
	{
		var numero_pagina = $("#numero_pagina").val();
		$.post("calificaciones/contar_estudiantes_paralelo.php", { id_paralelo: id_paralelo },
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
					$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: "+total_registros);
					if (total_registros == 0) {
						$("#paginacion_estudiantes").html("");
						$("#resumen_anual").html("No existen estudiantes matriculados en este paralelo...");
					} else {
						cargarTablaResumenAnual(id_curso, numero_pagina, id_paralelo, id_asignatura);
					}
				}
			}
		);
	}

	function cargarTablaResumenAnual(id_curso, numero_pagina, id_paralelo, id_asignatura)
	{
		$("#resumen_anual").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$("#ver_reporte").css("display","none");
		$.post("scripts/reporte_anual.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
                                id_curso: id_curso
			},
			function(resultado)
			{
				$("#resumen_anual").html(resultado);
				$("#ver_reporte").css("display","block");
			}
		);
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTE DE CALIFICACIONES " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="pag_asignaturas">
      <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
      <div id="total_registros" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_asignaturas">&nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_asignaturas"> 
                    	<!-- Aqui va la paginacion de asignaturas --> 
                    </div>
                </td>
            </tr>
        </table>
        <input id="numero_pagina" type="hidden" value="1" />
      </div>
      <div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="39%" align="left">Asignatura</td>
                <td width="32%" align="left">Curso</td>
                <td width="6%" align="left">Paralelo</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_asignaturas" style="text-align:center"> </div>
    </div>
    <div id="pag_nomina_estudiantes" style="margin-top:2px;">
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
      <div id="tituloNomina" class="header2"> RESUMEN ANUAL </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%" align="left">Nro.</td>
                <td width="5%" align="left">Id.</td>
                <td width="30%" align="left">N&oacute;mina</td>
                <td width="5%" align="left">1ER.Q.</td>
                <td width="5%" align="left">2DO.Q.</td>
                <td width="5%" align="left">Suma</td>
                <td width="5%" align="left">Prom.</td>
                <td width="5%" align="left">SUP.</td>
                <td width="5%" align="left">REM.</td>
                <td width="5%" align="left">GRA.</td>
                <td width="5%" align="left">P.F.</td>
                <td width="20%" align="left">Observaci&oacute;n</td>
            </tr>
        </table>
	  </div>
      <form id="formulario_periodo" action="reportes/reporte_anual_docente.php" method="post" target="_blank">
      	 <div id="resumen_anual" style="text-align:center"> Debe elegir una asignatura.... </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_asignatura" name="id_asignatura" type="hidden" />
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input type="submit" value="Ver Reporte" />
         </div>
      </form>
   </div>
</div>
</body>
</html>
