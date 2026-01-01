-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql103.infinityfree.com
-- Generation Time: Jan 01, 2026 at 01:01 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38161163_brandx`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Simon Ngugi ', 'www@gmail.com', '$2y$10$jy7E0vRgL/p46LllDY5SUOIGwz2.J47PbvHL7A8V4k4kYSehoPZWm', '2024-12-30 21:49:47'),
(2, 'Jeff', 'jeff@gmail.com', '$2y$10$TL1dTqKo7xPA4v9IgR0.EeDJy8Nm.2aIC4dXbKYMG8dUFjNqQ0kCu', '2024-12-30 23:43:06'),
(3, 'Simon Ngugi ', 'sngugi175@gmail.com', '$2y$10$mkHOLAI9uKDo.PoPd1i1.e/AHgtrXnDPy0dlrDfYJL4CZTZmeIwLO', '2024-12-31 13:31:02'),
(4, 'Michael ngugi', 'freekyone7254@gmail.com', '$2y$10$iNR6NQbe/v8nnzfzJFn2Au4cPn036PY9AcU3XSdYatu1ly5vH.Vgq', '2025-01-03 04:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_color` varchar(255) NOT NULL,
  `product_size` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_color`, `product_size`, `quantity`, `created_at`) VALUES
(2, 11, '237', 'Green', '39', 1, '2025-01-23 19:27:03'),
(21, 8, '285', 'Blue', '37', 1, '2025-01-27 23:46:03'),
(41, 39, '193', 'Pink', '36', 1, '2025-01-29 16:36:15'),
(57, 44, '177', 'Blue/white', '38', 1, '2025-01-31 14:32:47'),
(59, 46, '222', 'White', '40', 1, '2025-01-31 14:59:04'),
(63, 47, '62', 'brown', '36', 1, '2025-01-31 15:23:37'),
(105, 10, '37', 'Black', '41', 1, '2025-06-26 20:30:28'),
(106, 10, '57', 'brown', '36', 1, '2025-06-26 20:30:55'),
(107, 10, '375', 'Creame', '40', 1, '2025-06-26 20:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 0, 'Mime Mimensns', 'sngugi175@gmail.com', '$2y$10$nIaTgB8w7ltcU0EguqkQS./WFRvR7k0L6Hz5e/vUi5HC9poQ3Jice', '077856685', 'Dcgg', '2025-07-24 17:22:28', '2025-12-21 06:19:17'),
(2, 0, 'Mime Mimensns', 'kamau2222@gmail.com', '$2y$10$mHOkWn.LVjqtuEyTNj5AJOE72atNBjRUS4mU.GflmLxwH9vXWfQaa', '077856685', 'Dcgg', '2025-07-24 12:25:56', '2025-12-21 06:19:17'),
(3, 0, 'Mime Mimensns', 'sngh@gmail.com', '$2y$10$.J9q93Yh19/3lrFeKS0VJeiCsTuLhuDkVqd3RMpsB5kdlRRB5PFki', '077856685', 'Dcgg', '2025-07-24 17:36:58', '2025-12-21 06:19:17'),
(4, 0, 'Mime Mimensns', 'www@gmail.com', '$2y$10$UuCfXxPADztj188AubfWou.cIy/PM0xm6KCq6J2imP73L2C.GT7C6', '077856685', 'Dcgg', '2025-07-25 10:37:04', '2025-12-21 06:19:17'),
(5, 0, 'Mime Mimensns', 'yegon@datany.online', '$2y$10$IANrU1ZqcIDblI0yX.TBcOmmucdNXpbzp7yM2dlZeC5N5R16JocbK', '077856685', 'Dcgg', '2025-08-04 15:06:03', '2025-12-21 06:19:17'),
(6, 0, 'Mime Mimensns', 'mumbiimelda540@gmail.com', '$2y$10$saS4eySPViHI2RGBhS6hZeGI3n5CtRXGA0LDcDCLWrBAVFNJAj9TC', NULL, NULL, '2025-09-01 18:41:24', '2025-12-21 06:19:17'),
(7, 0, 'Mime Mimensns', 'mumbiimela540@gmail.com', '$2y$10$G5SS13XevZbyYkvCF65ACOyW3h/rNuHIqVfr5USHu/1SFf47MFa7O', NULL, NULL, '2025-09-17 11:41:47', '2025-12-21 06:19:17'),
(8, 0, 'Mime Mimensns', 'imeldasong123@gmail.com', '$2y$10$m6t5c/TO3cRtybdybI4e5u.RK.4NzebYj6A2k4I6zUWtIvoEylN8K', NULL, NULL, '2025-09-19 13:05:18', '2025-12-21 06:19:17'),
(9, 0, 'Mime Mimensns', 'faruoqtest1@gmail.com', '$2y$10$VJjL57Wjg1pqfIhyhsl60eyWyj6EXRUZLWXUnZ4GKybVKUtvO.zt6', NULL, NULL, '2025-11-30 22:34:14', '2025-12-21 06:19:17'),
(10, 0, 'Mime Mimensns', 'faruoqmuhammed@gmail.com', '$2y$10$SGtRCzsNATbBq.2UlSxBr.oy1rSWb8ZQqTXci0eFad3F7q8gQ9EoO', NULL, NULL, '2025-12-01 03:12:23', '2025-12-21 06:19:17'),
(11, 0, 'Mime Mimensns', 'freekyone7254@gmail.com', '$2y$10$xXwrCd5XsR10EAfYL0YTxOL2MHPwH/G.NUK6nMd7ue7kjJMy66OA2', NULL, NULL, '2025-12-15 10:44:30', '2025-12-21 06:19:17');

-- --------------------------------------------------------

--
-- Table structure for table `featured_products`
--

CREATE TABLE `featured_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `featured_products`
--

INSERT INTO `featured_products` (`id`, `product_id`) VALUES
(64, 63),
(66, 400),
(57, 222),
(61, 34),
(67, 343),
(65, 337),
(60, 264),
(41, 249),
(62, 227),
(51, 163),
(58, 41),
(56, 266),
(69, 345),
(68, 353),
(70, 363);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender` enum('customer','admin') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(4) NOT NULL,
  `conversation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `sender`, `timestamp`, `read_status`, `conversation_id`) VALUES
