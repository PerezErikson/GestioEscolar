-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2026 a las 22:05:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pozo_de_bejuco`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `fecha_nacimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anio_escolar`
--

CREATE TABLE `anio_escolar` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anio_escolar`
--

INSERT INTO `anio_escolar` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`, `fecha_registro`) VALUES
(1, '2025-2026', '2025-01-10', '2026-08-12', 'Activo', '2026-06-03 16:22:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_materias`
--

CREATE TABLE `asignacion_materias` (
  `id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_materias`
--

INSERT INTO `asignacion_materias` (`id`, `grado_id`, `materia_id`, `fecha_registro`) VALUES
(1, 3, 15, '2026-06-03 16:28:01'),
(2, 3, 16, '2026-06-03 16:28:01'),
(3, 3, 17, '2026-06-03 16:28:01'),
(4, 3, 18, '2026-06-03 16:28:01'),
(5, 3, 19, '2026-06-03 16:28:01'),
(6, 3, 14, '2026-06-03 16:28:01'),
(7, 3, 13, '2026-06-03 16:28:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Presente','Ausente','Excusa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `estudiante_id`, `grado_id`, `fecha`, `estado`) VALUES
(15, 19, 5, '2026-05-30', 'Presente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `anio_id` int(11) NOT NULL,
  `competencia_id` int(11) NOT NULL,
  `p1` decimal(5,2) NOT NULL DEFAULT 0.00,
  `p2` decimal(5,2) NOT NULL DEFAULT 0.00,
  `p3` decimal(5,2) NOT NULL DEFAULT 0.00,
  `p4` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nota_final` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `estudiante_id`, `grado_id`, `materia_id`, `anio_id`, `competencia_id`, `p1`, `p2`, `p3`, `p4`, `nota_final`, `fecha_registro`) VALUES
(1, 19, 3, 15, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(2, 19, 3, 16, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(3, 19, 3, 17, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(4, 19, 3, 18, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(5, 19, 3, 19, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(6, 19, 3, 14, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(7, 19, 3, 13, 1, 1, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:28:41'),
(8, 19, 3, 15, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(9, 19, 3, 16, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(10, 19, 3, 17, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(11, 19, 3, 18, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(12, 19, 3, 19, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(13, 19, 3, 14, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(14, 19, 3, 13, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:16'),
(15, 19, 3, 15, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(16, 19, 3, 16, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(17, 19, 3, 17, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(18, 19, 3, 18, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(19, 19, 3, 19, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(20, 19, 3, 14, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45'),
(21, 19, 3, 13, 1, 2, 100.00, 100.00, 100.00, 100.00, 100.00, '2026-06-03 16:29:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencias`
--

CREATE TABLE `competencias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `competencias`
--

INSERT INTO `competencias` (`id`, `nombre`, `descripcion`, `estado`, `fecha_registro`) VALUES
(1, 'Comunicativa', 'Comunicativa', 'Activo', '2026-06-03 16:25:40'),
(2, 'Pensamiento Lógico, Creativo y Crítico; Resolución de Problemas; Científica y Tecnológica', 'Pensamiento Lógico, Creativo y\r\nCrítico; Resolución de Problemas;\r\nCientífica y Tecnológica', 'Activo', '2026-06-03 16:25:49'),
(3, 'Ética y Ciudadana; Desarrollo Personal y Espiritual;  Ambiental y de la Salud', 'Ética y Ciudadana; Desarrollo\r\nPersonal y Espiritual;\r\n Ambiental y de la Salud', 'Activo', '2026-06-03 16:25:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comportamiento`
--

CREATE TABLE `comportamiento` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `nota` varchar(255) NOT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comportamiento`
--

INSERT INTO `comportamiento` (`id`, `estudiante_id`, `grado_id`, `fecha`, `nota`, `observacion`) VALUES
(16, 19, 5, '2026-05-30', 'Excelente', 'su comportamiento fue excelente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre_centro` varchar(150) NOT NULL,
  `codigo_centro` varchar(50) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `distrito` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `fecha_creacion` date DEFAULT curdate(),
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre_centro`, `codigo_centro`, `direccion`, `distrito`, `telefono`, `correo`, `director`, `fecha_creacion`, `logo`) VALUES
(9, 'Pozo De Bejuco', '01815', 'Pueblo Viejo, La Vega, Rep. Dom.', '06-05', '829 207-4684', 'administrador@gmail.com', 'MIRURJIA ROSARIO ABREU', '2026-06-04', '1780547644_6a21003c8be31.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estado_civil` varchar(50) NOT NULL,
  `anos_servicio` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo','Vacaciones','Despedido') NOT NULL DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente`
--

INSERT INTO `docente` (`id`, `nombre`, `apellido`, `correo`, `cedula`, `fecha_nacimiento`, `titulo`, `direccion`, `telefono`, `estado_civil`, `anos_servicio`, `estado`, `fecha_registro`) VALUES
(6, 'Pamela', 'Acevedo', 'pameela@gmail.com', '047-0000000-0', '1999-01-05', 'Licenciada  en segundo ciclo de primaria', 'Moca', '8090000009', 'Soltero', 3, 'Activo', '2026-05-29 19:29:01'),
(7, 'Erikson Antonio', 'Perez Rosario', 'erikson1@gmail.com', '047-0000000-7', '2000-09-19', 'Licenciada  en segundo ciclo de primaria', 'Pueblo Viejo La Vega', '8090000009', 'Soltero', 3, 'Activo', '2026-05-29 19:33:36'),
(8, 'Pamela', 'Acevedo', 'administrador1@gmail.com', '402-3455557-7', '1999-02-10', 'Licenciada  en segundo ciclo de primaria', 'Moca', '8090000001', 'Soltero', 4, 'Vacaciones', '2026-05-30 02:57:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_estudiante`
--

CREATE TABLE `estado_estudiante` (
  `id` int(11) NOT NULL,
  `estudiante_numero` int(11) DEFAULT NULL,
  `estado` enum('Promovido','Reprobado','Aplazado','Abandono') DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_estudiante`
--

INSERT INTO `estado_estudiante` (`id`, `estudiante_numero`, `estado`, `fecha`, `observaciones`) VALUES
(17, 19, 'Promovido', '2026-05-29', NULL),
(18, 19, 'Reprobado', '2026-05-29', NULL),
(19, 19, 'Reprobado', '2026-05-29', NULL),
(20, 19, 'Reprobado', '2026-05-29', NULL),
(21, 19, 'Reprobado', '2026-05-29', NULL),
(22, 19, 'Reprobado', '2026-05-29', NULL),
(23, 19, 'Reprobado', '2026-05-29', NULL),
(24, 19, 'Reprobado', '2026-05-29', NULL),
(25, 19, 'Promovido', '2026-05-29', NULL),
(26, 19, 'Promovido', '2026-05-29', NULL),
(27, 19, 'Promovido', '2026-05-29', NULL),
(28, 19, 'Promovido', '2026-05-29', NULL),
(29, 19, 'Promovido', '2026-05-29', NULL),
(30, 19, 'Reprobado', '2026-05-29', NULL),
(31, 19, 'Promovido', '2026-05-29', NULL),
(32, 19, 'Promovido', '2026-05-29', NULL),
(33, 19, 'Reprobado', '2026-05-29', NULL),
(34, 19, 'Promovido', '2026-05-29', NULL),
(35, 19, 'Abandono', '2026-05-29', NULL),
(36, 19, 'Reprobado', '2026-06-03', NULL),
(37, 20, 'Promovido', '2026-06-03', NULL),
(38, 19, 'Promovido', '2026-06-03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `numero` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `ID` varchar(20) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `grado_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `responsable_id` int(11) NOT NULL,
  `estado` enum('Activo','Aplazado','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`numero`, `nombre`, `apellido`, `ID`, `correo`, `fecha_nacimiento`, `direccion`, `telefono`, `grado_id`, `nivel_id`, `fecha_registro`, `responsable_id`, `estado`) VALUES
(19, 'Eridania Mercedes', 'Muñoz Rosario', '1092091', 'eridania@gmail.com', '2009-06-09', 'Pueblo Viejo La Vega', '8098972083', 3, 3, '2026-05-30 02:56:37', 45, 'Activo'),
(20, 'Cristia', 'Rosario Abreu', '109209', 'cristian@gmail.com', '2010-08-18', 'Pueblo Viejo La Vega', '8098972083', 3, 3, '2026-06-03 16:33:11', 46, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE TABLE `grados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados1`
--

CREATE TABLE `grados1` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_seccion` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados1`
--

INSERT INTO `grados1` (`id`, `nombre`, `id_seccion`, `id_nivel`) VALUES
(3, 'Primero', 2, 3),
(5, 'Segundo', 2, 3),
(6, 'Tercero', 2, 3),
(7, 'Cuarto', 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `id_nivel`) VALUES
(13, 'Matemática', 3),
(14, 'Lengua Española', 3),
(15, 'Ciencias Naturales', 3),
(16, 'Ciencias Sociales', 3),
(17, 'Educación Artística', 3),
(18, 'Educación Física', 3),
(19, 'Formación Integral Humana y Religiosa', 3),
(22, 'Inglés', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `oculto_emisor` tinyint(1) NOT NULL DEFAULT 0,
  `oculto_receptor` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `emisor_id`, `receptor_id`, `mensaje`, `leido`, `oculto_emisor`, `oculto_receptor`, `fecha`) VALUES
(1, 5, 6, 'hola', 1, 0, 0, '2026-06-07 16:02:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id`, `nombre`) VALUES
(3, 'Primer Ciclo'),
(4, 'Segundo Ciclo'),
(7, 'primero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responsables`
--

CREATE TABLE `responsables` (
  `id` int(11) NOT NULL,
  `tipo` enum('Padre','Madre','Tutor') NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_responsable` varchar(30) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `tipo_responsable` varchar(20) NOT NULL,
  `nacionalidad` varchar(100) NOT NULL,
  `parentesco` varchar(100) NOT NULL,
  `estado_civil` varchar(50) NOT NULL,
  `nivel_academico` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `responsables`
--

INSERT INTO `responsables` (`id`, `tipo`, `nombre`, `id_responsable`, `telefono`, `direccion`, `tipo_responsable`, `nacionalidad`, `parentesco`, `estado_civil`, `nivel_academico`, `fecha_registro`) VALUES
(45, 'Padre', 'Erikson Antonio', '047-0000000-0', '8098972083', 'Pueblo Viejo La Vega', 'Padre', 'Dominicano', 'Padre', 'Casado', 'Universitario', '2026-05-30 02:55:53'),
(46, 'Madre', 'Mirurjia Carmela Rosario Abreu', '047-0000000-6', '8098972083', 'Pueblo Viejo La Vega', 'Madre', 'Dominicano', 'Madre', 'Casado', 'Universitario', '2026-06-03 16:32:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Docente'),
(3, 'Estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `nombre`, `descripcion`) VALUES
(2, 'A', 'Matutina'),
(3, 'B', 'Matutina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contraseña`, `rol_id`) VALUES
(5, 'admin', 'admin@admin.com', '123456', 1),
(6, 'docente', 'docente@escuela.com', '123456', 2),
(7, 'estudiante', 'estudiante@escuela.com', '123456', 3),
(20, 'Eridania Mercedes Muñoz Rosario', 'eridania@gmail.com', 'QFlzcmfP', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `anio_escolar`
--
ALTER TABLE `anio_escolar`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asignacion_materias`
--
ALTER TABLE `asignacion_materias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_asignacion` (`grado_id`,`materia_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `grado_id` (`grado_id`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comportamiento`
--
ALTER TABLE `comportamiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `grado_id` (`grado_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `estado_estudiante`
--
ALTER TABLE `estado_estudiante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_numero` (`estudiante_numero`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`numero`),
  ADD UNIQUE KEY `cedula` (`ID`),
  ADD KEY `nivel_id` (`nivel_id`),
  ADD KEY `fk_grado_estudiante` (`grado_id`),
  ADD KEY `fk_responsable_estudiante` (`responsable_id`);

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grados1`
--
ALTER TABLE `grados1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_seccion` (`id_seccion`),
  ADD KEY `id_nivel` (`id_nivel`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nivel` (`id_nivel`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mensajes_receptor` (`receptor_id`),
  ADD KEY `idx_conversacion` (`emisor_id`,`receptor_id`,`fecha`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`id_responsable`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `anio_escolar`
--
ALTER TABLE `anio_escolar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `asignacion_materias`
--
ALTER TABLE `asignacion_materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `competencias`
--
ALTER TABLE `competencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comportamiento`
--
ALTER TABLE `comportamiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `estado_estudiante`
--
ALTER TABLE `estado_estudiante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `numero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grados1`
--
ALTER TABLE `grados1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `responsables`
--
ALTER TABLE `responsables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_materias`
--
ALTER TABLE `asignacion_materias`
  ADD CONSTRAINT `asignacion_materias_ibfk_1` FOREIGN KEY (`grado_id`) REFERENCES `grados1` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_materias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`numero`),
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados1` (`id`);

--
-- Filtros para la tabla `comportamiento`
--
ALTER TABLE `comportamiento`
  ADD CONSTRAINT `comportamiento_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`numero`),
  ADD CONSTRAINT `comportamiento_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados1` (`id`);

--
-- Filtros para la tabla `estado_estudiante`
--
ALTER TABLE `estado_estudiante`
  ADD CONSTRAINT `estado_estudiante_ibfk_1` FOREIGN KEY (`estudiante_numero`) REFERENCES `estudiantes` (`numero`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_grado_estudiante` FOREIGN KEY (`grado_id`) REFERENCES `grados1` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_responsable_estudiante` FOREIGN KEY (`responsable_id`) REFERENCES `responsables` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grados1`
--
ALTER TABLE `grados1`
  ADD CONSTRAINT `grados1_ibfk_1` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id`),
  ADD CONSTRAINT `grados1_ibfk_2` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_mensajes_emisor` FOREIGN KEY (`emisor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mensajes_receptor` FOREIGN KEY (`receptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
