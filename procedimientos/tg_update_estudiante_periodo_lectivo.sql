USE colegion_1;

DROP TRIGGER IF EXISTS tg_update_estudiante_periodo_lectivo;

DELIMITER \\

CREATE TRIGGER tg_update_estudiante_periodo_lectivo AFTER UPDATE ON sw_estudiante_periodo_lectivo

FOR EACH ROW

BEGIN
	
	-- Aqui voy a actualizar los registros correspondientes en la tabla sw_rubrica_estudiante

	UPDATE sw_rubrica_estudiante 
		SET id_paralelo = new.id_paralelo
		WHERE id_estudiante = new.id_estudiante
		  AND id_paralelo = old.id_paralelo;
	
END\\

DELIMITER ;
