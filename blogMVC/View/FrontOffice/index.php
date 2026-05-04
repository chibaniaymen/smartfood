<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Blog.php';
require_once __DIR__ . '/../../Controller/BlogController.php';

$blog = new Blog($pdo);
$controller = new BlogController($blog);
$articles = $controller->getAllArticles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog SmartFood - FrontOffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .blog-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .blog-header {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .blog-header h1 {
            color: #333;
            margin: 0;
            font-size: 2.5rem;
        }
        .blog-header p {
            color: #666;
            margin: 10px 0 0 0;
        }
        .article-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .article-card h2 {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .article-meta {
            color: #999;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .article-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .empty-state {
            background: white;
            border-radius: 12px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .empty-state p {
            color: #999;
            font-size: 1.1rem;
            margin: 0;
        }
        .articles-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .articles-table table {
            margin: 0;
        }
        .articles-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .articles-table td {
            vertical-align: middle;
            padding: 15px;
        }
        .articles-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .article-title-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .article-title-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="blog-container">
        <div class="blog-header">
            <h1>📚 Blog SmartFood</h1>
            <p>Découvrez nos derniers articles et actualités</p>
        </div>

        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <p>Aucun article pour le moment. Revenez bientôt!</p>
            </div>
        <?php else: ?>
            <div class="articles-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 40%;">Titre</th>
                            <th style="width: 35%;">Contenu</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>#<?php echo h($article['id']); ?></td>
                                <td>
                                    <a href="article.php?id=<?php echo $article['id']; ?>" class="article-title-link">
                                        <?php echo h($article['title']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo nl2br(h(substr($article['content'], 0, 100))); ?>...
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">Lire</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
