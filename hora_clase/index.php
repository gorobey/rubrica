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
                            <input type="text" class="form-control fuente9" id="hc_hora_inicio" value="0" onfocus="sel_texto(this)">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora de Fin:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9" id="hc_hora_fin" value="0" onfocus="sel_texto(this)">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Ordinal:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="number" min="1" class="form-control fuente9" id="horas_tutorias" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
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
    });
    function sel_texto(input) {
		$(input).select();
	}
</script>