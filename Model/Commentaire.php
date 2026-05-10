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

    /** @var string Statut du commentaire (approved, pending, rejected) */
    private string $status;

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
    public function getStatus(): string          { return $this->status; }
    public function setStatus(string $v): void   { $this->status = $v; }
    public function getCreatedAt(): string { return $this->created_at; }
}
