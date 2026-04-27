<?php
/**
 * Modèle Article
 * Gère les opérations CRUD sur la table `articles`
 *
 * @package blogMVC
 * @subpackage Model
 */
class Article
{
    private PDO    $pdo;
    private ?int   $id;
    private string $title;
    private string $content;
    private string $created_at;

    public function __construct(PDO $pdo, ?int $id = null)
    {
        $this->pdo = $pdo;
        $this->id  = $id;
    }

    // ── Getters / Setters ──────────────────────────────────────────────────
    public function getId(): ?int           { return $this->id; }
    public function setId(int $v): void     { $this->id = $v; }

    public function getTitle(): string          { return $this->title; }
    public function setTitle(string $v): void   { $this->title = $v; }

    public function getContent(): string         { return $this->content; }
    public function setContent(string $v): void  { $this->content = $v; }

    public function getCreatedAt(): string { return $this->created_at; }

    // ── CRUD ───────────────────────────────────────────────────────────────

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM articles ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO articles (title, content) VALUES (:title, :content)'
        );
        return $stmt->execute([':title' => $title, ':content' => $content]);
    }

    public function update(int $id, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE articles SET title = :title, content = :content WHERE id = :id'
        );
        return $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
    }

    // ── LOGIQUE MÉTIER ─────────────────────────────────────────────────────

    /**
     * Recherche par mots-clés et Tri dynamique sécurisé
     */
    public function searchAndSort(string $query = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        $allowedSortColumns = ['id', 'title', 'created_at'];
        $allowedSortOrders  = ['ASC', 'DESC'];

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtoupper($sortOrder), $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }

        $sql = 'SELECT * FROM articles';
        $params = [];
        
        if ($query !== '') {
            $sql .= ' WHERE title LIKE :query1 OR content LIKE :query2';
            $params[':query1'] = '%' . $query . '%';
            $params[':query2'] = '%' . $query . '%';
        }

        $sql .= " ORDER BY $sortBy $sortOrder";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques : Top N des articles les plus commentés (Jointure + Group By)
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
}
