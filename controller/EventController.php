<?php
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../config.php';

class EventController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function list(string $search = '', string $sortBy = 'event_date', string $order = 'ASC'): array {
        return Event::searchAndSort($this->db, $search, $sortBy, $order);
    }

    public function getStats(): array {
        return Event::getStatistics($this->db);
    }

    public function show(int $id): ?Event {
        return Event::getById($this->db, $id);
    }

    public function add(array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $event = new Event($data);
        if ($event->insert($this->db)) {
            return null; // Success
        }
        return "Failed to insert event.";
    }

    public function edit(int $id, array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $event = new Event($data);
        $event = new Event(array_merge($data, ['id' => $id])); // Ensure ID is set
        if ($event->update($this->db)) {
            return null;
        }
        return "Failed to update event.";
    }

    public function delete(int $id): bool {
        return Event::delete($this->db, $id);
    }

    private function validate(array $data): ?string {
        if (empty($data['title'])) return "Title is required.";
        if (empty($data['event_date'])) return "Date is required.";
        if (!isset($data['location_id']) || empty($data['location_id'])) return "Location is required.";
        if (isset($data['price']) && $data['price'] < 0) return "Please enter a valid price.";
        return null;
    }
}
?>
