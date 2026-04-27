<?php
require_once 'controller/EventController.php';
require_once 'controller/LocationController.php';

$controller = new EventController();
$locController = new LocationController();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'list':
        $search = $_GET['search'] ?? '';
        $sortBy = $_GET['sort'] ?? 'event_date';
        $order = $_GET['order'] ?? 'ASC';
        
        $events = $controller->list($search, $sortBy, $order);
        $stats = $controller->getStats();
        
        // Helper for sorting URLs
        $nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';
        include 'view/BackOffice/eventList.php';
        break;
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $controller->add($_POST);
            if (!$error) header('Location: events.php?action=list');
        }
        $locations = $locController->list();
        include 'view/BackOffice/eventAdd.php';
        break;
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $controller->edit($id, $_POST);
            if (!$error) header('Location: events.php?action=list');
        }
        $event = $controller->show($id);
        $locations = $locController->list();
        include 'view/BackOffice/eventEdit.php';
        break;
    case 'delete':
        $controller->delete($id);
        header('Location: events.php?action=list');
        break;
    case 'front':
        $events = $controller->list();
        include 'view/FrontOffice/eventList.php';
        break;
    case 'show':
        $event = $controller->show($id);
        include 'view/FrontOffice/eventShow.php';
        break;
    default:
        $events = $controller->list();
        include 'view/BackOffice/eventList.php';
        break;
}
?>