-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`root`@`localhost` FUNCTION `aprueba_todos_supletorios`(
	IdPeriodoLectivo int,
	IdEstudiante int,
	IdParalelo int
) RETURNS tinyint(4)
    READS SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				SET done = 1;
				SET aprueba = FALSE;
			END IF;
		ELSE IF promedio < 5 THEN -- tiene que rendir el examen supletorio
				SET done = 1;
				SET aprueba = FALSE;
			 END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END