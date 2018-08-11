<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		listarPeriodosLectivos();
		$("#nuevo_periodo_lectivo").click(function(e) {
			e.preventDefault();
			nuevoPeriodoLectivo();
		});		
	});

	function listarPeriodosLectivos()
	{
		$.get("periodos_lectivos/listar_periodos_lectivos.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_periodos_lectivos").html(resultado);
				}
			}
		);
	}

	function limpiarPeriodoLectivo()
	{
		$("input").val("");
		$("#pe_anio_inicio").focus();
	}
		
	function salirPeriodoLectivo()
	{
		$("#mensaje").html("");
		$("#formulario_nuevo").hide();
	}
	
	function editarPeriodoLectivo(id)
	{
		//Procedimiento para editar un nuevo periodo lectivo
		document.getElementById("id_periodo_lectivo").value = id;
		$("#tituloForm").html("EDITAR PERIODO LECTIVO");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		var html = "<div id='editarPeriodoLectivo' class='link_form'><a href='#' onclick='actualizarPeriodoLectivo()'>Actualizar</a></div>"; 
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "periodos_lectivos/obtener_periodo_lectivo.php",
				data: "id="+id,
				success: function(resultado){
					var JSONPeriodoLectivo = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el perfil elegido
					document.getElementById("id_periodo_lectivo").value=JSONPeriodoLectivo.id_periodo_lectivo;
					document.getElementById("pe_anio_inicio").value=JSONPeriodoLectivo.pe_anio_inicio;
					document.getElementById("pe_anio_fin").value=JSONPeriodoLectivo.pe_anio_fin;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("pe_anio_inicio").focus();
			  }
		});			
	}

	function actualizarPeriodoLectivo()
	{
		//Procedimiento para actualizar un periodo lectivo
		var id_periodo_lectivo = $("#id_periodo_lectivo").val();
		var anio_inicio = $("#pe_anio_inicio").val();
		var anio_fin = $("#pe_anio_fin").val();
		
		anio_inicio = eliminaEspacios(anio_inicio);
		anio_fin = eliminaEspacios(anio_fin);
		
		//Validaciones necesarias
		if(id_periodo_lectivo=="") {
			$("#mensaje").html("No se ha pasado el par&aacute;metro [id_periodo_lectivo]...");
			$("#pe_anio_inicio").focus();
		} else if(anio_inicio=="") {
			$("#mensaje").html("Debe ingresar el a&ntilde;o inicial...");
			$("#pe_anio_inicio").focus();
		} else if(anio_fin=="") {
			$("#mensaje").html("Debe ingresar el a&ntilde;o final...");
			$("#pe_anio_fin").focus();
		} else if(parseInt(anio_inicio) < 2013) {
			$("#mensaje").html("Debe ingresar un a&ntilde;o mayor o igual a 2013...");
			$("#pe_anio_inicio").focus();
		} else if(parseInt(anio_inicio) >= parseInt(anio_fin)) {
			$("#mensaje").html("El a&ntilde;o inicial no puede ser mayor o igual que el a&ntilde;o final...");
			$("#pe_anio_inicio").focus();
		} else if(parseInt(anio_fin) - parseInt(anio_inicio) > 1) {
			$("#mensaje").html("Los per&iacute;odos lectivos tienen que ser consecutivos...");
			$("#pe_anio_fin").focus();
		} else {
			//OK aqui toca ingresar el nuevo periodo lectivo
			
			$.post("periodos_lectivos/actualizar_periodo_lectivo.php", 
				{ 
					id_periodo_lectivo: id_periodo_lectivo,
					anio_inicial: anio_inicio,
					anio_final: anio_fin
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#mensaje").html(resultado);
						listarPeriodosLectivos();
					}
				}
			);
		}
	}

	function nuevoPeriodoLectivo()
	{
		//Procedimiento para ingresar un nuevo periodo lectivo
		var html = "<div id='insertarPeriodoLectivo' class='link_form'><a href='#' onclick='insertarPeriodoLectivo()'>Insertar</a></div>"; 
		$("#boton_accion").html(html);
		$("#formulario_nuevo").show();
		$("#pe_anio_inicio").focus();
	}
	
	function insertarPeriodoLectivo()
	{	
		//Procedimiento para ingresar un nuevo periodo lectivo
		var anio_inicio = $("#pe_anio_inicio").val();
		var anio_fin = $("#pe_anio_fin").val();
		
		anio_inicio = eliminaEspacios(anio_inicio);
		anio_fin = eliminaEspacios(anio_fin);
		
		//Validaciones necesarias
		if(anio_inicio=="") {
			$("#mensaje").html("Debe ingresar el a&ntilde;o inicial...");
			$("#pe_anio_inicio").focus();
		} else if(anio_fin=="") {
			$("#mensaje").html("Debe ingresar el a&ntilde;o final...");
			$("#pe_anio_fin").focus();
		} else if(parseInt(anio_inicio) < 2013) {
			$("#mensaje").html("Debe ingresar un a&ntilde;o mayor o igual a 2013...");
			$("#pe_anio_inicio").focus();
		} else if(parseInt(anio_inicio) >= parseInt(anio_fin)) {
			$("#mensaje").html("El a&ntilde;o inicial no puede ser mayor o igual que el a&ntilde;o final...");
			$("#pe_anio_inicio").focus();
		} else if(parseInt(anio_fin) - parseInt(anio_inicio) > 1) {
			$("#mensaje").html("Los per&iacute;odos lectivos tienen que ser consecutivos...");
			$("#pe_anio_fin").focus();
		} else {
			//OK aqui toca ingresar el nuevo periodo lectivo
			
			$.post("periodos_lectivos/insertar_periodo_lectivo.php", 
				{ 
					anio_inicial: anio_inicio,
					anio_final: anio_fin
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#mensaje").html(resultado);
						listarPeriodosLectivos();
					}
				}
			);
		}
	}
	
	function cerrarPeriodoLectivo(id_periodo_lectivo)
	{
		$.post("periodos_lectivos/cerrar_periodo_lectivo.php", 
			{ 
				id_periodo_lectivo: id_periodo_lectivo
			},
			function(resultado)
			{
				// Solamante para realizar la llamada a AJAX
				listarPeriodosLectivos();
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
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td> <div id="nuevo_periodo_lectivo" class="boton" style="display:block"> <a href="#"> Nuevo Periodo Lectivo </a> </div> </td>
         </tr>
      </table>
    </div>
    <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Periodo Lectivo</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">A&ntilde;o Inicial:</td>
                  <td width="*">
                     <input id="pe_anio_inicio" type="text" class="cajaPequenia" name="pe_anio_inicio" maxlength="40" onkeypress="return permite(event,'num')" />
                  </td>
               </tr>
               <tr>
                  <td width="15%" align="right">A&ntilde;o Final:</td>
                  <td width="*">
                     <input id="pe_anio_fin" type="text" class="cajaPequenia" name="pe_anio_fin" maxlength="40" onkeypress="return permite(event,'num')" />
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                           <td width="15%" align="right">
							  <div id="boton_accion">
                                 <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                              </div>   
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="limpiarPeriodoLectivo()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirPeriodoLectivo()">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_periodo_lectivo" name="id_periodo_lectivo" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error" align="center"></div>
   <div id="pag_periodos_lectivos">
      <!-- Aqui va la paginacion de los periodos lectivos encontrados -->
      <div class="header2"> LISTA DE PERIODOS LECTIVOS EXISTENTES </div>
      <div class="cabeceraTabla">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
            <tr class="cabeceraTabla">
                <td width="5%">NRO.</td>
                <td width="5%">ID.</td>
                <td width="24%" align="left">A&Ntilde;O INICIAL</td>
                <td width="24%" align="left">A&Ntilde;O FINAL</td>
                <td width="24%" align="left">ESTADO</td>
                <td width="18%" align="center">ACCIONES</td>
            </tr>
        </table>
	  </div>
      <div id="lista_periodos_lectivos" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
