<?php
require_once __DIR__ . '/../../config.php';

require_once __DIR__ . '/../../controller/CommentaireController.php';


$commentaireController = new CommentaireController($pdo);

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    // On pourrait vérifier si le commentaire existe, etc.
    $commentaireController->delete($id);
}

// Redirection vers la page de recherche avec message de succès
header('Location: searchCommentaires.php?success=deleted');
exit;

