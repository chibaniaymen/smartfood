<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Blog.php';
require_once __DIR__ . '/../../Controller/BlogController.php';

$blog = new Blog($pdo);
$controller = new BlogController($blog);

$action = $_GET['action'] ?? 'dashboard';
$error = '';
$success = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_article'])) {
        $result = $controller->createArticle($_POST);
        if ($result['success']) {
            $success = $result['message'];
            $_POST = [];
        } else {
            $error = $result['message'];
        }
    } elseif (isset($_POST['update_article'])) {
        if (isValidId($_POST['article_id'] ?? null)) {
            $result = $controller->updateArticle((int)$_POST['article_id'], $_POST);
            if ($result['success']) {
                redirect('admin.php?action=articles&success=1');
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Traitement des actions GET
if ($action === 'delete_article' && isValidId($_GET['id'] ?? null)) {
    $controller->deleteArticle((int)$_GET['id']);
    redirect('admin.php?action=articles');
}

if ($action === 'delete_comment' && isValidId($_GET['id'] ?? null)) {
    $controller->deleteComment((int)$_GET['id']);
    redirect('admin.php?action=comments');
}

$stats = $controller->getStats();
$articles = $controller->getAllArticles();
$comments = $controller->getAllComments();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog SmartFood - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding: 20px 0;
            color: white;
        }
        .sidebar h2 {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid white;
            padding-left: 16px;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card .number {
            font-size: 2.5rem;
            color: #667eea;
            font-weight: bold;
        }
        .stat-card .label {
            color: #999;
            margin-top: 10px;
        }
        .content-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        table {
            font-size: 0.95rem;
        }
        .btn-sm {
            padding: 5px 12px;
            font-size: 0.85rem;
        }
        .alert {
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-blog"></i> Admin</h2>
        <a href="admin.php" class="<?php echo $action === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i> Tableau de bord
        </a>
        <a href="admin.php?action=articles" class="<?php echo $action === 'articles' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> Articles
        </a>
        <a href="admin.php?action=add" class="<?php echo $action === 'add' ? 'active' : ''; ?>">
            <i class="fas fa-plus"></i> Nouvel article
        </a>
        <a href="admin.php?action=comments" class="<?php echo $action === 'comments' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Commentaires
        </a>
        <hr style="border-color: rgba(255,255,255,0.2);">
        <a href="../FrontOffice/index.php" target="_blank">
            <i class="fas fa-eye"></i> Voir le site
        </a>
    </div>

    <div class="main-content">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
        <?php endif; ?>

        <!-- Tableau de bord -->
        <?php if ($action === 'dashboard'): ?>
            <div class="header">
                <h1>📊 Tableau de bord</h1>
            </div>
            <div class="stats">
                <div class="stat-card">
                    <div class="number"><?php echo $stats['articles_count']; ?></div>
                    <div class="label">Articles publiés</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $stats['comments_count']; ?></div>
                    <div class="label">Commentaires</div>
                </div>
            </div>

        <!-- Gestion des articles -->
        <?php elseif ($action === 'articles'): ?>
            <div class="header">
                <h1>📰 Gestion des articles</h1>
            </div>
            <div class="content-card">
                <a href="admin.php?action=add" class="btn btn-primary mb-3">+ Nouvel article</a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo $article['id']; ?></td>
                                <td><?php echo h(substr($article['title'], 0, 50)); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($article['created_at'])); ?></td>
                                <td>
                                    <a href="admin.php?action=edit&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="admin.php?action=delete_article&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- Ajouter/modifier un article -->
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <?php
            $editArticle = null;
            if ($action === 'edit' && isValidId($_GET['id'] ?? null)) {
                $editArticle = $controller->getArticleById((int)$_GET['id']);
            }
            ?>
            <div class="header">
                <h1><?php echo $action === 'add' ? '✍️ Nouvel article' : '✏️ Modifier l\'article'; ?></h1>
            </div>
            <div class="content-card">
                <form method="POST">
                    <?php if ($action === 'edit' && $editArticle): ?>
                        <input type="hidden" name="article_id" value="<?php echo $editArticle['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Entrez le titre de l'article" value="<?php echo $editArticle ? h($editArticle['title']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="content" class="form-label">Contenu</label>
                        <textarea class="form-control" id="content" name="content" rows="10" placeholder="Écrivez le contenu de l'article..."><?php echo $editArticle ? h($editArticle['content']) : ''; ?></textarea>
                    </div>
                    <button type="submit" name="<?php echo $action === 'add' ? 'add_article' : 'update_article'; ?>" class="btn btn-primary">
                        <?php echo $action === 'add' ? 'Créer l\'article' : 'Mettre à jour'; ?>
                    </button>
                    <a href="admin.php?action=articles" class="btn btn-secondary">Annuler</a>
                </form>
            </div>

        <!-- Gestion des commentaires -->
        <?php elseif ($action === 'comments'): ?>
            <div class="header">
                <h1>💬 Gestion des commentaires</h1>
            </div>
            <div class="content-card">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Auteur</th>
                            <th>Article</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo h($comment['author']); ?></td>
                                <td><?php echo h(substr($comment['article_title'], 0, 30)); ?></td>
                                <td><?php echo h(substr($comment['content'], 0, 50)); ?>...</td>
                                <td><?php echo date('d/m/Y', strtotime($comment['created_at'])); ?></td>
                                <td>
                                    <a href="admin.php?action=delete_comment&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression?')">Supprimer</a>
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
