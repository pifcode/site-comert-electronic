-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gazdă: localhost
-- Timp de generare: dec. 10, 2024 la 05:11 PM
-- Versiune server: 10.4.28-MariaDB
-- Versiune PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `pescarul_expert`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `payment_id` varchar(255) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `shipping_name` varchar(255) NOT NULL,
  `shipping_email` varchar(255) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_county` varchar(100) NOT NULL,
  `shipping_postal_code` varchar(10) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  `amount_eur` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_id`, `payment_status`, `shipping_name`, `shipping_email`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_county`, `shipping_postal_code`, `currency`, `amount_eur`, `order_date`, `created_at`) VALUES
(1, 1, 268.38, 'new', '5HM97670686949240', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 54.00, NULL, '2024-11-30 22:30:32'),
(2, 1, 19.88, 'new', '9W538861NT5988616', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 4.00, NULL, '2024-11-30 22:35:02'),
(3, 1, 198.80, 'new', '9B528885F7841103V', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 40.00, NULL, '2024-11-30 22:35:55'),
(4, 1, 198.80, 'new', '52G83638HX8348107', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 40.00, NULL, '2024-11-30 22:39:39'),
(5, 1, 268.38, 'new', '6P580653XU692142N', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 54.00, NULL, '2024-11-30 22:42:21'),
(6, 1, 492.03, 'new', '87Y79621B4474711T', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 99.00, NULL, '2024-11-30 22:45:55'),
(7, 1, 422.45, 'new', '1B424398N40018506', 'COMPLETED', 'Hultoana Catalina', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 85.00, NULL, '2024-11-30 22:53:57'),
(8, 1, 556.64, 'new', '0D798899AR284863N', 'COMPLETED', 'Patrichi Ionel Florin', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 112.00, NULL, '2024-11-30 22:57:29'),
(9, 1, 352.87, 'new', '1PB583566F086432E', 'COMPLETED', 'Patrichi Ionel Florin', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 71.00, NULL, '2024-11-30 23:01:22'),
(10, 1, 362.81, 'new', '140244009V0091522', 'COMPLETED', 'Patrichi Ionel Costel', 'costelino@yyy.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 73.00, NULL, '2024-12-01 00:49:43'),
(11, 1, 34.79, 'new', '9XJ89849K92805406', 'COMPLETED', 'Catalina Patrichi', 'patrichi@me.com', '0747873817', 'Regina Maria,43', 'Zorleni', 'Vaslui', '737635', 'EUR', 7.00, NULL, '2024-12-01 07:43:24'),
(12, 1, 313.11, 'new', '8JM203844C5790509', 'COMPLETED', 'Simion Silviu', 'sivmondsdf@gmail.com', '9347576489533', 'Sadsasa', 'Barlkad', 'Vasii', '234567', 'EUR', 63.00, NULL, '2024-12-02 17:11:35'),
(13, 2, 422.45, 'new', '3BN1343689144021S', 'COMPLETED', 'test 1', 'tresdsodfjdfj@gfgjfgjf.com', '4556788765432', 'ertyhgfvdcsxdscfv', 'sdfghj', 'sdfghjk', '34567', 'EUR', 85.00, NULL, '2024-12-09 19:56:24'),
(14, 3, 377.72, 'new', '4KB07381FS001333Y', 'COMPLETED', 'sdfgdhhg dhghgdhgdh', 'dfghdhdg@dfggfh.com', '2345675865', 'Rrefgrgrhrt ', 'Barlad', 'Vaslui', '737635', 'EUR', 76.00, NULL, '2024-12-10 15:55:53');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 3, 1, 250.00),
(2, 3, 4, 1, 180.00),
(3, 4, 4, 1, 180.00),
(4, 5, 3, 1, 250.00),
(5, 6, 4, 1, 180.00),
(6, 6, 2, 1, 45.00),
(7, 6, 3, 1, 250.00),
(8, 7, 3, 1, 250.00),
(9, 7, 5, 1, 95.00),
(10, 7, 1, 1, 15.00),
(11, 7, 2, 1, 45.00),
(12, 8, 5, 1, 95.00),
(13, 8, 3, 1, 250.00),
(14, 8, 1, 1, 15.00),
(15, 8, 4, 1, 180.00),
(16, 9, 1, 1, 15.00),
(17, 9, 4, 1, 180.00),
(18, 9, 5, 1, 95.00),
(19, 9, 2, 1, 45.00),
(20, 10, 5, 1, 95.00),
(21, 10, 3, 1, 250.00),
(22, 11, 1, 1, 15.00),
(23, 12, 2, 1, 45.00),
(24, 12, 3, 1, 250.00),
(25, 13, 1, 3, 15.00),
(26, 13, 4, 2, 180.00),
(27, 14, 1, 2, 15.00),
(28, 14, 5, 3, 95.00),
(29, 14, 2, 1, 45.00);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`) VALUES
(1, 'Cârlig Professional', 'Cârlig de înaltă calitate pentru pescuit profesional', 15.00, 'imagini/carlig.jpg', 'carlige', 100, '2024-11-30 15:50:16'),
(2, 'Fir Premium', 'Fir rezistent pentru pescuit în ape adânci', 45.00, 'imagini/fir.jpg', 'fire', 50, '2024-11-30 15:50:16'),
(3, 'Lansetă Pro', 'Lansetă telescopică pentru pescuit la distanță', 250.00, 'imagini/lanseta.jpg', 'lansete', 30, '2024-11-30 15:50:16'),
(4, 'Mulinetă Expert', 'Mulinetă cu rulmenți pentru pescuit de performanță', 180.00, 'imagini/mulineta.jpg', 'mulinete', 40, '2024-11-30 15:50:16'),
(5, 'Năvod Special', 'Năvod special pentru pescuit în ape adânci', 95.00, 'imagini/navod.jpg', 'accesorii', 25, '2024-11-30 15:50:16');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Eliminarea datelor din tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `first_name`, `last_name`, `phone`, `address`) VALUES
(1, 'patrichi', 'patrichi@me.com', '$2y$10$I9XZsYA/THNAO.H.LvTK1.rNxnFV8j1QKq3iw6JesG92jBNqKfvi.', '2024-11-30 17:05:47', '', '', '', ''),
(2, 'test1', 'test1@gmail.com', '$2y$10$jlckDPXmk5yNHJwIuWA4bOIwL5K8LeI36onPtnrc3MagJbNNOjIza', '2024-12-09 19:54:49', NULL, NULL, NULL, NULL),
(3, 'test22', 'test22@dsfdfddfdf.com', '$2y$10$uO8ykxWjCNRKwNm98m55HO/ssYOLStD2uyzTH3MMd2DEz1MAPyPsy', '2024-12-10 15:54:01', 'Ionel', 'Patrichi', '43434455544', 'Regina Maria,43');

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexuri pentru tabele `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexuri pentru tabele `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pentru tabele `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pentru tabele `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pentru tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constrângeri pentru tabele `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
