<?php
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../config.php';

class EventController {
    private PDO $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function list(string $search = '', string $sortBy = 'event_date', string $order = 'ASC'): array {
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
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM events");
        $stats['total'] = $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM events WHERE status = 'active'");
        $stats['active'] = $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT AVG(price) FROM events");
        $stats['avg_price'] = round((float)$stmt->fetchColumn(), 2);

        $stmt = $this->db->query("SELECT COUNT(*) FROM events WHERE MONTH(event_date) = MONTH(CURRENT_DATE()) AND YEAR(event_date) = YEAR(CURRENT_DATE())");
        $stats['this_month'] = $stmt->fetchColumn();

        return $stats;
    }

    public function getOptimizationData(): array {
        $sql = "SELECT e.id, e.title, e.event_date, e.price, e.max_attendees, e.status, 
                       l.name as location_name, l.capacity 
                FROM events e 
                LEFT JOIN locations l ON e.location_id = l.id
                WHERE e.status = 'active'";
        $stmt = $this->db->query($sql);
        $events = $stmt->fetchAll();

        $optimizationList = [];
        foreach ($events as $event) {
            $max_attendees = (int)$event['max_attendees'];
            $capacity = (int)$event['capacity'];
            $price = (float)$event['price'];

            // Calculate fill rate
            if ($capacity > 0) {
                $fill_rate = ($max_attendees / $capacity) * 100;
            } else {
                $fill_rate = 0;
            }

            $revenue = $max_attendees * $price;
            
            $status_color = 'success';
            $suggestion = '✅ Capacité optimale. Aucun changement requis.';

            if ($fill_rate >= 100) {
                $status_color = 'danger';
                $suggestion = '🚨 Capacité maximale atteinte ! Envisagez de changer pour une salle plus grande ou de clôturer les ventes.';
            } elseif ($fill_rate >= 80) {
                $status_color = 'warning';
                $suggestion = '🔥 Événement presque complet ! Pensez à augmenter le prix des derniers billets.';
            } elseif ($fill_rate > 0 && $fill_rate < 40) {
                $status_color = 'info';
                $suggestion = '⚠️ Faible affluence prévue. Envisagez une salle plus petite pour réduire les coûts ou lancez une promotion.';
            }

            $optimizationList[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'location' => $event['location_name'] ?? 'Non assignée',
                'capacity' => $capacity,
                'attendees' => $max_attendees,
                'fill_rate' => round($fill_rate, 2),
                'revenue' => $revenue,
                'status_color' => $status_color,
                'suggestion' => $suggestion
            ];
        }

        return $optimizationList;
    }

    public function show(int $id): ?Event {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Event($data) : null;
    }

    public function add(array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $sql = "INSERT INTO events (title, description, event_date, location_id, price, max_attendees, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            $data['title'], 
            $data['description'] ?? null, 
            $data['event_date'], 
            $data['location_id'], 
            $data['price'], 
            $data['max_attendees'] ?? null, 
            $data['status'] ?? 'active'
        ]);

        return $success ? null : "Failed to insert event.";
    }

    public function edit(int $id, array $data): ?string {
        $error = $this->validate($data);
        if ($error) return $error;

        $sql = "UPDATE events SET title = ?, description = ?, event_date = ?, location_id = ?, price = ?, max_attendees = ?, status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            $data['title'], 
            $data['description'] ?? null, 
            $data['event_date'], 
            $data['location_id'], 
            $data['price'], 
            $data['max_attendees'] ?? null, 
            $data['status'] ?? 'active', 
            $id
        ]);

        return $success ? null : "Failed to update event.";
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function duplicate(int $id): bool {
        $event = $this->show($id);
        if (!$event) return false;

        $data = [
            'title' => 'Copie de ' . $event->getTitle(),
            'description' => $event->getDescription(),
            'event_date' => $event->getEventDate(),
            'location_id' => $event->getLocationId(),
            'price' => $event->getPrice(),
            'max_attendees' => $event->getMaxAttendees(),
            'status' => 'active'
        ];

        return $this->add($data) === null;
    }

    public function complete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE events SET status = 'completed' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function validate(array $data): ?string {
        if (empty($data['title'])) return "Title is required.";
        if (empty($data['event_date'])) return "Date is required.";
        if (!isset($data['location_id']) || empty($data['location_id'])) return "Location is required.";
        if (isset($data['price']) && $data['price'] < 0) return "Please enter a valid price.";
        return null;
    }

    public function updateDescription(int $id, string $description): void {
        $stmt = $this->db->prepare("UPDATE events SET description = ? WHERE id = ?");
        $stmt->execute([$description, $id]);
    }
}
?>
