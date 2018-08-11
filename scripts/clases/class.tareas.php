<?php

class tareas extends MySQL
{
	
    function cargarTareas(){
        // Funcion que retorna todas las tareas ingresadas en la base de datos
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_tarea ORDER BY fecha DESC");
        if(parent::num_rows($consulta) > 0){
            while($tarea = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $task = $tarea["tarea"];
                $id = $tarea["id"];
                $checked = $tarea["hecho"] ? "checked" : "";
                $clase = $tarea["hecho"] ? "taskDone" : "";
                $cadena .= "<td><input type='checkbox' onclick='checkTask(this,".$id.")' $checked></td>";
                $cadena .= "<td><div class='".$clase."'>".$task."</div></td>";
                $cadena .= "<td><button onclick='editTask(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteTask(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
        }
        return $cadena;
    }

    function consultarTareas($where){
        // Funcion que retorna las tareas ingresadas de acuerdo al filtro
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_tarea" . $where . "ORDER BY fecha DESC");
        if(parent::num_rows($consulta) > 0){
            while($tarea = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $task = $tarea["tarea"];
                $id = $tarea["id"];
                $checked = $tarea["hecho"] ? "checked" : "";
                $clase = $tarea["hecho"] ? "taskDone" : "";
                $cadena .= "<td><input type='checkbox' onclick='checkTask(this,".$id.")' $checked></td>";
                $cadena .= "<td><div class='".$clase."'>".$task."</div></td>";
                $cadena .= "<td><button onclick='editTask(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteTask(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
        }
        return $cadena;
    }

    function insertarTarea($tarea_descripcion){
        // Funcion que inserta una tarea en la base de datos
        // devuelve un mensaje de error o exito
        $consulta = parent::consulta("INSERT INTO sw_tarea (tarea, hecho) VALUES ('$tarea_descripcion', 0)");
        $mensaje = "Tarea insertada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo insertar la tarea...Error: " . mysql_error();
		return $mensaje;
    }

    function actualizarTarea($id, $tarea_descripcion){
        // Funcion que actualiza una tarea en la base de datos
        // devuelve un mensaje de error o exito
        $consulta = parent::consulta("UPDATE sw_tarea SET tarea = '$tarea_descripcion' WHERE id = $id");
        $mensaje = "Tarea actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la tarea...Error: " . mysql_error();
		return $mensaje;
    }

    function eliminarTarea($id){
        // Funcion que elimina una tarea en la base de datos
        // devuelve un mensaje de error o exito
        $consulta = parent::consulta("DELETE FROM sw_tarea WHERE id = $id");
        $mensaje = "Tarea eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la tarea...Error: " . mysql_error();
		return $mensaje;
    }

    function obtenerTarea($id){
		$consulta = parent::consulta("SELECT tarea FROM sw_tarea WHERE id = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}
    
    function actualizarCampoHecho($id, $estado_hecho){
		// Procedimiento para actualizar el estado de Hecho de una tarea
		$consulta = parent::consulta("UPDATE sw_tarea SET hecho = $estado_hecho WHERE id = " . $id);
		if($consulta) return "Tarea realizada actualizada correctamente.";
		else return "Tarea realizada no pudo actualizarse. Error: " . mysql_error();
	}

}