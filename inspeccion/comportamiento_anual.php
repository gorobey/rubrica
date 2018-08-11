<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelosInspector();
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			listarComportamientoAnualParalelo($(this).val());
		});
	});

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
                    console.log(resultado);
                    $("#lista_paralelos").html(resultado);
                }
            }
        );
	}

	function seleccionarParalelo(id_curso, id_paralelo, curso, paralelo)
	{
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + curso + " \"" + paralelo + "\"]";
		//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
		listarEstudiantesParalelo(id_paralelo);
		$("#ver_reporte").css("display","block");
	}

	function listarEstudiantesParalelo(id_paralelo)
	{
		document.getElementById("id_paralelo").value = id_paralelo;
		$("#lista_estudiantes_paralelo").html("");
		$("#img_loader").show();
		$("#ver_reporte").css("display","none");
		$.post("inspeccion/listar_comportamiento_anual_paralelo.php", 
			{ 
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				$("#img_loader").hide();
				$("#lista_estudiantes_paralelo").html(resultado);
				$("#ver_reporte").css("display","block");
			}
		);
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "COMPORTAMIENTO " . $_SESSION['titulo_pagina'] ?>
    </div>
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
                <td width="45%" align="left">N&oacute;mina</td>
                <td width="15%" align="center">1er.Q.</td>
                <td width="15%" align="center">2do.Q.</td>
                <td width="20%" align="center">ANUAL</td>
                <td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
            </tr>
        </table>
    </div>
    <form id="formulario_comportamiento" action="reportes/reporte_comportamiento_anual.php" method="post" target="_blank">
		<div id="lista_estudiantes_paralelo" style="text-align:center"> Debe seleccionar un paralelo... </div>
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
    <div id="img_loader" style="text-align:center;display:none">
		<img src='imagenes/ajax-loader-red-dog.GIF' alt='procesando...' />    
    </div>
</div>
</body>
</html>
