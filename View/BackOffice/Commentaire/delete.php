<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Commentaire.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$commentaireModel      = new Commentaire($pdo);
$commentaireController = new CommentaireController($commentaireModel);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    header('Location: list.php'); exit;
}

$commentaireController->delete((int)$_GET['id']);
header('Location: list.php?success=deleted');
exit;
