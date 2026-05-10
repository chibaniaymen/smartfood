<?php
require_once __DIR__ . '/controller/AuthController.php';
if (!(new AuthController())->isLoggedIn()) {
    $path = strpos($_SERVER['REQUEST_URI'], 'view/BackOffice') !== false ? '../../login.php' : 'login.php';
    header("Location: $path");
    exit;
}
?>
