USE colegion_1;

DROP TRIGGER IF EXISTS tg_insert_sw_rubrica_estudiante;

DELIMITER \\

CREATE TRIGGER tg_insert_sw_rubrica_estudiante AFTER INSERT ON sw_rubrica_estudiante

FOR EACH ROW

BEGIN
	
	DECLARE IdUsuario INT DEFAULT 0;

	-- Aqui voy a insertar el registro correspondiente en la tabla sw_rubrica_estudiante_log

	SET IdUsuario = (SELECT id_usuario FROM sw_paralelo_asignatura WHERE id_paralelo = new.id_paralelo AND id_asignatura = new.id_asignatura);
		
	INSERT INTO sw_rubrica_estudiante_log 
		SET id_rubrica_estudiante = new.id_rubrica_estudiante,
			id_estudiante = new.id_estudiante,
			id_paralelo = new.id_paralelo,
			id_asignatura = new.id_asignatura,
			id_rubrica_personalizada = new.id_rubrica_personalizada,
			id_usuario = IdUsuario,
			re_calificacion_nueva = new.re_calificacion;
	
END\\

DELIMITER ;
