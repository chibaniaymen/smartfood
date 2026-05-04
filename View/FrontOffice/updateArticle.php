<?php
require_once __DIR__ . '/../../config.php';

require_once __DIR__ . '/../../Controller/ArticleController.php';


$articleController = new ArticleController($pdo);

$id = $_GET['id'] ?? null;
if (!$id || !isValidId($id)) {
    header('Location: index.php'); exit;
}
$id      = (int)$id;
$article = $articleController->getById($id);
if (!$article) { header('Location: index.php'); exit; }

$error    = '';
$formData = ['title' => $article['title'], 'content' => $article['content']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title'   => trim($_POST['title']   ?? ''),
        'content' => trim($_POST['content'] ?? ''),
    ];
    $result = $articleController->update($id, $formData);
    if ($result['success']) {
        header('Location: index.php?success=article_updated');
        exit;
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Modifier l'article — SmartFood</title>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/style.css"/>
  <style>
    body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
    .nav-container { background: white; padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
    .form-box { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 25px rgba(0,0,0,.08); margin-top: 50px; }
    .btn-orange { background: #f7941d; color: white; border-radius: 50px; padding: 12px 30px; border: none; font-weight: 700; transition: .3s; }
    .btn-orange:hover { background: #e07b10; transform: translateY(-2px); }
    .form-label { font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
    .form-control { border-radius: 10px; border: 1.5px solid #e9ecef; padding: 12px; }
    .form-control:focus { border-color: #f7941d; box-shadow: none; }
  </style>
</head>
<body>

<div class="nav-container text-center">
  <a class="navbar-brand" href="index.php" style="font-size:1.8rem; font-weight:800; color:#2D6A4F; text-decoration:none;">
    Smart<span style="color:#f7941d;">Food</span>
  </a>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="form-box">
        <h2 class="text-center mb-4" style="font-weight:800;">✏️ Modifier l'article</h2>

        <?php if ($error): ?>
          <div class="alert alert-danger" style="border-radius:12px;">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="userUpdateForm" novalidate>
          <div class="mb-4">
            <label class="form-label">Titre de l'article</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($formData['title']); ?>">
            <div class="text-danger small mt-1" id="err-title" style="display:none;"></div>
          </div>

          <div class="mb-4">
            <label class="form-label">Contenu</label>
            <textarea class="form-control" id="content" name="content" rows="10"><?php echo htmlspecialchars($formData['content']); ?></textarea>
            <div class="text-danger small mt-1" id="err-content" style="display:none;"></div>
          </div>

          <div class="d-flex gap-3 justify-content-center mt-4">
            <button type="button" class="btn-orange" onclick="validateAndSubmit()">Enregistrer les modifications</button>
            <a href="index.php" class="btn btn-outline-secondary" style="border-radius:50px; padding:12px 30px;">Annuler</a>
          </div>
        </form>
      </div>
    </div>
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
  var title = document.getElementById('title').value.trim();
  var content = document.getElementById('content').value.trim();

  if (title === '') { showErr('title', 'Le titre est obligatoire.'); ok = false; }
  else if (title.length < 3) { showErr('title', 'Minimum 3 caractères requis.'); ok = false; }
  else clearErr('title');

  if (content === '') { showErr('content', 'Le contenu est obligatoire.'); ok = false; }
  else if (content.length < 10) { showErr('content', 'Minimum 10 caractères requis.'); ok = false; }
  else clearErr('content');

  if (ok) document.getElementById('userUpdateForm').submit();
}

document.getElementById('title').addEventListener('input', function() { if (this.value.trim().length >= 3) clearErr('title'); });
document.getElementById('content').addEventListener('input', function() { if (this.value.trim().length >= 10) clearErr('content'); });
</script>

</body>
</html>

