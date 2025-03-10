-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2025 at 09:05 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `nuevo_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_credentials`
--

CREATE TABLE `business_credentials` (
  `id` bigint UNSIGNED NOT NULL,
  `NIT` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_credentials`
--
ALTER TABLE `business_credentials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_credentials`
--
ALTER TABLE `business_credentials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `business_product` 
ADD COLUMN `stockInicial` INT NOT NULL DEFAULT 0 AFTER `updated_at`,
ADD COLUMN `stockActual` INT NOT NULL DEFAULT 0 AFTER `stockInicial`,
ADD COLUMN `estado_stock` ENUM('disponible', 'agotado', 'por_agotarse') NOT NULL DEFAULT 'disponible' AFTER `stockActual`,
ADD COLUMN `stockMinimo` INT NOT NULL DEFAULT 0 AFTER `estado_stock`;

-- --------------------------------------------------------

--
-- Table structure for table `business_product_movements`
--

CREATE TABLE `business_product_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `business_product_id` int NOT NULL,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('entrada','salida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `producto` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_product_movements`
--
ALTER TABLE `business_product_movements`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_product_movements`
--
ALTER TABLE `business_product_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `cuentas_por_cobrar`
--

CREATE TABLE `cuentas_por_cobrar` (
  `id` bigint UNSIGNED NOT NULL,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(8,2) NOT NULL,
  `saldo` decimal(8,2) NOT NULL,
  `estado` enum('pendiente','parcial','pagado','vencido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `dtes`
--

CREATE TABLE `dtes` (
  `id` bigint UNSIGNED NOT NULL,
  `business_id` int NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','error','success') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtes`
--
ALTER TABLE `dtes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtes`
--
ALTER TABLE `dtes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `movements`
--

CREATE TABLE `movements` (
  `id` bigint UNSIGNED NOT NULL,
  `cuenta_id` bigint UNSIGNED DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('pago','ajuste','cargo_extra','descuento') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `monto` decimal(8,2) NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movements`
--
ALTER TABLE `movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movements_cuenta_id_foreign` (`cuenta_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movements`
--
ALTER TABLE `movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movements`
--
ALTER TABLE `movements`
  ADD CONSTRAINT `movements_cuenta_id_foreign` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_por_cobrar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `facturacion_konverza`.`users` 
ADD COLUMN `identity_document` VARCHAR(255) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1 AFTER `updated_at`,
ADD COLUMN `last_login_at` TIMESTAMP NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `reset_password_token` VARCHAR(255) NULL DEFAULT NULL AFTER `last_login_at`,
ADD COLUMN `reset_password_at` TIMESTAMP NULL DEFAULT NULL AFTER `reset_password_token`;


COMMIT;
