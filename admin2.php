<?php
	session_start();
	// Conexión con la base de datos
	require_once("scripts/clases/class.mysql.php");
	require_once("scripts/clases/class.periodos_lectivos.php");
	if (!isset($_SESSION['usuario_logueado']))
		header("Location: index.php");
	else {
		// Recepción de las variables GET
		$id_usuario = $_GET["id_usuario"];
		$id_perfil = $_GET["id_perfil"];
		$db = new mysql();
		//Obtengo los nombres del usuario
		$consulta = $db->consulta("SELECT * FROM sw_usuario WHERE id_usuario = $id_usuario");
		$usuario = $db->fetch_assoc($consulta);
		$nombreUsuario = $usuario["us_nombres"];
		if (!isset($_GET['nivel'])) {
			$titulo = "SIAE-WEB Admin";
			$enlace = "central.php";
		} else {
			if (isset($_GET["enlace"])) {
				$enlace = $_GET["enlace"];
			} else {
				$consulta = $db->consulta("SELECT mnu_texto, mnu_enlace, mnu_nivel FROM sw_menu WHERE id_menu = " . $_GET['id_menu']);
				$pagina = $db->fetch_assoc($consulta);
				$titulo = $pagina["mnu_texto"];
				$enlace = $pagina["mnu_enlace"];
				$nivel = $pagina["mnu_nivel"];
				$_SESSION['titulo_pagina'] = $titulo;
			}
		}
	}
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	//Obtengo los años de inicio y de fin del periodo lectivo actual
	$periodos_lectivos = new periodos_lectivos();
	$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($id_periodo_lectivo);
	$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $titulo ?></title>
	<script src="js/keypress.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<!-- jquery-ui -->
	<link rel="stylesheet" href="assets/template/jquery-ui/jquery-ui.css">
	<link href="estilos.css" rel="stylesheet" type="text/css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<!-- jquery-ui -->
	<script src="assets/template/jquery-ui/jquery-ui.js"></script>
	<!-- plotly -->
	<script src="js/plotly-latest.min.js"></script>
	<!-- Theme style -->
	<link rel="stylesheet" href="assets/template/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="assets/template/dist/css/skins/_all-skins.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="assets/template/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="assets/template/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="assets/template/datatables.net-bs/css/dataTables.bootstrap.min.css">
	<style>
	    .error {
			color: #ff0000;
			display: none;
		}
		.taskDone {
			text-decoration: line-through;
		}
		.success {
			color:#006400;
			text-align:center;
			font-family:Arial, Helvetica, sans-serif;
		}
		.negrita {
			font-weight: bold;
		}
		.removeRow
		{
			background-color: #FF0000;
			color:#FFFFFF;
		}
	</style>
</head>
<body>
	<input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>">
	<input type="hidden" id="nombrePeriodoLectivo" value="<?php echo $nombrePeriodoLectivo ?>">
	<?php
		$menus = $db->consulta("SELECT * FROM sw_menu WHERE id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");
	?>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="admin2.php?id_usuario=<?php echo $id_usuario?>&id_perfil=<?php echo $id_perfil?>&enlace=central.php&nivel=0">SIAE <?php echo $nombrePeriodoLectivo; ?></a>
			</div>
			<ul class="nav navbar-nav">
				<!-- <li class="active"><a href="admin2.php?id_usuario=$id_usuario&id_perfil=$id_perfil">Inicio</a></li> -->
				<?php
					while($menu=$db->fetch_assoc($menus)) {
						$submenus = $db->consulta("SELECT * FROM sw_menu WHERE mnu_padre = " . $menu['id_menu'] . " ORDER BY mnu_orden");
						$num_submenus = $db->num_rows($submenus);
						if($num_submenus > 0) {
				?>
							<li class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" href="#">
									<?php echo $menu["mnu_texto"] ?> <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
				<?php
							while($submenu=$db->fetch_assoc($submenus)) {
				?>
									<li>
										<a href="admin2.php?id_usuario=<?php echo $id_usuario?>&id_perfil=<?php echo $id_perfil?>&id_menu=<?php echo $submenu["id_menu"]?>&nivel=<?php echo $submenu["mnu_nivel"]?>">
											<?php echo $submenu["mnu_texto"] ?>
										</a>
									</li>
				<?php
							}
				?>
								</ul>
							</li>
				<?php
						} else {
				?>
							<li>
								<a href="admin2.php?id_usuario=<?php echo $id_usuario?>&id_perfil=<?php echo $id_perfil?>&id_menu=<?php echo $menu["id_menu"]?>&nivel=<?php echo $menu["mnu_nivel"]?>">
									<?php echo $menu["mnu_texto"] ?>
								</a>
							</li>
				<?php
						}
					}
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<span class="glyphicon glyphicon-user"></span> <?php echo $nombreUsuario ?> <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="admin2.php?id_usuario=<?php echo $id_usuario?>&id_perfil=<?php echo $id_perfil?>&enlace=change_password.php&nivel=0"> Cambiar Clave</a></li>
						<li><a href="admin2.php?id_usuario=<?php echo $id_usuario?>&id_perfil=<?php echo $id_perfil?>&enlace=mensajes/index.php&nivel=0"> Mensajes</a></li>
						<li><a href="logout.php"> Salir</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
	<?php include($enlace); ?>
</body>
</html>