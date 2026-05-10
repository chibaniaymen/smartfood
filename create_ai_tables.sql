USE `feane_events`;

-- Création de la table tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tag_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table article_tag (relation pivot)
CREATE TABLE IF NOT EXISTS `article_tag` (
  `article_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`article_id`, `tag_id`),
  CONSTRAINT `fk_article_tag_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_article_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table user_history (suivi des vues pour recommandation)
CREATE TABLE IF NOT EXISTS `user_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(255) NOT NULL,
  `article_id` INT UNSIGNED NOT NULL,
  `viewed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`user_id`),
  INDEX (`session_id`),
  CONSTRAINT `fk_history_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table user_profile (scores d'intérêts pour recommandation)
CREATE TABLE IF NOT EXISTS `user_profile` (
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(255) NOT NULL,
  `tag` VARCHAR(100) NOT NULL,
  `score` INT NOT NULL DEFAULT 1,
  UNIQUE KEY `uk_user_session_tag` (`user_id`, `session_id`, `tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
