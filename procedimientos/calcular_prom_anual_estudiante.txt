-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------

DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` 
FUNCTION `calcular_prom_anual_estudiante`(
`IdPeriodoLectivo` INT, 
`IdEstudiante` INT, 
`IdParalelo` INT) 
RETURNS float
    
NO SQL

BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_anual FLOAT;     
    DECLARE promedio_quimestre FLOAT;
    DECLARE IdAsignatura INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    
    DECLARE cAsignaturas CURSOR FOR
    SELECT id_asignatura
      FROM sw_asignatura_curso ac,
           sw_paralelo p
     WHERE p.id_curso = ac.id_curso
       AND id_paralelo = IdParalelo;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAsignaturas;
    
    Lazo: LOOP
    	FETCH cAsignaturas INTO IdAsignatura;
        IF done THEN
        	CLOSE cAsignaturas;
            LEAVE Lazo;
        END IF;
        
        SET promedio_anual = (SELECT calcular_promedio_anual(
            						IdPeriodoLectivo, IdEstudiante,
            						IdParalelo, IdAsignatura));
        SET Suma = Suma + promedio_anual;
        SET Contador = Contador + 1;
    END LOOP Lazo;
    
    SELECT Suma / Contador INTO promedio_anual;
    
    RETURN promedio_anual;
    
END$$