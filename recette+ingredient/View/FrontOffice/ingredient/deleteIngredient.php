<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/IngredientController.php';

$ctrl = new IngredientController();
$recette_id = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $recette_id = isset($_GET['recette_id']) ? (int)$_GET['recette_id'] : null;
    $ctrl->deleteIngredient($id);
}
// preserve search/sort params when redirecting
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? '';
if ($recette_id) {
    $redir = 'index.php?recette_id=' . $recette_id;
    if ($q !== '') $redir .= '&q=' . urlencode($q);
    if ($sort !== '') $redir .= '&sort=' . urlencode($sort);
    header('Location: ' . $redir);
} else {
    header('Location: ../recette/listRecettes.php');
}
exit;
