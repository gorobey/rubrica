<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>R&uacute;brica Web 2.0</title>
    <link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/funciones.js"></script>
    <script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
    <script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
    <script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            cargarAsignaturasDocente();
            cargarLeyendasAsistencia();
            
            $("#cboHoraClase").change(function(){
               cargarInasistencias(); 
            });

            $('#mostrar_ocultar_asignaturas').toggle( 

                    // Primer click
                    function(e){ 
                        $('#pag_asignaturas').slideUp();
                        $(this).html('<a href="#">Mostrar lista de asignaturas</a>');
                        e.preventDefault();
                    }, // Separamos las dos funciones con una coma

                    // Segundo click
                    function(e){ 
                        $('#pag_asignaturas').slideDown();
                        $(this).html('<a href="#">Ocultar lista de asignaturas</a>');
                        e.preventDefault();
                    }

            );
        });

        //Recibe fecha en formato DD/MM/YYYY
        function dia_semana(fecha) {
            fecha = fecha.split('-');
            if (fecha.length != 3) {
                return null;
            }
            //Vector para calcular día de la semana de un año regular.
            var regular = [0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5];
            //Vector para calcular día de la semana de un año bisiesto.
            var bisiesto = [0, 3, 4, 0, 2, 5, 0, 3, 6, 1, 4, 6];
            //Vector para hacer la traducción de resultado en día de la semana.
            //var semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            var semana = [7, 1, 2, 3, 4, 5, 6];
            //Día especificado en la fecha recibida por parametro.
            var dia = fecha[2];
            //Módulo acumulado del mes especificado en la fecha recibida por parametro.
            var mes = fecha[1] - 1;
            //Año especificado por la fecha recibida por parametros.
            var anno = fecha[0];
            //Comparación para saber si el año recibido es bisiesto.
            if ((anno % 4 == 0) && !(anno % 100 == 0 && anno % 400 != 0))
                mes = bisiesto[mes];
            else
                mes = regular[mes];
            //Se retorna el resultado del calculo del día de la semana.
            return semana[Math.ceil(Math.ceil(Math.ceil((anno - 1) % 7) + Math.ceil((Math.floor((anno - 1) / 4) - Math.floor((3 * (Math.floor((anno - 1) / 100) + 1)) / 4)) % 7) + mes + dia % 7) % 7)];
        }

        function cargarLeyendasAsistencia()
        {
            $.post("horarios/cargar_tipos_asistencia.php", {},
                    function (resultado)
                    {
                        if (resultado == false)
                        {
                            alert("Error");
                        } else
                        {
                            $("#leyendas").html(resultado);
                        }
                    }
            );
        }
        
        function cargarAsignaturasDocente()
        {
            contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
        }

        function contarAsignaturasDocente()
        {
            $.post("calificaciones/contar_asignaturas_docente.php", {},
                    function (resultado)
                    {
                        if (resultado == false) {
                            alert("Error");
                        } else {
                            var JSONNumRegistros = eval('(' + resultado + ')');
                            var total_registros = JSONNumRegistros.num_registros;
                            $("#num_asignaturas").html("N&uacute;mero de Asignaturas encontradas: " + total_registros);
                            paginarAsignaturasDocente(4, 1, total_registros);
                        }
                    }
            );
        }

        function paginarAsignaturasDocente(cantidad_registros, num_pagina, total_registros)
        {
            $.post("calificaciones/paginar_asignaturas_docente.php",
                    {
                        cantidad_registros: cantidad_registros,
                        num_pagina: num_pagina,
                        total_registros: total_registros
                    },
                    function (resultado)
                    {
                        $("#paginacion_asignaturas").html(resultado);
                    }
            );
            listarAsignaturasDocente(num_pagina);
        }

        function listarAsignaturasDocente(numero_pagina)
        {
            $.post("scripts/cargar_asignaturas_docente.php",
                    {
                        cantidad_registros: 4,
                        numero_pagina: numero_pagina
                    },
                    function (resultado)
                    {
                        if (resultado == false)
                        {
                            alert("Error");
                        } else
                        {
                            $("#lista_asignaturas").html(resultado);
                        }
                    }
            );
        }

	function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo)
	{
            document.getElementById("id_asignatura").value = id_asignatura;
            document.getElementById("id_paralelo").value = id_paralelo;
            var fecha = document.getElementById("fecha_asistencia").value;
            document.getElementById("cboHoraClase").disabled = true;
            $("#ver_reporte").hide();
            if (fecha == "") {
                $("#lista_estudiantes_paralelo").addClass("error");
                $("#lista_estudiantes_paralelo").html("Debe elegir una Fecha...");
            } else {
                $("#mensaje").html("");
                document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]";
                $("#lista_estudiantes_paralelo").removeClass("error");
                $("#lista_estudiantes_paralelo").html("");
                //Consultar el dia de la semana
                var ds_ordinal = dia_semana(fecha);
                var id_periodo_lectivo = document.getElementById("id_periodo_lectivo").value;
                $.post("horarios/consultar_id_dia_semana.php", 
                    {
                        ds_ordinal: ds_ordinal,
                        id_periodo_lectivo: id_periodo_lectivo
                    },
                    function (resultado)
                    {
                        $("#lista_estudiantes_paralelo").html("<img src='./imagenes/ajax-loader-blue.GIF' alt='Procesando...'>");
                        if (resultado == false) {
                            $("#lista_estudiantes_paralelo").addClass("error");
                            $("#lista_estudiantes_paralelo").html("No se ha definido el D&iacute;a de la Semana...");
                        } else {
                            var JSONIdDiaSemana = eval('(' + resultado + ')');
                            var id_dia_semana = JSONIdDiaSemana.id_dia_semana;
                            document.getElementById("id_dia_semana").value = id_dia_semana;
                            if(id_dia_semana==null) {
                                $("#lista_estudiantes_paralelo").addClass("error");
                                $("#lista_estudiantes_paralelo").html("No se ha definido el D&iacute;a de la Semana...");
                            } else {
                                $.post("horarios/consultar_horas_clase.php",
                                    {
                                        id_asignatura: id_asignatura,
                                        id_paralelo: id_paralelo,
                                        id_dia_semana: id_dia_semana
                                    },
                                    function (resultado)
                                    {
                                        document.getElementById("cboHoraClase").length = 1;
                                        if (resultado == false) {
                                            $("#lista_estudiantes_paralelo").addClass("error");
                                            $("#lista_estudiantes_paralelo").html("No se han definido Horas Clase para este D&iacute;a de la Semana...");
                                        } else {
                                            $("#cboHoraClase").append(resultado);
                                            $("#lista_estudiantes_paralelo").html("");
                                        }
                                    }
                                );
                            }
                        }
                    }
                );
            }
            document.getElementById("cboHoraClase").disabled = false;
	}
        
        function cargarInasistencias() {
            // Procedimiento para cargar las inasistencia de los estudiantes
            var id_asignatura = document.getElementById("id_asignatura").value;
            var id_paralelo = document.getElementById("id_paralelo").value;
            var id_hora_clase = document.getElementById("cboHoraClase").value;
            document.getElementById("id_hora_clase").value = id_hora_clase;
            var ae_fecha = document.getElementById("fecha_asistencia").value;
            document.getElementById("ae_fecha").value = ae_fecha;
            
            $("#lista_estudiantes_paralelo").html("<img src='./imagenes/ajax-loader-blue.GIF' alt='Procesando...'>");
            $("#mensaje_asistencia").html("");
            
            $.post("horarios/listar_inasistencia_paralelo.php", 
                { 
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    id_hora_clase: id_hora_clase,
                    ae_fecha: ae_fecha
                },
                function(resultado)
                {
                    //anadir el resultado al DOM
                    $("#lista_estudiantes_paralelo").html(resultado);
                    $("#ver_reporte").show();
                }
            );
        }

        function actualizar_asistencia(obj, id_estudiante) {
            // Procedimiento para insertar/actualizar las inasistencia de los estudiantes
            var id_asignatura = document.getElementById("id_asignatura").value;
            var id_paralelo = document.getElementById("id_paralelo").value;
            var id_dia_semana = document.getElementById("id_dia_semana").value;
            var id_hora_clase = document.getElementById("cboHoraClase").value;
            var ae_fecha = document.getElementById("fecha_asistencia").value;
            
            var name = obj.name;
            var id_inasistencia = $("input:radio[name="+name+"]:checked", "#formulario_asistencia").val();
            $.post("horarios/consultar_inasistencia_estudiante.php", 
                {
                    id_estudiante: id_estudiante,
                    id_asignatura: id_asignatura,
                    id_paralelo: id_paralelo,
                    id_hora_clase: id_hora_clase,
                    ae_fecha: ae_fecha
                },
                function(resultado) {
                    var JSONExiste = eval('(' + resultado + ')');
                    var contador = JSONExiste.contador;
                    if(contador > 0) { // Existe el registro se procede a actualizar
                        $.post("horarios/actualizar_inasistencia_estudiante.php",
                            {
                                id_estudiante: id_estudiante,
                                id_asignatura: id_asignatura,
                                id_paralelo: id_paralelo,
                                id_dia_semana: id_dia_semana,
                                id_hora_clase: id_hora_clase,
                                id_inasistencia: id_inasistencia,
                                ae_fecha: ae_fecha
                            },
                            function(resultado) {
                                $("#mensaje_asistencia").html(resultado);
                            }
                        )
                    } else { // Existe el registro se procede a insertar
                        $.post("horarios/insertar_inasistencia_estudiante.php",
                            {
                                id_estudiante: id_estudiante,
                                id_asignatura: id_asignatura,
                                id_paralelo: id_paralelo,
                                id_dia_semana: id_dia_semana,
                                id_hora_clase: id_hora_clase,
                                id_inasistencia: id_inasistencia,
                                ae_fecha: ae_fecha
                            },
                            function(resultado) {
                                $("#mensaje_asistencia").html(resultado);
                            }
                        )
                    }
                }
            );
        }
    </script>

    <body>
        <div id="pagina">
            <div id="titulo_pagina">
                <?php echo $_SESSION['titulo_pagina'] ?>
            </div>
            <div id="barra_opciones" style="background-color: #f5f5f5; height: 24px;">
                <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="5%" class="fuente9" align="right"> &nbsp;Fecha:&nbsp; </td>
                        <td width="16%" align="left"><input id="fecha_asistencia" class="cajaPequenia" type="text" disabled /> 
                            <img src="imagenes/calendario.png" id="calendario" name="calendario" width="16" height="16" title="calendario" alt="calendario" onmouseover="style.cursor = cursor"/> 
                            <script type="text/javascript">
                                Calendar.setup(
                                        {
                                            inputField: "fecha_asistencia",
                                            ifFormat: "%Y-%m-%d",
                                            button: "calendario"
                                        }
                                );
                            </script>
                        </td>
                        <td width="*"><div id="mensaje_asistencia" style="text-align: right;"></div></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="mensaje" class="error"></div>
        <div id="mensaje_slideToggle" class="paginacion">
            <div id="mostrar_ocultar_asignaturas" class="link_form" style="text-align:right;padding-right:2px;">
                <!-- Aqui va el hiperenlace para mostrar u ocultar la lista de asignaturas-->
                <a href="#">Ocultar la lista de asignaturas</a>
            </div>
        </div>
        <div id="pag_asignaturas">
            <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
            <div id="total_registros" class="paginacion">
                <table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
                    <tr>
                        <td>
                            <div id="num_asignaturas">&nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;</div>
                        </td>
                        <td>
                            <div id="paginacion_asignaturas"> 
                                <!-- Aqui va la paginacion de asignaturas --> 
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
            <div class="cabeceraTabla">
                <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                    <tr class="cabeceraTabla">
                        <td width="5%">Nro.</td>
                        <td width="39%" align="left">Asignatura</td>
                        <td width="32%" align="left">Curso</td>
                        <td width="6%" align="left">Paralelo</td>
                        <td width="18%" align="center">Acciones</td>
                    </tr>
                </table>
            </div>
            <div id="lista_asignaturas" style="text-align:center"> </div>
        </div>
        <!-- Aqui va la paginacion de los estudiantes encontrados -->
        <div id="barra_opciones2" style="background-color: #f5f5f5; height: 24px; padding-top: 4px; margin-top: 2px;">
            <table id="tabla_navegacion2" border="0" cellpadding="0" cellspacing="0" width="90%">
                <tr>
                    <td width="50%" class="fuente9" align="right"> &nbsp;Hora Clase:&nbsp; </td>
                    <td width="50%" align="left">
                        <select id="cboHoraClase" class="fuente8"> <option value="0"> Seleccione... </option> </select>
                    </td>
                    <td width="*"></td>
                </tr>
            </table>
        </div>
        <div id="total_registros_estudiantes" class="paginacion">
            <table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
                <tr>
                    <td>
                        <div id="num_estudiantes">&nbsp;TIPOS DE INASISTENCIA:&nbsp;</div>
                    </td>
                    <td>
                        <div id="leyendas"> 
                            <!-- Aqui van las leyendas de los tipos de inasistencia --> 
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
        <div class="cabeceraTabla">
            <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                <tr class="cabeceraTabla">
                    <td width="5%">Nro.</td>
                    <td width="5%">Id.</td>
                    <td width="30%" align="left">N&oacute;mina</td>
                    <td width="60%" align="left"><div id="txt_rubricas">Asistencia</div></td>
                    <!-- <td width="18%" align="center">Acciones</td> -->
                </tr>
            </table>
        </div>
        <form id="formulario_asistencia" action="reportes/reporte_asistencia_docente.php" method="post" target="_blank">
            <div id="img_loader_estudiantes" style="text-align:center"> </div>
            <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
            <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                <input id="id_asignatura" name="id_asignatura" type="hidden" />
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input id="id_dia_semana" name="id_dia_semana" type="hidden" />
                <input id="id_hora_clase" name="id_hora_clase" type="hidden" />
                <input id="ae_fecha" name="ae_fecha" type="hidden" />
                <input type="submit" value="Ver Reporte" />
            </div>
        </form>
        <input id="id_periodo_lectivo" type="hidden" value="<?php echo $_SESSION['id_periodo_lectivo'] ?>" />
        <input id="id_usuario" type="hidden" value="<?php echo $id_usuario ?>" />
    </body>
</html>
