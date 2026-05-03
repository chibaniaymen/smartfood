<?php
require_once '../../auth_guard.php';
require_once '../../config.php';
require_once '../../controller/EventController.php';
require_once '../../controller/LocationController.php';

$db = Config::getConnexion();

// Fetch real data from database
$eventController = new EventController();
$locController = new LocationController();

$events = $eventController->list();
$locations = $locController->list();

$totalEvents = count($events);
$totalLocations = count($locations);
$activeEvents = count(array_filter($events, fn($e) => $e['status'] == 'active'));
$totalCapacity = array_sum(array_column($locations, 'capacity'));
$totalRevenue = array_sum(array_column($events, 'price'));
$upcomingEvents = array_filter($events, fn($e) => strtotime($e['event_date']) > time());
$pastEvents = array_filter($events, fn($e) => strtotime($e['event_date']) <= time());

// Group events by month for chart
$monthlyEvents = [];
foreach ($events as $e) {
    $month = date('M', strtotime($e['event_date']));
    $monthlyEvents[$month] = ($monthlyEvents[$month] ?? 0) + 1;
}

// Group events by status for chart
$statusCounts = ['active' => 0, 'cancelled' => 0, 'completed' => 0];
foreach ($events as $e) {
    $statusCounts[$e['status']] = ($statusCounts[$e['status']] ?? 0) + 1;
}

// Cities from locations
$cities = array_unique(array_column($locations, 'city'));

include 'dashboard_view.php';
?>
