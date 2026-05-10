<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../Controller/IngredientController.php';
require_once __DIR__ . '/../../../Controller/RecetteController.php';
require_once __DIR__ . '/../../../Model/Ingredient.php';
require_once __DIR__ . '/../../FrontOffice/partials/session.php';

$ctrl = new IngredientController();
$recCtrl = new RecetteController();
$error = '';

// require a recette context (from listRecettes.php button)
$recette_id = isset($_GET['recette_id']) ? (int)$_GET['recette_id'] : (int)($_POST['id_rec'] ?? 0);
if ($recette_id <= 0) {
    header('Location: ../recette/listRecettes.php');
    exit;
}

// search & sort
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'name_asc';
$extraParams = '&q=' . urlencode($q) . '&sort=' . urlencode($sort);

$recipe = $recCtrl->showRecette($recette_id);
if (!$recipe) {
    header('Location: ../recette/listRecettes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ingredient'])) {
    $id_rec = $recette_id;
    $nom = trim($_POST['nom'] ?? '');
    $quantite = $_POST['quantite'] ?? null;
    $unite = $_POST['unite'] ?? null;
    $categorie = $_POST['categorie'] ?? null;
    $calories = isset($_POST['calories']) ? (float)$_POST['calories'] : null;
    $proteines = isset($_POST['proteines']) ? (float)$_POST['proteines'] : null;
    $glucides = isset($_POST['glucides']) ? (float)$_POST['glucides'] : null;
    $lipides = isset($_POST['lipides']) ? (float)$_POST['lipides'] : null;
    $fibres = isset($_POST['fibres']) ? (float)$_POST['fibres'] : null;
    $sucre = isset($_POST['sucre']) ? (float)$_POST['sucre'] : null;
    $sel = isset($_POST['sel']) ? (float)$_POST['sel'] : null;

    // Server-side validation
    if ($id_rec <= 0) {
        $error = 'Recette invalide.';
    } elseif ($nom === '') {
        $error = 'Le nom de l\'ingrédient est requis.';
    } elseif (strlen($nom) > 255) {
        $error = 'Le nom est trop long (max 255 caractères).';
    } elseif ($quantite === null || $quantite === '' || !is_numeric($quantite)) {
        $error = 'La quantité doit être un nombre.';
    } elseif ($unite === null || $unite === '' || !is_numeric($unite)) {
        $error = 'L\'unité doit être un nombre.';
    } else {
        // optional: basic sanitization/validation for quantite (allow free text) and category
        // Attempt to add ingredient
        $i = new Ingredient(null, $id_rec, $nom, $quantite, $unite, $categorie, $calories, $proteines, $glucides, $lipides, $fibres, $sucre, $sel);
        if ($ctrl->addIngredient($i)) {
            header('Location: index.php?recette_id=' . $id_rec);
            exit;
        }
        $error = 'Erreur lors de l\'ajout de l\'ingrédient.';
    }
}

