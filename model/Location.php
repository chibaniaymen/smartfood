<?php
class Location {
    private ?int $id = null;
    private ?string $name = null;
    private ?string $address = null;
    private ?string $city = null;
    private ?string $country = null;
    private ?int $capacity = null;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->city = $data['city'] ?? null;
            $this->country = $data['country'] ?? null;
            $this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
        }
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(string $city): void { $this->city = $city; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(string $country): void { $this->country = $country; }
    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }

    // CRUD Methods
    // ─── Business Logic (Partie Métier) ─────────────────────────
    
    /**
     * Get all locations with Search and Sorting (Recherche & Tri)
     */
    public static function searchAndSort(PDO $db, string $search = '', string $sortBy = 'name', string $order = 'ASC'): array {
        $allowedSorts = ['id', 'name', 'city', 'country', 'capacity'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM locations";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR city LIKE ? OR country LIKE ? OR address LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY $sortBy $order";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get Location Statistics
     */
    public static function getStatistics(PDO $db): array {
        $stats = [];
        
        // Total locations
        $stmt = $db->query("SELECT COUNT(*) FROM locations");
        $stats['total'] = $stmt->fetchColumn();

        // Total capacity
        $stmt = $db->query("SELECT SUM(capacity) FROM locations");
        $stats['total_capacity'] = $stmt->fetchColumn() ?: 0;

        // Distinct cities
        $stmt = $db->query("SELECT COUNT(DISTINCT city) FROM locations");
        $stats['cities'] = $stmt->fetchColumn();

        // Largest venue
        $stmt = $db->query("SELECT MAX(capacity) FROM locations");
        $stats['max_capacity'] = $stmt->fetchColumn() ?: 0;

        return $stats;
    }

    public static function getAll(PDO $db): array {
        return self::searchAndSort($db);
    }

    public static function getById(PDO $db, int $id): ?self {
        $stmt = $db->prepare("SELECT * FROM locations WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function insert(PDO $db): bool {
        $stmt = $db->prepare("INSERT INTO locations (name, address, city, country, capacity) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$this->name, $this->address, $this->city, $this->country, $this->capacity]);
    }

    public function update(PDO $db): bool {
        $stmt = $db->prepare("UPDATE locations SET name = ?, address = ?, city = ?, country = ?, capacity = ? WHERE id = ?");
        return $stmt->execute([$this->name, $this->address, $this->city, $this->country, $this->capacity, $this->id]);
    }

    public static function delete(PDO $db, int $id): bool {
        $stmt = $db->prepare("DELETE FROM locations WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
