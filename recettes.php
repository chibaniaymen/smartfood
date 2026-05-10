<?php
require_once __DIR__ . '/recette+ingredient/Controller/RecetteController.php';

$rc = new RecetteController();
$recettes = $rc->listRecettes();
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="view/FrontOffice/images/favicon.png" type="">

  <title>Recettes</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="view/FrontOffice/css/bootstrap.css" />

  <!-- font awesome style -->
  <link href="view/FrontOffice/css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="view/FrontOffice/css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="view/FrontOffice/css/responsive.css" rel="stylesheet" />

  <style>
    /* Forcer couleur du texte du tableau en noir */
    .table, .table th, .table td { color: #000 !important; }
  </style>

</head>

<body>

  <div class="hero_area">
    <div class="bg-box">
      <img src="view/FrontOffice/images/hero-bg.jpg" alt="">
    </div>
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.php">
            <span>Feane</span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  mx-auto ">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="view/FrontOffice/searchCommentaires.php">Commentaires</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="view/FrontOffice/addArticle.php">Blog</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="recettes.php">Recettes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="myEvents.php">Evenemnts</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Administration
                </a>
                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                  <a class="dropdown-item" href="view/BackOffice/dashboard.php">Blog Dashboard</a>
                  <a class="dropdown-item" href="view/BackOffice/dashboard_view.php">Event Dashboard</a>
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
    <!-- slider section -->
    <section class="slider_section ">
      <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container ">
              <div class="row">
                <div class="col-md-7 col-lg-6 ">
                  <div class="detail-box">
                    <h1>
                      Recettes
                    </h1>
                    <p>
                      Découvrez nos délicieuses recettes.
                    </p>
                    <div class="btn-box">
                      <a href="#recettes-list" class="btn1">
                        Voir recettes
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>


  <section class="food_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center" id="recettes-list">
        <h2>Recettes</h2>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Description</th>
              <th>Temps (prépa / cuisson)</th>
              <th>Portions</th>
              <th>Difficulté</th>
              <th>Image</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recettes)): ?>
              <tr><td colspan="7" class="text-center">Aucune recette trouvée.</td></tr>
            <?php else: ?>
              <?php foreach ($recettes as $r): ?>
                <?php
                  $id = $r['id_recette'] ?? '';
                  $nom = $r['nom'] ?? ($r['titre'] ?? '');
                  $desc = $r['description'] ?? '';
                  $tp = $r['temp_preparation'] ?? '';
                  $tc = $r['temp_cuisson'] ?? '';
                  $portions = $r['nombre_portion'] ?? '';
                  $difficulte = $r['difficulte'] ?? ($r['difficulté'] ?? '');
                  $img = $r['image'] ?? '';
                  $imgUrl = '';
                  if (!empty($img)) {
                      if (strpos($img, 'http') === 0 || strpos($img, '/') === 0) {
                          $imgUrl = $img;
                      } else {
                          $imgUrl = 'recette+ingredient/View/FrontOffice/' . ltrim($img, '/');
                      }
                  }
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($id); ?></td>
                  <td><?php echo htmlspecialchars($nom); ?></td>
                  <td><?php echo htmlspecialchars(mb_strimwidth($desc, 0, 120, '...')); ?></td>
                  <td><?php echo htmlspecialchars(($tp === '' ? '' : $tp) . ' / ' . ($tc === '' ? '' : $tc)); ?></td>
                  <td><?php echo htmlspecialchars($portions); ?></td>
                  <td><?php echo htmlspecialchars($difficulte); ?></td>
                  <td><?php if (!empty($imgUrl)): ?><img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="" style="max-width:120px; max-height:80px;" /><?php else: ?>&nbsp;<?php endif; ?></td>
                  <td>
                    <a class="btn" href="recette+ingredient/View/FrontOffice/ingredient/index.php?recette_id=<?php echo urlencode($id); ?>" style="background:#06b6d4;padding:8px 10px;border-radius:8px;color:white;text-decoration:none">Voir ingrédients</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>


  <!-- jQery -->
  <script src="view/FrontOffice/js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <!-- bootstrap js -->
  <script src="view/FrontOffice/js/bootstrap.js"></script>
  <!-- custom js -->
  <script src="view/FrontOffice/js/custom.js"></script>

</body>

</html>
