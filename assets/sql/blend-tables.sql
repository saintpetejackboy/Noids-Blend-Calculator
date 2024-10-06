--
-- Database: `mr`
--

-- --------------------------------------------------------

-- Table structure for table `attributes`
CREATE TABLE `attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `emoji` varchar(10) NOT NULL,
  `color` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `blends`
CREATE TABLE `blends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `blend_images`
CREATE TABLE `blend_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blend_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `blend_id` (`blend_id`),
  CONSTRAINT `blend_images_ibfk_1` FOREIGN KEY (`blend_id`) REFERENCES `blends` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `blend_ingredients`
CREATE TABLE `blend_ingredients` (
  `blend_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `grams` decimal(10,3) DEFAULT 0.000,
  `price` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`blend_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `blend_ingredients_ibfk_1` FOREIGN KEY (`blend_id`) REFERENCES `blends` (`id`),
  CONSTRAINT `blend_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `blend_notes`
CREATE TABLE `blend_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blend_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `blend_id` (`blend_id`),
  CONSTRAINT `blend_notes_ibfk_1` FOREIGN KEY (`blend_id`) REFERENCES `blends` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `ingredients`
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `ingredient_attributes`
CREATE TABLE `ingredient_attributes` (
  `ingredient_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  PRIMARY KEY (`ingredient_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`),
  CONSTRAINT `ingredient_attributes_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  CONSTRAINT `ingredient_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
