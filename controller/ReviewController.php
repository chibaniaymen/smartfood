<?php
require_once __DIR__ . '/../model/Review.php';

class ReviewController {
    private $model;

    public function __construct() {
        $this->model = new Review();
    }

    public function getReviewsForEvent(int $eventId): array {
        return $this->model->getReviewsForEvent($eventId);
    }

    public function getStatsForEvent(int $eventId): array {
        return $this->model->getStatsForEvent($eventId);
    }

    public function getStatsForAllEvents(): array {
        return $this->model->getStatsForAllEvents();
    }

    public function add(array $post, int $eventId): array {
        return $this->model->add($post, $eventId);
    }

    public function delete(int $id): void {
        $this->model->delete($id);
    }
}
?>
