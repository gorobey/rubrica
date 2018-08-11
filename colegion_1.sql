-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 20-06-2018 a las 22:42:40
-- Versión del servidor: 10.1.31-MariaDB-cll-lve
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--
CREATE DATABASE IF NOT EXISTS `colegion_1` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `colegion_1`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_actualizar_usuario` (IN `IdUsuario` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsNombreCompleto` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64))  NO SQL
BEGIN
	UPDATE sw_usuario SET
	us_titulo = UsTitulo,
	us_apellidos = UsApellidos,
	us_nombres = UsNombres,
	us_fullname = UsNombreCompleto,
	us_login = UsLogin,
	us_password = UsPassword
	WHERE id_usuario = IdUsuario;
END$$

CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_institucion` (IN `In_nombre` VARCHAR(64), IN `In_direccion` VARCHAR(45), IN `In_telefono1` VARCHAR(12), IN `In_nom_rector` VARCHAR(45), IN `In_nom_secretario` VARCHAR(45))  NO SQL
BEGIN
	IF (EXISTS (SELECT * FROM sw_institucion)) THEN
		UPDATE sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	ELSE
		INSERT INTO sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	END IF;
END$$

CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_periodo_lectivo` (IN `AnioInicial` INT, IN `AnioFinal` INT)  NO SQL
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
			UPDATE sw_aporte_curso_cierre
			   SET ap_estado = 'C'
			 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
		UNTIL done END REPEAT;

		CLOSE cAportesEvaluacion;
	
	END IF;

	-- Finalmente inserto el nuevo periodo lectivo
	INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado)
	VALUES (AnioInicial, AnioFinal, 'A');

END$$

CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_usuario` (IN `IdPeriodoLectivo` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsFullname` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64))  NO SQL
BEGIN
	DECLARE max_id INT;
	INSERT INTO sw_usuario (
		id_periodo_lectivo,
		id_perfil,
		us_titulo,
		us_apellidos,
		us_nombres,
		us_fullname,
		us_login,
		us_password
	) VALUES (
		IdPeriodoLectivo,
		IdPerfil,
		UsTitulo,
		UsApellidos,
		UsNombres,
		UsFullname,
		UsLogin,
		UsPassword
	);
    SET max_id = (SELECT MAX(id_usuario) FROM sw_usuario);
    INSERT INTO sw_usuario_perfil SET id_usuario = max_id, id_perfil = IdPerfil;
END$$

--
-- Funciones
--
CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todas_asignaturas` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(1) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

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
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todos_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todos_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				SET done = 1;
				SET aprueba = FALSE;
			END IF;
		ELSE IF promedio < 5 THEN -- tiene que rendir el examen remedial
				SET done = 1;
				SET aprueba = FALSE;
			 END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT;     
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT id_periodo_evaluacion
	  FROM sw_periodo_evaluacion
	 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_comp_asignatura(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

	RETURN Promedio;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_asignatura` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	
	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND ap_tipo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Calificacion = (
		SELECT co_calificacion
		  FROM sw_calificacion_comportamiento
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_insp_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Cualitativa VARCHAR(3);
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;

	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND ap_tipo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Cualitativa = (
		SELECT co_calificacion
		  FROM sw_comportamiento_inspector
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Cualitativa = IFNULL(Cualitativa, 'S/N');

		SET Calificacion = (
		SELECT ec_correlativa
		  FROM sw_escala_comportamiento
		 WHERE ec_equivalencia = Cualitativa);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_examen_supletorio` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE IdRubricaEvaluacion INT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0; -- variable de salida de la funcion

	-- Aqui obtengo el valor del examen supletorio, si existe
	SET IdRubricaEvaluacion = (SELECT id_rubrica_evaluacion 
								   FROM sw_rubrica_evaluacion r, 
									    sw_aporte_evaluacion a, 
										sw_periodo_evaluacion p 
								  WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
									AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
									AND p.pe_principal = PePrincipal AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
							   FROM sw_rubrica_estudiante 
							  WHERE id_estudiante = IdEstudiante 
								AND id_paralelo = IdParalelo 
								AND id_asignatura = IdAsignatura 
								AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; -- variable de salida de la funcion
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	
	-- Aqui calculo el promedio anual utilizando un cursor
	DECLARE cPeriodosEvaluacion CURSOR FOR
		SELECT id_periodo_evaluacion
		  FROM sw_periodo_evaluacion 
		 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO promedio_anual;

	RETURN promedio_anual;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_anual_proyectos` (`IdPeriodoLectivo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; 	
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE IdClub INT;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT id_periodo_evaluacion
	  FROM sw_periodo_evaluacion 
	 WHERE id_periodo_lectivo = IdPeriodoLectivo
	   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	-- Obtener el id_club correspondiente
	SET IdClub = (SELECT id_club FROM sw_estudiante_club
                   WHERE id_estudiante = IdEstudiante
                     AND id_periodo_lectivo = IdPeriodoLectivo);

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre_club(
									IdPeriodoEvaluacion,IdEstudiante,IdClub));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO promedio_anual;

	RETURN promedio_anual;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_aporte` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT; 	DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	DECLARE cRubricasEvaluacion CURSOR FOR
	SELECT id_rubrica_evaluacion
	  FROM sw_rubrica_evaluacion
	 WHERE id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		SET ReCalificacion = (
		SELECT re_calificacion
		  FROM sw_rubrica_estudiante
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_rubrica_personalizada = IdRubricaEvaluacion);
           
        SET ReCalificacion = IFNULL(ReCalificacion, 0);

		SET Suma = Suma + ReCalificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_aporte_club` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT READS SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT;
    DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	DECLARE cRubricasEvaluacion CURSOR FOR
	SELECT id_rubrica_evaluacion
	  FROM sw_rubrica_evaluacion
	 WHERE id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		SET ReCalificacion = (
		SELECT rc_calificacion
		  FROM sw_rubrica_club
		 WHERE id_estudiante = IdEstudiante
		   AND id_club = IdClub
		   AND id_rubrica_evaluacion = IdRubricaEvaluacion);
           
        SET ReCalificacion = IFNULL(ReCalificacion, 0);

		SET Suma = Suma + ReCalificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0; 	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;
	DECLARE examen_de_gracia FLOAT DEFAULT 0;

	SET promedio_final = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
	IF promedio_final >= 5 AND promedio_final < 7 THEN 		
		SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
		IF examen_supletorio >= 7 THEN
			SET promedio_final = 7;
		ELSE
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	ELSE 
		IF promedio_final > 0 AND promedio_final < 5 THEN
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	END IF;

	RETURN promedio_final;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; -- variable de salida de la funcion
	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
	DECLARE IdAsignatura INT;

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
		SET suma = suma + (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT; -- variable de salida de la funcion
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Total_Aportes INT DEFAULT 0;
    DECLARE Examen FLOAT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;
    
    -- Declaracion del cursor que se va a utilizar
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
    
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_quimestre_club` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT NO SQL
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
        
        SELECT calcular_promedio_aporte_club (IdAporteEvaluacion, IdEstudiante, IdClub) INTO promedio_aporte;
        
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
    
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `calc_max_nro_matricula` () RETURNS VARCHAR(4) CHARSET ascii NO SQL
BEGIN
	DECLARE max_nro_matricula VARCHAR(4);

	SET max_nro_matricula = (SELECT LPAD(MAX(es_nro_matricula)+1,4,'0') FROM sw_estudiante);
	RETURN IFNULL(max_nro_matricula,'0001');

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE supletorio FLOAT DEFAULT 0;

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
		SET supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));

		IF (promedio > 0 AND promedio < 5) OR (promedio >= 5 AND promedio < 7 AND supletorio < 7) THEN 			
			SET contador = contador + 1;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_remediales_no_aprobados` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

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
		IF promedio > 5 AND promedio < 7 THEN 			
			SET contador = contador + 1;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `determinar_asignatura_de_gracia` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE vid_asignatura INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE contador INT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SET contador = (SELECT contar_remediales_no_aprobados(IdPeriodoLectivo,IdEstudiante,IdParalelo));

	IF contador = 1 THEN

		OPEN cAsignaturas;

		Lazo: LOOP
			FETCH cAsignaturas INTO IdAsignatura;
			IF done THEN
				CLOSE cAsignaturas;
				LEAVE Lazo;
			END IF;
			SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
			IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
				SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
				IF examen_supletorio < 7 THEN
					-- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			ELSE 
				IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			END IF;
		END LOOP Lazo;

	END IF;

	RETURN vid_asignatura;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `es_promocionado` (`IdEstudiante` INT, `IdPeriodoLectivo` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	-- DECLARE IdParalelo INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;

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
		SET promedio = (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_curso_asignatura` (`IdCurso` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(ac_orden)
		  FROM sw_asignatura_curso
		 WHERE id_curso = IdCurso);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_hora_clase_dia_semana` (`IdDiaSemana` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(hc_ordinal)
		  FROM sw_hora_clase
		 WHERE id_dia_semana = IdDiaSemana);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_menu_nivel_perfil_padre` (`Nivel` INT, `IdPerfil` INT, `Padre` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(mnu_orden)
		  FROM sw_menu
		 WHERE id_perfil = IdPerfil
           AND mnu_nivel = Nivel
           AND mnu_padre = Padre);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_curso_cierre`
--

CREATE TABLE `sw_aporte_curso_cierre` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_evaluacion`
--

CREATE TABLE `sw_aporte_evaluacion` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_tipo_aporte` int(11) NOT NULL,
  `ap_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_abreviatura` varchar(8) NOT NULL,
  `ap_tipo` tinyint(4) NOT NULL,
  `ap_estado` varchar(1) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_fecha_inicio` date NOT NULL,
  `ap_fecha_fin` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) NOT NULL,
  `ar_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncar tablas antes de insertar `sw_area`
--

TRUNCATE TABLE `sw_area`;
--
-- Volcado de datos para la tabla `sw_area`
--

INSERT INTO `sw_area` (`id_area`, `ar_nombre`) VALUES
(1, 'LENGUA Y LITERATURA'),
(2, 'MATEMATICA'),
(3, 'CIENCIAS SOCIALES'),
(4, 'CIENCIAS NATURALES'),
(5, 'EDUCACION CULTURAL Y ARTISTICA'),
(6, 'EDUCACION FISICA'),
(7, 'LENGUA EXTRANJERA'),
(8, 'PROYECTOS ESCOLARES'),
(9, 'MODULO INTERDISCIPLINAR'),
(10, 'CONTABILIDAD'),
(11, 'ADMINISTRACION DE SISTEMAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL,
  `as_nombre` varchar(84) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `as_abreviatura` varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `as_carga_horaria` int(11) NOT NULL,
  `as_orden` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_asignatura`
