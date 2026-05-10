<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/RecetteController.php';
require_once __DIR__ . '/../../../Controller/IngredientController.php';
require_once __DIR__ . '/../../../Model/Recette.php';
require_once __DIR__ . '/../../FrontOffice/partials/session.php';

$controller = new RecetteController();
$ingCtrl = new IngredientController();
$error = '';

// action handling: edit, delete, view (all performed within this page)
$action = $_GET['action'] ?? null;
$actionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// search & sort (preserve for redirects)
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$extraParams = '&q=' . urlencode($q) . '&sort=' . urlencode($sort);


if ($action === 'delete' && $actionId > 0) {
    $controller->supprimerRecette($actionId);
    header('Location: listRecettes.php?q=' . urlencode($q) . '&sort=' . urlencode($sort));
    exit;
}

$editData = null;
if ($action === 'edit' && $actionId > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_recette'])) {
        $existing = $controller->showRecette($actionId);
        $image = $existing['image'] ?? null;
        if (!empty($_FILES['image_recette']['name']) && $_FILES['image_recette']['error'] === 0) {
            $uploaded = $controller->saveUploadedImage($_FILES['image_recette']);
            if ($uploaded) { $image = $uploaded; }
        }

        $nom = trim($_POST['nom'] ?? '');
        $temp_preparation = isset($_POST['temp_preparation']) && $_POST['temp_preparation'] !== '' ? (int)$_POST['temp_preparation'] : null;
        $temp_cuisson = isset($_POST['temp_cuisson']) && $_POST['temp_cuisson'] !== '' ? (int)$_POST['temp_cuisson'] : null;
        $nombre_portion = isset($_POST['nombre_portion']) && $_POST['nombre_portion'] !== '' ? (int)$_POST['nombre_portion'] : null;
        $difficulte = trim($_POST['difficulte'] ?? '');
        $description = $_POST['description'] ?? '';
        $instruction = $_POST['instruction'] ?? '';

        $r = new Recette(null, $nom, $description, $instruction, $temp_preparation, $temp_cuisson, $nombre_portion, $difficulte, $image);
        if ($controller->modifierRecette($r, $actionId)) {
            header('Location: listRecettes.php?q=' . urlencode($q) . '&sort=' . urlencode($sort));
            exit;
        }
        $error = 'Erreur lors de la modification.';
    } else {
        $editData = $controller->showRecette($actionId);
    }
}

$viewId = ($action === 'view' && $actionId > 0) ? $actionId : 0;

$recettes = $controller->listRecettes($q !== '' ? $q : null, $sort);
// render within BackOffice
require_once __DIR__ . '/../../../../view/BackOffice/header.php';
?>

