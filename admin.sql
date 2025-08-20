-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 07:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `adminId` bigint(20) UNSIGNED NOT NULL,
  `modelChanged` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`changes`)),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `entityId` bigint(20) UNSIGNED DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `adminId`, `modelChanged`, `action`, `changes`, `createdAt`, `entityId`, `ip`) VALUES
(1, 1, 'products', 'create', '{\"product_id\":1,\"version\":1,\"before\":{},\"after\":{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"diff\":{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"meta\":{\"slug\":\"dining-table-top-sku-2006\",\"sku\":\"SKU-2006\"}}', '2025-08-19 23:08:09', 1, '::1'),
(2, 1, 'products', 'create', '{\"product_id\":2,\"version\":1,\"before\":{},\"after\":{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"diff\":{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"meta\":{\"slug\":\"resin-epoxy-table-top-sku-2005\",\"sku\":\"SKU-2005\"}}', '2025-08-19 23:08:09', 2, '::1'),
(3, 1, 'products', 'update', '{\"product_id\":1,\"version\":2,\"before\":{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"after\":{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":2,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664716,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"diff\":{\"version\":{\"before\":1,\"after\":2},\"updatedOn\":{\"before\":1755664689,\"after\":1755664716}},\"meta\":{\"slug\":\"dining-table-top-sku-2006\",\"sku\":\"SKU-2006\"}}', '2025-08-19 23:08:36', 1, '::1'),
(4, 1, 'products', 'update', '{\"product_id\":2,\"version\":2,\"before\":{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"after\":{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":2,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664716,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null},\"diff\":{\"version\":{\"before\":1,\"after\":2},\"updatedOn\":{\"before\":1755664689,\"after\":1755664716}},\"meta\":{\"slug\":\"resin-epoxy-table-top-sku-2005\",\"sku\":\"SKU-2005\"}}', '2025-08-19 23:08:36', 2, '::1'),
(5, 1, 'products', 'update', '{\"product_id\":2,\"version\":3,\"before\":{\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"category_id\":2,\"status\":0,\"mrp\":199.99,\"sale_price\":159.99,\"sku\":\"SKU-2005\",\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"description\":\"Elegant vase made from black onyx stone\",\"images\":[],\"version\":2},\"after\":{\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top\",\"category_id\":2,\"status\":0,\"mrp\":199.99,\"sale_price\":159.99,\"sku\":\"SKU-2005\",\"stock_count\":25,\"featured\":0,\"meta_title\":\"test 1\",\"meta_description\":\"test 1\",\"description\":\"<p>Elegant vase made from black onyx stone<\\/p>\",\"images\":[\"public\\/upload\\/product\\/resin-epoxy-table-top\\/resin-epoxy-table-top-68a5523c7413d.jpg\"]},\"diff\":{\"slug\":{\"old\":\"resin-epoxy-table-top-sku-2005\",\"new\":\"resin-epoxy-table-top\"},\"meta_title\":{\"old\":null,\"new\":\"test 1\"},\"meta_description\":{\"old\":null,\"new\":\"test 1\"},\"description\":{\"old\":\"Elegant vase made from black onyx stone\",\"new\":\"<p>Elegant vase made from black onyx stone<\\/p>\"},\"images\":{\"old\":[],\"new\":[\"public\\/upload\\/product\\/resin-epoxy-table-top\\/resin-epoxy-table-top-68a5523c7413d.jpg\"]},\"version\":{\"old\":2,\"new\":null}},\"meta\":{\"slug\":\"resin-epoxy-table-top\",\"sku\":\"SKU-2005\"}}', '2025-08-19 23:12:36', 2, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `uniqueId` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `createdOn` int(11) DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updatedOn` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `isTrashed` int(11) NOT NULL DEFAULT 0,
  `trashedOn` int(11) DEFAULT NULL,
  `trashedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`uniqueId`, `name`, `slug`, `createdOn`, `createdBy`, `updatedOn`, `updatedBy`, `isTrashed`, `trashedOn`, `trashedBy`) VALUES
(1, 'Tables', 'tables', 1755664689, 1, 1755664689, 1, 0, NULL, NULL),
(2, 'Vases', 'vases', 1755664689, 1, 1755664689, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `uniqueId` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `stock_count` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(256) DEFAULT NULL,
  `meta_description` varchar(256) DEFAULT NULL,
  `version` int(11) DEFAULT 1,
  `createdOn` int(11) DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updatedOn` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `isTrashed` int(11) NOT NULL DEFAULT 0,
  `trashedOn` int(11) DEFAULT NULL,
  `trashedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`uniqueId`, `name`, `slug`, `description`, `mrp`, `sale_price`, `sku`, `category_id`, `images`, `status`, `stock_count`, `featured`, `meta_title`, `meta_description`, `version`, `createdOn`, `createdBy`, `updatedOn`, `updatedBy`, `isTrashed`, `trashedOn`, `trashedBy`) VALUES
