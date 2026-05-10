<?php
require_once __DIR__ . '/../model/Review.php';
require_once __DIR__ . '/../config.php';

class ReviewController {
    private PDO $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Get all reviews for a specific event.
     */
    public function getReviewsForEvent(int $eventId): array {
        $stmt = $this->db->prepare("
            SELECT id, event_id, author_name, rating, comment, created_at
            FROM reviews
            WHERE event_id = :event_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['event_id' => $eventId]);
        $results = $stmt->fetchAll();
        return is_array($results) ? $results : [];
    }

    /**
     * Get average rating and total review count for an event.
     */
    public function getStatsForEvent(int $eventId): array {
        $stmt = $this->db->prepare("
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

    /**
     * Get statistics for all events in a single call.
     */
    public function getStatsForAllEvents(): array {
        $stmt = $this->db->query("
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

    /**
     * Validate and add a new review.
     */
    public function add(array $data, int $eventId): array {
        $name    = trim($data['author_name'] ?? '');
        $rating  = (int)($data['rating'] ?? 0);
        $comment = trim($data['comment'] ?? '');
        $errors  = [];

        // Simple validation logic
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

        $stmt = $this->db->prepare("
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

    /**
     * Delete a review by ID.
     */
    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
?>
