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

    // CRUD Methods
    // ─── Business Logic (Partie Métier) ─────────────────────────
    
    /**
     * Get all events with Search and Sorting (Recherche & Tri)
     */
    public static function searchAndSort(PDO $db, string $search = '', string $sortBy = 'event_date', string $order = 'ASC'): array {
        $allowedSorts = ['id', 'title', 'event_date', 'price', 'max_attendees', 'status', 'location_name'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'event_date';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT e.*, l.name as location_name 
                FROM events e 
                LEFT JOIN locations l ON e.location_id = l.id";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE e.title LIKE ? OR e.description LIKE ? OR e.status LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY $sortBy $order";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get Event Statistics
     */
    public static function getStatistics(PDO $db): array {
        $stats = [];
        
        // Total events
        $stmt = $db->query("SELECT COUNT(*) FROM events");
        $stats['total'] = $stmt->fetchColumn();

        // Active events
        $stmt = $db->query("SELECT COUNT(*) FROM events WHERE status = 'active'");
        $stats['active'] = $stmt->fetchColumn();

        // Average Price
        $stmt = $db->query("SELECT AVG(price) FROM events");
        $stats['avg_price'] = round((float)$stmt->fetchColumn(), 2);

        // Events this month
        $stmt = $db->query("SELECT COUNT(*) FROM events WHERE MONTH(event_date) = MONTH(CURRENT_DATE()) AND YEAR(event_date) = YEAR(CURRENT_DATE())");
        $stats['this_month'] = $stmt->fetchColumn();

        return $stats;
    }

    public static function getAll(PDO $db): array {
        return self::searchAndSort($db);
    }

    public static function getById(PDO $db, int $id): ?self {
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function insert(PDO $db): bool {
        $stmt = $db->prepare("INSERT INTO events (title, description, event_date, location_id, price, max_attendees, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$this->title, $this->description, $this->event_date, $this->location_id, $this->price, $this->max_attendees, $this->status]);
    }

    public function update(PDO $db): bool {
        $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, location_id = ?, price = ?, max_attendees = ?, status = ? WHERE id = ?");
        return $stmt->execute([$this->title, $this->description, $this->event_date, $this->location_id, $this->price, $this->max_attendees, $this->status, $this->id]);
    }

    public static function delete(PDO $db, int $id): bool {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
