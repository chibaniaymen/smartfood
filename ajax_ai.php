<?php
require_once 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['prompt'])) {
    $result = callAI($input['prompt']);
    echo json_encode(['response' => $result]);
} else {
    echo json_encode(['error' => 'No prompt provided']);
}
?>
