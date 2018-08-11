<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargarAsignaturasDocente();
		mostrarTitulosPeriodos();
	});

	function sel_texto(input) {
		$(input).select();
	}

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
        
	function mostrarEstadoRubrica(id_curso)
	{

		$.post("calificaciones/obtener_id_aporte_evaluacion.php",
			{
				id_curso: id_curso,
				pe_principal: 3
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var JSONIdAporteEvaluacion = eval('(' + resultado + ')');
					var id_aporte_evaluacion = JSONIdAporteEvaluacion.id_aporte_evaluacion;
					
					$.post("calificaciones/mostrar_estado_rubrica.php",
						{
							id_aporte_evaluacion: id_aporte_evaluacion,
							id_curso: id_curso
						},
						function(resultado)
						{
							if(resultado == false)
							{
								alert("Error");
							}
							else
							{
								$("#div_estado_rubrica").html(resultado);
							}
						}
					);

					$.post("calificaciones/obtener_fecha_cierre_aporte.php",
						{ 
							id_aporte_evaluacion: id_aporte_evaluacion,
							id_curso: id_curso
						},
						function(resultado) 
						{
							if(resultado == false)
							{
								alert("Error");
							}
							else
							{
								$("#div_fecha_cierre").html(resultado);
							}
						}
					);
					
				}
			}
		);
			
	}        

	function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
		mostrarEstadoRubrica(id_curso);
                document.getElementById("id_asignatura").value = id_asignatura;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("numero_pagina").value = 1;
		$("#mensaje").html("");
		$("#tituloNomina").html("RESUMEN REMEDIALES [" + asignatura + " - " + curso + " " + paralelo + "]");
		//Aqui va la llamada a la paginacion de estudiantes
		cargarEstudiantesSupletorio(id_paralelo, id_asignatura);
		$("#ver_reporte").css("display","block");
	}

	function cargarEstudiantesSupletorio(id_paralelo, id_asignatura)
	{
		$("#lista_remediales").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$("#ver_reporte").css("display","none");
		$.post("scripts/reporte_remediales.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
				$("#lista_remediales").html(resultado);
				$("#ver_reporte").css("display","block");
			}
		);
	}

	function editarCalificacion(obj,id_estudiante,id_paralelo,id_asignatura,id_rubrica_personalizada)
	{
		var calificacion = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);
		var promedio_final = 0;
		var observacion = "";

		var frmFormulario = document.forms["formulario_periodo"];
		//Validacion de la calificacion
		if(calificacion<0 || calificacion>7) {
			alert("La calificacion debe estar en el rango de 0 a 7");
		} else {
			//Aqui va el codigo para calcular el promedio
			for (var iCont=0; iCont < frmFormulario.length; iCont++) {
				var objElemento=frmFormulario.elements[iCont];
				if(objElemento.type=='text') {
					var id_elem = objElemento.id;
					var fila_elem = id_elem.substr(id_elem.indexOf("_")+1);
					if(fila_elem==fila) {
						// Aca calculo la suma del promedio de los quimestres mas el examen supletorio
						var promedio_final = document.getElementById("promedio_periodos_"+fila_elem).value;
						// Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
						if (calificacion < 7) {
							observacion = "NO APRUEBA";
						} else {
							promedio_final = 7;
							observacion = "APRUEBA";
						}
						document.getElementById("promedio_final_"+fila_elem).value = Math.round(promedio_final * 100) / 100;
						document.getElementById("observacion_"+fila_elem).value = observacion;
					}
				}
			}
			$.post("calificaciones/editar_calificacion.php",
				{
					id_estudiante: id_estudiante,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_rubrica_personalizada: id_rubrica_personalizada,
					re_calificacion: calificacion
				},
				function(resultado)
				{
					if(resultado) { // Solo si existe resultado
						$("#mensaje_rubrica").html(resultado);
					}
				}
			);	
		}		
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "INGRESAR CALIFICACIONES DE " .  $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td width="20%"> <div id="div_estado_rubrica" style="padding-left: 4px;"> </div> </td>
            <td width="25%"> <div id="div_fecha_cierre" style="padding-left: 4px;"> </div> </td>
            <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
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
      <form id="formulario_periodo" action="reportes/reporte_anual_docente.php" method="post" target="_blank">
      	 <div id="lista_remediales" style="text-align:center"> Debe elegir una asignatura.... </div>
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