$ingredients = $ctrl->listByRecette($recette_id, $q !== '' ? $q : null, $sort);
// admin mode (show add/edit/delete) when called with ?admin=1
$isAdmin = isset($_GET['admin']) && $_GET['admin'] == '1';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ingrédients</title>
        <link rel="stylesheet" type="text/css" href="/integ/view/FrontOffice/css/bootstrap.css" />
        <link href="/integ/view/FrontOffice/css/font-awesome.min.css" rel="stylesheet" />
        <link href="/integ/view/FrontOffice/css/style.css" rel="stylesheet" />
        <link href="/integ/view/FrontOffice/css/responsive.css" rel="stylesheet" />
        <style>
            /* Forcer texte en noir pour tableaux */
            .table, .table th, .table td { color: #000 !important; }
        </style>
</head>
<body>

    <div class="hero_area">
        <div class="bg-box">
            <img src="/integ/view/FrontOffice/images/hero-bg.jpg" alt="">
        </div>
        <!-- header section strats -->
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container ">
                    <a class="navbar-brand" href="/integ/index.php">
                        <span>Feane</span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class=""> </span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav  mx-auto ">
                            <li class="nav-item active">
                                <a class="nav-link" href="/integ/index.php">Home <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/integ/view/FrontOffice/searchCommentaires.php">Commentaires</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/integ/view/FrontOffice/addArticle.php">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/integ/recettes.php">Recettes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/integ/myEvents.php">Evenemnts</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Administration
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <a class="dropdown-item" href="/integ/view/BackOffice/dashboard.php">Blog Dashboard</a>
                                    <a class="dropdown-item" href="/integ/view/BackOffice/dashboard_view.php">Event Dashboard</a>
                                </div>
                            </li>
                        </ul>
                        <div class="user_option">
                            <a href="" class="user_link"><i class="fa fa-user" aria-hidden="true"></i></a>
                            <a class="cart_link" href="#"> 
                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;" xml:space="preserve">
                                    <g>
                                        <g>
                                            <path d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248 c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                        </g>
                                    </g>
                                </svg>
                            </a>
                            <form class="form-inline">
                                <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </form>
                            <a href="" class="order_online">Order Online</a>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <!-- end header section -->
    </div>

    <section class="food_section layout_padding">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Ingrédients</h2>
            </div>

            <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between">
                <h3 style="margin:0">Ingrédients pour : <?php echo htmlspecialchars($recipe['nom'] ?? $recipe['titre'] ?? ''); ?></h3>
                <a href="/integ/recettes.php" style="text-decoration:none;color:#374151">← Retour aux recettes</a>
            </div>

            <div class="table-responsive">
                <form method="GET" action="index.php" style="display:flex;gap:8px;align-items:center;margin-bottom:12px">
                        <input type="hidden" name="recette_id" value="<?php echo (int)$recette_id; ?>">
                        <input type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($q); ?>" style="padding:8px;border-radius:8px;border:1px solid #e6e6e6">
                        <select name="sort" style="padding:8px;border-radius:8px;border:1px solid #e6e6e6">
                                <option value="name_asc" <?php echo ($sort==='name_asc') ? 'selected' : ''; ?>>Nom A→Z</option>
                                <option value="name_desc" <?php echo ($sort==='name_desc') ? 'selected' : ''; ?>>Nom Z→A</option>
                                <option value="newest" <?php echo ($sort==='newest') ? 'selected' : ''; ?>>Plus récents</option>
                                <option value="oldest" <?php echo ($sort==='oldest') ? 'selected' : ''; ?>>Plus anciens</option>
                        </select>
                        <button class="btn" type="submit">Rechercher</button>
                        <a class="btn btn-secondary" href="index.php?recette_id=<?php echo (int)$recette_id; ?>" style="background:#f3f4f6;color:var(--muted)">Réinitialiser</a>
                </form>

                <table class="table table-striped table-bordered">
                        <thead>
                                <tr>
                                    <th>#</th>
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
                                    <?php if ($isAdmin): ?><th>Actions</th><?php endif; ?>
                                </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($ingredients)): ?>
                                <tr><td colspan="13">Aucun ingrédient trouvé.</td></tr>
                        <?php else: ?>
                                <?php foreach ($ingredients as $ing): ?>
                                        <tr>
                                                <td><?php echo htmlspecialchars($ing['id_ingredient']); ?></td>
                                                <td><?php echo htmlspecialchars($ing['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($ing['quantite'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($ing['unite'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($ing['categorie'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['calories']) ? $ing['calories'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['proteines']) ? $ing['proteines'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['glucides']) ? $ing['glucides'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['lipides']) ? $ing['lipides'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['fibres']) ? $ing['fibres'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['sucre']) ? $ing['sucre'] : ''); ?></td>
                                                <td><?php echo htmlspecialchars(isset($ing['sel']) ? $ing['sel'] : ''); ?></td>
                                                <?php if ($isAdmin): ?>
                                                <td>
                                                    <a class="btn btn-sm btn-info" href="editIngredient.php?id=<?php echo (int)$ing['id_ingredient']; ?><?php echo $extraParams; ?>">Modifier</a>
                                                    &nbsp;
                                                    <a class="btn btn-sm btn-danger" href="deleteIngredient.php?id=<?php echo (int)$ing['id_ingredient']; ?>&recette_id=<?php echo (int)$ing['id_recette']; ?><?php echo $extraParams; ?>" onclick="return confirm('Supprimer cet ingrédient ?');">Supprimer</a>
                                                </td>
                                                <?php endif; ?>
                                        </tr>
                                <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                </table>
            </div>

                <?php if ($isAdmin): ?>
                <h2 style="margin-top:18px">Ajouter un ingrédient</h2>
                <?php if ($error): ?><div id="server-error" style="color:#b91c1c;margin-bottom:8px"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                <div id="client-error" style="color:#b91c1c;margin-bottom:8px;display:none"></div>
                <form id="add-ingredient-form" method="POST" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <input type="hidden" name="id_rec" value="<?php echo (int)$recette_id; ?>">
                    <input name="nom" placeholder="Nom ingrédient" required>
                    <input type="number" step="any" name="quantite" placeholder="Quantité (ex: 200)" required>
                    <input type="number" step="any" name="unite" placeholder="Unité (ex: 100)" required>
                    <input name="categorie" placeholder="Catégorie (ex: Légume)">
                    <button name="add_ingredient" class="btn">Ajouter</button>
                </form>
                <?php endif; ?>

                <script>
                document.addEventListener('DOMContentLoaded', function(){
                        var form = document.getElementById('add-ingredient-form');
                        var clientErr = document.getElementById('client-error');
                        if (!form) return;
                        form.addEventListener('submit', function(e){
                                clientErr.style.display = 'none';
                                clientErr.textContent = '';
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
    </section>

    <script src="/integ/view/FrontOffice/js/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="/integ/view/FrontOffice/js/bootstrap.js"></script>
    <script src="/integ/view/FrontOffice/js/custom.js"></script>
</body>
</html>
