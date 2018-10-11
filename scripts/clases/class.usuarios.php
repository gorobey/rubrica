<?php

include_once("class.encrypter.php");

class usuarios extends MySQL
{
	var $code = "";
	var $clave = "";
	var $clave_actual = "";
	var $id_periodo_lectivo = "";
	var $id_perfil = "";
    var $id_usuario = "";
	var $us_titulo = "";
	var $us_apellidos = "";
	var $us_nombres = "";
	var $us_fullname = "";
	var $us_login = "";
	var $us_password = "";

	function cargarUsuarios()
	{
		$consulta = parent::consulta("SELECT id_usuario, us_fullname FROM sw_usuario WHERE id_perfil = " . $this->code . " ORDER BY us_fullname ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if($num_total_registros>0)
		{
			while($usuarios = parent::fetch_assoc($consulta))
			{
				$code = $usuarios["id_usuario"];
				$name = $usuarios["us_fullname"];	
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}
	
	function existeUsuario($login, $clave, $id_perfil)
	{
		$clave = encrypter::encrypt($clave);
        //$consulta = parent::consulta("SELECT * FROM sw_usuario WHERE us_login = '$login' AND us_password = '$clave' AND id_perfil = $id_perfil");
		$consulta = parent::consulta("SELECT u.id_usuario "
                                            . " FROM sw_usuario u, "
                                            . "      sw_perfil p, "
                                            . "      sw_usuario_perfil up "
                                            . "WHERE u.id_usuario = up.id_usuario "
                                            . "  AND p.id_perfil = up.id_perfil "
                                            . "  AND us_login = '$login' "
                                            . "  AND us_password = '$clave' "
											. "  AND p.id_perfil = $id_perfil"
											. "  AND us_activo = 1");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros > 0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	function obtenerIdUsuario($login, $clave, $id_perfil)
	{
		$clave = encrypter::encrypt($clave);
		$consulta = parent::consulta("SELECT u.id_usuario "
                                            . " FROM sw_usuario u, "
                                            . "      sw_perfil p, "
                                            . "      sw_usuario_perfil up "
                                            . "WHERE u.id_usuario = up.id_usuario "
                                            . "  AND p.id_perfil = up.id_perfil "
                                            . "  AND us_login = '$login' "
                                            . "  AND us_password = '$clave' "
                                            . "  AND p.id_perfil = $id_perfil");
		$usuario = parent::fetch_object($consulta);
		return $usuario->id_usuario;
	}

	function obtenerDatosUsuario()
	{
		$consulta = parent::consulta("SELECT * FROM sw_usuario WHERE id_usuario = " . $this->code);
		$usuario = parent::fetch_assoc($consulta);
		$usuario["us_password"] = encrypter::decrypt($usuario["us_password"]);
		return json_encode($usuario);
	}

	function obtenerUsuario($id)
	{
		$consulta = parent::consulta("SELECT id_usuario, us_nombres, us_fullname, DES_DECRYPT(us_password) AS us_password, us_foto, us_alias FROM sw_usuario WHERE id_usuario = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerUsuarios($valor)
	{
		$consulta = parent::consulta("SELECT id_usuario, CONCAT(us_apellidos,' ',us_nombres) AS nombre FROM sw_usuario WHERE us_fullname LIKE '%$valor%'");
		$datos = array();
		if(mysql_num_rows($consulta)==0)
			array_push($datos, "");
		else{
			while($dato = mysql_fetch_array($consulta)){
				$datos[] = array('value' => $dato['nombre'], 'id' => $dato['id_usuario']);
		  	}
		}
		return json_encode($datos);
	}

	function obtenerNivelAcceso()
	{
		$consulta = parent::consulta("SELECT pe_nivel_acceso FROM sw_perfil p, sw_usuario u WHERE u.id_perfil = p.id_perfil AND id_usuario = ".$this->code);
		$registro = parent::fetch_array($consulta);
		return json_encode($registro);
	}

	function obtenerNombreUsuario($id)
	{
		$consulta = parent::consulta("SELECT us_titulo, us_apellidos, us_nombres FROM sw_usuario WHERE id_usuario = $id");
		$usuario = parent::fetch_object($consulta);
		return $usuario->us_titulo." ".$usuario->us_nombres." ".$usuario->us_apellidos;
	}

	function actualizarClave()
	{
		$qry = "UPDATE sw_usuario SET ";
		$qry .= "us_password = '" . encrypter::encrypt($this->clave) . "'";
		$qry .= " WHERE id_usuario = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Clave actualizada exitosamente...";
		if (!$consulta){
			$mensaje = "No se pudo actualizar la clave del usuario...Error: " . mysql_error() . ".Consulta: " . $qry;
			$respuesta = array("error" => true, "mensaje" => $mensaje);
		} else {
			$respuesta = array("error" => false, "mensaje" => $mensaje);
		}
		return json_encode($respuesta);
	}

	function listarUsuarios()
	{
		if ($this->id_perfil == 0)
			$cadena = "Seleccione un perfil...";
		else {
			$consulta = parent::consulta("SELECT up.id_usuario, "
										. "       us_titulo, "
										. "       us_apellidos, "
										. "       us_nombres, "
										. "       us_login, "
										. "       pe_nombre, "
										. "       us_activo "
										. "  FROM sw_usuario u, "
										. "       sw_perfil p,"
										. "       sw_usuario_perfil up"
										. " WHERE u.id_usuario = up.id_usuario "
										. "   AND p.id_perfil = up.id_perfil "
										. "   AND up.id_perfil = " . $this->id_perfil 
										. " ORDER BY us_apellidos, us_nombres ASC");
			$num_total_registros = parent::num_rows($consulta);
			$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
			if($num_total_registros>0)
			{
				$contador = 0;
				while($usuarios = parent::fetch_assoc($consulta))
				{
					$contador++;
					$cadena .= "<tr>\n";
					$code = $usuarios["id_usuario"];
					$name = $usuarios["us_apellidos"] . " " . $usuarios["us_nombres"] . ", " . $usuarios["us_titulo"];
					$login = $usuarios["us_login"];
					$perfil = $usuarios["pe_nombre"];
					$cadena .= "<td>$contador</td>\n";	
					$cadena .= "<td>$code</td>\n";	
					$cadena .= "<td>$name</td>\n";
					$cadena .= "<td>$login</td>\n";
					$activo = ($usuarios["us_activo"]==1)?"Sí":"No";
					$cadena .= "<td>$activo</td>\n";
					$cadena .= "<td><button class='btn btn-block btn-warning' onclick=\"editarUsuario(".$code.")\">Editar</button></td>";
					//$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarUsuario(".$code.")\">Eliminar</button></td>";
					$cadena .= "<td><button class='btn btn-block btn-secondary' onclick=\"desasociarUsuarioPerfil(".$code.")\">Des-Asociar</button></td>";
					$cadena .= "</tr>\n";	
				}
			}
			else {
				$cadena .= "<tr>\n";	
				$cadena .= "<td colspn='8' align='center'>No se han definido usuarios para este perfil...</td>\n";
				$cadena .= "</tr>\n";	
			}
			$cadena .= "</table>";	
		}
		return $cadena;
	}

    function listarUsuariosAsociados()
	{
		$consulta = parent::consulta("SELECT u.id_usuario, "
											. "us_titulo, "
											. "us_fullname, "
											. "us_login, "
											. "pe_nombre "
										. "FROM sw_usuario u, "
											. "sw_perfil p, "
											. "sw_usuario_perfil up "
									. "WHERE u.id_usuario = up.id_usuario "
										. "AND p.id_perfil = up.id_perfil "
										. "AND up.id_perfil = " . $this->id_perfil 
								. " ORDER BY pe_nombre, us_apellidos, us_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
				$contador = 0;
				while($usuarios = parent::fetch_assoc($consulta))
				{
						$contador += 1;
						$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
						$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
						$code = $usuarios["id_usuario"];
						$name = $usuarios["us_titulo"] . " " . $usuarios["us_fullname"];
						$login = $usuarios["us_login"];
						$perfil = $usuarios["pe_nombre"];
						$cadena .= "<td width=\"5%\">$contador</td>\n";	
						$cadena .= "<td width=\"5%\">$code</td>\n";	
						$cadena .= "<td width=\"24%\" align=\"left\">$name</td>\n";
						$cadena .= "<td width=\"24%\" align=\"left\">$login</td>\n";
						$cadena .= "<td width=\"24%\" align=\"left\">$perfil</td>\n";
						$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.",".$this->id_perfil.")\">eliminar</a></td>\n";
						$cadena .= "</tr>\n";	
				}
		}
		else {
				$cadena .= "<tr>\n";	
				$cadena .= "<td>No se han asociado usuarios para este perfil...</td>\n";
				$cadena .= "</tr>\n";	
		}
		$cadena .= "</table>";	
		return $cadena;
	}

	function insertarUsuario()
	{
        $consulta = parent::consulta("SELECT * FROM sw_usuario WHERE us_fullname='".$this->us_fullname."' AND id_perfil=".$this->id_perfil);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0)
            $mensaje = "Ya existe el Usuario (".$this->us_fullname.") en la Base de Datos";
		else {
			$qry = "call sp_insertar_usuario (";
			$qry .= $this->id_periodo_lectivo . ",";
			$qry .= $this->id_perfil . ",";
			$qry .= "'" . $this->us_titulo . "',";
			$qry .= "'" . $this->us_apellidos . "',";
			$qry .= "'" . $this->us_nombres . "',";
			$qry .= "'" . $this->us_fullname . "',";
			$qry .= "'" . $this->us_login . "',";
			$clave = encrypter::encrypt($this->us_password);
			$qry .= "'" . $clave . "')";
			$consulta = parent::consulta($qry);
			$mensaje = "Usuario insertado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo insertar el usuario...Error: " . mysql_error() . "<br />Consulta: " . $qry;
		}
		return $mensaje;
	}

	function actualizarUsuario()
	{
		$qry = "call sp_actualizar_usuario (";
		$qry .= $this->code . ",";
		$qry .= $this->id_perfil . ",";
		$qry .= "'" . $this->us_titulo . "',";
		$qry .= "'" . $this->us_apellidos . "',";
		$qry .= "'" . $this->us_nombres . "',";
		$qry .= "'" . $this->us_fullname . "',";
		$qry .= "'" . $this->us_login . "',";
		$clave = encrypter::encrypt($this->us_password);
		$qry .= "'" . $clave ."',";
		$qry .= $this->us_activo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Usuario actualizado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el usuario...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarUsuario()
	{
		$qry = "DELETE FROM sw_usuario WHERE id_usuario=". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Usuario eliminado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar el usuario...Error: " . mysql_error();
		return $mensaje;
	}

	function buscarUsuario($patron)
	{
		$qry = "SELECT us_login FROM sw_usuario WHERE us_login LIKE '" . $patron . "%' ORDER BY us_login";
		$consulta = parent::consulta($qry);
		$arreglo_php = array();
		if(mysql_num_rows($consulta)==0)
		   array_push($arreglo_php, "");
		else{
		  while($palabras = mysql_fetch_array($consulta)){
			array_push($arreglo_php, $palabras["us_login"]);
		  }
		}
		return json_encode($arreglo_php);
	}

	function contarCalificacionesErroneasDocente($id_usuario)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e,  sw_paralelo_asignatura pa, sw_usuario u, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_paralelo = pa.id_paralelo AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pa.id_usuario = $id_usuario AND re_calificacion > 10");
		return json_encode(parent::fetch_assoc($consulta));	
	}
	
	function listarCalificacionesErroneasDocente($id_usuario)
	{
		$consulta = parent::consulta("SELECT as_nombre, es_apellidos, es_nombres, pe_nombre, cu_nombre, pa_nombre, ap_nombre, ru_nombre, r.id_rubrica_estudiante, re_calificacion FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e,  sw_paralelo_asignatura pa, sw_usuario u, sw_curso cu, sw_paralelo p, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_paralelo = pa.id_paralelo AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_paralelo = p.id_paralelo AND p.id_curso = cu.id_curso AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pa.id_usuario = $id_usuario AND re_calificacion > 10 ORDER BY as_nombre, es_apellidos, es_nombres");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($calificacion = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$id_rubrica_estudiante = $calificacion["id_rubrica_estudiante"];
				$asignatura = $calificacion["as_nombre"];
				$estudiante = $calificacion["es_apellidos"] . " " . $calificacion["es_nombres"];
				$curso = $calificacion["cu_nombre"] . " \"". $calificacion["pa_nombre"] . "\"";
				$periodo = $calificacion["pe_nombre"];
				$aporte = $calificacion["ap_nombre"];
				$rubrica = $calificacion["ru_nombre"];
				$nota = $calificacion["re_calificacion"];
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"15%\">$asignatura</td>\n";
				$cadena .= "<td width=\"15%\">$estudiante</td>\n";
				$cadena .= "<td width=\"15%\">$curso</td>\n";
				$cadena .= "<td width=\"15%\">$periodo</td>\n";
				$cadena .= "<td width=\"15%\">$aporte</td>\n";
				$cadena .= "<td width=\"15%\">$rubrica</td>\n";
				$cadena .= "<td width=\"5%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCalificacionErronea(".$id_rubrica_estudiante.")\">$nota</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n";	
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han encontrado calificaciones err&oacute;neas...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}
        
    function asociarUsuarioPerfil()
	{
		//Primero verificar si ya se realizo la asociacion
		$qry = "SELECT * FROM sw_usuario_perfil WHERE id_perfil = " . $this->id_perfil
				. " AND id_usuario = " . $this->id_usuario;
		$consulta = parent::consulta($qry);
		if(parent::num_rows($consulta) > 0){
			$mensaje = "Este usuario ya fue asociado anteriormente...";
		}else{
			$qry = "INSERT INTO sw_usuario_perfil (id_perfil, id_usuario) VALUES (";
			$qry .= $this->id_perfil . ",";
			$qry .= $this->id_usuario . ")";
			$consulta = parent::consulta($qry);
			$mensaje = "Usuario asociado exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo asociar el Usuario...Error: " . mysql_error();
		}
		
		return $mensaje;
	}

    function eliminarUsuarioPerfil()
	{
		$qry = "DELETE FROM sw_usuario_perfil WHERE id_perfil = ". $this->id_perfil .
				" AND id_usuario = " . $this->id_usuario;
		$consulta = parent::consulta($qry);
		$mensaje = "Usuario des-asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar el Usuario...Error: " . mysql_error();
		return $mensaje;
	}

}
?>