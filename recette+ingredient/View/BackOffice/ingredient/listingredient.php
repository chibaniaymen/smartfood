<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/IngredientController.php';
require_once __DIR__ . '/../../../Controller/RecetteController.php';

$ctrl = new IngredientController();
$recCtrl = new RecetteController();

$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'id_asc';
$recette_id = isset($_GET['recette_id']) ? (int)$_GET['recette_id'] : 0;

// If a recette_id is provided, show only those ingredients
if ($recette_id > 0) {
    $ingredients = $ctrl->listByRecette($recette_id, $q !== '' ? $q : null, $sort);
} else {
    $ingredients = $ctrl->listAll();
}

// Use BackOffice template
require_once __DIR__ . '/../../../../view/BackOffice/header.php';
?>

<div class="row">
  <div class="col-md-12">
    <div class="card card-round">
      <div class="card-header">
          <div class="card-head-row">
          <h4 class="card-title">Ingrédients</h4>
          <div class="card-tools">
            <form method="GET" action="" style="display:flex;gap:8px;align-items:center">
                <?php if ($recette_id): ?><input type="hidden" name="recette_id" value="<?php echo (int)$recette_id; ?>"><?php endif; ?>
                <input id="q-input-ingredients" type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($q); ?>" class="form-control form-control-sm" style="width:220px">
                <button type="button" id="stt-btn-ingredients" class="btn btn-outline-secondary btn-sm" title="Recherche vocale" style="margin-left:4px"><i class="fa fa-microphone"></i></button>
                <select name="sort" class="form-control form-control-sm" style="width:160px">
                    <option value="id_asc" <?php echo ($sort==='id_asc') ? 'selected' : ''; ?>>ID ↑</option>
                    <option value="id_desc" <?php echo ($sort==='id_desc') ? 'selected' : ''; ?>>ID ↓</option>
                    <option value="name_asc" <?php echo ($sort==='name_asc') ? 'selected' : ''; ?>>Nom A→Z</option>
                    <option value="name_desc" <?php echo ($sort==='name_desc') ? 'selected' : ''; ?>>Nom Z→A</option>
                </select>
                <button class="btn btn-primary btn-sm" type="submit">Filtrer</button>
            </form>
          </div>
          <div style="margin-left:12px;display:flex;align-items:center">
            <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/BackOffice/ingredient/addIngredient.php" class="btn btn-primary btn-sm btn-round"><i class="fa fa-plus me-1"></i> Ajouter ingrédient</a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Recette</th>
                <th>Nom</th>
                <th>Quantité</th>
                <th>Unité</th>
                <th>Catégorie</th>
                <th>Calories</th>
                <th>Prot. (g)</th>
                <th>Gluc. (g)</th>
                <th>Lip. (g)</th>
                <th>Fibres (g)</th>
                <th>Sucre (g)</th>
                <th>Sel (g)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($ingredients)): ?>
                <tr><td colspan="14">Aucun ingrédient trouvé.</td></tr>
              <?php else: ?>
                <?php foreach ($ingredients as $ing): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($ing['id_ingredient']); ?></td>
                    <td><?php echo htmlspecialchars($recCtrl->showRecette((int)$ing['id_recette'])['nom'] ?? $ing['id_recette']); ?></td>
                    <td><?php echo htmlspecialchars($ing['nom'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['quantite'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['unite'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['categorie'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['calories'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['proteines'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['glucides'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['lipides'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['fibres'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['sucre'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ing['sel'] ?? ''); ?></td>
                    <td>
                      <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/ingredient/editIngredient.php?id=<?php echo (int)$ing['id_ingredient']; ?>" class="btn btn-link btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                      <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/ingredient/deleteIngredient.php?id=<?php echo (int)$ing['id_ingredient']; ?>" class="btn btn-link btn-danger btn-sm" onclick="return confirm('Supprimer cet ingrédient ?')"><i class="fa fa-times"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function setupSTT(btnId, inputId, lang){
    var btn = document.getElementById(btnId);
    var input = document.getElementById(inputId);
    if(!btn || !input) return;
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition || null;
    if(!SpeechRecognition){
      btn.addEventListener('click', function(){ alert('Reconnaissance vocale non supportée par votre navigateur. Utilisez Chrome ou Edge.');});
      return;
    }
    var recognizing = false;
    var recognition = new SpeechRecognition();
    recognition.lang = lang || 'fr-FR';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    recognition.onstart = function(){ recognizing=true; btn.classList.add('recording'); btn.innerHTML = '<i class="fa fa-microphone"></i> ...'; };
    recognition.onend = function(){ recognizing=false; btn.classList.remove('recording'); btn.innerHTML = '<i class="fa fa-microphone"></i>'; };
    recognition.onresult = function(e){ var transcript = e.results[0][0].transcript.trim(); input.value = transcript; var form = input.closest('form'); if(form) form.submit(); };
    recognition.onerror = function(e){ recognizing=false; btn.classList.remove('recording'); alert('Erreur reconnaissance vocale: '+(e.error||e.message||'') ); };
    btn.addEventListener('click', function(){ if(recognizing){ recognition.stop(); } else { recognition.start(); } });
  }
  setupSTT('stt-btn-ingredients', 'q-input-ingredients', 'fr-FR');
});
</script>
<?php require_once __DIR__ . '/../../../../view/BackOffice/footer.php'; ?>
