<?php

class areas extends MySQL
{
	
	var $code = "";
	var $ar_nombre = "";
	
	function existeArea($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE ar_nombre = '$nombre'");
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

	function obtenerIdArea($nombre)
	{
		$consulta = parent::consulta("SELECT id_area FROM sw_area WHERE ar_nombre = '$nombre'");
		$area = parent::fetch_object($consulta);
		return $area->id_area;
	}

	function obtenerArea($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE id_area = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerAreas()
	{
		// Funcion que retorna todas las areas ingresadas en la base de datos
		return parent::consulta("SELECT *
								   FROM sw_area
								 ORDER BY ar_nombre");
	}

	function obtenerDatosArea()
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE id_area = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

    function cargarAreas(){
        // Funcion que retorna todas las areas ingresadas en la base de datos
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_area ORDER BY ar_nombre");
        if(parent::num_rows($consulta) > 0){
            while($area = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $nombre = $area["ar_nombre"];
                $id = $area["id_area"];
                $cadena .= "<td>".$id."</td>";
                $cadena .= "<td>".$nombre."</td>";
                $cadena .= "<td><button onclick='editArea(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteArea(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado areas todavia...</td></tr>";
        }
        return $cadena;
    }	

	function insertarArea()
	{
		$qry = "INSERT INTO sw_area (ar_nombre) VALUES (";
		$qry .= "'" . $this->ar_nombre . "')";
		$consulta = parent::consulta($qry);
		$mensaje = "Area insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar el area...Error: " . mysql_error();
		return $mensaje;
	}

	function actualizarArea()
	{
		$qry = "UPDATE sw_area SET ";
		$qry .= "ar_nombre = '" . $this->ar_nombre . "'";
		$qry .= " WHERE id_area = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Area " . $this->ar_nombre . " actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar el area...Error: " . mysql_error();
		return $mensaje;
	}

	function eliminarArea()
	{
		// Primero compruebo si existen asignaturas asociadas
		$qry = "SELECT * FROM sw_asignatura WHERE id_area = ". $this->code;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "No se puede eliminar esta Area, porque tiene Asignaturas asociadas.";
		} else {
			$qry = "DELETE FROM sw_area WHERE id_area = ". $this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "Area eliminada exitosamente...";
			if (!$consulta)
				$mensaje = "No se pudo eliminar el area...Error: " . mysql_error();	
		}
		return $mensaje;
	}

}
?>