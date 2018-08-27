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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label class="control-label" style="position:relative; top:7px;">Presenciales:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_presenciales" value="0">
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" style="position:relative; top:7px;">Autónomas:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_autonomas" value="0">
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" style="position:relative; top:7px;">Tutorías:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="horas_tutorias" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary">
                                Añadir
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message2" class="fuente9 text-center"></div>
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