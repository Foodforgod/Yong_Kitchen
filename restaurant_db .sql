SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
(1, 'Classic Burger', 'Beef patty with lettuce and tomato', 8.00, 'Main Course', 96, 'https://mccormick.widen.net/content/mrqnc4vbjy/original/frenchs_burger_styled-image_800x800.png'),
(2, 'Iced Latte', 'Cold brewed coffee with milk', 4.50, 'Drinks', 43, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiTy7K9gzHduql4bUBCCFi-4NceO7NW-WDbg&s'),
(3, 'French Fries', 'Crispy golden fries', 3.00, 'Appetizer', 68, 'https://images.themodernproper.com/production/posts/2022/Homemade-French-Fries_8.jpg?w=1200&h=1200&q=60&fm=jpg&fit=crop&dm=1662474181&s=15046582e76b761a200998df2dcad0fd'),
(4, 'Chicken Chop', 'deep-fried in a crispy batter or grilled to tender perfection', 9.90, 'Main Course', 70, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrmYyVR0aIle12ViUSqddpw2igIHpzzqUcaA&s'),
(5, 'Bistecca alla Fiorentina', 'Florentine-style beef steak', 25.00, 'Main Course', 46, 'https://d21klxpge3tttg.cloudfront.net/wp-content/uploads/2016/02/BISTECCA-093022.jpg'),
(6, 'Porchetta di Ariccia /100g', 'boneless pork roast with origins in the province of Rome', 28.00, 'Main Course', 65, 'https://www.saporie.com/assets/it/product/salumi-tipici/Saporiecom-porchetta-di-ariccia-igp--171668.jpg?_u=369ac81081b1c356a3edf1897702d9360bea3ff5'),
(7, 'Tiramisu', 'featuring layers of coffee-soaked ladyfinger biscuits', 10.00, 'Dessert', 49, 'https://www.ruchikrandhap.com/wp-content/uploads/2018/03/Tiramisu-1-1.jpg');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `amount_change` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `orders` (`id`, `table_number`, `total_price`, `status`, `created_at`, `amount_paid`, `amount_change`) VALUES
(1, 'T2', 4.50, 'completed', '2026-05-05 07:20:33', 0.00, 0.00),
(2, 'T3', 3.00, 'completed', '2026-05-05 07:32:39', 0.00, 0.00),
(3, 'T5', 3.00, 'completed', '2026-05-05 07:34:06', 0.00, 0.00),
(4, 'T2', 4.50, 'completed', '2026-05-05 07:43:01', 0.00, 0.00),
(5, 'T3', 3.00, 'completed', '2026-05-05 07:45:34', 0.00, 0.00),
(6, 'T4', 8.00, 'completed', '2026-05-05 07:46:00', 0.00, 0.00),
(7, 'T4', 6.00, 'completed', '2026-05-05 08:10:07', 0.00, 0.00),
(8, 'T3', 31.00, 'completed', '2026-05-06 01:51:12', 0.00, 0.00),
(9, 'T2', 6.00, 'completed', '2026-05-06 01:59:25', 0.00, 0.00),
(10, 'T2', 36.00, 'completed', '2026-05-06 06:56:42', 0.00, 0.00),
(11, 'T3', 19.50, 'completed', '2026-05-08 03:21:39', 0.00, 0.00),
(12, 'T4', 27.50, 'completed', '2026-05-08 05:21:10', 100.00, 0.00),
(13, 'T2', 9.90, 'completed', '2026-05-11 07:19:11', 0.00, 0.00),
(14, 'T2', 9.90, 'ready', '2026-05-11 07:41:02', 0.00, 0.00),
(15, 'T2', 69.99, 'completed', '2026-05-11 07:44:32', 70.00, 0.00),
(16, 'T3', 9.90, 'completed', '2026-05-11 07:45:22', 10.00, 0.00),
(17, 'Table 11', 81.90, 'completed', '2026-05-11 08:09:42', 100.00, 0.00),
(18, 'Table 13', 94.39, 'completed', '2026-05-12 02:27:53', 0.00, 0.00),
(19, 'Table 12', 175.98, 'completed', '2026-05-12 02:33:32', 0.00, 0.00),
(20, 'Table 12', 53.00, 'completed', '2026-05-12 06:35:24', 60.00, 0.00);



CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `item_status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `remarks`, `item_status`) VALUES
(1, 1, 2, 1, '', 'pending'),
(2, 2, 3, 1, '', 'pending'),
(3, 3, 3, 1, '', 'pending'),
(4, 4, 2, 1, '', 'pending'),
(5, 5, 3, 1, '', 'pending'),
(6, 6, 1, 1, '', 'pending'),
(7, 7, 3, 1, '', 'pending'),
(8, 7, 3, 1, '', 'pending'),
(9, 8, 1, 2, '', 'pending'),
(10, 8, 2, 2, '', 'pending'),
(11, 8, 3, 2, '', 'pending'),
(12, 9, 3, 2, '', 'pending'),
(13, 10, 4, 3, '', 'pending'),
(14, 11, 4, 1, '', 'pending'),
(15, 11, 3, 1, '', 'pending'),
(16, 11, 2, 1, '', 'pending'),
(17, 12, 4, 1, 'No veggies', 'pending'),
(18, 12, 1, 1, '', 'pending'),
(19, 12, 3, 1, '', 'pending'),
(20, 12, 2, 1, '', 'pending'),
(21, 13, 4, 1, '', 'pending'),
(22, 14, 4, 1, '', 'done'),
(23, 15, 6, 1, '', 'done'),
(24, 16, 4, 1, 'No veggies', 'done'),
(25, 17, 5, 1, 'No veggies', 'done'),
(26, 17, 5, 1, '', 'done'),
(27, 17, 4, 1, '', 'done'),
(28, 18, 4, 1, '', 'done'),
(29, 18, 7, 1, '', 'pending'),
(30, 18, 6, 1, '', 'pending'),
(31, 18, 3, 1, '', 'pending'),
(32, 18, 2, 1, '', 'pending'),
(33, 19, 6, 1, '', 'done'),
(34, 19, 6, 1, '', 'done'),
(35, 19, 5, 1, '', 'done'),
(36, 20, 6, 1, '', 'done'),
(37, 20, 5, 1, '', 'done');


CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `price_at_time` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

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

ALTER TABLE `order_item_addons`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL;
COMMIT;
