<?php
/**
 * Vue FrontOffice — Recherche de commentaires par article (Workshop Jointure)
 * Jointure INNER JOIN entre `commentaires` et `articles`
 */
require_once __DIR__ . '/../../config.php';


require_once __DIR__ . '/../../Controller/ArticleController.php';
require_once __DIR__ . '/../../Controller/CommentaireController.php';



$articleController     = new ArticleController($pdo);
$commentaireController = new CommentaireController($pdo);

// Récupérer tous les articles pour le menu déroulant
$articles = $articleController->getAll();

// Traitement du formulaire POST
$list      = null;
$idArticle = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['article'])
    && isset($_POST['search'])) {

    $idArticle = (int) $_POST['article'];

    if (isValidId($idArticle)) {
        // ── JOINTURE : commentaires INNER JOIN articles ──────────────────
        $list = $commentaireController->getCommentairesByArticleJoin($idArticle);
    }
}

$icons = ['🥦','🍎','🥑','🫐','🌿','🍋','🥕','🫚'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Recherche de commentaires — SmartFood</title>
  <link rel="icon" href="images/favicon.png" type="image/png"/>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/responsive.css"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green: #2D6A4F; --green-lt: #52B788; --orange: #E76F51;
      --dark: #1a1a2e; --muted: #6c757d; --border: #e9ecef;
      --radius: 16px;
      --ff-head: 'Playfair Display', Georgia, serif;
      --ff-body: 'Inter', sans-serif;
    }
    body { font-family: var(--ff-body); background: #f8f9fa; color: var(--dark); }

    /* NAV */
    .nav { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; height: 68px; }
    .nav-brand { font-family: var(--ff-head); font-size: 1.6rem; font-weight: 700; color: var(--green); text-decoration: none; }
    .nav-brand span { color: var(--orange); }
    .nav-back { display: flex; align-items: center; gap: 8px; color: var(--muted); text-decoration: none; font-size: .85rem; font-weight: 500; padding: 8px 16px; border-radius: 50px; border: 1.5px solid var(--border); transition: all .2s; }
    .nav-back:hover { border-color: var(--green); color: var(--green); }

    /* HERO */
    .page-hero { background: linear-gradient(135deg, var(--green), #1B4332); padding: 48px 40px; text-align: center; }
    .page-hero h1 { font-family: var(--ff-head); font-size: 2rem; color: white; margin-bottom: 10px; }
    .page-hero p  { color: rgba(255,255,255,.7); font-size: .95rem; }

    /* LAYOUT */
    .page-layout { max-width: 860px; margin: 0 auto; padding: 48px 24px 80px; }

    /* FORMULAIRE */
    .search-card {
      background: white;
      border-radius: var(--radius);
      padding: 32px 36px;
      box-shadow: 0 2px 20px rgba(0,0,0,.07);
      margin-bottom: 36px;
    }
    .search-card h2 {
      font-family: var(--ff-head);
      font-size: 1.3rem;
      color: var(--dark);
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--border);
    }
    .form-row { display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap; }
    .form-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 200px; }
    .form-group label { font-size: .82rem; font-weight: 600; color: var(--dark); }
    .form-select {
      padding: 11px 14px; border: 1.5px solid var(--border);
      border-radius: 10px; font-size: .9rem; font-family: var(--ff-body);
      background: #f8f9fa; cursor: pointer; width: 100%;
      transition: border-color .2s;
    }
    .form-select:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(45,106,79,.1); }
    .btn-search {
      background: var(--green); color: white; border: none;
      padding: 12px 28px; border-radius: 50px; font-size: .92rem;
      font-weight: 600; cursor: pointer; font-family: var(--ff-body);
      display: inline-flex; align-items: center; gap: 8px;
      transition: background .2s; white-space: nowrap;
    }
    .btn-search:hover { background: #1b4332; }

    /* REQUÊTE SQL affichée */
    .sql-badge {
      background: #1a1a2e; border-radius: 10px; padding: 16px 20px;
      margin-top: 20px; font-family: 'Courier New', monospace;
      font-size: .8rem; color: #95D5B2; line-height: 1.6;
      overflow-x: auto;
    }
    .sql-kw  { color: #52B788; font-weight: 700; }
    .sql-tbl { color: #E76F51; }
    .sql-col { color: #ffd166; }
    .sql-val { color: #a8dadc; }

    /* RÉSULTATS */
    .results-section { }
    .results-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 20px;
    }
    .results-title {
      font-family: var(--ff-head); font-size: 1.3rem; color: var(--dark);
      display: flex; align-items: center; gap: 10px;
    }
    .results-count {
      background: var(--green); color: white; padding: 4px 14px;
      border-radius: 50px; font-size: .8rem; font-weight: 600;
    }

    /* CARTE COMMENTAIRE */
    .comment-result {
      background: white; border-radius: var(--radius);
      padding: 22px 26px; margin-bottom: 16px;
      box-shadow: 0 2px 12px rgba(0,0,0,.05);
      border-left: 4px solid var(--green-lt);
      display: grid; grid-template-columns: auto 1fr; gap: 18px; align-items: start;
    }
    .comment-avatar {
      width: 46px; height: 46px; border-radius: 50%;
      background: linear-gradient(135deg, var(--green-lt), var(--green));
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: 700; font-size: .9rem; text-transform: uppercase;
      flex-shrink: 0;
    }
    .comment-body { }
    .comment-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px; }
    .comment-author { font-weight: 700; font-size: .95rem; color: var(--dark); }
    .comment-article {
      font-size: .75rem; background: #e8f5e9; color: var(--green);
      padding: 3px 10px; border-radius: 50px; font-weight: 600;
    }
    .comment-date { font-size: .75rem; color: #adb5bd; margin-left: auto; }
    .comment-text { font-size: .88rem; color: #495057; line-height: 1.65; }

    /* VIDE */
    .no-results {
      text-align: center; padding: 48px; background: white;
      border-radius: var(--radius); color: var(--muted);
      box-shadow: 0 2px 12px rgba(0,0,0,.05);
    }
    .no-results p { font-size: 2.5rem; margin-bottom: 12px; }

    /* FOOTER */
    .footer { background: var(--dark); color: rgba(255,255,255,.5); padding: 28px 40px; display: flex; justify-content: space-between; align-items: center; font-size: .82rem; }
    .footer a { color: rgba(255,255,255,.5); text-decoration: none; }
    .footer a:hover { color: white; }
    .footer-brand { font-family: var(--ff-head); font-size: 1.2rem; color: white; margin-bottom: 4px; }

    @media (max-width: 640px) {
      .nav { padding: 0 16px; }
      .page-hero { padding: 32px 20px; }
      .search-card { padding: 22px 20px; }
      .form-row { flex-direction: column; }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg custom_nav-container">
  <a class="navbar-brand" href="index.php" style="font-size:1.5rem;font-weight:800;">Smart<span style="color:#f7941d;">Food</span></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mx-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
      <li class="nav-item"><a class="nav-link" href="index.php">Blog</a></li>
      <li class="nav-item active"><a class="nav-link" href="searchCommentaires.php">Commentaires</a></li>
    </ul>
    <div class="user_option">
      <a href="../../View/BackOffice/dashboard.php" class="order_online_btn">
        <i class="fa fa-cog me-1"></i> Admin
      </a>
    </div>
  </div>
</nav>

<div class="page-hero">
  <h1>💬 Recherche de commentaires par article</h1>
</div>

<div class="page-layout">

  <!-- ── FORMULAIRE ─────────────────────────────────────────────── -->
  <div class="search-card">
    <h2>🔍 Sélectionnez un article</h2>

    <form method="POST" action="">
      <div class="form-row">
        <div class="form-group">
          <label for="article">Sélectionnez un article :</label>
          <select name="article" id="article" class="form-select">
            <?php foreach ($articles as $art): ?>
              <option value="<?php echo $art['id']; ?>"
                <?php echo ($idArticle == $art['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(mb_substr($art['title'], 0, 60)); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" name="search" value="1" class="btn-search">
          <i class="fa fa-search"></i> Rechercher
        </button>
      </div>
    </form>

    <!-- Requête SQL masquée -->
  </div>

  <?php if (isset($_GET['success'])): ?>
    <?php if($_GET['success'] == 'updated') $msg = 'Commentaire modifié avec succès !'; ?>
    <?php if($_GET['success'] == 'deleted') $msg = 'Commentaire supprimé avec succès !'; ?>
    <div class="alert alert-success" style="background:#e8f5e9;color:#2D6A4F;padding:16px;border-radius:12px;margin-bottom:20px;font-weight:600;">
      ✅ <?php echo $msg; ?>
    </div>
  <?php endif; ?>

  <!-- ── RÉSULTATS ──────────────────────────────────────────────── -->
  <?php if ($list !== null): ?>
  <div class="results-section">
    <div class="results-header">
      <div class="results-title">
        <i class="fa fa-comments" style="color:var(--green);"></i>
        Commentaires correspondants au genre sélectionné :
      </div>
      <span class="results-count"><?php echo count($list); ?> résultat(s)</span>
    </div>

    <?php if (empty($list)): ?>
      <div class="no-results">
        <p>😶</p>
        <strong>Aucun commentaire pour cet article.</strong><br>
        <span style="font-size:.85rem;">Soyez le premier à commenter !</span>
      </div>

    <?php else: ?>
      <?php foreach ($list as $row): ?>
      <div class="comment-result">
        <div class="comment-avatar">
          <?php echo mb_strtoupper(mb_substr($row['author'], 0, 2)); ?>
        </div>
        <div class="comment-body">
          <div class="comment-meta">
            <span class="comment-author"><?php echo htmlspecialchars($row['author']); ?></span>
            <span class="comment-article">
              <?php echo $icons[$row['article_id'] % count($icons)]; ?>
              <?php echo htmlspecialchars(mb_substr($row['article_title'], 0, 40)); ?>
            </span>
            <span class="comment-date">
              <i class="fa fa-calendar-o"></i>
              <?php echo date('d/m/Y à H:i', strtotime($row['commentaire_date'])); ?>
            </span>
          </div>
          <p class="comment-text"><?php echo nl2br(htmlspecialchars($row['commentaire_content'])); ?></p>
          
          <!-- Actions CRUD FrontOffice -->
          <div style="margin-top:12px;display:flex;gap:8px;">
            <a href="updateCommentaire.php?id=<?php echo $row['commentaire_id']; ?>" style="font-size:.8rem;color:#E76F51;text-decoration:none;font-weight:600;background:#fff3e0;padding:4px 12px;border-radius:50px;">
              <i class="fa fa-pencil"></i> Modifier
            </a>
            <a href="deleteCommentaire.php?id=<?php echo $row['commentaire_id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');" style="font-size:.8rem;color:#dc3545;text-decoration:none;font-weight:600;background:#fde2e2;padding:4px 12px;border-radius:50px;">
              <i class="fa fa-trash"></i> Supprimer
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div><!-- /.page-layout -->

<section class="footer_section footer_bg">
  <div class="container">
    <div class="row">
      <div class="col-md-4 footer-col">
        <h4>SmartFood</h4>
        <p>Votre blog dédié à la nutrition saine et au bien-être.</p>
      </div>
      <div class="col-md-4 footer-col">
        <h4>Navigation</h4>
        <ul style="list-style:none;padding:0;">
          <li><a href="index.php" style="color:rgba(255,255,255,.7);">Accueil</a></li>
          <li><a href="searchCommentaires.php" style="color:rgba(255,255,255,.7);">Commentaires</a></li>
          <li><a href="../../View/BackOffice/dashboard.php" style="color:rgba(255,255,255,.7);">Administration</a></li>
        </ul>
      </div>
      <div class="col-md-4 footer-col">
        <h4>Contact</h4>
        <p style="color:rgba(255,255,255,.7);"><i class="fa fa-envelope me-2"></i> contact@smartfood.tn</p>
      </div>
    </div>
    <div class="footer-info">
      <p>&copy; <?php echo date('Y'); ?> SmartFood — Tous droits réservés.</p>
    </div>
  </div>
</section>

<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
</body>
</html>

