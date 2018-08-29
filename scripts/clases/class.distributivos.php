<?php

class distributivos extends MySQL
{
	var $code = "";
	var $id_malla_curricular = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $id_usuario = "";

	function insertarDistributivo()
	{
		$consulta = parent::consulta("SELECT id_malla_curricular, 
                                             ma_horas_presenciales, 
                                             ma_horas_autonomas, 
                                             ma_horas_tutorias, 
                                             ma_subtotal 
                                        FROM sw_malla_curricular
                                       WHERE id_paralelo = " . $this->id_paralelo
                                    . "  AND id_asignatura = " . $this->id_asignatura);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros == 0)
		{
			$mensaje = "No se han asociado items a la malla con el paralelo y la asignatura elegidos...";
        }
        else
        {
            $registro = parent::fetch_assoc($consulta);
            $id_malla_curricular = $registro["id_malla_curricular"];
            $horas_presenciales = $registro["ma_horas_presenciales"];
            $horas_autonomas = $registro["ma_horas_autonomas"];
            $horas_tutorias = $registro["ma_horas_tutorias"];
            $subtotal = $registro["ma_subtotal"];
            // Ahora si procedemos a insertar...
            $qry = "INSERT INTO sw_distributivo(";
            $qry .= "id_malla_curricular,";
            $qry .= "id_paralelo,";
            $qry .= "id_asignatura,";
            $qry .= "id_usuario,";
            $qry .= "di_horas_presenciales,";
            $qry .= "di_horas_autonomas,";
            $qry .= "di_horas_tutorias,";
            $qry .= "di_subtotal) VALUES(";
            // id_malla_curricular
            $qry .= $id_malla_curricular . ",";
            // id_paralelo
            $qry .= $this->id_paralelo . ",";
            // id_asignatura
            $qry .= $this->id_asignatura . ",";
            // id_usuario
            $qry .= $this->id_usuario . ",";
            // di_horas_presenciales
            $qry .= $horas_presenciales . ",";
            // di_horas_autonomas
            $qry .= $horas_autonomas . ",";
            // di_horas_tutorias
            $qry .= $horas_tutorias . ",";
            // di_subtotal
            $qry .= $subtotal . ")";
            $consulta = parent::consulta($qry);
            if (!$consulta) {
                $mensaje = "No se pudo insertar el item del distributivo. Error: " . mysql_error();
            } else {
                $mensaje = "Insercion exitosa.";
            }
        }
		return $mensaje;
    }
    
}
?>