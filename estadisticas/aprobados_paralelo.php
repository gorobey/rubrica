<div class="container">
    <div id="appAprobadosParalelo" class="col-sm-10 col-sm-offset-1">
        <h2>Aprobados por Paralelo</h2>
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Paralelo</h4>
            <form id="form_aprobados_paralelo" action="" class="app-form">
                <select id="cboParalelos" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="submit" type="button" class="btn btn-block btn-primary" onclick="verAprobadosParalelo()">
                    Consultar
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
        </div>
    </div>
    <div id="grafico">
        <!-- Aqui va el grafico estadistico de aprobados por paralelo -->
    </div>
</div>