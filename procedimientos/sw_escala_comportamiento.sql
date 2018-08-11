-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-10-2017 a las 00:20:20
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
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

DROP TABLE IF EXISTS `sw_escala_comportamiento`;
CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_escala_comportamiento`
--

INSERT INTO `sw_escala_comportamiento` (`id_escala_comportamiento`, `ec_relacion`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_equivalencia`) VALUES
(1, 'A = muy satisfactorio', 'Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.', '9 - 10', 9, 10, 'A'),
(2, 'B = satisfactorio', 'Cumple con los compromisos establecidos para la sana convivencia social.', '7 - 8.99', 7, 8.99, 'B'),
(3, 'C = poco satisfactorio', 'Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '6 - 6.99', 6, 6.99, 'C'),
(4, 'D = mejorable', 'Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '4 - 5.99', 4, 5.99, 'D'),
(5, 'E = insatisfactorio', 'No cumple con los compromisos establecidos para la sana convivencia social.', '< 4', 0.01, 3.99, 'E'),
(6, 'S/N = sin notas', 'Sin notas.', '0', 0, 0, 'S/N');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
