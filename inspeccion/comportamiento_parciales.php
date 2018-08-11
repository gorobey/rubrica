<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
            cargarPeriodosEvaluacion();
            cargarParalelosInspector();
            
            $("#cboPeriodosEvaluacion").change(function(e){
                cargarAportesEvaluacion();
                $("#div_estado_rubrica").html("");
                $("#div_fecha_cierre").html("");
                $("#mensaje_rubrica").html("");
                $("#ver_reporte").hide();
                $("#tituloNomina").html("NOMINA DE ESTUDIANTES");
            });
            
            $("#cboAportesEvaluacion").change(function(e){
                $("#div_estado_rubrica").html("");
                $("#div_fecha_cierre").html("");
                document.getElementById('id_aporte_evaluacion').value = $(this).val();
                $("#ver_reporte").hide();
                $("#lista_estudiantes_paralelo").addClass("error");
                $("#lista_estudiantes_paralelo").html("Debe seleccionar un paralelo...");
                $("#tituloNomina").html("NOMINA DE ESTUDIANTES");
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
                        $("#lista_estudiantes_paralelo").addClass("error");
                        $("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
                    }
                }
            );
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_principales_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if (resultado == false) 
				{
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
			}
		);
	}
        
    function mostrarTitulosIndices()
    {
        $.post("inspeccion/mostrar_titulos_indices.php", 
            {
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
                    $("#txt_indices").html(resultado);
                }
            }
        );
    }
        
    function cargarParalelosInspector()
    {
        contarParalelosInspector(); //Esta funcion desencadena las demas funciones de paginacion
    }

    function contarParalelosInspector()
    {
        $.post("inspeccion/contar_paralelos_inspector.php", { },
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
    				$("#num_paralelos").html("N&uacute;mero de Paralelos encontrados: "+total_registros);
    				paginarParalelosInspector(4,1,total_registros);
    			}
    		}
	    );
    }
        
    function paginarParalelosInspector(cantidad_registros, num_pagina, total_registros)
    {
        $.post("inspeccion/paginar_paralelos_inspector.php",
            {
                cantidad_registros: cantidad_registros,
                num_pagina: num_pagina,
                total_registros: total_registros
            },
            function(resultado)
            {
                $("#paginacion_paralelos").html(resultado);
            }
        );
        listarParalelosInspector(num_pagina);
    }

	function listarParalelosInspector(numero_pagina)
	{
            $.post("scripts/listar_paralelos_inspector.php", 
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
                            $("#lista_paralelos").html(resultado);
                    }
                }
            );
	}

	function seleccionarParalelo(id_curso, id_paralelo, curso, paralelo)
	{
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
        var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        //alert(id_paralelo);
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		} else if (id_aporte_evaluacion == 0 && $('#div_combo_aportes').is(':visible')) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
		} else {
			$("#mensaje").html("");
			document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + curso + " \"" + paralelo + "\"]";
			$("#lista_estudiantes_paralelo").removeClass("error");
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			listarEstudiantesParalelo(id_paralelo, id_curso);
			$("#ver_reporte").css("display","block");
		}
	}

	function listarEstudiantesParalelo(id_paralelo, id_curso)
	{
            var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
            $("#lista_estudiantes_paralelo").empty();
            $("#img_loader_estudiantes").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
            $.post("inspeccion/listar_estudiantes_paralelo_inspector.php", 
                    {
                        id_curso: id_curso,
                        id_paralelo: id_paralelo,
                        id_aporte_evaluacion: id_aporte_evaluacion
                    },
                    function(resultado)
                    {
                        $("#img_loader_estudiantes").html("");
                        //anadir el resultado al DOM
                        $("#lista_estudiantes_paralelo").html(resultado);
                    }
            );
	}

	function sel_texto(input) {
		$(input).select();
	}

	function truncateFloat(number,digitos) {
		var raiz = 10;
		var multiplicador = Math.pow (raiz,digitos);
		var resultado = (parseInt(number * multiplicador)) / multiplicador;
		return resultado;
	}

	function editarCalificacion(obj,id_estudiante,id_paralelo,id_aporte_evaluacion)
	{
		var str = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);

            //Validacion de la calificacion
            str = eliminaEspacios(str);
            var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
            var idx = permitidos.indexOf(str);
            if(str != '') { 
                if(idx == -1) {
                    alert("La calificacion debe estar en el rango de A a E");
                    obj.value = "";
                } else {
                    $.post("inspeccion/obtener_escala_comportamiento.php",
                        {
                            co_calificacion: document.getElementById("puntaje_"+fila).value
                        },
                        function(resultado)
                        {
                            if(resultado==false) { // Si existe algun error
                                alert(resultado);
                            } else {
                                //alert(resultado);
                                var JSONEscalaComportamiento = eval('(' + resultado + ')');
                                var id_escala_comportamiento = JSONEscalaComportamiento.id_escala_comportamiento;
                                document.getElementById('equivalencia_'+fila).value = JSONEscalaComportamiento.ec_relacion;
                                $.post("inspeccion/editar_calificacion.php",
                                        {
                                                id_estudiante: id_estudiante,
                                                id_paralelo: id_paralelo,
                                                id_escala_comportamiento: id_escala_comportamiento,
                                                id_aporte_evaluacion: id_aporte_evaluacion,
                                                co_calificacion: str.toUpperCase()
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
                    );
                }
            } else {
                //Aqui va el codigo para eliminar la calificacion del comportamiento
                $.post("inspeccion/obtener_id_comportamiento_inspector.php",
                    {
                        id_paralelo: id_paralelo,
                        id_estudiante: id_estudiante,
                        id_aporte_evaluacion: id_aporte_evaluacion
                    },
                    function(resultado) {
                        if(resultado==false) { // Si existe algun error
                            alert(resultado);
                        } else {
                            //Aqui va el codigo para recuperar el id_comportamiento_inspector
                            var JSONEscalaComportamiento = eval('(' + resultado + ')');
                            var id_comportamiento_inspector = JSONEscalaComportamiento.id_comportamiento_inspector;
                            if(id_comportamiento_inspector != 0) {
                                $.post("inspeccion/eliminar_comportamiento_inspector.php",
                                    {
                                        id_comportamiento_inspector: id_comportamiento_inspector
                                    },
                                    function(resultado) {
                                        if(resultado==false) {
                                            alert(resultado);
                                        } else {
                                            //Aqui va el mensaje de exito o fracaso al eliminar el registro
                                            $("#mensaje_rubrica").html(resultado);
                                            document.getElementById('equivalencia_'+fila).value = '';
                                        }
                                    }
                                );
                            }
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
    	<?php echo "COMPORTAMIENTO DE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td width="5%" class="fuente9" align="right"> Per&iacute;odo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="5%" class="fuente9" align="right"> <div id="label_combo_aportes"> Aporte:&nbsp; </div> </td>
            <td width="5%"> <div id="div_combo_aportes"> <select id="cboAportesEvaluacion" class="fuente8"> <option value="0"> Seleccione... </option> </select> </div> 
            </td>
            <td width="10%"> <div id="div_estado_rubrica" style="padding-left: 4px;"> </div> </td>
            <td width="20%"> <div id="div_fecha_cierre" style="padding-left: 4px;"> </div> </td>
            <td width="*"> <div id="mensaje_rubrica" class="error" style="text-align:center"></div> </td>
         </tr>
      </table>
    </div>
    <div id="mensaje" class="error"></div>
    <div id="pag_paralelos">
      <!-- Aqui va la paginacion de los paralelos asociados al docente -->
      <div id="total_registros" class="paginacion">
      	<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
        	<tr>
            	<td>
                	<div id="num_paralelos">&nbsp;N&uacute;mero de Paralelos encontrados:&nbsp;</div>
                </td>
                <td>
                	<div id="paginacion_paralelos"> 
                    	<!-- Aqui va la paginacion de asignaturas --> 
                    </div>
                </td>
            </tr>
        </table>
      </div>
      <div class="header2"> LISTA DE PARALELOS ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="71%" align="left">Curso</td>
                <td width="6%" align="left">Paralelo</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
      </div>
      <div id="lista_paralelos" style="text-align:center"> </div>
    </div>
    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
    <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="30%" align="left">N&oacute;mina</td>
                <td width="10%" align="left">COMP.</td>
                <td width="10%" align="left">#FALTAS</td>
                <td width="10%" align="left">JUSTIF.</td>
                <td width="30%" align="left">DESCRIPCION</td>
            </tr>
        </table>
    </div>
    <form id="formulario_rubrica" action="reportes/reporte_por_aporte_inspector.php" method="post" target="_blank">
      	<div id="img_loader_estudiantes" style="text-align:center"> </div>
        <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
	    <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>
</body>
</html>
