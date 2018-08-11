-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE sp_insertar_usuario (
	in IdPeriodoLectivo int,
	in IdPerfil int,
	in UsTitulo varchar(5),
	in UsApellidos varchar(32),
	in UsNombres varchar(32),
	in UsFullname varchar(64),
	in UsLogin varchar(24),
	in UsPassword varchar(64)
)
BEGIN
	INSERT INTO sw_usuario (
		id_periodo_lectivo,
		id_perfil,
		us_titulo,
		us_apellidos,
		us_nombres,
		us_fullname,
		us_login,
		us_password
	) VALUES (
		IdPeriodoLectivo,
		IdPerfil,
		UsTitulo,
		UsApellidos,
		UsNombres,
		UsFullname,
		UsLogin,
		UsPassword
	);
END