(1, 'Dining table top', 'dining-table-top-sku-2006', 'Stylish coffee table crafted from premium white marble', 799.99, 649.99, 'SKU-2006', 1, NULL, 1, 10, 0, NULL, NULL, 2, 1755664689, 1, 1755664716, 1, 0, NULL, NULL),
(2, 'resin epoxy table top', 'resin-epoxy-table-top', '<p>Elegant vase made from black onyx stone</p>', 199.99, 159.99, 'SKU-2005', 2, '[\"public\\/upload\\/product\\/resin-epoxy-table-top\\/resin-epoxy-table-top-68a5523c7413d.jpg\"]', 0, 25, 0, 'test 1', 'test 1', 3, 1755664689, 1, 1755664956, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_versions`
--

CREATE TABLE `product_versions` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `version` int(11) NOT NULL,
  `data_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data_json`)),
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_versions`
--

INSERT INTO `product_versions` (`id`, `product_id`, `version`, `data_json`, `created_by`, `created_at`) VALUES
(1, 1, 1, '{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null}', 1, '2025-08-20 04:38:09'),
(2, 2, 1, '{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":1,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664689,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null}', 1, '2025-08-20 04:38:09'),
(3, 1, 2, '{\"uniqueId\":1,\"name\":\"Dining table top\",\"slug\":\"dining-table-top-sku-2006\",\"description\":\"Stylish coffee table crafted from premium white marble\",\"mrp\":\"799.99\",\"sale_price\":\"649.99\",\"sku\":\"SKU-2006\",\"category_id\":1,\"images\":null,\"status\":1,\"stock_count\":10,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":2,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664716,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null}', 1, '2025-08-20 04:38:36'),
(4, 2, 2, '{\"uniqueId\":2,\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top-sku-2005\",\"description\":\"Elegant vase made from black onyx stone\",\"mrp\":\"199.99\",\"sale_price\":\"159.99\",\"sku\":\"SKU-2005\",\"category_id\":2,\"images\":null,\"status\":0,\"stock_count\":25,\"featured\":0,\"meta_title\":null,\"meta_description\":null,\"version\":2,\"createdOn\":1755664689,\"createdBy\":1,\"updatedOn\":1755664716,\"updatedBy\":1,\"isTrashed\":0,\"trashedOn\":null,\"trashedBy\":null}', 1, '2025-08-20 04:38:36'),
(5, 2, 3, '{\"name\":\"resin epoxy table top\",\"slug\":\"resin-epoxy-table-top\",\"category_id\":2,\"status\":0,\"mrp\":199.99,\"sale_price\":159.99,\"sku\":\"SKU-2005\",\"stock_count\":25,\"featured\":0,\"meta_title\":\"test 1\",\"meta_description\":\"test 1\",\"description\":\"<p>Elegant vase made from black onyx stone<\\/p>\",\"images\":[\"public\\/upload\\/product\\/resin-epoxy-table-top\\/resin-epoxy-table-top-68a5523c7413d.jpg\"],\"version\":3}', 1, '2025-08-20 04:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people`
--

CREATE TABLE `tbl_people` (
  `uniqueId` bigint(20) NOT NULL,
  `active` varchar(1) DEFAULT '1',
  `fName` varchar(120) DEFAULT NULL,
  `lName` varchar(120) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `mail` varchar(120) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `number` varchar(15) DEFAULT NULL,
  `createdOn` int(11) DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updatedOn` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `isTrashed` int(11) NOT NULL DEFAULT 0,
  `trashedOn` int(11) DEFAULT NULL,
  `trashedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_people`
--

INSERT INTO `tbl_people` (`uniqueId`, `active`, `fName`, `lName`, `photo`, `mail`, `password`, `number`, `createdOn`, `createdBy`, `updatedOn`, `updatedBy`, `isTrashed`, `trashedOn`, `trashedBy`) VALUES
(1, '1', 'Pankaj', 'pankaj', 'http://localhost/admin/public/upload/user/favicon.png', 'admin@gmail.com', '$2y$12$0bz3zjabAvqW1SUbdkgTu.Re3xZiTLkb97ygPvaXUdqIJR3VrUTkq', '08882263480', 1751699419, NULL, 1755665760, 1, 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`uniqueId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`uniqueId`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_versions`
--
ALTER TABLE `product_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_product_version` (`product_id`,`version`),
  ADD KEY `idx_versions_product_time` (`product_id`,`created_at`);

--
-- Indexes for table `tbl_people`
--
ALTER TABLE `tbl_people`
  ADD PRIMARY KEY (`uniqueId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `uniqueId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `uniqueId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_versions`
--
ALTER TABLE `product_versions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_people`
--
ALTER TABLE `tbl_people`
  MODIFY `uniqueId` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`uniqueId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
