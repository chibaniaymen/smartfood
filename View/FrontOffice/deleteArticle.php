<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Article.php';
require_once __DIR__ . '/../../Controller/ArticleController.php';

$articleModel      = new Article($pdo);
$articleController = new ArticleController($articleModel);

$id = $_GET['id'] ?? null;
if ($id && isValidId($id)) {
    $result = $articleController->delete((int)$id);
    if ($result['success']) {
        header('Location: index.php?success=article_deleted');
        exit;
    }
}

header('Location: index.php');
exit;
