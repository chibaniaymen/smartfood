<?php
require_once 'controller/LocationController.php';

$controller = new LocationController();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'list':
        $search = $_GET['search'] ?? '';
        $sortBy = $_GET['sort'] ?? 'name';
        $order = $_GET['order'] ?? 'ASC';

        $locations = $controller->list($search, $sortBy, $order);
        $stats = $controller->getStats();
        
        $nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';
        include 'view/BackOffice/locationList.php';
        break;
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $controller->add($_POST);
            if (!$error) header('Location: locations.php?action=list');
        }
        include 'view/BackOffice/locationAdd.php';
        break;
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $controller->edit($id, $_POST);
            if (!$error) header('Location: locations.php?action=list');
        }
        $location = $controller->show($id);
        include 'view/BackOffice/locationEdit.php';
        break;
    case 'delete':
        $controller->delete($id);
        header('Location: locations.php?action=list');
        break;
    case 'front':
        $locations = $controller->list();
        include 'view/FrontOffice/locationList.php';
        break;
    case 'show':
        $location = $controller->show($id);
        include 'view/FrontOffice/locationShow.php';
        break;
    default:
        $locations = $controller->list();
        include 'view/BackOffice/locationList.php';
        break;
}
?>
