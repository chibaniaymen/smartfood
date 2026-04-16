<?php

/**
 * Contrôleur Principal du Blog
 * Gère la logique métier pour le blog
 * 
 * @package blogMVC
 * @subpackage Controller
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Blog.php';

class BlogController
{
    /**
     * @var Blog Instance du modèle Blog
     */
    private $blog;

    /**
     * Constructeur du contrôleur
     * 
     * @param Blog $blog Instance du modèle Blog
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * Récupère tous les articles
     * 
     * @return array Tableau des articles
     */
    public function getAllArticles(): array
    {
        return $this->blog->getAllArticles();
    }

    /**
     * Récupère un article par son ID
     * 
     * @param int $articleId ID de l'article
     * @return array|null Article ou null
     */
    public function getArticleById(int $articleId): ?array
    {
        if (!isValidId($articleId)) {
            return null;
        }
        return $this->blog->getArticleById($articleId);
    }

    /**
     * Crée un nouvel article
     * 
     * @param array $data Données de l'article ['title', 'content']
     * @return array Résultat ['success' => bool, 'message' => string]
     */
    public function createArticle(array $data): array
    {
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');

        if (empty($title)) {
            return ['success' => false, 'message' => 'Le titre est obligatoire.'];
        }
        if (empty($content)) {
            return ['success' => false, 'message' => 'Le contenu est obligatoire.'];
        }

        try {
            $this->blog->addArticle($title, $content);
            return ['success' => true, 'message' => 'Article créé avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Modifie un article
     * 
     * @param int $articleId ID de l'article
     * @param array $data Données de l'article ['title', 'content']
     * @return array Résultat ['success' => bool, 'message' => string]
     */
    public function updateArticle(int $articleId, array $data): array
    {
        if (!isValidId($articleId)) {
            return ['success' => false, 'message' => 'ID d\'article invalide.'];
        }

        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');

        if (empty($title)) {
            return ['success' => false, 'message' => 'Le titre est obligatoire.'];
        }
        if (empty($content)) {
            return ['success' => false, 'message' => 'Le contenu est obligatoire.'];
        }

        try {
            $this->blog->updateArticle($articleId, $title, $content);
            return ['success' => true, 'message' => 'Article modifié avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Supprime un article
     * 
     * @param int $articleId ID de l'article
     * @return array Résultat ['success' => bool, 'message' => string]
     */
    public function deleteArticle(int $articleId): array
    {
        if (!isValidId($articleId)) {
            return ['success' => false, 'message' => 'ID d\'article invalide.'];
        }

        try {
            $this->blog->deleteArticle($articleId);
            return ['success' => true, 'message' => 'Article supprimé avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Récupère tous les commentaires
     * 
     * @return array Tableau des commentaires
     */
    public function getAllComments(): array
    {
        return $this->blog->getAllComments();
    }

    /**
     * Récupère les commentaires d'un article
     * 
     * @param int $articleId ID de l'article
     * @return array Tableau des commentaires
     */
    public function getCommentsByArticle(int $articleId): array
    {
        if (!isValidId($articleId)) {
            return [];
        }
        return $this->blog->getCommentsByArticle($articleId);
    }

    /**
     * Ajoute un commentaire
     * 
     * @param int $articleId ID de l'article
     * @param array $data Données du commentaire ['author', 'content']
     * @return array Résultat ['success' => bool, 'message' => string]
     */
    public function addComment(int $articleId, array $data): array
    {
        if (!isValidId($articleId)) {
            return ['success' => false, 'message' => 'ID d\'article invalide.'];
        }

        $author = trim($data['author'] ?? '');
        $content = trim($data['content'] ?? '');

        if (empty($author)) {
            return ['success' => false, 'message' => 'Le nom est obligatoire.'];
        }
        if (empty($content)) {
            return ['success' => false, 'message' => 'Le commentaire est obligatoire.'];
        }

        try {
            $this->blog->addComment($articleId, $author, $content);
            return ['success' => true, 'message' => 'Commentaire ajouté avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Supprime un commentaire
     * 
     * @param int $commentId ID du commentaire
     * @return array Résultat ['success' => bool, 'message' => string]
     */
    public function deleteComment(int $commentId): array
    {
        if (!isValidId($commentId)) {
            return ['success' => false, 'message' => 'ID de commentaire invalide.'];
        }

        try {
            $this->blog->deleteComment($commentId);
            return ['success' => true, 'message' => 'Commentaire supprimé avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Récupère les statistiques du blog
     * 
     * @return array Statistiques ['articles_count' => int, 'comments_count' => int]
     */
    public function getStats(): array
    {
        return [
            'articles_count' => $this->blog->getArticleCount(),
            'comments_count' => $this->blog->getCommentCount(),
        ];
    }
}
