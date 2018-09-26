BEGIN
	DECLARE done INT DEFAULT 0;
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAsignatura INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE promedio_asignatura FLOAT;
    
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
        
        SET promedio_asignatura = (SELECT calcular_promedio_aporte(
            						IdAporteEvaluacion, IdEstudiante,
            						IdParalelo, IdAsignatura));
        SET Suma = Suma + promedio_asignatura;
        SET Contador = Contador + 1;
    END LOOP Lazo;
    
    SELECT Suma / Contador INTO promedio_aporte;
    
    RETURN promedio_aporte;
END