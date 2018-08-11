-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 21-08-2015 a las 18:07:34
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
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE IF NOT EXISTS `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT,
  `cs_nombre` varchar(64) NOT NULL,
  PRIMARY KEY (`id_curso_superior`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Volcar la base de datos para la tabla `sw_curso_superior`
--

INSERT INTO `sw_curso_superior` (`id_curso_superior`, `cs_nombre`) VALUES
(2, 'NOVENO AÑO DE EDUCACION GENERAL BASICA'),
(3, 'DECIMO AÑO DE EDUCACION GENERAL BASICA'),
(4, 'PRIMER AÑO DE BACHILLERATO'),
(5, 'SEGUNDO AÑO DE BACHILLERATO'),
(6, 'TERCER AÑO DE BACHILLERATO');