--

TRUNCATE TABLE `sw_asignatura`;
--
-- Volcado de datos para la tabla `sw_asignatura`
--

INSERT INTO `sw_asignatura` (`id_asignatura`, `id_area`, `id_curso`, `id_tipo_asignatura`, `as_nombre`, `as_abreviatura`, `as_carga_horaria`, `as_orden`) VALUES
(1, 2, 0, 1, 'MATEMATICA', 'MAT', 6, 2),
(2, 3, 0, 1, 'ESTUDIOS SOCIALES', 'EESS', 5, 4),
(3, 4, 0, 1, 'CIENCIAS NATURALES', 'CCNN', 6, 3),
(4, 6, 0, 1, 'EDUCACION FISICA', 'EDU.F.', 2, 6),
(5, 11, 0, 1, 'DIBUJO TECNICO APLICADO', 'DIB', 0, 0),
(6, 1, 0, 1, 'LENGUA Y LITERATURA', 'LEN', 6, 1),
(7, 7, 0, 1, 'INGLES', 'ING', 5, 7),
(8, 4, 0, 1, 'BIOLOGIA', 'BIO', 0, 0),
(9, 4, 0, 1, 'FISICA', 'FIS', 2, 1),
(10, 4, 0, 1, 'QUIMICA', 'QUIM', 2, 2),
(11, 9, 0, 1, 'INFORMATICA APLICADA A LA EDUCACION', 'INFO', 0, 0),
(12, 5, 0, 1, 'EDUCACION CULTURAL Y ARTISTICA', 'ED.ART', 0, 0),
(13, 1, 0, 1, 'LENGUA Y LITERATURA', 'LIT', 0, 0),
(14, 11, 0, 1, 'FUNDAMENTOS DE PROGRAMACION', 'FUND.P', 0, 0),
(15, 11, 0, 1, 'SISTEMAS INFORMATICOS MONOUSUARIOS Y MULTIUSUARIOS', 'MONO', 0, 0),
(16, 4, 0, 1, 'FISICO QUIMICA', 'FISQ', 0, 0),
(17, 6, 0, 1, 'EDUCACION FISICA', 'EDU.F.', 0, 6),
(18, 5, 0, 1, 'EDUCACION ESTETICA', 'EDU.E.', 3, 5),
(19, 3, 0, 1, 'HISTORIA', 'HIST', 2, 3),
(20, 3, 0, 1, 'EDUCACION PARA LA CIUDADANIA', 'EDU.C.', 0, 0),
(21, 3, 0, 1, 'DESARROLLO DEL PENSAMIENTO FILOSOFICO', 'DES.P.', 0, 0),
(22, 11, 0, 1, 'GESTOR DE BASES DE DATOS', 'GBDD', 0, 0),
(23, 11, 0, 1, 'REDES DE AREA LOCAL', 'REDES', 0, 0),
(24, 11, 0, 1, 'IMPLANTACION DE APLICACIONES INFORMATICAS DE GESTION', 'MIAIG', 0, 0),
(25, 9, 0, 1, 'EMPRENDIMIENTO Y GESTION', 'EMPRE', 0, 0),
(26, 10, 0, 1, 'GESTION ADMINISTRATIVA DE COMPRA Y VENTA', 'COMPRA', 0, 0),
(27, 9, 0, 1, 'OPTATIVA', 'OPTA', 2, 8),
(28, 10, 0, 1, 'CONTABILIDAD GENERAL Y TESORERIA', 'CONTA', 0, 0),
(29, 10, 0, 1, 'GESTION ADMINISTRATIVA DE LOS RECURSOS HUMANOS', 'RRHH', 0, 0),
(30, 10, 0, 1, 'PRODUCTOS Y SERVICIOS FINANCIEROS Y DE SEGUROS BASICOS', 'PROD', 0, 0),
(31, 10, 0, 1, 'APLICACIONES INFORMATICAS', 'APL.I.', 0, 0),
(32, 10, 0, 1, 'COMUNICACION, ARCHIVO DE LA INFORMACION Y OPERATORIA DE TECLADOS', 'COMU', 0, 0),
(33, 11, 0, 1, 'RELACIONES EN EL ENTORNO DE TRABAJO', 'RET', 0, 0),
(34, 11, 0, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', 0, 0),
(35, 11, 0, 1, 'DESARROLLO DE FUNCIONES EN EL SISTEMA INFORMATICO', 'DES.F.', 0, 0),
(40, 11, 0, 1, 'FORMACION EN CENTROS DE TRABAJO', 'FCT', 0, 0),
(152, 10, 0, 1, 'CONTABILIDAD GENERAL Y TESORERIA', 'CONTA', 0, 0),
(153, 3, 0, 1, 'FILOSOFIA', 'FILO', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura_curso`
--

CREATE TABLE `sw_asignatura_curso` (
  `id_asignatura_curso` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ac_orden` int(11) NOT NULL,
  `ac_num_horas` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asistencia_estudiante`
--

CREATE TABLE `sw_asistencia_estudiante` (
  `id_asistencia_estudiante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `ae_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) NOT NULL,
  `id_curso_inferior` int(11) DEFAULT NULL,
  `id_curso_superior` int(11) DEFAULT NULL,
  `id_periodo_lectivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_calificacion_comportamiento`
--

CREATE TABLE `sw_calificacion_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `co_calificacion` float NOT NULL,
  `co_cualitativa` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club`
--

CREATE TABLE `sw_club` (
  `id_club` int(11) NOT NULL,
  `cl_nombre` varchar(32) NOT NULL,
  `cl_abreviatura` varchar(6) NOT NULL,
  `cl_carga_horaria` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club_docente`
--

CREATE TABLE `sw_club_docente` (
  `id_club_docente` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento`
--

CREATE TABLE `sw_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_indice_evaluacion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_inspector`
--

CREATE TABLE `sw_comportamiento_inspector` (
  `id_comportamiento_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL,
  `nro_faltas` int(11) NOT NULL,
  `justificadas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso`
--

CREATE TABLE `sw_curso` (
  `id_curso` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `cu_nombre` varchar(128) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cu_orden` int(11) NOT NULL,
  `id_curso_superior` int(11) NOT NULL,
  `bol_proyectos` tinyint(1) NOT NULL,
  `cu_abreviatura` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_ordinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_calificaciones`
--

CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ec_cualitativa` varchar(64) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_escala_calificaciones`
--

TRUNCATE TABLE `sw_escala_calificaciones`;
--
-- Volcado de datos para la tabla `sw_escala_calificaciones`
--

INSERT INTO `sw_escala_calificaciones` (`id_escala_calificaciones`, `id_periodo_lectivo`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`) VALUES
(1, 1, 'Supera los aprendizajes requeridos.', '10', 10, 10, 1, 'S'),
(2, 1, 'Domina los aprendizajes requeridos.', '9', 9, 9.999, 2, 'D'),
(3, 1, 'Alcanza los aprendizajes requeridos.', '7-8', 7, 8.999, 3, 'A'),
(4, 1, 'Está próximo a alcanzar los aprendizajes requeridos.', '5-6', 4.001, 6.999, 4, 'E'),
(5, 1, 'No alcanza los aprendizajes requeridos.', '<= 4', 0.01, 4, 5, 'N'),
(6, 2, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(7, 2, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(8, 2, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(9, 2, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(10, 3, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(11, 3, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(12, 3, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(13, 3, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(14, 4, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(15, 4, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(16, 4, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(17, 4, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(18, 5, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(19, 5, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(20, 5, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(21, 5, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_escala_comportamiento`
--

TRUNCATE TABLE `sw_escala_comportamiento`;
--
-- Volcado de datos para la tabla `sw_escala_comportamiento`
--

INSERT INTO `sw_escala_comportamiento` (`id_escala_comportamiento`, `ec_relacion`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_equivalencia`, `ec_correlativa`) VALUES
(1, 'A = muy satisfactorio', 'Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.', '9 - 10', 9, 10, 'A', 5),
(2, 'B = satisfactorio', 'Cumple con los compromisos establecidos para la sana convivencia social.', '7 - 8.99', 7, 8.99, 'B', 4),
(3, 'C = poco satisfactorio', 'Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '6 - 6.99', 6, 6.99, 'C', 3),
(4, 'D = mejorable', 'Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '4 - 5.99', 4, 5.99, 'D', 2),
(5, 'E = insatisfactorio', 'No cumple con los compromisos establecidos para la sana convivencia social.', '< 4', 0.01, 3.99, 'E', 1),
(6, 'S/N = sin notas', 'Sin notas.', '0', 0, 0, 'S/N', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_proyectos`
--

CREATE TABLE `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL,
  `ec_cualitativa` varchar(256) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) NOT NULL,
  `ec_abreviatura` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_escala_proyectos`
--

TRUNCATE TABLE `sw_escala_proyectos`;
--
-- Volcado de datos para la tabla `sw_escala_proyectos`
--

INSERT INTO `sw_escala_proyectos` (`id_escala_proyectos`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`, `ec_abreviatura`) VALUES
(1, 'Demuestra destacado desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un excelente aporte a su formación integral.', '9.00 - 10.00', 9, 10, 1, 'EXCELENTE', 'EX'),
(2, 'Demuestra muy buen desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un aporte a su formación integral.', '7.00 - 8.99', 7, 8.99, 2, 'MUY BUENA', 'MB'),
(3, 'Demuestra buen desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye a su formación integral.', '4.01 - 6.99', 4.01, 6.99, 3, 'BUENA', 'B'),
(4, 'Demuestra regular desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye escasamente a su formación integral.', '<= 4', 0.01, 4, 4, 'REGULAR', 'R'),
(5, 'Sin notas.', '0', 0, 0, 5, 'SIN NOTAS', 'SN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) NOT NULL,
  `id_tipo_educacion` int(11) NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(50) NOT NULL,
  `es_orden` int(11) NOT NULL,
  `es_abreviatura` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `es_nro_matricula` varchar(4) NOT NULL,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) NOT NULL,
  `es_nombre_completo` varchar(64) NOT NULL,
  `es_cedula` varchar(10) NOT NULL,
  `es_genero` varchar(1) NOT NULL,
  `es_email` varchar(64) NOT NULL,
  `es_sector` varchar(36) NOT NULL,
  `es_direccion` varchar(64) NOT NULL,
  `es_telefono` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_club`
--

CREATE TABLE `sw_estudiante_club` (
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `es_retirado` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_periodo_lectivo`
--

CREATE TABLE `sw_estudiante_periodo_lectivo` (
  `id_estudiante_periodo_lectivo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `es_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_retirado` varchar(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_foro`
--

CREATE TABLE `sw_foro` (
  `id_foro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fo_titulo` varchar(50) NOT NULL,
  `fo_descripcion` varchar(250) NOT NULL,
  `fo_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario`
--

CREATE TABLE `sw_horario` (
  `id_horario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `hc_nombre` varchar(10) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_ordinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_inasistencia`
--

CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) NOT NULL,
  `in_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_inasistencia`
--

TRUNCATE TABLE `sw_inasistencia`;
--
-- Volcado de datos para la tabla `sw_inasistencia`
--

INSERT INTO `sw_inasistencia` (`id_inasistencia`, `in_nombre`, `in_abreviatura`) VALUES
(1, 'Atraso', 'A'),
(2, 'Fuga', 'F'),
(3, 'falta Injustificada', 'I'),
(4, 'falta Justificada', 'J'),
(5, 'Permiso', 'P'),
(6, 'aSiste', 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_indice_evaluacion`
--

CREATE TABLE `sw_indice_evaluacion` (
  `id_indice_evaluacion` int(11) NOT NULL,
  `valores_t` float NOT NULL,
  `cum_norma_t` float NOT NULL,
  `pun_asiste_t` float NOT NULL,
  `presentacion_t` float NOT NULL,
  `valores_i` float NOT NULL,
  `cum_norma_i` float NOT NULL,
  `pun_asiste_i` float NOT NULL,
  `presentacion_i` float NOT NULL,
  `total` float NOT NULL,
  `promedio` float NOT NULL,
  `equivalencia` varchar(1) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_indice_evaluacion_def`
--

CREATE TABLE `sw_indice_evaluacion_def` (
  `id_indice_evaluacion` int(11) NOT NULL,
  `ie_descripcion` varchar(64) NOT NULL,
  `ie_abreviatura` varchar(8) NOT NULL,
  `ie_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_indice_evaluacion_def`
--

TRUNCATE TABLE `sw_indice_evaluacion_def`;
--
-- Volcado de datos para la tabla `sw_indice_evaluacion_def`
--

INSERT INTO `sw_indice_evaluacion_def` (`id_indice_evaluacion`, `ie_descripcion`, `ie_abreviatura`, `ie_orden`) VALUES
(1, 'CUMPLE CON VALORES', 'VALORES', 1),
(2, 'CUMPLE CON LAS NORMAS INSTITUCIONALES', 'NORMAS', 2),
(3, 'ASISTE PUNTUALMENTE A LA INSTITUCION', 'PUNTUAL', 3),
(4, 'CUMPLE LAS NORMAS DE PRESENTACION', 'PRESENT', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(12) NOT NULL,
  `in_nom_rector` varchar(45) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_institucion`
--

TRUNCATE TABLE `sw_institucion`;
--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_nom_rector`, `in_nom_secretario`) VALUES
(1, 'UNIDAD EDUCATIVA PCEI FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256-104', 'MSC. EDISON CUYO', 'MG. ANA PILATAXI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_mensaje`
--

CREATE TABLE `sw_mensaje` (
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `me_texto` varchar(250) NOT NULL,
  `me_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu`
--

CREATE TABLE `sw_menu` (
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `mnu_texto` varchar(32) NOT NULL,
  `mnu_enlace` varchar(64) NOT NULL,
  `mnu_link` varchar(64) NOT NULL,
  `mnu_nivel` int(11) NOT NULL,
  `mnu_orden` int(11) NOT NULL,
  `mnu_padre` int(11) NOT NULL,
  `mnu_publicado` int(11) NOT NULL,
  `mnu_icono` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_menu`
--

TRUNCATE TABLE `sw_menu`;
--
-- Volcado de datos para la tabla `sw_menu`
--

INSERT INTO `sw_menu` (`id_menu`, `id_perfil`, `mnu_texto`, `mnu_enlace`, `mnu_link`, `mnu_nivel`, `mnu_orden`, `mnu_padre`, `mnu_publicado`, `mnu_icono`) VALUES
(2, 1, 'Menus', 'menu/index.php', 'administracion/menus', 1, 3, 146, 1, 'fa fa-circle-o'),
(12, 1, 'Perfiles', 'perfil/index.php', 'administracion/perfiles', 1, 1, 146, 1, 'fa fa-circle-o'),
(13, 2, 'Ingresar calificaciones', '#', '', 1, 2, 0, 1, ''),
(40, 3, 'Promoción', 'promocion/index.php', '', 1, 8, 0, 1, ''),
(18, 1, 'Usuarios', 'usuario/index.php', 'administracion/usuarios', 1, 3, 146, 1, 'fa fa-circle-o'),
(21, 3, 'Matriculación', '#', '#', 1, 4, 0, 1, 'fa fa-share-alt'),
(26, 5, 'Comportamiento', '#', '', 1, 2, 0, 1, ''),
(28, 3, 'Definiciones', '#', '#', 1, 2, 0, 1, 'fa fa-cogs'),
(31, 6, 'Definir Rúbricas', '#', '', 1, 2, 0, 1, ''),
(32, 3, 'Libretación', '#', '', 1, 5, 0, 1, ''),
(39, 3, 'A Excel', '#', '', 1, 7, 0, 1, ''),
(48, 2, 'Listas', '#', '', 1, 7, 0, 1, ''),
(43, 1, 'Cierres', '#', '#', 1, 8, 0, 1, 'fa fa-lock'),
(120, 1, 'Periodos', 'aportes_evaluacion/view_cerrar_periodos.php', 'admin/cerrar_periodos', 2, 2, 43, 1, 'fa fa-circle-o'),
(37, 3, 'Reporte', '#', '', 1, 6, 0, 1, ''),
(38, 2, 'Reportes', '#', '', 1, 6, 0, 1, ''),
(196, 6, 'Definir Escalas', 'escalas/index.php', '', 2, 6, 31, 1, ''),
(45, 2, 'Informes', '#', '', 1, 5, 0, 0, ''),
(173, 3, 'Representantes', '', 'secretaria/representantes', 0, 1, 21, 1, 'fa fa-user-plus'),
(194, 7, 'Horario', 'horarios/v_horario_paralelo.php', '', 1, 4, 0, 0, ''),
(52, 7, 'Reportes', '#', '', 1, 3, 0, 1, ''),
(149, 1, 'Especificaciones', '', '#', 0, 5, 0, 1, 'fa fa-share-alt'),
(121, 1, 'Periodos Lectivos', 'periodos_lectivos/view_cerrar_periodos_lectivos.php', 'admin/cerrar_anio_lectivo', 2, 3, 43, 1, 'fa fa-circle-o'),
(55, 5, 'Foros', 'foros/index.php', '', 1, 3, 0, 1, ''),
(56, 2, 'Foros', 'foros/index.php', '', 1, 8, 0, 0, ''),
(58, 7, 'Comportamiento', '#', '', 1, 2, 0, 1, ''),
(60, 6, 'Asociar Cierres', 'aportes_evaluacion/view_definir_cierres.php', '', 1, 6, 0, 1, ''),
(62, 6, 'Períodos de Evaluación', 'periodos_evaluacion/index.php', 'param/periodos_evaluacion', 2, 2, 31, 1, 'fa fa-circle-o'),
(63, 6, 'Aportes de Evaluación', 'aportes_evaluacion/index.php', '', 2, 3, 31, 1, ''),
(64, 6, 'Rúbricas de Evaluación', 'rubricas_evaluacion/index.php', '', 2, 4, 31, 1, ''),
(65, 6, 'Rúbricas de Proyectos', 'rubricas_proyecto/index.php', '', 2, 5, 31, 1, ''),
(66, 2, 'Quimestrales', 'calificaciones/index.php', '', 2, 1, 13, 1, ''),
(67, 2, 'Supletorios', 'calificaciones/supletorios.php', '', 2, 2, 13, 1, ''),
(68, 2, 'Remediales', 'calificaciones/remediales.php', '', 2, 3, 13, 1, ''),
(69, 2, 'De Gracia', 'calificaciones/de_gracia.php', '', 2, 4, 13, 1, ''),
(70, 2, 'Proyectos', 'calificaciones/clubes.php', '', 2, 5, 13, 1, ''),
(71, 2, 'Parciales', 'calificaciones/informe_parciales.php', '', 2, 1, 45, 1, ''),
(72, 2, 'Quimestrales', 'calificaciones/informe_quimestral.php', '', 2, 2, 45, 1, ''),
(73, 2, 'Anuales', 'calificaciones/informe_anual.php', '', 2, 3, 45, 1, ''),
(74, 2, 'Quimestrales', 'reportes/por_periodo.php', '', 2, 1, 38, 1, ''),
(75, 2, 'Anuales', 'calificaciones/reporte_anual.php', '', 2, 2, 38, 1, ''),
(76, 2, 'Por Parcial', 'listas_estudiantes/por_parcial.php', '', 2, 1, 48, 1, ''),
(77, 2, 'Por Quimestre', 'listas_estudiantes/por_quimestre.php', '', 2, 2, 48, 1, ''),
(78, 5, 'Quimestral', 'inspeccion/comportamiento.php', '', 2, 2, 26, 1, ''),
(79, 5, 'Anual', 'inspeccion/comportamiento_anual.php', '', 2, 3, 26, 1, ''),
(80, 3, 'Institución', 'institucion/index.php', '', 2, 1, 28, 1, ''),
(81, 3, 'Periodos Lectivos', 'periodos_lectivos/index.php', '', 2, 2, 28, 1, ''),
(82, 3, 'Tipos de Educación', 'tipo_educacion/index.php', 'definiciones/tipos_educacion', 2, 3, 28, 1, 'fa fa-university'),
(83, 3, 'Especialidades', 'especialidades/index.php', 'definiciones/especialidades', 2, 4, 28, 1, 'fa fa-university'),
(84, 3, 'Cursos', 'cursos/index.php', 'definiciones/cursos', 2, 5, 28, 1, 'fa fa-university'),
(85, 3, 'Paralelos', 'paralelos/index.php', 'definiciones/paralelos', 2, 6, 28, 1, 'fa fa-university'),
(86, 3, 'Tipos de Asignaturas', 'tipos_asignatura/index.php', '', 2, 7, 28, 1, ''),
(87, 3, 'Asignaturas', 'asignaturas/index.php', 'definiciones/asignaturas', 2, 8, 28, 1, 'fa fa-university'),
(88, 3, 'Proyectos', 'clubes/index.php', '', 2, 9, 28, 1, ''),
(176, 1, 'Por Hacer', 'por_hacer/index.php', '#', 0, 3, 0, 1, 'fa fa-list-alt'),
(123, 3, 'Paralelos', 'matriculacion/index.php', 'secretaria/matriculacion_paralelos', 2, 2, 21, 1, 'fa fa-university'),
(124, 3, 'Proyectos Escolares', 'matriculacion/clubes.php', 'secretaria/proyectos_escolares', 2, 1, 21, 1, ''),
(97, 3, 'Validar calificaciones', 'calificaciones/validar_calificaciones.php', '', 2, 1, 32, 1, ''),
(98, 3, 'Libretación', 'reportes/libretacion.php', '', 2, 2, 32, 1, ''),
(99, 3, 'Por Asignatura', 'calificaciones/por_asignaturas.php', '', 2, 1, 37, 1, ''),
(100, 3, 'Parciales', 'calificaciones/reporte_parciales.php', '', 2, 2, 37, 1, ''),
(101, 3, 'Quimestral', 'calificaciones/procesar_promedios.php', '', 2, 3, 37, 1, ''),
(102, 3, 'Anual', 'calificaciones/promedios_anuales.php', '', 2, 4, 37, 1, ''),
(103, 3, 'De Supletorios', 'calificaciones/reporte_supletorios.php', '', 2, 5, 37, 1, ''),
(104, 3, 'De Remediales', 'calificaciones/reporte_remediales.php', '', 2, 6, 37, 1, ''),
(105, 3, 'De Exámenes de Gracia', 'calificaciones/reporte_de_gracia.php', '', 2, 7, 37, 1, ''),
(106, 3, 'Proyectos', 'calificaciones/reporte_de_clubes.php', '', 2, 8, 37, 1, ''),
(107, 3, 'Quimestrales', 'php_excel/quimestrales.php', '', 2, 2, 39, 1, ''),
(108, 3, 'Anuales', 'php_excel/anuales.php', '', 2, 3, 39, 1, ''),
(109, 3, 'Cuadro Final', 'php_excel/cuadro_final.php', '', 2, 4, 39, 1, ''),
(110, 7, 'Parciales', 'tutores/parciales.php', '', 2, 1, 52, 1, ''),
(111, 7, 'Quimestrales', 'tutores/quimestrales.php', '', 2, 2, 52, 1, ''),
(112, 7, 'Anuales', 'tutores/anuales.php', '', 2, 3, 52, 1, ''),
(113, 7, 'Supletorios', 'tutores/supletorios.php', '', 2, 4, 52, 1, ''),
(114, 7, 'Remediales', 'tutores/remediales.php', '', 2, 5, 52, 1, ''),
(116, 7, 'De Parciales', 'tutores/comp_parciales.php', '', 2, 1, 58, 1, ''),
(117, 7, 'De Quimestrales', 'tutores/comportamiento.php', '', 2, 1, 58, 1, ''),
(118, 7, 'Anual', 'tutores/comp_anual.php', '', 2, 1, 58, 1, ''),
(119, 2, 'Proyectos', 'listas_estudiantes/proyectos.php', '', 2, 3, 48, 1, ''),
(195, 6, 'Definir Inasistencias', 'inasistencias/index.php', '', 2, 2, 127, 1, ''),
(127, 6, 'Definir Horarios', '#', '', 1, 4, 0, 1, ''),
(128, 6, 'Definir Días de la Semana', 'horarios/view_dia_semana.php', '', 2, 1, 127, 1, ''),
(129, 6, 'Definir Horas Clase', 'horarios/view_hora_clase.php', '', 2, 1, 127, 1, ''),
(130, 6, 'Definir Horario Semanal', 'horarios/view_horario_semanal.php', '', 2, 1, 127, 1, ''),
(132, 6, 'Reportes', '#', '', 1, 7, 0, 1, ''),
(133, 6, 'Parciales', 'calificaciones/reporte_parciales.php', '', 2, 1, 132, 1, ''),
(134, 6, 'Quimestral', 'calificaciones/procesar_promedios.php', '', 2, 1, 132, 1, ''),
(135, 6, 'Anual', 'calificaciones/promedios_anuales.php', '', 2, 1, 132, 1, ''),
(138, 2, 'Asistencia', 'horarios/view_asistencia.php', '', 1, 4, 0, 1, ''),
(139, 6, 'Modalidades', 'modalidades/index.php', '', 2, 1, 31, 1, ''),
(140, 3, 'Cuadro Remediales', 'php_excel/cuadro_remediales.php', '', 2, 5, 39, 1, ''),
(141, 3, 'Cuadro De Gracia', 'php_excel/cuadro_de_gracia.php', '', 2, 6, 39, 1, ''),
(142, 3, 'Nómina de Matriculados', 'php_excel/nomina_matriculados.php', '', 2, 1, 39, 1, ''),
(143, 7, 'De Gracia', 'tutores/de_gracia.php', '', 2, 6, 52, 1, ''),
(144, 5, 'Parciales', 'inspeccion/comportamiento_parciales.php', '', 2, 1, 26, 1, ''),
(147, 1, 'Permisos', '', 'administracion/permisos', 0, 4, 146, 1, 'fa fa-check-circle'),
(146, 1, 'Administración', '#', '#', 1, 4, 0, 1, 'fa fa-cogs'),
(151, 1, 'Períodos de Evaluación', '', 'especificaciones/periodos_evaluacion', 0, 1, 149, 1, 'fa fa-circle-o'),
(152, 1, 'Aportes de Evaluación', '', 'especificaciones/aportes_evaluacion', 0, 2, 149, 1, 'fa fa-circle-o'),
(153, 1, 'Rúbricas de Evaluación', '', 'especificaciones/rubricas_evaluacion', 0, 3, 149, 1, 'fa fa-circle-o'),
(160, 1, 'Tipos de Educación', '', 'definiciones/tipos_educacion', 0, 1, 159, 1, 'fa fa-university'),
(159, 1, 'Definiciones', '', '#', 0, 6, 0, 1, 'fa fa-cogs'),
(156, 1, 'Asociar', '', 'admin/asociar_cierres', 0, 1, 43, 1, 'fa fa-circle-o'),
(161, 1, 'Especialidades', '', 'definiciones/especialidades', 0, 2, 159, 1, 'fa fa-university'),
(162, 1, 'Cursos', '', 'definiciones/cursos', 0, 3, 159, 1, 'fa fa-university'),
(163, 1, 'Paralelos', '', 'definiciones/paralelos', 0, 4, 159, 1, 'fa fa-university'),
(164, 1, 'Areas', '', 'definiciones/areas', 0, 5, 159, 1, 'fa fa-university'),
(165, 1, 'Asignaturas', '', 'definiciones/asignaturas', 0, 6, 159, 1, 'fa fa-university'),
(166, 1, 'Asociar', '', '#', 0, 7, 0, 1, 'fa fa-share-alt'),
(167, 1, 'Asignaturas Cursos', '', 'asociaciones/asignaturas_cursos', 0, 1, 166, 1, 'fa fa-circle-o'),
(168, 1, 'Asignaturas Docentes', '', 'asociaciones/asignaturas_docentes', 0, 2, 166, 1, 'fa fa-circle-o'),
(170, 1, 'Rúbricas de Proyectos', '', 'especificaciones/rubricas_proyectos', 0, 4, 149, 1, 'fa fa-circle-o'),
(171, 1, 'Paralelos Tutores', '', 'asociaciones/paralelos_tutores', 0, 3, 166, 1, 'fa fa-circle-o'),
(172, 1, 'Paralelos Inspectores', '', 'asociaciones/paralelos_inspectores', 0, 4, 166, 1, 'fa fa-circle-o'),
(189, 13, 'Reportes', '#', '', 1, 1, 0, 1, ''),
(179, 13, 'Parciales', 'calificaciones/reporte_parciales.php', 'parciales', 2, 1, 189, 1, ''),
(180, 13, 'Quimestrales', 'calificaciones/procesar_promedios.php', 'quimestrales', 2, 2, 189, 1, ''),
(181, 13, 'Anuales', 'calificaciones/promedios_anuales.php', 'anuales', 2, 3, 189, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_modalidad`
--

CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `mo_nombre` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_asignatura`
--

CREATE TABLE `sw_paralelo_asignatura` (
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL,
  `pe_acceso_login` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_perfil`
--

TRUNCATE TABLE `sw_perfil`;
--
-- Volcado de datos para la tabla `sw_perfil`
--

INSERT INTO `sw_perfil` (`id_perfil`, `pe_nombre`, `pe_nivel_acceso`, `pe_acceso_login`) VALUES
(1, 'Administrador', 3, 1),
(2, 'Docente', 2, 1),
(3, 'Secretaría', 1, 1),
(5, 'Inspector', 1, 1),
(6, 'Autoridad', 3, 1),
(7, 'Tutor', 2, 1),
(13, 'DECE', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_estado`
--

CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) NOT NULL,
  `pe_descripcion` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion`
--

CREATE TABLE `sw_periodo_evaluacion` (
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `pe_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_abreviatura` varchar(6) NOT NULL,
  `pe_principal` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_lectivo`
--

CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_estado` int(11) NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `pe_anio_inicio` int(11) NOT NULL,
  `pe_anio_fin` int(11) NOT NULL,
  `pe_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_permiso`
--

CREATE TABLE `sw_permiso` (
  `id_permiso` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `read` int(11) NOT NULL,
  `insert` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `delete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_plan_rubrica`
--

CREATE TABLE `sw_plan_rubrica` (
  `id_plan_rubrica` int(11) NOT NULL,
  `pr_tema` varchar(50) NOT NULL,
  `pr_descripcion` varchar(250) NOT NULL,
  `pr_fecha_elab` datetime NOT NULL,
  `pr_fecha_eval` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_recomendaciones`
--

CREATE TABLE `sw_recomendaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `re_recomendaciones` varchar(255) NOT NULL,
  `re_plan_de_mejora` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_recomendaciones_anuales`
--

CREATE TABLE `sw_recomendaciones_anuales` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `re_plan_de_mejora_anual` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_recomendaciones_quimestrales`
--

CREATE TABLE `sw_recomendaciones_quimestrales` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `re_plan_de_mejora_quimestral` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_representante`
--

CREATE TABLE `sw_representante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `re_apellidos` varchar(32) DEFAULT NULL,
  `re_nombres` varchar(32) DEFAULT NULL,
  `re_nombre_completo` varchar(64) DEFAULT NULL,
  `re_cedula` varchar(10) DEFAULT NULL,
  `re_genero` varchar(1) DEFAULT NULL,
  `re_email` varchar(64) DEFAULT NULL,
  `re_sector` varchar(36) DEFAULT NULL,
  `re_direccion` varchar(64) DEFAULT NULL,
  `re_telefono` varchar(16) DEFAULT NULL,
  `re_observacion` varchar(256) DEFAULT NULL,
  `re_parentesco` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='					';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_respuesta`
--

CREATE TABLE `sw_respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `re_texto` text NOT NULL,
  `re_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `re_autor` int(11) NOT NULL,
  `re_perfil` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_club`
--

CREATE TABLE `sw_rubrica_club` (
  `id_rubrica_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `rc_calificacion` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_docente`
--

CREATE TABLE `sw_rubrica_docente` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rd_nombre` varchar(64) NOT NULL,
  `rd_descripcion` varchar(256) NOT NULL,
  `rd_fecha_envio` date NOT NULL,
  `rd_fecha_revision` date NOT NULL,
  `rd_observacion` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_estudiante`
--

CREATE TABLE `sw_rubrica_estudiante` (
  `id_rubrica_estudiante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL,
  `re_calificacion` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion`
--

CREATE TABLE `sw_rubrica_evaluacion` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `ru_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_abreviatura` varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion_club`
--

CREATE TABLE `sw_rubrica_evaluacion_club` (
  `id_rubrica_evaluacion_club` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `rc_nombre` varchar(24) NOT NULL,
  `rc_abreviatura` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_personalizada`
--

CREATE TABLE `sw_rubrica_personalizada` (
  `id_rubrica_personalizada` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rp_tema` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `rp_fec_envio` date NOT NULL,
  `rp_fec_evaluacion` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_proyecto`
--

CREATE TABLE `sw_rubrica_proyecto` (
  `id_rubrica_proyecto` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) DEFAULT NULL,
  `rp_nombre` varchar(36) DEFAULT NULL,
  `rp_abreviatura` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_submenu`
--

CREATE TABLE `sw_submenu` (
  `id_submenu` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `sbmnu_texto` varchar(64) NOT NULL,
  `sbmnu_enlace` varchar(64) NOT NULL,
  `sbmnu_nivel` int(11) NOT NULL,
  `sbmnu_orden` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tarea`
--

CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Truncar tablas antes de insertar `sw_tarea`
--

TRUNCATE TABLE `sw_tarea`;
--
-- Volcado de datos para la tabla `sw_tarea`
--

INSERT INTO `sw_tarea` (`id`, `tarea`, `hecho`, `fecha`) VALUES
(6, 'Implementar el subsistema de estadísticas para el perfil de autoridad. Idea: 4 de abril de 2018', 0, '2018-04-05 13:31:33'),
(5, 'Revisar el Reporte de Comportamiento de Parciales en el Perfil de Inspector. Inicio 3 de abril de 2018.', 0, '2018-04-10 15:41:27'),
(32, 'Implementar la opción de consultar el horario semanal de clases en el perfil de tutor. Idea: 10-jun-2018.', 0, '2018-06-14 00:22:09'),
(33, 'Cambiar el reporte de comportamiento anual de Inspección. Inicio: 19-jun-2018.', 0, '2018-06-19 17:35:32'),
(34, 'Cambiar el reporte de comportamiento anual de Tutores. Inicio: 19-jun-2018.', 0, '2018-06-19 19:19:26'),
(11, 'Falta codificar el funcionamiento de hacer clic en el casillero de Autorepresentado en Datos del Representante.', 0, '2018-04-05 15:14:09'),
(14, 'Cambiar el menú de asistencia: Asistencia/Ingreso, Asistencia/Parciales, Asistencia/Quimestres, Asistencia/Anual; en el perfil de docente. Inicio: Abril 19 de 2018.', 0, '2018-04-20 15:03:53'),
(16, 'Falta cambiar la apariencia del formulario cambiar clave (https://bootsnipp.com/snippets/featured/change-password-form-with-validation)', 1, '2018-04-20 20:32:16'),
(18, 'Cambio de Clave, casi listo... Falta capturar el evento submit del formulario para enviar y validar los datos ingresados.', 1, '2018-04-21 13:36:19'),
(19, 'Revisar la creación de nuevos usuarios: Falta validar el registro de nuevos usuarios. Listo: 23-05-18.', 1, '2018-05-23 19:22:07'),
(20, 'Cambiar la interfaz de Administración/Perfiles: Referencia ci_siae. Se cambió con referencia a la opción Por Hacer del perfil Administrador. Listo!', 1, '2018-04-22 13:54:53'),
(21, 'Cambiar la interfaz de Administración/Menús. 1) Cargar los perfiles en el combobox (select): Listo. 2) Codificar los botones de Nuevo Menú y demás.', 1, '2018-04-30 19:38:44'),
(22, 'Cambiar la interfaz de Administración/Menús. Codificación del botón eliminar: 27/04/2018. Listo!', 1, '2018-04-27 23:36:54'),
(23, 'Cambiar los reportes de calificaciones para los estudiantes para que también se vea el número de faltas injustificadas por asignatura.', 0, '2018-04-27 23:35:25'),
(24, 'Cambiar la interfaz de Administración/Menús. Codificación del botón Nuevo Menú: 27/04/2018. Listo!', 1, '2018-04-27 23:41:34'),
(25, 'Cambiar la interfaz de Administración/Menús. Codificación del botón Editar: 27/04/2018. Listo 29/04/2018.', 1, '2018-04-29 16:53:32'),
(26, 'Cambiar la interfaz de Administración/Menús. Codificación de los botones Subir y Bajar: Listo 29/04/2018.', 1, '2018-04-30 00:36:44'),
(27, 'Cambiar la interfaz de Administración/Menús. Codificación del botón Submenús: Inicio 30/04/2018. Listo 30/04/2018.', 1, '2018-04-30 19:38:28'),
(29, 'Revisar la administración de usuarios: Falta la codificación de Asociar Perfil, Asociar Usuario, Editar y Eliminar. Cambiar interface al estilo de Mensajes.', 0, '2018-05-30 11:27:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tema`
--

CREATE TABLE `sw_tema` (
  `id_tema` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `te_titulo` varchar(50) NOT NULL,
  `te_descripcion` text NOT NULL,
  `te_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) NOT NULL,
  `ta_descripcion` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_educacion`
--

CREATE TABLE `sw_tipo_educacion` (
  `id_tipo_educacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `te_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `te_bachillerato` int(11) NOT NULL,
  `te_orden` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `us_titulo` varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_apellidos` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_fullname` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_clave` varchar(64) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_alias` varchar(15) NOT NULL,
  `us_activo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_curso_cierre`
--
ALTER TABLE `sw_aporte_curso_cierre`
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD PRIMARY KEY (`id_aporte_evaluacion`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`);

--
-- Indices de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD PRIMARY KEY (`id_asignatura_curso`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD PRIMARY KEY (`id_asistencia_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_asignatura`,`id_paralelo`,`id_hora_clase`,`id_inasistencia`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_dia_semana` (`id_dia_semana`),
  ADD KEY `id_hora_clase` (`id_hora_clase`),
  ADD KEY `id_inasistencia` (`id_inasistencia`);

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `fk_curso_superior_periodo_lectivo_idx` (`id_periodo_lectivo`),
  ADD KEY `fk_curso_inferior_curso_idx` (`id_curso_inferior`),
  ADD KEY `fk_curso_superior_curso_idx` (`id_curso_superior`);

--
-- Indices de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  ADD KEY `id_paralelo` (`id_paralelo`,`id_estudiante`,`id_aporte_evaluacion`,`id_asignatura`);

--
-- Indices de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  ADD PRIMARY KEY (`id_club_docente`);

--
-- Indices de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Indices de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD PRIMARY KEY (`id_comportamiento_inspector`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_indice_evaluacion` (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`);

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  ADD PRIMARY KEY (`id_escala_proyectos`);

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`);

--
-- Indices de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD PRIMARY KEY (`id_estudiante_periodo_lectivo`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_periodo_lectivo`,`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  ADD PRIMARY KEY (`id_foro`);

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_hora_clase` (`id_hora_clase`);

--
-- Indices de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD PRIMARY KEY (`id_hora_clase`),
  ADD KEY `id_dia_semana` (`id_dia_semana`);

--
-- Indices de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  ADD PRIMARY KEY (`id_inasistencia`);

--
-- Indices de la tabla `sw_indice_evaluacion`
--
ALTER TABLE `sw_indice_evaluacion`
  ADD PRIMARY KEY (`id_indice_evaluacion`);

--
-- Indices de la tabla `sw_indice_evaluacion_def`
--
ALTER TABLE `sw_indice_evaluacion_def`
  ADD PRIMARY KEY (`id_indice_evaluacion`);

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `sw_mensaje`
--
ALTER TABLE `sw_mensaje`
  ADD PRIMARY KEY (`id_mensaje`);

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  ADD PRIMARY KEY (`id_paralelo_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`);

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`);

--
-- Indices de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  ADD PRIMARY KEY (`id_periodo_estado`);

--
-- Indices de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`),
  ADD UNIQUE KEY `pe_anio_inicio` (`pe_anio_inicio`),
  ADD UNIQUE KEY `pe_anio_fin` (`pe_anio_fin`),
  ADD KEY `id_institucion` (`id_institucion`);

--
-- Indices de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `fk_menus_idx` (`id_menu`),
  ADD KEY `fk_perfiles_idx` (`id_perfil`);

--
-- Indices de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  ADD PRIMARY KEY (`id_plan_rubrica`);

--
-- Indices de la tabla `sw_recomendaciones`
--
ALTER TABLE `sw_recomendaciones`
  ADD KEY `id_escala_calificaciones` (`id_escala_calificaciones`,`id_paralelo_asignatura`),
  ADD KEY `id_paralelo_asignatura` (`id_paralelo_asignatura`);

--
-- Indices de la tabla `sw_recomendaciones_quimestrales`
--
ALTER TABLE `sw_recomendaciones_quimestrales`
  ADD KEY `fk_sw_escala_calificaciones_idx` (`id_escala_calificaciones`),
  ADD KEY `fk_sw_paralelo_asignatura_idx` (`id_paralelo_asignatura`),
  ADD KEY `fk_sw_periodo_evaluacion_idx` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `fk_representante_estudiante_idx` (`id_estudiante`);

--
-- Indices de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  ADD PRIMARY KEY (`id_rubrica_club`);

--
-- Indices de la tabla `sw_rubrica_docente`
--
ALTER TABLE `sw_rubrica_docente`
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_docente` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD PRIMARY KEY (`id_rubrica_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD PRIMARY KEY (`id_rubrica_evaluacion`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`);

--
-- Indices de la tabla `sw_rubrica_evaluacion_club`
--
ALTER TABLE `sw_rubrica_evaluacion_club`
  ADD PRIMARY KEY (`id_rubrica_evaluacion_club`);

--
-- Indices de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  ADD PRIMARY KEY (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_proyecto`
--
ALTER TABLE `sw_rubrica_proyecto`
  ADD PRIMARY KEY (`id_rubrica_proyecto`),
  ADD KEY `fk_rubrica_proyecto_id_aporte_evaluacion_idx` (`id_aporte_evaluacion`);

--
-- Indices de la tabla `sw_submenu`
--
ALTER TABLE `sw_submenu`
  ADD PRIMARY KEY (`id_submenu`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indices de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  ADD PRIMARY KEY (`id_tema`),
  ADD KEY `id_foro` (`id_foro`);

--
-- Indices de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  ADD PRIMARY KEY (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  ADD PRIMARY KEY (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD PRIMARY KEY (`id_tipo_educacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  MODIFY `id_aporte_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  MODIFY `id_asistencia_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12644;

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  MODIFY `id_club_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  MODIFY `id_comportamiento_inspector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2124;

--
-- AUTO_INCREMENT de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  MODIFY `id_escala_proyectos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1692;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2471;

--
-- AUTO_INCREMENT de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  MODIFY `id_foro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=997;

--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  MODIFY `id_inasistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_indice_evaluacion`
--
ALTER TABLE `sw_indice_evaluacion`
  MODIFY `id_indice_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2549;

--
-- AUTO_INCREMENT de la tabla `sw_indice_evaluacion_def`
--
ALTER TABLE `sw_indice_evaluacion_def`
  MODIFY `id_indice_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_mensaje`
--
ALTER TABLE `sw_mensaje`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  MODIFY `id_paralelo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1186;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  MODIFY `id_periodo_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  MODIFY `id_periodo_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  MODIFY `id_plan_rubrica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  MODIFY `id_rubrica_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5612;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=616935;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion_club`
--
ALTER TABLE `sw_rubrica_evaluacion_club`
  MODIFY `id_rubrica_evaluacion_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  MODIFY `id_rubrica_personalizada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_proyecto`
--
ALTER TABLE `sw_rubrica_proyecto`
  MODIFY `id_rubrica_proyecto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_submenu`
--
ALTER TABLE `sw_submenu`
  MODIFY `id_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  MODIFY `id_tipo_aporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=593;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD CONSTRAINT `sw_hora_clase_ibfk_1` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
