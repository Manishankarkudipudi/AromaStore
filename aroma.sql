



create database aroma;
use aroma;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


--
-- Database: `aroma`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL ,
  `title` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) NOT NULL,
  `brand` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) 


--
-- Dumping data for table `products`
--


INSERT INTO products (id,title, category, price, brand, image) VALUES
(1,'Apples', 'Fruits', 50, 'Ferero', 'apple.jpg'),
(2,'Face Wash', 'cream', 25, 'Clean&clear', 'facewash.jpg'),
(3,'Onions', 'Vegetables', 33, 'Local', 'oni.jpg'),
(4,'Broccoli', 'Vegetables', 22, 'Local', 'broc.jpg'),
(5,'Tomatoes', 'Vegetables', 24, 'Local', 'tomo.jpg'),
(6,'Tresmme Shampoo ', 'Shampoo', 54, 'Nivea', 'shampoo.jpg'),
(7,'Drinks', 'Drink', 54, 'PEPSI', 'coke.jpg'),
(8,'Lays', 'food', 50, 'PEPSI', 'lays.jpg'),
(9,'Coffee', 'Drink', 25, 'Nestle', 'coffee.jpg'),
(10,'Dairymilk', 'chocolate', 33, 'Dairtmilk', 'choco.jpg'),
(11,'Olive Oil', 'Oil', 22, 'Gold Drop', 'oil.jpg'),
(12,'Rice', 'Food', 24, 'Rice', 'rice3.jpg');



-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_uuid` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'customer',
  PRIMARY KEY (`user_uuid`),
  UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_seq` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_uuid` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_date` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE (`order_seq`),
  FOREIGN KEY (`user_uuid`) REFERENCES `users`(`user_uuid`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;








