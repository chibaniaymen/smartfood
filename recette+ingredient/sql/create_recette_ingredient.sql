-- Migration SQL: create tables 'recette' and 'ingredient'
-- Run this in phpMyAdmin or via CLI on database `projet`

CREATE TABLE IF NOT EXISTS `recette` (
  `id_recette` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `instructions` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_recette`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ingredient` (
  `id_ingredient` INT NOT NULL AUTO_INCREMENT,
  `id_recette` INT NOT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `quantite` VARCHAR(100) DEFAULT NULL,
  `unite` VARCHAR(20) DEFAULT NULL,
  `categorie` VARCHAR(255) DEFAULT NULL,
  `calories` FLOAT DEFAULT 0,
  `proteines` FLOAT DEFAULT 0,
  `glucides` FLOAT DEFAULT 0,
  `lipides` FLOAT DEFAULT 0,
  `fibres` FLOAT DEFAULT 0,
  `sucre` FLOAT DEFAULT 0,
  `sel` FLOAT DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ingredient`),
  KEY `fk_ingredient_recette_idx` (`id_recette`),
  CONSTRAINT `fk_ingredient_recette` FOREIGN KEY (`id_recette`) REFERENCES `recette` (`id_recette`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
