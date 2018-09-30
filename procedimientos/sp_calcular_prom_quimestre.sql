DELIMITER $$

CREATE PROCEDURE sp_calcular_prom_quimestre(
    IN IdPeriodoEvaluacion INT, 
    IN IdParalelo INT
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_quimestre = (SELECT calcular_prom_quim_estudiante(
        						IdPeriodoEvaluacion, 
            					IdParalelo, 
            					IdEstudiante));
                                
        IF (EXISTS (SELECT * FROM sw_estudiante_prom_quimestral
                    WHERE id_paralelo = IdParalelo
                    AND id_estudiante = IdEstudiante
                    AND id_periodo_evaluacion = IdPeriodoEvaluacion)) 
                    THEN
        	UPDATE sw_estudiante_prom_quimestral
            SET eq_promedio = promedio_quimestre
            WHERE id_paralelo = IdParalelo
            AND id_estudiante = IdEstudiante
            AND id_periodo_evaluacion = IdPeriodoEvaluacion;
        ELSE
        	INSERT INTO sw_estudiante_prom_quimestral
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
                id_periodo_evaluacion = IdPeriodoEvaluacion,
                eq_promedio = promedio_quimestre;
        END IF;
    END LOOP Lazo;
END$$