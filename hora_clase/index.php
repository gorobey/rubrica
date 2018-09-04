<div class="container">
    <div id="horaClaseApp" class="col-sm-12">
        <input type="hidden" id="id_hora_clase">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Horas Clase</h4>
            </div>
            <div class="panel-body">
                <form id="form_hora_clase" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="hc_nombre" value="" onfocus="sel_texto(this)" autofocus>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora de Inicio:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9" placeholder="formato hh:mm" id="hc_hora_inicio" value="" onfocus="sel_texto(this)">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora de Fin:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9" placeholder="formato hh:mm" id="hc_hora_fin" value="" onfocus="sel_texto(this)">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Ordinal:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="number" min="1" class="form-control fuente9" id="hc_ordinal" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje2"></span>
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
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarHoraClase()">
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
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarHoraClase()">
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
                        <th>Nombre</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Fin</th>
                        <th>Ordinal</th>
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
        listarHorasClase();
    });
    function sel_texto(input) {
		$(input).select();
	}
    function listarHorasClase()
    {
        $.get("hora_clase/listar_horas_clase.php", {},
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
    function insertarHoraClase()
    {
        // Recolección de datos
        var cont_errores = 0;
        var nombre = $("#hc_nombre").val();
        var hora_inicio = $("#hc_hora_inicio").val();
        var hora_fin = $("#hc_hora_fin").val();
        var ordinal = $("#hc_ordinal").val();

        // Expresiones regulares para la validación de datos
        var reg_hora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/i;

        // Validación de ingreso de datos
        if (nombre.trim() == "") {
            $("#mensaje1").html("Debe ingresar el nombre de la hora clase.");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        } 
        
        if(hora_inicio.trim()==""){
            $("#mensaje2").html("Debe ingresar la hora de inicio de la hora clase.");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else if(!reg_hora.test(hora_inicio)){
            $("#mensaje2").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje2").fadeOut();
        }

        if(hora_fin.trim()==""){
            $("#mensaje3").html("Debe ingresar la hora de fin de la hora clase.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else if(!reg_hora.test(hora_fin)){
            $("#mensaje3").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje3").fadeOut();
        }

        if (ordinal.trim() == "") {
            $("#mensaje4").html("Debe ingresar el ordinal de las horas clase.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (parseInt(ordinal) < 1) {
            $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que uno! para el ordinal.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "hora_clase/insertar_hora_clase.php",
                method: "POST",
                type: "html",
                data: {
                    hc_nombre: nombre,
                    hc_hora_inicio: hora_inicio,
                    hc_hora_fin: hora_fin,
                    hc_ordinal: ordinal
                },
                success: function(response){
                    listarHorasClase();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
</script>