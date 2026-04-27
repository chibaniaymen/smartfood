<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Model/Commentaire.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$commentaireModel      = new Commentaire($pdo);
$commentaireController = new CommentaireController($commentaireModel);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) {
    header('Location: list.php'); exit;
}
$id = (int)$_GET['id'];
$commentaire = $commentaireController->getById($id);
if (!$commentaire) { header('Location: list.php'); exit; }

$error    = '';
$formData = ['author' => $commentaire['author'], 'content' => $commentaire['content']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'author'  => trim($_POST['author']  ?? ''),
        'content' => trim($_POST['content'] ?? ''),
    ];
    
    $result = $commentaireController->update($id, $formData);
    if ($result['success']) {
        header('Location: list.php?success=updated');
        exit;
    }
    $error = $result['message'];
}

$pageTitle  = 'Modifier le commentaire';
$activeMenu = 'comment-list';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header-sf">
  <div>
    <h1><i class="fas fa-pencil-alt" style="color:#ffc107;margin-right:8px;"></i>Modifier le commentaire</h1>
    <div class="breadcrumb-sf">Admin → <a href="list.php">Commentaires</a> → Modifier #<?php echo $id; ?></div>
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
  <form method="POST" id="updateForm" novalidate>

    <div class="mb-3">
      <label class="form-label fw-semibold">Auteur <span style="color:#dc3545;">*</span></label>
      <input type="text" class="form-control" id="author" name="author"
             value="<?php echo htmlspecialchars($formData['author']); ?>"
             style="border-radius:8px;">
      <div class="text-danger small mt-1" id="err-author" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Commentaire <span style="color:#dc3545;">*</span></label>
      <textarea class="form-control" id="content" name="content" rows="6"
                style="border-radius:8px;line-height:1.7;"><?php echo htmlspecialchars($formData['content']); ?></textarea>
      <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
      <div class="text-end text-muted" style="font-size:.78rem;margin-top:4px;" id="charCount">0 / 1000</div>
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
  var len = this.value.length;
  document.getElementById('charCount').textContent = len + ' / 1000';
  if (len >= 5) clearErr('content');
});
document.getElementById('author').addEventListener('input', function() {
  if (this.value.trim().length >= 2) clearErr('author');
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
  var author  = document.getElementById('author').value.trim();
  var content = document.getElementById('content').value.trim();

  if (author === '')          { showErr('author', 'Le nom est obligatoire.'); ok = false; }
  else if (author.length < 2) { showErr('author', 'Minimum 2 caractères requis.'); ok = false; }
  else if (author.length > 100) { showErr('author', 'Maximum 100 caractères.'); ok = false; }
  else                          clearErr('author');

  if (content === '')           { showErr('content', 'Le contenu est obligatoire.'); ok = false; }
  else if (content.length < 5)  { showErr('content', 'Minimum 5 caractères requis.'); ok = false; }
  else if (content.length > 1000) { showErr('content', 'Maximum 1000 caractères.'); ok = false; }
  else                            clearErr('content');

  if (ok) document.getElementById('updateForm').submit();
  else document.querySelector('[style*="dc3545"]')?.scrollIntoView({behavior:'smooth',block:'center'});
}

window.addEventListener('load', function() {
  var c = document.getElementById('content').value;
  if (c.length > 0) document.getElementById('charCount').textContent = c.length + ' / 1000';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
