-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 21-08-2015 a las 14:47:05
-- Versión del servidor: 5.5.8
-- Versión de PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_proyectos`
--

CREATE TABLE IF NOT EXISTS `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL AUTO_INCREMENT,
  `ec_cualitativa` varchar(256) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) NOT NULL,
  PRIMARY KEY (`id_escala_proyectos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `sw_escala_proyectos`
--

INSERT INTO `sw_escala_proyectos` (`id_escala_proyectos`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`) VALUES
(1, 'Demuestra destacado desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un excelente aporte a su formación integral.', '9.00 - 10.00', 9, 10, 1, 'Excelente'),
(2, 'Demuestra muy buen desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un aporte a su formación integral.', '7.00 - 8.99', 7, 8.99, 2, 'Muy buena'),
(3, 'Demuestra buen desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye a su formación integral.', '4.01 - 6.99', 4.01, 6.99, 3, 'Buena'),
(4, 'Demuestra regular desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye escasamente a su formación integral.', '<= 4', 0.01, 4, 4, 'Regular'),
(5, 'Sin notas.', '0', 0, 0, 5, 'Sin notas');
