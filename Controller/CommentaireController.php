<?php
class CommentaireController
{
    /** @var PDO Instance PDO */
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ── Lecture ────────────────────────────────────────────────────────────

    /** Retourne tous les commentaires (avec titre de l'article) */
    public function getAll(bool $onlyApproved = false): array
    {
        $sql = 'SELECT c.*, a.title AS article_title
                FROM commentaires c
                JOIN articles a ON c.article_id = a.id';
        if ($onlyApproved) {
            $sql .= " WHERE c.status = 'approved'";
        }
        $sql .= ' ORDER BY c.created_at DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Retourne les commentaires d'un article donné */
    public function getByArticle(int $articleId, bool $onlyApproved = false): array
    {
        if (!isValidId($articleId)) return [];
        $sql = 'SELECT * FROM commentaires WHERE article_id = :aid';
        if ($onlyApproved) {
            $sql .= " AND status = 'approved'";
        }
        $sql .= ' ORDER BY created_at ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':aid' => $articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Retourne le nombre total de commentaires */
    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM commentaires')->fetchColumn();
    }

    /** Retourne un commentaire par son ID */
    public function getById(int $id): ?array
    {
        if (!isValidId($id)) return null;
        $stmt = $this->pdo->prepare('SELECT * FROM commentaires WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ── Création ───────────────────────────────────────────────────────────

    /**
     * Valide et ajoute un commentaire
     *
     * @param int   $articleId ID de l'article cible
     * @param array $data      ['author' => string, 'content' => string]
     * @return array ['success' => bool, 'message' => string]
     */
    public function create(int $articleId, array $data): array
    {
        if (!isValidId($articleId)) {
            return ['success' => false, 'message' => 'Identifiant d\'article invalide.'];
        }

        $author  = trim($data['author']  ?? '');
        $content = trim($data['content'] ?? '');

        $error = $this->validateCommentaire($author, $content);
        if ($error) return ['success' => false, 'message' => $error];

        // Métier Avancé: Sentiment & Auto-Modération
        $status = 'approved';
        $sentiment = $this->analyzeSentiment($content);
        if ($sentiment === 'negative') {
            $status = 'pending'; // Auto-modération si négatif
        } elseif ($sentiment === 'rejected') {
            $status = 'rejected'; // Rejet automatique si insulte
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO commentaires (article_id, author, content, status)
                 VALUES (:aid, :author, :content, :status)'
            );
            $stmt->execute([
                ':aid'     => $articleId,
                ':author'  => $author,
                ':content' => $content,
                ':status'  => $status
            ]);
            
            if ($status === 'rejected') {
                return [
                    'success' => false, 
                    'message' => 'votre commentaire est refusé vous ne pouvez pas écrire de commentaires insultes ou contenant un vocabulaire inapproprié'
                ];
            }
            
            $msg = 'Commentaire ajouté avec succès.';
            if ($status === 'pending') {
                $msg .= ' (En attente de modération car détecté comme potentiellement négatif)';
            }
            return ['success' => true, 'message' => $msg];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    /**
     * Métier Avancé: Analyse de sentiment et Filtre Anti-Spam / Vocabulaire
     */
    public function analyzeSentiment(string $text): string
    {
        $insults = [
            // Insultes Françaises
            'putain', 'merde', 'con', 'connard', 'connasse', 'salope', 'idiot', 'débile', 'pute',
            'nique', 'niquer', 'enculé', 'encule', 'batard', 'bâtard', 'fdp', 'ntm', 'tg', 'gueule',
            'chienne', 'bouffon', 'pd', 'pédé', 'bite', 'couille', 'chatte', 'zizi', 'foutre',
            
            // Insultes Anglaises
            'fuck', 'fucker', 'fucking', 'bitch', 'asshole', 'shit', 'nigga', 'niggas', 'nigger', 'niggers',
            'cunt', 'dick', 'pussy', 'bastard', 'slut', 'whore', 'motherfucker', 'bullshit', 'ass'
        ];
        
        $spamPatterns = [
            'http', 'https', 'www\.', '\.com', '\.net', '\.org', '\.fr', 'viagra', 'casino', 'bitcoin', 'crypto', 'cliquez ici', 'gagnez'
        ];

        $negWords = ['nul', 'mauvais', 'déçu', 'horrible', 'poubelle', 'arnaque', 'haine', 'spam'];
        $posWords = ['super', 'excellent', 'merci', 'top', 'génial', 'bravo', 'adore', 'passionné'];
        
        $text = mb_strtolower($text);
        
        // Tolérance zéro pour les insultes -> rejet automatique (gère aussi le pluriel avec s?)
        foreach ($insults as $w) {
            if (preg_match('/\b' . preg_quote($w, '/') . 's?\b/iu', $text)) {
                return 'rejected';
            }
        }
        
        // Tolérance zéro pour le spam -> rejet automatique
        foreach ($spamPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/iu', $text)) {
                return 'rejected';
            }
        }
        
        foreach ($negWords as $w) {
            if (mb_strpos($text, $w) !== false) return 'negative';
        }
        foreach ($posWords as $w) {
            if (mb_strpos($text, $w) !== false) return 'positive';
        }
        return 'neutral';
    }

    /**
     * Valide et modifie un commentaire
     *
     * @param int   $id   ID du commentaire
     * @param array $data ['author' => string, 'content' => string]
     * @return array ['success' => bool, 'message' => string]
     */
    public function update(int $id, array $data): array
    {
        if (!isValidId($id)) {
            return ['success' => false, 'message' => 'Identifiant de commentaire invalide.'];
        }

        $author  = trim($data['author']  ?? '');
        $content = trim($data['content'] ?? '');
        $status  = $data['status']  ?? 'approved';

        $error = $this->validateCommentaire($author, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE commentaires SET author = :author, content = :content, status = :status WHERE id = :id'
            );
            $stmt->execute([
                ':id'      => $id,
                ':author'  => $author,
                ':content' => $content,
                ':status'  => $status
            ]);
            return ['success' => true, 'message' => 'Commentaire modifié avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    // ── Suppression ────────────────────────────────────────────────────────

    /**
     * Supprime un commentaire
     *
     * @param int $id ID du commentaire
     * @return array ['success' => bool, 'message' => string]
     */
    public function delete(int $id): array
    {
        if (!isValidId($id)) {
            return ['success' => false, 'message' => 'Identifiant de commentaire invalide.'];
        }

        try {
            $stmt = $this->pdo->prepare('DELETE FROM commentaires WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return ['success' => true, 'message' => 'Commentaire supprimé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    // ── Jointure ───────────────────────────────────────────────────────────

    /**
     * Retourne les commentaires d'un article avec les infos de l'article (INNER JOIN)
     *
     * @param int $articleId ID de l'article sélectionné
     * @return array Résultat de la jointure
     */
    public function getCommentairesByArticleJoin(int $articleId): array
    {
        if (!isValidId($articleId)) return [];
        $stmt = $this->pdo->prepare(
            'SELECT c.id        AS commentaire_id,
                    c.author,
                    c.content   AS commentaire_content,
                    c.created_at AS commentaire_date,
                    a.id        AS article_id,
                    a.title     AS article_title,
                    a.content   AS article_content
             FROM commentaires c
             INNER JOIN articles a ON c.article_id = a.id
             WHERE a.id = :aid
             ORDER BY c.created_at ASC'
        );
        $stmt->execute([':aid' => $articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne tous les commentaires avec le titre de l'article associé (INNER JOIN global)
     *
     * @return array Résultat de la jointure globale
     */
    public function getAllWithArticle(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.id        AS commentaire_id,
                    c.author,
                    c.content   AS commentaire_content,
                    c.created_at AS commentaire_date,
                    a.id        AS article_id,
                    a.title     AS article_title
             FROM commentaires c
             INNER JOIN articles a ON c.article_id = a.id
             ORDER BY c.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Validation privée ──────────────────────────────────────────────────

    /**
     * Recherche et tri des commentaires (Métier)
     */
    public function searchAndSort(string $query = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        $allowedSortColumns = ['created_at', 'author'];
        $allowedSortOrders  = ['ASC', 'DESC'];

        // Sécurisation des paramètres de tri (anti SQL injection)
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtoupper($sortOrder), $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }

        $sql = 'SELECT c.id AS id, c.author, c.content, c.status, c.created_at, a.title AS article_title
                FROM commentaires c
                LEFT JOIN articles a ON c.article_id = a.id';

        $params = [];
        if ($query !== '') {
            $sql .= ' WHERE c.author LIKE :query1 OR c.content LIKE :query2';
            $params[':query1'] = '%' . $query . '%';
            $params[':query2'] = '%' . $query . '%';
        }

        $sql .= " ORDER BY c.{$sortBy} {$sortOrder}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Valide l'auteur et le contenu d'un commentaire
     *
     * @return string|null Message d'erreur ou null si valide
     */
    private function validateCommentaire(string $author, string $content): ?string
    {
        if ($author === '') {
            return 'Votre nom est obligatoire.';
        }
        if (mb_strlen($author) < 2) {
            return 'Le nom doit contenir au moins 2 caractères.';
        }
        if (mb_strlen($author) > 100) {
            return 'Le nom ne doit pas dépasser 100 caractères.';
        }
        if ($content === '') {
            return 'Le commentaire est obligatoire.';
        }
        if (mb_strlen($content) < 5) {
            return 'Le commentaire doit contenir au moins 5 caractères.';
        }
        if (mb_strlen($content) > 1000) {
            return 'Le commentaire ne doit pas dépasser 1000 caractères.';
        }
        
        // Partie Métier : Filtre anti-spam / anti-grossièreté
        $motsInterdits = ['spam', 'insulte', 'arnaque', 'pub', 'grosmot'];
        $contentLower = mb_strtolower($content);
        foreach ($motsInterdits as $mot) {
            if (mb_strpos($contentLower, $mot) !== false) {
                return 'Votre commentaire contient un vocabulaire non autorisé.';
            }
        }

        return null;
    }

    /**
     * Statistiques : Récupère les auteurs les plus actifs
     */
    public function getTopAuthors(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT author, COUNT(*) as nb_comments 
             FROM commentaires 
             GROUP BY author 
             ORDER BY nb_comments DESC 
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Métier Avancé : Purge groupée des commentaires rejetés
     */
    public function purgeRejected(): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM commentaires WHERE status = 'rejected'");
            $stmt->execute();
            $count = $stmt->rowCount();
            return ['success' => true, 'message' => "$count commentaire(s) rejeté(s) ont été supprimés définitivement."];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la purge : ' . $e->getMessage()];
        }
    }

    /**
     * Métier : Exportation des commentaires en CSV
     */
    public function exportCSV(): void
    {
        $stmt = $this->pdo->query('SELECT * FROM commentaires ORDER BY created_at DESC');
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "commentaires_export_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Article ID', 'Auteur', 'Contenu', 'Date', 'Statut']);
        
        foreach ($commentaires as $row) {
            fputcsv($output, [
                $row['id'],
                $row['article_id'],
                $row['author'],
                $row['content'],
                $row['created_at'],
                $row['status']
            ]);
        }
        fclose($output);
        exit;
    }
}
