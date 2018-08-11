<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        cargar_perfiles();
        cargar_usuarios();
        $("#asociar").click(function(e){
            e.preventDefault();
            asociar_usuario_perfil();
        });
        $("#lstPerfiles").click(function(e){
            e.preventDefault();
            listar_usuarios_asociados();
        });
    });
    
    function cargar_perfiles(){
        $.get("scripts/cargar_perfiles.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#lstPerfiles').append(resultado);
            }
        });
    }
    
    function cargar_usuarios(){
        $.get("scripts/cargar_usuarios_combo.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#lstUsuarios').append(resultado);
            }
        }).error(function(r){
            console.log(r);
        });
    }
    
    function listar_usuarios_asociados(){
        var id_perfil = document.getElementById("lstPerfiles").value;
        $.post("scripts/listar_usuarios_asociados.php",
            {id_perfil: id_perfil },
            function(resultado){
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $('#lista_usuarios_perfiles').html(resultado);
                }
            }).error(function(r){
                console.log(r);
        });
    }

    function asociar_usuario_perfil()
    {
        var id_perfil = document.getElementById("lstPerfiles").value;
        var id_usuario = document.getElementById("lstUsuarios").value;
        if (id_perfil == "") {
                document.getElementById("mensaje").innerHTML = "Debe elegir un perfil...";
                document.getElementById("lstPerfiles").focus();
        } else if (id_usuario == "") {
                document.getElementById("mensaje").innerHTML = "Debe elegir un usuario...";
                document.getElementById("lstUsuarios").focus();
        } else {
                $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                    type: "POST",
                    url: "usuario/insertar_asociacion.php",
                    data: "id_perfil="+id_perfil+"&id_usuario="+id_usuario,
                    success: function(resultado){
                            $("#mensaje").html(resultado);
                            listar_usuarios_asociados();
                    }
                });
        }	
    }
    
    function eliminarAsociacion(id_usuario, id_perfil)
    {
            if (id_usuario == "" || id_perfil == "") {
                    document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_usuario e id_perfil...";
            } else {
                    $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                    $.ajax({
                                    type: "POST",
                                    url: "usuario/eliminar_asociacion.php",
                                    data: "id_usuario="+id_usuario+"&id_perfil="+id_perfil,
                                    success: function(resultado){
                                            $("#mensaje").html(resultado);
                                            listar_usuarios_asociados();
                              }
                    });			
            }	
    }
</script>
</head>

<body>
    <div id="pagina">
        <div id="titulo_pagina">
            <?php echo $_SESSION['titulo_pagina'] . " PERFILES CON USUARIOS" ?>
        </div>
        <div id="frmVisor">
            <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="fuente9" valign="top">&nbsp;</td>
                    <td><span class="fuente9">&nbsp;Perfiles:</span></td>
                    <td class="fuente9" valign="top">&nbsp;</td>
                    <td><span class="fuente9">&nbsp;Usuarios:</span></td>
                    <td valign="top"><div id="asociar" class="boton" style="display:block"> <a href="#"> Asociar </a> </div></td>
                </tr>
                <tr>
                    <td class="fuente9" valign="top">&nbsp;</td>
                    <td> <select id="lstPerfiles" class="fuente9" multiple size="7" > </select> </td>         
                    <td class="fuente9" valign="top">&nbsp;</td>
                    <td> <select id="lstUsuarios" class="fuente9" multiple size="7" > </select> </td>         
                </tr>
            </table>
        </div>
        <div id="mensaje" class="error"></div>
        <div id="pag_asociacion">
            <!-- Aqui va la paginacion de los usuarios asociados con los perfiles -->
            <div class="header2" style="margin-top:2px;"> LISTA DE USUARIOS ASOCIADOS </div>
            <div class="cabeceraTabla">
                <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                    <tr class="cabeceraTabla">
                        <td width="5%">Nro.</td>
                        <td width="5%">Id</td>
                        <td width="24%" align="left">Usuario</td>
                        <td width="24%" align="left">Login</td>
                        <td width="24%" align="left">Perfil</td>
                        <td width="18%" align="center">Acciones</td>
                    </tr>
                </table>
            </div>
            <div id="lista_usuarios_perfiles" style="text-align:center"> Seleccione un perfil... </div>
        </div>    
    </div>
</body>
</html>
