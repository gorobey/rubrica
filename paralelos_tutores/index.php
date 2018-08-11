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
		cargar_tutores();
		listar_paralelos_tutores(false);
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_paralelo_tutor();
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

	function cargar_tutores()
	{
		$.get("scripts/cargar_tutores.php", function(resultado){
			if(resultado == false)
			{
				alert("No se han definido tutores en el presenta periodo lectivo...");
			}
			else
			{
				$('#lstTutores').append(resultado);			
			}
		});	
	}

	function asociar_paralelo_tutor()
	{
		var id_paralelo = $("#lstParalelos").find(":selected").val();
		var id_usuario = $("#lstTutores").find(":selected").val();
		if (id_paralelo == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un paralelo...";
			document.getElementById("lstParalelos").focus();
		} else if (id_usuario == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un docente...";
			document.getElementById("lstTutores").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_tutores/insertar_asociacion.php",
					data: "id_paralelo="+id_paralelo+"&id_usuario="+id_usuario,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_paralelos_tutores(true);
				  }
			});	
		}
	}

	function eliminarAsociacion(id_paralelo_tutor)
	{
		if (id_paralelo_tutor == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_paralelo_tutor...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_tutores/eliminar_asociacion.php",
					data: "id_paralelo_tutor="+id_paralelo_tutor,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_paralelos_tutores(true);
				  }
			});	
		}
	}

	function listar_paralelos_tutores(iDesplegar)
	{
		$.get("paralelos_tutores/listar_paralelos_tutores.php",
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_paralelos_tutores").html(resultado);
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
           <td><span class="fuente9">&nbsp;Tutores:</span></td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstParalelos" class="fuente9" multiple size="7" >
             	 </select> 
            </td>         
            <td valign="top">&nbsp;</td>
            <td valign="top"><select id="lstTutores" class="fuente9" multiple size="7" > </select></td>
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
                <td width="38%" align="left">Tutor</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_paralelos_tutores" style="text-align:center"> Debe seleccionar un paralelo... </div>
   </div>
</div>
</body>
</html>
