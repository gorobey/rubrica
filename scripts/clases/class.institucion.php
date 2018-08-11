<?php

class institucion extends MySQL
{
	var $in_nombre = "";
	var $in_direccion = "";
	var $in_telefono1 = "";
	var $in_nom_rector = "";
	var $in_nom_secretario = "";
	
	function obtenerDatosInstitucion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_institucion");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function actualizarDatosInstitucion()
	{
		$qry = "call sp_insertar_institucion (";
		$qry .= "'" . $this->in_nombre . "',";
		$qry .= "'" . $this->in_direccion . "',";
		$qry .= "'" . $this->in_telefono1 . "',";
		$qry .= "'" . $this->in_nom_rector . "',";
		$qry .= "'" . $this->in_nom_secretario . "')";
		$consulta = parent::consulta($qry);
		if($consulta)
			return "Actualizaci&oacute;n realizada exitosamente.";
		else
			return "No se pudo realizar la actualizaci&oacute;n. Error: " . mysql_error();
	}
	
	function obtenerNombreInstitucion()
	{
		$consulta = parent::consulta("SELECT in_nombre FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nombre;
	}

	function obtenerDireccionInstitucion()
	{
		$consulta = parent::consulta("SELECT in_direccion FROM sw_institucion");
		return parent::fetch_object($consulta)->in_direccion;
	}

	function obtenerTelefonoInstitucion()
	{
		$consulta = parent::consulta("SELECT in_telefono1 FROM sw_institucion");
		return parent::fetch_object($consulta)->in_telefono1;
	}

	function obtenerNombreRector()
	{
		$consulta = parent::consulta("SELECT in_nom_rector FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nom_rector;
	}

	function obtenerNombreSecretario()
	{
		$consulta = parent::consulta("SELECT in_nom_secretario FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nom_secretario;
	}
}	
?>