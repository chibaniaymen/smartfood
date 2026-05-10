-- ================================================
-- SmartFood - Base de données Restaurants
-- Version : 2.0
-- ================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Base de données : `smartfood`
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table `restaurants`
-- --------------------------------------------------------

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `cuisine` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `votes` int(11) NOT NULL DEFAULT 0,
  `delivery_time` varchar(50) DEFAULT NULL,
  `min_order` decimal(10,2) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `cover_color` varchar(20) DEFAULT '#2d6a4f',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Données : restaurants
-- --------------------------------------------------------

INSERT INTO `restaurants` (`id`, `name`, `slug`, `cuisine`, `description`, `address`, `rating`, `votes`, `delivery_time`, `min_order`, `badge`, `emoji`, `cover_color`) VALUES
(1, 'Green Bowl', 'green-bowl', 'Salades & Bowls', 'Des bowls frais et colorés préparés avec des ingrédients bio locaux.', 'Avenue Habib Bourguiba, Tunis', 4.90, 128, '20–30 min', 15.00, 'Top Noté', '🥗', '#2d6a4f'),
(2, 'Sushi Zen', 'sushi-zen', 'Japonais', 'Sushis artisanaux préparés par des chefs japonais avec du poisson frais du marché.', 'Lac 2, Tunis', 4.70, 89, '25–40 min', 25.00, 'Nouveau', '🍣', '#1a3c5e'),
(3, 'Casa Pizza', 'casa-pizza', 'Italien', 'Pizzas au four à bois avec des tomates San Marzano et mozzarella fraîche.', 'La Marsa, Tunis', 4.50, 204, '30–45 min', 20.00, NULL, '🍕', '#8b3a3a'),
(4, 'Burger Farm', 'burger-farm', 'Burgers Bio', 'Burgers artisanaux avec de la viande bio et des légumes frais du terroir tunisien.', 'El Menzah, Tunis', 4.60, 156, '20–35 min', 18.00, 'Bio', '🍔', '#6b4c2a'),
(5, 'Détox Lab', 'detox-lab', 'Jus & Smoothies', 'Jus pressés à froid, smoothies et shots detox pour votre bien-être quotidien.', 'Les Berges du Lac, Tunis', 4.80, 67, '15–25 min', 12.00, 'Santé', '🧃', '#3a6b2e'),
(6, 'Pasta Fresca', 'pasta-fresca', 'Pâtes Fraîches', 'Pâtes fraîches faites maison avec des sauces traditionnelles italiennes.', 'Gammarth, Tunis', 4.40, 93, '25–40 min', 22.00, NULL, '🍝', '#7a4f1a');

