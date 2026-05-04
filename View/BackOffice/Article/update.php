<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../Controller/ArticleController.php';


$articleController = new ArticleController($pdo);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    header('Location: list.php'); exit;
}
$id      = (int)$_GET['id'];
$article = $articleController->getById($id);
if (!$article) { header('Location: list.php'); exit; }

$error    = '';
$formData = [
    'title'   => $article['title'], 
    'content' => $article['content'],
    'status'  => $article['status'],
    'tags'    => $article['tags']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title'   => trim($_POST['title']   ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'status'  => $_POST['status']  ?? 'published',
        'tags'    => trim($_POST['tags']    ?? ''),
    ];
    $result = $articleController->update($id, $formData);
    if ($result['success']) {
        header('Location: list.php?success=updated'); exit;
    }
    $error = $result['message'];
}

$pageTitle  = 'Modifier l\'article';
$activeMenu = 'article-list';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-pencil-alt" style="color:#ffc107;margin-right:8px;"></i>Modifier l'article</h1>
    <div class="breadcrumb-sf">Admin → <a href="list.php">Articles</a> → Modifier #<?php echo $id; ?></div>
  </div>
  <a href="list.php" class="btn btn-secondary btn-sm" style="border-radius:50px;padding:8px 20px;">← Retour</a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger" style="border-radius:10px;">❌ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="background:white;border-radius:12px;padding:32px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
  <form method="POST" id="updateForm" novalidate>

    <div class="mb-3">
      <label class="form-label fw-semibold">Titre <span style="color:#dc3545;">*</span></label>
      <input type="text" class="form-control" id="title" name="title"
             value="<?php echo htmlspecialchars($formData['title']); ?>"
             style="border-radius:8px;">
      <div class="text-danger small mt-1" id="err-title" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Contenu <span style="color:#dc3545;">*</span></label>
      <textarea class="form-control" id="content" name="content" rows="12"
                style="border-radius:8px;line-height:1.7;"><?php echo htmlspecialchars($formData['content']); ?></textarea>
      <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
      <div class="text-end text-muted" style="font-size:.78rem;margin-top:4px;" id="wordCount">0 mot(s)</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Statut de publication</label>
        <select name="status" class="form-select" style="border-radius:8px;">
          <option value="published" <?php echo $formData['status'] === 'published' ? 'selected' : ''; ?>>Publié (visible sur le site)</option>
          <option value="draft" <?php echo $formData['status'] === 'draft' ? 'selected' : ''; ?>>Brouillon (masqué)</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Étiquettes (Tags)</label>
        <input type="text" name="tags" class="form-control" placeholder="Ex: Santé, Recette, Vegan"
               value="<?php echo htmlspecialchars($formData['tags']); ?>" style="border-radius:8px;">
        <div class="text-muted small mt-1">Séparez les tags par des virgules.</div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="button" class="btn btn-warning" onclick="validateAndSubmit()" style="border-radius:50px;padding:10px 28px;">
        <i class="fas fa-save"></i> Mettre à jour
      </button>
      <a href="list.php" class="btn btn-outline-secondary" style="border-radius:50px;padding:10px 20px;">Annuler</a>
    </div>

  </form>
</div>

<script>
document.getElementById('content').addEventListener('input', function() {
  var w = this.value.trim() ? this.value.trim().split(/\s+/).length : 0;
  document.getElementById('wordCount').textContent = w + ' mot(s)';
  if (w >= 2) clearErr('content');
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

  if (title === '')          { showErr('title',   'Le titre est obligatoire.');     ok = false; }
  else if (title.length < 3) { showErr('title',   'Minimum 3 caractères requis.'); ok = false; }
  else                         clearErr('title');

  if (content === '')           { showErr('content', 'Le contenu est obligatoire.');    ok = false; }
  else if (content.length < 10) { showErr('content', 'Minimum 10 caractères requis.'); ok = false; }
  else                            clearErr('content');

  if (ok) document.getElementById('updateForm').submit();
}

window.addEventListener('load', function() {
  var c = document.getElementById('content').value.trim();
  if (c) document.getElementById('wordCount').textContent = c.split(/\s+/).length + ' mot(s)';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

