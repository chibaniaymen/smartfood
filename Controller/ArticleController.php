<?php
/**
 * Contrôleur Article
 *
 * @package blogMVC
 * @subpackage Controller
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Article.php';

class ArticleController
{
    private Article $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getAll(): array  { return $this->article->findAll(); }
    public function count(): int     { return $this->article->count(); }

    public function searchAndSort(string $query = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        return $this->article->searchAndSort($query, $sortBy, $sortOrder);
    }

    public function getById(int $id): ?array
    {
        if (!isValidId($id)) return null;
        return $this->article->findById($id);
    }

    public function create(array $data): array
    {
        $title   = trim($data['title']   ?? '');
        $content = trim($data['content'] ?? '');

        $error = $this->validateArticle($title, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $this->article->create($title, $content);
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

        $error = $this->validateArticle($title, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $this->article->update($id, $title, $content);
            return ['success' => true, 'message' => 'Article modifié avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        if (!isValidId($id)) return ['success' => false, 'message' => 'ID invalide.'];
        try {
            $this->article->delete($id);
            return ['success' => true, 'message' => 'Article supprimé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
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
     * Statistiques : Récupère le Top N des articles les plus commentés
     */
    public function getTopCommented(int $limit = 5): array
    {
        return $this->article->getTopCommented($limit);
    }
}
