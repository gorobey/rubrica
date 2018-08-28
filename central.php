
        <!-- =============================================== -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Dashboard
                    <small>Sistema Integrado de Administración Estudiantil</small>
                </h1>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-lg-3 col-xs-3">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <?php 
                                    $result = $db->consulta("SELECT u.id_usuario
                                                               FROM sw_usuario u, 
                                                                    sw_perfil p,
                                                                    sw_usuario_perfil up
                                                              WHERE p.id_perfil = up.id_perfil
                                                                AND u.id_usuario = up.id_usuario
                                                                AND pe_nombre = 'Autoridad'
                                                                AND us_activo = 1");
                                    $num_autoridades = $db->num_rows($result);
                                ?>
                                <h3><?php echo $num_autoridades; ?></h3>

                                <p><?php echo ($num_autoridades==1)?"Autoridad":"Autoridades";?></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-stalker"></i>
                            </div>
                            <!-- <a href="#" class="small-box-footer">Ver Autoridades <i class="fa fa-arrow-circle-right"></i></a> -->
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-3">
                        <!-- small box -->
                        <div class="small-box bg-green">
                            <div class="inner">
                                <?php 
                                    $result = $db->consulta("SELECT u.id_usuario
                                                                FROM sw_usuario u, 
                                                                    sw_perfil p,
                                                                    sw_usuario_perfil up
                                                                WHERE p.id_perfil = up.id_perfil
                                                                AND u.id_usuario = up.id_usuario
                                                                AND pe_nombre = 'Docente'
                                                                AND us_activo = 1");
                                    $num_docentes = $db->num_rows($result);
                                ?>
                                <h3><?php echo $num_docentes; ?></h3>

                                <p><?php echo ($num_docentes==1)?"Docente":"Docentes";?></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                            <!-- <a href="#" class="small-box-footer">Ver Docentes <i class="fa fa-arrow-circle-right"></i></a> -->
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-3">
                        <!-- small box -->
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <?php
                                    $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
                                    $result = $db->consulta("SELECT e.id_estudiante
                                                                FROM sw_estudiante e, 
                                                                    sw_estudiante_periodo_lectivo ep
                                                                WHERE e.id_estudiante = ep.id_estudiante
                                                                AND id_periodo_lectivo = $id_periodo_lectivo");
                                    $num_estudiantes = $db->num_rows($result);
                                ?>
                                <h3><?php echo $num_estudiantes; ?></h3>

                                <p><?php echo ($num_estudiantes==1)?"Estudiante":"Estudiantes";?></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <!-- <a href="#" class="small-box-footer">Ver Estudiantes <i class="fa fa-arrow-circle-right"></i></a> -->
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-3">
                        <!-- small box -->
                        <div class="small-box bg-red">
                            <div class="inner">
                                <?php
                                    $result = $db->consulta("SELECT id_representante
                                                               FROM sw_representante r,
                                                                    sw_estudiante_periodo_lectivo ep
                                                              WHERE r.id_estudiante = ep.id_estudiante
                                                                AND id_periodo_lectivo = $id_periodo_lectivo");
                                    $num_representantes = $db->num_rows($result);
                                ?>
                                <h3><?php echo $num_representantes; ?></h3>

                                <p><?php echo ($num_representantes==1)?"Representante":"Representantes";?></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-woman"></i>
                            </div>
                            <!-- <a href="#" class="small-box-footer">Ver Representantes <i class="fa fa-arrow-circle-right"></i></a> -->
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Aquí irá el gráfico estadístico -->
                <div class="row">
                    <div class="col-sm-12">
                        <div id="graficoBarras">

                        <div>
                    </div>
                </div>
                <!-- /.row -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong><?php echo date("  Y"); ?> &copy; <a href="http://colegionocturnosalamanca.com" target="_blank">Unidad Educativa PCEI Fiscal Salamanca</a>.</strong> Todos los derechos reservados.
        </footer>
    </div>
    <!-- ./wrapper -->

<script type="text/javascript">
	$(document).ready(function(){
        var id_periodo_lectivo = $("#id_periodo_lectivo").val();
        $.ajax({
            url: "getNumberOfStudents.php",
            type: "POST",
            data: {id_periodo_lectivo:id_periodo_lectivo},
            dataType: "json",
            success: function(data){
                //console.log(data);
                var paralelos = new Array();
                var cuantos = new Array();
                $.each(data,function(key,value){
                    paralelos.push(value.paralelo);
                    numero = Number(value.numero);
                    cuantos.push(numero);
                });
                var per_lectivo = $("#nombrePeriodoLectivo").val();
                graficar(paralelos, cuantos, "graficoBarras", per_lectivo);
            }
        });
	});

    function graficar(paralelos, cuantos, idDiv, per_lectivo)
    {
        var xValue = paralelos;
        var yValue = cuantos;

        var trace1 = {
            x: xValue,
            y: yValue,
            type: 'bar',
            text: yValue,
            textposition: 'auto',
            hoverinfo: 'none',
            marker: {
                color: 'rgb(158,202,225)',
                opacity: 0.6,
                line: {
                color: 'rbg(8,48,107)',
                width: 1.5
                }
            }
        };

        var data = [trace1];

        var layout = {
            title: 'Número de estudiantes por paralelo ' + per_lectivo
        };

        Plotly.newPlot(idDiv, data, layout);
    }

</script>