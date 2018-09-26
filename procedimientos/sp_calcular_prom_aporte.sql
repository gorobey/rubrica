DELIMITER $$

CREATE PROCEDURE sp_calcular_prom_aporte(
    IN IdAporteEvaluacion INT, 
    IN IdParalelo INT
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_aporte FLOAT;
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
        
        SET promedio_aporte = (SELECT calcular_prom_aporte_estudiante(
        						IdAporteEvaluacion, 
            					IdParalelo, 
            					IdEstudiante));
                                
        IF (EXISTS (SELECT * FROM sw_estudiante_promedio_parcial
                    WHERE id_paralelo = IdParalelo
                    AND id_estudiante = IdEstudiante
                    AND id_aporte_evaluacion = IdAporteEvaluacion)) 
                    THEN
        	UPDATE sw_estudiante_promedio_parcial
            SET ep_promedio = promedio_aporte
            WHERE id_paralelo = IdParalelo
            AND id_estudiante = IdEstudiante
            AND id_aporte_evaluacion = IdAporteEvaluacion;
        ELSE
        	INSERT INTO sw_estudiante_promedio_parcial
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
                id_aporte_evaluacion = IdAporteEvaluacion,
                ep_promedio = promedio_aporte;
        END IF;
    END LOOP Lazo;
END$$