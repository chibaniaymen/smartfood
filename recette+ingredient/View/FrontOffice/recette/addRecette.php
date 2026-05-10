<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/RecetteController.php';
require_once __DIR__ . '/../../../Model/Recette.php';
require_once __DIR__ . '/../../FrontOffice/partials/session.php';

$controller = new RecetteController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    if ($nom === '') {
        $error = 'Le nom est requis.';
    } else {
        $image = null;
        if (!empty($_FILES['image_recette']['name']) && $_FILES['image_recette']['error'] === 0) {
            $uploaded = $controller->saveUploadedImage($_FILES['image_recette']);
            if ($uploaded) { $image = $uploaded; }
        }

        $temp_preparation = isset($_POST['temp_preparation']) && $_POST['temp_preparation'] !== '' ? (int)$_POST['temp_preparation'] : null;
        $temp_cuisson = isset($_POST['temp_cuisson']) && $_POST['temp_cuisson'] !== '' ? (int)$_POST['temp_cuisson'] : null;
        $nombre_portion = isset($_POST['nombre_portion']) && $_POST['nombre_portion'] !== '' ? (int)$_POST['nombre_portion'] : null;
        $difficulte = trim($_POST['difficulte'] ?? '');

        $r = new Recette(
            null,
            $nom,
            $_POST['description'] ?? '',
            $_POST['instruction'] ?? '',
            $temp_preparation,
            $temp_cuisson,
            $nombre_portion,
            $difficulte,
            $image
        );

        // debug: inspect table columns to diagnose column name mismatches
        $debug_cols = '';
        try {
            $db = config::getConnexion();
            $desc = $db->query("DESCRIBE recette")->fetchAll();
            $colnames = array_column($desc, 'Field');
            $debug_cols = implode(', ', $colnames);
            error_log('DEBUG DESCRIBE recette: ' . $debug_cols);
        } catch (Exception $e) {
            $debug_cols = 'DESCRIBE error: ' . $e->getMessage();
            error_log('DEBUG DESCRIBE erreur: ' . $e->getMessage());
        }

        if ($controller->ajouterRecette($r)) {
            header('Location: listRecettes.php');
            exit;
        }
        $error = "Erreur lors de l'ajout de la recette.";
        // surface debug info to the page to help next steps
        $error .= ' (' . htmlspecialchars($debug_cols) . ')';
    }
}

// render inside BackOffice header/footer
require_once __DIR__ . '/../../../../view/BackOffice/header.php';
?>

<div class="row">
  <div class="col-md-12">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row">
          <h4 class="card-title">Ajouter une Recette</h4>
        </div>
      </div>
      <div class="card-body">
        <?php if ($error): ?>
            <div style="padding:10px;background:#fff1f0;border-radius:8px;color:#b91c1c;margin-bottom:12px"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2"><label>Nom</label><input name="nom" class="form-control"></div>
                    <div class="mb-2"><label>Temps préparation (min)</label><input name="temp_preparation" type="number" class="form-control"></div>
                    <div class="mb-2"><label>Temps cuisson (min)</label><input name="temp_cuisson" type="number" class="form-control"></div>
                    <div class="mb-2"><label>Nombre de portions</label><input name="nombre_portion" type="number" class="form-control"></div>
                    <div class="mb-2"><label>Difficulté</label>
                        <select name="difficulte" class="form-control">
                            <option value="">--</option>
                            <option value="Facile">Facile</option>
                            <option value="Moyen">Moyen</option>
                            <option value="Difficile">Difficile</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2"><label>Photo (optionnel)</label><input name="image_recette" type="file" class="form-control"></div>
                    <div class="mb-2"><label>Courte description</label><textarea name="description" class="form-control"></textarea></div>
                    <div class="mb-2"><label>Instructions détaillées</label><textarea name="instruction" class="form-control"></textarea></div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Enregistrer la recette</button>
                <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
      </div>
    </div>
  </div>
 </div>

<?php require_once __DIR__ . '/../../../../view/BackOffice/footer.php'; ?>

</html>
