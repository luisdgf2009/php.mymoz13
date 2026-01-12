-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-01-2026 a las 03:36:16
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
-- Base de datos: `sistema_usuarios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `categoria` varchar(50) DEFAULT 'General',
  `descripcion` text DEFAULT NULL,
  `tallas` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 50,
  `en_oferta` tinyint(1) DEFAULT 0,
  `porcentaje_oferta` int(11) DEFAULT 0,
  `codigo` varchar(50) DEFAULT NULL,
  `colores` varchar(255) DEFAULT NULL,
  `stock_por_color` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `imagen`, `categoria`, `descripcion`, `tallas`, `stock`, `en_oferta`, `porcentaje_oferta`, `codigo`, `colores`, `stock_por_color`) VALUES
(16, 'Franelas ', 20.00, 'uploads/1768162156_f2.jpeg,uploads/1768162156_f3.jpeg,uploads/1768162156_f4.jpeg,uploads/1768162156_f5.jpeg,uploads/1768162156_f6.jpeg,uploads/1768162156_f7.jpeg,uploads/1768162156_f8.jpeg,uploads/1768162156_f9.jpeg', 'Caballeros', '100% algodón  Tallas disponibles  S M L XL', 'S,M,L,XL', 50, 0, 0, NULL, NULL, NULL),
(17, 'Franela oversize', 20.00, 'uploads/1768162511_fo1.jpeg', 'Damas', 'acid Wash \r\nTela french Terry', 'S M L ', 17, 0, 0, NULL, NULL, NULL),
(18, 'Franela oversize', 20.00, 'uploads/1768173586_WhatsApp Image 2026-01-11 at 15.53.15.jpeg', 'Caballeros', '....', 'S', 10, 0, 0, '003', 'Gris, Negro', '{\"Gris\":3,\"Negro\":7}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones_home`
--

CREATE TABLE `secciones_home` (
  `id` int(11) NOT NULL,
  `identificador` varchar(50) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` text DEFAULT NULL,
  `filtro` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones_home`
--

INSERT INTO `secciones_home` (`id`, `identificador`, `titulo`, `descripcion`, `imagen`, `filtro`) VALUES
(1, 'oferta_principal', 'Tendencias para Damas', 'Descubre lo último en moda femenina. Elegancia y estilo que definen tu personalidad.', 'uploads/1768174108_banner_WhatsApp Image 2026-01-11 at 16.03.36.jpeg', 'Damas'),
(2, 'oferta_secundaria_1', 'Caballeros: Poder y Estilo', 'Chaquetas de cuero y outfits que imponen respeto.', 'uploads/1768174119_banner_WhatsApp Image 2026-01-11 at 15.57.30.jpeg', 'Caballeros'),
(3, 'oferta_secundaria_2', 'Mundo Kids', 'Comodidad para sus grandes aventuras.', 'uploads/1768174134_banner_WhatsApp Image 2026-01-11 at 16.10.39.jpeg', 'Niños');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) DEFAULT 'cliente',
  `carrito` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula`, `nombre`, `apellido`, `telefono`, `email`, `direccion`, `password`, `rol`, `carrito`) VALUES
('0000', 'Administrador', 'Sistema', '0000000000', 'admin@mymoz13.com', 'Oficina Central', '$2y$10$KHAV30UrvC1PnIeTnlw1VOWWO.PMVRIBvTCX66qRk1J.RUOOdtRVa', 'admin', NULL),
('31928686', 'Luis', 'Gómez', '04267525479', 'admin@inversionesemily.com', 'San Francisco', '$2y$10$mn7B6aHVVXn0vuxARn1zDePSPD6a//l4hRpOaSJdiTclNydmAwmne', 'cliente', '[{\"nombre\":\"Franela oversize | Color: Negro (Talla S)\",\"precio\":20,\"imagen\":\"uploads\\/1768173586_WhatsApp Image 2026-01-11 at 15.53.15.jpeg\",\"cantidad\":2}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `productos` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `comprobante` varchar(255) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL,
  `metodo_retiro` varchar(100) DEFAULT 'Tienda',
  `direccion_envio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `secciones_home`
--
ALTER TABLE `secciones_home`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificador` (`identificador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `secciones_home`
--
ALTER TABLE `secciones_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
