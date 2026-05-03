<?php
require_once __DIR__ . '/../config.php';

class Review {
    public function getReviewsForEvent(int $eventId): array {
        $db = Config::getConnexion();
        $stmt = $db->prepare("
            SELECT id, event_id, author_name, rating, comment, created_at
            FROM reviews
            WHERE event_id = :event_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['event_id' => $eventId]);
        $results = $stmt->fetchAll();
        return is_array($results) ? $results : [];
    }

    public function getStatsForEvent(int $eventId): array {
        $db = Config::getConnexion();
        $stmt = $db->prepare("
            SELECT 
              ROUND(AVG(rating), 1) as avg_rating,
              COUNT(*) as total_reviews
            FROM reviews
            WHERE event_id = :event_id
        ");
        $stmt->execute(['event_id' => $eventId]);
        $result = $stmt->fetch();
        if (!$result || empty($result['total_reviews'])) {
            return ['avg_rating' => 0.0, 'total_reviews' => 0];
        }
        return [
            'avg_rating' => (float)$result['avg_rating'],
            'total_reviews' => (int)$result['total_reviews']
        ];
    }

    public function getStatsForAllEvents(): array {
        $db = Config::getConnexion();
        $stmt = $db->query("
            SELECT 
              event_id,
              ROUND(AVG(rating), 1) as avg_rating,
              COUNT(*) as total_reviews
            FROM reviews
            GROUP BY event_id
        ");
        $rows = $stmt->fetchAll();
        $stats = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $stats[$row['event_id']] = [
                    'avg_rating' => (float)$row['avg_rating'],
                    'total_reviews' => (int)$row['total_reviews']
                ];
            }
        }
        return $stats;
    }

    public function add(array $data, int $eventId): array {
        $name    = trim($data['author_name'] ?? '');
        $rating  = (int)($data['rating'] ?? 0);
        $comment = trim($data['comment'] ?? '');
        $errors  = [];

        if (empty($name)) {
            $errors[] = "Le nom de l'auteur est obligatoire.";
        }
        
        if ($rating < 1 || $rating > 5) {
            $errors[] = "La note doit être un entier compris entre 1 et 5.";
        }
        
        if (empty($comment)) {
            $errors[] = "Le commentaire est obligatoire.";
        } elseif (strlen($comment) < 10) {
            $errors[] = "Le commentaire doit comporter au moins 10 caractères.";
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $db = Config::getConnexion();
        $stmt = $db->prepare("
            INSERT INTO reviews (event_id, author_name, rating, comment)
            VALUES (:event_id, :author_name, :rating, :comment)
        ");
        $stmt->execute([
            'event_id'    => $eventId,
            'author_name' => $name,
            'rating'      => $rating,
            'comment'     => $comment
        ]);

        return ['success' => true];
    }

    public function delete(int $id): void {
        $db = Config::getConnexion();
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
?>
