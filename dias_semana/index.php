<div class="container">
    <div id="diasSemanaApp" class="col-sm-9 col-sm-offset-1">
        <input type="hidden" id="id_dia">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Días de la Semana</h4>
            </div>
            <div class="panel-body">
                <form id="form_dias_semana" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="ds_nombre" value="" onfocus="sel_texto(this)" onkeypress="return permite(event,'car')">
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Ordinal:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <input type="number" min="0" class="form-control fuente9" id="ds_ordinal" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarDiaSemana()">
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
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarDiaSemana()">
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
                        <th colspan="2" align="center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_dias">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#botones_edicion").hide();
        cargarDiasSemana();
        $("#mensaje1").html("");
        $("#mensaje2").html("");
        // Código para editar un dia de la semana
        /* $('#btn-add-item').click(function(event){

                if($(this).html() == "Añadir"){
                    
                }else if($(this).html() == "Actualizar"){
                    var id = $("#id_dia").val();
                    $.ajax({
                        url: "dias_semana/actualizar_dia_semana.php",
                        method: "POST",
                        data: {
                            id_dia_semana: id,
                            ds_nombre: nombre,
                            ds_ordinal: ordinal
                        },
                        type: "html",
                        success: function(response){
                            $('#text_message').html(response);
                            $("#ds_nombre").val("");
                            $("#ds_ordinal").val("0");
                            $("#enviar").val("Añadir");
                            cargarDiasSemana();
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            }
        }); */
    });

	function sel_texto(input) {
		$(input).select();
	}

    function cancelarEdicion()
    {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
    }

    function cargarDiasSemana(){
        // Obtengo todos los dias de la semana ingresados en la base de datos
        $.ajax({
            url: "dias_semana/cargar_dias_semana.php",
            method: "GET",
            type: "html",
            success: function(response){
                $("#nombreDia").html("");
                $('#lista_dias').html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarDiaSemana()
    {
        // Procedimiento para insertar un dia de la semana
        var nombre = $('#ds_nombre').val();
        var ordinal = $('#ds_ordinal').val();
        var cont_errores = 0;

        if(nombre.trim()==""){
            $("#mensaje1").html("Debes ingresar el nombre del dia de la semana...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        } 
        
        if(ordinal.trim()==""){
            $("#mensaje2").html("Debes ingresar el ordinal del dia de la semana...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if(ordinal.trim()=="0"){
            $("#mensaje2").html("Debes ingresar un numero mayor que cero para el ordinal...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "dias_semana/insertar_dia_semana.php",
                method: "POST",
                data: {
                    ds_nombre: nombre,
                    ds_ordinal: ordinal
                },
                type: "html",
                success: function(response){
                    $('#text_message').html(response);
                    $("#ds_nombre").val("");
                    $("#ds_ordinal").val("0");
                    cargarDiasSemana();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function editDiaSemana(id_dia)
    {
        $("#id_dia").val(id_dia);
        $("#botones_insercion").hide();
        $("#botones_edicion").show();
        // Primero obtengo los datos del item elegido
        $.ajax({
            url: "dias_semana/obtener_dia_semana.php",
            method: "POST",
            type: "html",
            data: {
                id_dia_semana: id_dia
            },
            success: function(response){
                var dia = jQuery.parseJSON(response);
                $("#ds_nombre").val(dia.ds_nombre);
                $("#ds_ordinal").val(dia.ds_ordinal);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function actualizarDiaSemana()
    {
        // Recolección de datos
        var cont_errores = 0;
        var id_dia = $("#id_dia").val();
        var nombre = $("#ds_nombre").val();
        var ordinal = $("#ds_ordinal").val();

        // Validación de ingreso de datos
        if(nombre.trim()==""){
            $("#mensaje1").html("Debes ingresar el nombre del dia de la semana...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        } 
        
        if(ordinal.trim()==""){
            $("#mensaje2").html("Debes ingresar el ordinal del dia de la semana...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if(ordinal.trim()=="0"){
            $("#mensaje2").html("Debes ingresar un numero mayor que cero para el ordinal...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la actualizacion del dia de la semana
            $.ajax({
                url: "dias_semana/actualizar_dia_semana.php",
                method: "POST",
                type: "html",
                data: {
                    id_dia_semana: id_dia,
                    ds_nombre: nombre,
                    ds_ordinal: ordinal
                },
                success: function(response){
                    cargarDiasSemana();
                    cancelarEdicion();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function deleteDiaSemana(id_dia){
        //Elimino el dia de la semana mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "dias_semana/eliminar_dia_semana.php",
            method: "POST",
            type: "html",
            data: {
                id_dia_semana: id_dia
            },
            success: function(response){
                $("#text_message").html(response);
                cargarDiasSemana();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>