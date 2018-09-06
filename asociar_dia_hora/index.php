<div class="container">
    <div id="AssocHoraDiaApp" class="col-sm-12">
        <input type="hidden" id="id_hora_dia">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Día - Hora Clase</h4>
            </div>
            <div class="panel-body">
                <form id="form_assoc_hora_dia" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Día de la Semana:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboDiasSemana">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora Clase:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboHorasClase">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="asociarHoraDia()">
                                Asociar
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-10 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Total Horas:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_horas" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargarDiasSemana();
        cargarHorasClase();
        $("#cboDiasSemana").change(function(e){
            e.preventDefault();
            listarHorasAsociadas();
        })
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
    function cargarHorasClase()
	{
		$.get("scripts/cargar_horas_clase.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					//console.log(resultado);
                    $("#cboHorasClase").append(resultado);
				}
			}
		);
	}
    function asociarHoraDia()
    {
        // Procedimiento para insertar la asociacion entre hora clase y dia de la semana
        var id_dia_semana = $("#cboDiasSemana").val();
        var id_hora_clase = $("#cboHorasClase").val();
        var cont_errores = 0;

        // Validación de ingreso de datos
        if (id_dia_semana == 0) {
            $("#mensaje1").html("Debe elegir el dia de la semana...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (id_hora_clase == 0) {
            $("#mensaje2").html("Debe elegir la hora clase...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "asociar_dia_hora/insertar_asociacion.php",
                method: "POST",
                type: "html",
                data: {
                    id_dia_semana: id_dia_semana,
                    id_hora_clase: id_hora_clase
                },
                success: function(response){
                    listarHorasAsociadas();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function listarHorasAsociadas()
    {
        var id_dia_semana = $("#cboDiasSemana").val();
        if(id_dia_semana==0){
            $("#lista_items").html("<tr><td colspan='4' align='center'>Debes seleccionar un dia de la semana...</td></tr>");
        }else{
            $.get("asociar_dia_hora/listar_horas_asociadas.php", 
                { 
                    id_dia_semana: id_dia_semana
                },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        var datos = JSON.parse(resultado);
                        $("#lista_items").html(datos.cadena);
                        $("#total_horas").val(datos.total_horas);
                    }
                }
            );
        }
    }
    function eliminarAsociacion(id_hora_dia){
        //Elimino la asociacion de la hora clase en el dia seleccionado mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "asociar_dia_hora/eliminar_asociacion.php",
            method: "POST",
            type: "html",
            data: {
                id_hora_dia: id_hora_dia
            },
            success: function(response){
                $("#text_message").html(response);
                listarHorasAsociadas();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>