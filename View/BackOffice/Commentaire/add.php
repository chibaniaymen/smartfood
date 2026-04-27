<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Commentaire.php';
require_once __DIR__ . '/../../../Model/Article.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';

$commentaireModel      = new Commentaire($pdo);
$articleModel          = new Article($pdo);
$commentaireController = new CommentaireController($commentaireModel);
$articleController     = new ArticleController($articleModel);

$articles = $articleController->getAll();

$error    = '';
$formData = ['article_id' => '', 'author' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'article_id' => $_POST['article_id'] ?? '',
        'author'     => trim($_POST['author']   ?? ''),
        'content'    => trim($_POST['content'] ?? ''),
    ];
    
    $articleId = (int)$formData['article_id'];
    
    $result = $commentaireController->create($articleId, [
        'author'  => $formData['author'],
        'content' => $formData['content']
    ]);
    
    if ($result['success']) {
        header('Location: list.php?success=created');
        exit;
    }
    $error = $result['message'];
}

$pageTitle  = 'Nouveau commentaire';
$activeMenu = 'comment-list';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-plus-circle" style="color:#E76F51;margin-right:8px;"></i>Nouveau commentaire</h1>
    <div class="breadcrumb-sf">Admin → <a href="list.php">Commentaires</a> → Ajouter</div>
  </div>
  <a href="list.php" class="btn btn-secondary btn-sm" style="border-radius:50px;padding:8px 20px;">
    ← Retour à la liste
  </a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger" style="border-radius:10px;">❌ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="background:white;border-radius:12px;padding:32px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
  <!-- novalidate pour désactiver les contrôles HTML5 -->
  <form method="POST" id="addForm" novalidate>

    <div class="mb-3">
      <label class="form-label fw-semibold">Article <span style="color:#dc3545;">*</span></label>
      <select class="form-select" id="article_id" name="article_id" style="border-radius:8px;">
        <option value="">-- Sélectionnez un article --</option>
        <?php foreach ($articles as $a): ?>
          <option value="<?php echo $a['id']; ?>" <?php echo ($formData['article_id'] == $a['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars(mb_substr($a['title'], 0, 60)); ?>…
          </option>
        <?php endforeach; ?>
      </select>
      <div class="text-danger small mt-1" id="err-article" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Auteur <span style="color:#dc3545;">*</span></label>
      <input type="text" class="form-control" id="author" name="author"
             placeholder="Nom de l'auteur"
             value="<?php echo htmlspecialchars($formData['author']); ?>"
             style="border-radius:8px;">
      <div class="text-danger small mt-1" id="err-author" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Commentaire <span style="color:#dc3545;">*</span></label>
      <textarea class="form-control" id="content" name="content" rows="6"
                placeholder="Contenu du commentaire…"
                style="border-radius:8px;line-height:1.7;"><?php echo htmlspecialchars($formData['content']); ?></textarea>
      <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
      <div class="text-end text-muted" style="font-size:.78rem;margin-top:4px;" id="charCount">0 / 1000</div>
    </div>

    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" onclick="validateAndSubmit()" style="background:#E76F51;border:none;border-radius:50px;padding:10px 28px;">
        <i class="fas fa-check"></i> Créer le commentaire
      </button>
      <a href="list.php" class="btn btn-outline-secondary" style="border-radius:50px;padding:10px 20px;">Annuler</a>
    </div>

  </form>
</div>

<script>
document.getElementById('content').addEventListener('input', function() {
  var len = this.value.length;
  document.getElementById('charCount').textContent = len + ' / 1000';
  if (len >= 5) clearErr('content');
});
document.getElementById('author').addEventListener('input', function() {
  if (this.value.trim().length >= 2) clearErr('author');
});
document.getElementById('article_id').addEventListener('change', function() {
  if (this.value !== '') clearErr('article');
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
  var article = document.getElementById('article_id').value;
  var author  = document.getElementById('author').value.trim();
  var content = document.getElementById('content').value.trim();

  if (article === '') { showErr('article', 'Veuillez sélectionner un article.'); ok = false; }
  else { clearErr('article'); }

  if (author === '')          { showErr('author', 'Le nom est obligatoire.'); ok = false; }
  else if (author.length < 2) { showErr('author', 'Minimum 2 caractères requis.'); ok = false; }
  else if (author.length > 100) { showErr('author', 'Maximum 100 caractères.'); ok = false; }
  else                          clearErr('author');

  if (content === '')           { showErr('content', 'Le contenu est obligatoire.'); ok = false; }
  else if (content.length < 5)  { showErr('content', 'Minimum 5 caractères requis.'); ok = false; }
  else if (content.length > 1000) { showErr('content', 'Maximum 1000 caractères.'); ok = false; }
  else                            clearErr('content');

  if (ok) document.getElementById('addForm').submit();
  else document.querySelector('[style*="dc3545"]')?.scrollIntoView({behavior:'smooth',block:'center'});
}

window.addEventListener('load', function() {
  var c = document.getElementById('content').value;
  if (c.length > 0) document.getElementById('charCount').textContent = c.length + ' / 1000';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
