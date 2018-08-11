<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_paralelos();
		cargar_asignaturas();
		cargar_docentes();
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_paralelo_asignatura();
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			cargar_asignaturas_asociadas();
			listar_asignaturas_asociadas();
		});
	});

	function cargar_paralelos()
	{
		$.get("scripts/cargar_paralelos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboParalelos').append(resultado);
			}
		});	
	}

	function cargar_asignaturas()
	{
		$.get("scripts/cargar_asignaturas.php",
				function(resultado){
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$('#lstAsignaturas').append(resultado);
					}
		});	
	}

	function cargar_asignaturas_asociadas()
	{
		$.get("paralelos_asignaturas/cargar_asignaturas_asociadas.php",
				{ id_paralelo: document.getElementById("cboParalelos").value },
				function(resultado){
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						document.getElementById("lstAsignaturas").innerHTML = "";
						$('#lstAsignaturas').append(resultado);
					}
		});	
	}

	function cargar_docentes()
	{
		$.get("scripts/cargar_docentes.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#lstDocentes').append(resultado);			
			}
		});	
	}

	function listar_asignaturas_asociadas(iDesplegar)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		$.get("paralelos_asignaturas/listar_asignaturas_asociadas.php", { id_paralelo: id_paralelo },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_paralelos_asignaturas").html(resultado);
				}
			}
		);
	}

	function asociar_paralelo_asignatura()
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_asignatura = document.getElementById("lstAsignaturas").value;
		var id_docente = document.getElementById("lstDocentes").value;
		if (id_paralelo == 0) {
			document.getElementById("mensaje").innerHTML = "Debe elegir un paralelo...";
			document.getElementById("cboParalelos").focus();
		} else if (id_asignatura == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir una asignatura...";
			document.getElementById("lstAsignaturas").focus();
		} else if (id_docente == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un docente...";
			document.getElementById("lstDocentes").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_asignaturas/insertar_asociacion.php",
					data: "id_paralelo="+id_paralelo+"&id_asignatura="+id_asignatura+"&id_docente="+id_docente,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function eliminarAsociacion(id_paralelo_asignatura)
	{
		if (id_paralelo_asignatura == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_paralelo_asignatura...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_asignaturas/eliminar_asociacion.php",
					data: "id_paralelo_asignatura="+id_paralelo_asignatura,
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
           <td><span class="fuente9">&nbsp;Paralelos:&nbsp;</span>
           <select id="cboParalelos" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
           <td width="*">&nbsp;  </td> <!-- Esto es para igualar las columnas -->
         </tr>
         <tr>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Asignaturas:</span></td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Docentes:</span></td>
           <td width="*">&nbsp;  </td>  <!-- Esto es para igualar las columnas -->
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstAsignaturas" class="fuente9" multiple size="7" style="width:500px"> </select> </td>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td valign="top"><select id="lstDocentes" class="fuente9" multiple size="7"> </select></td>
            <td width="*">&nbsp;  </td>  <!-- Esto es para igualar las columnas -->
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
                <!-- <td width="2.5%">Id</td> -->
                <td width="30%" align="left">Paralelo</td>
                <td width="40%" align="left">Asignatura</td>
                <td width="19%" align="left">Docente</td>
                <td width="6%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_paralelos_asignaturas" style="text-align:center"> Debe seleccionar un paralelo... </div>
   </div>
</div>
</body>
</html>
