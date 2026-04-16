<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Blog.php';
require_once __DIR__ . '/../../Controller/BlogController.php';

$blog = new Blog($pdo);
$controller = new BlogController($blog);

$id = isValidId($_GET['id'] ?? null) ? (int)$_GET['id'] : 0;
$article = $controller->getArticleById($id);

if (!$article) {
    redirect('index.php');
}

$commentError = '';
$comments = $controller->getCommentsByArticle($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $result = $controller->addComment($id, $_POST);
    if (!$result['success']) {
        $commentError = $result['message'];
    } else {
        redirect('article.php?id=' . $id);
    }
}

if (isset($_GET['delete_comment']) && isValidId($_GET['delete_comment'])) {
    $controller->deleteComment((int)$_GET['delete_comment']);
    redirect('article.php?id=' . $id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($article['title']); ?> - Blog SmartFood</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .container-custom {
            max-width: 900px;
            margin: 0 auto;
        }
        .article-full {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .article-full h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .article-meta {
            color: #999;
            font-size: 0.95rem;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .article-content {
            color: #555;
            line-height: 1.8;
            font-size: 1.05rem;
            margin-bottom: 30px;
        }
        .comments-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .comments-section h3 {
            color: #333;
            margin-bottom: 20px;
        }
        .comment-item {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .comment-author {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .comment-date {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 10px;
        }
        .comment-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .alert-danger {
            border-radius: 8px;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="article-full">
            <h1><?php echo h($article['title']); ?></h1>
            <div class="article-meta">
                Publié le <?php echo date('d/m/Y à H:i', strtotime($article['created_at'])); ?>
            </div>
            <div class="article-content">
                <?php echo nl2br(h($article['content'])); ?>
            </div>
            <a href="index.php" class="btn btn-secondary btn-back">← Retour aux articles</a>
        </div>

        <div class="comments-section">
            <h3>💬 Commentaires (<?php echo count($comments); ?>)</h3>

            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-author"><?php echo h($comment['author']); ?></div>
                        <div class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></div>
                        <p><?php echo nl2br(h($comment['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #999;">Aucun commentaire pour le moment.</p>
            <?php endif; ?>

            <div class="comment-form">
                <h4>Ajouter un commentaire</h4>
                <?php if ($commentError): ?>
                    <div class="alert alert-danger"><?php echo h($commentError); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="author" class="form-label">Votre nom</label>
                        <input type="text" class="form-control" id="author" name="author" placeholder="Entrez votre nom">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Votre commentaire</label>
                        <textarea class="form-control" id="content" name="content" rows="4" placeholder="Écrivez votre commentaire..."></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary">Publier le commentaire</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
