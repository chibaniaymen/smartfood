<?php

/**
 * Review Model
 * Simple data object representing a review.
 */
class Review {
    private ?int $id = null;
    private ?int $event_id = null;
    private ?string $author_name = null;
    private ?int $rating = null;
    private ?string $comment = null;
    private ?string $created_at = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->event_id = $data['event_id'] ?? null;
            $this->author_name = $data['author_name'] ?? null;
            $this->rating = $data['rating'] ?? null;
            $this->comment = $data['comment'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getEventId(): ?int { return $this->event_id; }
    public function getAuthorName(): ?string { return $this->author_name; }
    public function getRating(): ?int { return $this->rating; }
    public function getComment(): ?string { return $this->comment; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}
?>
