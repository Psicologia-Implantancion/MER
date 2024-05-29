-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2024 a las 23:55:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `emocionvital`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `ID_Admi` int(11) NOT NULL,
  `Primer Nombre` varchar(80) NOT NULL,
  `Segundo Nombre` varchar(80) NOT NULL,
  `Primer Apellido` varchar(80) NOT NULL,
  `Segundo Apellido` varchar(80) NOT NULL,
  `Cedula` int(11) NOT NULL,
  `Teléfono` varchar(11) NOT NULL,
  `Correo` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`ID_Admi`, `Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Cedula`, `Teléfono`, `Correo`) VALUES
(1, 'acsd', 'adasd', 'asdasd', 'adsd', 28372278, '04260649763', 'leximar@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

CREATE TABLE `cita` (
  `ID_Cita` int(11) NOT NULL,
  `Id_TipoCita` int(11) NOT NULL,
  `ID_Paciente` int(11) NOT NULL,
  `ID_Psicologa` int(11) NOT NULL,
  `ID_CitaMenor` int(11) NOT NULL,
  `ID_Pareja` int(11) NOT NULL,
  `Id_Fecha` int(11) NOT NULL,
  `ID_Login` int(11) NOT NULL,
  `Monto` float NOT NULL,
  `Hora` time NOT NULL,
  `Hora_Fin` time NOT NULL,
  `Status` enum('Espera','Realizada','Suspendida','Agendada','Reagendada') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cita`
--

INSERT INTO `cita` (`ID_Cita`, `Id_TipoCita`, `ID_Paciente`, `ID_Psicologa`, `ID_CitaMenor`, `ID_Pareja`, `Id_Fecha`, `ID_Login`, `Monto`, `Hora`, `Hora_Fin`, `Status`) VALUES
(1, 1, 1, 1, 0, 0, 1, 1, 15, '08:30:00', '09:15:00', 'Espera');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citamenor`
--

CREATE TABLE `citamenor` (
  `ID_CitaMenor` int(11) NOT NULL,
  `1erNombreInfante` varchar(80) NOT NULL,
  `2doNombreInfante` varchar(80) NOT NULL,
  `1erApellidoInfante` varchar(80) NOT NULL,
  `2doApellidoInfante` varchar(80) NOT NULL,
  `fecha nacimiento` date NOT NULL,
  `status` enum('ACTIVO','INACTIVO','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citamenor`
--

INSERT INTO `citamenor` (`ID_CitaMenor`, `1erNombreInfante`, `2doNombreInfante`, `1erApellidoInfante`, `2doApellidoInfante`, `fecha nacimiento`, `status`) VALUES
(0, 'ds', 'asds', 'adsd', 'das', '2024-05-08', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita_pareja`
--

CREATE TABLE `cita_pareja` (
  `ID_Pareja` int(11) NOT NULL,
  `Primer Nombre1` varchar(250) NOT NULL,
  `Segundo Nombre1` varchar(250) NOT NULL,
  `Primer Apellido1` varchar(250) NOT NULL,
  `Segundo Apellido1` varchar(250) NOT NULL,
  `Tipo_Cedula` enum('V','E','J','P') NOT NULL,
  `Cedula1` int(11) NOT NULL,
  `Telefono1` varchar(15) NOT NULL,
  `Fecha_Nacimiento1` date NOT NULL,
  `Correo1` varchar(90) NOT NULL,
  `ID_Direccion1` int(11) NOT NULL,
  `Status` enum('Activo','Inactivo','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cita_pareja`
--

INSERT INTO `cita_pareja` (`ID_Pareja`, `Primer Nombre1`, `Segundo Nombre1`, `Primer Apellido1`, `Segundo Apellido1`, `Tipo_Cedula`, `Cedula1`, `Telefono1`, `Fecha_Nacimiento1`, `Correo1`, `ID_Direccion1`, `Status`) VALUES
(0, 'asd', 'das', 'asd', 'sd', 'V', 28372278, '04260649763', '2024-05-01', 'asds@gmail.com', 1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `ID_Direccion` int(11) NOT NULL,
  `ID_Estados` int(11) NOT NULL,
  `ID_Municipio` int(11) NOT NULL,
  `Descripcion` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direccion`
--

INSERT INTO `direccion` (`ID_Direccion`, `ID_Estados`, `ID_Municipio`, `Descripcion`) VALUES
(1, 2, 29, 'zds');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `iso_3166-2` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `estado`, `iso_3166-2`) VALUES
(1, 'Amazonas', 'VE-X'),
(2, 'Anzoátegui', 'VE-B'),
(3, 'Apure', 'VE-C'),
(4, 'Aragua', 'VE-D'),
(5, 'Barinas', 'VE-E'),
(6, 'Bolívar', 'VE-F'),
(7, 'Carabobo', 'VE-G'),
(8, 'Cojedes', 'VE-H'),
(9, 'Delta Amacuro', 'VE-Y'),
(10, 'Falcón', 'VE-I'),
(11, 'Guárico', 'VE-J'),
(12, 'Lara', 'VE-K'),
(13, 'Mérida', 'VE-L'),
(14, 'Miranda', 'VE-M'),
(15, 'Monagas', 'VE-N'),
(16, 'Nueva Esparta', 'VE-O'),
(17, 'Portuguesa', 'VE-P'),
(18, 'Sucre', 'VE-R'),
(19, 'Táchira', 'VE-S'),
(20, 'Trujillo', 'VE-T'),
(21, 'La Guaira', 'VE-W'),
(22, 'Yaracuy', 'VE-U'),
(23, 'Zulia', 'VE-V'),
(24, 'Distrito Capital', 'VE-A'),
(25, 'Dependencias Federales', 'VE-Z');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factoresfamiliares`
--

CREATE TABLE `factoresfamiliares` (
  `ID_Familiares` int(11) NOT NULL,
  `Nombre_Madre` varchar(80) NOT NULL,
  `Apellido_Madre` varchar(80) NOT NULL,
  `Nombre_Padre` varchar(80) NOT NULL,
  `Apellido_Padre` varchar(80) NOT NULL,
  `Salus_Fisica_Madre` varchar(80) NOT NULL,
  `Salus_Fisica_Padre` varchar(80) NOT NULL,
  `Nivel_Educativo_Madre` varchar(80) NOT NULL,
  `Nivel_Educativo_Padre` varchar(80) NOT NULL,
  `Trabajo_Actual_Madre` varchar(80) NOT NULL,
  `Trabajo_Actual_Padre` varchar(80) NOT NULL,
  `Horario_Trabajo_Madre` varchar(80) NOT NULL,
  `Horario_Trabajo_Padre` varchar(80) NOT NULL,
  `Habitos_De_la_Madre` varchar(250) NOT NULL,
  `Habitos_De_la_Padre` varchar(250) NOT NULL,
  `Perdida_Algun_Familiar` varchar(20) NOT NULL,
  `Quien_Era` varchar(20) NOT NULL,
  `Como_fue` varchar(250) NOT NULL,
  `Edad_Que_Tenia_Infante` varchar(20) NOT NULL,
  `Presencio_Suceso` varchar(20) NOT NULL,
  `Reaccion_Del_Infante` varchar(250) NOT NULL,
  `Accidentes_Infante` varchar(250) NOT NULL,
  `Castigos_Graves` varchar(250) NOT NULL,
  `De_Parte_Quien` varchar(50) NOT NULL,
  `Edad_Infante` int(11) NOT NULL,
  `Problemas_Infante` varchar(250) NOT NULL,
  `Problemas_Fisicos` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factoresfamiliares`
--

INSERT INTO `factoresfamiliares` (`ID_Familiares`, `Nombre_Madre`, `Apellido_Madre`, `Nombre_Padre`, `Apellido_Padre`, `Salus_Fisica_Madre`, `Salus_Fisica_Padre`, `Nivel_Educativo_Madre`, `Nivel_Educativo_Padre`, `Trabajo_Actual_Madre`, `Trabajo_Actual_Padre`, `Horario_Trabajo_Madre`, `Horario_Trabajo_Padre`, `Habitos_De_la_Madre`, `Habitos_De_la_Padre`, `Perdida_Algun_Familiar`, `Quien_Era`, `Como_fue`, `Edad_Que_Tenia_Infante`, `Presencio_Suceso`, `Reaccion_Del_Infante`, `Accidentes_Infante`, `Castigos_Graves`, `De_Parte_Quien`, `Edad_Infante`, `Problemas_Infante`, `Problemas_Fisicos`) VALUES
(1, 'cmñcm', 'sslakdaslk', 'sdlksadks', 'askadm', 'aDSDSAK', 'KDSKLADMK', 'skmflkds', 'mldksmcsdl', 'askmlksa', 'amslkal', 'klmlk', 'kbkjnkj', 'nkhjhhj', 'jhhjj', 'kkslfds', 'dsdas', 'adsd', 'aSAs', 'asdsd', 'sdsads', 'sadsdsa', 'asdsdsa', 'sadsdsa', 9, 'sad', 'sadsa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factoresfisicos`
--

CREATE TABLE `factoresfisicos` (
  `ID_Fisico` int(11) NOT NULL,
  `Desarollo_prenata_natal` varchar(80) NOT NULL,
  `Desarrollo_Primera_Infancia` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factoresfisicos`
--

INSERT INTO `factoresfisicos` (`ID_Fisico`, `Desarollo_prenata_natal`, `Desarrollo_Primera_Infancia`) VALUES
(1, 'admslkadms', 'ñadsdmsadl');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factores_hereditarios`
--

CREATE TABLE `factores_hereditarios` (
  `ID_Hereditario` int(250) NOT NULL,
  `Incidencias_Anomalias` varchar(250) NOT NULL,
  `Tratamientp_Medico` varchar(250) NOT NULL,
  `Alcoholismo` varchar(250) NOT NULL,
  `Abuso_Drogas` varchar(250) NOT NULL,
  `Debilidad_Mental` varchar(250) NOT NULL,
  `Convulciones_Desmayos_Temblores` varchar(250) NOT NULL,
  `ETS` varchar(250) NOT NULL,
  `Suicido` varchar(250) NOT NULL,
  `Anormalidades` varchar(250) NOT NULL,
  `Trastorno_Habla` varchar(250) NOT NULL,
  `Trastorno_Vista` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factores_hereditarios`
--

INSERT INTO `factores_hereditarios` (`ID_Hereditario`, `Incidencias_Anomalias`, `Tratamientp_Medico`, `Alcoholismo`, `Abuso_Drogas`, `Debilidad_Mental`, `Convulciones_Desmayos_Temblores`, `ETS`, `Suicido`, `Anormalidades`, `Trastorno_Habla`, `Trastorno_Vista`) VALUES
(1, 'ññlxasdas', 'asdsdasd', 'sdadsd', 'asdsdsad', 'sadsad', 'asdsd', 'asdsdsad', 'asdsasd', 'sadsads', 'sadsd', 'sdsad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factores_personalidad_conducta`
--

CREATE TABLE `factores_personalidad_conducta` (
  `ID_Personalidad_Conducta` int(11) NOT NULL,
  `Comida` varchar(250) NOT NULL,
  `Sueno` varchar(250) NOT NULL,
  `Eliminaciones` varchar(250) NOT NULL,
  `Manias_Tics` varchar(250) NOT NULL,
  `Historia_Sexual` varchar(250) NOT NULL,
  `Timido` tinyint(1) NOT NULL,
  `Agresivo` tinyint(1) NOT NULL,
  `Tranquilo` tinyint(1) NOT NULL,
  `Irritable` tinyint(1) NOT NULL,
  `Alegre` tinyint(1) NOT NULL,
  `Triste` tinyint(1) NOT NULL,
  `Cooperador` tinyint(1) NOT NULL,
  `Negatividad` tinyint(1) NOT NULL,
  `Sereno` tinyint(1) NOT NULL,
  `Impulsivo` tinyint(1) NOT NULL,
  `Confiado_en_si` tinyint(1) NOT NULL,
  `Frio` tinyint(1) NOT NULL,
  `Sociable` tinyint(1) NOT NULL,
  `Retardado` tinyint(1) NOT NULL,
  `Equilibrado` tinyint(1) NOT NULL,
  `Nervioso` tinyint(1) NOT NULL,
  `Cariñoso` tinyint(1) NOT NULL,
  `Inseguro` tinyint(1) NOT NULL,
  `Juega` tinyint(1) NOT NULL,
  `No_juega` tinyint(1) NOT NULL,
  `Controlado` tinyint(1) NOT NULL,
  `Emotivo` tinyint(1) NOT NULL,
  `Seguro` tinyint(1) NOT NULL,
  `Amable` tinyint(1) NOT NULL,
  `Desconsiderado` tinyint(1) NOT NULL,
  `Laborioso` tinyint(1) NOT NULL,
  `Perezoso` tinyint(1) NOT NULL,
  `Desconfiado` tinyint(1) NOT NULL,
  `Dominante` tinyint(1) NOT NULL,
  `Sumiso` tinyint(1) NOT NULL,
  `Disciplinado` tinyint(1) NOT NULL,
  `Indisciplinado` tinyint(1) NOT NULL,
  `Rebelde` tinyint(1) NOT NULL,
  `Obediente` tinyint(1) NOT NULL,
  `Ordenado` tinyint(1) NOT NULL,
  `Desordenado` tinyint(1) NOT NULL,
  `Tendencias_Destructivas` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factores_personalidad_conducta`
--

INSERT INTO `factores_personalidad_conducta` (`ID_Personalidad_Conducta`, `Comida`, `Sueno`, `Eliminaciones`, `Manias_Tics`, `Historia_Sexual`, `Timido`, `Agresivo`, `Tranquilo`, `Irritable`, `Alegre`, `Triste`, `Cooperador`, `Negatividad`, `Sereno`, `Impulsivo`, `Confiado_en_si`, `Frio`, `Sociable`, `Retardado`, `Equilibrado`, `Nervioso`, `Cariñoso`, `Inseguro`, `Juega`, `No_juega`, `Controlado`, `Emotivo`, `Seguro`, `Amable`, `Desconsiderado`, `Laborioso`, `Perezoso`, `Desconfiado`, `Dominante`, `Sumiso`, `Disciplinado`, `Indisciplinado`, `Rebelde`, `Obediente`, `Ordenado`, `Desordenado`, `Tendencias_Destructivas`) VALUES
(1, 'asdsdsad', 'adsdsad', 'dsdadsad', 'asdsdasd', 'asdsda', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'ñsmlñsm');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factores_que_motivan`
--

CREATE TABLE `factores_que_motivan` (
  `ID_Factores` int(11) NOT NULL,
  `FactoresQueMotivan` varchar(250) NOT NULL,
  `Referido_Por` varchar(250) NOT NULL,
  `Diagnostico_Organico` varchar(250) NOT NULL,
  `ActitudDeLosPadres` varchar(250) NOT NULL,
  `EstadoEmocionalNiño` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factores_que_motivan`
--

INSERT INTO `factores_que_motivan` (`ID_Factores`, `FactoresQueMotivan`, `Referido_Por`, `Diagnostico_Organico`, `ActitudDeLosPadres`, `EstadoEmocionalNiño`) VALUES
(1, 'asdad', 'adsdasd', 'asdsa', 'asdsadassd', 'sadsads'),
(2, 'dasdasd', 'assa', 'as', 'aasa', 'saasa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `ID_Factura` int(11) NOT NULL,
  `ID_Membrete` int(11) NOT NULL,
  `ID_Paciente` int(11) NOT NULL,
  `ID_Psicologo` int(11) NOT NULL,
  `Id_Fecha` int(11) NOT NULL,
  `Monto` float NOT NULL,
  `IVA` float NOT NULL,
  `Total` float NOT NULL,
  `Asunto` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`ID_Factura`, `ID_Membrete`, `ID_Paciente`, `ID_Psicologo`, `Id_Fecha`, `Monto`, `IVA`, `Total`, `Asunto`) VALUES
(1, 1, 1, 1, 1, 15, 16, 21, 'mjnkjnk');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fecha`
--

CREATE TABLE `fecha` (
  `Id_Fecha` int(11) NOT NULL,
  `Dia` date NOT NULL,
  `Status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fecha`
--

INSERT INTO `fecha` (`Id_Fecha`, `Dia`, `Status`) VALUES
(1, '2024-05-06', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_medico`
--

CREATE TABLE `historial_medico` (
  `ID_Historial` int(11) NOT NULL,
  `ID_Psicologo` int(11) NOT NULL,
  `ID_Factores` int(11) NOT NULL,
  `ID_Fisico` int(11) NOT NULL,
  `ID_Familiares` int(11) NOT NULL,
  `ID_Personalidad_Conducta` int(11) NOT NULL,
  `ID_Hereditario` int(11) NOT NULL,
  `ID_Impresion` int(11) NOT NULL,
  `ID_Pronostico` int(11) NOT NULL,
  `Cedula` int(11) NOT NULL,
  `Telefono` varchar(12) NOT NULL,
  `Direccion` varchar(250) NOT NULL,
  `Escolaridad` varchar(50) NOT NULL,
  `Promedio` float NOT NULL,
  `Escuela` varchar(50) NOT NULL,
  `LugaQueOcupaFamilia` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_medico`
--

INSERT INTO `historial_medico` (`ID_Historial`, `ID_Psicologo`, `ID_Factores`, `ID_Fisico`, `ID_Familiares`, `ID_Personalidad_Conducta`, `ID_Hereditario`, `ID_Impresion`, `ID_Pronostico`, `Cedula`, `Telefono`, `Direccion`, `Escolaridad`, `Promedio`, `Escuela`, `LugaQueOcupaFamilia`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '', '', 'jbj', 18.7, 'kjnkjnj', ' kjini');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historico`
--

CREATE TABLE `historico` (
  `ID_Historico` int(11) NOT NULL,
  `ID_Paciente` int(11) NOT NULL,
  `ID_Psicologa` int(11) NOT NULL,
  `ID_Admin` int(11) NOT NULL,
  `ID_Factura` int(11) NOT NULL,
  `ID_Historial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historico`
--

INSERT INTO `historico` (`ID_Historico`, `ID_Paciente`, `ID_Psicologa`, `ID_Admin`, `ID_Factura`, `ID_Historial`) VALUES
(1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impresion_psicologa`
--

CREATE TABLE `impresion_psicologa` (
  `ID_Impresion` int(11) NOT NULL,
  `Opinion` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `impresion_psicologa`
--

INSERT INTO `impresion_psicologa` (`ID_Impresion`, `Opinion`) VALUES
(1, 'hjhjb');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `ID_Login` int(11) NOT NULL,
  `Usuario` varchar(80) NOT NULL,
  `Tipo` enum('Usuario','Psicologo','Administrador','') NOT NULL,
  `Password` varchar(34) NOT NULL,
  `Correo` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`ID_Login`, `Usuario`, `Tipo`, `Password`, `Correo`) VALUES
(1, '28372278', 'Usuario', '81dc9bdb52d04dc20036dbd8313ed055', 'leximar277@gmail.com'),
(2, 'Psicologa', 'Psicologo', '202cb962ac59075b964b07152d234b70', 'Psicologa@gmail.com'),
(3, 'Admi', 'Administrador', '202cb962ac59075b964b07152d234b70', 'admi@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `municipio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id_municipio`, `id_estado`, `municipio`) VALUES
(1, 1, 'Alto Orinoco'),
(2, 1, 'Atabapo'),
(3, 1, 'Atures'),
(4, 1, 'Autana'),
(5, 1, 'Manapiare'),
(6, 1, 'Maroa'),
(7, 1, 'Río Negro'),
(8, 2, 'Anaco'),
(9, 2, 'Aragua'),
(10, 2, 'Manuel Ezequiel Bruzual'),
(11, 2, 'Diego Bautista Urbaneja'),
(12, 2, 'Fernando Peñalver'),
(13, 2, 'Francisco Del Carmen Carvajal'),
(14, 2, 'General Sir Arthur McGregor'),
(15, 2, 'Guanta'),
(16, 2, 'Independencia'),
(17, 2, 'José Gregorio Monagas'),
(18, 2, 'Juan Antonio Sotillo'),
(19, 2, 'Juan Manuel Cajigal'),
(20, 2, 'Libertad'),
(21, 2, 'Francisco de Miranda'),
(22, 2, 'Pedro María Freites'),
(23, 2, 'Píritu'),
(24, 2, 'San José de Guanipa'),
(25, 2, 'San Juan de Capistrano'),
(26, 2, 'Santa Ana'),
(27, 2, 'Simón Bolívar'),
(28, 2, 'Simón Rodríguez'),
(29, 3, 'Achaguas'),
(30, 3, 'Biruaca'),
(31, 3, 'Muñóz'),
(32, 3, 'Páez'),
(33, 3, 'Pedro Camejo'),
(34, 3, 'Rómulo Gallegos'),
(35, 3, 'San Fernando'),
(36, 4, 'Atanasio Girardot'),
(37, 4, 'Bolívar'),
(38, 4, 'Camatagua'),
(39, 4, 'Francisco Linares Alcántara'),
(40, 4, 'José Ángel Lamas'),
(41, 4, 'José Félix Ribas'),
(42, 4, 'José Rafael Revenga'),
(43, 4, 'Libertador'),
(44, 4, 'Mario Briceño Iragorry'),
(45, 4, 'Ocumare de la Costa de Oro'),
(46, 4, 'San Casimiro'),
(47, 4, 'San Sebastián'),
(48, 4, 'Santiago Mariño'),
(49, 4, 'Santos Michelena'),
(50, 4, 'Sucre'),
(51, 4, 'Tovar'),
(52, 4, 'Urdaneta'),
(53, 4, 'Zamora'),
(54, 5, 'Alberto Arvelo Torrealba'),
(55, 5, 'Andrés Eloy Blanco'),
(56, 5, 'Antonio José de Sucre'),
(57, 5, 'Arismendi'),
(58, 5, 'Barinas'),
(59, 5, 'Bolívar'),
(60, 5, 'Cruz Paredes'),
(61, 5, 'Ezequiel Zamora'),
(62, 5, 'Obispos'),
(63, 5, 'Pedraza'),
(64, 5, 'Rojas'),
(65, 5, 'Sosa'),
(66, 6, 'Caroní'),
(67, 6, 'Cedeño'),
(68, 6, 'El Callao'),
(69, 6, 'Gran Sabana'),
(70, 6, 'Heres'),
(71, 6, 'Piar'),
(72, 6, 'Angostura (Raúl Leoni)'),
(73, 6, 'Roscio'),
(74, 6, 'Sifontes'),
(75, 6, 'Sucre'),
(76, 6, 'Padre Pedro Chien'),
(77, 7, 'Bejuma'),
(78, 7, 'Carlos Arvelo'),
(79, 7, 'Diego Ibarra'),
(80, 7, 'Guacara'),
(81, 7, 'Juan José Mora'),
(82, 7, 'Libertador'),
(83, 7, 'Los Guayos'),
(84, 7, 'Miranda'),
(85, 7, 'Montalbán'),
(86, 7, 'Naguanagua'),
(87, 7, 'Puerto Cabello'),
(88, 7, 'San Diego'),
(89, 7, 'San Joaquín'),
(90, 7, 'Valencia'),
(91, 8, 'Anzoátegui'),
(92, 8, 'Tinaquillo'),
(93, 8, 'Girardot'),
(94, 8, 'Lima Blanco'),
(95, 8, 'Pao de San Juan Bautista'),
(96, 8, 'Ricaurte'),
(97, 8, 'Rómulo Gallegos'),
(98, 8, 'San Carlos'),
(99, 8, 'Tinaco'),
(100, 9, 'Antonio Díaz'),
(101, 9, 'Casacoima'),
(102, 9, 'Pedernales'),
(103, 9, 'Tucupita'),
(104, 10, 'Acosta'),
(105, 10, 'Bolívar'),
(106, 10, 'Buchivacoa'),
(107, 10, 'Cacique Manaure'),
(108, 10, 'Carirubana'),
(109, 10, 'Colina'),
(110, 10, 'Dabajuro'),
(111, 10, 'Democracia'),
(112, 10, 'Falcón'),
(113, 10, 'Federación'),
(114, 10, 'Jacura'),
(115, 10, 'José Laurencio Silva'),
(116, 10, 'Los Taques'),
(117, 10, 'Mauroa'),
(118, 10, 'Miranda'),
(119, 10, 'Monseñor Iturriza'),
(120, 10, 'Palmasola'),
(121, 10, 'Petit'),
(122, 10, 'Píritu'),
(123, 10, 'San Francisco'),
(124, 10, 'Sucre'),
(125, 10, 'Tocópero'),
(126, 10, 'Unión'),
(127, 10, 'Urumaco'),
(128, 10, 'Zamora'),
(129, 11, 'Camaguán'),
(130, 11, 'Chaguaramas'),
(131, 11, 'El Socorro'),
(132, 11, 'José Félix Ribas'),
(133, 11, 'José Tadeo Monagas'),
(134, 11, 'Juan Germán Roscio'),
(135, 11, 'Julián Mellado'),
(136, 11, 'Las Mercedes'),
(137, 11, 'Leonardo Infante'),
(138, 11, 'Pedro Zaraza'),
(139, 11, 'Ortíz'),
(140, 11, 'San Gerónimo de Guayabal'),
(141, 11, 'San José de Guaribe'),
(142, 11, 'Santa María de Ipire'),
(143, 11, 'Sebastián Francisco de Miranda'),
(144, 12, 'Andrés Eloy Blanco'),
(145, 12, 'Crespo'),
(146, 12, 'Iribarren'),
(147, 12, 'Jiménez'),
(148, 12, 'Morán'),
(149, 12, 'Palavecino'),
(150, 12, 'Simón Planas'),
(151, 12, 'Torres'),
(152, 12, 'Urdaneta'),
(179, 13, 'Alberto Adriani'),
(180, 13, 'Andrés Bello'),
(181, 13, 'Antonio Pinto Salinas'),
(182, 13, 'Aricagua'),
(183, 13, 'Arzobispo Chacón'),
(184, 13, 'Campo Elías'),
(185, 13, 'Caracciolo Parra Olmedo'),
(186, 13, 'Cardenal Quintero'),
(187, 13, 'Guaraque'),
(188, 13, 'Julio César Salas'),
(189, 13, 'Justo Briceño'),
(190, 13, 'Libertador'),
(191, 13, 'Miranda'),
(192, 13, 'Obispo Ramos de Lora'),
(193, 13, 'Padre Noguera'),
(194, 13, 'Pueblo Llano'),
(195, 13, 'Rangel'),
(196, 13, 'Rivas Dávila'),
(197, 13, 'Santos Marquina'),
(198, 13, 'Sucre'),
(199, 13, 'Tovar'),
(200, 13, 'Tulio Febres Cordero'),
(201, 13, 'Zea'),
(223, 14, 'Acevedo'),
(224, 14, 'Andrés Bello'),
(225, 14, 'Baruta'),
(226, 14, 'Brión'),
(227, 14, 'Buroz'),
(228, 14, 'Carrizal'),
(229, 14, 'Chacao'),
(230, 14, 'Cristóbal Rojas'),
(231, 14, 'El Hatillo'),
(232, 14, 'Guaicaipuro'),
(233, 14, 'Independencia'),
(234, 14, 'Lander'),
(235, 14, 'Los Salias'),
(236, 14, 'Páez'),
(237, 14, 'Paz Castillo'),
(238, 14, 'Pedro Gual'),
(239, 14, 'Plaza'),
(240, 14, 'Simón Bolívar'),
(241, 14, 'Sucre'),
(242, 14, 'Urdaneta'),
(243, 14, 'Zamora'),
(258, 15, 'Acosta'),
(259, 15, 'Aguasay'),
(260, 15, 'Bolívar'),
(261, 15, 'Caripe'),
(262, 15, 'Cedeño'),
(263, 15, 'Ezequiel Zamora'),
(264, 15, 'Libertador'),
(265, 15, 'Maturín'),
(266, 15, 'Piar'),
(267, 15, 'Punceres'),
(268, 15, 'Santa Bárbara'),
(269, 15, 'Sotillo'),
(270, 15, 'Uracoa'),
(271, 16, 'Antolín del Campo'),
(272, 16, 'Arismendi'),
(273, 16, 'García'),
(274, 16, 'Gómez'),
(275, 16, 'Maneiro'),
(276, 16, 'Marcano'),
(277, 16, 'Mariño'),
(278, 16, 'Península de Macanao'),
(279, 16, 'Tubores'),
(280, 16, 'Villalba'),
(281, 16, 'Díaz'),
(282, 17, 'Agua Blanca'),
(283, 17, 'Araure'),
(284, 17, 'Esteller'),
(285, 17, 'Guanare'),
(286, 17, 'Guanarito'),
(287, 17, 'Monseñor José Vicente de Unda'),
(288, 17, 'Ospino'),
(289, 17, 'Páez'),
(290, 17, 'Papelón'),
(291, 17, 'San Genaro de Boconoíto'),
(292, 17, 'San Rafael de Onoto'),
(293, 17, 'Santa Rosalía'),
(294, 17, 'Sucre'),
(295, 17, 'Turén'),
(296, 18, 'Andrés Eloy Blanco'),
(297, 18, 'Andrés Mata'),
(298, 18, 'Arismendi'),
(299, 18, 'Benítez'),
(300, 18, 'Bermúdez'),
(301, 18, 'Bolívar'),
(302, 18, 'Cajigal'),
(303, 18, 'Cruz Salmerón Acosta'),
(304, 18, 'Libertador'),
(305, 18, 'Mariño'),
(306, 18, 'Mejía'),
(307, 18, 'Montes'),
(308, 18, 'Ribero'),
(309, 18, 'Sucre'),
(310, 18, 'Valdéz'),
(341, 19, 'Andrés Bello'),
(342, 19, 'Antonio Rómulo Costa'),
(343, 19, 'Ayacucho'),
(344, 19, 'Bolívar'),
(345, 19, 'Cárdenas'),
(346, 19, 'Córdoba'),
(347, 19, 'Fernández Feo'),
(348, 19, 'Francisco de Miranda'),
(349, 19, 'García de Hevia'),
(350, 19, 'Guásimos'),
(351, 19, 'Independencia'),
(352, 19, 'Jáuregui'),
(353, 19, 'José María Vargas'),
(354, 19, 'Junín'),
(355, 19, 'Libertad'),
(356, 19, 'Libertador'),
(357, 19, 'Lobatera'),
(358, 19, 'Michelena'),
(359, 19, 'Panamericano'),
(360, 19, 'Pedro María Ureña'),
(361, 19, 'Rafael Urdaneta'),
(362, 19, 'Samuel Darío Maldonado'),
(363, 19, 'San Cristóbal'),
(364, 19, 'Seboruco'),
(365, 19, 'Simón Rodríguez'),
(366, 19, 'Sucre'),
(367, 19, 'Torbes'),
(368, 19, 'Uribante'),
(369, 19, 'San Judas Tadeo'),
(370, 20, 'Andrés Bello'),
(371, 20, 'Boconó'),
(372, 20, 'Bolívar'),
(373, 20, 'Candelaria'),
(374, 20, 'Carache'),
(375, 20, 'Escuque'),
(376, 20, 'José Felipe Márquez Cañizalez'),
(377, 20, 'Juan Vicente Campos Elías'),
(378, 20, 'La Ceiba'),
(379, 20, 'Miranda'),
(380, 20, 'Monte Carmelo'),
(381, 20, 'Motatán'),
(382, 20, 'Pampán'),
(383, 20, 'Pampanito'),
(384, 20, 'Rafael Rangel'),
(385, 20, 'San Rafael de Carvajal'),
(386, 20, 'Sucre'),
(387, 20, 'Trujillo'),
(388, 20, 'Urdaneta'),
(389, 20, 'Valera'),
(390, 21, 'Vargas'),
(391, 22, 'Arístides Bastidas'),
(392, 22, 'Bolívar'),
(407, 22, 'Bruzual'),
(408, 22, 'Cocorote'),
(409, 22, 'Independencia'),
(410, 22, 'José Antonio Páez'),
(411, 22, 'La Trinidad'),
(412, 22, 'Manuel Monge'),
(413, 22, 'Nirgua'),
(414, 22, 'Peña'),
(415, 22, 'San Felipe'),
(416, 22, 'Sucre'),
(417, 22, 'Urachiche'),
(418, 22, 'José Joaquín Veroes'),
(441, 23, 'Almirante Padilla'),
(442, 23, 'Baralt'),
(443, 23, 'Cabimas'),
(444, 23, 'Catatumbo'),
(445, 23, 'Colón'),
(446, 23, 'Francisco Javier Pulgar'),
(447, 23, 'Páez'),
(448, 23, 'Jesús Enrique Losada'),
(449, 23, 'Jesús María Semprún'),
(450, 23, 'La Cañada de Urdaneta'),
(451, 23, 'Lagunillas'),
(452, 23, 'Machiques de Perijá'),
(453, 23, 'Mara'),
(454, 23, 'Maracaibo'),
(455, 23, 'Miranda'),
(456, 23, 'Rosario de Perijá'),
(457, 23, 'San Francisco'),
(458, 23, 'Santa Rita'),
(459, 23, 'Simón Bolívar'),
(460, 23, 'Sucre'),
(461, 23, 'Valmore Rodríguez'),
(462, 24, 'Libertador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `ID_Paciente` int(11) NOT NULL,
  `Primer Nombre` varchar(80) NOT NULL,
  `Segundo Nombre` varchar(80) NOT NULL,
  `Primer Apellido` varchar(80) NOT NULL,
  `Segundo Apellido` varchar(80) NOT NULL,
  `Tipo_Cedula` enum('V','E','J','P') NOT NULL,
  `Cedula` int(11) NOT NULL,
  `Num_Hijos` int(11) NOT NULL,
  `Teléfono` varchar(15) NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Correo` varchar(80) NOT NULL,
  `ID_Direccion` int(11) NOT NULL,
  `StatusPaciente` enum('Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`ID_Paciente`, `Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Tipo_Cedula`, `Cedula`, `Num_Hijos`, `Teléfono`, `Fecha_Nacimiento`, `Correo`, `ID_Direccion`, `StatusPaciente`) VALUES
(1, 'asds', 'dsa', 'dsad', 'sad', 'V', 28372278, 0, '04260649763', '2024-05-05', 'dsasd@gamil.com', 1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pronostico`
--

CREATE TABLE `pronostico` (
  `ID_Pronostico` int(11) NOT NULL,
  `Recomendaciones` varchar(300) NOT NULL,
  `Plan_Psicoterapeutico` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pronostico`
--

INSERT INTO `pronostico` (`ID_Pronostico`, `Recomendaciones`, `Plan_Psicoterapeutico`) VALUES
(1, 'admaslñdmñsa', 'cmsaklmsa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `psicologa`
--

CREATE TABLE `psicologa` (
  `ID_Psicologa` int(11) NOT NULL,
  `Primer Nombre` varchar(80) NOT NULL,
  `Segundo Nombre` varchar(80) NOT NULL,
  `Primer Apellido` varchar(80) NOT NULL,
  `Segundo Apellido` varchar(80) NOT NULL,
  `Cedula` varchar(10) NOT NULL,
  `Teléfono` varchar(15) NOT NULL,
  `Correo` varchar(80) NOT NULL,
  `Especialidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `psicologa`
--

INSERT INTO `psicologa` (`ID_Psicologa`, `Primer Nombre`, `Segundo Nombre`, `Primer Apellido`, `Segundo Apellido`, `Cedula`, `Teléfono`, `Correo`, `Especialidad`) VALUES
(1, 'zczxc', 'zcxzc', 'zxczx', 'zcxc', '28372278', '04260649763', 'leximar@gmail.com', 'Psicología infantil y adolencente ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cita`
--

CREATE TABLE `tipo_cita` (
  `Id_TipoCita` int(11) NOT NULL,
  `Tipo` enum('Infantil','Adulto','Pareja') NOT NULL,
  `Precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_cita`
--

INSERT INTO `tipo_cita` (`Id_TipoCita`, `Tipo`, `Precio`) VALUES
(1, 'Adulto', 15),
(2, 'Infantil', 20000),
(3, 'Pareja', 30000);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`ID_Admi`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`ID_Cita`),
  ADD KEY `Id_TipoCita` (`Id_TipoCita`),
  ADD KEY `ID_Paciente` (`ID_Paciente`),
  ADD KEY `ID_Psicologa` (`ID_Psicologa`),
  ADD KEY `ID_CitaMenor` (`ID_CitaMenor`),
  ADD KEY `Id_Fecha` (`Id_Fecha`),
  ADD KEY `ID_Pareja` (`ID_Pareja`),
  ADD KEY `ID_Login` (`ID_Login`);

--
-- Indices de la tabla `citamenor`
--
ALTER TABLE `citamenor`
  ADD PRIMARY KEY (`ID_CitaMenor`);

--
-- Indices de la tabla `cita_pareja`
--
ALTER TABLE `cita_pareja`
  ADD PRIMARY KEY (`ID_Pareja`),
  ADD KEY `ID_Direccion1` (`ID_Direccion1`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`ID_Direccion`),
  ADD KEY `ID_Estados` (`ID_Estados`),
  ADD KEY `ID_Municipio` (`ID_Municipio`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `factoresfamiliares`
--
ALTER TABLE `factoresfamiliares`
  ADD PRIMARY KEY (`ID_Familiares`);

--
-- Indices de la tabla `factoresfisicos`
--
ALTER TABLE `factoresfisicos`
  ADD PRIMARY KEY (`ID_Fisico`);

--
-- Indices de la tabla `factores_hereditarios`
--
ALTER TABLE `factores_hereditarios`
  ADD PRIMARY KEY (`ID_Hereditario`);

--
-- Indices de la tabla `factores_personalidad_conducta`
--
ALTER TABLE `factores_personalidad_conducta`
  ADD PRIMARY KEY (`ID_Personalidad_Conducta`);

--
-- Indices de la tabla `factores_que_motivan`
--
ALTER TABLE `factores_que_motivan`
  ADD PRIMARY KEY (`ID_Factores`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`ID_Factura`),
  ADD KEY `ID_Paciente` (`ID_Paciente`),
  ADD KEY `ID_Psicologo` (`ID_Psicologo`),
  ADD KEY `ID_Membrete` (`ID_Membrete`),
  ADD KEY `Id_Fecha` (`Id_Fecha`),
  ADD KEY `Id_Fecha_2` (`Id_Fecha`);

--
-- Indices de la tabla `fecha`
--
ALTER TABLE `fecha`
  ADD PRIMARY KEY (`Id_Fecha`);

--
-- Indices de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `ID_Psicologo` (`ID_Psicologo`),
  ADD KEY `ID_Factores` (`ID_Factores`),
  ADD KEY `ID_Fisico` (`ID_Fisico`),
  ADD KEY `ID_Familiares` (`ID_Familiares`),
  ADD KEY `ID_Personalidad_Conducta` (`ID_Personalidad_Conducta`),
  ADD KEY `ID_Hereditario` (`ID_Hereditario`),
  ADD KEY `ID_Impresion` (`ID_Impresion`),
  ADD KEY `ID_Pronostico` (`ID_Pronostico`);

--
-- Indices de la tabla `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`ID_Historico`),
  ADD KEY `ID_Paciente` (`ID_Paciente`),
  ADD KEY `ID_Psicologa` (`ID_Psicologa`),
  ADD KEY `ID_Admin` (`ID_Admin`),
  ADD KEY `ID_Factura` (`ID_Factura`),
  ADD KEY `ID_Historial` (`ID_Historial`);

--
-- Indices de la tabla `impresion_psicologa`
--
ALTER TABLE `impresion_psicologa`
  ADD PRIMARY KEY (`ID_Impresion`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`ID_Login`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`ID_Paciente`),
  ADD KEY `ID_Direccion` (`ID_Direccion`);

--
-- Indices de la tabla `pronostico`
--
ALTER TABLE `pronostico`
  ADD PRIMARY KEY (`ID_Pronostico`);

--
-- Indices de la tabla `psicologa`
--
ALTER TABLE `psicologa`
  ADD PRIMARY KEY (`ID_Psicologa`);

--
-- Indices de la tabla `tipo_cita`
--
ALTER TABLE `tipo_cita`
  ADD PRIMARY KEY (`Id_TipoCita`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `ID_Admi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `ID_Cita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `citamenor`
--
ALTER TABLE `citamenor`
  MODIFY `ID_CitaMenor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cita_pareja`
--
ALTER TABLE `cita_pareja`
  MODIFY `ID_Pareja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `ID_Direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `factoresfamiliares`
--
ALTER TABLE `factoresfamiliares`
  MODIFY `ID_Familiares` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factoresfisicos`
--
ALTER TABLE `factoresfisicos`
  MODIFY `ID_Fisico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factores_hereditarios`
--
ALTER TABLE `factores_hereditarios`
  MODIFY `ID_Hereditario` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factores_personalidad_conducta`
--
ALTER TABLE `factores_personalidad_conducta`
  MODIFY `ID_Personalidad_Conducta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factores_que_motivan`
--
ALTER TABLE `factores_que_motivan`
  MODIFY `ID_Factores` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `ID_Factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `fecha`
--
ALTER TABLE `fecha`
  MODIFY `Id_Fecha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  MODIFY `ID_Historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historico`
--
ALTER TABLE `historico`
  MODIFY `ID_Historico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `impresion_psicologa`
--
ALTER TABLE `impresion_psicologa`
  MODIFY `ID_Impresion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `ID_Login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=463;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `ID_Paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pronostico`
--
ALTER TABLE `pronostico`
  MODIFY `ID_Pronostico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `psicologa`
--
ALTER TABLE `psicologa`
  MODIFY `ID_Psicologa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_cita`
--
ALTER TABLE `tipo_cita`
  MODIFY `Id_TipoCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `cita_ibfk_3` FOREIGN KEY (`Id_TipoCita`) REFERENCES `tipo_cita` (`Id_TipoCita`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_4` FOREIGN KEY (`ID_Paciente`) REFERENCES `paciente` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_5` FOREIGN KEY (`ID_Psicologa`) REFERENCES `psicologa` (`ID_Psicologa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_6` FOREIGN KEY (`ID_CitaMenor`) REFERENCES `citamenor` (`ID_CitaMenor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_7` FOREIGN KEY (`Id_Fecha`) REFERENCES `fecha` (`Id_Fecha`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_8` FOREIGN KEY (`ID_Pareja`) REFERENCES `cita_pareja` (`ID_Pareja`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cita_ibfk_9` FOREIGN KEY (`ID_Login`) REFERENCES `login` (`ID_Login`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cita_pareja`
--
ALTER TABLE `cita_pareja`
  ADD CONSTRAINT `cita_pareja_ibfk_1` FOREIGN KEY (`ID_Direccion1`) REFERENCES `direccion` (`ID_Direccion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD CONSTRAINT `direccion_ibfk_1` FOREIGN KEY (`ID_Estados`) REFERENCES `estados` (`id_estado`) ON UPDATE CASCADE,
  ADD CONSTRAINT `direccion_ibfk_3` FOREIGN KEY (`ID_Municipio`) REFERENCES `municipios` (`id_municipio`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`ID_Paciente`) REFERENCES `paciente` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`ID_Psicologo`) REFERENCES `psicologa` (`ID_Psicologa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`Id_Fecha`) REFERENCES `fecha` (`Id_Fecha`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD CONSTRAINT `historial_medico_ibfk_2` FOREIGN KEY (`ID_Pronostico`) REFERENCES `pronostico` (`ID_Pronostico`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_3` FOREIGN KEY (`ID_Psicologo`) REFERENCES `psicologa` (`ID_Psicologa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_4` FOREIGN KEY (`ID_Impresion`) REFERENCES `impresion_psicologa` (`ID_Impresion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_5` FOREIGN KEY (`ID_Factores`) REFERENCES `factores_que_motivan` (`ID_Factores`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_6` FOREIGN KEY (`ID_Hereditario`) REFERENCES `factores_hereditarios` (`ID_Hereditario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_7` FOREIGN KEY (`ID_Personalidad_Conducta`) REFERENCES `factores_personalidad_conducta` (`ID_Personalidad_Conducta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_8` FOREIGN KEY (`ID_Fisico`) REFERENCES `factoresfisicos` (`ID_Fisico`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_medico_ibfk_9` FOREIGN KEY (`ID_Familiares`) REFERENCES `factoresfamiliares` (`ID_Familiares`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`ID_Paciente`) REFERENCES `paciente` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_ibfk_2` FOREIGN KEY (`ID_Admin`) REFERENCES `administrador` (`ID_Admi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_ibfk_3` FOREIGN KEY (`ID_Psicologa`) REFERENCES `psicologa` (`ID_Psicologa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_ibfk_4` FOREIGN KEY (`ID_Factura`) REFERENCES `factura` (`ID_Factura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_ibfk_5` FOREIGN KEY (`ID_Historial`) REFERENCES `historial_medico` (`ID_Historial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD CONSTRAINT `municipios_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `paciente_ibfk_2` FOREIGN KEY (`ID_Direccion`) REFERENCES `direccion` (`ID_Direccion`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
