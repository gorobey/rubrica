<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_clubes();
		cargar_docentes();
		listar_clubes_docentes(false);
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_club_docente();
		});
	});

	function cargar_clubes()
	{
		$.get("scripts/cargar_clubes.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#lstClubes').append(resultado);			
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

	function asociar_club_docente()
	{
		var id_club = document.getElementById("lstClubes").value;
		var id_usuario = document.getElementById("lstDocentes").value;
		if (id_club == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un club...";
			document.getElementById("lstClubes").focus();
		} else if (id_usuario == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir un docente...";
			document.getElementById("lstDocentes").focus();
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "clubes_docentes/insertar_asociacion.php",
					data: "id_club="+id_club+"&id_usuario="+id_usuario,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_clubes_docentes(true);
				  }
			});			
		}	
	}

	function eliminarAsociacion(id_club_docente)
	{
		if (id_club_docente == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado el par&aacute;metro id_club_docente...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "clubes_docentes/eliminar_asociacion.php",
					data: "id_club_docente="+id_club_docente,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_clubes_docentes(true);
				  }
			});			
		}	
	}

	function listar_clubes_docentes(iDesplegar)
	{
		$.get("clubes_docentes/listar_clubes_docentes.php", 
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					if (!iDesplegar) $("#mensaje").html("");
					$("#lista_clubes_docentes").html(resultado);
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
           <td><span class="fuente9">&nbsp;Clubes:</span></td>
           <td>&nbsp;</td>
           <td><span class="fuente9">&nbsp;Docentes:</span></td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstClubes" class="fuente9" multiple size="7" >
             	 </select> 
            </td>         
            <td valign="top">&nbsp;</td>
            <td valign="top"><select id="lstDocentes" class="fuente9" multiple size="7" > </select></td>
            <td valign="top">&nbsp;</td>
         </tr>
      </table>
  </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_asociacion">
      <!-- Aqui va la paginacion de los clubes asociados con los docentes -->
      <div class="header2" style="margin-top:2px;"> LISTA DE CLUBES ASOCIADOS </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <!-- <td width="2.5%">Id</td> -->
                <td width="37%" align="left">Club</td>
                <td width="38%" align="left">Docente</td>
                <td width="18%" align="center">Acciones</td>
            </tr>
        </table>
	  </div>
      <div id="lista_clubes_docentes" style="text-align:center"> Debe seleccionar un club... </div>
   </div>
</div>
</body>
</html>
