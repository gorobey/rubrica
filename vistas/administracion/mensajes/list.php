<?php
    require_once("scripts/clases/class.permisos.php");
    $permisos_obj = new permisos();
    $id_menu = $_GET['id_menu'];
    $id_perfil = $_GET['id_perfil'];
    $id_usuario = $_GET['id_usuario'];
    $permisos = $permisos_obj->getPermisos($id_menu, $id_perfil);
    //Obtengo todos los mensajes ingresados en la base de datos
    $mensajes = $objmensajes->obtenerMensajes();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Mensajes
        <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($permisos->insert == 1): ?>
                            <?php $link = "admin2.php?id_usuario=" 
                                            . $id_usuario . "&id_perfil=" 
                                            . $id_perfil 
                                            . "&id_menu=" . $_GET['id_menu'] 
                                            . "&enlace=vistas/administracion/mensajes/add.php" 
                                            . "&file_js=vistas/administracion/mensajes/mensajes.js" ?>
                            <a href="<?php echo $link ?>" class="btn btn-primary btn-flat"><span class="fa fa-plus"></span> Agregar Mensaje</a>
                        <?php endif ?>
                    </div>
                </div>
                <hr>
                <input type="hidden" id="id_usuario" value="<?php echo $id_usuario ?>">
                <input type="hidden" id="id_perfil" value="<?php echo $id_perfil ?>">
                <input type="hidden" id="id_menu" value="<?php echo $id_menu ?>">
                <div class="row">
                    <div class="col-md-12">
                        <table id="tb_no_ordered" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Usuario</th>
                                    <th>Texto</th>
                                    <th>Fecha</th>
                                    <th>opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($mensajes)): ?>
                                    <?php while ($mensaje = mysqli_fetch_object($mensajes)): ?>
                                        <tr>
                                            <td><?php echo $mensaje->id_mensaje ?></td>
                                            <?php $usuario = $mensaje->us_titulo . " " . $mensaje->us_apellidos . " " . $mensaje->us_nombres ?>
                                            <td><?php echo $usuario ?></td>
                                            <td><?php echo $mensaje->me_texto ?></td>
                                            <td><?php echo $mensaje->me_fecha ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" id="<?php echo $mensaje->id_mensaje ?>" class="btn btn-info btn-view" data-toggle="modal" data-target="#modal-default" value="scripts/mensajes/view.php" title="Ver"><span class="fa fa-search"></span></button>
                                                    <?php if ($permisos->delete == 1): ?>
                                                        <a id="<?php echo $mensaje->id_mensaje; ?>" href="scripts/mensajes/delete.php" class="btn btn-danger btn-delete" title="Eliminar"><span class="fa fa-remove"></span></a>
                                                    <?php endif ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>
                                <?php endif ?>
                            </tbody>
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

<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Informacion del Mensaje</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
