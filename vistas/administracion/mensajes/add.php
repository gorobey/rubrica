
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Mensajes
        <small>Nuevo</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p><i class="icon fa fa-ban"></i> <?php echo $this->session->flashdata('error') ?></p>
                            </div>
                        <?php endif ?>
                        <form id="form_mensajes" action="" method="POST">
                            <div class="form-group">
                                <label for="texto">Texto:</label>
                                <textarea class="form-control" id="texto" name="texto" value="" rows="5" autofocus></textarea>
                                <span id="mensaje1" class='help-block'></span>
                            </div>
                            <div class="form-group">
                                <button id="add-message" type="submit" class="btn btn-success btn-flat">Guardar</button>
                                <?php $link = "admin2.php?id_usuario=" . $id_usuario 
                                                . "&id_perfil=" . $id_perfil 
                                                . "&id_menu=" . $_GET['id_menu'] 
                                                . "&enlace=vistas/administracion/mensajes/list.php" 
                                                . "&file_js=vistas/administracion/mensajes/mensajes.js" ?>
                                <a href="<?php echo $link; ?>" class="btn btn-primary btn-flat">Regresar a la lista</a>
                            </div>
                        </form>
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
