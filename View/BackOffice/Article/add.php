<?php
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../Controller/ArticleController.php';


$articleController = new ArticleController($pdo);

$error    = '';
$formData = ['title' => '', 'content' => '', 'status' => 'published', 'tags' => '', 'publish_mode' => 'now', 'scheduled_at' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publishMode  = $_POST['publish_mode'] ?? 'now';
    $scheduledAt  = trim($_POST['scheduled_at'] ?? '');

    // Dériver le statut et la date de publication
    if ($publishMode === 'draft') {
        $status       = 'draft';
        $publishedAt  = null;
    } elseif ($publishMode === 'scheduled' && !empty($scheduledAt)) {
        $scheduledDateTime = new DateTime($scheduledAt);
        $now               = new DateTime();
        // Si la date planifiée est dans le futur → brouillon avec date; si passée → publié directement
        $status      = $scheduledDateTime > $now ? 'draft' : 'published';
        $publishedAt = $scheduledDateTime->format('Y-m-d H:i:s');
    } else {
        $status      = 'published';
        $publishedAt = (new DateTime())->format('Y-m-d H:i:s');
    }

    $formData = [
        'title'        => trim($_POST['title']   ?? ''),
        'content'      => trim($_POST['content'] ?? ''),
        'status'       => $status,
        'tags'         => trim($_POST['tags']    ?? ''),
        'published_at' => $publishedAt,
        'publish_mode' => $publishMode,
        'scheduled_at' => $scheduledAt,
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

    <!-- Planning de Publication -->
    <div class="mb-4" style="background:#f8f9fa;border-radius:12px;padding:20px;border:1px solid #e9ecef;">
      <label class="form-label fw-semibold d-flex align-items-center gap-2" style="font-size:1rem;">
        <i class="fas fa-calendar-alt" style="color:#2D6A4F;"></i> Planification de la publication
      </label>
      <p class="text-muted small mb-3">Choisissez quand cet article sera visible sur le site.</p>

      <div class="d-flex flex-wrap gap-3 mb-3" id="publish-mode-selector">
        <label class="mode-btn" for="mode-now">
          <input type="radio" name="publish_mode" id="mode-now" value="now"
            <?php echo ($formData['publish_mode'] === 'now') ? 'checked' : ''; ?>
            onchange="updateScheduleUI()">
          <span><i class="fas fa-bolt"></i> Publier maintenant</span>
        </label>
        <label class="mode-btn" for="mode-scheduled">
          <input type="radio" name="publish_mode" id="mode-scheduled" value="scheduled"
            <?php echo ($formData['publish_mode'] === 'scheduled') ? 'checked' : ''; ?>
            onchange="updateScheduleUI()">
          <span><i class="fas fa-clock"></i> Planifier</span>
        </label>
        <label class="mode-btn" for="mode-draft">
          <input type="radio" name="publish_mode" id="mode-draft" value="draft"
            <?php echo ($formData['publish_mode'] === 'draft') ? 'checked' : ''; ?>
            onchange="updateScheduleUI()">
          <span><i class="fas fa-file-alt"></i> Brouillon</span>
        </label>
      </div>

      <div id="schedule-picker" style="display:none;">
        <label class="form-label small fw-semibold">Date et heure de publication</label>
        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control"
               style="border-radius:8px; max-width:280px;"
               value="<?php echo htmlspecialchars($formData['scheduled_at']); ?>"
               min="<?php echo date('Y-m-d\TH:i'); ?>">
        <div class="text-muted small mt-2">
          <i class="fas fa-info-circle"></i> L'article sera automatiquement publié à la date choisie.
        </div>
      </div>

      <div id="schedule-preview" class="mt-2 small"></div>
    </div>

    <style>
      .mode-btn { cursor:pointer; user-select:none; }
      .mode-btn input[type=radio] { display:none; }
      .mode-btn span {
        display:inline-flex; align-items:center; gap:7px;
        padding:8px 18px; border-radius:30px; font-size:.88rem; font-weight:600;
        border:2px solid #dee2e6; color:#6c757d; background:#fff;
        transition:all .2s ease;
      }
      .mode-btn input:checked + span {
        border-color:#2D6A4F; color:#2D6A4F; background:#f0f7f4;
      }
    </style>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Étiquettes (Tags)</label>
        <input type="text" name="tags" class="form-control" placeholder="Ex: Santé, Recette, Vegan"
               value="<?php echo htmlspecialchars($formData['tags']); ?>" style="border-radius:8px;">
        <div class="text-muted small mt-1">Séparez les tags par des virgules.</div>
      </div>
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
function updateScheduleUI() {
  const mode = document.querySelector('input[name="publish_mode"]:checked')?.value;
  const picker = document.getElementById('schedule-picker');
  const preview = document.getElementById('schedule-preview');
  picker.style.display = (mode === 'scheduled') ? 'block' : 'none';

  if (mode === 'now') {
    preview.innerHTML = '<span style="color:#2D6A4F;"><i class="fas fa-check-circle"></i> L\'article sera visible immédiatement après création.</span>';
  } else if (mode === 'draft') {
    preview.innerHTML = '<span style="color:#6c757d;"><i class="fas fa-eye-slash"></i> L\'article sera sauvegardé en brouillon, invisible sur le site.</span>';
  } else {
    preview.innerHTML = '';
  }
}
document.getElementById('scheduled_at')?.addEventListener('change', function() {
  if (this.value) {
    const d = new Date(this.value);
    const preview = document.getElementById('schedule-preview');
    preview.innerHTML = `<span style="color:#f0a500;"><i class="fas fa-calendar-check"></i> Publication prévue le : <strong>${d.toLocaleString('fr-FR')}</strong></span>`;
  }
});
window.addEventListener('DOMContentLoaded', updateScheduleUI);

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

