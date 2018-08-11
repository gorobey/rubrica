<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<style>
	textarea {
		width: 330px;
		height: 30px;
		font:8pt helvetica;
    	color:#000;
    	border: 1px solid #696969;
	}
</style>
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

	function seleccionarParalelo(id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		$("#escala_calificaciones").html("");
		$("#tituloNomina").html("ESCALA DE CALIFICACIONES [" + asignatura + " - " + curso + " " + paralelo + "]");
		//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
		cargarEscalaCalificaciones(id_paralelo, id_asignatura);
		$("#ver_reporte").css("display","block");
	}

	function cargarEscalaCalificaciones(id_paralelo,id_asignatura)
	{
		document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		$("#escala_calificaciones").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.post("scripts/informe_anual.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
				$("#escala_calificaciones").html(resultado);
				$("#ver_reporte").css("display","block");
			}
		);
	}

	function guardarRecomendaciones()
	{
		//Procedimiento para guardar en la base de datos los campos de tipo input = text
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("id_asignatura").value;
		$.post("scripts/obtener_id_paralelo_asignatura.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
				var id_paralelo_asignatura = resultado;
				for (i=0; ele=document.forms[0].elements[i]; i++)
				  if (ele.type == 'textarea') // quita esto si quieres que afecte a todos los elementos
					{
						var id = ele.id;
						var fila = id.substr(id.indexOf("_")+1);
						var plandemejora = ele.value;
						// Saco los espacios en blanco al comienzo y al final de la cadena
						plandemejora = eliminaEspacios(plandemejora);
						$("#mensajes").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
						$.post("scripts/editar_recomendaciones_anuales.php", 
							{ 
								id_escala_calificaciones: fila,
								id_paralelo_asignatura: id_paralelo_asignatura,
								plandemejora: plandemejora
							},
							function(resultado)
							{
								$("#mensajes").html(resultado);
							}
						);
					}		    
			}
		);
	}
	
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "INFORMES " . $_SESSION['titulo_pagina'] ?>
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
      <div id="tituloNomina" class="header2"> ESCALA DE CALIFICACIONES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="20%">ESCALA CUALITATIVA</td>
                <td width="20%">ESCALA CUANTITATIVA</td>
                <td width="10%">Nro. Estudiantes</td>
                <td width="20%">PORCENTAJE</td>
                <td width="30%">PLAN DE MEJORA</td>
                <!-- <td width="18%" align="center">Acciones</td> -->
            </tr>
        </table>
	  </div>
      <form id="formulario_periodo" action="php_excel/informe_anual.php" method="post">
      	 <div id="escala_calificaciones" style="text-align:center"> Debe elegir una asignatura.... </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_asignatura" name="id_asignatura" type="hidden" />
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input type="button" value="Guardar" onclick="guardarRecomendaciones()" />
            <input type="submit" value="Exportar a Excel" />
         </div>
      </form>
   </div>
</div>
</body>
</html>
