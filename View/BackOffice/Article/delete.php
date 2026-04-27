<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Article.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';

$articleModel      = new Article($pdo);
$articleController = new ArticleController($articleModel);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    header('Location: list.php'); exit;
}

$id     = (int)$_GET['id'];
$result = $articleController->delete($id);

// Redirection avec message
header('Location: list.php?success=deleted');
exit;
