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
                            <input type="text" class="form-control fuente9 text-right" id="ds_nombre" value="">
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Ordinal:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="ds_ordinal" value="0">
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
        // JQuery Listo para utilizar
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

    function editPerfil(id){
        //Primero obtengo el nombre del perfil seleccionado
        $.ajax({
            url: "perfil/obtener_perfil.php",
            method: "POST",
            type: "html",
            data: {
                id: id
            },
            success: function(response){
                var perfil = jQuery.parseJSON(response);
                $("#perfil").val(perfil.pe_nombre);
                $("#subtitulo").html("Actualiza este Perfil");
                $("#enviar").val("Actualizar");
                $("#id").val(id);
                $("#perfil").focus();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function deletePerfil(id){
        //Elimino el perfil mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "perfil/eliminar_perfil.php",
            method: "POST",
            type: "html",
            data: {
                id: id
            },
            success: function(response){
                $("#text_message").html(response);
                cargarPerfiles();
                $("#perfil").focus();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>