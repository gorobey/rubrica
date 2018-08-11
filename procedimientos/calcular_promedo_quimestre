-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------

DELIMITER $$


CREATE DEFINER=`colegion_1`@`localhost` 
FUNCTION `calcular_promedio_quimestre`(
`IdPeriodoEvaluacion` INT, 
`IdEstudiante` INT, 
`IdParalelo` INT, 
`IdAsignatura` INT) 
RETURNS float
    
NO SQL

BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;     
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Total_Aportes INT DEFAULT 0;
    DECLARE Examen FLOAT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;
    
        DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    SET Total_Aportes = (SELECT COUNT(*) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);
    
    OPEN cAportesEvaluacion;
    
    REPEAT
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
        
        SELECT calcular_promedio_aporte (IdAporteEvaluacion, IdEstudiante, IdParalelo, IdAsignatura) INTO promedio_aporte;
        
        SET Contador = Contador + 1;
        
        IF Contador <= Total_Aportes - 1 THEN
        	SET Suma = Suma + promedio_aporte;
        ELSE
        	SET Examen = promedio_aporte;
        END IF;
    UNTIL done END REPEAT;
    
    CLOSE cAportesEvaluacion;
    
    SET Promedio = Suma / (Total_Aportes - 1);
    
    SELECT 0.8 * Promedio + 0.2 * Examen INTO promedio_quimestre;
    
    RETURN promedio_quimestre;
    
END