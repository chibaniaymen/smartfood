<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/NewsletterController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $controller = new NewsletterController($pdo);
    $result = $controller->subscribe($email, $recaptchaResponse);

    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
