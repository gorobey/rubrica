<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_paralelos();
		cargar_inspectores();
		listar_paralelos_inspectores(false);
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_paralelo_inspector();
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
				$('#lstParalelos').append(resultado);			
			}
		});	
	}

	function cargar_inspectores()
	{
		$.get("scripts/cargar_inspectores.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#lstInspectores').append(resultado);			
			}
		});	
	}

	function asociar_paralelo_inspector()
	{
		var id_paralelo = $("#lstParalelos").find(":selected").val();
		var id_usuario = $("#lstInspectores").find(":selected").val();
		if (id_paralelo == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un paralelo...";
			document.getElementById("lstParalelos").focus();
		} else if (id_usuario == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un docente...";
			document.getElementById("lstInspectores").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_inspectores/insertar_asociacion.php",
					data: "id_paralelo="+id_paralelo+"&id_usuario="+id_usuario,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_paralelos_inspectores(true);
				  }
			});
		}
	}

	function eliminarAsociacion(id_paralelo_inspector)
	{
		if (id_paralelo_inspector == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_paralelo_inspector...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_inspectores/eliminar_asociacion.php",
					data: "id_paralelo_inspector="+id_paralelo_inspector,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_paralelos_inspectores(true);
				  }
			});	
		}
	}

	function listar_paralelos_inspectores(iDesplegar)
	{
		$.get("paralelos_inspectores/listar_paralelos_inspectores.php",
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_paralelos_inspectores").html(resultado);
				}
			}
		);
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
           <td><span class="fuente9">&nbsp;Paralelos:</span></td>
           <td>&nbsp;</td>
           <td><span class="fuente9">&nbsp;Inspectores:</span></td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstParalelos" class="fuente9" multiple size="7" >
             	 </select> 
            </td>         
            <td valign="top">&nbsp;</td>
            <td valign="top"><select id="lstInspectores" class="fuente9" multiple size="7" > </select></td>
            <td valign="top">&nbsp;</td>
         </tr>
      </table>
  </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asociacion">
      <!-- Aqui va la paginacion de los clubes asociados con los docentes -->
      <div class="header2" style="margin-top:2px;"> LISTA DE PARALELOS ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <!-- <td width="2.5%">Id</td> -->
                <td width="37%" align="left">Paralelo</td>
                <td width="38%" align="left">Inspector</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_paralelos_inspectores" style="text-align:center"> Debe seleccionar un paralelo... </div>
   </div>
</div>
</body>
</html>
