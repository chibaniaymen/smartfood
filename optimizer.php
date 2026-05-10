<?php
require_once 'auth_guard.php';
require_once 'controller/EventController.php';

$controller = new EventController();
$optimizations = $controller->getOptimizationData();

include 'view/BackOffice/optimizer.php';
?>
