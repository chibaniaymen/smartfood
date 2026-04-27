<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Article.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';

$articleModel      = new Article($pdo);
$articleController = new ArticleController($articleModel);
$query     = trim($_GET['q'] ?? '');
$sortBy    = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'DESC';

$articles = $articleController->searchAndSort($query, $sortBy, $sortOrder);
$pageTitle  = 'Liste des articles';
$activeMenu = 'article-list';

// Message flash après redirection
$success = $_GET['success'] ?? '';
$messages = ['created' => '✅ Article créé avec succès.', 'updated' => '✅ Article modifié avec succès.', 'deleted' => '✅ Article supprimé avec succès.'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-newspaper" style="color:#2D6A4F;margin-right:8px;"></i>Articles</h1>
    <div class="breadcrumb-sf">Admin → <a href="list.php">Articles</a></div>
  </div>
  <div>
    <!-- Icônes Métiers : Séparées -->
    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#searchPanel" title="Rechercher" style="border-radius:50px;margin-right:5px;padding:8px 16px;">
      <i class="fas fa-search"></i>
    </button>
    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#sortPanel" title="Trier" style="border-radius:50px;margin-right:5px;padding:8px 16px;">
      <i class="fas fa-sort-amount-down"></i>
    </button>
    <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#statsPanel" title="Statistiques" style="border-radius:50px;margin-right:10px;padding:8px 16px;">
      <i class="fas fa-chart-bar"></i>
    </button>
    <a href="add.php" class="btn btn-success btn-sm" style="border-radius:50px;padding:8px 20px;">
      <i class="fas fa-plus"></i> Nouvel article
    </a>
  </div>
</div>

<?php if ($success && isset($messages[$success])): ?>
  <div class="alert alert-success alert-dismissible" style="border-radius:10px;">
    <?php echo $messages[$success]; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- ── MÉTIER : RECHERCHE SEULE ── -->
<div class="collapse mb-3 <?php echo ($query !== '') ? 'show' : ''; ?>" id="searchPanel">
  <form method="GET" action="list.php" style="background:white;padding:15px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06); border-left: 4px solid #0d6efd;">
    <div class="row align-items-end">
      <div class="col-md-10">
        <label class="form-label fw-semibold small">Recherche (Titre ou Contenu)</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
          <input type="text" name="q" class="form-control border-start-0" placeholder="Mot-clé..." value="<?php echo htmlspecialchars($query); ?>">
          <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
          <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
        </div>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100 fw-bold">Chercher</button>
      </div>
    </div>
  </form>
</div>

<!-- ── MÉTIER : TRI SEUL ── -->
<div class="collapse mb-3 <?php echo (isset($_GET['sort'])) ? 'show' : ''; ?>" id="sortPanel">
  <form method="GET" action="list.php" style="background:white;padding:15px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06); border-left: 4px solid #6c757d;">
    <div class="row align-items-end">
      <input type="hidden" name="q" value="<?php echo htmlspecialchars($query); ?>">
      <div class="col-md-5">
        <label class="form-label fw-semibold small">Trier par</label>
        <select name="sort" class="form-select bg-light">
          <option value="created_at" <?php echo $sortBy === 'created_at' ? 'selected' : ''; ?>>Date</option>
          <option value="title" <?php echo $sortBy === 'title' ? 'selected' : ''; ?>>Titre</option>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label fw-semibold small">Ordre</label>
        <select name="order" class="form-select bg-light">
          <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Décroissant</option>
          <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Croissant</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100 fw-bold">Trier</button>
      </div>
    </div>
  </form>
</div>

<!-- ── MÉTIER : STATISTIQUES ── -->
<div class="collapse mb-3" id="statsPanel">
  <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06); border-left: 4px solid #0dcaf0;">
    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-info"></i> Top 3 des articles les plus commentés</h6>
    <div class="row">
      <?php 
      $topArticles = $articleController->getTopCommented(3);
      foreach($topArticles as $ta): 
      ?>
      <div class="col-md-4">
        <div class="p-2 border rounded bg-light mb-2">
          <div class="small text-muted text-truncate"><?php echo htmlspecialchars($ta['title']); ?></div>
          <div class="fw-bold text-info"><?php echo $ta['comment_count']; ?> commentaires</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div style="background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;">
  <div style="padding:16px 20px;border-bottom:1px solid #e9ecef;display:flex;align-items:center;justify-content:space-between;">
    <span style="font-size:.85rem;color:#adb5bd;"><?php echo count($articles); ?> article(s)</span>
  </div>
  <table class="table table-hover" style="margin:0;font-size:.88rem;">
    <thead style="background:#f8f9fa;">
      <tr>
        <th style="padding:14px 20px;">#</th>
        <th>Titre</th>
        <th>Date</th>
        <th style="text-align:center;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($articles)): ?>
        <tr><td colspan="6" style="text-align:center;padding:40px;color:#adb5bd;">Aucun article. <a href="add.php">Créer le premier →</a></td></tr>
      <?php else: ?>
        <?php foreach ($articles as $a): ?>
        <tr>
          <td style="padding:14px 20px;color:#adb5bd;">#<?php echo $a['id']; ?></td>

          <td style="font-weight:600;max-width:280px;">
            <?php echo htmlspecialchars(mb_substr($a['title'], 0, 55)); ?>…
          </td>

          <td style="color:#adb5bd;"><?php echo date('d/m/Y', strtotime($a['created_at'])); ?></td>
          <td style="text-align:center;">
            <a href="../../../View/FrontOffice/article.php?id=<?php echo $a['id']; ?>" target="_blank"
               class="btn btn-sm" style="background:#e8f5e9;color:#2D6A4F;border:none;border-radius:6px;margin-right:4px;" title="Voir">
              <i class="fas fa-eye"></i>
            </a>
            <a href="update.php?id=<?php echo $a['id']; ?>"
               class="btn btn-sm btn-warning" style="border-radius:6px;margin-right:4px;" title="Modifier">
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="delete.php?id=<?php echo $a['id']; ?>"
               class="btn btn-sm btn-danger" style="border-radius:6px;"
               onclick="return confirm('Supprimer définitivement cet article et ses commentaires ?')" title="Supprimer">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