-- --------------------------------------------------------
-- Table `products`
-- --------------------------------------------------------

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `display_category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `votes` int(11) NOT NULL DEFAULT 0,
  `badge` varchar(50) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `orders`
-- --------------------------------------------------------

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `order_items`
-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Données : produits de Green Bowl (restaurant_id = 1)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(1, 'Buddha Bowl Quinoa', 'green-bowl', 'Bowls', 18.50, 'portion', 'Quinoa, avocat, pois chiches rôtis, légumes grillés et sauce tahini maison.', 4.90, 45, 'Best-seller', '🥗'),
(1, 'Salade César Bio', 'green-bowl', 'Salades', 14.00, 'portion', 'Laitue romaine, parmesan, croûtons maison et sauce César sans anchois.', 4.70, 38, NULL, '🥬'),
(1, 'Smoothie Vert', 'green-bowl', 'Boissons', 9.50, 'verre', 'Épinards, pomme verte, gingembre, citron et eau de coco. 100% naturel.', 4.80, 29, 'Detox', '🥤'),
(1, 'Wrap Avocat Falafel', 'green-bowl', 'Wraps', 16.00, 'portion', 'Falafels croustillants, avocat, tomates, concombre et houmous dans un wrap complet.', 4.60, 22, NULL, '🌯'),
(1, 'Acai Bowl', 'green-bowl', 'Bowls', 19.00, 'portion', 'Base acai, granola, banane, fraises, miel et noix de coco râpée.', 5.00, 18, 'Nouveau', '🫐');

-- --------------------------------------------------------
-- Données : produits de Sushi Zen (restaurant_id = 2)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(2, 'Plateau Sushi Mix', 'sushi-zen', 'Plateaux', 45.00, '16 pièces', 'Assortiment de maki, nigiri et california roll avec poisson ultra-frais.', 4.90, 62, 'Top Vente', '🍣'),
(2, 'Ramen Tonkotsu', 'sushi-zen', 'Ramen', 22.00, 'bol', 'Bouillon de porc mijoté 12h, nouilles fraîches, œuf mollet, chashu et champignons.', 4.80, 41, NULL, '🍜'),
(2, 'California Roll', 'sushi-zen', 'Makis', 18.00, '8 pièces', 'Crabe, avocat, concombre et tobiko. Un classique revisité avec finesse.', 4.60, 35, NULL, '🌀'),
(2, 'Tataki Saumon', 'sushi-zen', 'Entrées', 24.00, 'portion', 'Saumon légèrement saisi, sauce ponzu, sésame et ciboulette japonaise.', 4.70, 27, 'Chef', '🐟'),
(2, 'Mochi Glacé', 'sushi-zen', 'Desserts', 12.00, '3 pièces', 'Mochis glacés au thé matcha, mangue et fruits rouges. Fondants et délicats.', 4.50, 19, NULL, '🍡');

-- --------------------------------------------------------
-- Données : produits de Casa Pizza (restaurant_id = 3)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(3, 'Pizza Margherita', 'casa-pizza', 'Pizzas', 22.00, 'pizza', 'Tomate San Marzano, mozzarella fior di latte, basilic frais. La classique parfaite.', 4.50, 88, NULL, '🍕'),
(3, 'Pizza Quattro Stagioni', 'casa-pizza', 'Pizzas', 28.00, 'pizza', 'Jambon, champignons, artichaut, olives. Quatre saveurs en une pizza généreuse.', 4.60, 56, 'Signature', '🍕'),
(3, 'Burrata Fraîche', 'casa-pizza', 'Entrées', 16.00, 'portion', 'Burrata crémeuse, tomates cerises, huile d\'olive extra vierge et pesto de basilic.', 4.70, 44, NULL, '🫙'),
(3, 'Tiramisu Maison', 'casa-pizza', 'Desserts', 11.00, 'portion', 'Tiramisu traditionnel avec mascarpone, café fort et biscuits savoiardi.', 4.80, 61, 'Maison', '🍰'),
(3, 'Limonade Sicilienne', 'casa-pizza', 'Boissons', 7.00, 'verre', 'Limonade fraîche aux citrons de Sicile, menthe et sirop d\'agave.', 4.40, 33, NULL, '🍋');

-- --------------------------------------------------------
-- Données : produits de Burger Farm (restaurant_id = 4)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(4, 'Farm Classic Burger', 'burger-farm', 'Burgers', 26.00, 'burger', 'Bœuf bio 180g, cheddar affiné, laitue, tomate, oignon caramélisé et sauce BBQ maison.', 4.70, 74, 'Best-seller', '🍔'),
(4, 'Crispy Chicken Burger', 'burger-farm', 'Burgers', 24.00, 'burger', 'Poulet croustillant mariné au babeurre, salade coleslaw et pickles maison.', 4.60, 58, NULL, '🐔'),
(4, 'Sweet Potato Fries', 'burger-farm', 'Accompagnements', 9.00, 'portion', 'Frites de patate douce croustillantes servies avec dip au yaourt épicé.', 4.50, 49, NULL, '🍟'),
(4, 'Milkshake Chocolat', 'burger-farm', 'Boissons', 12.00, 'verre', 'Milkshake onctueux au chocolat artisanal, crème fouettée et brownie émietté.', 4.80, 36, NULL, '🍫'),
(4, 'Veggie Burger', 'burger-farm', 'Burgers', 22.00, 'burger', 'Steak de légumineuses maison, avocat, tomate, roquette et mayo au citron.', 4.40, 27, 'Végé', '🥬');

-- --------------------------------------------------------
-- Données : produits de Détox Lab (restaurant_id = 5)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(5, 'Green Detox Shot', 'detox-lab', 'Shots', 6.50, 'shot', 'Céleri, concombre, gingembre, citron et curcuma. Un coup de boost immédiat.', 4.80, 52, 'Detox', '💚'),
(5, 'Smoothie Tropical', 'detox-lab', 'Smoothies', 13.00, 'verre', 'Mangue, ananas, noix de coco, banane et eau de coco. L\'évasion en verre.', 4.90, 44, 'Top', '🌴'),
(5, 'Jus Cold Press Betterave', 'detox-lab', 'Jus Pressés', 11.00, 'bouteille', 'Betterave, pomme, gingembre et citron. Pressé à froid pour garder tous les nutriments.', 4.70, 31, NULL, '❤️'),
(5, 'Protein Shake Vanille', 'detox-lab', 'Protéines', 15.00, 'verre', 'Protéine de whey bio, lait d\'amande, vanille de Madagascar et miel de thym.', 4.60, 22, NULL, '💪'),
(5, 'Infusion Froide Hibiscus', 'detox-lab', 'Infusions', 8.00, 'verre', 'Hibiscus, gingembre, citron et miel. Infusée à froid pendant 12h pour un goût profond.', 4.50, 18, 'Maison', '🌺');

-- --------------------------------------------------------
-- Données : produits de Pasta Fresca (restaurant_id = 6)
-- --------------------------------------------------------

INSERT INTO `products` (`restaurant_id`, `name`, `category`, `display_category`, `price`, `unit`, `description`, `rating`, `votes`, `badge`, `emoji`) VALUES
(6, 'Carbonara Authentique', 'pasta-fresca', 'Pâtes', 26.00, 'portion', 'Spaghetti frais, guanciale, pecorino romano, jaune d\'œuf et poivre noir. Zéro crème.', 4.80, 67, 'Signature', '🍝'),
(6, 'Ravioli Ricotta Épinards', 'pasta-fresca', 'Pâtes Farcies', 28.00, 'portion', 'Ravioli fait main, farci à la ricotta fraîche et épinards, sauce beurre sauge.', 4.70, 43, 'Maison', '🥟'),
(6, 'Focaccia Romarin', 'pasta-fresca', 'Pains', 8.00, 'pièce', 'Focaccia moelleuse à l\'huile d\'olive, romarin frais et fleur de sel de Guérande.', 4.50, 55, NULL, '🫓'),
(6, 'Panna Cotta Coulis Fruits', 'pasta-fresca', 'Desserts', 10.00, 'portion', 'Panna cotta à la vanille de Tahiti, coulis de fruits rouges frais du marché.', 4.60, 38, NULL, '🍮'),
(6, 'Vin Rouge Maison', 'pasta-fresca', 'Boissons', 14.00, 'verre', 'Sélection de vins tunisiens de qualité, accordés avec les plats du chef.', 4.40, 24, NULL, '🍷');

COMMIT;