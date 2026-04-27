<?php
require_once __DIR__ . '/../model/Location.php';
require_once __DIR__ . '/../config.php';

class LocationController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function list(string $search = '', string $sortBy = 'name', string $order = 'ASC'): array {
        return Location::searchAndSort($this->db, $search, $sortBy, $order);
    }

    public function getStats(): array {
        return Location::getStatistics($this->db);
    }

    public function show(int $id): ?Location {
        return Location::getById($this->db, $id);
    }

    public function add(array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $location = new Location($data);
        if ($location->insert($this->db)) {
            return null;
        }
        return "Failed to insert location.";
    }

    public function edit(int $id, array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $location = new Location(array_merge($data, ['id' => $id]));
        if ($location->update($this->db)) {
            return null;
        }
        return "Failed to update location.";
    }

    public function delete(int $id): bool {
        return Location::delete($this->db, $id);
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
