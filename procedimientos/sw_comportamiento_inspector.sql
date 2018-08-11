-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-10-2017 a las 19:27:50
-- Versión del servidor: 5.6.36-cll-lve
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_inspector`
--

CREATE TABLE `sw_comportamiento_inspector` (
  `id_comportamiento_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_indice_evaluacion` int(11) NOT NULL,
  `co_calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD PRIMARY KEY (`id_comportamiento_inspector`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_indice_evaluacion` (`id_indice_evaluacion`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD CONSTRAINT `sw_comportamiento_inspector_ibfk_1` FOREIGN KEY (`id_indice_evaluacion`) REFERENCES `sw_indice_evaluacion_def` (`id_indice_evaluacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
