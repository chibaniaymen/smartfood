<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Article.php';
require_once __DIR__ . '/../../Model/Commentaire.php';
require_once __DIR__ . '/../../Controller/ArticleController.php';
require_once __DIR__ . '/../../Controller/CommentaireController.php';

$articleModel          = new Article($pdo);
$commentaireModel      = new Commentaire($pdo);
$articleController     = new ArticleController($articleModel);
$commentaireController = new CommentaireController($commentaireModel);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) redirect('index.php');
$articleId = (int)$_GET['id'];
$article   = $articleController->getById($articleId);
if (!$article) redirect('index.php');

$comments    = $commentaireController->getByArticle($articleId);
$allArticles = $articleController->getAll();
$related     = array_slice(
    array_values(array_filter($allArticles, fn($a) => $a['id'] != $articleId)),
    0, 3
);

$errors   = [];
$success  = '';
$formData = ['author' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $formData['author']  = trim($_POST['author']  ?? '');
    $formData['content'] = trim($_POST['content'] ?? '');
    $result = $commentaireController->create($articleId, $formData);
    if ($result['success']) {
        $success  = $result['message'];
        $formData = ['author' => '', 'content' => ''];
        $comments = $commentaireController->getByArticle($articleId);
    } else {
        $errors['general'] = $result['message'];
    }
}

