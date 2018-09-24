<div class="container">
    <div id="div_leccionario" class="col-sm-10 col-sm-offset-1">
        <h2>Leccionario</h2>
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Paralelo</h4>
            <form id="form_leccionario" action="php_excel/generar_leccionario.php" method="POST" class="app-form">
                <div class="row">
                    <div class="col-sm-2 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                    </div>
                    <div class="col-sm-10">
                        <select class="form-control fuente9" id="cboParalelos" name="cboParalelos">
                            <option value="0">Seleccione...</option>
                        </select>
                        <span class="help-desk error" id="mensaje1"></span>
                    </div>
                </div>
				<div class="row">
					<div class="col-sm-2 text-right">
						<label class="control-label" style="position:relative; top:7px;">D&iacute;a:</label>
					</div>
					<div class="col-sm-10" style="margin-top: 2px;">
						<select class="form-control fuente9" id="cboDiasSemana" name="cboDiasSemana">
							<option value="0">Seleccione...</option>
						</select>
						<span class="help-desk error" id="mensaje2"></span>
					</div>
				</div>
				<div class="row" id="botones_insercion">
					<div class="col-sm-12" style="margin-top: 4px;">
						<button id="btn-generar" type="submit" class="btn btn-block btn-primary">
							Generar Leccionario
						</button>
					</div>
				</div>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargar_paralelos();
		cargarDiasSemana();
    });
    function cargar_paralelos()
	{
		$.get("scripts/cargar_paralelos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboParalelos').append(resultado);			
			}
		});	
	}
	function cargarDiasSemana()
	{
		$.get("scripts/cargar_ordinal_dias_semana.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboDiasSemana").append(resultado);
				}
			}
		);
    }
</script>