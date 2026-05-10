<?php
class Event {
    private ?int $id = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $event_date = null;
    private ?int $location_id = null;
    private ?float $price = null;
    private ?int $max_attendees = null;
    private ?string $status = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->title = $data['title'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->event_date = $data['event_date'] ?? null;
            $this->location_id = $data['location_id'] ?? null;
            $this->price = isset($data['price']) ? (float)$data['price'] : null;
            $this->max_attendees = isset($data['max_attendees']) ? (int)$data['max_attendees'] : null;
            $this->status = $data['status'] ?? 'active';
        }
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function getEventDate(): ?string { return $this->event_date; }
    public function setEventDate(string $event_date): void { $this->event_date = $event_date; }
    public function getLocationId(): ?int { return $this->location_id; }
    public function setLocationId(int $location_id): void { $this->location_id = $location_id; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function getMaxAttendees(): ?int { return $this->max_attendees; }
    public function setMaxAttendees(int $max_attendees): void { $this->max_attendees = $max_attendees; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }
}
?>
