<div id="pagina">
	<div id="titulo_pagina">
    	<?php echo $_SESSION['titulo_pagina'] ?>
    </div>
    <div id="frmVisor">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
      	 <tr>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td colspan="3"><span class="fuente9">&nbsp;Paralelos:&nbsp;</span>
           <select id="cboParalelos" class="fuente9"> <option value="0"> Seleccione... </option> </select> </td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
           <td width="*">&nbsp;  </td> <!-- Esto es para igualar las columnas -->
         </tr>
         <tr>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;D&iacute;as:</span></td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Horas Clase:</span></td>
           <td class="fuente9" valign="top">&nbsp;</td>
           <td><span class="fuente9">&nbsp;Asignaturas:</span></td>
           <td width="*">&nbsp;  </td>  <!-- Esto es para igualar las columnas -->
         </tr>
         <tr>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstDiasSemana" class="fuente9" multiple size="7"> </select> </td>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td> <select id="lstHorasClase" class="fuente9" multiple size="7"> </select> </td>
            <td class="fuente9" valign="top">&nbsp;</td>
            <td valign="top"><select id="lstAsignaturas" class="fuente9" multiple size="7"> </select></td>
            <td width="*">&nbsp;  </td>  <!-- Esto es para igualar las columnas -->
         </tr>
      </table>
    </div>
    <div id="mensaje" class="error"></div>
    <div id="pag_asociacion">
      <!-- Aqui va la paginacion del horario semanal del paralelo elegido -->
      <div id="titulo_dia" class="header2" style="margin-top:2px;"> HORARIO DIARIO </div>
      <div id="tabla" class="table-responsive">
        <table class="table">
			<thead>
				<tr>
					<th width="15%" align="center"><button type="button" name="delete_all" id="delete_all" class="btn btn-danger btn-xs">Eliminar</button></th>
					<th width="15%" align="left">Hora Clase</th>
					<th width="35%" align="left">Asignatura</th>
					<th width="35%" align="left">Docente</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="3" class="text-center">Debe seleccionar un paralelo... </td>
				</tr>
			</tbody>
        </table>
	  </div>
   </div>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_paralelos();
		cargarDiasSemana();
		$('#delete_all').click(function(){
			var checkbox = $('.delete_checkbox:checked');
			if(checkbox.length > 0)
			{
				var checkbox_value = [];
				$(checkbox).each(function(){
					checkbox_value.push($(this).val());
				});

				$.ajax({
					url:"horarios/eliminar_asociacion.php",
					method:"POST",
					data:{checkbox_value:checkbox_value},
					success:function()
					{
						listar_asignaturas_asociadas();
					}
				});
			}
			else
			{
				alert("Seleccione al menos un registro");
			}
		});
		$("#cboParalelos").change(function(e){
			e.preventDefault();
			cargar_asignaturas_asociadas();
			listar_asignaturas_asociadas();
		});
		$("#lstDiasSemana").click(function(e){
			e.preventDefault();
			$("#titulo_dia").html("HORARIO DEL "+$("#lstDiasSemana option:selected").text());
			cargar_horas_clase();
			listar_asignaturas_asociadas();
		});
		$("#asociar").click(function(e){
			e.preventDefault();
			asociar_hora_asignatura();
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

	function cargarDiasSemana()
	{
		$.get("scripts/cargar_dias_semana.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lstDiasSemana").append(resultado);
				}
			}
		);
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
						document.getElementById("lstAsignaturas").length = 0;
						$('#lstAsignaturas').append(resultado);
					}
		});	
	}
		
	function cargar_horas_clase()
	{
		var id_dia_semana = document.getElementById("lstDiasSemana").value;
		document.getElementById("lstHorasClase").length = 0;
		$.post("scripts/cargar_horas_clase.php", 
			{ id_dia_semana: id_dia_semana },		
			function(resultado){
				if(resultado == false)
				{
					alert("No se han definido horas clase para este dia de la semana...");
				}
				else
				{
					$('#lstHorasClase').append(resultado);			
				}
		});	
	}

	function listar_asignaturas_asociadas(iDesplegar)
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_dia_semana = document.getElementById("lstDiasSemana").value;
		if(id_paralelo==0){
			$("#lista_horario_diario").html("Debe elegir un paralelo...");
			$("#cboParalelos").focus();
		}else if(id_dia_semana==""){
			$("#lista_horario_diario").html("Debe elegir un d&iacute;a de la semana...");
			$("#cboParalelos").focus();
		}else{
			$("#lista_horario_diario").html("<img src='imagenes/ajax-loader.gif' alt='procesando...'>");
			$.get("horarios/listar_asignaturas_asociadas.php", 
				{ 
					id_paralelo: id_paralelo,
					id_dia_semana: id_dia_semana
				},
				function(resultado)
				{
					$("#mensaje").html("");
					$("#tabla tbody").html(resultado);
				}
			);
		}
	}

	function asociar_hora_asignatura()
	{
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_dia_semana = document.getElementById("lstDiasSemana").value;
		var id_asignatura = document.getElementById("lstAsignaturas").value;
		var id_hora_clase = document.getElementById("lstHorasClase").value;
		if (id_paralelo == 0) {
			document.getElementById("mensaje").innerHTML = "Debe elegir un Paralelo...";
			document.getElementById("cboParalelos").focus();
		} else if (id_dia_semana == 0) {
			document.getElementById("mensaje").innerHTML = "Debe elegir un D&iacute;a de la Semana...";
			document.getElementById("cboDiasSemana").focus();
		} else if (id_hora_clase == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir una Hora Clase...";
			document.getElementById("lstHorasClase").focus();
		} else if (id_asignatura == "") {
			document.getElementById("mensaje").innerHTML = "Debe elegir una Asignatura...";
			document.getElementById("lstAsignaturas").focus();
		} else {
			$("#mensaje").hide();
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/existe_asociacion.php",
					data: "id_paralelo="+id_paralelo+"&id_dia_semana="+id_dia_semana+"&id_hora_clase="+id_hora_clase,
					success: function(resultado){
						var JSONResultado = eval('(' + resultado + ')');
						if (JSONResultado.error) {
							//Ya existe asociada una asignatura...
							alert("Ya existe una Asignatura asociada en esta Hora Clase...");
						}
						else
						{
							$.ajax({
								type: "POST",
								url: "horarios/insertar_asociacion.php",
								data: "id_paralelo="+id_paralelo+"&id_hora_clase="+id_hora_clase+"&id_asignatura="+id_asignatura+"&id_dia_semana="+id_dia_semana,
								success: function(resultado){
									$("#mensaje").html(resultado);
									listar_asignaturas_asociadas(false);
								}
							});
						}
				  }
			});
		}	
	}

	function eliminarHorario(id_horario)
	{
		if (id_horario == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado correctamente el par&aacute;metros id_horario...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "horarios/eliminar_asociacion.php",
					data: "id_horario="+id_horario,
					success: function(resultado){
						$("#mensaje").html(resultado);
						listar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}	
</script>