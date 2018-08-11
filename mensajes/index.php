
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Mensajes
        <small id="subtitulo">Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div id="btn-Agregar" class="col-md-12">
                        <a href="#" class="btn btn-primary btn-flat btn-add"><span class="fa fa-plus"></span> Agregar Mensaje</a>
                    </div>
                </div>
                <div class="row">
                    <div id="cuadro2" class="col-sm-12 col-md-12 col-lg-12">
                        <form id="form_ingreso" class="form-horizontal" action="" method="POST">
                            <div class="form-group">
                                <h3 class="col-sm-offset-2 col-sm-8 text-center">					
                                Ingrese aqu&iacute; su mensaje</h3>
                            </div>
                            <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">
                            <input type="hidden" id="id_perfil" name="id_perfil" value="<?php echo $id_perfil; ?>">
                            <input type="hidden" id="opcion" name="opcion" value="registrar">
                            <div class="form-group">
                                <label for="txt_mensaje" class="col-sm-2 control-label">Mensaje:</label>
                                <div class="col-sm-8"><textarea id="txt_mensaje" name="txt_mensaje" class="form-control"></textarea></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input id="" type="submit" class="btn btn-primary" value="Guardar">
                                    <input id="btn_cancelar" type="button" class="btn btn-primary" value="Cancelar">
                                </div>
                            </div>
                        </form>
                        <div class="col-sm-offset-2 col-sm-8">
                            <p class="mensaje"></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                    <table id="tbl_mensajes" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Usuario</th>
                                <th>Texto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- DataTables -->
<script src="assets/template/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/template/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#form_ingreso").hide();
        listar();
        $("#form_ingreso").on("submit", function(e){
            e.preventDefault();
            var mensaje = $("#txt_mensaje").val();
            if(mensaje.trim()==""){
                $(".mensaje").html("Debe ingresar el texto de su mensaje.").css({"color": "#C9302C" });
                $(".mensaje").fadeOut(5000, function(){
                    $(this).html("");
                    $(this).fadeIn(3000);
                });
                return false;
            }
            var frm = $(this).serialize();
            $.ajax({
                method: "POST",
                url: "mensajes/guardar.php",
                data: frm
            }).done( function( info ){
                console.log( info );		
                var json_info = JSON.parse( info );
                mostrar_mensaje( json_info );
                limpiar_datos();
                listar();
            });
        });
        $(".btn-add").on("click",function(e){
            //Deshabilito el comportamiento habitual
            e.preventDefault();
            $("#subtitulo").html("Agregar Mensaje");
            $("#btn-Agregar").slideUp();
            $("#form_ingreso").slideDown();
            document.getElementById("form_ingreso").reset();
			document.getElementById("txt_mensaje").focus();
        });
        $("#btn_cancelar").on("click",function(){
            //Cancelar la acción del usuario
            $("#subtitulo").html("Listado");
            $("#form_ingreso").slideUp();
            $("#btn-Agregar").slideDown();
            $(".mensaje").fadeOut("slow");
        });
    });
    var mostrar_mensaje = function( informacion ){
        var texto = "", color = "";
        if( informacion.respuesta == "BIEN" ){
                texto = "<strong>Bien!</strong> " + informacion.mensaje;
                color = "#379911";
        }else if( informacion.respuesta == "ERROR"){
                texto = "<strong>Error</strong>, " + informacion.mensaje;
                color = "#C9302C";
        }else if( informacion.respuesta == "EXISTE" ){
                texto = "<strong>Información!</strong> el usuario ya existe.";
                color = "#5b94c5";
        }else if( informacion.respuesta == "VACIO" ){
                texto = "<strong>Advertencia!</strong> debe llenar todos los campos solicitados.";
                color = "#ddb11d";
        }else if( informacion.respuesta == "OPCION_VACIA" ){
                texto = "<strong>Advertencia!</strong> la opción no existe o esta vacia, recargar la página.";
                color = "#ddb11d";
        }
        $(".mensaje").html( texto ).css({"color": color });
        $(".mensaje").fadeOut(5000, function(){
            $(this).html("");
            $(this).fadeIn(3000);
        });	
    }
    var listar = function(){
        $('#tbl_mensajes').DataTable({
            "destroy": true,
            "ajax":{
                    "method":"POST",
                    "url":"mensajes/cargar_mensajes.php"
                },
            "columns":[
                {"data":"id_mensaje"},
                {"data":"usuario"},
                {"data":"me_texto"},
                {"data":"me_fecha"}
            ],
            "order": [],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por pagina",
                "zeroRecords": "No se encontraron resultados en su busqueda",
                "searchPlaceholder": "Buscar registros",
                "info": "Mostrando registros de _START_ al _END_ de un total de  _TOTAL_ registros",
                "infoEmpty": "No existen registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
            }
        });
    }
    var agregar_nuevo_mensaje = function(){
        limpiar_datos();
        $("#form_ingreso").slideUp();
        $("#btn-Agregar").slideDown();
    }
    var limpiar_datos = function(){
        $("#txt_mensaje").html("").focus();
        $("#subtitulo").html("Listado");
        $("#form_ingreso").slideUp();
        $("#btn-Agregar").slideDown();
    }
</script>
