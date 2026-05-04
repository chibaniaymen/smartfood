<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../config.php';


require_once __DIR__ . '/../../Controller/ArticleController.php';
require_once __DIR__ . '/../../Controller/CommentaireController.php';



$articleController     = new ArticleController($pdo);
$commentaireController = new CommentaireController($pdo);

$pageTitle  = 'Tableau de bord';
$activeMenu = 'dashboard';

require_once __DIR__ . '/includes/header.php';

$articles   = $articleController->getAll();
$comments   = $commentaireController->getAll();
$nbArticles = $articleController->count();
$nbComments = $commentaireController->count();
$topArticles= $articleController->getTopCommented(5); // Partie Métier: Stats
?>

<!-- Page Title -->
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
  <div>
    <h3 class="fw-bold mb-3">Tableau de bord</h3>
    <h6 class="op-7 mb-2">SmartFood — Gestion du blog nutrition</h6>
  </div>
  <div class="ms-md-auto py-2 py-md-0">
    <a href="Article/list.php" class="btn btn-label-info btn-round me-2">Gérer les articles</a>
    <a href="Article/add.php" class="btn btn-primary btn-round">+ Nouvel article</a>
  </div>
</div>

<!-- ── STAT CARDS ────────────────────────────────────────── -->
<div class="row">
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-primary bubble-shadow-small">
              <i class="fas fa-newspaper"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Articles</p>
              <h4 class="card-title"><?php echo $nbArticles; ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-info bubble-shadow-small">
              <i class="fas fa-comments"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Commentaires</p>
              <h4 class="card-title"><?php echo $nbComments; ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-secondary bubble-shadow-small">
              <i class="fas fa-file-export"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Exportations</p>
              <h4 class="card-title">Disponibles</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-secondary bubble-shadow-small">
              <i class="fas fa-database"></i>
            </div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers">
              <p class="card-category">Base de données</p>
              <h4 class="card-title">feane_blog</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── CHARTS + RECENT ───────────────────────────────────── -->
<div class="row">

  <!-- Graphique Articles par mois -->
  <div class="col-md-8">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row">
          <div class="card-title">Activité du blog</div>
          <div class="card-tools">
            <a href="Article/list.php" class="btn btn-label-success btn-round btn-sm me-2">
              <span class="btn-label"><i class="fas fa-newspaper"></i></span> Articles
            </a>
            <a href="Commentaire/list.php" class="btn btn-label-info btn-round btn-sm">
              <span class="btn-label"><i class="fas fa-comments"></i></span> Commentaires
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="chart-container" style="min-height:300px;">
          <canvas id="blogActivityChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions rapides -->
  <div class="col-md-4">
    <div class="card card-primary card-round">
      <div class="card-header">
        <div class="card-head-row">
          <div class="card-title">Actions rapides</div>
        </div>
        <div class="card-category">Raccourcis d'administration</div>
      </div>
      <div class="card-body pb-0">
        <div class="mb-3 mt-2" style="display:flex;flex-direction:column;gap:10px;">
          <a href="Article/add.php" class="btn btn-success btn-round w-100">
            <i class="fas fa-plus me-2"></i> Nouvel article
          </a>
          <a href="Commentaire/list.php" class="btn btn-info btn-round w-100">
            <i class="fas fa-comments me-2"></i> Voir les commentaires
          </a>
          <a href="Jointure/export_data.php" class="btn btn-warning btn-round w-100">
            <i class="fas fa-file-export me-2"></i> Exportation CSV
          </a>
          <a href="../FrontOffice/index.php" target="_blank" class="btn btn-label-success btn-round w-100">
            <span class="btn-label"><i class="fas fa-external-link-alt"></i></span> Voir le site
          </a>
        </div>
      </div>
    </div>

    <div class="card card-round">
      <div class="card-body pb-0">
        <div class="h1 fw-bold float-end text-primary"><?php echo $nbComments; ?></div>
        <h2 class="mb-2"><?php echo $nbArticles; ?></h2>
        <p class="text-muted">Articles publiés</p>
        <div class="pull-in sparkline-fix">
          <div id="sparklineArticles"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── MÉTIER AVANCÉ : TRENDING & ACTIONS GLOBALES ──────────────── -->
