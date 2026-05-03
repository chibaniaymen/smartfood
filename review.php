<?php
require_once 'controller/ReviewController.php';
require_once 'controller/EventController.php';

$controller = new ReviewController();
$action     = $_GET['action'] ?? 'show';
$eventId    = (int)($_GET['event_id'] ?? 0);
$id         = (int)($_GET['id'] ?? 0);

switch ($action) {

  case 'show':
    $event    = (new EventController())->show($eventId);
    if (!$event) {
        header('Location: myEvents.php');
        exit;
    }
    $reviews  = $controller->getReviewsForEvent($eventId);
    $stats    = $controller->getStatsForEvent($eventId);
    $errors   = [];
    include 'view/FrontOffice/eventReviews.php';
    break;

  case 'add':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $result = $controller->add($_POST, $eventId);
      if (isset($result['success'])) {
        header('Location: review.php?action=show&event_id=' . $eventId);
        exit;
      }
      $errors  = $result['errors'] ?? [];
      $event   = (new EventController())->show($eventId);
      $reviews = $controller->getReviewsForEvent($eventId);
      $stats   = $controller->getStatsForEvent($eventId);
      include 'view/FrontOffice/eventReviews.php';
    }
    break;

  case 'delete':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $controller->delete($id);
    }
    header('Location: review.php?action=show&event_id=' . $eventId);
    exit;
}
?>
