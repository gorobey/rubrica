<div class="container">
    <div id="distributivoApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Distributivo Docente</h4>
            </div>
            <div class="panel-body">
                <form id="form_distributivo" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Docente:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboDocentes">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 2px;">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 2px;">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboAsignaturas">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarItemDistributivo()">
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
                            <th>Paralelo</th>
                            <th>Asignatura</th>
                            <th>Presencial</th>
                            <th>Autónomo</th>
                            <th>Tutoría</th>
                            <th>SubTotal</th>
                            <th><!-- Aqui va el boton de eliminar --></th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-3 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Presenciales:</label>
                    </div>
                    <div class="col-sm-1" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="horas_presenciales" value="0" disabled>
                    </div>
                    <div class="col-sm-3 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Tutorias:</label>
                    </div>
                    <div class="col-sm-1" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="horas_tutorias" value="0" disabled>
                    </div>
                    <div class="col-sm-3 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Total Horas:</label>
                    </div>
                    <div class="col-sm-1" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_horas" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargarDocentes();
        cargarParalelos();
        $("#cboParalelos").change(function(e){
            e.preventDefault();
            $("#text_message").html("");
            cargarAsignaturas($(this).val());
        });
    });
	function cargarDocentes()
	{
		$.get("scripts/cargar_docentes.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboDocentes").append(resultado);
				}
			}
		);
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
    function cargarAsignaturas(id_paralelo)
	{
        $.ajax({
            url: "scripts/cargar_asignaturas_por_paralelo.php",
            method: "POST",
            type: "html",
            data: {
                id_paralelo: id_paralelo
            },
            success: function(response){
                document.getElementById("cboAsignaturas").length = 0;
                $("#cboAsignaturas").append("<option value='0'>Seleccione...</option>");
                $("#cboAsignaturas").append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
	}
    function insertarItemDistributivo()
    {
        var id_usuario = $("#cboDocentes").val();
        var id_paralelo = $("#cboParalelos").val();
        var id_asignatura = $("#cboAsignaturas").val();

        var cont_errores = 0;

        // Validación de ingreso de datos
        if (id_usuario == 0) {
            $("#mensaje1").html("Debe elegir el docente...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (id_paralelo == 0) {
            $("#mensaje2").html("Debe elegir el paralelo...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if (id_asignatura == 0) {
            $("#mensaje3").html("Debe elegir la asignatura...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut("slow");
        }

        // Si no hay errores en el ingreso de datos, procedemos a insertar el item del distributivo
        if (cont_errores == 0) {
            $.ajax({
                url: "distributivo/insertar_item_distributivo.php",
                method: "POST",
                type: "html",
                data: {
                    id_usuario: id_usuario,
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura
                },
                success: function(response){
                    //listarDistributivo();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
</script>