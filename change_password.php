<?php
	if (!isset($_SESSION['usuario_logueado']) OR !$_SESSION['usuario_logueado'])
		header("Location: index.php");
	else {
		if (!isset($_SESSION['id_usuario']) OR !$_SESSION['id_usuario'])
			header("Location: index.php");
		else {
			require_once("scripts/clases/class.mysql.php");
            require_once("scripts/clases/class.usuarios.php");
            require_once("scripts/clases/class.encrypter.php");
			$usuario = new usuarios();
			$usuarios = $usuario->obtenerUsuario($_SESSION['id_usuario']);
			$us_fullname = $usuarios->us_fullname;
			$password = encrypter::decrypt($usuarios->us_password);
		}
	}
?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Cambiar Clave</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <p class="text-center">Use el formulario de abajo para cambiar su clave. Su clave no puede ser la misma que su nombre de usuario.</p>
            <form action="scripts/actualizar_clave.php" method="post" id="passwordForm">
                <input type="hidden" name="fullname" id="fullname" value="<?php echo $us_fullname ?>">
                <input type="hidden" name="bdpassword" id="bdpassword" value="<?php echo $password ?>">
                <input type="password" class="input-lg form-control" name="password" id="password" placeholder="Clave Actual" autocomplete="off" autofocus>
                <div class="row">
                    <div class="col-sm-12">
                        <span id="bdpwdmatch" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Coincide la Clave Actual
                    </div>
                </div>                
                <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="Clave Nueva" autocomplete="off">
                <div class="row">
                    <div class="col-sm-6">
                        <span id="8char" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> 8 Caracteres de Longitud<br>
                        <span id="ucase" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Una Letra May&uacute;scula
                    </div>
                    <div class="col-sm-6">
                        <span id="lcase" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Una Letra Min&uacute;scula<br>
                        <span id="num" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Un N&uacute;mero
                    </div>
                </div>
                <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Redigite la Clave Nueva" autocomplete="off">
                <div class="row">
                    <div class="col-sm-12">
                        <span id="pwmatch" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Claves Coincidentes
                    </div>
                </div>
                <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" data-loading-text="Cambiando la Clave..." value="Cambiar la Clave">
            </form>
            <span id="mensaje" class="error"></span>
        </div>
    </div>
</div>
<script>
    var b_minlenchar = false;
    var b_ucase = false;
    var b_lcase = false;
    var b_num = false;
    var b_pwmatch = false;
    var b_bdpwdmatch = false;

    var bdpassword = $("#bdpassword").val();

    $("#passwordForm").submit(function(e){
        e.preventDefault();
        var url = $(this).attr("action");
        var password = $("#password").val();

        if(!b_minlenchar){
            $("#mensaje").html("La nueva clave debe contener al menos 8 caracteres.");
            return false;
        }else if(!b_ucase){
            $("#mensaje").html("La nueva clave debe contener al menos una letra may&uacute;scula.");
            return false;
        }else if(!b_lcase){
            $("#mensaje").html("La nueva clave debe contener al menos una letra min&uacute;scula.");
            return false;
        }else if(!b_num){
            $("#mensaje").html("La nueva clave debe contener al menos un n&uacute;mero.");
            return false;
        }else if(!b_pwmatch){
            $("#mensaje").html("La nueva clave y redigitada no coinciden.");
            return false;
        }else if(!b_bdpwdmatch){
            $("#mensaje").html("La Clave Actual no coincide con la clave guardada en el sistema.");
            return false;
        }

        $("#mensaje").html("");
        // Si pasa todas las validaciones procedemos a actualizar la nueva clave en la base de datos
        $.post(url, $(this).serialize(), function(resp) {
            if (!resp.error) {
                $("#mensaje").removeClass("error");
                $("#mensaje").addClass("success");
            } else {
                $("#mensaje").removeClass("success");
                $("#mensaje").addClass("error");
            }
            $("#mensaje").html(resp.mensaje);
        }, 'json');

    });
    $("input[type=password]").keyup(function(){
        var ucase = new RegExp("[A-Z]+");
        var lcase = new RegExp("[a-z]+");
        var num = new RegExp("[0-9]+");

        var passwd = $("#password").val();

        if(passwd==bdpassword){
            $("#bdpwdmatch").removeClass("glyphicon-remove");
            $("#bdpwdmatch").addClass("glyphicon-ok");
            $("#bdpwdmatch").css("color","#00A41E");
            b_bdpwdmatch = true;
        }else{
            $("#bdpwdmatch").removeClass("glyphicon-ok");
            $("#bdpwdmatch").addClass("glyphicon-remove");
            $("#bdpwdmatch").css("color","#FF0004");
            b_bdpwdmatch = false;
        }
        
        if($("#password1").val().length >= 8){
            $("#8char").removeClass("glyphicon-remove");
            $("#8char").addClass("glyphicon-ok");
            $("#8char").css("color","#00A41E");
            b_minlenchar = true;
        }else{
            $("#8char").removeClass("glyphicon-ok");
            $("#8char").addClass("glyphicon-remove");
            $("#8char").css("color","#FF0004");
            b_minlenchar = false;
        }
        
        if(ucase.test($("#password1").val())){
            $("#ucase").removeClass("glyphicon-remove");
            $("#ucase").addClass("glyphicon-ok");
            $("#ucase").css("color","#00A41E");
            b_ucase = true;
        }else{
            $("#ucase").removeClass("glyphicon-ok");
            $("#ucase").addClass("glyphicon-remove");
            $("#ucase").css("color","#FF0004");
            b_ucase = false;
        }
        
        if(lcase.test($("#password1").val())){
            $("#lcase").removeClass("glyphicon-remove");
            $("#lcase").addClass("glyphicon-ok");
            $("#lcase").css("color","#00A41E");
            b_lcase = true;
        }else{
            $("#lcase").removeClass("glyphicon-ok");
            $("#lcase").addClass("glyphicon-remove");
            $("#lcase").css("color","#FF0004");
            b_lcase = false;
        }
        
        if(num.test($("#password1").val())){
            $("#num").removeClass("glyphicon-remove");
            $("#num").addClass("glyphicon-ok");
            $("#num").css("color","#00A41E");
            b_num = true;
        }else{
            $("#num").removeClass("glyphicon-ok");
            $("#num").addClass("glyphicon-remove");
            $("#num").css("color","#FF0004");
            b_num = false;
        }
        
        if($("#password1").val()!="" && $("#password1").val() == $("#password2").val()){
            $("#pwmatch").removeClass("glyphicon-remove");
            $("#pwmatch").addClass("glyphicon-ok");
            $("#pwmatch").css("color","#00A41E");
            b_pwmatch = true;
        }else{
            $("#pwmatch").removeClass("glyphicon-ok");
            $("#pwmatch").addClass("glyphicon-remove");
            $("#pwmatch").css("color","#FF0004");
            b_pwmatch = false;
        }
    });
</script>