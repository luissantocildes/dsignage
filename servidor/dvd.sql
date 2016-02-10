-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 29-07-2015 a las 12:28:48
-- Versión del servidor: 5.5.44-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dvd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientePlaylist`
--

CREATE TABLE IF NOT EXISTS `clientePlaylist` (
  `idPlaylist` int(11) NOT NULL,
  `idCliente` varchar(32) NOT NULL,
  `start` datetime DEFAULT NULL,
  `stop` datetime DEFAULT NULL,
  PRIMARY KEY (`idPlaylist`,`idCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` varchar(32) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `ubicacion` text,
  `ultimaConexion` datetime DEFAULT NULL,
  `alta` datetime NOT NULL,
  `estado` int(11) NOT NULL,
  `comentario` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `ubicacion`, `ultimaConexion`, `alta`, `estado`, `comentario`) VALUES
('DVDS55b7fa0ddd867', 'Raspberry Prueba', 'MÃ¡laga', NULL, '2015-07-28 21:54:21', 1, 'Raspberry de prueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medios`
--

CREATE TABLE IF NOT EXISTS `medios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fichero` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `longitud` int(11) NOT NULL,
  `alta` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `tipo` int(11) NOT NULL,
  `comentario` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `medios`
--

INSERT INTO `medios` (`id`, `fichero`, `nombre`, `longitud`, `alta`, `activo`, `tipo`, `comentario`) VALUES
(8, 'xthresh.mp4', 'Empaquetadora de heno', 6429475, '2015-07-28 13:56:20', 1, 0, ''),
(9, 'suits-short.mkv', 'Serie', 27073838, '2015-07-28 14:05:37', 1, 0, ''),
(10, 'Wildlife.wmv', 'Naturaleza', 26246026, '2015-07-28 18:44:41', 1, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` tinytext NOT NULL,
  `alta` datetime NOT NULL,
  `activa` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playMedio`
--

CREATE TABLE IF NOT EXISTS `playMedio` (
  `idPlaylist` int(11) NOT NULL,
  `idMedio` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY (`idPlaylist`,`idMedio`),
  KEY `orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `permisos` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
