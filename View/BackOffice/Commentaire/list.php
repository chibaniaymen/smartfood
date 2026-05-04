<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../Controller/CommentaireController.php';


$commentaireController = new CommentaireController($pdo);

$query     = trim($_GET['q'] ?? '');
$sortBy    = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'DESC';

$comments  = $commentaireController->searchAndSort($query, $sortBy, $sortOrder);

$pageTitle  = 'Commentaires';
$activeMenu = 'comment-list';

$success = $_GET['success'] ?? '';

// Métier Avancé : Traitement de la purge
if (isset($_GET['action']) && $_GET['action'] === 'purge' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $purgeResult = $commentaireController->purgeRejected();
    if ($purgeResult['success']) {
        $success = 'deleted'; // Réutilisation du message de succès
    }
}

$messages = ['created' => '✅ Commentaire créé avec succès.', 'updated' => '✅ Commentaire modifié avec succès.', 'deleted' => '✅ Nettoyage terminé : Les commentaires rejetés ont été supprimés.'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-comments" style="color:#E76F51;margin-right:8px;"></i>Commentaires</h1>
    <div class="breadcrumb-sf">Admin → Commentaires</div>
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
      <i class="fas fa-chart-pie"></i>
    </button>
    <span style="background:#fde2e2;color:#721c24;padding:6px 16px;border-radius:50px;font-size:.82rem;font-weight:600;margin-right:10px;">
      <?php echo count($comments); ?> commentaire(s)
    </span>
    <a href="add.php" class="btn btn-success btn-sm" style="background:#E76F51;border:none;border-radius:50px;padding:8px 20px;">
      <i class="fas fa-plus"></i> Nouveau commentaire
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
      <label class="form-label fw-semibold small">Recherche par mot-clé</label>
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="q" class="form-control border-start-0" placeholder="Auteur ou contenu..." value="<?php echo htmlspecialchars($query); ?>">
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
        <option value="author" <?php echo $sortBy === 'author' ? 'selected' : ''; ?>>Auteur</option>
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
    <h6 class="fw-bold mb-3"><i class="fas fa-users text-info"></i> Top 3 des auteurs les plus actifs</h6>
    <div class="row">
      <?php 
      $topAuthors = $commentaireController->getTopAuthors(3);
      foreach($topAuthors as $ta): 
      ?>
      <div class="col-md-4">
        <div class="p-2 border rounded bg-light mb-2 d-flex justify-content-between align-items-center">
          <span class="fw-bold text-truncate"><?php echo htmlspecialchars($ta['author']); ?></span>
          <span class="badge bg-info"><?php echo $ta['nb_comments']; ?> coms</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div style="background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;">
  <table class="table table-hover" style="margin:0;font-size:.88rem;">
    <thead style="background:#f8f9fa;">
      <tr>
        <th style="padding:14px 20px;">#</th>
        <th>Auteur</th>
        <th>Article</th>
        <th>Commentaire</th>
        <th>Sentiment</th>
        <th>Statut</th>
        <th style="text-align:center;">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($comments)): ?>
        <tr><td colspan="7" style="text-align:center;padding:40px;color:#adb5bd;">Aucun commentaire pour le moment.</td></tr>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
        <?php 
          $sentiment = $commentaireController->analyzeSentiment($c['content']);
          $sentColor = $sentiment === 'positive' ? '#28a745' : ($sentiment === 'negative' ? '#dc3545' : '#6c757d');
          $sentIcon  = $sentiment === 'positive' ? 'smile' : ($sentiment === 'negative' ? 'frown' : 'meh');
          
          $statusBadge = $c['status'] === 'approved' ? 'bg-success' : ($c['status'] === 'rejected' ? 'bg-danger' : 'bg-warning');
        ?>
        <tr>
          <td style="padding:14px 20px;color:#adb5bd;">#<?php echo $c['id']; ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:32px;height:32px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#2D6A4F;">
                <?php echo mb_strtoupper(mb_substr($c['author'], 0, 2)); ?>
              </div>
              <span style="font-weight:600;"><?php echo htmlspecialchars($c['author']); ?></span>
            </div>
          </td>
          <td style="color:#6c757d;max-width:150px;">
            <?php echo htmlspecialchars(mb_substr($c['article_title'] ?? 'Inconnu', 0, 30)); ?>…
          </td>
          <td style="max-width:200px;color:#495057;">
            <?php echo htmlspecialchars(mb_substr($c['content'], 0, 60)); ?>…
            <div class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></div>
          </td>
          <td>
            <span style="color:<?php echo $sentColor; ?>;font-weight:600;font-size:.75rem;">
              <i class="fas fa-<?php echo $sentIcon; ?>"></i> <?php echo ucfirst($sentiment); ?>
            </span>
          </td>
          <td>
            <span class="badge <?php echo $statusBadge; ?>" style="font-size:.7rem;">
              <?php echo strtoupper($c['status']); ?>
            </span>
          </td>
          <td style="text-align:center;white-space:nowrap;">
            <a href="update.php?id=<?php echo $c['id']; ?>"
               class="btn btn-sm btn-warning" style="border-radius:6px;margin-right:4px;" title="Modifier">
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="delete.php?id=<?php echo $c['id']; ?>"
               class="btn btn-sm btn-danger" style="border-radius:6px;"
               onclick="return confirm('Supprimer ce commentaire ?')" title="Supprimer">
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

