<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/ArticleController.php';
require_once __DIR__ . '/../../controller/CommentaireController.php';

$articleController = new ArticleController($pdo);
$commentController = new CommentaireController($pdo);

$nbArticles = $articleController->count();
$nbComments = $commentController->count();
$topArticles = $articleController->getTopCommented(5);
$topAuthors  = $commentController->getTopAuthors(5);

$pageTitle  = 'Statistiques Globales';
$activeMenu = 'stats';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-chart-line" style="color:#177dff;margin-right:8px;"></i>Statistiques du Blog</h1>
    <div class="breadcrumb-sf">Admin → Statistiques</div>
  </div>
</div>

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
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-title">Répartition Engagement</div>
      </div>
      <div class="card-body">
        <div class="chart-container" style="min-height:300px;">
          <canvas id="engagementChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-title">Auteurs les plus actifs</div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-head-bg-info">
            <thead>
              <tr><th>Auteur</th><th class="text-center">Commentaires</th></tr>
            </thead>
            <tbody>
              <?php foreach ($topAuthors as $ta): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($ta['author']); ?></strong></td>
                <td class="text-center"><span class="badge badge-info"><?php echo $ta['nb_comments']; ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-title">Top 5 des articles les plus commentés</div>
      </div>
      <div class="card-body">
        <?php foreach ($topArticles as $art): ?>
        <div class="d-flex align-items-center mb-3">
          <div class="flex-1 ms-3">
            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($art['title']); ?></h6>
          </div>
          <div class="text-end">
            <span class="badge badge-success"><?php echo $art['comment_count']; ?> commentaires</span>
          </div>
        </div>
        <div class="progress mb-3" style="height: 6px;">
          <?php 
            $percent = $nbComments > 0 ? ($art['comment_count'] / $nbComments) * 100 : 0;
          ?>
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/plugin/chart.js/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('engagementChart').getContext('2d');
  var engagementChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Articles', 'Commentaires'],
      datasets: [{
        data: [<?php echo $nbArticles; ?>, <?php echo $nbComments; ?>],
        backgroundColor: ['#1d7af3', '#f3545d'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: { position: 'bottom' }
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
