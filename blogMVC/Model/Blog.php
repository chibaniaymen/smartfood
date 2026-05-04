<?php

/**
 * Classe Blog
 * Représente l'entité Blog avec gestion des articles et commentaires
 * 
 * @package blogMVC
 * @subpackage Model
 */
class Blog
{
    /**
     * @var int Identifiant du blog
     */
    private $id;

    /**
     * @var string Titre du blog
     */
    private $title;

    /**
     * @var string Description du blog
     */
    private $description;

    /**
     * @var PDO Instance de connexion à la base de données
     */
    private $pdo;

    /**
     * Constructeur de la classe Blog
     * 
     * @param PDO $pdo Instance de connexion PDO
     * @param int $id Identifiant du blog (optionnel)
     */
    public function __construct(PDO $pdo, ?int $id = null)
    {
        $this->pdo = $pdo;
        $this->id = $id;
    }

    /**
     * Retourne l'ID du blog
     * 
     * @return int|null ID du blog
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Définit l'ID du blog
     * 
     * @param int $id ID du blog
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retourne le titre du blog
     * 
     * @return string Titre du blog
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Définit le titre du blog
     * 
     * @param string $title Titre du blog
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Retourne la description du blog
     * 
     * @return string Description du blog
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Définit la description du blog
     * 
     * @param string $description Description du blog
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Récupère tous les articles du blog
     * 
     * @return array Tableau des articles
     */
    public function getAllArticles(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM articles ORDER BY created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des articles: ' . $e->getMessage());
        }
    }

    /**
     * Récupère un article par son ID
     * 
     * @param int $articleId ID de l'article
     * @return array|null Article trouvé ou null
     */
    public function getArticleById(int $articleId): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = ?');
            $stmt->execute([$articleId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute un nouvel article
     * 
     * @param string $title Titre de l'article
     * @param string $content Contenu de l'article
     * @return bool True si succès, false sinon
     */
    public function addArticle(string $title, string $content): bool
    {
        if (empty($title) || empty($content)) {
            throw new Exception('Le titre et le contenu sont obligatoires');
        }

        try {
            $stmt = $this->pdo->prepare('INSERT INTO articles (title, content) VALUES (?, ?)');
            return $stmt->execute([$title, $content]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de l\'ajout de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Modifie un article existant
     * 
     * @param int $articleId ID de l'article
     * @param string $title Nouveau titre
     * @param string $content Nouveau contenu
     * @return bool True si succès, false sinon
     */
    public function updateArticle(int $articleId, string $title, string $content): bool
    {
        if (empty($title) || empty($content)) {
            throw new Exception('Le titre et le contenu sont obligatoires');
        }

        try {
            $stmt = $this->pdo->prepare('UPDATE articles SET title = ?, content = ? WHERE id = ?');
            return $stmt->execute([$title, $content, $articleId]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la modification de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un article
     * 
     * @param int $articleId ID de l'article à supprimer
     * @return bool True si succès, false sinon
     */
    public function deleteArticle(int $articleId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = ?');
            return $stmt->execute([$articleId]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la suppression de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Récupère tous les commentaires
     * 
     * @return array Tableau des commentaires
     */
    public function getAllComments(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT c.*, a.title as article_title FROM commentaires c JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des commentaires: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les commentaires d'un article
     * 
     * @param int $articleId ID de l'article
     * @return array Tableau des commentaires
     */
    public function getCommentsByArticle(int $articleId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM commentaires WHERE article_id = ? ORDER BY created_at DESC');
            $stmt->execute([$articleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des commentaires: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute un commentaire
     * 
     * @param int $articleId ID de l'article
     * @param string $author Auteur du commentaire
     * @param string $content Contenu du commentaire
     * @return bool True si succès, false sinon
     */
    public function addComment(int $articleId, string $author, string $content): bool
    {
        if (empty($author) || empty($content)) {
            throw new Exception('Le nom et le commentaire sont obligatoires');
        }

        try {
            $stmt = $this->pdo->prepare('INSERT INTO commentaires (article_id, author, content) VALUES (?, ?, ?)');
            return $stmt->execute([$articleId, $author, $content]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un commentaire
     * 
     * @param int $commentId ID du commentaire à supprimer
     * @return bool True si succès, false sinon
     */
    public function deleteComment(int $commentId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM commentaires WHERE id = ?');
            return $stmt->execute([$commentId]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la suppression du commentaire: ' . $e->getMessage());
        }
    }

    /**
     * Récupère le nombre total d'articles
     * 
     * @return int Nombre d'articles
     */
    public function getArticleCount(): int
    {
        try {
            return (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors du comptage des articles: ' . $e->getMessage());
        }
    }

    /**
     * Récupère le nombre total de commentaires
     * 
     * @return int Nombre de commentaires
     */
    public function getCommentCount(): int
    {
        try {
            return (int) $this->pdo->query('SELECT COUNT(*) FROM commentaires')->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors du comptage des commentaires: ' . $e->getMessage());
        }
    }
}
