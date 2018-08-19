<div class="container">
    <div id="appHorariosDocentes" class="col-sm-10 col-sm-offset-1">
        <h2>Horario del Docente</h2>
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Día de la Semana</h4>
            <form id="form_horario_docente" action="" class="app-form">
                <select id="cboDiasSemana" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table table-striped fuente9">
                <thead>
                    <tr>
                        <th>Hora Clase</th>
                        <th>Asignatura</th>
                        <th>Paralelo</th>
                    </tr>
                </thead>
                <tbody id="horario_docente">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        cargarDiasSemana();
        $("#cboDiasSemana").change(function(e){
            // Código para recuperar el horario docente del día seleccionado
            listarHorarioDocente();
        });           
        $("#horario_docente").html("<tr><td colspan='3' align='center'>Debes seleccionar un dia de la semana...</td></tr>");
    });
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
					$("#cboDiasSemana").append(resultado);
				}
			}
		);
	}
    function listarHorarioDocente()
    {
        var id_dia_semana = $("#cboDiasSemana").val();
        if(id_dia_semana=="0"){
			$("#horario_docente").html("<tr><td colspan='3' align='center'>Debes seleccionar un dia de la semana...</td></tr>");
			$("#cboDiasSemana").focus();
		}else{
			$("#horario_docente").html("<tr><td colspan='3' align='center'><img src='imagenes/ajax-loader.gif' alt='procesando...'></td></tr>");
			$.get("horarios/listar_horario_docente.php", 
				{ 
					id_dia_semana: id_dia_semana
				},
				function(resultado)
				{
                    console.log(resultado);
					$("#horario_docente").html(resultado);
				}
			);
		}
    }
</script>