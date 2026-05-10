<?php
require_once __DIR__ . '/controller/EventController.php';

$eventController = new EventController();
$action = $_GET['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'book_front') {
    $eventId = $_POST['event_id'] ?? null;
    $clientName = trim($_POST['client_name'] ?? '');
    $clientEmail = trim($_POST['client_email'] ?? '');
    $clientPhone = trim($_POST['client_phone'] ?? '');
    $tickets = trim($_POST['nb_tickets'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$eventId || !ctype_digit((string)$eventId)) {
        $error = 'Invalid event.';
    } elseif (strlen($clientName) < 3) {
        $error = 'Client name must be at least 3 characters.';
    } elseif (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^\+?[0-9\s\-]{8,15}$/', $clientPhone)) {
        $error = 'Please enter a valid phone number.';
    } elseif (!ctype_digit($tickets) || (int)$tickets < 1 || (int)$tickets > 10) {
        $error = 'Number of tickets must be between 1 and 10.';
    } else {
        $event = $eventController->show((int)$eventId);
        if (!$event) {
            $error = 'Event not found.';
        } elseif ($event->getStatus() !== 'active' || strtotime($event->getEventDate()) < time()) {
            $error = 'This event is no longer accepting reservations.';
        }
    }

    if (isset($error)) {
        header('Location: myEvents.php?error=' . urlencode($error));
        exit;
    }

    header('Location: myEvents.php?success=1');
    exit;
}

header('Location: myEvents.php');
exit;
