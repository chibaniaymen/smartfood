<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Article.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';

$articleModel      = new Article($pdo);
$articleController = new ArticleController($articleModel);

$error    = '';
$formData = ['title' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title'   => trim($_POST['title']   ?? ''),
        'content' => trim($_POST['content'] ?? ''),
    ];
    $result = $articleController->create($formData);
    if ($result['success']) {
        header('Location: list.php?success=created');
        exit;
    }
    $error = $result['message'];
}

$pageTitle  = 'Nouvel article';
$activeMenu = 'article-add';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-plus-circle" style="color:#2D6A4F;margin-right:8px;"></i>Nouvel article</h1>
    <div class="breadcrumb-sf">Admin → <a href="list.php">Articles</a> → Ajouter</div>
  </div>
  <a href="list.php" class="btn btn-secondary btn-sm" style="border-radius:50px;padding:8px 20px;">
    ← Retour à la liste
  </a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger" style="border-radius:10px;">❌ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="background:white;border-radius:12px;padding:32px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
  <form method="POST" id="addForm" novalidate>

    <div class="mb-3">
      <label class="form-label fw-semibold">Titre <span style="color:#dc3545;">*</span></label>
      <input type="text" class="form-control" id="title" name="title"
             placeholder="Ex : 10 superaliments à intégrer dès demain"
             value="<?php echo htmlspecialchars($formData['title']); ?>"
             style="border-radius:8px;">
      <div class="text-danger small mt-1" id="err-title" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Contenu <span style="color:#dc3545;">*</span></label>
      <textarea class="form-control" id="content" name="content" rows="12"
                placeholder="Rédigez votre article ici…"
                style="border-radius:8px;line-height:1.7;"><?php echo htmlspecialchars($formData['content']); ?></textarea>
      <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
      <div class="text-end text-muted" style="font-size:.78rem;margin-top:4px;" id="wordCount">0 mot(s)</div>
    </div>

    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" onclick="validateAndSubmit()" style="border-radius:50px;padding:10px 28px;">
        <i class="fas fa-check"></i> Créer l'article
      </button>
      <a href="list.php" class="btn btn-outline-secondary" style="border-radius:50px;padding:10px 20px;">Annuler</a>
    </div>

  </form>
</div>

<script>
document.getElementById('content').addEventListener('input', function() {
  var words = this.value.trim() ? this.value.trim().split(/\s+/).length : 0;
  document.getElementById('wordCount').textContent = words + ' mot(s)';
  if (words >= 2) clearErr('content');
});
document.getElementById('title').addEventListener('input', function() {
  if (this.value.trim().length >= 3) clearErr('title');
});

function showErr(id, msg) {
  document.getElementById(id).style.borderColor = '#dc3545';
  var e = document.getElementById('err-' + id);
  e.textContent = msg; e.style.display = 'block';
}
function clearErr(id) {
  document.getElementById(id).style.borderColor = '';
  document.getElementById('err-' + id).style.display = 'none';
}
function validateAndSubmit() {
  var ok = true;
  var title   = document.getElementById('title').value.trim();
  var content = document.getElementById('content').value.trim();

  if (title === '')          { showErr('title',   'Le titre est obligatoire.');         ok = false; }
  else if (title.length < 3) { showErr('title',   'Minimum 3 caractères requis.');      ok = false; }
  else if (title.length>255) { showErr('title',   'Maximum 255 caractères.');           ok = false; }
  else                         clearErr('title');

  if (content === '')           { showErr('content', 'Le contenu est obligatoire.');       ok = false; }
  else if (content.length < 10) { showErr('content', 'Minimum 10 caractères requis.');     ok = false; }
  else                            clearErr('content');

  if (ok) document.getElementById('addForm').submit();
  else document.querySelector('[style*="dc3545"]')?.scrollIntoView({behavior:'smooth',block:'center'});
}

window.addEventListener('load', function() {
  var c = document.getElementById('content').value.trim();
  if (c) document.getElementById('wordCount').textContent = c.split(/\s+/).length + ' mot(s)';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
