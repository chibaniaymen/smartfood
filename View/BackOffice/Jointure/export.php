<?php
/**
 * Export CSV — Partie Métier
 * Exporte le résultat de la jointure sous forme de fichier CSV
 */
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Commentaire.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$commentaireModel      = new Commentaire($pdo);
$commentaireController = new CommentaireController($commentaireModel);

$idArticle = isset($_GET['article']) && isValidId($_GET['article']) ? (int)$_GET['article'] : null;

if ($idArticle) {
    $list = $commentaireController->getCommentairesByArticleJoin($idArticle);
    $filename = "export_commentaires_article_{$idArticle}_" . date('Y-m-d_H-i') . ".csv";
} else {
    $list = $commentaireController->getAllWithArticle();
    $filename = "export_tous_commentaires_" . date('Y-m-d_H-i') . ".csv";
}

// En-têtes HTTP pour forcer le téléchargement du fichier CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
// Ajouter le BOM UTF-8 pour que le fichier s'ouvre correctement dans Excel
fputs($output, (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Ligne d'en-tête du CSV (séparateur point-virgule pour Excel français)
fputcsv($output, [
    'ID Commentaire',
    'Auteur',
    'Commentaire',
    'Date du Commentaire',
    'ID Article',
    'Titre de l\'Article'
], ';');

// Ajout des données
if (!empty($list)) {
    foreach ($list as $row) {
        fputcsv($output, [
            $row['commentaire_id'],
            $row['author'],
            $row['commentaire_content'],
            $row['commentaire_date'],
            $row['article_id'],
            $row['article_title']
        ], ';');
    }
}

fclose($output);
exit;