<div class="wrap">
    <div class="top">
        <h1>Recettes</h1>
        <div style="display:flex;align-items:center;gap:8px">
            <form method="GET" action="" style="display:flex;gap:8px;align-items:center">
                <input id="q-input-recettes" type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($q); ?>" style="padding:8px;border-radius:8px;border:1px solid #e6e6e6">
                <button type="button" id="stt-btn-recettes" title="Recherche vocale" style="background:#ffbe33;border:0;border-radius:8px;padding:8px;color:#222831;"><i class="fa fa-microphone"></i></button>
                <select name="sort" style="padding:8px;border-radius:8px;border:1px solid #e6e6e6">
                    <option value="newest" <?php echo ($sort==='newest') ? 'selected' : ''; ?>>Plus récentes</option>
                    <option value="oldest" <?php echo ($sort==='oldest') ? 'selected' : ''; ?>>Plus anciennes</option>
                    <option value="name_asc" <?php echo ($sort==='name_asc') ? 'selected' : ''; ?>>Nom A→Z</option>
                    <option value="name_desc" <?php echo ($sort==='name_desc') ? 'selected' : ''; ?>>Nom Z→A</option>
                </select>
                <button class="btn" type="submit">Rechercher</button>
                <a class="btn btn-secondary" href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php" style="background:#f3f4f6;color:var(--muted)">Réinitialiser</a>
            </form>
            <a class="btn" href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/addRecette.php" style="margin-left:8px">➕ Nouvelle recette</a>
        </div>
    </div>

    <div class="grid">
        <?php if (empty($recettes)): ?>
            <div>Aucune recette trouvée.</div>
        <?php else: ?>
            <?php foreach ($recettes as $r): ?>
                <div class="card">
                    <?php if ($action === 'edit' && isset($editData) && (int)$r['id_recette'] === (int)$editData['id_recette']): ?>
                        <h3>Modifier : <?php echo htmlspecialchars($editData['nom'] ?? $editData['titre'] ?? 'Recette'); ?></h3>
                        <?php if ($error): ?><div style="color:#b91c1c"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <label>Nom</label>
                            <input name="nom" value="<?php echo htmlspecialchars($editData['nom'] ?? $editData['titre'] ?? ''); ?>">
                            <label>Temps préparation (min)</label>
                            <input name="temp_preparation" type="number" min="0" value="<?php echo htmlspecialchars($editData['temp_preparation'] ?? ''); ?>">
                            <label>Temps cuisson (min)</label>
                            <input name="temp_cuisson" type="number" min="0" value="<?php echo htmlspecialchars($editData['temp_cuisson'] ?? ''); ?>">
                            <label>Nombre de portions</label>
                            <input name="nombre_portion" type="number" min="1" value="<?php echo htmlspecialchars($editData['nombre_portion'] ?? ''); ?>">
                            <label>Difficulté</label>
                            <select name="difficulte">
                                <option value="" <?php echo (empty($editData['difficulte'] ?? $editData['difficulté'] ?? '') ? 'selected' : ''); ?>>--</option>
                                <option value="Facile" <?php echo (($editData['difficulte'] ?? '') === 'Facile' ? 'selected' : ''); ?>>Facile</option>
                                <option value="Moyen" <?php echo (($editData['difficulte'] ?? '') === 'Moyen' ? 'selected' : ''); ?>>Moyen</option>
                                <option value="Difficile" <?php echo (($editData['difficulte'] ?? '') === 'Difficile' ? 'selected' : ''); ?>>Difficile</option>
                            </select>
                            <label>Description</label>
                            <textarea name="description"><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
                            <label>Instructions</label>
                            <textarea name="instruction"><?php echo htmlspecialchars($editData['instruction'] ?? $editData['instructions'] ?? ''); ?></textarea>
                            <label>Photo (laisser vide pour conserver)</label>
                            <input type="file" name="image_recette" accept=".jpg,.jpeg,.png,.webp">
                            <div class="actions">
                                <button type="submit" name="edit_recette" style="background:#10b981;color:white;padding:8px 10px;border-radius:8px;border:0">Enregistrer</button>
                                <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php" style="margin-left:8px">Annuler</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <?php if (!empty($r['image'])): ?>
                            <img src="<?php echo htmlspecialchars('../../FrontOffice/' . $r['image']); ?>" alt="">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($r['nom'] ?? $r['titre'] ?? ''); ?></h3>
                        <p style="color:#6b7280;font-size:0.95rem"><?php echo htmlspecialchars(substr($r['description'] ?? '',0,140)); ?><?php echo strlen($r['description'] ?? '')>140?'...':''; ?></p>
                        <?php if ($viewId && (int)$r['id_recette'] === (int)$viewId):
                            $ingredients = $ingCtrl->listByRecette((int)$r['id_recette']); ?>
                            <div style="margin-top:8px;padding:10px;border:1px solid #f3f4f6;border-radius:8px;background:#fff">
                                <h4>Détails</h4>
                                <p><?php echo nl2br(htmlspecialchars($r['description'] ?? '')); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($r['instruction'] ?? $r['instructions'] ?? '')); ?></p>
                                <h4>Ingrédients (<?php echo count($ingredients); ?>)</h4>
                                <a class="btn" style="background:#06b6d4;padding:6px 8px" href="<?php echo $baseUrl; ?>/recette+ingredient/View/BackOffice/ingredient/listingredient.php?recette_id=<?php echo (int)$r['id_recette']; ?>">Modifier ingrédients</a>
                            </div>
                        <?php endif; ?>
                        <div class="actions">
                            <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php?action=view&id=<?php echo (int)$r['id_recette']; ?><?php echo $extraParams; ?>" style="background:#06b6d4;padding:8px 10px;border-radius:8px;color:white;text-decoration:none">Voir</a>
                            <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php?action=edit&id=<?php echo (int)$r['id_recette']; ?><?php echo $extraParams; ?>" style="background:#10b981;padding:8px 10px;border-radius:8px;color:white;text-decoration:none">Modifier</a>
                            <?php if (!($viewId && (int)$r['id_recette'] === (int)$viewId)): ?>
                                <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/BackOffice/ingredient/listingredient.php?recette_id=<?php echo (int)$r['id_recette']; ?>" style="background:#06b6d4;padding:8px 10px;border-radius:8px;color:white;text-decoration:none">Modifier ingrédients</a>
                            <?php endif; ?>
                            <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php?action=delete&id=<?php echo (int)$r['id_recette']; ?>" style="background:#ef4444;padding:8px 10px;border-radius:8px;color:white;text-decoration:none" onclick="return confirm('Supprimer cette recette ?');">Supprimer</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
    setupSTT('stt-btn-recettes', 'q-input-recettes', 'fr-FR');
});
</script>
<?php require_once __DIR__ . '/../../../../view/BackOffice/footer.php'; ?>
