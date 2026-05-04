<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../Controller/CommentaireController.php';


$commentaireController = new CommentaireController($pdo);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    header('Location: list.php'); exit;
}

$commentaireController->delete((int)$_GET['id']);
header('Location: list.php?success=deleted');
exit;

