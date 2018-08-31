<div class="container">
    <div id="asociarAsignaturaCursoApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Asignaturas con Cursos</h4>
            </div>
            <div class="panel-body">
                <form id="form_malla" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Curso:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboCursos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
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
                        <th>Curso</th>
                        <th>Asignatura</th>
                        <th colspan="3" align="center">Acciones</th>
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
                        <label class="control-label" style="position:relative; top:7px;">Total Asignaturas:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_asignaturas" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
		cargar_cursos();
		cargar_asignaturas();
        $("#cboCursos").change(function(e){
			e.preventDefault();
			cargar_asignaturas_asociadas();
		});
    });

    function cargar_cursos()
    {
        $.get("scripts/cargar_cursos.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#cboCursos').append(resultado);
            }
        });	
    }

    function cargar_asignaturas()
    {
        $.get("scripts/cargar_asignaturas.php", {}, 
            function(resultado){
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $('#cboAsignaturas').append(resultado);
                }
            }
        );	
    }

    function cargar_asignaturas_asociadas()
	{
		var id_curso = document.getElementById("cboCursos").value;
		$.get("asignaturas_cursos/cargar_asignaturas_asociadas.php", { id_curso: id_curso },
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
                    $("#total_asignaturas").val(datos.total_asignaturas);
				}
			}
		);
	}

    function insertarAsociacion()
	{
		var id_curso = document.getElementById("cboCursos").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
        var cont_errores = 0;

		if (id_curso == 0) {
			$("#mensaje1").html("Debe seleccionar un curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje1").fadeOut();
        }
        
        if (id_asignatura == 0) {
			$("#mensaje2").html("Debe elegir una asignatura...");
            $("#mensaje2").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
			$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/insertar_asociacion.php",
					data: "id_curso="+id_curso+"&id_asignatura="+id_asignatura,
					success: function(resultado){
                        $("#text_message").html(resultado);
                        cargar_asignaturas_asociadas();
				  }
			});			
		}	
	}

    function eliminarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/eliminar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas();
				  }
			});
		}
	}

    function subirAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/subir_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function bajarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/bajar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}
</script>