<div class="row">

  <!-- Articles Trending (Score d'engagement) -->
  <div class="col-md-8">
    <div class="card card-round" style="border-top: 5px solid #ff9800;">
      <div class="card-header">
        <div class="card-head-row">
          <div class="card-title"><i class="fas fa-fire text-warning me-2"></i>Articles en vogue (Top Engagement)</div>
        </div>
        <div class="card-category">Basé sur les commentaires et le sentiment positif</div>
      </div>
      <div class="card-body">
        <div class="row">
          <?php 
          $trending = $articleController->getTrendingArticles(3);
          foreach($trending as $art): 
            $readTime = $articleController->calculateReadTime($art['content']);
          ?>
          <div class="col-md-4">
            <div class="p-3 border rounded h-100 bg-white shadow-sm">
              <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Trending</span>
                <small class="text-muted"><i class="far fa-clock"></i> <?php echo $readTime; ?> min</small>
              </div>
              <h6 class="fw-bold text-truncate"><?php echo htmlspecialchars($art['title']); ?></h6>
              <div class="small text-muted mb-3">
                <i class="fas fa-comments"></i> <?php echo $art['comment_count']; ?> coms
              </div>
              <a href="../FrontOffice/article.php?id=<?php echo $art['id']; ?>" target="_blank" class="btn btn-xs btn-outline-warning w-100">Voir</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions de Masse (Nettoyage Intelligent) -->
  <div class="col-md-4">
    <div class="card card-round bg-dark text-white">
      <div class="card-header">
        <div class="card-title text-white">Nettoyage Intelligent</div>
      </div>
      <div class="card-body">
        <p class="small op-7">Optimisez votre base de données en supprimant le contenu indésirable.</p>
        <form method="POST" action="Commentaire/list.php?action=purge">
           <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Supprimer définitivement tous les commentaires REJETÉS ?')">
             <i class="fas fa-broom me-2"></i> Purger les rejets
           </button>
        </form>
        <div class="alert alert-info py-2 small mb-0" style="background:rgba(255,255,255,0.1); border:none;">
           <i class="fas fa-info-circle"></i> Cette action est irréversible.
        </div>
      </div>
    </div>
  </div>

</div>

<!-- ── TABLEAUX ───────────────────────────────────────────── -->
<div class="row">

  <!-- Articles récents -->
  <div class="col-md-7">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row card-tools-still-right">
          <h4 class="card-title">Articles récents</h4>
          <div class="card-tools">
            <a href="Article/list.php" class="btn btn-icon btn-link btn-primary btn-xs">
              <i class="fas fa-list"></i>
            </a>
            <a href="Article/add.php" class="btn btn-icon btn-link btn-success btn-xs">
              <i class="fas fa-plus"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($articles)): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">Aucun article. <a href="Article/add.php">Créer →</a></td></tr>
              <?php else: ?>
                <?php foreach (array_slice($articles, 0, 5) as $a): ?>
                <tr>
                  <td><span class="text-muted">#<?php echo $a['id']; ?></span></td>
                  <td style="font-weight:600;"><?php echo htmlspecialchars(mb_substr($a['title'], 0, 40)); ?>…</td>
                  <td><span class="text-muted"><?php echo date('d/m/Y', strtotime($a['created_at'])); ?></span></td>
                  <td class="text-center">
                    <a href="../FrontOffice/article.php?id=<?php echo $a['id']; ?>" target="_blank" class="btn btn-icon btn-round btn-success btn-sm" title="Voir"><i class="fas fa-eye"></i></a>
                    <a href="Article/update.php?id=<?php echo $a['id']; ?>" class="btn btn-icon btn-round btn-warning btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                    <a href="Article/delete.php?id=<?php echo $a['id']; ?>" class="btn btn-icon btn-round btn-danger btn-sm" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Derniers commentaires -->
  <div class="col-md-5">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row card-tools-still-right">
          <h4 class="card-title">Derniers commentaires</h4>
          <div class="card-tools">
            <a href="Commentaire/list.php" class="btn btn-icon btn-link btn-primary btn-xs">
              <i class="fas fa-list"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr><th>Auteur</th><th>Article</th><th>Date</th></tr>
            </thead>
            <tbody>
              <?php if (empty($comments)): ?>
                <tr><td colspan="3" class="text-center py-4 text-muted">Aucun commentaire</td></tr>
              <?php else: ?>
                <?php foreach (array_slice($comments, 0, 5) as $c): ?>
                <tr>
                  <td><span class="fw-bold"><?php echo htmlspecialchars($c['author']); ?></span></td>
                  <td><span class="badge badge-success">art #<?php echo $c['article_id']; ?></span></td>
                  <td><span class="text-muted" style="font-size:.78rem;"><?php echo date('d/m/Y', strtotime($c['created_at'])); ?></span></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<?php
$extraScripts = <<<JS
<script>
// Graphique activité blog
var ctx = document.getElementById('blogActivityChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
    datasets: [
      {
        label: 'Articles',
        backgroundColor: 'rgba(45,106,79,.7)',
        borderColor: '#2D6A4F',
        borderWidth: 2,
        data: [0,0,0,{$nbArticles},0,0,0,0,0,0,0,0]
      },
      {
        label: 'Commentaires',
        backgroundColor: 'rgba(231,111,81,.7)',
        borderColor: '#E76F51',
        borderWidth: 2,
        data: [0,0,0,{$nbComments},0,0,0,0,0,0,0,0]
      }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});

// Sparkline articles
$("#sparklineArticles").sparkline([2,4,3,{$nbArticles},{$nbComments},5,6], {
  type: 'line', height: '70', width: '100%',
  lineWidth: '2', lineColor: '#177dff',
  fillColor: 'rgba(23,125,255,.14)'
});
</script>
JS;
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

