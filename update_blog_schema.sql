USE `feane_events`;

-- Mise à jour de la table commentaires
ALTER TABLE `commentaires`
ADD COLUMN `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved' AFTER `content`;
