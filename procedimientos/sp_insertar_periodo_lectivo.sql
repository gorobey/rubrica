-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_periodo_lectivo`(
	in AnioInicial int,
	in AnioFinal int
)
BEGIN

	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR 
		SELECT a.id_aporte_evaluacion
		  FROM sw_aporte_evaluacion a,
			   sw_periodo_evaluacion p,
			   sw_periodo_lectivo pl
		 WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion
		   AND p.id_periodo_lectivo = pl.id_periodo_lectivo
		   AND pl.pe_anio_inicio = AnioInicial - 1
		   AND a.ap_tipo < 4;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	-- Primero debo verificar si hay un periodo lectivo anterior
	
	SET @IdPeriodoLectivoAnterior = (SELECT id_periodo_lectivo
                                      FROM sw_periodo_lectivo
                                     WHERE pe_anio_inicio = AnioInicial - 1);

	-- SELECT @IdPeriodoLectivoAnterior;

	IF @IdPeriodoLectivoAnterior IS NOT NULL THEN
		-- Actualizo el estado del periodo lectivo anterior
		UPDATE sw_periodo_lectivo
		   SET pe_estado = 'T'
		 WHERE id_periodo_lectivo = @IdPeriodoLectivoAnterior;

		-- Aqui actualizo a 'C' todos los periodos de evaluacion
		-- menos el examen de gracia utilizando un cursor

		OPEN cAportesEvaluacion;

		REPEAT
			FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
			UPDATE sw_aporte_evaluacion
			   SET ap_estado = 'C'
			 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
		UNTIL done END REPEAT;

		CLOSE cAportesEvaluacion;
	
	END IF;

	-- Finalmente inserto el nuevo periodo lectivo
	INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado)
	VALUES (AnioInicial, AnioFinal, 'A');

END