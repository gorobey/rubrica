-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_usuario`(
	in IdUsuario int,
	in IdPerfil int,
	in UsTitulo varchar(5),
	in UsApellidos varchar(32),
	in UsNombres varchar(32),
	in UsFullname varchar(64),
	in UsLogin varchar(24),
	in UsPassword varchar(64)
)
BEGIN
	UPDATE sw_usuario 
	SET	id_perfil = IdPerfil,
		us_titulo = 'UsTitulo',
		us_apellidos = 'UsApellidos',
		us_nombres = 'UsNombres',
		us_fullname = 'UsFullname',
		us_login = 'UsLogin',
		us_password = 'UsPassword'
	WHERE id_usuario = IdUsuario;
END