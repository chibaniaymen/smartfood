<?php
require_once __DIR__ . '/../model/Location.php';
require_once __DIR__ . '/../config.php';

class LocationController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function list(string $search = '', string $sortBy = 'name', string $order = 'ASC'): array {
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
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM locations");
        $stats['total'] = $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT SUM(capacity) FROM locations");
        $stats['total_capacity'] = $stmt->fetchColumn() ?: 0;

        $stmt = $this->db->query("SELECT COUNT(DISTINCT city) FROM locations");
        $stats['cities'] = $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT MAX(capacity) FROM locations");
        $stats['max_capacity'] = $stmt->fetchColumn() ?: 0;

        return $stats;
    }

    public function show(int $id): ?Location {
        $stmt = $this->db->prepare("SELECT * FROM locations WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Location($data) : null;
    }

    public function add(array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $sql = "INSERT INTO locations (name, address, city, country, capacity) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            $data['name'], 
            $data['address'], 
            $data['city'], 
            $data['country'] ?? null, 
            $data['capacity']
        ]);

        return $success ? null : "Failed to insert location.";
    }

    public function edit(int $id, array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $sql = "UPDATE locations SET name = ?, address = ?, city = ?, country = ?, capacity = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            $data['name'], 
            $data['address'], 
            $data['city'], 
            $data['country'] ?? null, 
            $data['capacity'], 
            $id
        ]);

        return $success ? null : "Failed to update location.";
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM locations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function validate(array $data): ?string {
        if (empty($data['name'])) return "Name is required.";
        if (empty($data['address'])) return "Address is required.";
        if (empty($data['city'])) return "City is required.";
        if (isset($data['capacity']) && $data['capacity'] < 0) return "Please enter a valid capacity.";
        return null;
    }
}
?>
