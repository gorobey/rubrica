-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_abrir_periodos`()
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR
		SELECT id_aporte_evaluacion
		  FROM sw_aporte_evaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_evaluacion
		   SET ap_estado = 'A'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion
		   AND ap_fecha_apertura = (SELECT curdate());
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;

END