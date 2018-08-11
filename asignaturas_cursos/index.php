<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_cursos();
		cargar_asignaturas();
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_curso_asignatura();
		});
		$("#lstCursos").click(function(e){
			e.preventDefault();
			listar_asignaturas_asociadas();
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
				$('#lstCursos').append(resultado);			
			}
		});	
	}

	function cargar_asignaturas()
	{
		var id_curso = document.getElementById("lstCursos").value;
		$.post("scripts/cargar_asignaturas.php", 
			{
				id_curso: id_curso
			}, 
			function(resultado){
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$('#lstAsignaturas').append(resultado);			
				}
			}
		);	
	}

	function listar_asignaturas_asociadas(iDesplegar)
	{
		var id_curso = document.getElementById("lstCursos").value;
		$.get("asignaturas_cursos/listar_asignaturas_asociadas.php", { id_curso: id_curso },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_cursos_asignaturas").html(resultado);
				}
			}
		);
	}

	function asociar_curso_asignatura()
	{
		var id_curso = document.getElementById("lstCursos").value;
		var id_asignatura = document.getElementById("lstAsignaturas").value;
		if (id_curso == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un curso...";
			document.getElementById("lstCursos").focus();
		} else if (id_asignatura == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir una asignatura...";
			document.getElementById("lstAsignaturas").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/insertar_asociacion.php",
					data: "id_curso="+id_curso+"&id_asignatura="+id_asignatura,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function eliminarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/eliminar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function subirAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/subir_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function bajarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/bajar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
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
           <td><span class="fuente9">&nbsp;Cursos:</span></td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Asignaturas:</span></td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstCursos" class="fuente9" multiple size="7" > </select> </td>         
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstAsignaturas" class="fuente9" multiple size="7" > </select> </td>         
         </tr>
      </table>
  </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asociacion">
      <!-- Aqui va la paginacion de las asignaturas asociadas con los paralelos -->
      <div class="header2" style="margin-top:2px;"> LISTA DE ASIGNATURAS ASOCIADAS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="38%" align="left">Curso</td>
                <td width="39%" align="left">Asignatura</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_cursos_asignaturas" style="text-align:center"> Debe seleccionar un curso... </div>
   </div>
</div>
</body>
</html>
