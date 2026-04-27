<?php

/**
 * Contrôleur Commentaire
 * Contient la logique métier et les validations pour l'entité Commentaire
 *
 * @package blogMVC
 * @subpackage Controller
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Commentaire.php';

class CommentaireController
{
    /** @var Commentaire Instance du modèle Commentaire */
    private Commentaire $commentaire;

    public function __construct(Commentaire $commentaire)
    {
        $this->commentaire = $commentaire;
    }

    // ── Lecture ────────────────────────────────────────────────────────────

    /** Retourne tous les commentaires (avec titre de l'article) */
    public function getAll(): array
    {
        return $this->commentaire->findAll();
    }

    /** Retourne les commentaires d'un article donné */
    public function getByArticle(int $articleId): array
    {
        if (!isValidId($articleId)) return [];
        return $this->commentaire->findByArticle($articleId);
    }

    /** Retourne le nombre total de commentaires */
    public function count(): int
    {
        return $this->commentaire->count();
    }

    /** Retourne un commentaire par son ID */
    public function getById(int $id): ?array
    {
        if (!isValidId($id)) return null;
        return $this->commentaire->findById($id);
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

        try {
            $this->commentaire->create($articleId, $author, $content);
            return ['success' => true, 'message' => 'Commentaire ajouté avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()];
        }
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

        $error = $this->validateCommentaire($author, $content);
        if ($error) return ['success' => false, 'message' => $error];

        try {
            $this->commentaire->update($id, $author, $content);
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
            $this->commentaire->delete($id);
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
        return $this->commentaire->findCommentairesByArticleJoin($articleId);
    }

    /**
     * Retourne tous les commentaires avec le titre de l'article associé (INNER JOIN global)
     *
     * @return array Résultat de la jointure globale
     */
    public function getAllWithArticle(): array
    {
        return $this->commentaire->findAllWithArticle();
    }

    // ── Validation privée ──────────────────────────────────────────────────

    /**
     * Recherche et tri des commentaires (Métier)
     */
    public function searchAndSort(string $query = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        return $this->commentaire->searchAndSort($query, $sortBy, $sortOrder);
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
        return $this->commentaire->getTopAuthors($limit);
    }
}
