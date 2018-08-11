<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_clubes();
		listarEstudiantesClub(0,false);
		$("#img-loader-busqueda").hide();
		$("#aniadir_estudiante").click(function(e){
			e.preventDefault();
			if($("#cboClubes").val() > 0) {
				aniadirEstudiante();
			} else {
				$("#lista_estudiantes").html("Debe elegir un club...");
			}
		});
		$("#cboClubes").change(function(){
			if($(this).val()==0) {
				$("#lista_estudiantes").html("Debe elegir un club...");
			} else {
				contarEstudiantesClub($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
			}
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
				$('#cboClubes').append(resultado);			
			}
		});	
	}

	function limpiarBusqueda()
	{
		document.getElementById("txt_patron").value="";
		document.getElementById("lista_busqueda").innerHTML = "";
		document.getElementById("txt_patron").focus();
	}
	
	function salirBusqueda()
	{
		$("#formulario_busqueda").css("display", "none");
		$("#pag_busqueda").css("display", "none");
		document.getElementById("lista_busqueda").innerHTML = "";
	}

	function aniadirEstudiante()
	{
		limpiarBusqueda();
		$("#formulario_busqueda").css("display", "block");
		document.getElementById("txt_patron").focus();
	}

	function buscarEstudiantes()
	{
		//Aqui va el codigo para buscar estudiantes antiguos
		var patron = eliminaEspacios(document.getElementById("txt_patron").value);

		var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{4,64})$/i;
		
		if (patron=="") {
			$("#mensaje").html("Debe ingresar el patr&oacute;n de b&uacute;squeda...");
			document.getElementById("txt_patron").focus();
    	} else if(!reg_texto.test(patron)) {
			$("#mensaje").html("El patr&oacute;n de b&uacute;squeda debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("txt_patron").focus();
		} else {
			$("#mensaje").html("");
			$("#img-loader-busqueda").show();
			$.ajax({
					type: "POST",
					url: "matriculacion/buscar_estudiantes_matriculados.php",
					data: "patron="+patron,
					success: function(resultado){
						$("#img-loader-busqueda").hide();
						$("#lista_busqueda").html(resultado);
						$("#titulo_busqueda").css("display","block");
						$("#cabeceraBusqueda").css("display","block");
						$("#pag_busqueda").css("display", "block");
				  }
			});			
		}	
	}

	function listarEstudiantesClub(id_club,iDesplegar)
	{
		var id_club = document.getElementById("cboClubes").value;
		if (id_club == 0) {
			document.getElementById("lista_estudiantes").innerHTML = "Debe elegir un club...";
		} else {
			$.post("matriculacion/listar_estudiantes_clubes.php", 
				{ 
					id_club: id_club
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						if (!iDesplegar) $("#mensaje").html("");
						$("#lista_estudiantes").html(resultado);
					}
				}
			);
		}
	}

	function seleccionarEstudiante(id_estudiante,es_apellidos,es_nombres)
	{
		var id_club = document.getElementById("cboClubes").value;
		var matricular = confirm("¿Seguro que desea matricular el estudiante ["+es_apellidos+" "+es_nombres+"]?")
		if (matricular) {
			$("#img-loader-busqueda").show();
			$.post("matriculacion/seleccionar_estudiante_club.php", 
				{ 
					id_estudiante: id_estudiante,
					id_club: id_club,
					es_apellidos: es_apellidos,
					es_nombres: es_nombres
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#img-loader-busqueda").hide();
						// Mensaje de salida
						$("#mensaje").html(resultado);
						contarEstudiantesClub(id_club);
						salirBusqueda();
					}
				}
			);
		}
	}

	function contarEstudiantesClub(id_club)
	{
		$.post("matriculacion/contar_estudiantes_club.php", { id_club: id_club },
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
						$("#lista_estudiantes").html("No existen estudiantes matriculados en este club...");
					} else {
						listarEstudiantesClub(id_club,false);
					}
				}
			}
		);
	}

	function quitarEstudiante(id_estudiante)
	{
		// Quitar al estudiante del club
		
		var id_club = $("#cboClubes").val();
		
		if (id_estudiante=="") {
			$("#mensaje").html("No se ha pasado el parámetro de id_estudiante...");
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "matriculacion/quitar_estudiante_club.php",
					data: "id_estudiante="+id_estudiante+"&id_club="+id_club,
					success: function(resultado){
						$("#mensaje").html(resultado);
						contarEstudiantesClub(id_club);
				  }
			});			
		}			
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo "MATRICULACION DE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td class="fuente9">&nbsp;Club: &nbsp;</td>
            <td> <select id="cboClubes" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="aniadir_estudiante" class="boton" style="display:block"> <a href="#"> A&ntilde;adir Estudiante </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_busqueda">
      <div id="tituloBusqueda" class="header">BUSCAR ESTUDIANTE</div>
      <div id="frmBusqueda" align="left">
   	     <form id="form_busqueda" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Ingrese Patr&oacute;n de B&uacute;squeda:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_patron" type="text" class="cajaGrande" style="text-transform:uppercase" name="txt_patron" maxlength="40" />
                  </td>
				  <td width="5%">
                  	 <div id="buscar_estudiante" class="link_form"><a href="#" onclick="buscarEstudiantes()">Buscar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="limpiarBusqueda()">Limpiar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="salirBusqueda(true)">Salir</a></div>
                  </td>
                  <td width="*">
                     <div id="img-loader-busqueda" style="padding-left:2px">
                     	<img src="imagenes/ajax-loader.gif" alt="procesando..." />
                     </div>
                  </td>
               </tr>
            </table>
         </form>
      </div>   
    </div>
	<div id="pag_busqueda">
    	<!-- Aqui val la paginacion de los estudiantes encontrados en la busqueda -->
        <div id="titulo_busqueda" class="header2" style="display:none"> LISTA DE ESTUDIANTES ENCONTRADOS </div> 
        <div id="cabeceraBusqueda" class="cabeceraTabla" style="display:none">
           <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
              <tr class="cabeceraTabla">
                <td width="5%">Nro.</td>
                <td width="5%">Id.</td>
                <td width="18%" align="left">Apellidos</td>
                <td width="18%" align="left">Nombres</td>
                <td width="18%" align="left">Curso</td>
                <td width="18%" align="left">Paralelo</td>
                <td width="18%" align="center">Acciones</td>
              </tr>
           </table>
        </div>
        <div id="lista_busqueda">
            <!-- Aqui va el resultado de la busqueda de estudiantes matriculados -->
        </div>
    </div>
    <div id="mensaje" class="error" style="text-align:center"></div>
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
                	<td width="36%" align="left">Apellidos</td>
                	<td width="36%" align="left">Nombres</td>
                	<td width="8%" align="center">Retirado</td>
                	<td width="10%" align="center">Acciones</td>
            	</tr>
        	</table>
	  	</div>
      	<div id="lista_estudiantes" style="text-align:center"> </div>
      </div>
    </div> 
</div>
</body>
</html>
