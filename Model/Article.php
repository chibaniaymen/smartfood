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
    private string $status;
    private string $tags;
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
    public function getStatus(): string          { return $this->status; }
    public function setStatus(string $v): void   { $this->status = $v; }
    public function getTags(): string            { return $this->tags; }
    public function setTags(string $v): void     { $this->tags = $v; }
    public function getCreatedAt(): string { return $this->created_at; }

    /**
     * Enregistre la vue d'un article dans l'historique utilisateur.
     */
    public function trackView(?int $userId, string $sessionId, int $articleId): void
    {
        // Anti-spam : on vérifie si l'utilisateur a déjà vu cet article dans la dernière heure
        $checkSql = "SELECT COUNT(*) FROM user_history 
                     WHERE (user_id = :userId OR (user_id IS NULL AND session_id = :sessionId)) 
                     AND article_id = :articleId 
                     AND viewed_at > (NOW() - INTERVAL 1 HOUR)";
        $stmtCheck = $this->pdo->prepare($checkSql);
        $stmtCheck->execute([
            'userId' => $userId,
            'sessionId' => $sessionId,
            'articleId' => $articleId
        ]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            $sql = "INSERT INTO user_history (user_id, session_id, article_id) VALUES (:userId, :sessionId, :articleId)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'userId' => $userId,
                'sessionId' => $sessionId,
                'articleId' => $articleId
            ]);
        }
    }

    /**
     * Apprend le profil utilisateur en stockant ses tags préférés.
     */
    public function updateUserProfile(string $sessionId, int $articleId, ?int $userId = null): void
    {
        // Récupérer les tags de l'article
        $stmt = $this->pdo->prepare("
            SELECT t.name 
            FROM tags t
            JOIN article_tag at ON t.id = at.tag_id
            WHERE at.article_id = :articleId
        ");
        $stmt->execute(['articleId' => $articleId]);
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($tags)) return;

        // Mettre à jour le profil (apprentissage)
        $insert = "INSERT INTO user_profile (user_id, session_id, tag, score) 
                   VALUES (:userId, :sessionId, :tag, 1) 
                   ON DUPLICATE KEY UPDATE score = score + 1";
        $stmtInsert = $this->pdo->prepare($insert);
        
        foreach ($tags as $tag) {
            $stmtInsert->execute([
                'userId' => $userId,
                'sessionId' => $sessionId,
                'tag' => $tag
            ]);
        }
    }

    /**
     * Calcule le score de correspondance entre les tags d'un article et le profil.
     */
    public function getUserProfileScore(int $articleId, string $sessionId): float
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(up.score), 0)
            FROM user_profile up
            JOIN tags t ON up.tag = t.name
            JOIN article_tag at ON t.id = at.tag_id
            WHERE at.article_id = :articleId
            AND up.session_id = :sessionId
        ");
        $stmt->execute([
            'articleId' => $articleId,
            'sessionId' => $sessionId
        ]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Nettoie le texte pour l'analyse NLP.
     */
    private function cleanText(string $text): string
    {
        $text = mb_strtolower(strip_tags($text), 'UTF-8');
        $text = preg_replace('/[[:punct:]]+/u', ' ', $text);
        
        $stopwords = ['le','la','les','de','des','un','une','et','ou','est','sont','dans','pour','par','sur','avec','qui','que','quoi','dont','où','ce','ces','se','sa','son','ses','mon','ton','ma','ta','mes','tes','ne','pas','plus','il','elle','ils','elles','nous','vous','je','tu','au','aux','du'];
        
        $words = explode(' ', $text);
        $filtered = array_filter($words, function($w) use ($stopwords) {
            return strlen($w) > 2 && !in_array($w, $stopwords);
        });
        
        return implode(' ', $filtered);
    }

    /**
     * Convertit un texte en vecteur de fréquences de mots.
     */
    private function getWordFrequencies(string $text): array
    {
        $words = explode(' ', $this->cleanText($text));
        $freqs = [];
        foreach ($words as $word) {
            if ($word !== '') {
                $freqs[$word] = ($freqs[$word] ?? 0) + 1;
            }
        }
        return $freqs;
    }

    /**
     * Calcule la similarité Cosinus (TF-IDF simplifié) entre deux vecteurs.
     */
    public function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vec1 as $word => $val) {
            $dot += $val * ($vec2[$word] ?? 0);
            $normA += $val * $val;
        }

        foreach ($vec2 as $val) {
            $normB += $val * $val;
        }

        if ($normA == 0 || $normB == 0) return 0.0;
        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Récupère le TOP 5 des articles recommandés via l'IA avancée.
     * Score = (tags*0.25) + (pop*0.15) + (hist*0.10) + (profile*0.25) + (sim*0.25)
     */
    public function getRecommendedArticles(int $currentArticleId, ?int $userId, string $sessionId): array
    {
        $stmtCurrent = $this->pdo->prepare("SELECT title, content FROM articles WHERE id = :id");
        $stmtCurrent->execute(['id' => $currentArticleId]);
        $currentArticle = $stmtCurrent->fetch(PDO::FETCH_ASSOC);
        if (!$currentArticle) return [];
        
        $currentVec = $this->getWordFrequencies($currentArticle['title'] . ' ' . $currentArticle['content']);

        $stmtAll = $this->pdo->prepare("SELECT id, title, content, created_at FROM articles WHERE status = 'published' AND id != :currentId");
        $stmtAll->execute(['currentId' => $currentArticleId]);
        $articles = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

        $sqlTags = "SELECT a.id, COUNT(*) as score_tags
                    FROM article_tag at1
                    JOIN article_tag at2 ON at1.tag_id = at2.tag_id
                    JOIN articles a ON a.id = at2.article_id
                    WHERE at1.article_id = :currentArticleId1 AND a.id != :currentArticleId2 AND a.status = 'published'
                    GROUP BY a.id";
        $stmtTags = $this->pdo->prepare($sqlTags);
        $stmtTags->execute([
            'currentArticleId1' => $currentArticleId,
            'currentArticleId2' => $currentArticleId
        ]);
        $tagsData = $stmtTags->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $sqlPop = "SELECT article_id, COUNT(*) as score_pop FROM commentaires GROUP BY article_id";
        $popData = $this->pdo->query($sqlPop)->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $sqlUser = "SELECT article_id, COUNT(*) as score_user 
                    FROM user_history 
                    WHERE (user_id = :userId OR (user_id IS NULL AND session_id = :sessionId))
                    GROUP BY article_id";
        $stmtUser = $this->pdo->prepare($sqlUser);
        $stmtUser->execute(['userId' => $userId, 'sessionId' => $sessionId]);
        $userHistory = $stmtUser->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $profileScores = [];
        foreach ($articles as $a) {
            $profileScores[$a['id']] = $this->getUserProfileScore($a['id'], $sessionId);
        }

        $maxTags = max(1, max($tagsData ?: [0]));
        $maxPop  = max(1, max($popData ?: [0]));
        $maxHist = max(1, max($userHistory ?: [0]));
        $maxProf = max(1, max($profileScores ?: [0]));

        $recommended = [];

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['ai_cache_similarity'])) $_SESSION['ai_cache_similarity'] = [];

        foreach ($articles as $art) {
            $id = $art['id'];

            $nTags = ($tagsData[$id] ?? 0) / $maxTags;
            $nPop  = ($popData[$id] ?? 0) / $maxPop;
            $nHist = ($userHistory[$id] ?? 0) / $maxHist;
            $nProf = ($profileScores[$id] ?? 0) / $maxProf;

            $cacheKey = $currentArticleId . '_' . $id;
            if (!isset($_SESSION['ai_cache_similarity'][$cacheKey])) {
                $otherVec = $this->getWordFrequencies($art['title'] . ' ' . $art['content']);
                $_SESSION['ai_cache_similarity'][$cacheKey] = $this->cosineSimilarity($currentVec, $otherVec);
            }
            $nSim = $_SESSION['ai_cache_similarity'][$cacheKey];

            $score = ($nTags * 0.25) + ($nPop * 0.15) + ($nHist * 0.10) + ($nProf * 0.25) + ($nSim * 0.25);

            $ageDays = max(1, (time() - strtotime($art['created_at'])) / 86400);
            $recencyBoost = ($ageDays <= 14) ? 1.2 : (($ageDays > 365) ? 0.9 : 1.0);
            
            if (isset($userHistory[$id])) {
                $recencyBoost *= 0.5;
            }

            $finalScore = $score * $recencyBoost;

            $reasons = [];
            if ($nTags > 0) $reasons[] = "Mots-clés en commun";
            if ($nProf > 0) $reasons[] = "Correspond à vos intérêts";
            if ($nPop > 0.5) $reasons[] = "Article très populaire";
            if ($nSim > 0.5) $reasons[] = "Contenu sémantiquement similaire";
            if ($ageDays <= 14) $reasons[] = "Récemment publié";

            if ($finalScore > 0 || $nPop > 0) {
                $art['ai_score'] = round($finalScore, 4);
                $art['ai_reasons'] = empty($reasons) ? ["Pourrait vous plaire"] : $reasons;
                $recommended[] = $art;
            }
        }

        usort($recommended, function($a, $b) {
            return $b['ai_score'] <=> $a['ai_score'];
        });

        if (empty($recommended)) {
            $fallback = array_slice($articles, 0, 5);
            foreach($fallback as &$f) { 
                $f['ai_score'] = 0; 
                $f['ai_reasons'] = ["Découverte"];
            }
            return $fallback;
        }

        return array_slice($recommended, 0, 5);
    }

    /**
     * Génère un rapport analytique intelligent (AI Insights Dashboard)
     */
    public function getAiSnapshotReport(?int $userId, string $sessionId): array
    {
        // 1. User AI Profile Summary
        $stmtTags = $this->pdo->prepare("
            SELECT tag, SUM(score) as total_score 
            FROM user_profile 
            WHERE session_id = :sessionId OR (user_id IS NOT NULL AND user_id = :userId)
            GROUP BY tag 
            ORDER BY total_score DESC 
            LIMIT 5
        ");
        $stmtTags->execute(['sessionId' => $sessionId, 'userId' => $userId]);
        $userProfile = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

        // 2. Trending Articles
        $stmtTrending = $this->pdo->query("
            SELECT a.id, a.title, a.created_at, COUNT(c.id) as comments_count
            FROM articles a
            LEFT JOIN commentaires c ON a.id = c.article_id
            WHERE a.status = 'published'
            GROUP BY a.id
            ORDER BY comments_count DESC, a.created_at DESC
            LIMIT 4
        ");
        $trending = $stmtTrending->fetchAll(PDO::FETCH_ASSOC);

        // 3. AI Top Recommendations
        $stmtLastViewed = $this->pdo->prepare("
            SELECT article_id FROM user_history 
            WHERE session_id = :sessionId OR (user_id IS NOT NULL AND user_id = :userId)
            ORDER BY viewed_at DESC LIMIT 1
        ");
        $stmtLastViewed->execute(['sessionId' => $sessionId, 'userId' => $userId]);
        $lastViewed = $stmtLastViewed->fetchColumn();

        if (!$lastViewed) {
            $lastViewed = $this->pdo->query("SELECT id FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 1")->fetchColumn();
        }

        $recommendations = [];
        if ($lastViewed) {
            $recommendations = $this->getRecommendedArticles((int)$lastViewed, $userId, $sessionId);
        }

        // 4. AI Confidence
        $stmtHistory = $this->pdo->prepare("
            SELECT COUNT(*) FROM user_history 
            WHERE session_id = :sessionId OR (user_id IS NOT NULL AND user_id = :userId)
        ");
        $stmtHistory->execute(['sessionId' => $sessionId, 'userId' => $userId]);
        $historyCount = (int) $stmtHistory->fetchColumn();

        if ($historyCount >= 10) {
            $confidence = 'High';
        } elseif ($historyCount >= 3) {
            $confidence = 'Medium';
        } else {
            $confidence = 'Low';
        }

        // 5. Global KPIs
        $totalArticles = (int) $this->pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
        $totalComments = (int) $this->pdo->query("SELECT COUNT(*) FROM commentaires")->fetchColumn();
        $totalViews = (int) $this->pdo->query("SELECT COUNT(*) FROM user_history")->fetchColumn();
        $engagementRate = $totalViews > 0 ? round(($totalComments / $totalViews) * 100, 1) : 0;

        // 6. Views Over Time (Line Chart)
        $stmtViews = $this->pdo->query("
            SELECT DATE(viewed_at) as date, COUNT(*) as views 
            FROM user_history 
            GROUP BY DATE(viewed_at) 
            ORDER BY date ASC 
            LIMIT 7
        ");
        $viewsOverTime = $stmtViews->fetchAll(PDO::FETCH_ASSOC);

        // 7. Categories / Tags Distribution (Pie Chart)
        $stmtCategories = $this->pdo->query("
            SELECT t.name as category, COUNT(at.article_id) as count 
            FROM tags t 
            JOIN article_tag at ON t.id = at.tag_id 
            GROUP BY t.name 
            ORDER BY count DESC 
            LIMIT 5
        ");
        $categoriesData = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

        return [
            'user_profile' => $userProfile,
            'recommendations' => $recommendations,
            'trending' => $trending,
            'ai_confidence' => $confidence,
            'kpis' => [
                'total_articles' => $totalArticles,
                'engagement_rate' => $engagementRate,
                'history_count' => $historyCount
            ],
            'charts' => [
                'views_over_time' => $viewsOverTime,
                'categories' => $categoriesData
            ]
        ];
    }
}
