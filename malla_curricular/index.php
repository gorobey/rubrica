<div class="container">
    <div id="mallaApp" class="col-sm-12">
        <input type="hidden" id="id_malla_curricular">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Malla Curricular</h4>
            </div>
            <div class="panel-body">
                <form id="form_malla" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboAsignaturas">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Presenciales:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_presenciales" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Autónomas:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_autonomas" value="0">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Tutorías:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_tutorias" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje5"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="editarItemMalla()">
                                Añadir
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
                        <th>Asignatura</th>
                        <th>Paralelo</th>
                        <th>Presencial</th>
                        <th>Autónomo</th>
                        <th>Tutoría</th>
                        <th colspan="2" align="center">Acciones</th>
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
        cargarParalelos();
        cargarAsignaturas();
    });
	function sel_texto(input) {
		$(input).select();
	}    
    function editarItemMalla()
    {
        // Recolección de datos
        var cont_errores = 0;
        var id_paralelo = $("#cboParalelos").val();
        var id_asignatura = $("#cboAsignaturas").val();
        var presenciales = $("#horas_presenciales").val();
        var autonomas = $("#horas_autonomas").val();
        var tutorias = $("#horas_tutorias").val();

        // Validación de ingreso de datos
        if (id_paralelo == 0) {
            $("#mensaje1").html("Debe elegir el paralelo...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (id_asignatura == 0) {
            $("#mensaje2").html("Debe elegir la asignatura...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if (presenciales.trim() == "") {
            $("#mensaje3").html("Debe ingresar el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (parseInt(presenciales) <= 0) {
            $("#mensaje3").html("Debe ingresar un valor entero mayor que cero! para el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }
        
        if (autonomas.trim() == "") {
            $("#mensaje4").html("Debe ingresar el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (parseInt(autonomas) < 0) {
            $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (tutorias.trim() == "") {
            $("#mensaje5").html("Debe ingresar el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (parseInt(tutorias) < 0) {
            $("#mensaje5").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la inserción o edición del item de la malla
            
        }
    }
	function cargarParalelos()
	{
		$.get("scripts/cargar_paralelos.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboParalelos").append(resultado);
				}
			}
		);
	}
    function cargarAsignaturas()
	{
		$.get("scripts/cargar_asignaturas.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboAsignaturas").append(resultado);
				}
			}
		);
	}
</script>