$readTime = max(1, ceil(str_word_count($article['content']) / 200));
$icons    = ['🥦','🍎','🥑','🫐','🌿','🍋','🥕','🫚'];
$icon     = $icons[$article['id'] % count($icons)];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo h($article['title']); ?> — SmartFood</title>
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
    .nav-back:hover { border-color: var(--green); color: var(--green); text-decoration: none; }

    /* HERO */
    .article-hero { background: linear-gradient(135deg, var(--green), #1B4332); padding: 60px 40px; display: flex; align-items: center; gap: 40px; }
    .article-hero-icon { font-size: 6rem; flex-shrink: 0; }
    .article-hero-content h1 { font-family: var(--ff-head); font-size: 2.2rem; color: white; line-height: 1.25; margin-bottom: 16px; }
    .hero-meta { display: flex; gap: 20px; font-size: .82rem; color: rgba(255,255,255,.75); }
    .hero-meta i { margin-right: 5px; }

    /* LAYOUT */
    .article-layout { max-width: 1100px; margin: 0 auto; padding: 48px 24px 80px; display: grid; grid-template-columns: 1fr 300px; gap: 40px; }

    /* BODY */
    .article-body { background: white; border-radius: var(--radius); padding: 48px 52px; box-shadow: 0 2px 20px rgba(0,0,0,.06); }
    .article-lead { font-size: 1.1rem; color: #495057; line-height: 1.8; padding-bottom: 24px; margin-bottom: 24px; border-bottom: 2px solid var(--border); font-style: italic; }
    .article-content { font-size: 1rem; line-height: 1.85; color: #3d3d3d; white-space: pre-wrap; }
    .share-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 20px 0; margin: 32px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
    .share-bar span { font-size: .85rem; font-weight: 600; }
    .share-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 50px; font-size: .8rem; font-weight: 500; text-decoration: none; }
    .share-btn.tw { background: #1DA1F2; color: white; }
    .share-btn.fb { background: #1877F2; color: white; }

    /* READING PROGRESS */
    .reading-progress { position: fixed; top: 68px; left: 0; right: 0; height: 3px; background: var(--border); z-index: 99; }
    .reading-progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, var(--green), var(--orange)); transition: width .1s; }

    /* COMMENTS */
    .comments-section { margin-top: 40px; }
    .section-heading { font-family: var(--ff-head); font-size: 1.5rem; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .section-heading::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .comment-card { display: flex; gap: 16px; margin-bottom: 20px; }
    .comment-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, var(--green-lt), var(--green)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: .9rem; flex-shrink: 0; text-transform: uppercase; }
    .comment-bubble { flex: 1; background: #f8f9fa; border-radius: 0 12px 12px 12px; padding: 14px 18px; }
    .comment-author { font-weight: 600; font-size: .88rem; }
    .comment-date { font-size: .75rem; color: #adb5bd; margin-left: 10px; }
    .comment-text { font-size: .88rem; color: #495057; line-height: 1.65; margin-top: 6px; }
    .no-comments { text-align: center; padding: 32px; background: #f8f9fa; border-radius: var(--radius); color: var(--muted); }

    /* FORM */
    .comment-form-wrap { background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border: 1.5px solid #86efac; border-radius: var(--radius); padding: 28px 32px; margin-top: 32px; }
    .form-title { font-family: var(--ff-head); font-size: 1.2rem; color: var(--dark); margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: .82rem; font-weight: 600; }
    .form-control { padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: .9rem; font-family: var(--ff-body); transition: border-color .2s; width: 100%; background: white; }
    .form-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(45,106,79,.1); }
    .form-control.is-invalid { border-color: #dc3545; }
    .error-msg { color: #dc3545; font-size: .78rem; display: none; margin-top: 4px; }
    .error-msg.show { display: block; }
    .char-counter { font-size: .75rem; color: #adb5bd; text-align: right; margin-top: 4px; }
    .char-counter.warn { color: #fd7e14; }
    .char-counter.danger { color: #dc3545; }
    .btn-submit { background: var(--green); color: white; border: none; padding: 12px 32px; border-radius: 50px; font-size: .92rem; font-weight: 600; cursor: pointer; font-family: var(--ff-body); display: inline-flex; align-items: center; gap: 8px; transition: background .2s; }
    .btn-submit:hover { background: #1b4332; }
    .alert-success { background: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; padding: 12px 16px; border-radius: 10px; font-size: .88rem; margin-bottom: 16px; }
    .alert-danger  { background: #f8d7da; border: 1px solid #f5c2c7; color: #842029; padding: 12px 16px; border-radius: 10px; font-size: .88rem; margin-bottom: 16px; }

    /* SIDEBAR */
    .sidebar { display: flex; flex-direction: column; gap: 24px; }
    .sidebar-card { background: white; border-radius: var(--radius); padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
    .sidebar-title { font-family: var(--ff-head); font-size: 1.05rem; color: var(--dark); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--green); display: inline-block; }
    .related-card { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid var(--border); text-decoration: none; color: inherit; }
    .related-card:last-child { border-bottom: none; }
    .related-card:hover { color: var(--green); text-decoration: none; }
    .related-icon { width: 48px; height: 48px; border-radius: 8px; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .related-title { font-size: .82rem; font-weight: 600; line-height: 1.35; margin-bottom: 4px; }
    .related-date  { font-size: .72rem; color: #adb5bd; }

    /* FOOTER */
    .footer { background: var(--dark); color: rgba(255,255,255,.5); padding: 28px 40px; display: flex; justify-content: space-between; align-items: center; font-size: .82rem; }
    .footer a { color: rgba(255,255,255,.5); text-decoration: none; }
    .footer a:hover { color: white; }
    .footer-brand { font-family: var(--ff-head); font-size: 1.2rem; color: white; margin-bottom: 4px; }

    @media (max-width: 900px) {
      .article-layout { grid-template-columns: 1fr; }
      .article-hero { flex-direction: column; padding: 32px 24px; gap: 20px; }
      .article-hero-content h1 { font-size: 1.6rem; }
      .article-body { padding: 28px 24px; }
      .nav { padding: 0 16px; }
    }
  </style>
</head>
<body>

<div class="reading-progress">
  <div class="reading-progress-bar" id="progressBar"></div>
</div>

<nav class="navbar navbar-expand-lg custom_nav-container">
  <a class="navbar-brand" href="index.php" style="font-size:1.5rem;font-weight:800;">Smart<span style="color:#f7941d;">Food</span></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mx-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
      <li class="nav-item active"><a class="nav-link" href="index.php">Blog</a></li>
      <li class="nav-item"><a class="nav-link" href="searchCommentaires.php">Commentaires</a></li>
    </ul>
    <div class="user_option">
      <a href="index.php" class="order_online_btn" style="background:transparent;border:2px solid #fff;margin-right:10px;">
        <i class="fa fa-arrow-left me-1"></i> Retour
      </a>
      <a href="../../View/BackOffice/dashboard.php" class="order_online_btn">
        <i class="fa fa-cog me-1"></i> Admin
      </a>
    </div>
  </div>
</nav>

<div class="article-hero">
  <div class="article-hero-icon"><?php echo $icon; ?></div>
  <div class="article-hero-content">
    <h1><?php echo h($article['title']); ?></h1>
    <div class="hero-meta">
      <span><i class="fa fa-calendar-o"></i><?php echo date('d M Y', strtotime($article['created_at'])); ?></span>
      <span><i class="fa fa-clock-o"></i><?php echo $readTime; ?> min de lecture</span>
      <span><i class="fa fa-comments"></i><?php echo count($comments); ?> commentaire(s)</span>
    </div>
  </div>
</div>

<div class="article-layout">
  <main>
    <article class="article-body" id="articleBody">
      <p class="article-lead"><?php echo h(substr($article['content'], 0, 200)); ?>…</p>
      <div class="article-content"><?php echo nl2br(h(substr($article['content'], 200))); ?></div>

      <div class="share-bar">
        <span>Partager :</span>
        <a href="#" class="share-btn tw"><i class="fa fa-twitter"></i> Twitter</a>
        <a href="#" class="share-btn fb"><i class="fa fa-facebook"></i> Facebook</a>
      </div>
    </article>

    <!-- COMMENTAIRES -->
    <div class="comments-section">
      <h2 class="section-heading">
        <i class="fa fa-comments" style="color:var(--green);"></i>
        Commentaires (<?php echo count($comments); ?>)
      </h2>

      <?php if (empty($comments)): ?>
        <div class="no-comments">💬 Soyez le premier à commenter cet article !</div>
      <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-card">
          <div class="comment-avatar"><?php echo mb_substr($comment['author'], 0, 2); ?></div>
          <div class="comment-bubble">
            <span class="comment-author"><?php echo h($comment['author']); ?></span>
            <span class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
            <p class="comment-text"><?php echo nl2br(h($comment['content'])); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- FORMULAIRE -->
    <div class="comment-form-wrap">
      <div class="form-title">✍️ Laisser un commentaire</div>

      <?php if ($success): ?>
        <div class="alert-success">✅ <?php echo h($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors['general'])): ?>
        <div class="alert-danger">❌ <?php echo h($errors['general']); ?></div>
      <?php endif; ?>

      <form method="POST" id="commentForm" novalidate>
        <input type="hidden" name="add_comment" value="1"/>
        <div class="form-group">
          <label for="author">Votre nom *</label>
          <input type="text" id="author" name="author" class="form-control"
                 placeholder="Marie Dupont"
                 value="<?php echo h($formData['author']); ?>"/>
          <div class="error-msg" id="err-author"></div>
        </div>
        <div class="form-group">
          <label for="content">Votre commentaire *</label>
          <textarea id="content" name="content" class="form-control" rows="4"
                    placeholder="Partagez votre avis…"
                    maxlength="1000"><?php echo h($formData['content']); ?></textarea>
          <div class="char-counter" id="charCount">0 / 1000</div>
          <div class="error-msg" id="err-content"></div>
        </div>
        <button type="button" class="btn-submit" onclick="validateAndSubmit()">
          <i class="fa fa-paper-plane"></i> Publier
        </button>
      </form>
    </div>
  </main>

  <aside class="sidebar">

    <?php if (!empty($related)): ?>
    <div class="sidebar-card">
      <div class="sidebar-title">📌 À lire aussi</div>
      <?php foreach ($related as $rel): ?>
      <?php $relIcon = $icons[$rel['id'] % count($icons)]; ?>
      <a href="article.php?id=<?php echo $rel['id']; ?>" class="related-card">
        <div class="related-icon"><?php echo $relIcon; ?></div>
        <div>
          <div class="related-title"><?php echo h(mb_substr($rel['title'], 0, 50)); ?>…</div>
          <div class="related-date"><?php echo date('d/m/Y', strtotime($rel['created_at'])); ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="sidebar-card" style="text-align:center;">
      <div style="font-size:2.5rem;margin-bottom:12px;">🌿</div>
      <div style="font-family:var(--ff-head);font-size:1.05rem;margin-bottom:6px;">Équipe SmartFood</div>
      <p style="font-size:.8rem;color:var(--muted);line-height:1.6;">Nutritionnistes passionnés, nous partageons des conseils fondés sur la science pour mieux manger au quotidien.</p>
    </div>

    <div class="sidebar-card" style="background:linear-gradient(135deg,#fff8f0,#fff3e0);border-left:4px solid var(--orange);">
      <p style="font-family:var(--ff-head);font-size:1rem;font-style:italic;color:var(--dark);line-height:1.6;">"Que ton aliment soit ta première médecine."</p>
      <p style="font-size:.78rem;color:var(--muted);margin-top:8px;">— Hippocrate</p>
    </div>

  </aside>
</div>

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
          <li><a href="index.php" style="color:rgba(255,255,255,.7);">Blog</a></li>
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

<script>
window.addEventListener('scroll', function() {
  var body = document.getElementById('articleBody');
  if (!body) return;
  var pct = Math.min(100, Math.max(0, (-body.getBoundingClientRect().top / body.offsetHeight) * 100));
  document.getElementById('progressBar').style.width = pct + '%';
});

var contentEl = document.getElementById('content');
var charEl    = document.getElementById('charCount');

contentEl.addEventListener('input', function() {
  var len = this.value.length;
  charEl.textContent = len + ' / 1000';
  charEl.className = 'char-counter' + (len > 900 ? ' danger' : len > 700 ? ' warn' : '');
  if (len >= 5) clearErr('content');
});
document.getElementById('author').addEventListener('input', function() {
  if (this.value.trim().length >= 2) clearErr('author');
});

function showErr(id, msg) {
  var el = document.getElementById(id);
  var err = document.getElementById('err-' + id);
  el.classList.add('is-invalid');
  err.textContent = msg; err.classList.add('show');
}
function clearErr(id) {
  document.getElementById(id).classList.remove('is-invalid');
  document.getElementById('err-' + id).classList.remove('show');
}
function validateAndSubmit() {
  var ok = true;
  var author  = document.getElementById('author').value.trim();
  var content = document.getElementById('content').value.trim();
  if (author === '')         { showErr('author',  'Le nom est obligatoire.');           ok = false; }
  else if (author.length<2)  { showErr('author',  'Minimum 2 caractères.');             ok = false; }
  else if (author.length>100){ showErr('author',  'Maximum 100 caractères.');           ok = false; }
  else clearErr('author');
  if (content === '')          { showErr('content', 'Le commentaire est obligatoire.');   ok = false; }
  else if (content.length<5)   { showErr('content', 'Minimum 5 caractères.');            ok = false; }
  else if (content.length>1000){ showErr('content', 'Maximum 1000 caractères.');         ok = false; }
  else clearErr('content');
  if (ok) document.getElementById('commentForm').submit();
  else document.querySelector('.is-invalid')?.scrollIntoView({behavior:'smooth',block:'center'});
}
window.addEventListener('load', function() {
  var len = contentEl.value.length;
  if (len) charEl.textContent = len + ' / 1000';
});
</script>
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
