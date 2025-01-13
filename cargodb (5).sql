-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost: 3308
-- Tiempo de generación: 13-01-2025 a las 06:02:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cargodb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carros`
--

CREATE TABLE `carros` (
  `id` int(11) NOT NULL,
  `marca` varchar(255) NOT NULL,
  `modelo` varchar(255) NOT NULL,
  `ano` int(11) NOT NULL,
  `asientos` int(11) NOT NULL,
  `placa` varchar(255) NOT NULL,
  `combustible` varchar(255) NOT NULL,
  `transmision` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `disponible` tinyint(1) DEFAULT 0,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carros`
--

INSERT INTO `carros` (`id`, `marca`, `modelo`, `ano`, `asientos`, `placa`, `combustible`, `transmision`, `descripcion`, `imagen`, `disponible`, `precio`) VALUES
(15, 'Toyota', 'Corolla', 2020, 4, 'Pcx-8300', 'Gasolina', 'Manual', 'El Toyota Corolla combina confiabilidad, eficiencia y tecnología avanzada, lo que lo convierte en una opción ideal para quienes buscan un sedán compacto práctico y cómodo. Su diseño moderno y sus características de seguridad lo hacen apto tanto para viajes diarios como para aventuras más largas.', 'uploads/toyotaCorola.jpg', 0, 20.00),
(16, 'Hyundai', 'Kona', 2020, 4, 'Pcx-8301', 'Gasolina', 'Manual', 'El Hyundai Kona es una opción versátil en el segmento de SUV subcompactos, destacando por su diseño atractivo, opciones de motorización eficientes y tecnología moderna. Su enfoque en la seguridad y la comodidad lo convierte en una excelente elección para quienes buscan un vehículo práctico para la vida diaria.', 'uploads/accent.jpg', 1, 25.00),
(17, 'Audi', 'A4', 2024, 4, 'Pcx-8302', 'Híbrido', 'Automática', 'El Audi A4 es un sedán que combina lujo, tecnología y rendimiento, lo que lo convierte en una opción atractiva para quienes buscan un vehículo elegante y funcional. Su diseño moderno y su enfoque en la seguridad y la comodidad hacen del A4 una elección destacada en el segmento de los sedanes premium.', 'uploads/AudiA4.png', 1, 50.00),
(19, 'BMW', 'Serie 3', 2022, 2, 'Pcx-8303', 'Eléctrico', 'Automática', 'El BMW Serie 3 se mantiene como una opción destacada en el mercado de sedanes de lujo gracias a su combinación de confort, tecnología avanzada y un rendimiento ágil. Su diseño sofisticado y las múltiples opciones de motorización lo convierten en una elección ideal para quienes buscan un vehículo que ofrezca tanto estilo como funcionalidad.', 'uploads/BMWserie3.png', 0, 40.00),
(20, 'Ford', 'Mustang', 2021, 5, 'Pcx-8304', 'Eléctrico', 'Manual', 'El Ford Mustang 2024 combina potencia, estilo y tecnología avanzada, manteniéndose como un líder en el segmento de automóviles deportivos. Su variedad de motorizaciones y características personalizables lo convierten en una opción atractiva para los entusiastas del automovilismo que buscan una experiencia emocionante al volante.', 'uploads/fordMustang.jpg', 1, 35.00),
(21, 'Volkswagen', 'Tiguan', 2024, 3, 'Pcx-8305', 'Híbrido', 'Automática', 'El Volkswagen Tiguan se posiciona como una opción sólida en el segmento de SUV compactos, ofreciendo un equilibrio entre diseño, tecnología y rendimiento. Su amplia gama de motorizaciones y características avanzadas lo hacen atractivo tanto para familias como para quienes buscan un vehículo versátil para el día a día.', 'uploads/renegade.jpg', 1, 60.00),
(23, 'Toyota', 'Prius', 2020, 3, 'Pcx-8307', 'Eléctrico', 'Automática', 'El Toyota Prius sigue siendo una opción líder en el mercado de vehículos híbridos gracias a su excepcional eficiencia de combustible, diseño moderno y tecnología avanzada. Su enfoque en la sostenibilidad y la comodidad lo convierte en una elección ideal para quienes buscan un vehículo práctico y ecológico.', 'uploads/toyotaPrado.jpg', 1, 65.00),
(24, 'Ford', 'F-150', 2023, 3, 'Pcx-8309', 'Diesel', 'Manual', 'El Ford F-150 combina potencia, versatilidad y tecnología avanzada, lo que lo convierte en una opción destacada tanto para trabajos exigentes como para uso diario. Su amplia gama de motorizaciones y configuraciones asegura que haya un F-150 adecuado para cada necesidad.', 'uploads/toyotaHilux.png', 1, 90.00),
(25, 'Chevrolet', 'Camaro', 2023, 2, 'Pcx-9000', 'Eléctrico', 'Automática', 'El Chevrolet Camaro combina rendimiento impresionante con un diseño atractivo y tecnología avanzada, lo que lo convierte en una opción destacada para los amantes de los automóviles deportivos. Su variedad de motorizaciones y características personalizables aseguran que haya un Camaro para cada tipo de conductor que busque emoción y estilo en la carretera.', 'uploads/autoCarretera.jpg', 1, 90.00),
(26, 'Audi', 'Q7', 2024, 5, 'Pcx-9010', 'Híbrido', 'Automática', 'Un SUV de lujo con características avanzadas.', 'uploads/principal.png', 1, 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductores`
--

CREATE TABLE `conductores` (
  `id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cedula` varchar(255) NOT NULL,
  `licencia` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `conductores`
--

INSERT INTO `conductores` (`id`, `renta_id`, `nombre`, `cedula`, `licencia`, `telefono`) VALUES
(25, 61, 'Andre', '1804888922', '1902821', '0959995555');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `cargo_extra` float DEFAULT 0,
  `detalle_cargos` text DEFAULT NULL,
  `total_cargo` decimal(10,2) DEFAULT 0.00,
  `retraso` int(11) DEFAULT 0,
  `metodo_pagoCargo` varchar(50) NOT NULL,
  `total_pago` decimal(10,2) DEFAULT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `renta_id`, `monto`, `cargo_extra`, `detalle_cargos`, `total_cargo`, `retraso`, `metodo_pagoCargo`, `total_pago`, `fecha_generacion`, `estado`) VALUES
(58, 60, 160.00, 0, NULL, 0.00, 0, '', 160.00, '2025-01-12 23:54:19', 'pendiente'),
(59, 61, 60.00, 0, NULL, 0.00, 0, '', 60.00, '2025-01-12 23:54:45', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rentas`
--

CREATE TABLE `rentas` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `carro_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `garantia` varchar(50) NOT NULL,
  `estado_renta` varchar(20) DEFAULT 'solicitado',
  `estado_vehiculo` varchar(50) DEFAULT NULL,
  `estado_pago` varchar(20) DEFAULT 'sin pago',
  `metodo_pago` varchar(50) NOT NULL DEFAULT 'No definido',
  `conductor` varchar(255) DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rentas`
--

INSERT INTO `rentas` (`id`, `usuario`, `carro_id`, `fecha_inicio`, `fecha_fin`, `garantia`, `estado_renta`, `estado_vehiculo`, `estado_pago`, `metodo_pago`, `conductor`, `fecha_devolucion`) VALUES
(60, 'andrea', 19, '2025-01-14', '2025-01-18', 'Cheque', 'alquilado', NULL, 'pago inicial complet', 'Efectivo', 'titular', NULL),
(61, 'andrea', 15, '2025-01-22', '2025-01-25', 'Tarjeta de Crédito', 'alquilado', NULL, 'pago inicial complet', 'Efectivo', 'otro', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('admin','empleado','usuario') NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `email`, `nombre_usuario`, `contraseña`, `rol`, `token`, `verificado`, `token_expira`) VALUES
(8, 'Andrea Vasquez', 'andrealdj289@gmail.com', 'andrea', '$2y$10$lSiGYGdkimEDDSLXgmwpTueHmr4BLJaGY/BWrgq.rx5s0CAJ3jCGS', 'usuario', NULL, 1, NULL),
(9, 'Karen Ayala', 'kandreavasquez1992@gmail.com', 'karen', '$2y$10$uhZR11HyHcGqv6g9ze99n.espG8n1KNXJrH0peIUEjUHxA9EFgmPm', 'empleado', NULL, 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`);

--
-- Indices de la tabla `conductores`
--
ALTER TABLE `conductores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `renta_id` (`renta_id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `renta_id` (`renta_id`);

--
-- Indices de la tabla `rentas`
--
ALTER TABLE `rentas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carro_id` (`carro_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `conductores`
--
ALTER TABLE `conductores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `rentas`
--
ALTER TABLE `rentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `conductores`
--
ALTER TABLE `conductores`
  ADD CONSTRAINT `conductores_ibfk_1` FOREIGN KEY (`renta_id`) REFERENCES `rentas` (`id`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`renta_id`) REFERENCES `rentas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rentas`
--
ALTER TABLE `rentas`
  ADD CONSTRAINT `rentas_ibfk_1` FOREIGN KEY (`carro_id`) REFERENCES `carros` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
