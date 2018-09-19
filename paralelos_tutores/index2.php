<div class="container">
    <div id="asociarParaleloTutorApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Paralelos con Tutores</h4>
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
                            <label class="control-label" style="position:relative; top:7px;">Docente:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboDocentes">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarAsociacion()">
                                Asociar
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
                            <th>Paralelo</th>
                            <th>Tutor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_paralelos_tutores">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-10 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Total Tutores:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_tutores" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
		cargar_paralelos();
		cargar_tutores();
		cargar_paralelos_tutores();
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
	function cargar_tutores()
	{
		$.get("scripts/cargar_tutores.php", function(resultado){
			if(resultado == false)
			{
				alert("No se han definido tutores en el presenta periodo lectivo...");
			}
			else
			{
				$('#cboDocentes').append(resultado);			
			}
		});	
	}
	function cargar_paralelos_tutores(iDesplegar)
	{
		$.get("paralelos_tutores/cargar_paralelos_tutores.php",
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					var datos = JSON.parse(resultado);
                    $("#lista_paralelos_tutores").html(datos.cadena);
                    $("#total_tutores").val(datos.total_tutores);
				}
			}
		);
	}
	function insertarAsociacion()
	{
		var id_paralelo = $("#cboParalelos").find(":selected").val();
		var id_usuario = $("#cboDocentes").find(":selected").val();
		if (id_paralelo == "") {
			document.getElementById("text_message").innerHTML = "Debe elegir un paralelo...";
			document.getElementById("cboParalelos").focus();
		} else if (id_usuario == "") {
			document.getElementById("text_message").innerHTML = "Debe elegir un docente...";
			document.getElementById("cboDocentes").focus();
		} else {
			$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_tutores/insertar_asociacion.php",
					data: "id_paralelo="+id_paralelo+"&id_usuario="+id_usuario,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_paralelos_tutores();
				  }
			});	
		}
	}
	function eliminarAsociacion(id_paralelo_tutor)
	{
		if (id_paralelo_tutor == "") {
			document.getElementById("text_message").innerHTML = "No se ha pasado el par&aacute;metro id_paralelo_tutor...";
		} else {
			$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "paralelos_tutores/eliminar_asociacion.php",
					data: "id_paralelo_tutor="+id_paralelo_tutor,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_paralelos_tutores(true);
				  }
			});	
		}
	}
</script>