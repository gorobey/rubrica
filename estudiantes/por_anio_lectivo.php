<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_periodos_lectivos();
		$("#cboPeriodosLectivos").change(function(e){
			e.preventDefault();
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			$("#txt_apellidos").focus();
		});
	});

	function cargar_periodos_lectivos()
	{
		$.get("../periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPeriodosLectivos').append(resultado);			
			}
		});	
	}

	function limpiarBusqueda()
	{
		document.getElementById("txt_apellidos").value="";
		document.getElementById("txt_nombres").value="";
		document.getElementById("lista_calificaciones").innerHTML="Ingrese los apellidos del estudiante...";
		document.getElementById("txt_apellidos").focus();
	}
	
	function consultarEstudiante()
	{
		var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
		var txt_apellidos = $("#txt_apellidos").val();
		var txt_nombres = $("#txt_nombres").val();
		
		// Saco los espacios en blanco al comienzo y al final de la cadena
		txt_apellidos=eliminaEspacios(txt_apellidos);
		txt_nombres=eliminaEspacios(txt_nombres);
		
		if (id_periodo_lectivo==0) {
			$("#lista_calificaciones").html("Debe elegir un per&iacute;odo lectivo...");
			document.getElementById("cboPeriodosLectivos").focus();
		} else if (txt_apellidos=="") {
			$("#lista_calificaciones").html("Debe ingresar los apellidos del estudiante...");
			document.getElementById("txt_apellidos").focus();
		} else if (txt_nombres=="") {
			$("#lista_calificaciones").html("Debe ingresar los nombres del estudiante...");
			document.getElementById("txt_nombres").focus();
		} else {
			$("#lista_calificaciones").html("<img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' />");
			$.post("obtener_calificaciones_anuales.php", 
				{ txt_apellidos: txt_apellidos,
				  txt_nombres: txt_nombres,
				  id_periodo_lectivo: id_periodo_lectivo
				 },
				function(resultado)
				{
					$("#lista_calificaciones").html(resultado);
				}
			);
		}
	}
</script>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	Consultar Calificaciones por A&ntilde;o Lectivo
    </div>
    <div class="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="8%" class="fuente9" align="right"> A&ntilde;o Lectivo:&nbsp; </td>
            <td width="5%"> <select id="cboPeriodosLectivos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
			<td width="*">&nbsp;</td>
         </tr>
      </table>
	</div>
	<div id="formulario_busqueda" style="display:block">
      <div id="tituloBusqueda" class="header">DATOS DEL ESTUDIANTE</div>
      <div id="frmBusqueda" align="left">
   	     <form id="form_busqueda" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="7%" align="right">Apellidos:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_apellidos" type="text" class="cajaGrande" name="txt_apellidos" maxlength="40" style="text-transform:uppercase" />
                  </td>
                  <td  width="7%" align="right">Nombres:&nbsp;</td>
                  <td width="10%">
                     <input id="txt_nombres" type="text" class="cajaGrande" name="txt_nombres" maxlength="40" style="text-transform:uppercase" />
                  </td>
				  <td width="5%">
                  	 <div id="buscar_estudiante" class="link_form"><a href="#" onclick="consultarEstudiante()">Consultar</a></div>
                  </td>
				  <td width="5%">
                  	 <div class="link_form"><a href="#" onclick="limpiarBusqueda()">Limpiar</a></div>
                  </td>
                  <td width="*">
                     <div id="img-loader" style="padding-left:2px"></div>
                  </td>
               </tr>
            </table>
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_estudiantes">
      <!-- Aqui va la paginacion de los estudiantes encontrados -->
      <div class="header2"> CALIFICACIONES DEL ESTUDIANTE </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%" align="left">Nro.</td>
                <td width="35%" align="left">Asignatura</td>
                <td width="5%" align="left">1ER.Q.</td>
                <td width="5%" align="left">2DO.Q.</td>
                <td width="5%" align="left">Suma</td>
                <td width="5%" align="left">Prom.</td>
                <td width="5%" align="left">SUP.</td>
                <td width="5%" align="left">REM.</td>
                <td width="5%" align="left">GRA.</td>
                <td width="5%" align="left">P.F.</td>
                <td width="20%" align="left">Observaci&oacute;n</td>
            </tr>
        </table>
	  </div>
      <div id="lista_calificaciones" style="text-align:center">
      	Debe elegir un a&ntilde;o lectivo...
      </div>
   </div>
</div>
</body>
</html>
