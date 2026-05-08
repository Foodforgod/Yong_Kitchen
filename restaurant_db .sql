SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, '1234genelol', '$2y$10$cDM/PkywJkEA8AAbq107Eu6b.dEr0WpjfAAcjnAdthcYiFQtMFHt6');


CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items` (`id`, `name`, `description`, `price`, `category`, `stock`, `image_path`) VALUES
(1, 'Classic Burger', 'Beef patty with lettuce and tomato', 8.00, 'Food', 97, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTeCacsKFVUFTWs2O_nNZ-liKRkUT8eU8zBHQ&s'),
(2, 'Iced Latte', 'Cold brewed coffee with milk', 4.50, 'Drinks', 46, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiTy7K9gzHduql4bUBCCFi-4NceO7NW-WDbg&s'),
(3, 'French Fries', 'Crispy golden fries', 3.00, 'Food', 71, 'https://www.allrecipes.com/thmb/HJ1tuIo-zUOO_ReZk77k4npojTg=/0x512/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/50223-homemade-crispy-seasoned-french-fries-VAT-Beauty-4x3-789ecb2eaed34d6e879b9a93dd56a50a.jpg'),
(4, 'Chicken Chop', ' deep-fried in a crispy batter or grilled to tender perfection', 12.00, 'Food', 77, 'https://www.unileverfoodsolutions.com.my/dam/global-ufs/mcos/SEA/calcmenu/recipes/MY-recipes/general/chicken-chop-kegemaran-malaysia/main-header.jpg');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `table_number`, `total_price`, `status`, `created_at`) VALUES
(1, 'T2', 4.50, 'completed', '2026-05-05 07:20:33'),
(2, 'T3', 3.00, 'completed', '2026-05-05 07:32:39'),
(3, 'T5', 3.00, 'completed', '2026-05-05 07:34:06'),
(4, 'T2', 4.50, 'completed', '2026-05-05 07:43:01'),
(5, 'T3', 3.00, 'completed', '2026-05-05 07:45:34'),
(6, 'T4', 8.00, 'completed', '2026-05-05 07:46:00'),
(7, 'T4', 6.00, 'ready', '2026-05-05 08:10:07'),
(8, 'T3', 31.00, 'completed', '2026-05-06 01:51:12'),
(9, 'T2', 6.00, 'completed', '2026-05-06 01:59:25'),
(10, 'T2', 36.00, 'ready', '2026-05-06 06:56:42');

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `remarks`) VALUES
(1, 1, 2, 1, ''),
(2, 2, 3, 1, ''),
(3, 3, 3, 1, ''),
(4, 4, 2, 1, ''),
(5, 5, 3, 1, ''),
(6, 6, 1, 1, ''),
(7, 7, 3, 1, ''),
(8, 7, 3, 1, ''),
(9, 8, 1, 2, ''),
(10, 8, 2, 2, ''),
(11, 8, 3, 2, ''),
(12, 9, 3, 2, ''),
(13, 10, 4, 3, '');


ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL;
COMMIT;
