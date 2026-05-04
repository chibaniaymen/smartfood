<?php
/**
 * Mobile Preview for Articles & Comments
 * Accessed via QR Code scanning
 */
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$type = $_GET['type'] ?? 'articles';
$title = $type === 'articles' ? 'Aperçu des Articles' : 'Aperçu des Commentaires';

$items = [];
if ($type === 'articles') {
    $controller = new ArticleController($pdo);
    $items = $controller->getAll(false); // get all, including drafts
} else {
    $controller = new CommentaireController($pdo);
    // get all comments. Assuming getByArticle is not appropriate here, we need a raw query or a getAll method.
    // Let's do a raw query for simplicity and speed.
    $stmt = $pdo->query('SELECT * FROM commentaires ORDER BY created_at DESC');
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="color-scheme" content="light dark">
    <style>
        :root {
            --bg-color: #f3f4f6;
            --text-color: #111827;
            --card-bg: #ffffff;
            --header-bg: #2D6A4F;
            --muted-text: #6b7280;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #111827;
                --text-color: #f9fafb;
                --card-bg: #1f2937;
                --header-bg: #1b4332;
                --muted-text: #9ca3af;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .header {
            background-color: var(--header-bg);
            color: #ffffff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .header h1 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .container {
            padding: 15px;
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #2D6A4F;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            line-height: 1.4;
            color: var(--text-color);
        }
        .card-meta {
            font-size: 0.75rem;
            color: var(--muted-text);
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        .card-content {
            font-size: 0.85rem;
            color: var(--text-color);
            line-height: 1.5;
            opacity: 0.9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge.published { background: #d1fae5; color: #065f46; }
        .badge.draft { background: #fef3c7; color: #92400e; }
        .badge.approved { background: #d1fae5; color: #065f46; }
        .badge.pending { background: #fef3c7; color: #92400e; }
        .badge.rejected { background: #fee2e2; color: #991b1b; }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted-text);
        }
    </style>
</head>
<body>

<div class="header">
    <h1><?php echo $title; ?></h1>
</div>

<div class="container">
    <?php if (empty($items)): ?>
        <div class="empty-state">Aucune donnée trouvée.</div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="card">
                <?php if ($type === 'articles'): ?>
                    <div class="card-meta">
                        <span>ID: <?php echo $item['id']; ?></span>
                        <span class="badge <?php echo htmlspecialchars($item['status']); ?>">
                            <?php echo strtoupper($item['status']); ?>
                        </span>
                    </div>
                    <h2 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                    <div class="card-content">
                        <?php echo htmlspecialchars(mb_substr($item['content'], 0, 100)) . '...'; ?>
                    </div>
                <?php else: ?>
                    <div class="card-meta">
                        <span>Article ID: <?php echo $item['article_id']; ?> | Par: <?php echo htmlspecialchars($item['author']); ?></span>
                        <span class="badge <?php echo htmlspecialchars($item['status']); ?>">
                            <?php echo strtoupper($item['status']); ?>
                        </span>
                    </div>
                    <div class="card-content" style="font-weight: 600; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($item['content']); ?>
                    </div>
                    <div class="card-meta" style="margin-bottom:0; font-size:0.7rem;">
                        <?php echo $item['created_at']; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
