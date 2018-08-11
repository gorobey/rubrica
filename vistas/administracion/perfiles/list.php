<?php
    require_once("scripts/clases/class.permisos.php");
    $permisos_obj = new permisos();
    $id_menu = $_GET['id_menu'];
    $id_perfil = $_GET['id_perfil'];
    $id_usuario = $_GET['id_usuario'];
    $permisos = $permisos_obj->getPermisos($id_menu, $id_perfil);
    //Obtengo todos los perfiles ingresados en la base de datos
    $reg_perfiles = $perfiles->obtenerPerfiles();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Perfiles
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
                            <?php $link = "admin2.php?id_usuario=" . $id_usuario 
                                            . "&id_perfil=" . $id_perfil 
                                            . "&id_menu=" . $_GET['id_menu'] 
                                            . "&enlace=vistas/administracion/perfiles/add.php" 
                                            . "&file_js=vistas/administracion/perfiles/perfiles.js"; ?>
                            <a href="<?php echo $link ?>" class="btn btn-primary btn-flat"><span class="fa fa-plus"></span> Agregar Perfil</a>
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
                                    <th>Nombre</th>
                                    <th>opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reg_perfiles)): ?>
                                    <?php while ($reg_perfil = mysqli_fetch_object($reg_perfiles)): ?>
                                        <tr>
                                            <td><?php echo $reg_perfil->id_perfil ?></td>
                                            <td><?php echo $reg_perfil->pe_nombre ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" id="<?php echo $reg_perfil->id_perfil ?>" class="btn btn-info btn-view" data-toggle="modal" data-target="#modal-default" value="scripts/perfiles/view.php" title="Ver"><span class="fa fa-search"></span></button>
                                                    <?php if ($permisos->delete == 1): ?>
                                                        <a id="<?php echo $reg_perfil->id_perfil; ?>" href="scripts/perfiles/delete.php" class="btn btn-danger btn-delete" title="Eliminar"><span class="fa fa-remove"></span></a>
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
        <h4 class="modal-title">Informacion del Perfil</h4>
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
