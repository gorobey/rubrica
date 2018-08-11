-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`root`@`localhost` FUNCTION `es_promocionado`(
	IdEstudiante INT,
	IdPeriodoLectivo INT
) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE IdParalelo INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	DECLARE cAsignaturas CURSOR FOR
	 SELECT id_asignatura
	   FROM sw_paralelo_asignatura
	  WHERE id_paralelo = IdParalelo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SET IdParalelo = (SELECT id_paralelo
						FROM sw_estudiante_periodo_lectivo
					   WHERE id_estudiante = IdEstudiante
					     AND id_periodo_lectivo = IdPeriodoLectivo);

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

DELIMITER ;