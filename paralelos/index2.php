<div class="container">
    <div id="div_paralelos" class="col-sm-10 col-sm-offset-1">
        <h2>Paralelos</h2>
        <input type="hidden" id="id_paralelo">
        <!-- panel -->
        <div class="panel panel-default">
            <form id="form_paralelos" action="" class="app-form">
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Paralelo
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Especialidad</th>
                        <th>Curso</th>
                        <th>Nombre</th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Borrar --></th>
                        <th><!-- Botón Subir --></th>
                        <th><!-- Botón Bajar --></th>
                    </tr>
                </thead>
                <tbody id="lista_paralelos">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- New Paralelos Modal -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel1">Nuevo Paralelo</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Curso:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_cursos">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_pa_nombre" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addParalelo()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Paralelo Modal -->
    <div class="modal fade" id="editMenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h4 class="modal-title" id="myModalLabel3">Editar Paralelo</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_pa_nombre" name="edit_pa_nombre" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateParalelo()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled","true");
        cargar_paralelos();
    });
    function cargar_paralelos()
    {
        $.get("paralelos/cargar_paralelos.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#lista_paralelos').html(resultado);
            }
        });	
    }
</script>