<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Commentaire.php';
require_once __DIR__ . '/../../Controller/CommentaireController.php';

$commentaireModel      = new Commentaire($pdo);
$commentaireController = new CommentaireController($commentaireModel);

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    // On pourrait vérifier si le commentaire existe, etc.
    $commentaireController->delete($id);
}

// Redirection vers la page de recherche avec message de succès
header('Location: searchCommentaires.php?success=deleted');
exit;
