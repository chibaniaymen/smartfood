<?php
require_once __DIR__ . '/../Model/Article.php';

class ArticleController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(bool $onlyPublished = false): array
    {
        $sql = 'SELECT * FROM articles';
        if ($onlyPublished) {
            $sql .= " WHERE status = 'published'";
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
    }

    public function searchAndSort(string $query = '', string $sortBy = 'created_at', string $sortOrder = 'DESC', bool $onlyPublished = false): array
    {
        $allowedSortColumns = ['id', 'title', 'created_at'];
        $allowedSortOrders  = ['ASC', 'DESC'];

        $sortBy    = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtoupper($sortOrder), $allowedSortOrders) ? strtoupper($sortOrder) : 'DESC';

        $sql = 'SELECT * FROM articles';
        $conditions = [];
        $params = [];
        
        if ($onlyPublished) {
            $conditions[] = "status = 'published'";
        }

        if ($query !== '') {
            $conditions[] = '(LOWER(title) LIKE LOWER(:query1) OR LOWER(content) LIKE LOWER(:query2) OR LOWER(tags) LIKE LOWER(:query3))';
            $params[':query1'] = '%' . $query . '%';
            $params[':query2'] = '%' . $query . '%';
            $params[':query3'] = '%' . $query . '%';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($query !== '') {
            // Relevancy: prioritize articles starting with the query
            $sql .= " ORDER BY (LOWER(title) LIKE LOWER(:startQuery)) DESC, $sortBy $sortOrder";
            $params[':startQuery'] = $query . '%';
        } else {
            $sql .= " ORDER BY $sortBy $sortOrder";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        if (!isValidId($id)) return null;
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): array
    {
        $title       = trim($data['title']        ?? '');
        $content     = trim($data['content']      ?? '');
        $status      = $data['status']            ?? 'published';
        $tags        = trim($data['tags']         ?? '');
        $publishedAt = $data['published_at']      ?? null;

        $error = $this->validateArticle($title, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO articles (title, content, status, tags, published_at) VALUES (:title, :content, :status, :tags, :publishedAt)'
            );
            $stmt->execute([
                ':title'       => $title,
                ':content'     => $content,
                ':status'      => $status,
                ':tags'        => $tags,
                ':publishedAt' => $publishedAt
            ]);
            return ['success' => true, 'message' => 'Article créé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    public function update(int $id, array $data): array
    {
        if (!isValidId($id)) return ['success' => false, 'message' => 'ID invalide.'];

        $title   = trim($data['title']   ?? '');
        $content = trim($data['content'] ?? '');
        $status  = $data['status']  ?? 'published';
        $tags    = trim($data['tags']    ?? '');

        $error = $this->validateArticle($title, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE articles SET title = :title, content = :content, status = :status, tags = :tags WHERE id = :id'
            );
            $stmt->execute([
                ':title'   => $title, 
                ':content' => $content, 
                ':status'  => $status, 
                ':tags'    => $tags, 
                ':id'      => $id
            ]);
            return ['success' => true, 'message' => 'Article modifié avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        if (!isValidId($id)) return ['success' => false, 'message' => 'ID invalide.'];
        try {
            $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return ['success' => true, 'message' => 'Article supprimé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    /**
     * Métier Avancé : Calcul du temps de lecture estimé
     */
    public function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return (int)max(1, $minutes);
    }

    /**
     * Métier Avancé : Algorithme d'engagement (Trending)
     * Score = (Nombre de coms * 2) + (Bonus Sentiment Positif)
     */
    public function getTrendingArticles(int $limit = 3): array
    {
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM commentaires c WHERE c.article_id = a.id AND c.status = 'approved') as comment_count,
                (SELECT COUNT(*) FROM commentaires c WHERE c.article_id = a.id AND c.status = 'approved' AND (LOWER(c.content) LIKE '%super%' OR LOWER(c.content) LIKE '%top%' OR LOWER(c.content) LIKE '%excellent%')) as positive_bonus
                FROM articles a
                WHERE a.status = 'published'
                ORDER BY (comment_count * 2 + positive_bonus) DESC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function validateArticle(string $title, string $content): ?string
    {
        if ($title === '')            return 'Le titre est obligatoire.';
        if (mb_strlen($title) < 3)   return 'Le titre doit contenir au moins 3 caractères.';
        if (mb_strlen($title) > 255) return 'Le titre ne doit pas dépasser 255 caractères.';
        if ($content === '')          return 'Le contenu est obligatoire.';
        if (mb_strlen($content) < 10) return 'Le contenu doit contenir au moins 10 caractères.';
        return null;
    }

    /**
     * Métier Avancé : Génère le HTML structuré pour l'export PDF
     */
    public function generatePdfHtml(array $article, array $comments): string
    {
        ob_start();
        ?>
        <style>
            body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
            .header { text-align: center; border-bottom: 2px solid #2D6A4F; padding-bottom: 20px; margin-bottom: 30px; }
            .title { color: #2D6A4F; font-size: 24px; margin-bottom: 5px; }
            .meta { color: #666; font-size: 12px; font-style: italic; }
            .content { margin-bottom: 40px; text-align: justify; }
            .comments-section { background: #f9f9f9; padding: 20px; border-radius: 10px; }
            .comment-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
            .comment-author { font-weight: bold; color: #E76F51; }
        </style>
        <div class="header">
            <h1 class="title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="meta">Publié le <?php echo date('d/m/Y', strtotime($article['created_at'])); ?> | SmartFood Blog</div>
        </div>
        <div class="content">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>
        <div class="comments-section">
            <h3>Commentaires (<?php echo count($comments); ?>)</h3>
            <?php if (empty($comments)): ?>
                <p>Aucun commentaire.</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                    <div class="comment-item">
                        <span class="comment-author"><?php echo htmlspecialchars($c['author']); ?></span>
                        <span style="font-size: 10px; color: #999;"> - <?php echo date('d/m/Y', strtotime($c['created_at'])); ?></span>
                        <p style="margin-top: 5px;"><?php echo htmlspecialchars($c['content']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Statistiques : Récupère le Top N des articles les plus commentés
     */
    public function getTopCommented(int $limit = 5): array
    {
        $sql = 'SELECT a.id, a.title, COUNT(c.id) AS comment_count
                FROM articles a
                LEFT JOIN commentaires c ON a.id = c.article_id
                GROUP BY a.id, a.title
                ORDER BY comment_count DESC
                LIMIT :limit';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques : Récupère le Top N des auteurs les plus actifs
     */
    public function getTopAuthors(int $limit = 5): array
    {
        $sql = 'SELECT author, COUNT(id) AS nb_comments
                FROM commentaires
                GROUP BY author
                ORDER BY nb_comments DESC
                LIMIT :limit';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Métier : Exportation des articles en CSV
     */
    public function exportCSV(): void
    {
        $stmt = $this->pdo->query('SELECT * FROM articles ORDER BY created_at DESC');
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "articles_export_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Titre', 'Contenu', 'Date', 'Statut', 'Tags']);
        
        foreach ($articles as $row) {
            fputcsv($output, [
                $row['id'],
                $row['title'],
                $row['content'],
                $row['created_at'],
                $row['status'],
                $row['tags']
            ]);
        }
        fclose($output);
        exit;
    }

    /**
     * Retourne les recommandations d'articles basées sur l'IA hybride.
     */
    public function recommendations(int $articleId): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;

        $articleModel = new Article($this->pdo);
        return $articleModel->getRecommendedArticles($articleId, $userId, $sessionId);
    }

    /**
     * Enregistre la visite d'un utilisateur sur un article.
     */
    public function trackView(int $articleId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;

        $articleModel = new Article($this->pdo);
        $articleModel->trackView($userId, $sessionId, $articleId);
        $articleModel->updateUserProfile($sessionId, $articleId, $userId);
    }

    /**
     * Génère le rapport "AI Insights Dashboard"
     */
    public function generateAiSnapshotReport(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;

        $articleModel = new Article($this->pdo);
        return $articleModel->getAiSnapshotReport($userId, $sessionId);
    }
}
