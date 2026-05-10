<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/ArticleController.php';
require_once __DIR__ . '/../../controller/CommentaireController.php';

// Inclusion manuelle de Dompdf
require_once __DIR__ . '/../../lib/dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    redirect('index.php');
}

$id = (int)$_GET['id'];
$articleController = new ArticleController($pdo);
$commentController = new CommentaireController($pdo);

$article = $articleController->getById($id);
if (!$article) redirect('index.php');

$comments = $commentController->getByArticle($id, true);

// Configuration Dompdf
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

// Génération du contenu via le contrôleur
$html = $articleController->generatePdfHtml($article, $comments);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Sortie du fichier : "false" pour un rendu INLINE (affichage direct sur iOS/Android)
$filename = 'SmartFood_Article_' . $id . '.pdf';
$dompdf->stream($filename, ["Attachment" => false]);
exit;
