<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_cursos();
		cargarPeriodosEvaluacion();
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_curso_aporte();
		});
		$("#cboCursos").change(function(e){
			e.preventDefault();
			listar_aportes_asociados();
		});
		$("#cboPeriodos").change(function(e){
			e.preventDefault();
			cargarAportesEvaluacion();
			listar_aportes_asociados();
		});		
		$("#cboAportes").change(function(e){
			e.preventDefault();
			var id_curso = $("#cboCursos").val();
			if(id_curso==0)	$("#lista_cursos_aportes").html("Debe elegir un curso...");
			else listar_aportes_asociados();
		});		
	});

	function cargar_cursos()
	{
		$.get("scripts/cargar_cursos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboCursos').append(resultado);
			}
		});	
	}

	function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion.php",
				function(resultado){
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$('#cboPeriodos').append(resultado);
					}
		});	
	}

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodos").value;
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
					document.getElementById("cboAportes").length=1;
					$("#cboAportes").append(resultado);
				}
			}
		);
	}

	function listar_aportes_asociados()
	{
		var id_curso = document.getElementById("cboCursos").value;
		var id_periodo_evaluacion = document.getElementById("cboPeriodos").value;
		if(id_curso==0)
			document.getElementById("lista_cursos_aportes").innerHTML="Debe elegir un curso...";
		else if(id_periodo_evaluacion==0) {
			document.getElementById("lista_cursos_aportes").innerHTML="Debe elegir un periodo...";
		} else 
			$.post("aportes_evaluacion/listar_aportes_asociados.php", 
				{ 
					id_curso: id_curso,
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
						$("#lista_cursos_aportes").html(resultado);
					}
				}
			);
	}

	function asociar_curso_aporte()
	{
		var id_curso = document.getElementById("cboCursos").value;
		var id_aporte_evaluacion = document.getElementById("cboAportes").value;
		if (id_curso == 0) {
			document.getElementById("mensaje").innerHTML = "Debe elegir un curso...";
			document.getElementById("cboCursos").focus();
		} else if (id_aporte_evaluacion == 0) {
			document.getElementById("mensaje").innerHTML = "Debe elegir un aporte de evaluacion...";
			document.getElementById("cboAportes").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "aportes_evaluacion/insertar_asociacion.php",
					data: "id_curso="+id_curso+"&id_aporte_evaluacion="+id_aporte_evaluacion,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_aportes_asociados();
				  }
			});
		}	
	}

	function eliminarAsociacion(id_curso, id_aporte_evaluacion)
	{
		if (id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_curso...";
		} else if (id_aporte_evaluacion == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_aporte_evaluacion...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "aportes_evaluacion/eliminar_asociacion.php",
					data: "id_curso="+id_curso+"&id_aporte_evaluacion="+id_aporte_evaluacion,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_aportes_asociados(true);
				  }
			});			
		}	
	}

</script>
</head>

<body>
<div id="pagina">
   <div id="titulo_pagina">
    	<?php echo $_SESSION['titulo_pagina'] ?>
   </div>
   <div id="frmVisor">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
      	 <tr>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Periodo:&nbsp;</span>
             <select id="cboPeriodos" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Aporte:&nbsp;</span>
             <select id="cboAportes" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Curso:&nbsp;</span>
             <select id="cboCursos" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
           <td width="*">&nbsp;  </td> <!-- Esto es para igualar las columnas -->
         </tr>
      </table>
  </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asociacion">
      <!-- Aqui va la paginacion de las asignaturas asociadas con los paralelos -->
      <div class="header2" style="margin-top:2px;"> LISTA DE APORTES ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id</td> 
                <td width="39%" align="left">Curso</td>
                <td width="39%" align="left">Aporte</td>
                <td width="6%" align="left">Estado</td>
                <td width="6%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_cursos_aportes" style="text-align:center"> Debe seleccionar un periodo... </div>
   </div>
</div>
</body>
</html>
