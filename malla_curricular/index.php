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
                            <input type="text" class="form-control fuente9 text-right" id="horas_autonomas" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Tutorías:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_tutorias" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
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
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarItemMalla()">
                                Añadir
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 4px;" id="botones_edicion">
                        <div class="col-sm-6">
                            <button id="btn-cancel" type="button" class="btn btn-block" onclick="cancelarEdicion()">
                                Cancelar
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarItemMalla()">
                                Actualizar
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
        $("#botones_edicion").hide();
        cargarParalelos();
        $("#cboParalelos").change(function(e){
            e.preventDefault();
            $("#text_message").html("");
            cargarAsignaturas($(this).val());
            listarMalla();
        })
    });
	function sel_texto(input) {
		$(input).select();
	}
    function cancelarEdicion()
    {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
        $("#cboParalelos").attr("disabled",false);
        $("#cboAsignaturas").attr("disabled",false);
    }
    function editarMalla(id_malla)
    {
        $("#id_malla_curricular").val(id_malla);
        $("#cboParalelos").attr("disabled",true);
        $("#cboAsignaturas").attr("disabled",true);
        $("#botones_insercion").hide();
        $("#botones_edicion").show();
        // Primero obtengo los datos del item elegido
        $.ajax({
            url: "malla_curricular/obtener_item_malla.php",
            method: "POST",
            type: "html",
            data: {
                id_malla_curricular: id_malla
            },
            success: function(response){
                var malla = jQuery.parseJSON(response);
                $("#horas_presenciales").val(malla.ma_horas_presenciales);
                $("#horas_autonomas").val(malla.ma_horas_autonomas);
                $("#horas_tutorias").val(malla.ma_horas_tutorias);
                // Procedimiento para "setear" el índice de cboAsignaturas
                var id_asignatura = malla.id_asignatura;
                var sel = document.getElementById("cboAsignaturas"); 
                for (var i = 0; i < sel.length; i++) 
                {
                    //console.log(sel[i].value+"\n");
                    if (sel[i].value == id_asignatura) {
                        document.getElementById("cboAsignaturas").selectedIndex = i;
                    }
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function insertarItemMalla()
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
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "malla_curricular/insertar_item_malla.php",
                method: "POST",
                type: "html",
                data: {
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    ma_horas_presenciales: presenciales,
                    ma_horas_autonomas: autonomas,
                    ma_horas_tutorias: tutorias
                },
                success: function(response){
                    listarMalla();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function actualizarItemMalla()
    {
        // Recolección de datos
        var cont_errores = 0;
        var id_malla = $("#id_malla_curricular").val();
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
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "malla_curricular/actualizar_item_malla.php",
                method: "POST",
                type: "html",
                data: {
                    id_malla_curricular: id_malla,
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    ma_horas_presenciales: presenciales,
                    ma_horas_autonomas: autonomas,
                    ma_horas_tutorias: tutorias
                },
                success: function(response){
                    listarMalla();
                    cancelarEdicion();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    function eliminarMalla(id_malla){
        //Elimino el item de la malla mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "malla_curricular/eliminar_item_malla.php",
            method: "POST",
            type: "html",
            data: {
                id_malla_curricular: id_malla
            },
            success: function(response){
                $("#text_message").html(response);
                listarMalla();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function listarMalla()
    {
        var id_paralelo = $("#cboParalelos").val();
        if(id_paralelo==0){
            $("#text_message").html("<tr><td colspan='8' align='center'>Debes seleccionar un paralelo...</td></tr>");
        }else{
            $.get("malla_curricular/listar_malla.php", 
                { 
                    id_paralelo: id_paralelo
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
</script>