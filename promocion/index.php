<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargarParalelos();
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			document.getElementById("id_paralelo").value = $(this).val();
			//($(this).val()==0) ? $("#ver_reporte").hide() : $("#ver_reporte").show();
			if ($(this).val()==0) 
				$("#lista_estudiantes").html("Debe elegir un paralelo...");
			else
				contarEstudiantesParalelo($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
		});
	});

	function cargarParalelos()
	{
		$.get("scripts/cargar_paralelos.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboParalelos").append(resultado);
				}
			}
		);
	}

	function contarEstudiantesParalelo(id_paralelo)
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
						$("#lista_estudiantes").html("No existen estudiantes matriculados en este paralelo...");
					} else {
						listarEstudiantesParalelo(id_paralelo);
					}
				}
			}
		);
	}

	function listarEstudiantesParalelo(id_paralelo)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		$("#lista_estudiantes").html('<img src="imagenes/ajax-loader.gif" alt="procesando..." />');
		$.post("promocion/listar_estudiantes.php", 
			{ 
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
					$("#lista_estudiantes").html('Error...');
				}
				else
				{
					$("#lista_estudiantes").html(resultado);
				}
			}
		);
	}

	function seleccionarEstudiante(id_estudiante)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_estudiante").value = id_estudiante;
		document.getElementById("formulario_promocion").submit();
	}

</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	REPORTE DE PROMOCIONES
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="90%">
                <form id="formulario_promocion" action="php_excel/reporte_promocion.php" method="post">
                     <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
                     <div id="ver_reporte" style="text-align:left;margin-top:2px;display:none">
                        <input id="id_paralelo" name="id_paralelo" type="hidden" />
                        <input id="id_estudiante" name="id_estudiante" type="hidden" />
                        <input type="submit" value="Ver Reporte" />
                     </div>
                </form>
            </td>
         </tr>
      </table>
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
      <div id="pag_estudiantes">
        <!-- Aqui va la paginacion de los estudiantes encontrados -->
        <div class="header2"> LISTA DE ESTUDIANTES MATRICULADOS </div>
        <div class="cabeceraTabla">
          <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="24%" align="left">Apellidos</td>
                <td width="24%" align="left">Nombres</td>
                <td width="32%" align="left">Promedio Anual</td>
                <td width="10%" align="center">Acciones</td>
            </tr>
          </table>
        </div>
        <div id="lista_estudiantes" style="text-align:center">Debe elegir un paralelo...</div>
      </div>
    </div>
</div>
<div id="mensaje" class="error"></div>
</body>
</html>
