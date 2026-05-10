<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/IngredientController.php';
require_once __DIR__ . '/../../../Controller/RecetteController.php';
require_once __DIR__ . '/../../../Model/Ingredient.php';

$ctrl = new IngredientController();
$recCtrl = new RecetteController();
$error = '';

$recipes = $recCtrl->listRecettes(null, 'newest');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ingredient'])) {
    $id_rec = isset($_POST['id_rec']) ? (int)$_POST['id_rec'] : 0;
    $nom = trim($_POST['nom'] ?? '');
    $quantite = $_POST['quantite'] ?? null;
    $unite = $_POST['unite'] ?? null;
    $categorie = trim($_POST['categorie'] ?? '');
    $calories = isset($_POST['calories']) && $_POST['calories'] !== '' ? (float)$_POST['calories'] : null;
    $proteines = isset($_POST['proteines']) && $_POST['proteines'] !== '' ? (float)$_POST['proteines'] : null;
    $glucides = isset($_POST['glucides']) && $_POST['glucides'] !== '' ? (float)$_POST['glucides'] : null;
    $lipides = isset($_POST['lipides']) && $_POST['lipides'] !== '' ? (float)$_POST['lipides'] : null;
    $fibres = isset($_POST['fibres']) && $_POST['fibres'] !== '' ? (float)$_POST['fibres'] : null;
    $sucre = isset($_POST['sucre']) && $_POST['sucre'] !== '' ? (float)$_POST['sucre'] : null;
    $sel = isset($_POST['sel']) && $_POST['sel'] !== '' ? (float)$_POST['sel'] : null;

    if ($id_rec <= 0) {
        $error = 'Veuillez choisir une recette.';
    } elseif ($nom === '') {
        $error = 'Le nom de l\'ingrédient est requis.';
    } else {
        $i = new Ingredient(null, $id_rec, $nom, $quantite, $unite, $categorie, $calories, $proteines, $glucides, $lipides, $fibres, $sucre, $sel);
        if ($ctrl->addIngredient($i)) {
            header('Location: listingredient.php');
            exit;
        }
        $error = 'Erreur lors de l\'ajout de l\'ingrédient.';
    }
}

require_once __DIR__ . '/../../../../view/BackOffice/header.php';
?>

<div class="row">
  <div class="col-md-8">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row">
          <h4 class="card-title">Ajouter un ingrédient</h4>
        </div>
      </div>
      <div class="card-body">
        <?php if ($error): ?><div style="color:#b91c1c;margin-bottom:8px"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
          <div class="mb-2">
            <label>Recette</label>
            <select name="id_rec" class="form-control form-control-sm">
              <option value="">-- Choisir --</option>
              <?php foreach ($recipes as $r): ?>
                <option value="<?php echo (int)$r['id_recette']; ?>"><?php echo htmlspecialchars($r['nom'] ?? $r['titre'] ?? 'Recette'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2"><label>Nom</label><input name="nom" class="form-control"></div>
          <div class="mb-2"><label>Quantité</label><input name="quantite" class="form-control"></div>
          <div class="mb-2"><label>Unité</label><input name="unite" class="form-control"></div>
          <div class="mb-2"><label>Catégorie</label><input name="categorie" class="form-control"></div>
          <fieldset class="mb-2"><legend>Nutrition</legend>
            <div class="row">
              <div class="col"><input name="calories" class="form-control" placeholder="Calories"></div>
              <div class="col"><input name="proteines" class="form-control" placeholder="Prot. (g)"></div>
              <div class="col"><input name="glucides" class="form-control" placeholder="Gluc. (g)"></div>
            </div>
            <div class="row mt-2">
              <div class="col"><input name="lipides" class="form-control" placeholder="Lip. (g)"></div>
              <div class="col"><input name="fibres" class="form-control" placeholder="Fibres (g)"></div>
              <div class="col"><input name="sucre" class="form-control" placeholder="Sucre (g)"></div>
              <div class="col"><input name="sel" class="form-control" placeholder="Sel (g)"></div>
            </div>
          </fieldset>
          <div class="mt-3">
            <button name="add_ingredient" class="btn btn-primary">Ajouter</button>
            <a href="listingredient.php" class="btn btn-secondary" style="margin-left:8px">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../../../view/BackOffice/footer.php'; ?>
