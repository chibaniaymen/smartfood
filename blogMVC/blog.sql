-- Base de données et tables pour le blog Feane
CREATE DATABASE IF NOT EXISTS `feane_blog` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `feane_blog`;

CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `commentaires` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` INT UNSIGNED NOT NULL,
  `author` VARCHAR(100) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`article_id`),
  CONSTRAINT `fk_commentaire_article`
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `articles` (`title`, `content`) VALUES
('Bienvenue sur le blog Feane', 'Ceci est le premier article du blog. Utilisez l\'espace admin pour créer, modifier ou supprimer des articles.'),
('Conseils pour votre restaurant', 'Partagez des idées, des recettes et des actualités du restaurant ici. Le système supporte également les commentaires.');

INSERT INTO `commentaires` (`article_id`, `author`, `content`) VALUES
(1, 'Antoine', 'Super article ! J\'aime beaucoup le nouveau design.'),
(2, 'Sophie', 'Merci pour ces conseils, très utiles.');
