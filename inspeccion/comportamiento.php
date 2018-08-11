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
			e.preventDefault();
			$("#lista_estudiantes_paralelo").html("Debe seleccionar un paralelo...");
			$("#ver_reporte").css("display","none");
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			var id_paralelo = $(this).find('option:selected').val();
			var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").find('option:selected').val();
			if(id_periodo_evaluacion==0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display","none");
				$(this).val(0); // Para setear el indice a cero
				$("#cboPeriodosEvaluacion").focus();
			} else {
				// Aqui va el codigo para desplegar las calificaciones del comportamiento
				$.post("scripts/obtener_nombre_paralelo.php", 
					{ 
						id_paralelo: id_paralelo
					},
					function(resultado)
					{
						var JSONParalelo = eval('(' + resultado + ')');
						document.getElementById("tituloNomina").innerHTML='NOMINA DE ESTUDIANTES [' + JSONParalelo.cu_nombre + ' ' + JSONParalelo.pa_nombre + ']';
					}
				);
				//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
				listarEstudiantesParalelo(id_paralelo);
				$("#ver_reporte").css("display","block");
			}
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
		document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		if (id_periodo_evaluacion == 0) {
			$("#lista_estudiantes_paralelo").addClass("error");
			$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
		} else {
			document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + curso + " \"" + paralelo + "\"]";
			$("#lista_estudiantes_paralelo").removeClass("error");
			//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
			listarEstudiantesParalelo(id_paralelo);
			$("#ver_reporte").css("display","block");
		}
	}

	function listarEstudiantesParalelo(id_paralelo)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
		if(id_periodo_evaluacion==0) {
			$("#lista_estudiantes_paralelo").html("Debe escoger un per&iacute;odo de evaluaci&oacute;n...");
		} else if(id_paralelo==0) {
			$("#lista_estudiantes_paralelo").html("Debe escoger un paralelo...");
		} else {
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader-red-dog.GIF' alt='procesando...' />");
			$.post("inspeccion/listar_estudiantes_inspector.php", 
				{ 
					id_paralelo: id_paralelo,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado)
				{
					$("#lista_estudiantes_paralelo").html(resultado);
					$("#ver_reporte").css("display","block");
				}
			);
		}
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "COMPORTAMIENTO " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboPeriodosEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>			
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div> <!-- div barra_principal -->
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
    <!--<div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="385px">&nbsp;</td>
                <td width="240px" align="center">TUTORES</td>
                <td width="240px" align="center">INSPECTORES</td>
                <td width="*">&nbsp;</td> 
            </tr>
        </table>
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="35px">Nro.</td>
                <td width="350px" align="center">N&oacute;mina</td>
                <td width="60px" align="center">VALORES</td>
                <td width="60px" align="center">NORMAS</td>
                <td width="60px" align="center">PUNTUAL</td>
                <td width="60px" align="center">PRESENT</td>
                <td width="60px" align="center">VALORES</td>
                <td width="60px" align="center">NORMAS</td>
                <td width="60px" align="center">PUNTUAL</td>
                <td width="60px" align="center">PRESENT</td>
                <td width="60px" align="center">TOTAL</td>
                <td width="60px" align="center">PROM.</td>
                <td width="60px" align="center">EQUIV.</td>
                <td width="*">&nbsp;</td> 
            </tr>
        </table>
    </div>-->
	<div class="cabeceraTabla">
		<table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="45%" align="left">N&oacute;mina</td>
                <td width="10%" align="center">1er.P.</td>
                <td width="10%" align="center">2do.P.</td>
				<td width="10%" align="center">3er.P.</td>
                <td width="20%" align="center">QUIMESTRE</td>
                <td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
            </tr>
        </table>
    </div>
    <form id="formulario_comportamiento" action="reportes/reporte_comp_inspector.php" method="post" target="_blank">
		<div id="lista_estudiantes_paralelo" style="text-align:center"> Debe seleccionar un per&iacute;odo de evaluaci&oacute;n... </div>
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>
</body>
</html>
