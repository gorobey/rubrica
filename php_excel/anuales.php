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
			mostrarTitulosAsignaturas();
			document.getElementById("numero_pagina").value = 1;
			contarEstudiantesParalelo($(this).val());
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

	function mostrarTitulosAsignaturas()
	{
		var id_paralelo = $("#cboParalelos").val();
		$.post("scripts/mostrar_asignaturas.php", 
			{
				id_paralelo: id_paralelo,
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
					$("#txt_asignaturas").html(resultado);
				}
			}
		);
	}

	function procesar_promedios(cantidad_registros, numero_pagina)
	{
		var id_paralelo = $("#cboParalelos").val();
		document.getElementById("id_paralelo").value = id_paralelo;
		if(id_paralelo==0) {
			$("#mensaje").html("Debe elegir un paralelo...");
			$("#cboParalelos").focus();
		} else {
			$("#mensaje").html("");
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$("#ver_reporte").css("display","none");
			$.ajax({
					type: "POST",
					url: "scripts/promedios_anuales.php",
					data: "id_paralelo="+id_paralelo+"&cantidad_registros="+cantidad_registros+"&numero_pagina="+numero_pagina,
					success: function(resultado){
						$("#mensaje").html("");
						$("#lista_estudiantes_paralelo").html(resultado);
						$("#ver_reporte").css("display","block");
				  }
			});	
		}
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
						paginarEstudiantesParalelo(10,numero_pagina,total_registros,id_paralelo);
					}
				}
			}
		);
	}

	function paginarEstudiantesParalelo(cantidad_registros,numero_pagina,total_registros,id_paralelo)
	{
		document.getElementById("numero_pagina").value = numero_pagina;
		$.post("matriculacion/paginar_estudiantes_paralelo.php",
			{
				cantidad_registros: cantidad_registros,
				numero_pagina: numero_pagina,
				total_registros: total_registros,
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				$("#paginacion_estudiantes").html(resultado);
			}
		);
		procesar_promedios(cantidad_registros, numero_pagina);
	}
	
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "REPORTES " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
            <td width="5%"> <select id="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
            <td width="*">&nbsp;  </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" value="1" />
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
                <td width="30px">No.</td>
                <td width="240px" align="left">N&oacute;mina</td>
                <td width="*" align="left"><div id="txt_asignaturas">Asignaturas</div></td>
            </tr>
        </table>
	  </div>
      <form id="formulario_periodo" action="php_excel/reporte_anual.php" method="post">
      	 <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
	     <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
	        <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input type="submit" value="Exportar a Excel" />
         </div>
      </form>
   </div>
   <div id="mensaje" class="error"></div>
</div>
</body>
</html>
