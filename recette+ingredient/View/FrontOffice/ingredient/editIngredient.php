<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/IngredientController.php';
require_once __DIR__ . '/../../../Model/Ingredient.php';
require_once __DIR__ . '/../../FrontOffice/partials/session.php';

$ctrl = new IngredientController();
$error = '';

if (!isset($_GET['id']) || empty($_GET['id'])) { header('Location: index.php'); exit; }
$id = (int)$_GET['id'];
$data = $ctrl->getIngredient($id);
if (!$data) { header('Location: ../../FrontOffice/recette/listRecettes.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate
    $nom = trim($_POST['nom'] ?? '');
    $quantite = $_POST['quantite'] ?? null;
    $unite = $_POST['unite'] ?? null;
    $categorie = $_POST['categorie'] ?? null;
    $calories = isset($_POST['calories']) ? $_POST['calories'] : null;
    $proteines = isset($_POST['proteines']) ? $_POST['proteines'] : null;
    $glucides = isset($_POST['glucides']) ? $_POST['glucides'] : null;
    $lipides = isset($_POST['lipides']) ? $_POST['lipides'] : null;
    $fibres = isset($_POST['fibres']) ? $_POST['fibres'] : null;
    $sucre = isset($_POST['sucre']) ? $_POST['sucre'] : null;
    $sel = isset($_POST['sel']) ? $_POST['sel'] : null;

    if ($nom === '') {
        $error = 'Le nom de l\'ingrédient est requis.';
    } elseif (strlen($nom) > 255) {
        $error = 'Le nom est trop long (max 255 caractères).';
    } elseif ($quantite === null || $quantite === '' || !is_numeric($quantite)) {
        $error = 'La quantité doit être un nombre.';
    } elseif ($unite === null || $unite === '' || !is_numeric($unite)) {
        $error = 'L\'unité doit être un nombre.';
    } else {
        // Validate numeric fields if provided
        $numericFields = ['calories'=> $calories, 'proteines'=> $proteines, 'glucides'=> $glucides, 'lipides'=> $lipides, 'fibres'=> $fibres, 'sucre'=> $sucre, 'sel'=> $sel];
        foreach ($numericFields as $k => $v) {
            if ($v !== null && $v !== '' && !is_numeric($v)) {
                $error = 'La valeur de ' . $k . ' doit être un nombre.';
                break;
            }
        }
    }

    if ($error === '') {
        $i = new Ingredient(
            $id,
            $data['id_recette'],
            $nom,
            $quantite,
            $unite,
            $categorie,
            $calories !== null && $calories !== '' ? (float)$calories : null,
            $proteines !== null && $proteines !== '' ? (float)$proteines : null,
            $glucides !== null && $glucides !== '' ? (float)$glucides : null,
            $lipides !== null && $lipides !== '' ? (float)$lipides : null,
            $fibres !== null && $fibres !== '' ? (float)$fibres : null,
            $sucre !== null && $sucre !== '' ? (float)$sucre : null,
            $sel !== null && $sel !== '' ? (float)$sel : null
        );
                if ($ctrl->updateIngredient($i, $id)) {
                    $q = trim($_GET['q'] ?? '');
                    $sort = $_GET['sort'] ?? '';
                    $redir = 'index.php?recette_id=' . (int)$data['id_recette'];
                    if ($q !== '') $redir .= '&q=' . urlencode($q);
                    if ($sort !== '') $redir .= '&sort=' . urlencode($sort);
                    header('Location: ' . $redir);
                    exit;
                }
        $error = 'Erreur lors de la mise à jour.';
    }
}

// render within BackOffice template
require_once __DIR__ . '/../../../../view/BackOffice/header.php';
?>

<div class="row">
  <div class="col-md-8">
    <div class="card card-round">
      <div class="card-header"><h4 class="card-title">Modifier ingrédient</h4></div>
      <div class="card-body">
        <?php if ($error): ?>
            <div style="color:#b91c1c;margin-bottom:8px"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div id="client-error" style="color:#b91c1c;margin-bottom:8px;display:none"></div>
        <form id="edit-ingredient-form" method="POST">
            <div class="mb-2"><label>Nom</label><input name="nom" class="form-control" value="<?php echo htmlspecialchars($data['nom']); ?>"></div>
            <div class="mb-2"><label>Quantité</label><input type="number" step="any" name="quantite" class="form-control" value="<?php echo htmlspecialchars($data['quantite']); ?>"></div>
            <div class="mb-2"><label>Unité</label><input type="number" step="any" name="unite" class="form-control" value="<?php echo htmlspecialchars($data['unite'] ?? ''); ?>"></div>
            <div class="mb-2"><label>Catégorie</label><input name="categorie" class="form-control" value="<?php echo htmlspecialchars($data['categorie'] ?? ''); ?>"></div>
            <fieldset class="mb-2"><legend>Valeurs nutritionnelles (par portion)</legend>
                <div class="row">
                    <div class="col"><input name="calories" class="form-control" placeholder="Calories" value="<?php echo htmlspecialchars($data['calories'] ?? ''); ?>"></div>
                    <div class="col"><input name="proteines" class="form-control" placeholder="Prot. (g)" value="<?php echo htmlspecialchars($data['proteines'] ?? ''); ?>"></div>
                    <div class="col"><input name="glucides" class="form-control" placeholder="Gluc. (g)" value="<?php echo htmlspecialchars($data['glucides'] ?? ''); ?>"></div>
                </div>
                <div class="row mt-2">
                    <div class="col"><input name="lipides" class="form-control" placeholder="Lip. (g)" value="<?php echo htmlspecialchars($data['lipides'] ?? ''); ?>"></div>
                    <div class="col"><input name="fibres" class="form-control" placeholder="Fibres (g)" value="<?php echo htmlspecialchars($data['fibres'] ?? ''); ?>"></div>
                    <div class="col"><input name="sucre" class="form-control" placeholder="Sucre (g)" value="<?php echo htmlspecialchars($data['sucre'] ?? ''); ?>"></div>
                    <div class="col"><input name="sel" class="form-control" placeholder="Sel (g)" value="<?php echo htmlspecialchars($data['sel'] ?? ''); ?>"></div>
                </div>
            </fieldset>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/ingredient/index.php?recette_id=<?php echo (int)$data['id_recette']; ?>" class="btn btn-secondary" style="margin-left:8px">Annuler</a>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            var form = document.getElementById('edit-ingredient-form');
            var clientErr = document.getElementById('client-error');
            if (!form) return;
            form.addEventListener('submit', function(e){
                clientErr.style.display = 'none'; clientErr.textContent = '';
                var nom = (form.querySelector('[name="nom"]').value || '').trim();
                var quant = (form.querySelector('[name="quantite"]').value || '').trim();
                var unite = (form.querySelector('[name="unite"]').value || '').trim();
                var errors = [];
                if (nom === '') errors.push('Le nom de l\'ingrédient est requis.');
                if (quant === '' || !isFinite(Number(quant))) errors.push('La quantité doit être un nombre.');
                if (unite === '' || !isFinite(Number(unite))) errors.push('L\'unité doit être un nombre.');
                if (errors.length) {
                    e.preventDefault();
                    clientErr.textContent = errors.join(' ');
                    clientErr.style.display = 'block';
                    window.scrollTo({top: clientErr.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth'});
                }
            });
        });
        </script>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../../../view/BackOffice/footer.php'; ?>
