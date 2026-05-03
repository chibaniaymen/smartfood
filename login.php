<?php
require_once 'controller/AuthController.php';
$controller = new AuthController();

if ($controller->isLoggedIn()) {
    header('Location: view/BackOffice/dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->login($_POST);
    if (isset($result['success'])) {
        header('Location: view/BackOffice/dashboard.php');
        exit;
    }
    $errors = $result['errors'] ?? [];
}

include 'view/auth/login.php';
?>
