-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-03-2024 a las 19:06:30
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_agenda`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarPeriodos` ()   BEGIN
    -- Actualizar los nombres de los períodos incrementando el año en 1
    UPDATE Periodo
    SET nombre = REPLACE(nombre, YEAR(CURRENT_DATE()), YEAR(CURRENT_DATE()) + 1);

    -- Actualizar las fechas de inicio y terminación
    -- Ajustar las fechas según necesidad
    UPDATE Periodo
    SET fecha_inicio = DATE_ADD(fecha_inicio, INTERVAL 1 YEAR),
        fecha_terminacion = DATE_ADD(fecha_terminacion, INTERVAL 1 YEAR);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividadesacademicas`
--

CREATE TABLE `actividadesacademicas` (
  `ID_actividadesacademicas` int(11) NOT NULL,
  `usuariosID` int(11) DEFAULT NULL,
  `tiposactividadesID` int(11) DEFAULT NULL,
  `materiaID` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

CREATE TABLE `carrera` (
  `ID_carrera` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `perfil_carrera` varchar(50) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera`
--

INSERT INTO `carrera` (`ID_carrera`, `nombre`, `perfil_carrera`, `duracion`, `descripcion`) VALUES
(1, 'ITIC', 'a', '9', 'Ingeniero en sistemas y Comunicaciones'),
(2, 'ISIC', 'a', '9', 'Ingeniero en sistemas'),
(3, 'Turismo', 'a', '9', 'Viajes'),
(4, 'ITIC', 'a', '9', 'Ingeniero en sistemas y Comunicaciones'),
(5, 'ISIC', 'a', '9', 'Ingeniero en sistemas'),
(6, 'Turismo', 'a', '9', 'Viajes'),
(7, 'ITIC', 'a', '9', 'Ingeniero en sistemas y Comunicaciones'),
(8, 'ISIC', 'a', '9', 'Ingeniero en sistemas'),
(9, 'Turismo', 'a', '9', 'Viajes'),
(10, 'ITIC', 'a', '9', 'Ingeniero en sistemas y Comunicaciones'),
(11, 'ISIC', 'a', '9', 'Ingeniero en sistemas'),
(12, 'Turismo', 'a', '9', 'Viajes'),
(13, 'ITIC', 'a', '9', 'Ingeniero en sistemas y Comunicaciones'),
(14, 'ISIC', 'a', '9', 'Ingeniero en sistemas'),
(15, 'Turismo', 'a', '9', 'Viajes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera_materia`
--

CREATE TABLE `carrera_materia` (
  `ID_carrera_materia` int(11) NOT NULL,
  `carreraid` int(11) DEFAULT NULL,
  `materiaid` int(11) DEFAULT NULL,
  `es_reticula` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacionacademica_estudiante`
--

CREATE TABLE `informacionacademica_estudiante` (
  `ID_InformacionAcademica_estudiante` int(11) NOT NULL,
  `usuariosid` int(11) DEFAULT NULL,
  `periodoid` int(11) DEFAULT NULL,
  `carreraid` int(11) DEFAULT NULL,
  `numcontrol` varchar(50) DEFAULT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `promedio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informacionacademica_estudiante`
--

INSERT INTO `informacionacademica_estudiante` (`ID_InformacionAcademica_estudiante`, `usuariosid`, `periodoid`, `carreraid`, `numcontrol`, `semestre`, `promedio`) VALUES
(1, 2, 3, 4, '21390311', '5', 80.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacioncontacto`
--

CREATE TABLE `informacioncontacto` (
  `ID_infocontacto` int(11) NOT NULL,
  `usuariosid` int(11) DEFAULT NULL,
  `codigo_postal` varchar(5) DEFAULT NULL,
  `municipio` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `colonia` varchar(50) DEFAULT NULL,
  `calle_principal` varchar(50) DEFAULT NULL,
  `primer_cruzamiento` varchar(50) DEFAULT NULL,
  `segundo_cruzamiento` varchar(50) DEFAULT NULL,
  `referencias` varchar(50) DEFAULT NULL,
  `numero_exterior` varchar(50) DEFAULT NULL,
  `numero_interior` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informacioncontacto`
--

INSERT INTO `informacioncontacto` (`ID_infocontacto`, `usuariosid`, `codigo_postal`, `municipio`, `estado`, `ciudad`, `colonia`, `calle_principal`, `primer_cruzamiento`, `segundo_cruzamiento`, `referencias`, `numero_exterior`, `numero_interior`) VALUES
(1, 1, '12345', 'Ciudad de México', 'CDMX', 'CDMX', 'Colonia Ejemplo', 'Calle Principal', 'Cruzamiento 1', 'Cruzamiento 2', 'Referencias', '123', 'A'),
(2, 2, '54321', 'Guadalajara', 'Jalisco', 'Guadalajara', 'Colonia Ejemplo', 'Calle Principal', 'Cruzamiento 1', 'Cruzamiento 2', 'Referencias', '321', 'B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacionpersonal`
--

CREATE TABLE `informacionpersonal` (
  `ID_informacionpersonal` int(11) NOT NULL,
  `usuariosid` int(11) NOT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `primerapellido` varchar(50) DEFAULT NULL,
  `segundoapellido` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `RFC` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informacionpersonal`
--

INSERT INTO `informacionpersonal` (`ID_informacionpersonal`, `usuariosid`, `nombres`, `primerapellido`, `segundoapellido`, `fecha_nacimiento`, `telefono`, `email`, `RFC`) VALUES
(1, 1, 'Admin', '', '', '2023-01-01', '1234567890', 'admin@example.com', NULL),
(2, 2, 'Beto', 'Pasillas', '', '1995-01-01', '1234567890', 'beto@example.com', 'ABC123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `ID_login` int(11) NOT NULL,
  `usuariosid` int(11) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`ID_login`, `usuariosid`, `fecha_hora`) VALUES
(1, 1, '2023-12-13 21:02:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

CREATE TABLE `materia` (
  `ID_materia` int(11) NOT NULL,
  `periodoid` int(11) DEFAULT NULL,
  `carreraid` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia`
--

INSERT INTO `materia` (`ID_materia`, `periodoid`, `carreraid`, `nombre`) VALUES
(2, 3, 4, 'Base de datos distribuida'),
(3, 3, 5, 'Base de datos distribuida'),
(4, 3, 6, 'Cálculo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo`
--

CREATE TABLE `periodo` (
  `ID_periodo` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_terminacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodo`
--

INSERT INTO `periodo` (`ID_periodo`, `nombre`, `fecha_inicio`, `fecha_terminacion`) VALUES
(1, 'Enero-Junio 2023', '2023-01-30 00:00:00', '2023-06-09 00:00:00'),
(2, 'Verano 2023', '2023-06-26 00:00:00', '2023-08-04 00:00:00'),
(3, 'Agosto-Diciembre 2023', '2023-08-21 00:00:00', '2023-12-08 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposusuarios`
--

CREATE TABLE `tiposusuarios` (
  `ID_tiposusuarios` int(11) NOT NULL,
  `tipo` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposusuarios`
--

INSERT INTO `tiposusuarios` (`ID_tiposusuarios`, `tipo`) VALUES
(1, 'Estudiante'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_actividades`
--

CREATE TABLE `tipos_actividades` (
  `ID_tiposactividades` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_actividades`
--

INSERT INTO `tipos_actividades` (`ID_tiposactividades`, `nombre`) VALUES
(1, 'Tareas'),
(2, 'Proyectos'),
(3, 'Examenes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_usuarios` int(11) NOT NULL,
  `tiposusuariosid` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `contrasenas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_usuarios`, `tiposusuariosid`, `nombre`, `contrasenas`) VALUES
(1, 2, 'Admin', '$2y$10$ld/GfwiOuS0Qr1jFW8ZtGe3bkF9z5TBGstwMPZlJyxzX2ByAaEchK'),
(2, 1, 'BetoPasillas', '$2y$10$A0yKeg6zN0tpL5j24xJFcebV8ezVnkZdqLmJ3C1ikJKFwwyYj7ZtK');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividadesacademicas`
--
ALTER TABLE `actividadesacademicas`
  ADD PRIMARY KEY (`ID_actividadesacademicas`),
  ADD KEY `FK_ActividadesAcademicas_Tipos_actividades` (`tiposactividadesID`),
  ADD KEY `FK_informacionacademica_estudianteID` (`usuariosID`),
  ADD KEY `FK_informacionacademica_materiaID` (`materiaID`);

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`ID_carrera`);

--
-- Indices de la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  ADD PRIMARY KEY (`ID_carrera_materia`),
  ADD KEY `FK_carrera_materia_carrera` (`carreraid`),
  ADD KEY `FK_carrera_materia_materia` (`materiaid`);

--
-- Indices de la tabla `informacionacademica_estudiante`
--
ALTER TABLE `informacionacademica_estudiante`
  ADD PRIMARY KEY (`ID_InformacionAcademica_estudiante`),
  ADD KEY `FK_InformacionAcademica_estudiante_carrera` (`carreraid`),
  ADD KEY `FK_InformacionAcademica_estudiante_periodo` (`periodoid`),
  ADD KEY `FK_InformacionAcademica_estudiante_usuarios` (`usuariosid`);

--
-- Indices de la tabla `informacioncontacto`
--
ALTER TABLE `informacioncontacto`
  ADD PRIMARY KEY (`ID_infocontacto`),
  ADD KEY `FK_InformacionContacto_usuarios` (`usuariosid`);

--
-- Indices de la tabla `informacionpersonal`
--
ALTER TABLE `informacionpersonal`
  ADD PRIMARY KEY (`ID_informacionpersonal`),
  ADD KEY `FK_InformacionPersonal_usuarios` (`usuariosid`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`ID_login`),
  ADD KEY `FK_Login_usuarios` (`usuariosid`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`ID_materia`),
  ADD KEY `FK_Materia_carrera` (`carreraid`),
  ADD KEY `FK_Materia_periodo` (`periodoid`);

--
-- Indices de la tabla `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`ID_periodo`);

--
-- Indices de la tabla `tiposusuarios`
--
ALTER TABLE `tiposusuarios`
  ADD PRIMARY KEY (`ID_tiposusuarios`);

--
-- Indices de la tabla `tipos_actividades`
--
ALTER TABLE `tipos_actividades`
  ADD PRIMARY KEY (`ID_tiposactividades`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_usuarios`),
  ADD KEY `FK_Usuarios_tiposusuarios` (`tiposusuariosid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividadesacademicas`
--
ALTER TABLE `actividadesacademicas`
  MODIFY `ID_actividadesacademicas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `ID_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  MODIFY `ID_carrera_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `informacionacademica_estudiante`
--
ALTER TABLE `informacionacademica_estudiante`
  MODIFY `ID_InformacionAcademica_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `informacioncontacto`
--
ALTER TABLE `informacioncontacto`
  MODIFY `ID_infocontacto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `informacionpersonal`
--
ALTER TABLE `informacionpersonal`
  MODIFY `ID_informacionpersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `ID_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `ID_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `periodo`
--
ALTER TABLE `periodo`
  MODIFY `ID_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposusuarios`
--
ALTER TABLE `tiposusuarios`
  MODIFY `ID_tiposusuarios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_actividades`
--
ALTER TABLE `tipos_actividades`
  MODIFY `ID_tiposactividades` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_usuarios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividadesacademicas`
--
ALTER TABLE `actividadesacademicas`
  ADD CONSTRAINT `FK_ActividadesAcademicas_Tipos_actividades` FOREIGN KEY (`tiposactividadesID`) REFERENCES `tipos_actividades` (`ID_tiposactividades`),
  ADD CONSTRAINT `FK_informacionacademica_estudianteID` FOREIGN KEY (`usuariosID`) REFERENCES `usuarios` (`ID_usuarios`),
  ADD CONSTRAINT `FK_informacionacademica_materiaID` FOREIGN KEY (`materiaID`) REFERENCES `materia` (`ID_materia`);

--
-- Filtros para la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  ADD CONSTRAINT `FK_carrera_materia_carrera` FOREIGN KEY (`carreraid`) REFERENCES `carrera` (`ID_carrera`),
  ADD CONSTRAINT `FK_carrera_materia_materia` FOREIGN KEY (`materiaid`) REFERENCES `materia` (`ID_materia`);

--
-- Filtros para la tabla `informacionacademica_estudiante`
--
ALTER TABLE `informacionacademica_estudiante`
  ADD CONSTRAINT `FK_InformacionAcademica_estudiante_carrera` FOREIGN KEY (`carreraid`) REFERENCES `carrera` (`ID_carrera`),
  ADD CONSTRAINT `FK_InformacionAcademica_estudiante_periodo` FOREIGN KEY (`periodoid`) REFERENCES `periodo` (`ID_periodo`),
  ADD CONSTRAINT `FK_InformacionAcademica_estudiante_usuarios` FOREIGN KEY (`usuariosid`) REFERENCES `usuarios` (`ID_usuarios`);

--
-- Filtros para la tabla `informacioncontacto`
--
ALTER TABLE `informacioncontacto`
  ADD CONSTRAINT `FK_InformacionContacto_usuarios` FOREIGN KEY (`usuariosid`) REFERENCES `usuarios` (`ID_usuarios`);

--
-- Filtros para la tabla `informacionpersonal`
--
ALTER TABLE `informacionpersonal`
  ADD CONSTRAINT `FK_InformacionPersonal_usuarios` FOREIGN KEY (`usuariosid`) REFERENCES `usuarios` (`ID_usuarios`);

--
-- Filtros para la tabla `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `FK_Login_usuarios` FOREIGN KEY (`usuariosid`) REFERENCES `usuarios` (`ID_usuarios`);

--
-- Filtros para la tabla `materia`
--
ALTER TABLE `materia`
  ADD CONSTRAINT `FK_Materia_carrera` FOREIGN KEY (`carreraid`) REFERENCES `carrera` (`ID_carrera`),
  ADD CONSTRAINT `FK_Materia_periodo` FOREIGN KEY (`periodoid`) REFERENCES `periodo` (`ID_periodo`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `FK_Usuarios_tiposusuarios` FOREIGN KEY (`tiposusuariosid`) REFERENCES `tiposusuarios` (`ID_tiposusuarios`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