(614, 10, 'Starting a conversation', 'customer', '2025-01-24 08:22:03', 0, 0),
(615, 10, 'Starting a conversation', 'customer', '2025-01-24 08:22:05', 0, 0),
(616, 10, 'Starting a conversation', 'customer', '2025-01-24 08:22:06', 0, 0),
(617, 10, 'Starting a conversation', 'customer', '2025-01-24 08:22:08', 0, 0),
(618, 10, 'Starting a conversation', 'customer', '2025-01-24 08:23:10', 0, 0),
(619, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:45', 0, 0),
(620, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:49', 0, 0),
(621, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:49', 0, 0),
(622, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:50', 0, 0),
(623, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:50', 0, 0),
(624, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:50', 0, 0),
(625, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:50', 0, 0),
(626, 10, 'Starting a conversation', 'customer', '2025-01-24 08:28:50', 0, 0),
(627, 10, 'Starting a conversation', 'customer', '2025-01-24 08:33:47', 0, 0),
(628, 10, 'Starting a conversation', 'customer', '2025-01-24 08:35:23', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_payments`
--

CREATE TABLE `mpesa_payments` (
  `id` int(11) NOT NULL,
  `merchant_request_id` varchar(255) NOT NULL,
  `checkout_request_id` varchar(255) NOT NULL,
  `result_code` int(11) NOT NULL,
  `result_desc` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `mpesa_receipt_number` varchar(255) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `status` enum('pending','completed','failed','canceled','reversed','refunded') NOT NULL,
  `payment_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mpesa_payments`
--

INSERT INTO `mpesa_payments` (`id`, `merchant_request_id`, `checkout_request_id`, `result_code`, `result_desc`, `amount`, `mpesa_receipt_number`, `transaction_date`, `phone_number`, `status`, `payment_id`, `created_at`, `updated_at`) VALUES
(22, 'bbcd-4a89-bd1a-6ecdc639893b1521194', 'ws_CO_14012025234102197112022716', 0, '', '1.00', '', '0000-00-00 00:00:00', '254112022716', 'pending', 1736887263, '2025-01-14 20:41:03', '2025-01-14 20:41:03'),
(23, 'b54f-471d-93d9-f7f3bf3f7c0e1523577', 'ws_CO_14012025234436577114930814', 0, '', '1.00', '', '0000-00-00 00:00:00', '254114930814', 'pending', 1736887413, '2025-01-14 20:43:33', '2025-01-14 20:43:33');

-- --------------------------------------------------------

--
-- Table structure for table `mycheckout`
--

CREATE TABLE `mycheckout` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'on_delivery',
  `order_total` decimal(10,2) NOT NULL,
  `order_items` text NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mycheckout`
--

INSERT INTO `mycheckout` (`id`, `client_id`, `session_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `payment_method`, `order_total`, `order_items`, `status`, `created_at`) VALUES
(1, 0, 'dc9b59f38b440c79d2d5acd85713eda2', 'Simon Ngugi', 'sngugi175@gmail.com', '0777992666', 'Kiambu', 'on_delivery', '21300.00', '[{\"product_id\":\"458\",\"product_name\":\"Cork sandles\\ud83d\\udd25\",\"price\":\"2500\",\"image\":\"686975ab702a6.jpeg\",\"quantity\":1},{\"product_id\":\"337\",\"product_name\":\"LACOSTE OFFICIALS\",\"price\":\"3500\",\"image\":\"67b75d129bd76.jpg\",\"quantity\":1},{\"product_id\":\"407\",\"product_name\":\"Popcaan clarks\",\"price\":\"3200\",\"image\":\"682f73200946c.jpeg\",\"quantity\":2},{\"product_id\":\"381\",\"product_name\":\"Af1 aquarium \",\"price\":\"2800\",\"image\":\"68151cf52d6c7.jpeg\",\"quantity\":1},{\"product_id\":\"417\",\"product_name\":\"Af \\ud83c\\udf35 plant flea\",\"price\":\"3300\",\"image\":\"6838ccc569c3c.jpeg\",\"quantity\":1},{\"product_id\":\"380\",\"product_name\":\"Addidas casuals\",\"price\":\"2800\",\"image\":\"68151c5c0db4a.jpeg\",\"quantity\":1}]', 'pending', '2025-07-24 08:45:38'),
(2, 0, 'dc9b59f38b440c79d2d5acd85713eda2', 'Dave', 'dave@gmail.com', '0777992666', 'Kangema', 'on_delivery', '6499.00', '[{\"product_id\":\"463\",\"product_name\":\"NIKE ZOOM\",\"price\":\"2999\",\"image\":\"686fdf3407bbe.jpeg\",\"quantity\":1},{\"product_id\":\"462\",\"product_name\":\"NB 1000 \\ud83e\\uded2\\ud83d\\udc9a\",\"price\":\"3500\",\"image\":\"686d54dfe69e1.jpeg\",\"quantity\":1}]', 'pending', '2025-07-24 08:51:50'),
(3, 0, 'cc981883d2bc1fa3714b07b6dd540a0e', 'jbjh', 'jbbj@kniih', '9098998098', 'hjhjiuhjuh', 'on_delivery', '62499.00', '[{\"product_id\":\"439\",\"product_name\":\"Timbs LV\\u2728\",\"price\":\"3800\",\"image\":\"6850516cb2d0f.jpeg\",\"quantity\":15},{\"product_id\":\"458\",\"product_name\":\"Cork sandles\\ud83d\\udd25\",\"price\":\"2500\",\"image\":\"686975ab702a6.jpeg\",\"quantity\":1},{\"product_id\":\"454\",\"product_name\":\"Clogs multi colours\\u2728\",\"price\":\"2999\",\"image\":\"686500d61bebe.jpeg\",\"quantity\":1}]', 'pending', '2025-07-24 08:54:55'),
(4, 0, '757ff796d2c068392886ece083836572', 'Oil filter', 'sngh@gmail.com', '9876543', 'kjhgfdxcvbn', 'on_delivery', '21596.00', '[{\"product_id\":\"338\",\"product_name\":\"NIKE PORTAL\",\"price\":\"3200\",\"image\":\"67c6cb2519d46.jpg\",\"quantity\":3},{\"product_id\":\"427\",\"product_name\":\"Puma xxl \\ud83d\\udc99\",\"price\":\"2999\",\"image\":\"683f4722ad747.jpeg\",\"quantity\":3},{\"product_id\":\"415\",\"product_name\":\"Cortez \\ud83d\\udc9a\",\"price\":\"2999\",\"image\":\"6838c67b00091.jpeg\",\"quantity\":1}]', 'pending', '2025-07-24 17:46:03'),
(5, 4, '9b1cf2c75f2322e984b5f92c11c2ce27', 'Simon Ngugi', 'www@gmail.com', '0768924330', 'Ryruurur', 'on_delivery', '38099.00', '[{\"product_id\":\"439\",\"product_name\":\"Timbs LV\\u2728\",\"price\":\"3800\",\"image\":\"6850516cb2d0f.jpeg\",\"quantity\":1},{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1},{\"product_id\":\"398\",\"product_name\":\"Nocta drake\",\"price\":\"3300\",\"image\":\"681a4dd437778.jpeg\",\"quantity\":2},{\"product_id\":\"346\",\"product_name\":\"NOCTA BLUEISH\",\"price\":\"3500\",\"image\":\"67e648f7a0300.jpeg\",\"quantity\":1},{\"product_id\":\"375\",\"product_name\":\"J6 mvp \\ud83d\\udc51\",\"price\":\"2999\",\"image\":\"681514d419454.jpeg\",\"quantity\":1},{\"product_id\":\"445\",\"product_name\":\"Gutta green \",\"price\":\"3200\",\"image\":\"68584886d6cda.jpeg\",\"quantity\":3},{\"product_id\":\"355\",\"product_name\":\"CLARKS\",\"price\":\"3200\",\"image\":\"6803b22786f64.jpeg\",\"quantity\":3}]', 'pending', '2025-07-25 10:37:25'),
(6, 0, 'a3be532b48ef45f21577d9dfafadb81f', 'Eygb', 'www@gmail.com', '09865432', 'Hhbdm', 'on_delivery', '7199.00', '[{\"product_id\":\"444\",\"product_name\":\"Vans\\u2728\",\"price\":\"1700\",\"image\":\"6855b3844dec5.jpeg\",\"quantity\":1},{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1},{\"product_id\":\"400\",\"product_name\":\"Shocks silver\\u2728\",\"price\":\"3499\",\"image\":\"681a4ec39290a.jpeg\",\"quantity\":1}]', 'pending', '2025-08-02 15:33:41'),
(7, 5, 'eb092bd4160c26606c71ca3125973983', 'Gideon Kipkorir Yegon', 'yegon@datany.online', '+254712269086', 'Nairobi', 'on_delivery', '3499.00', '[{\"product_id\":\"400\",\"product_name\":\"Shocks silver\\u2728\",\"price\":\"3499\",\"image\":\"681a4ec39290a.jpeg\",\"quantity\":1}]', 'pending', '2025-08-04 15:06:22'),
(8, 0, 'b1f082c1c73e6f8cb48765fd788d0613', 'Simon Ngugi', 'www@gmail.com', '0768924330', 'Kiambu', 'on_delivery', '2000.00', '[{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1}]', 'pending', '2025-08-26 17:25:49'),
(9, 0, '4b2362e183e6c873088745a67d8e689b', 'Www', 'www@gmail.com', '077856685', 'Dcgg', 'on_delivery', '27799.00', '[{\"product_id\":\"373\",\"product_name\":\"ADDIDAS GAZELLE\",\"price\":\"3400\",\"image\":\"6815132f893f5.jpeg\",\"quantity\":1},{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":9},{\"product_id\":\"464\",\"product_name\":\"Samba messi \\ud83e\\udd0e\",\"price\":\"2999\",\"image\":\"68700baf9c813.jpeg\",\"quantity\":1},{\"product_id\":\"449\",\"product_name\":\"Dior B30 \",\"price\":\"3400\",\"image\":\"685d9bc48da04.jpeg\",\"quantity\":1}]', 'pending', '2025-08-28 08:53:42'),
(10, 7, '57c6ccd2547d5bcda6ca9a44a6f90b08', 'imelda', 'mumbiimela540@gmail.com', '0704381122', 'kawangware', 'on_delivery', '3200.00', '[{\"product_id\":\"466\",\"product_name\":\"ALEXANDER MCQUEEN\",\"price\":\"3200\",\"image\":\"6877b83a6f98d.jpg\",\"quantity\":1}]', 'pending', '2025-09-17 11:42:12'),
(11, 8, '1092f56c881089f98b773dee43967253', 'Imelda', 'imeldasong123@gmail.com', '0704381122', 'Kawangware', 'on_delivery', '2000.00', '[{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1}]', 'pending', '2025-09-19 13:05:53'),
(12, 9, 'c75eb78557b85720bf01da563c3140de', 'Faruoq Muhammed Masika', 'faruoqtest1@gmail.com', '0701891004', 'nairobi', 'on_delivery', '11899.00', '[{\"product_id\":\"466\",\"product_name\":\"ALEXANDER MCQUEEN\",\"price\":\"3200\",\"image\":\"6877b83a6f98d.jpg\",\"quantity\":2},{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1},{\"product_id\":\"400\",\"product_name\":\"Shocks silver\\u2728\",\"price\":\"3499\",\"image\":\"681a4ec39290a.jpeg\",\"quantity\":1}]', 'pending', '2025-11-30 22:34:49'),
(13, 10, 'c75eb78557b85720bf01da563c3140de', 'Faruoq Muhammed', 'faruoqmuhammed@gmail.com', '0701891004', 'Kabarnet', 'on_delivery', '3200.00', '[{\"product_id\":\"466\",\"product_name\":\"ALEXANDER MCQUEEN\",\"price\":\"3200\",\"image\":\"6877b83a6f98d.jpg\",\"quantity\":1}]', 'pending', '2025-12-01 03:13:32'),
(14, 11, '72c9c4c51c6c85c43afa4fee412399a5', 'm', 'freekyone7254@gmail.com', '0773184426', 'mnn', 'on_delivery', '2000.00', '[{\"product_id\":\"468\",\"product_name\":\"FLUFFY SANDALS\",\"price\":\"2000\",\"image\":\"6877e7bf51b18.jpg\",\"quantity\":1}]', 'pending', '2025-12-15 10:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders_made`
--

CREATE TABLE `orders_made` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `location` text NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_names` varchar(255) NOT NULL,
  `product_color` varchar(50) NOT NULL,
  `product_size` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_made`
--

INSERT INTO `orders_made` (`id`, `name`, `email`, `phone`, `location`, `total_price`, `order_date`, `product_names`, `product_color`, `product_size`) VALUES
(3, 'Job kimani', 'www@gmail.com', '0774700668', 'Turitu', '2999', '2025-01-26 12:27:53', 'J4 psg', 'White/purple', '43'),
(6, 'Ryan kiprotich ', 'ryanadrian920@gmail.com', '0719560631', 'Diomond plaza ', '2900', '2025-01-28 10:43:16', 'Tn', 'White', '40'),
(12, 'Allan', 'allanmwangi800@gmail.com', '0759710461', 'KINOO, LEESTAR SUPERMARKET', '2999', '2025-03-13 21:02:08', 'Jordan 1 low', 'Pink', '36');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `product_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`, `product_name`) VALUES
(5, 3, 258, 1, '2999', ''),
(9, 6, 222, 1, '2900', ''),
(12, 12, 194, 1, '2999', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `video_thumbnail` varchar(255) DEFAULT NULL,
  `price_ksh` decimal(10,0) NOT NULL,
  `available_sizes` varchar(100) NOT NULL,
  `available_colors` varchar(255) NOT NULL,
  `units_available` int(11) NOT NULL,
  `category` enum('Nike','Adidas','Puma','Converse','Fila','Jordan','Vans','Gucci','Timberland','Lv','Newbalance','Other') NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `secondary_image` varchar(255) NOT NULL,
  `secondary_videos` text DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `sold` int(11) DEFAULT NULL,
  `gender_category` varchar(50) DEFAULT 'all',
  `stock_status` enum('in_stock','low_stock','out_of_stock') DEFAULT 'in_stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `image`, `video`, `video_thumbnail`, `price_ksh`, `available_sizes`, `available_colors`, `units_available`, `category`, `main_image`, `secondary_image`, `secondary_videos`, `rating`, `sold`, `gender_category`, `stock_status`) VALUES
(337, 'LACOSTE OFFICIALS', 'lacoste-officials', 'Crafted with precision and timeless appeal, the Lacoste Officials combine premium materials with everyday comfort. Featuring a smooth leather or high-quality textile upper, these sneakers showcase the iconic crocodile logo for instant recognition. The low-top design offers a clean, versatile profile, while the cushioned footbed and padded collar ensure long-lasting wear. A sturdy rubber outsole provides excellent traction, making them perfect for both casual outings and smart-casual looks. Balancing elegance and functionality, theyâ€™re an essential addition to any modern wardrobe.', '67b75d129bd76.jpg', NULL, NULL, '3500', '40,41,42,43,44,45', 'Black/gold,grey/gold,brown/gold,cream/gold', 0, 'Puma', '', '[\"67b75d129c044.jpg\",\"67b75d129c317.jpg\",\"67b75d129c5d5.jpg\"]', NULL, NULL, 0, 'all', 'out_of_stock'),
(338, 'NIKE PORTAL', 'nike-portal', 'Designed with a futuristic aesthetic, the Nike Portal merges bold style with all-day comfort. Its layered construction, sculpted midsole, and streamlined profile give it a distinctive edge. Soft cushioning and breathable materials enhance wearability, while modern details add a statement finish. Ideal for everyday wear, it stands out with a tech-inspired silhouette that blends innovation and streetwear flair. Durable outsoles offer reliable grip on various surfaces. The snug fit hugs the foot securely without sacrificing flexibility. With a fashion-forward look and practical performance, the Nike Portal elevates any urban outfit.\r\n\r\n\r\n\r\n\r\n\r\n', '67c6cb2519d46.jpg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Blue,red,black,white,green,yellow', 500, 'Nike', '', '[\"67c6cb251b18f.jpg\",\"67c6cb251b434.jpg\",\"67c6cb251b9ca.jpg\",\"67c6cb251be4e.jpg\",\"67c6cb251c0c6.jpg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(343, 'Airforce Cactus Plant', 'airforce-cactus-plant', 'The Air Force 1 Cactus Plant Flea  reimagines the iconic silhouette with a bold, eccentric twist. Featuring exaggerated details, oversized lettering, and playful asymmetry, it blends streetwear energy with artistic flair. The upper is crafted from premium leather or canvas, often paired with unique textures or reflective materials. Distinctive branding, layered designs, and removable elements add depth and customization. With classic AF1 cushioning and durable rubber soles, it maintains comfort while delivering standout visuals. A true fusion of style and expression, perfect for sneaker enthusiasts and fashion-forward individuals.', '67e646d0dc43c.jpeg', NULL, NULL, '3500', '40,41,42,43,44,45', 'White', 120, 'Nike', '', '[]', NULL, NULL, NULL, 'all', 'in_stock'),
(345, 'AIRFORCE NOCTA', 'airforce-nocta', 'The Air Force 1 NOCTA offers a sleek, modern take on the classic silhouette, blending performance with streetwear style. Crafted with premium materials, it features smooth leather and subtle overlays that emphasize clean lines. The design incorporates muted tones with reflective details, inspired by nocturnal urban environments. Enhanced cushioning and a durable rubber outsole provide comfort and reliable traction for all-day wear. With minimalist branding and a streamlined profile, this sneaker effortlessly balances function and fashion, making it a versatile choice for both casual and elevated looks.', '67e6483583085.jpeg', NULL, NULL, '3500', '40,41,42,43,44,45', 'White', 100, 'Nike', '', '[\"67e6483583339.jpeg\",\"67e64835835bb.jpeg\",\"67e64835837f0.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(346, 'NOCTA BLUEISH', 'nocta-blueish', 'Designed with performance and street style in mind, the NOCTA Blueish merges sleek lines with functional comfort. The upper features a mix of mesh and synthetic overlays, offering breathability and support. Reflective accents and bold blue tones give it a standout aesthetic, inspired by nocturnal cityscapes. A responsive midsole delivers cushioned steps, while the rubber outsole ensures solid grip and traction. Built for versatility, itâ€™s a go-to sneaker for everyday wear, training, or casual styling. The snug construction keeps the foot secure, combining athletic tech with a modern urban vibe.', '67e648f7a0300.jpeg', NULL, NULL, '3500', '40,41,42,43,44,45', 'baby blue', 100, 'Nike', '', '[\"67e648f7a064b.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(352, 'JORDAN 3 BLAVIN', 'jordan-3-blavin', 'The Jordan 3 Blavin combines classic Air Jordan heritage with a modern, fresh colorway. Featuring a sleek leather upper with striking blue accents, this sneaker highlights the iconic elephant print overlays and visible Air unit in the heel for lasting comfort. The padded collar and tongue provide support, while the durable rubber outsole offers excellent traction and grip. Its timeless silhouette makes it a versatile addition to any sneaker collection, blending bold style with legendary performance.', '6803b019623dd.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Yellow/white', 200, 'Jordan', '', '[\"6803b01962679.jpeg\",\"6803b01962873.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(353, 'Keens Multi Colours', 'keens-multi-colours', 'Keens are known for their rugged durability and outdoor-ready design, perfect for hiking and everyday adventures. Featuring a sturdy leather or synthetic upper combined with a signature toe-protecting rubber rand, they offer excellent foot protection. The cushioned midsole and contoured footbed provide comfort and support for long wear, while the grippy rubber outsole ensures reliable traction on various terrains. With a blend of function and style, Keens are ideal for those who value both performance and casual, sporty looks.', '6803b0ee89bfd.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Black,cream,blue,yellow,green', 500, '', '', '[\"6803b0ee8a112.jpeg\",\"6803b0ee8a59a.jpeg\",\"6803b0ee8a946.jpeg\",\"6803b0ee91257.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(354, 'Airforce Tweed Corduroy', 'airforce-tweed-corduroy', 'The Air Force Tweed Corduroy combines classic Nike Air Force 1 styling with unique textured materials for a fresh, stylish update. Featuring a premium corduroy upper with tweed accents, this sneaker offers a cozy, vintage-inspired look that stands out. The cushioned midsole delivers all-day comfort, while the durable rubber outsole provides excellent traction and support. With its blend of heritage design and modern fabric details, this shoe is perfect for adding subtle sophistication to any casual outfit.', '6803b18a875a6.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Brown/blue', 120, 'Nike', '', '[\"6803b18a87afd.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(355, 'CLARKS', 'clarks', 'Clarks shoes are renowned for their timeless designs, exceptional comfort, and durable craftsmanship. Made with premium leather and suede materials, they blend classic style with modern functionality. Known for their ergonomic foot beds and cushioned soles, Clarks offer all-day support, making them ideal for both casual and formal wear. Their versatile designs range from smart dress shoes to relaxed casuals, ensuring a refined yet comfortable fit for every occasion.', '6803b22786f64.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Black,brown,grey', 5000, '', '', '[\"6803b227871c5.jpeg\",\"6803b227873bf.jpeg\",\"6803b227875c5.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(356, 'Airforce 1 stussy', 'airforce-1-stussy', 'The AF1 Stussy  blends the classic Air Force 1 silhouette with premium materials and subtle design details. Featuring a smooth  leather upper, this edition offers a clean and sophisticated look. Signature Stussy branding adds a streetwear edge, while the traditional AF1 sole provides reliable cushioning and traction. With a timeless aesthetic and comfortable fit, this sneaker is perfect for everyday wear and elevates any casual outfit with understated style.', '6803b2ec3f0aa.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Black,cream', 500, 'Nike', '', '[\"6803b2ec3f32a.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(361, 'AF1 REIGNING CHAMP', 'af1-reigning-champ', 'The Air Force 1 Reigning Champ edition blends Nikeâ€™s iconic silhouette with premium craftsmanship from the Canadian brand. Featuring soft suede or leather uppers, minimalist tonal stitching, and subtle branding, it delivers elevated simplicity. The classic AF1 sole ensures comfort and durability, while the muted, clean finish reflects Reigning Champâ€™s signature aesthetic. A refined take on a streetwear staple.', '6803b7521bbf4.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Grey', 50, 'Nike', '', '[\"6803b7521bea5.jpeg\",\"6803b7521c0df.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(362, 'SB PURPLE PURSE', 'sb-purple-purse', 'The SB Dunk Low â€œPurple Pulseâ€ brings a bold twist to the classic skate silhouette with soft suede overlays and a unique cloud-like purple and white pattern across the upper. Its vintage-inspired tones are paired with durable construction, a padded tongue, and responsive Zoom Air cushioning for comfort on and off the board. A grippy rubber outsole ensures traction, while clean paneling and muted branding add a fresh, understated finish to this standout pair.', '6803b80af0f89.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Purple', 100, 'Nike', '', '[\"6803b80af1290.jpeg\",\"6803b80af14a7.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(363, 'Jordan 3 All Star', 'jordan-3-all-star', 'The Air Jordan 3 â€œAll-Starâ€ combines premium craftsmanship with heritage design, featuring a clean leather upper accented by elephant print overlays on the heel and toe. Designed for the NBA All-Star Weekend, it blends street style with on-court flair. Visible Air cushioning offers comfort, while the padded collar and durable rubber outsole ensure reliable support and traction. A sleek, versatile colorway makes it perfect for both athletic and casual wear.', '6803b8bbdb7bb.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Brown', 500, 'Jordan', '', '[\"6803b8bbdbaa2.jpeg\",\"6803b8bbdbce0.jpeg\",\"6803b8bbdbf49.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(365, 'NEW BALANCE 740', 'new-balance-740', 'The New Balance 740 blends retro basketball style with modern comfort. Featuring a high-top silhouette, it offers ankle support and a bold \'80s-inspired design. The upper combines durable leather and mesh for breathability and structure. A cushioned midsole and padded tongue provide comfort, while the rubber outsole delivers traction and grip. Ideal for everyday wear or sporty styling, the NB 740 stands out with its timeless athletic vibe.', '681508a2d6ff2.jpeg', NULL, NULL, '3499', '36,37,38,39,40,41,42,43,44,45', 'Grey/baby blue', 500, 'Newbalance', '', '[\"681508a2d7c19.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(366, 'AIRMAX 97 ', 'airmax-97-', 'The Air Max 97  features Nikeâ€™s signature ripple design with a sleek, feminine twist. Its upper blends mesh and synthetic materials in soft pink tones for breathability and style. A full-length visible Air unit delivers cushioned comfort, while the rubber outsole ensures grip and durability. With reflective accents and a streamlined silhouette, itâ€™s a standout choice for everyday wear or bold street style.', '6815099c6d542.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Pink,green,cream', 500, 'Nike', '', '[\"6815099c6d7d7.jpeg\",\"6815099c6daa5.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(367, 'JORDAN SB', 'jordan-sb', 'The Jordan SB blends iconic Jordan heritage with the functionality of a skateboarding shoe. Featuring durable suede or leather uppers, extra padding around the collar, and a responsive sole, it offers enhanced support and board feel. With classic colorways and co-branded details, it merges basketball legacy with skate culture for versatile, stylish performance both on and off the board.', '68150a75224f9.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Brown', 5000, 'Jordan', '', '[\"68150a7522c2d.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(368, 'JORDAN 4 PINK OREA', 'jordan-4-pink-orea', 'The Jordan 4 â€œPink Oreoâ€ combines the classic Air Jordan 4 design with soft pink tones and speckled white accents for a sweet yet bold look. It features a leather upper, mesh panels for breathability, and signature plastic wings and heel tab. The speckled midsole and Jumpman branding add standout detailing, while the cushioned sole ensures all-day comfort. Perfect for those who want a mix of sporty edge and feminine flair.', '68150b489ca09.jpeg', NULL, NULL, '3499', '36,37,38,39,40,41,42,43,44,45', 'Pink', 500, 'Jordan', '', '[\"68150b489cd4b.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(369, 'JORDAN 4 KAWAS', 'jordan-4-kawas', 'The Jordan 4 â€œKawaâ€ showcases a clean, modern look with soft pastel tones and premium materials. It features a smooth leather upper, breathable mesh panels, and signature Jordan 4 detailing like the plastic lace wings and heel tab. The cushioned midsole offers reliable comfort, while the rubber outsole provides traction and durability. A subtle yet stylish choice for everyday wear or standout sneaker rotations.', '68150be3c3834.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Grey', 500, 'Jordan', '', '[\"68150be3c3b31.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(371, 'CACTUS REVERSE', 'cactus-reverse', 'The Jordan 1 Low \"Cactus Jack Reverse Mocha\" blends earthy tones with signature Travis Scott styling. It features a premium suede and leather upper in mocha brown and sail white, highlighted by the iconic reversed Swoosh in white. The Cactus Jack branding appears on the tongue, heel, and insole, while the aged midsole adds a vintage vibe. With its comfortable cushioning and bold design, itâ€™s a sought-after pair for both sneakerheads and streetwear fans.', '6815119666259.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', 'Grey', 50, 'Nike', '', '[\"6815119666558.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(373, 'ADDIDAS GAZELLE', 'addidas-gazelle', 'The adidas Gazelle is a timeless classic, originally designed as a training shoe and now a streetwear staple. It features a soft suede upper with signature 3-Stripes, a T-toe overlay, and gold foil \"Gazelle\" branding. The low-profile silhouette is paired with a cushioned insole and a grippy rubber outsole for comfort and everyday wear. Its minimalist yet iconic design makes it a versatile choice for any outfit.', '6815132f893f5.jpeg', NULL, NULL, '3400', '36,37,38,39,40,41,42,43,44,45', 'baby blue,grey,black/silver stripes,brown,black/grey straps,black/white straps', 500, '', '', '[\"6815132f89755.jpeg\",\"6815132f89aff.jpeg\",\"6815132f89f13.jpeg\",\"6815132f8a32d.jpeg\",\"6815132f8a6c2.jpeg\",\"6815132f8aa46.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(374, 'Airmax 1 reactðŸ˜©', 'airmax-1-reactðÿ˜©', 'A modern take on the classic Air Max 1, featuring a lightweight React foam midsole for enhanced comfort, a breathable mesh upper, and the iconic visible Air unit for a stylish and responsive fit.', '68151416bee30.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Blue ', 500, 'Nike', '', '[\"68151416bf14b.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(375, 'J6 mvp ðŸ‘‘', 'j6-mvp-ðÿ‘‘', 'Cream-toned mid-top sneaker blending elements from the Air Jordan 6, 7, and 8, featuring a soft nubuck upper, embroidered Jumpman branding, and a translucent outsole for a refined, heritage-inspired look.', '681514d419454.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Creame', 300, 'Jordan', '', '[\"681514d4197bc.jpeg\",\"681514d419d7b.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(376, 'Af1 bold air', 'af1-bold-air', 'Bold Air Force 1 sneaker featuring a cracked red and black upper, skull graphic on the toe box, and blacked-out detailingâ€”blending edgy street style with classic AF1 elements.', '681519786d47a.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Red/black', 40, 'Nike', '', '[\"681519786d7ab.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(377, 'Clarks dior', 'clarks-dior', 'Luxurious fusion of classic Clarks design and Dior elegance, featuring premium suede or leather, crepe sole, and subtle high-fashion detailing for a refined, statement-making look.', '681519ec3b672.jpeg', NULL, NULL, '2799', '38,39,40,41,42,43,44,45', 'Black/white', 40, '', '', '[\"681519ec3b95b.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(378, 'NB 530', 'nb-530', 'Sporty retro runner with a mesh and synthetic upper, ABZORB cushioning, and a chunky soleâ€”offering classic \'90s style with modern all-day comfort.', '68151a81ba8cc.jpeg', NULL, NULL, '2799', '36,37,38,39,40,41,42,43,44,45', 'White/black', 500, 'Newbalance', '', '[\"68151a81babe9.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(379, 'Nike tnâœ¨', 'nike-tnâœ¨', 'Bold running-inspired sneaker featuring a wavy design, Tuned Air cushioning, and mesh upperâ€”delivering a mix of aggressive style, support, and street-ready comfort.', '68151b4b1fd56.jpeg', NULL, NULL, '2800', '38,39,40,41,42,43,44,45', 'Black', 30, 'Nike', '', '[\"68151b4b20046.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(380, 'Addidas casuals', 'addidas-casuals', 'Comfortable everyday footwear designed for relaxed settings, combining simple style, lightweight materials, and versatile looks suitable for both smart and laid-back outfits.', '68151c5c0db4a.jpeg', NULL, NULL, '2800', 'Comfortable everyday footwear designed for relaxed settings, combining simple style, lightweight mat', 'Black,grey,blue', 70, 'Adidas', '', '[\"68151c5c0ddc2.jpeg\",\"68151c5c0e087.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(381, 'Af1 aquarium ', 'af1-aquarium-', 'Low-top sneaker featuring a crisp white leather upper with vibrant Aquarius Blue accents on the Swoosh, tongue, and outsole. Perforated toe box enhances breathability, while the padded collar ensures all-day comfort. Classic cupsole construction provides durability and timeless style.', '68151cf52d6c7.jpeg', NULL, NULL, '2800', '36,37,38,39,40,41,42,43,44,45', 'Blue', 30, 'Nike', '', '[\"68151cf52d996.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(382, 'Af1 year of the ðŸ…', 'af1-year-of-the-ðÿ…', 'Striking low-top sneaker featuring tiger-inspired details, bold orange and black accents, and premium materialsâ€”celebrating the Chinese Zodiac with a fierce and festive Air Force 1 design.', '68151df1f2764.jpeg', NULL, NULL, '3000', '3000ðŸ’°', 'Yellow/white', 30, 'Nike', '', '[\"68151df1f2a07.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(383, 'SB batman', 'sb-batman', 'Skate-ready sneaker featuring dark color blocking inspired by Batman, with durable suede and leather overlays, padded cushioning, and bold contrast details for a heroic streetwear look.', '68151ea0a04de.jpeg', NULL, NULL, '2999', '36,37,38,38,40,41,42,43,44,45', 'Black/white', 50, 'Nike', '', '[\"68151ea0a07c2.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(384, 'Offical casuals', 'offical-casuals', 'Stylish yet comfortable footwear designed for semi-formal and relaxed occasions, combining sleek designs with durable materials to provide a refined yet laid-back look for everyday wear.', '68151fa120b43.jpeg', NULL, NULL, '2800', '40,41,42,43,44,45', 'Black,brown', 300, '', '', '[\"68151fa120e5c.jpeg\",\"68151fa12116b.jpeg\",\"68151fa121405.jpeg\",\"68151fa12172b.jpeg\",\"68151fa121a0c.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(385, 'Pepe jeans', 'pepe-jeans', 'Casual footwear by Pepe Jeans featuring stylish designs, durable materials, and a comfortable fit. Perfect for everyday wear, offering a blend of modern fashion and laid-back sophistication.', '681520670869a.jpeg', NULL, NULL, '2800', '40,41,42,43,44,45', 'Blue,black', 200, '', '', '[\"6815206708989.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(386, 'Asics', 'asics', 'Known for its high-performance running shoes, Asics combines cutting-edge technology, superior cushioning, and sleek designs for both athletes and casual wearers seeking comfort and support.', '6815213420ff1.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Blue,green,white,yellow', 500, '', '', '[\"68152134213de.jpeg\",\"6815213421680.jpeg\",\"681521342192d.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(387, '95 ammanier', '95-ammanier', 'A collaboration between Nike and A Ma ManiÃ©re, the Air Max 95 features premium materials with a sophisticated color palette, combining suede, leather, and textured details. The design highlights the iconic Air Max silhouette with luxe touches, including the signature A Ma ManiÃ©re branding and attention to detail, offering both style and comfort.', '68152202dc4d2.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Brown,', 50, 'Nike', '', '[\"68152202dc7bf.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(389, 'Tommy officials', 'tommy-officials', 'Tommy Hilfiger official shoes combine classic American style with modern elegance, featuring premium materials, clean silhouettes, and subtle brandingâ€”perfect for smart-casual and everyday wear.', '681523e2b3570.jpeg', NULL, NULL, '2800', '40,41,42,43,44,45', 'Brown,wite,blue,black', 50, '', '', '[\"681523e2b38dd.jpeg\",\"681523e2b3ba9.jpeg\",\"681523e2b3e71.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(390, 'LacosteðŸŠ', 'lacosteðÿš', 'Lacoste official shoes offer a sleek blend of sport-inspired design and refined elegance, crafted with premium materials, clean lines, and the iconic crocodile logoâ€”ideal for polished, everyday style.', '681525191ad3d.jpeg', NULL, NULL, '2800', '40,41,42,43,44,45', 'Black,white,blue', 40, '', '', '[\"681525191b054.jpeg\",\"681525191b2b4.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(391, 'Timberland casuals', 'timberland-casuals', 'Timberland casuals blend rugged durability with everyday style, featuring premium leather, cushioned insoles, and versatile designs perfect for both urban wear and relaxed outdoor settings.', '68152627ac5a8.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Black,grey,white', 50, 'Timberland', '', '[\"68152627ac87a.jpeg\",\"68152627acabb.jpeg\"]', NULL, NULL, NULL, 'all', 'in_stock'),
(395, '95 syna', '95-syna', 'The Air Max 95 Syna World is a limited-edition collab with Central Cee, featuring a black mesh and suede upper, rose gold accents, and custom â€œSynaâ€ branding for a bold streetwear look.', '681a4c3f9215a.jpeg', NULL, NULL, '3499', '39,40,41,42,43,44,45', 'Black/gold', 400, 'Nike', '', '[\"681a4c3f94c06.jpeg\",\"681a4c3f94e46.jpeg\"]', NULL, 4, 41, 'all', 'in_stock'),
(396, 'Af1 animal print', 'af1-animal-print', 'The AF1 Animal Print features a bold mix of textures and patterns, including leopard or zebra prints, on classic Air Force 1 leather uppersâ€”blending street style with wild flair.', '681a4cdeeee68.jpeg', NULL, NULL, '2800', '36,37,38,39,40,41,42,43,44,45', 'Black/brown', 50, 'Nike', '', '[\"681a4cdeef136.jpeg\"]', NULL, 3.9, 37, 'all', 'in_stock'),
(397, 'Af1 x vlone', 'af1-x-vlone', 'The AF1 x VLONE is a rare collab featuring black leather uppers with bold orange stitching, VLONE branding on the heel, and the signature â€œEvery Living Creature Dies Aloneâ€ textâ€”known for its exclusive, limited release.', '681a4d6d59e37.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Black/orange', 300, 'Nike', '', '[\"681a4d6d5a35e.jpeg\",\"681a4d6d5a889.jpeg\"]', NULL, 4.6, 145, 'all', 'in_stock'),
(398, 'Nocta drake', 'nocta-drake', 'The NOCTA x Nike Hot Step Air Terra \"Red\" features an all-red leather upper, reflective accents, visible Air cushioning, and signature NOCTA brandingâ€”bringing Drakeâ€™s sleek, bold style to a sporty silhouette.', '681a4dd437778.jpeg', NULL, NULL, '3300', '36,37,38,39,40,41,42,43,44,45', 'Red', 500, 'Nike', '', '[\"681a4dd437ada.jpeg\"]', NULL, 4, 295, 'all', 'in_stock'),
(399, 'Af1 pink', 'af1-pink', 'The AF1 Pink features classic Air Force 1 styling with soft pink leather or suede uppers, clean white midsoles, and subtle detailingâ€”blending sporty heritage with a fresh, feminine vibe.', '681a4e5517f27.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Pink', 3000, 'Nike', '', '[\"681a4e55184d6.jpeg\"]', NULL, 4, 110, 'all', 'in_stock'),
(400, 'Shocks silverâœ¨', 'shocks-silverâœ¨', 'Nike Shox Silver features metallic silver uppers with signature Shox cushioning columns in the heel, delivering a futuristic look with responsive, spring-like support.', '681a4ec39290a.jpeg', NULL, NULL, '3499', '36,37,38,39,40,41,42,43,44,45', 'Silver,red', 50, 'Nike', '', '[\"681a4ec392c52.jpeg\"]', NULL, 4.6, 296, 'all', 'in_stock'),
(401, 'Shocks ðŸ–¤', 'shocks-ðÿ–¤', 'Nike Shox Black features an all-black upper with matching Shox columns, offering a sleek, stealthy look paired with responsive cushioning and a bold, athletic design.', '681a503dd18c6.jpeg', NULL, NULL, '3499', '36,37,38,39,40,41,42,43,44,45', 'Black', 300, 'Nike', '', '[]', NULL, 4.1, 18, 'all', 'in_stock'),
(402, 'Af valentines', 'af-valentines', 'The AF1 \"Valentine\'s Day\" is a special edition Air Force 1 featuring romantic-themed colors like pink, red, and white, often with heart-shaped details or love-inspired graphicsâ€”perfect for celebrating love in style.', '681baba4aaf70.jpeg', NULL, NULL, '2800', '36,37,38,39,40,41,42,43,44,45', 'Purple', 400, 'Nike', '', '[\"681baba4abe03.jpeg\",\"681baba4ac094.jpeg\",\"681baba4ac2d0.jpeg\"]', NULL, 4.3, 204, 'all', 'in_stock'),
(403, 'Nike high cut', 'nike-high-cut', '*Sleek black Nike high-cut sneakers designed for all-day comfort and style, perfect for streetwear or athletic looks.*', '682f6e088ee12.jpeg', NULL, NULL, '2899', '38,39,40,41,42,43,44,45', 'Black,white ', 500, 'Nike', '', '[\"682f6e088f727.jpeg\",\"682f6e088f9c1.jpeg\"]', NULL, 5, 218, 'all', 'in_stock'),
(404, 'Af high cut', 'af-high-cut', '*Clean and classic all-white Nike Air Force 1 high-cut sneakers, featuring durable leather and ankle strap support for timeless street style and comfort.*', '682f6e84a5878.jpeg', NULL, NULL, '2899', '38,39,40,41,42,43,44,45', 'White,black', 500, 'Nike', '', '[\"682f6e84a5b0c.jpeg\",\"682f6e84a5d3a.jpeg\"]', NULL, 4.6, 10, 'all', 'in_stock'),
(405, 'Puma ', 'puma-', '*Sleek black Puma low-top sneakers with a bold white logo and gum sole, offering a perfect mix of sporty style and everyday comfort.*', '682f6f13722f1.jpeg', NULL, NULL, '2999', '38,39,40,41,42,43,44,45', 'Black,red,white,grey', 500, 'Puma', '', '[\"682f6f137262c.jpeg\",\"682f6f137292a.jpeg\",\"682f6f1372bc2.jpeg\"]', NULL, 4.3, 159, 'all', 'in_stock'),
(407, 'Popcaan clarks', 'popcaan-clarks', 'Limited edition Clarks Wallabees in collaboration with Popcaan, featuring camo print and suede finish for a bold, stylish look.\r\n\r\n', '682f73200946c.jpeg', NULL, NULL, '3200', '36,37,38,39,40,41,42,43,44,45', '  Brown, Pink, Cream', 500, '', '', '[\"682f7320097af.jpeg\",\"682f732009a32.jpeg\",\"682f732009d0d.jpeg\",\"682f732009f9b.jpeg\",\"682f73200a22f.jpeg\"]', NULL, 4.4, 49, 'all', 'in_stock'),
(408, 'Tn plus moonlight ðŸŒ™ ', 'tn-plus-moonlight-ðÿœ™-', 'It features a sleek black upper embellished with shimmering Swarovski crystals, giving it a luxury twist while retaining the classic TN silhouette. Ideal for making a bold, stylish statement.', '6836158e88ed6.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Black/silver', 500, 'Nike', '', '[\"6836158e891f0.jpeg\",\"6836158e89456.jpeg\"]', NULL, 5, 218, 'all', 'in_stock'),
(409, 'Af1 paris', 'af1-paris', 'Sleek black AF1 with white stitching, glossy finish, and Paris branding for a bold streetwear look.', '6836172edc59a.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Black/grey', 400, 'Nike', '', '[\"6836172edc9f2.jpeg\",\"6836172edcd0d.jpeg\"]', NULL, 4.3, 163, 'all', 'in_stock'),
(411, 'Air Jordan 4 X Nigel Sylvester â€œBrick by Brick ðŸ§± ðŸ¾  ', 'air-jordan-4-x-nigel-sylvester-â€œbrick-by-brick-ðÿ§±-ðÿ¾--', 'Bold and unique AJ4 â€œBrick by Brickâ€ by Nigel Sylvester, featuring textured brick-inspired design and premium detailing for standout street style.', '68361bfd52d50.jpeg', NULL, NULL, '3200', '40,41,42,43,44,45', 'Red/white', 500, 'Nike', '', '[\"68361bfd530b6.jpeg\",\"68361bfd53340.jpeg\"]', NULL, 4.9, 72, 'all', 'in_stock'),
(412, 'SambaðŸ¤Ž', 'sambaðÿ¤ž', 'Classic Adidas Samba in rich brown tones, featuring a sleek leather upper, gum sole, and iconic 3-stripe detailingâ€”perfect blend of vintage and modern style.', '68361d2978da7.jpeg', NULL, NULL, '2800', '36,37,38,39,40,41,42,43,44,45', 'Brown/white ', 400, 'Adidas', '', '[\"68361d29790cf.jpeg\"]', NULL, 4.7, 138, 'all', 'in_stock'),
(413, 'Af1 ðŸ¤Ž', 'af1-ðÿ¤ž', 'Aff 1 sneakers with premium leather, iconic silhouette, and classic Air cushioning. Stylish, durable, and versatile for everyday wear.', '68374152be181.jpeg', NULL, NULL, '2800', '40,41,42,43,44,45', 'Brown', 30, 'Nike', '', '[]', NULL, 4.6, 228, 'all', 'in_stock'),
(414, 'Af1 custom ', 'af1-custom-', 'Custom Nike Air Force 1 with unique designs, hand-painted details, and personalized touchesâ€”perfect for standout, one-of-a-kind street style.', '683741d7b5b4b.jpeg', NULL, NULL, '2800', '36,37,38,39,50,41,42,43,44,45', 'White/blue', 40, 'Nike', '', '[]', NULL, 4.5, 73, 'all', 'in_stock'),
(415, 'Cortez ðŸ’š', 'cortez-ðÿ’š', 'Classic Nike Cortez in green tones, featuring a sleek retro silhouette, leather or suede upper, and vintage-inspired detailing for timeless street style.', '6838c67b00091.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'Jungle green', 400, 'Nike', '', '[\"6838c67b015c1.jpeg\"]', NULL, 4.6, 31, 'all', 'in_stock'),
(416, 'Air plus tn', 'air-plus-tn', 'Nike Air Max Plus TN blends bold design with Tuned Air cushioning, offering comfort, durability, and street-ready style.', '6838c75eb433f.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Brown,light crystal', 300, 'Nike', '', '[\"6838c75eb4693.jpeg\"]', NULL, 4.9, 210, 'all', 'in_stock'),
(417, 'Af ðŸŒµ plant flea', 'af-ðÿœµ-plant-flea', 'The *Air Fear of God x Cactus Plant Flea Market* combines premium materials with unique design elements, offering a bold, modern sneaker with a streetwear edge. Featuring a mix of textures, vibrant detailing, and signature branding, it\'s perfect for sneakerheads looking to make a statement.', '6838ccc569c3c.jpeg', NULL, NULL, '3300', '38,39,40,41,42,43,44,45', 'White', 300, 'Nike', '', '[\"6838ccc569f31.jpeg\",\"6838ccc56a150.jpeg\",\"6838ccc56a378.jpeg\"]', NULL, 4, 177, 'all', 'in_stock'),
(418, 'Af 1 kobee', 'af-1-kobee', 'Nike AF1 Kobe is a tribute to Kobe Bryant, featuring Lakers-inspired colors, premium materials, and classic Air Force 1 styling with a Mamba twist.', '683a1aad81c99.jpeg', NULL, NULL, '3300', '36,37,38,39,40,41,42,43,44,45', 'Black/yellow', 30, 'Nike', '', '[\"683a1aad82c48.jpeg\"]', NULL, 4.8, 284, 'all', 'in_stock'),
(419, 'Airmax neon', 'airmax-neon', 'Nike Air Max Neon features bold neon accents, visible Air cushioning, and a sporty design that blends retro vibes with modern comfort.', '683a1b55ec27e.jpeg', NULL, NULL, '3300', '38,39,40,41,42,43,44,45', 'Green/white', 400, 'Nike', '', '[\"683a1b55ec550.jpeg\"]', NULL, 4.1, 278, 'all', 'in_stock'),
(420, 'Shocks heat reactive ', 'shocks-heat-reactive-', 'Nike Shox Heat Reactive features color-changing uppers that shift with temperature, paired with responsive Shox cushioning for bold style and dynamic comfort.', '683c951c068d6.jpeg', NULL, NULL, '3500', '40,41,42,43,44,45', 'Red/black,blue/black,green/black', 300, 'Nike', '', '[\"683c951c074d9.jpeg\",\"683c951c07770.jpeg\"]', NULL, 4.9, 299, 'all', 'in_stock'),
(421, '95â€™sðŸ¥¶', '95â€™sðÿ¥¶', 'Nike Air Max 95 with bold teal accents, layered mesh and leather upper, and visible Air cushioning for all-day comfort and style.', '683c95ea69b22.jpeg', NULL, NULL, '3300', '40,41,42,43,44,45', 'Green,orange,yellow', 400, 'Nike', '', '[\"683c95ea69df7.jpeg\",\"683c95ea6a033.jpeg\",\"683c95ea6a26a.jpeg\"]', NULL, 3.6, 167, 'all', 'in_stock'),
(422, 'NB 1000 â˜‘ï¸', 'nb-1000-â˜‘ï¸', 'New Balance 1000 Grey â€“ A retro runner revived with modern comfort. Features layered mesh and leather in sleek grey tones, plus ABZORB cushioning for all-day support. Perfect blend of vintage style and performance.', '683c969932a08.jpeg', NULL, NULL, '3300', '38,39,40,41,42,43,44,45', 'Grey', 200, 'Newbalance', '', '[\"683c969932c8b.jpeg\",\"683c969932ec9.jpeg\"]', NULL, 4.4, 254, 'all', 'in_stock'),
(423, 'J3 cactus ', 'j3-cactus-', 'A bold take on the classic J3, featuring earthy tones, cactus-inspired accents, and iconic elephant print. Perfect for standout streetwear looks.', '683c975f485af.jpeg', NULL, NULL, '2999', '39,40,41,42,43,44,45', 'Black', 300, 'Jordan', '', '[\"683c975f4886f.jpeg\"]', NULL, 4.7, 184, 'all', 'in_stock'),
(424, 'NB 1000 ðŸ’š', 'nb-1000-ðÿ’š', 'Retro-inspired runner with layered mesh and leather in vibrant green hues, ABZORB cushioning, and a bold, futuristic design for everyday comfort and standout style.', '683c97f470948.jpeg', NULL, NULL, '3300', '40,41,42,43,44,45', 'Green/cream', 200, 'Newbalance', '', '[\"683c97f470bff.jpeg\"]', NULL, 3.5, 214, 'all', 'in_stock'),
(425, 'Asics Gel Kayano white Fjord ', 'asics-gel-kayano-white-fjord-', ' A stability runner combining breathable mesh, sleek white overlays, and subtle Fjord Grey accents. Features GEL cushioning for all-day comfort and support with a clean, sporty look.', '683c98bc89897.jpeg', NULL, NULL, '3300', '40,41,42,43,44,45', 'Grey', 300, '', '', '[\"683c98bc89beb.jpeg\"]', NULL, 5, 222, 'all', 'in_stock'),
(426, 'ASICS Gel-Kayano White/Fjord', 'asics-gel-kayano-white/fjord', 'A stability runner combining breathable mesh, sleek white overlays, and subtle Fjord Grey accents. Features GEL cushioning for all-day comfort and support with a clean, sporty look.', '683c9ab0e6043.jpeg', NULL, NULL, '3300', '40,41,42,43,44,45', 'Green', 300, '', '', '[\"683c9ab0e6314.jpeg\"]', NULL, 3.5, 167, 'all', 'in_stock'),
(427, 'Puma xxl ðŸ’™', 'puma-xxl-ðÿ’™', 'Bold, oversized design with deep blue tones, sporty details, and signature branding. Combines streetwear style with everyday comfort.', '683f4722ad747.jpeg', NULL, NULL, '2999', '37,38,39,40,41,42,43,44,45', 'Blue', 200, 'Puma', '', '[\"683f4722b17e9.jpeg\",\"683f4722b1b3d.jpeg\"]', NULL, 3.7, 43, 'all', 'in_stock'),
(428, 'J4 seaform', 'j4-seaform', 'Soft green tones, classic suede overlays, and signature Jordan detailing come together for a clean, refreshing look with retro flair and modern comfort.', '683f4834cb00c.jpeg', NULL, NULL, '2999', '38,39,40,41,42,43,44,45', 'Sea blue', 5000, 'Jordan', '', '[]', NULL, 4.7, 188, 'all', 'in_stock'),
(429, 'Bike air ', 'bike-air-', 'Sporty and sleek design with vibrant green accents, breathable build, and cushioned sole for comfort and everyday street-ready style.', '683f48bd1f0b4.jpeg', NULL, NULL, '3300', '39,40,41,42,43,44,45', 'Green', 300, 'Nike', '', '[\"683f48bd1f39d.jpeg\"]', NULL, 3.7, 55, 'all', 'in_stock'),
(430, 'Bike air grey', 'bike-air-grey', 'Minimalist design with cool grey tones, breathable upper, and cushioned sole for all-day comfort and a clean, urban look.', '683f491fd867b.jpeg', NULL, NULL, '3300', '39,40,41,42,43,44,45', 'Grey', 300, 'Nike', '', '[\"683f491fd8996.jpeg\"]', NULL, 3.9, 170, 'all', 'in_stock'),
(431, 'Clarks ', 'clarks-', 'Timeless design with premium leather, sturdy sole, and signature Clarks comfortâ€”perfect for both casual and formal wear.', '683f4c8b158da.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Black,white,brown', 300, '', '', '[\"683f4c8b15b8b.jpeg\",\"683f4c8b15da4.jpeg\",\"683f4c8b16014.jpeg\"]', NULL, 4.7, 225, 'all', 'in_stock'),
(432, 'Af valentine', 'af-valentine', 'Bold, romantic vibes meet classic street styleâ€”perfect for making a statement.', '684095586e8f9.jpeg', NULL, NULL, '2800', '36,37,38,39,40,41,42,43,44,45', 'Red/white', 300, 'Nike', '', '[\"684095586f7a5.jpeg\",\"684095586fa13.jpeg\",\"684095586fca5.jpeg\"]', NULL, 4, 96, 'men', 'in_stock'),
(433, 'J1 low shinny', 'j1-low-shinny', 'Glossy finish with sleek detailing, low-cut silhouette, and iconic Jordan styleâ€”built for standout streetwear flair.', '68433eb5ac0c5.jpeg', NULL, NULL, '2999', '37,38,38,40,41,42,43,44,45', 'Black', 300, 'Jordan', '', '[]', NULL, 3.5, 27, 'all', 'in_stock'),
(434, 'NB 740', 'nb-740', 'Original New Balance 740 â€“ sporty, stylish, and cushioned for all-day comfort. Perfect for casual wear or light workouts. ', '684b21b0bb3b3.jpeg', NULL, NULL, '3300', '40,41,42,43,44,45', 'Orange,purple', 500, 'Newbalance', '', '[\"684b21b0c0927.jpeg\"]', NULL, 4.3, 86, 'all', 'in_stock'),
(435, 'Cactus plant ðŸ’š', 'cactus-plant-ðÿ’š', '\r\nBold and bright! ðŸ’š These Nike Air Force 1s feature oversized \"AIR\" patches and a striking neon finish. Built for standout style and everyday comfort ðŸ‘Ÿâš¡ï¸', '684c62adab1c5.jpeg', NULL, NULL, '3499', '38,39,40,41,42,43,44,45', 'Green', 3000, 'Nike', '', '[]', NULL, 4, 267, 'all', 'in_stock'),
(436, 'Clogs(bikernstocks)', 'clogs(bikernstocks)', 'Comfortable, stylish unisex clogs â€“ perfect for everyday wear. Lightweight, breathable, and easy to slip on. âœ…', '684dcae0b29c4.jpeg', NULL, NULL, '2499', '39,40,41,42,43,44,45', 'Green,brown,black', 3000, '', '', '[\"684dcae0b3479.jpeg\",\"684dcae0b376f.jpeg\"]', NULL, 4.2, 223, 'all', 'in_stock'),
(437, 'Puma speedcat â™¥ï¸', 'puma-speedcat-â™¥ï¸', 'Original Puma Speedcat ðŸðŸ”¥ â€” Sleek, race-inspired sneakers built for comfort and street style. Lightweight feel with premium detailing. Perfect for daily wear or motorsport fans! âœ…', '684f16dcaab8b.jpeg', NULL, NULL, '3300', '38,39.40,41,42,43,44,45', 'Red', 5000, 'Puma', '', '[\"684f16dcab5a4.jpeg\"]', NULL, 4.4, 158, 'all', 'in_stock'),
(438, 'Puma speedcat â™¥ï¸', 'puma-speedcat-â™¥ï¸', 'Original Puma Speedcat ðŸðŸ”¥ â€” Sleek, race-inspired sneakers built for comfort and street style. Lightweight feel with premium detailing. Perfect for daily wear or motorsport fans! âœ…', '684f16dd653d5.jpeg', NULL, NULL, '3300', '38,39.40,41,42,43,44,45', 'Red', 5000, 'Puma', '', '[\"684f16dd656a8.jpeg\"]', NULL, 3.6, 49, 'all', 'in_stock'),
(439, 'Timbs LVâœ¨', 'timbs-lvâœ¨', 'Original Timberland x LV-Inspired Boots ðŸ‘¢âœ¨  \r\nRugged, stylish, and built for comfortâ€”perfect for bold streetwear looks or everyday wear âœ…', '6850516cb2d0f.jpeg', NULL, NULL, '3800', '40,41,42,43,44,45', 'Brown,black', 30000, 'Timberland', '', '[\"6850516cb328d.jpeg\",\"6850516cb34e0.jpeg\",\"6850516cb374d.jpeg\",\"6850516cb39d2.jpeg\"]', NULL, 3.7, 220, 'all', 'in_stock'),
(440, '97 ducks of feathers', '97-ducks-of-feathers', 'Sleek and eye-catching, the Air Max 97 â€œDucks of a Featherâ€ features reflective detailing, feather-inspired patterns, and the signature full-length Air unit for ultimate comfort and streetwear flair. Sizes available. âœ…', '6851c21e12ca1.jpeg', NULL, NULL, '3300', '39,40,41,42,43,44,45', 'Green/black', 3000, 'Nike', '', '[\"6851c21e12fb9.jpeg\",\"6851c21e1326e.jpeg\"]', NULL, 4.9, 13, 'all', 'in_stock'),
(441, 'Y3 slides ', 'y3-slides-', 'Minimalist yet boldâ€”Y-3 slides blend luxury with comfort. Designed with a soft cushioned footbed, durable outsole, and signature Y-3 branding. Perfect for streetwear or chill days âœ…', '6851c2b215b0f.jpeg', NULL, NULL, '3300', '39,40,41,42,43,44,45', 'Black,orange', 30000, '', '', '[\"6851c2b216031.jpeg\"]', NULL, 4.2, 78, 'all', 'in_stock'),
(442, 'Honey blacks', 'honey-blacks', 'Step into timeless style with the Nike Cortez Honey Blackâ€”sleek, durable, and ultra-comfy. Perfect for casual fits and everyday wear ðŸ‘ŸðŸ”¥ Sizes 36â€“45 available â˜‘ï¸', '6853063e5ef94.jpeg', NULL, NULL, '3300', '38,39,40,41,42,43,44,45', 'Black/yellow', 4000, 'Nike', '', '[\"6853063e5f395.jpeg\",\"6853063e5f674.jpeg\"]', NULL, 4.6, 169, 'all', 'in_stock'),
(443, 'Nocta ðŸ–¤', 'nocta-ðÿ–¤', 'Sleek and bold, these Drake x Nike NOCTA kicks feature an all-black design with premium leather, visible Air cushioning, and rugged outsoles', '68545e1b979c1.jpeg', NULL, NULL, '3200', '40,41,42,43,44,45', 'Black', 3000, 'Nike', '', '[\"68545e1b98439.jpeg\",\"68545e1b98712.jpeg\"]', NULL, 5, 47, 'all', 'in_stock'),
(444, 'Vansâœ¨', 'vansâœ¨', 'Original Vans sneakers ðŸ–¤âœ¨â€” iconic low-top design, durable canvas upper, cushioned insole, and grippy waffle sole. Perfect for skate, street, or casual wear âœ…', '6855b3844dec5.jpeg', NULL, NULL, '1700', '36,37,38,39,40,41,42,43,44,45', 'Pink,red/black,red/white,blue/white,yellow,black/white,maroon/black,maroon/white,all black,grey/black', 30000, 'Vans', '', '[\"6855b3844e636.jpeg\",\"6855b3844e995.jpeg\",\"6855b3844ec0e.jpeg\",\"6855b3844ee26.jpeg\",\"6855b3844f122.jpeg\",\"6855b3844f45d.jpeg\",\"6855b3844f6b1.jpeg\",\"6855b3844f997.jpeg\"]', NULL, 4.2, 243, 'all', 'in_stock'),
(445, 'Gutta green ', 'gutta-green-', 'Step into bold, street-smart energy with the *Corteiz Gutta Green* sneakers â€” a limited-edition pair designed for standout style. Crafted with high-quality materials and signature Corteiz detailing, these kicks boast a rich green upper, durable rubber outsole, and supportive cushioning for everyday wear. The sleek low-top silhouette makes them perfect for both casual fits and streetwear statements. Whether you\'re on the move or flexing your style, Gutta Greens bring confidence, comfort, and serious drip to your step.  \r\n\r\nâœ… Lightweight comfort  \r\nâœ… Iconic Corteiz branding  \r\nâœ… Cushioned insole  \r\nâœ… Premium build for daily wear  \r\nâœ… Available in multiple sizes', '68584886d6cda.jpeg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'Green', 300, 'Nike', '', '[\"68584886d7bcf.jpeg\",\"68584886d7e96.jpeg\"]', NULL, 4.7, 191, 'all', 'in_stock'),
(446, 'Af chrome hearts ', 'af-chrome-hearts-', 'Stylish and bold, these AF1 Chrome Hearts feature iconic detailing and streetwear luxury. Perfect for standing out in timeless black & white drip ðŸ‘ŸðŸ”¥. Sizes 38â€“45 available âœ…', '685996a4b3cba.jpeg', NULL, NULL, '2999', '39,40,41,42,43,44,45', 'Green', 300, 'Nike', '', '[\"685996a4b40f4.jpeg\",\"685996a4b4460.jpeg\"]', NULL, 4.6, 96, 'all', 'in_stock'),
(447, '95 â€˜sâš«âšª', '95-â€˜sâš«âšª', 'Sleek, layered design with visible Air cushioning for all-day comfort and standout street style. Sizes available! âœ…', '685ae9f3939a0.jpeg', NULL, NULL, '3500', '39,40,41,42,43,44,45', 'Black/white', 400, 'Nike', '', '[\"685ae9f393f08.jpeg\",\"685ae9f394460.jpeg\"]', NULL, 5, 14, 'all', 'in_stock'),
(448, 'Timberland casuals ', 'timberland-casuals-', '\r\nPremium Timberland casuals built for style and everyday comfort. Durable construction, cushioned insoles, and rugged outsoles â€” perfect for streetwear or smart-casual fits ðŸ”¥âœ…\r\n', '685c4366a9261.jpeg', NULL, NULL, '2999', '39,40,41,42,43,44,45', 'Black,blue,grey', 300, 'Timberland', '', '[\"685c4366a9f1b.jpeg\",\"685c4366aa14c.jpeg\"]', NULL, 4.8, 256, 'all', 'in_stock'),
(449, 'Dior B30 ', 'dior-b30-', 'The Dior B30 sneakers blend modern sport-inspired design with the timeless sophistication of Dior. Crafted from premium mesh and smooth leather overlays, they feature reflective â€˜B30â€™ branding on the sides for a bold statement. The breathable construction ensures all-day comfort, while the sculpted sole provides stability and a sleek silhouette. Lightweight, stylish, and versatile, theyâ€™re perfect for both casual wear and upscale looks. A must-have for those who appreciate fashion-forward luxury and performance in one.', '685d9bc48da04.jpeg', NULL, NULL, '3400', '39,40,41,42,43,44,45', 'Black,yellow', 100, '', '', '[\"685d9bc48e56e.jpeg\"]', NULL, 3.6, 285, 'all', 'in_stock'),
(450, 'J9', 'j9', '\r\nThe Air Jordan 9 Retro pays homage to Michael Jordanâ€™s global influence on the game. Designed with a clean and bold silhouette, it features a durable leather or nubuck upper, supportive midsole cushioning, and a unique multi-language outsole design that reflects MJâ€™s worldwide legacy. Its inner bootie construction offers a snug, secure fit, while the heel pull tab ensures easy on-and-off. The perfect mix of performance and fashion.\r\n', '685eed9702b17.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Black,black/yellow,black/yellow/blue', 500, 'Jordan', '', '[\"685eed9703a18.jpeg\",\"685eed9703c27.jpeg\"]', NULL, 5, 118, 'all', 'in_stock'),
(451, 'Jordan 4 retroðŸ’™', 'jordan-4-retroðÿ’™', '\r\nThe Air Jordan 4 Retro â€œMilitary Blueâ€ returns with its timeless design and premium build. Featuring a crisp white leather upper, blue and grey accents, and mesh paneling for breathability, this iconic silhouette delivers both comfort and style. The visible Air-Sole unit in the heel provides responsive cushioning, while the supportive TPU wings and heel tab offer lockdown fit and classic AJ4 detailing. Perfect for collectors and everyday wearers alike.', '68602b2e79624.jpeg', NULL, NULL, '3500', '36,37,38,39,40,41,42,43,44,45', 'Baby blue ðŸ’™', 200, 'Jordan', '', '[\"68602b2e79ffc.jpeg\",\"68602b2e7a29d.jpeg\",\"68602b2e7a56d.jpeg\",\"68602b2e7a84c.jpeg\"]', NULL, 3.6, 83, 'all', 'in_stock'),
(452, 'Af lv ðŸ¤Ž', 'af-lv-ðÿ¤ž', 'Step out in timeless luxury with the Air Force 1 LV Brown â€” a perfect blend of iconic AF1 style and bold LV flair. Durable, comfy & fashion-forward. Sizes 38â€“45 available âœ…\r\n\r\n*What it contains:*  \r\nâ€“ Premium Synthetic Leather Upper  \r\nâ€“ Signature Louis Vuitton Pattern Overlay  \r\nâ€“ Perforated Toe Box for Breathability  \r\nâ€“ Cushioned Insole for Comfort  \r\nâ€“ Durable Rubber Outsole  \r\nâ€“ Classic Low-Top Silhouette  \r\nâ€“ Secure Lace-Up Closure  \r\nâ€“ Stylish Branding on Tongue & Heel  \r\n', '68616f6301030.jpeg', NULL, NULL, '2999', '36,37,38,39,40,41,42,43,44,45', 'White/brown', 300, 'Nike', '', '[\"68616f6301b9f.jpeg\"]', NULL, 4.3, 47, 'all', 'in_stock'),
(453, 'Airmaxes 97 âœ¨ðŸ’“', 'airmaxes-97-âœ¨ðÿ’“', 'Revolutionize your shoe game! This legendary sneaker boasts a full-length Max Air unit for maximum comfort and impact protection. The sleek, aerodynamic design is inspired by the Bullet Train, while the lightweight upper provides breathability and flexibility. With reflective details and a bold aesthetic, the Air Max 97 is perfect for runners, sneakerheads, and anyone who wants to make a statement. Get ready to elevate your style and performance!\"\r\n\r\n', '6862dc193cfae.jpeg', NULL, NULL, '3200', '37,38,39,40,41,42,43,44,45', 'Pink/white', 300, 'Nike', '', '[\"6862dc193e097.jpeg\",\"6862dc193e4c4.jpeg\"]', NULL, 3.5, 161, 'all', 'in_stock'),
(454, 'Clogs multi coloursâœ¨', 'clogs-multi-coloursâœ¨', '\r\nStep into timeless comfort and effortless style with these premium suede clogs. Designed with a soft dual-tone upper in warm rose pink and deep mocha brown, they feature a classic gold-tone buckle for a vintage touch. The inner lining is crafted for breathability and all-day wear, while the contoured footbed offers plush support.  \r\n\r\nThe rubber outsole has a unique ripple tread pattern for superior grip and traction, perfect for both indoor lounging and casual outdoor use. These clogs are a blend of cozy function and minimalist fashionâ€”ideal for anyone who appreciates comfort without compromising on aesthetics.', '686500d61bebe.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Brown/pink,brown/closed back,brown/heelstrap,black,white', 300, '', '', '[\"686500d61d9a4.jpeg\",\"686500d61dd91.jpeg\",\"686500d61e12f.jpeg\",\"686500d61e4cd.jpeg\"]', NULL, 3.5, 108, 'all', 'in_stock'),
(455, 'NB 1000ðŸ’œâœ¨', 'nb-1000ðÿ’œâœ¨', '(BOXED) Unisex New Balance 1000 â€“ Purple Edition ðŸ’œðŸ”¥  \r\nStep into bold style and comfort with the New Balance 1000. This iconic sneaker combines futuristic Y2K aesthetics with advanced performance features, making it a go-to for streetwear fans and trendsetters.\r\n\r\n*What It Entails:*  \r\nâ€“ Eye-catching purple mesh upper with premium synthetic overlays  \r\nâ€“ Sleek layered design inspired by 2000s running shoes  \r\nâ€“ ABZORB midsole technology for superior shock absorption  \r\nâ€“ Padded tongue and collar for added ankle comfort  \r\nâ€“ Durable rubber outsole with excellent traction  \r\nâ€“ Reflective details for visibility and edge  \r\nâ€“ Classic lace-up system for a secure fit  \r\n\r\nâœ… Available in sizes 38â€“42\r\nâœ… Ideal for everyday wear, gym, or fashion-forward outfits  \r\nâœ… Affordable & stylish â€“ Limited pairs only!', '6866b851d95ee.jpeg', NULL, NULL, '3500', '38,39,40,41,42', 'Purple,pink', 200, 'Newbalance', '', '[\"6866b851da98c.jpeg\",\"6866b851dae9e.jpeg\"]', NULL, 4.1, 28, 'all', 'in_stock'),
(456, 'Tn plus ðŸ¥¶', 'tn-plus-ðÿ¥¶', '\r\n\r\n*Key Features:*\r\n- Sleek, wavy overlays on a mesh base\r\n- Visible Tuned Air (Tn) units in the midsole for maximum cushioning\r\n- Reflective accents for visibility in low light\r\n- Rubber outsole with excellent traction\r\n- Branded Tn Air tag detail\r\n\r\nThis design is known for its aggressive styling and streetwear appeal. Let me know if you\'d like a short selling caption or where to buy them affordably.', '686814179512c.jpeg', NULL, NULL, '3200', '40,41,42,43,44,45', 'Black/white,white/orange,black/yellow,black/white sole', 300, 'Nike', '', '[\"6868141795794.jpeg\",\"6868141795a45.jpeg\",\"6868141795d58.jpeg\",\"6868141796051.jpeg\"]', NULL, 4.2, 274, 'all', 'in_stock');
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `image`, `video`, `video_thumbnail`, `price_ksh`, `available_sizes`, `available_colors`, `units_available`, `category`, `main_image`, `secondary_image`, `secondary_videos`, `rating`, `sold`, `gender_category`, `stock_status`) VALUES
(458, 'Cork sandlesðŸ”¥', 'cork-sandlesðÿ”¥', '\r\n\r\nStep into all-day comfort with these high-quality cork sandals designed for everyday wear. Featuring a contoured *natural cork footbed* ðŸªµ, they offer optimal arch support and mold perfectly to your feet over time.\r\n\r\n- *Upper Material:* Soft genuine suede/leather ðŸ‘ with double adjustable straps ðŸ” for a snug, personalized fit  \r\n- *Footbed:* Anatomically shaped cork-latex sole ðŸ¦¶ with suede lining â€“ breathable and moisture-absorbing ðŸ’¨  \r\n- *Outsole:* Durable EVA rubber sole ðŸ’ª with strong grip and shock absorption  \r\n- *Design:* Minimalist yet stylish ðŸ§¢ â€“ perfect for casual outfits, home wear, or weekend errands ðŸ›ï¸  \r\n- *Fit:* Unisex sizing available â€“ from size 38 to 45 ðŸ“  \r\n\r\nIdeal for those who want a blend of *style, durability, and comfort* in a laid-back sandal ðŸ’¯ðŸ”¥\r\n\r\n', '686975ab702a6.jpeg', NULL, NULL, '2500', '38,39,40,41,42,43,44,45', 'White,black,black/brown', 20, '', '', '[\"686975ab70613.jpeg\",\"686975ab7085c.jpeg\",\"686975ab70af8.jpeg\",\"686975ab70dd6.jpeg\",\"686975ab71092.jpeg\"]', NULL, 4.5, 250, 'all', 'in_stock'),
(460, 'Af1 kids:  âšªâš«', 'af1-kids:--âšªâš«', '\r\nThe Nike Air Force 1 Kids edition brings the iconic AF1 style to young sneaker lovers ðŸ‘Ÿ. Designed with durability, comfort, and timeless street style in mind, these sneakers feature:\r\n\r\nâ€“ *Premium synthetic leather upper* for long-lasting wear  \r\nâ€“ *Padded collar and tongue* for extra comfort  \r\nâ€“ *Perforated toe box* for breathability  \r\nâ€“ *Foam midsole with Air-Sole unit* for lightweight cushioning  \r\nâ€“ *Non-marking rubber outsole* for excellent grip and traction  \r\nâ€“ *Classic low-top silhouette* perfect for everyday use  \r\nâ€“ Iconic *Nike Swoosh logo* for that signature look  \r\n\r\nPerfect for school, outings, or casual play, these AF1s offer both function and fashion for kids on the move âœ…', '686ac364adf5c.jpeg', NULL, NULL, '1999', '25,26,27,28,29,30,31,32,33,34,35,36', 'Black,white', 150, 'Nike', '', '[\"686ac364aea2d.jpeg\",\"686ac364aecf0.jpeg\",\"686ac364aef34.jpeg\",\"686ac364af1ba.jpeg\"]', NULL, 3.8, 40, 'all', 'in_stock'),
(461, 'Vans KNU skool ðŸ“Œ', 'vans-knu-skool-ðÿ“œ', '\r\nThe Vans Knu Skool is a modern reissue of a \'90s skate classic. Designed with exaggerated features like extra-thick laces, a puffed-up tongue, and a chunky profile, these sneakers offer ultimate comfort and bold street-style flair. The durable suede upper and signature rubber waffle outsole ensure long-lasting grip and support. Ideal for casual wear, skating, or making a statement in your outfit.  \r\n\r\nWhat It Contains\r\nâ€“ Suede Upper  \r\nâ€“ Oversized Laces  \r\nâ€“ Padded Tongue & Collar  \r\nâ€“ Chunky Silhouette  \r\nâ€“ Classic Rubber Waffle Sole  \r\nâ€“ Iconic Vans Side Stripe  \r\nâ€“ Reinforced Toe Cap  \r\nâ€“ Low-Top Design', '686c0d7c5563d.jpeg', NULL, NULL, '2800', '38,39,40,41,42,43,44', 'Brown/blue sidestripe,black with detailed surface/white sidestripe,blue/white sidestripe,black/white sidestripe,black/black sidestripe', 270, 'Vans', '', '[\"686c0d7c55951.jpeg\",\"686c0d7c55c0d.jpeg\",\"686c0d7c55e9a.jpeg\",\"686c0d7c56137.jpeg\"]', NULL, 4, 222, 'all', 'in_stock'),
(462, 'NB 1000 ðŸ«’ðŸ’š', 'nb-1000-ðÿ«’ðÿ’š', 'The New Balance 1000 Olive Green is a bold, retro-futuristic sneaker that combines early-2000s tech-runner vibes with a rugged, street-ready edge. It features a breathable mesh base layered with glossy olive green synthetic overlays and metallic accents that give it a military-inspired look. \r\nThe exaggerated, segmented midsole houses ABZORB cushioning for high-impact shock absorption and all-day support, while the sculpted outsole offers durable traction. \r\nDistinct â€œ1000â€ branding on the tongue and lateral side adds archival character, making this pair both functional and fashion-forward. Ideal for fashion enthusiasts seeking a statement sneaker that balances comfort, nostalgia, and style. ðŸŸ¢ðŸ’¥ðŸ‘Ÿ', '686d54dfe69e1.jpeg', NULL, NULL, '3500', '37,38,39,40,41,42,43,44,45', 'Olive green ðŸ«’ðŸ’š', 20, 'Newbalance', '', '[\"686d54dfe797f.jpeg\",\"686d54dfe8238.jpeg\",\"686d54dfe853a.jpeg\"]', NULL, 3.9, 77, 'all', 'in_stock'),
(463, 'NIKE ZOOM', 'nike-zoom', '\r\n\r\n\r\nâ€“ Breathable mesh upper for ventilation  \r\nâ€“ Multicolor design that adds a unique streetwear flair  \r\nâ€“ Foam midsole for lightweight comfort  \r\nâ€“ Rubber outsole with grippy tread for traction  \r\nâ€“ Padded collar and tongue for ankle support  \r\nâ€“ Lace-up closure for a secure fit  \r\n           How greedyðŸ˜¬\r\n\r\nWhether you\'re hitting the gym, walking the city, or making a fashion statement, these sneakers deliver energy in every step \r\nI personally think that you should cope these', '686fdf3407bbe.jpeg', NULL, NULL, '2999', '40,41,42,43,44,45', 'Pink tingz,black tingzyellow/orange/ black nike,orange nike/multicolour surface', 200, 'Nike', '', '[\"686fdf3408e01.jpeg\",\"686fdf34090da.jpeg\",\"686fdf34093aa.jpeg\"]', NULL, 4.7, 125, 'all', 'in_stock'),
(464, 'Samba messi ðŸ¤Ž', 'samba-messi-ðÿ¤ž', '\r\nThe Adidas Samba \"Messi\" Edition is a tribute to the GOAT. \r\n\r\n\r\nThis iconic silhouette is reimagined with Messi-inspired color accents, premium materials, and bold detailing:  \r\n\r\n\r\n\r\n\r\n\r\nâ€“ Soft leather upper with suede overlays  \r\nâ€“ Messi logo detailing on the tongue or heel  \r\nâ€“ Durable rubber outsole for grip and agility  \r\nâ€“ Classic -Stripes design  \r\nâ€“ Cushioned insole for all-day comfort  \r\n\r\nPerfect for fans, collectors, or anyone who appreciates legendary style.', '68700baf9c813.jpeg', NULL, NULL, '2999', '38,39,40,41,42,43,44,45', 'Brown', 20, 'Adidas', '', '[\"68700baf9cb96.jpeg\",\"68700baf9ce35.jpeg\"]', NULL, 4.2, 285, 'all', 'in_stock'),
(465, 'AIRMAX DN', 'airmax-dn', '\r\nThe Nike Air Max DN blends innovation with attitude. Featuring a dual-pressure Air unit for responsive cushioning and a sleek silhouette, it\'s built for comfort, durability, and bold looks:  \r\n\r\nâ€“ Mesh upper with synthetic overlays for breathability  \r\nâ€“ Dual-chamber Air cushioning system  \r\nâ€“ Padded collar and tongue for support  \r\nâ€“ Grippy rubber outsole  \r\nâ€“ Reflective accents for added edge  \r\n\r\nIdeal for daily wear, workouts, or standout street style. rubber outsole \r\n', '687156445925a.jpeg', NULL, NULL, '3499', '40,41,42,43,44,45', 'Black,grey,red', 200, 'Nike', '', '[\"687156445ae82.jpeg\",\"687156445b0d9.jpeg\"]', NULL, 4.4, 38, 'all', 'in_stock'),
(466, 'ALEXANDER MCQUEEN', 'alexander-mcqueen', 'Crafted with a luxurious leather upper, the Alexander McQueen sneaker features a clean, minimalist design elevated by its signature chunky sole. The smooth lines and oversized proportions add a bold yet sophisticated edge, while the padded collar and tongue provide enhanced comfort. With meticulous stitching, premium materials, and subtle branding at the heel and tongue, this sneaker delivers a refined, fashion-forward look perfect for both casual and upscale styling. The durable rubber outsole offers reliable grip, making it as functional as it is stylish.', '6877b83a6f98d.jpg', NULL, NULL, '3200', '38,39,40,41,42,43,44,45', 'White body/black heel tab,All black,Black body/white heel tab,black body/black heel tab', 300, '', '', '[\"6877b83a703d8.jpg\",\"6877b83a706a5.jpeg\",\"6877b83a708e7.jpeg\",\"6877b83a70b41.jpeg\"]', NULL, 4, 259, 'all', 'in_stock'),
(468, 'FLUFFY SANDALS', 'fluffy-sandals', 'Crafted for comfort and flair, these fluffy sandals feature an ultra-soft faux fur upper that wraps your feet in cozy warmth. The slip-on design makes them easy to wear, while the cushioned insole ensures lasting comfort throughout the day. Lightweight yet durable, the sole provides support and traction for both indoor lounging and casual outings. The plush texture and stylish silhouette add a touch of luxury to your everyday look, making them a versatile choice for relaxed elegance.', '6877e7bf51b18.jpg', NULL, NULL, '2000', '36,37,38,39,40,41', 'brown,grey,light pink,black,dark pink', 370, '', '', '[\"6877e7bf52198.jpg\",\"6877e7bf523f8.jpg\",\"6877e7bf5267e.jpg\",\"6877e7bf52908.jpg\"]', NULL, 4.8, 58, 'all', 'in_stock');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL,
  `MerchantRequestID` varchar(500) NOT NULL,
  `CheckoutRequestID` varchar(500) NOT NULL,
  `ResultCode` varchar(500) NOT NULL,
  `Amount` int(11) NOT NULL,
  `MpesaReceiptNumber` varchar(500) NOT NULL,
  `PhoneNumber` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`ID`, `MerchantRequestID`, `CheckoutRequestID`, `ResultCode`, `Amount`, `MpesaReceiptNumber`, `PhoneNumber`) VALUES
(1, '10901-120004573-1', 'ws_CO_19072023190603085768168060', '0', 2, 'RGJ7XFLLZR', '254768168060'),
(2, '23315-193823651-1', 'ws_CO_19072023191437398768168060', '0', 1, 'RGJ7XGUMBF', '254768168060');

-- --------------------------------------------------------

--
-- Table structure for table `typing_status`
--

CREATE TABLE `typing_status` (
  `user_id` int(11) NOT NULL,
  `status` enum('typing','idle','','') NOT NULL DEFAULT 'idle'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ud` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `ud`, `email`, `phone`, `address`, `password`, `created_at`, `updated_at`, `remember_token`, `token_expiry`) VALUES
(8, '', 0, 'qwe@gmail.com', NULL, NULL, '$2y$10$KUKcZS248D2WsR1HeDnAg.m0KXU/nkocraKrYbfYZsc.nxOfJPOT.', '2024-12-31 00:09:01', '2024-12-31 00:09:01', NULL, NULL),
(10, '', 0, 'www@gmail.com', NULL, NULL, '$2y$10$HvqBzP7myIuvHopVAe1zkeuO1txt2TJzl3cB7V/Pa/RmXkY9tQ0he', '2025-01-02 17:39:39', '2025-01-02 17:39:39', 'c32e829abc7aa0c036da3e29a289146cd1d1df052a22a6b7b1b861816ba75197', '2025-07-27 00:48:43'),
(11, '', 0, 'kimothojackson1125@gmail.com', NULL, NULL, '$2y$10$xugHz.61w6yh4beQzRfiY.IGl5kdR2pt9Utr0waszl9vsM/TKBqwy', '2025-01-02 21:08:47', '2025-01-02 21:08:47', NULL, NULL),
(12, '', 0, 'freekyone7254@gmail.com', NULL, NULL, '$2y$10$HfKNa5CXtV3HN8eDmFTdJOJWKMlcUsYf/0UGAN8QEe8nn279n4rmi', '2025-01-03 04:06:27', '2025-01-03 04:06:27', '5a51caa8fda0a4b95b4203d3decbe05bf7427c5a401df72e1a24625dc8d79acd', '2025-09-25 14:16:48'),
(21, '', 0, 'chrisrezlatan@gmail.com', NULL, NULL, '$2y$10$Ypx7rQUOXNfskGRP7vkMVu0Do4gy6z5boIRlyVkgtYFYKGO55DSRe', '2025-01-03 22:04:52', '2025-01-03 22:04:52', NULL, NULL),
(31, '', 0, 'Alexisjoker614.@gmail.com', NULL, NULL, '$2y$10$ATRA4HLbBhsttev6zLmwhuhS3FhCw9l4zO2UFZA2CN.GPn.tbw4ZC', '2025-01-04 22:59:50', '2025-01-04 22:59:50', NULL, NULL),
(35, '', 0, 'njengaweshsam@gmail.com', NULL, NULL, '$2y$10$7U7HbAENCDUcQpqc2lBH3uc.OeXpVwp83C.vDAnYh1qhw1YmMIcS6', '2025-01-24 15:11:57', '2025-01-24 15:11:57', NULL, NULL),
(36, '', 0, 'jobkimani206..com@gmail.com', NULL, NULL, '$2y$10$7JjloqVcapzp7dt83jXZPuGMs.lUAUAKHRuI.yFUX9J5E7/97jeoq', '2025-01-25 11:53:32', '2025-01-25 11:53:32', NULL, NULL),
(37, '', 0, 'jobkimani5131@gmail.com', NULL, NULL, '$2y$10$ZTpddInatLXG.HsXMREoZuK6vQmePgrVJflxht/R.fG8KZl0869Rq', '2025-01-26 12:25:43', '2025-01-26 12:25:43', NULL, NULL),
(38, '', 0, 'ryanadrian920@gmail.com', NULL, NULL, '$2y$10$TEmkdaY7YYHVvM6GLmM07.scLzzeOM4k/yn6XG5qJ06H9/BMgI3Fa', '2025-01-28 10:41:45', '2025-01-28 10:41:45', NULL, NULL),
(39, 'Simon Ngugi', 0, 'sngugi172@gmail.com', NULL, NULL, '$2y$10$zCltu9UAR1VfI1Kr6kR7uOMmDK5gJu9g6goUo65ZBiy.g71mGKHEu', '2025-01-29 15:26:22', '2025-01-29 15:26:22', NULL, NULL),
(41, 'Mello', 0, 'melaniegasha@gmail.com', NULL, NULL, '$2y$10$LaDYjrTb3TUH3mZI15Xp6uqFBCOl61ptBcV6b4iQnZ3yqlEyj52bu', '2025-01-30 19:34:53', '2025-01-30 19:34:53', NULL, NULL),
(42, 'Simon Ngugi', 0, 'www5@gmail.com', NULL, NULL, '$2y$10$L1aOhas828Z3tk02sZHk/uw5B19nQybvW3L/8evpQqV5sheEdt85i', '2025-01-31 14:13:43', '2025-01-31 14:13:43', NULL, NULL),
(43, 'Simon Ngugi', 0, 'www52@gmail.com', NULL, NULL, '$2y$10$KFx3btAcBc0VvJu9PGoqvuiY5B57Da6/37RvC5lH/uDVcGZO6l.0e', '2025-01-31 14:30:20', '2025-01-31 14:30:20', NULL, NULL),
(48, 'Simon Ngugi', 0, 'q86we@gmail.com', NULL, NULL, '$2y$10$RI19cbqWbPYqt1EWvRmhI.YdwmvEG3ogB5zTLrCygyXi4w0uGuB/G', '2025-02-01 17:21:55', '2025-02-01 17:21:55', NULL, NULL),
(50, 'Allan', 0, 'allanmwangi800@gmail.com', NULL, NULL, '$2y$10$pQQwPOUI0WCM0GcWEFUV2.4pz5acVtNmxYqzWwFqiE0EsIHyF5N6m', '2025-02-23 20:29:31', '2025-02-23 20:29:31', NULL, NULL),
(51, 'wer', 0, 'wer@gmail.com', NULL, NULL, '$2y$10$aEANEKrjyAHfTmSi2yj4BuydwabwoPfosYuK.2zI64AgcLwks/UZG', '2025-06-26 20:06:50', '2025-06-26 20:06:50', NULL, NULL),
(52, 'Telvin james', 0, 'mjbhbvhvvfx@gmail.com', NULL, NULL, '$2y$10$K436b9EL0QzbHu4RZyMQC.YLMRFjwn//B62yjqgpoyFq8aL7nHZlS', '2025-08-26 20:04:14', '2025-08-26 20:04:14', NULL, NULL),
(53, 'Masika', 0, 'faruoqtest1@gmail.com', NULL, NULL, '$2y$10$k57.6gCOwduozQHddkWVOuLbh7J35BrkkwzFdjJCuqWqBQ1mZsuTW', '2025-11-30 22:31:44', '2025-11-30 22:31:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `featured_products`
--
ALTER TABLE `featured_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mycheckout`
--
ALTER TABLE `mycheckout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`client_id`);

--
-- Indexes for table `orders_made`
--
ALTER TABLE `orders_made`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `typing_status`
--
ALTER TABLE `typing_status`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `featured_products`
--
ALTER TABLE `featured_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=629;

--
-- AUTO_INCREMENT for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `mycheckout`
--
ALTER TABLE `mycheckout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders_made`
--
ALTER TABLE `orders_made`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=487;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `typing_status`
--
ALTER TABLE `typing_status`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
