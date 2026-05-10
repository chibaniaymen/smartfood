-- SMARTFOOD - Ajout de la table users pour l'authentification
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compte administrateur de démonstration
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrateur', 'admin@smartfood.local', '$2y$10$E1g5Qn8WnEkiIEChm/8z0uJlZXBGPaY88cYJwr0UjA4WwB5w6Ghx6', 'admin'),
('Utilisateur', 'user@smartfood.local', '$2y$10$wC4V8LCBppP.V1y87E4JyuR8vyS1NwksdICSCa7mPjL3i9nH8bP2C', 'user');

-- Mot de passe admin : admin123
-- Mot de passe utilisateur : user123
