<?php

/**
 * Modèle Commentaire
 * Gère les opérations CRUD sur la table `commentaires`
 *
 * @package blogMVC
 * @subpackage Model
 */
class Commentaire
{
    /** @var PDO Instance PDO */
    private PDO $pdo;

    /** @var int|null Identifiant du commentaire */
    private ?int $id;

    /** @var int Identifiant de l'article associé */
    private int $article_id;

    /** @var string Auteur du commentaire */
    private string $author;

    /** @var string Contenu du commentaire */
    private string $content;

    /** @var string Date de création */
    private string $created_at;

    public function __construct(PDO $pdo, ?int $id = null)
    {
        $this->pdo = $pdo;
        $this->id  = $id;
    }

    // ── Getters / Setters ──────────────────────────────────────────────────

    public function getId(): ?int         { return $this->id; }
    public function setId(int $v): void   { $this->id = $v; }

    public function getArticleId(): int          { return $this->article_id; }
    public function setArticleId(int $v): void   { $this->article_id = $v; }

    public function getAuthor(): string          { return $this->author; }
    public function setAuthor(string $v): void   { $this->author = $v; }

    public function getContent(): string         { return $this->content; }
    public function setContent(string $v): void  { $this->content = $v; }

    public function getCreatedAt(): string { return $this->created_at; }

    // ── CRUD ───────────────────────────────────────────────────────────────

    /**
     * Récupère tous les commentaires avec le titre de l'article associé
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.*, a.title AS article_title
             FROM commentaires c
             JOIN articles a ON c.article_id = a.id
             ORDER BY c.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les commentaires d'un article donné
     */
    public function findByArticle(int $articleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM commentaires WHERE article_id = :aid ORDER BY created_at ASC'
        );
        $stmt->execute([':aid' => $articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un commentaire par son identifiant
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM commentaires WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Insère un nouveau commentaire
     */
    public function create(int $articleId, string $author, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO commentaires (article_id, author, content)
             VALUES (:aid, :author, :content)'
        );
        return $stmt->execute([
            ':aid'     => $articleId,
            ':author'  => $author,
            ':content' => $content,
        ]);
    }

    /**
     * Modifie un commentaire existant
     */
    public function update(int $id, string $author, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE commentaires SET author = :author, content = :content WHERE id = :id'
        );
        return $stmt->execute([
            ':id'      => $id,
            ':author'  => $author,
            ':content' => $content,
        ]);
    }

    /**
     * Supprime un commentaire
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM commentaires WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retourne le nombre total de commentaires
     */
    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM commentaires')->fetchColumn();
    }

    // ── JOINTURE ───────────────────────────────────────────────────────────

    /**
     * Récupère tous les commentaires d'un article via INNER JOIN
     * Jointure entre `commentaires` et `articles` sur article_id = articles.id
     *
     * @param int $articleId ID de l'article
     * @return array Liste des commentaires avec les infos de l'article
     */
    public function findCommentairesByArticleJoin(int $articleId): array
    {
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
     * Récupère tous les commentaires avec les infos de leurs articles (INNER JOIN global)
     *
     * @return array Liste complète commentaires + article associé
     */
    public function findAllWithArticle(): array
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

    /**
     * Recherche et tri des commentaires avec jointure (Partie Métier)
     *
     * @param string $query Mot-clé pour chercher dans author ou content
     * @param string $sortBy Colonne de tri ('created_at' ou 'author')
     * @param string $sortOrder Ordre de tri ('ASC' ou 'DESC')
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

        $sql = 'SELECT c.id AS id, c.author, c.content, c.created_at, a.title AS article_title
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
     * Statistiques : Top N des auteurs les plus actifs (Group By)
     *
     * @param int $limit Nombre d'auteurs à retourner
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
}
