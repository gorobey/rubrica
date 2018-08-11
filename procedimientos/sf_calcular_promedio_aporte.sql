-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE FUNCTION `calcular_promedio_aporte` (
	IdAporteEvaluacion INT,
	IdEstudiante INT,
	IdParalelo INT,
	IdAsignatura INT
)
RETURNS FLOAT
DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT; -- variable de salida de la funcion
	DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	-- Aqui tengo que utilizar dos cursores anidados para calcular el promedio
	DECLARE cRubricasEvaluacion CURSOR FOR
		SELECT id_rubrica_evaluacion
		  FROM sw_rubrica_evaluacion
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE cReCalificaciones CURSOR FOR
		SELECT re_calificacion
		  FROM sw_rubrica_estudiante
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_rubrica_personalizada = IdRubricaEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		OPEN cReCalificaciones;
		Lazo2: LOOP
			FETCH cReCalificaciones INTO ReCalificacion;
			SET ReCalificacion = IFNULL(ReCalificacion, 0);
			SET Suma = Suma + ReCalificacion;
			SET Contador = Contador + 1;
			IF done THEN
				SET done = 0;
				CLOSE cReCalificaciones;
				LEAVE Lazo2;
			END IF;
		END LOOP Lazo2;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END