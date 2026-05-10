<?php
require_once 'controller/EventController.php';
require_once 'controller/ReviewController.php';

$controller = new EventController();
$events = $controller->list();

$reviewController = new ReviewController();
$reviewStats = $reviewController->getStatsForAllEvents();

include 'view/FrontOffice/myEventsView.php';
?>
