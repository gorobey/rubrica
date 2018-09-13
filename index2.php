<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LogIn</title>
    <link rel="stylesheet" href="./css/main.css">
</head>
<body class="cover" style="background-image: url('./assets/images/loginFont.jpg');">
    <form action="" method="" autocomplete="off" class="full-box logInForm">
        <p class="text-center text-muted text-uppercase">Introduzca sus datos de ingreso</p>
        <div class="form-group label-floating">
            <label class="control-label" for="UserName">Usuario</label>
            <input class="form-control" id="UserName" type="text">
            <p class="help-block">Escribe tu nombre de usuario</p>
		</div>
        <div class="form-group label-floating">
            <label class="control-label" for="UserPass">Contraseña</label>
            <input class="form-control" id="UserPass" type="text">
            <p class="help-block">Escribe tú contraseña</p>
		</div>
        <div class="form-group">
            <label class="control-label">Periodo Lectivo</label>
            <select class="form-control">
                <option>2018-2019</option>
                <option>2017-2018</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">Perfil</label>
            <select class="form-control">
                <option>Administrador</option>
                <option>Docente</option>
            </select>
        </div>
        <div class="form-group text-center">
			<input type="submit" value="Iniciar sesión" class="btn btn-raised btn-danger">
		</div>
    </form>
    <!--====== Scripts -->
	<!-- jQuery 3 -->
	<script src="assets/template/jquery/jquery.min.js"></script>
	<!-- Bootstrap 3.3.7 -->
	<script src="assets/template/bootstrap/js/bootstrap.min.js"></script>
    <script src="./js/material.min.js"></script>
    <script src="./js/ripples.min.js"></script>
	<script src="./js/sweetalert2.min.js"></script>
	<script src="./js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="./js/main.js"></script>
    <script>
		$.material.init();
	</script>
</body>
</html>