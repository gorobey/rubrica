<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
            listarModalidades(false);
            $("#nueva_modalidad").click(function(e){
                e.preventDefault();
                nuevaModalidad();
            });
	});
        function limpiarModalidad()
	{
		document.getElementById("mo_nombre").value="";
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("mo_nombre").focus();
	}
        function nuevaModalidad()
	{
		limpiarModalidad();
		$("#tituloForm").html("NUEVA MODALIDAD");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarModalidad()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("mo_nombre").focus();
	}
        function salirModalidad()
	{
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nueva_modalidad").focus();
	}
        function listarModalidades(iDesplegar)
	{ 
            $.get("modalidades/listar_modalidades.php", 
                    function(resultado)
                    {
                            if(resultado == false)
                            {
                                    alert("Error");
                            }
                            else
                            {
                                    if (!iDesplegar) $("#mensaje").html("");
                                    $("#lista_modalidades").html(resultado);
                            }
                    }
            );
	}
        
        function insertarModalidad()
	{
            // Validación de la entrada de datos
            var mo_nombre = document.getElementById("mo_nombre").value;

            // Saco los espacios en blanco al comienzo y al final de la cadena
            mo_nombre=eliminaEspacios(mo_nombre);

            if (mo_nombre.length < 4) {
                $("#mensaje").html("El nombre de la modalidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
                document.getElementById("mo_nombre").focus();
            } else {
                $("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                    type: "POST",
                    url: "modalidades/insertar_modalidad.php",
                    data: "mo_nombre=" + mo_nombre,
                    success: function (resultado) {
                        $("#img-loader").html("");
                        $("#mensaje").html(resultado);
                        listarModalidades(true);
                        salirModalidad();
                    }
                });
            }
        }
        
        function editarModalidad(id_modalidad)
	{
            limpiarModalidad();
            $("#formulario_nuevo").css("display", "none");
            $("#tituloForm").html("EDITAR MODALIDAD");
            $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarModalidad()\">Actualizar</a></div>";
            $("#boton_accion").html(html);
            $.ajax({
                type: "POST",
                url: "modalidades/obtener_modalidad.php",
                data: "id_modalidad="+id_modalidad,
                success: function(resultado){
                        var JSONModalidad = eval('(' + resultado + ')');
                        $("#mensaje").html("");
                        //Aqui se va a pintar la modalidad elegida
                        document.getElementById("id_modalidad").value=JSONModalidad.id_modalidad;
                        document.getElementById("mo_nombre").value=JSONModalidad.mo_nombre;
                        $("#formulario_nuevo").css("display", "block");
                        document.getElementById("mo_nombre").focus();
                }
            });			
	}
        
        function actualizarModalidad()
	{
            // Validación de la entrada de datos
            var id_modalidad = document.getElementById("id_modalidad").value;
            var mo_nombre = document.getElementById("mo_nombre").value;

            // Saco los espacios en blanco al comienzo y al final de la cadena
            mo_nombre=eliminaEspacios(mo_nombre);

            if (id_modalidad==0) {
                    $("#mensaje").html("No se ha pasado el par&aacute;metro de id_modalidad...");
            } else if(mo_nombre.length < 4) {
                    $("#mensaje").html("El nombre de la modalidad debe contener al menos cuatro caracteres alfab&eacute;ticos");
                    document.getElementById("mo_nombre").focus();
            } else {
                    $("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                    $.ajax({
                        type: "POST",
                        url: "modalidades/actualizar_modalidad.php",
                        data: "id_modalidad="+id_modalidad+"&mo_nombre="+mo_nombre,
                        success: function(resultado){
                                $("#img-loader").html("");
                                $("#mensaje").html(resultado);
                                listarModalidades(true);
                                salirModalidad();
                        }
                    });
            }	
	}
        
        function eliminarModalidad(id_modalidad, nombre)
	{
            // Validación de la entrada de datos

            if (id_modalidad==0) {
                $("#mensaje").html("No se ha pasado el parámetro de id_modalidad...");
            } else {
                var eliminar = confirm("¿Seguro que desea eliminar la Modalidad [" + nombre + "]?")
                if (eliminar) {
                    $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                    $.ajax({
                        type: "POST",
                        url: "modalidades/eliminar_modalidad.php",
                        data: "id_modalidad="+id_modalidad,
                        success: function(resultado){
                                $("#mensaje").html(resultado);
                                listarModalidades(true);
                                salirModalidad();
                        }
                    });			
                }
            }	
	}
</script>
</head>

<body>
    <div id="pagina">
        <div id="titulo_pagina">
            <?php echo $_SESSION['titulo_pagina'] ?>
        </div>
        <div id="barra_principal">
            <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td> <div id="nueva_modalidad" class="boton" style="display:block"> <a href="#"> Nueva Modalidad </a> </div> </td>
                </tr>
            </table>
        </div>
        <div id="formulario_nuevo">
            <div id="tituloForm" class="header">Nueva Modalidad</div>
            <div id="frmNuevo" align="left">
                <form id="form_nuevo" action="" method="post">
                    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
                        <tr>
                            <td width="15%" align="right">Nombre:</td>
                            <td width="*">
                                <input id="mo_nombre" type="text" class="cajaExtraGrande" name="mo_nombre" maxlength="128" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table id="botones_nuevo" cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td width="15%" align="right">
                                            <div id="boton_accion">
                                                <!-- <div id="insertarMenu" class="link_form"><a href="#">Insertar</a></div> -->
                                            </div>   
                                        </td>
                                        <td width="5%" align="right">
                                            <div class="link_form"><a href="#" onclick="limpiarModalidad()">Limpiar</a></div>
                                        </td>
                                        <td width="5%" align="right">
                                            <div class="link_form"><a href="#" onclick="salirModalidad(true)">Salir</a></div>
                                        </td>
                                        <td width="*">
                                            <div id="img-loader" style="padding-left:2px"></div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" id="id_modalidad" name="id_modalidad" />
                </form>
            </div>   
        </div>
        <div id="mensaje" class="error"></div>
        <div id="pag_modalidades">
            <!-- Aqui va la paginacion de las modalidades encontradas -->
            <div class="header2"> LISTA DE MODALIDADES EXISTENTES </div>
            <div class="cabeceraTabla">
                <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                    <tr class="cabeceraTabla">
                        <td width="5%">Nro.</td>
                        <td width="5%">Id.</td>
                        <td width="72%" align="left">Nombre</td>
                        <td width="18%" align="center">Acciones</td>
                    </tr>
                </table>
            </div>
            <div id="lista_modalidades" style="text-align:center"> </div>
        </div>
    </div>
</body>
</html>
