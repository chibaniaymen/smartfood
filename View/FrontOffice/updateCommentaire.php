<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/Commentaire.php';
require_once __DIR__ . '/../../Controller/CommentaireController.php';

$commentaireModel      = new Commentaire($pdo);
$commentaireController = new CommentaireController($commentaireModel);

$id = $_GET['id'] ?? null;
if (!$id || !isValidId((int)$id)) {
    header('Location: searchCommentaires.php');
    exit;
}

$comment = $commentaireController->getById((int)$id);
if (!$comment) {
    header('Location: searchCommentaires.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author  = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    $result = $commentaireController->update($id, [
        'author'     => $author,
        'content'    => $content,
        'article_id' => $comment['article_id'] // On garde le même article
    ]);
    
    if ($result['success']) {
        header('Location: searchCommentaires.php?success=updated');
        exit;
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Modifier un commentaire — SmartFood</title>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <style>
    body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
    .nav { background: white; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
    .form-container { max-width: 600px; margin: 60px auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .btn-green { background: #2D6A4F; color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 600; width: 100%; transition: .3s; }
    .btn-green:hover { background: #1b4332; }
    .form-control { border-radius: 8px; padding: 10px 15px; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg nav">
  <a class="navbar-brand" href="index.php" style="font-size:1.5rem;font-weight:800;color:#2D6A4F;">Smart<span style="color:#f7941d;">Food</span></a>
</nav>

<div class="container">
  <div class="form-container">
    <h2 style="font-weight:800;color:#1a1a2e;margin-bottom:24px;">✏️ Modifier le commentaire</h2>
    
    <?php if ($error): ?>
      <div class="alert alert-danger" style="border-radius:10px;">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" id="updateForm" novalidate>
      <div class="mb-3">
        <label class="form-label fw-bold">Auteur</label>
        <input type="text" name="author" id="author" class="form-control" value="<?php echo htmlspecialchars($_POST['author'] ?? $comment['author']); ?>">
        <div class="text-danger small mt-1" id="err-author" style="display:none;"></div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-bold">Commentaire</label>
        <textarea name="content" id="content" class="form-control" rows="5"><?php echo htmlspecialchars($_POST['content'] ?? $comment['content']); ?></textarea>
        <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
      </div>
      
      <div class="d-flex gap-2">
        <button type="button" class="btn-green" onclick="validateAndSubmit()">Enregistrer les modifications</button>
        <a href="searchCommentaires.php" class="btn btn-outline-secondary w-100" style="border-radius:50px;padding:12px 24px;font-weight:600;">Annuler</a>
      </div>
    </form>
  </div>
</div>

<script>
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
}

document.getElementById('author').addEventListener('input', function() { if (this.value.trim().length >= 2) clearErr('author'); });
document.getElementById('content').addEventListener('input', function() { if (this.value.trim().length >= 5) clearErr('content'); });
</script>

</body>
</html>
