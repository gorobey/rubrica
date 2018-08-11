<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		cargar_especialidades();
		//cargar_cursos_superiores();
		listarCursos(false);	    
		$("#cboEspecialidades").change(function(){
			listarCursos(false);
		});
		
		$("#nuevo_curso").click(function(e){
			e.preventDefault();
			nuevoCurso();
		});
		
		$("#definir_cursos_superiores").click(function(e){
			e.preventDefault();
			definirCursosSuperiores();
		});
		
	});

	function cargar_especialidades()
	{
		$.get("scripts/cargar_especialidades.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboEspecialidades').append(resultado);			
			}
		});	
	}

	function cargar_cursos_superiores()
	{
		$.get("scripts/cargar_cursos_superiores.php", function(resultado){
			if(resultado == false)
			{
				alert("No se han definido cursos superiores...");
			}
			else
			{
				$('#cboCursoSuperior').append(resultado);			
			}
		});	
	}

	function listarCursos(iDesplegar)
	{
		var id_especialidad = document.getElementById("cboEspecialidades").value;
		if (id_especialidad != 0) 
			$.get("cursos/listar_cursos.php", { id_especialidad: id_especialidad },
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						if (!iDesplegar) $("#mensaje").html("");
						$("#lista_cursos").html(resultado);
					}
				}
			);
	}

	function limpiarCurso()
	{
		document.getElementById("cu_nombre").value="";
                //document.getElementById("bol_proyectos").checked=0;
                //$('#bol_proyectos').prop('checked',false);
		document.getElementById("img-loader").innerHTML="";
		document.getElementById("mensaje").innerHTML="";
		document.getElementById("cu_nombre").focus();
	}

	function salirCurso()
	{
		$("#formulario_nuevo").css("display", "none");
		document.getElementById("nuevo_curso").focus();
	}

	function nuevoCurso()
	{
		limpiarCurso();
		$("#tituloForm").html("NUEVO CURSO");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"insertarCurso()\">Insertar</a></div>";
		$("#boton_accion").html(html);
		$("#formulario_nuevo").css("display", "block");
		document.getElementById("cu_nombre").focus();
	}

	function insertarCurso()
	{
		// Validación de la entrada de datos
		var id_especialidad = document.getElementById("cboEspecialidades").value;
		var cu_nombre = document.getElementById("cu_nombre").value;
		//var cu_superior = document.getElementById("cboCursoSuperior").value;
                var cu_superior = 0;
                var bol_proyectos = ($('input:checkbox[name=bol_proyectos]:checked'))?0:1;
                //alert(bol_proyectos);

		// Saco los espacios en blanco al comienzo y al final de la cadena
		cu_nombre=eliminaEspacios(cu_nombre);

		if (id_especialidad==0) {
			$("#mensaje").html("Debe escoger la especialidad...");
			document.getElementById("cboEspecialidades").focus();
                } else if(cu_nombre.length < 4) {
			$("#mensaje").html("El nombre del curso debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("cu_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
                                    type: "POST",
                                    url: "cursos/insertar_curso.php",
                                    data: "id_especialidad="+id_especialidad+"&cu_nombre="+cu_nombre+"&cu_superior="+cu_superior+"&bol_proyectos="+bol_proyectos,
                                    success: function(resultado){
                                            $("#img-loader").html("");
                                            $("#mensaje").html(resultado);
                                            listarCursos(true);
                                            salirCurso();
				  }
			});			
		}	
	}

	function actualizarCurso()
	{
		// Validación de la entrada de datos
		var id_curso = document.getElementById("id_curso").value;
		var id_especialidad = document.getElementById("cboEspecialidades").value;
		var cu_nombre = document.getElementById("cu_nombre").value;
		//var cu_superior = document.getElementById("cboCursoSuperior").value;
                var cu_superior = 0;
                var bol_proyectos = ($('input:checkbox[name=bol_proyectos]:checked').val()===null)?0:1;
                console.log(bol_proyectos);

		// Saco los espacios en blanco al comienzo y al final de la cadena
		cu_nombre=eliminaEspacios(cu_nombre);

		if (id_curso==0) {
			$("#mensaje").html("No se ha pasado el par&aacute;metro de id_curso...");
		} else if (id_especialidad==0) {
			$("#mensaje").html("Debe escoger la especialidad...");
			document.getElementById("cboEspecialidades").focus();
                } else if(cu_nombre.length < 4) {
			$("#mensaje").html("El nombre del curso debe contener al menos cuatro caracteres alfab&eacute;ticos");
			document.getElementById("cu_nombre").focus();
		} else {
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "cursos/actualizar_curso.php",
					data: "id_curso="+id_curso+"&id_especialidad="+id_especialidad+"&cu_nombre="+cu_nombre+"&cu_superior="+cu_superior+"&bol_proyectos="+bol_proyectos,
					success: function(resultado){
						$("#img-loader").html("");
						$("#mensaje").html(resultado);
						listarCursos(true);
						salirCurso();
				  }
			});
		}	
	}

	function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
	}

	function editarCurso(id_curso)
	{
		limpiarCurso();
		$("#formulario_nuevo").css("display", "none");
		$("#tituloForm").html("EDITAR CURSO");
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		html = "<div class=\"link_form\"><a href=\"#\" onclick=\"actualizarCurso()\">Actualizar</a></div>";
		$("#boton_accion").html(html);
		$.ajax({
				type: "POST",
				url: "cursos/obtener_curso.php",
				data: "id_curso="+id_curso,
				success: function(resultado){
                                        console.log(resultado);
					var JSONCurso = eval('(' + resultado + ')');
					$("#mensaje").html("");
					//Aqui se va a pintar el curso elegido
					document.getElementById("id_curso").value=JSONCurso.id_curso;
					document.getElementById("id_especialidad").value=JSONCurso.id_especialidad;
					document.getElementById("cu_nombre").value=JSONCurso.cu_nombre;
                                        document.getElementById("bol_proyectos").checked=(JSONCurso.bol_proyectos==0)?0:1;
					$("#formulario_nuevo").css("display", "block");
					document.getElementById("cu_nombre").focus();
//					setearIndice("cboEspecialidades",JSONCurso.id_especialidad);
//					setearIndice("cboCursoSuperior",JSONCurso.id_curso_superior);
			  }
		});			
	}

	function eliminarCurso(id_curso, nombre)
	{
		// Validación de la entrada de datos
		
		if (id_curso==0) {
			$("#mensaje").html("No se ha pasado el parámetro de id_curso...");
		} else {
			var eliminar = confirm("¿Seguro que desea eliminar el Curso [" + nombre + "]?")
			if (eliminar) {
				$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
						type: "POST",
						url: "cursos/eliminar_curso.php",
						data: "id_curso="+id_curso,
						success: function(resultado){
							$("#mensaje").html(resultado);
							listarCursos(true);
							salirCurso();
					  }
				});			
			}
		}	
	}
	
	function definirCursosSuperiores()
	{
	
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
            <td class="fuente9">&nbsp;Especialidad: &nbsp;</td>
            <td> <select id="cboEspecialidades" class="fuente9"> <option value="0">Seleccione...</option> </select> </td>         
            <td> <div id="nuevo_curso" class="boton" style="display:block"> <a href="#"> Nuevo Curso </a> </div> </td>
<!--            <td> <div id="definir_cursos_superiores" class="boton" style="display:block"> <a href="#"> Definir Cursos Superiores </a> </div> </td>-->
            <td> <div id="definir_cursos_superiores" class="boton" style="display:block"> &nbsp; </td>
         </tr>
      </table>
   </div>
   <div id="formulario_nuevo">
      <div id="tituloForm" class="header">Nuevo Curso</div>
      <div id="frmNuevo" align="left">
   	     <form id="form_nuevo" action="" method="post">
		    <table class="fuente8" width="98%" cellspacing=0 cellpadding=2 border=0>
               <tr>
                  <td width="15%" align="right">Nombre:</td>
                  <td width="*">
                     <input id="cu_nombre" type="text" class="cajaExtraGrande" name="cu_nombre" maxlength="128" />
                  </td>
               </tr>
<!--               <tr>
                  <td width="15%" align="right">Curso Superior:</td>
                  <td width="*">
                      <select id="cboCursoSuperior" name="cboCursoSuperior" class="fuente9">
                          <option value="0">Seleccione...</option>
                      </select>
                  </td>
               </tr>-->
               <tr>
                  <td width="15%" align="right">Proyectos:</td>
                  <td width="*">
                      <input id="bol_proyectos" name="bol_proyectos" type="checkbox" />
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
                              <div class="link_form"><a href="#" onclick="limpiarCurso()">Limpiar</a></div>
                           </td>
                           <td width="5%" align="right">
                              <div class="link_form"><a href="#" onclick="salirCurso(true)">Salir</a></div>
                           </td>
                           <td width="*">
                              <div id="img-loader" style="padding-left:2px"></div>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
            <input type="hidden" id="id_curso" name="id_curso" />
            <input type="hidden" id="id_especialidad" name="id_especialidad" />
         </form>
      </div>   
   </div>
   <div id="mensaje" class="error"></div>
   <div id="pag_cursos">
      <!-- Aqui va la paginacion de los cursos encontrados -->
      <div class="header2"> LISTA DE CURSOS EXISTENTES </div>
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
      <div id="lista_cursos" style="text-align:center"> </div>
   </div>
</div>
</body>
</html>
