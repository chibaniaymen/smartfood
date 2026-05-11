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
                      <a href="#recettes-list" class="btn1" id="hero-action-btn">
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

      <!-- container that will receive the ingredient table when requested -->
      <div id="ingredient-container" style="display:none; margin-top:18px;"></div>

      <div id="recettes-table-wrapper" class="table-responsive">
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
                    <a class="btn btn-voir-ingredients" href="recette+ingredient/View/FrontOffice/ingredient/index.php?recette_id=<?php echo urlencode($id); ?>"
                       data-recipe-name="<?php echo htmlspecialchars($nom); ?>"
                       data-recipe-id="<?php echo htmlspecialchars($id); ?>"
                       style="background:#06b6d4;padding:8px 10px;border-radius:8px;color:white;text-decoration:none">Voir ingrédients</a>
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

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    function showIngredientHero(recipeName, href) {
      var detailBox = document.querySelector('.hero_area .detail-box');
      if (!detailBox) return;
      var h1 = detailBox.querySelector('h1');
      var p = detailBox.querySelector('p');
      var btn = document.getElementById('hero-action-btn') || detailBox.querySelector('.btn-box a.btn1');
      if (h1) h1.textContent = 'Ingrédients';
      if (p) { p.textContent = ''; p.style.display = 'none'; }
      if (btn) {
        btn.textContent = 'Voir ingrédients';
        btn.classList.add('btn1');
        // keep the hero button visible and make it scroll to the ingredient container
        btn.setAttribute('href', '#ingredient-container');
        btn.style.display = '';
        // replace click handler so it always smooth-scrolls to the ingredient area
        btn.onclick = function(e) {
          e.preventDefault();
          var target = document.getElementById('ingredient-container') || document.getElementById('recettes-list');
          if (target) target.scrollIntoView({behavior:'smooth', block:'start'});
          // ensure ingredients are loaded if they are not yet
          var container = document.getElementById('ingredient-container');
          if (container && (!container.innerHTML || container.innerHTML.trim() === '')) {
            if (href) loadIngredientTable(href);
          }
        };
      }

      // update the section heading (use same template as Recettes section)
      try {
        var sectionHeading = document.querySelector('#recettes-list h2');
        if (sectionHeading) {
          sectionHeading.textContent = recipeName ? 'Ingrédients' : 'Ingrédients';
        }
      } catch (e) { /* ignore */ }
      // set hash and scroll to the recettes list anchor
      try { location.hash = '#recettes-list'; } catch(e) {}
      var anchor = document.getElementById('recettes-list');
      if (anchor) anchor.scrollIntoView({behavior:'smooth', block:'start'});

      // load ingredients table into page
      if (href) loadIngredientTable(href);
    }

    function loadIngredientTable(href) {
      var container = document.getElementById('ingredient-container');
      var recipesWrapper = document.getElementById('recettes-table-wrapper');
      if (!container) return;
      // show loading indicator
      container.innerHTML = '<div style="padding:16px;background:#fff;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.06)">Chargement des ingrédients…</div>';
      container.style.display = 'block';
      if (recipesWrapper) recipesWrapper.style.display = 'none';

      fetch(href, { credentials: 'same-origin' }).then(function(resp) {
        return resp.text();
      }).then(function(html) {
        try {
          var parser = new DOMParser();
          var doc = parser.parseFromString(html, 'text/html');
          // Try to extract the ingredients heading and the table wrapper
          var heading = doc.querySelector('h3');
          var tableDiv = doc.querySelector('.table-responsive');
          var extra = '';
          if (heading) extra += '<div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between">' + heading.outerHTML + '</div>';
          if (tableDiv) extra += tableDiv.outerHTML;
          if (extra === '') {
            // fallback: inject full body
            extra = doc.body ? doc.body.innerHTML : html;
          }
          container.innerHTML = extra;
        } catch (e) {
          container.innerHTML = '<div style="color:#b91c1c">Erreur lors du chargement des ingrédients.</div>';
        }
      }).catch(function() {
        container.innerHTML = '<div style="color:#b91c1c">Impossible de charger les ingrédients.</div>';
      });
    }

    document.querySelectorAll('a.btn-voir-ingredients').forEach(function(a) {
      a.addEventListener('click', function(e) {
        e.preventDefault();
        var recipeName = this.dataset.recipeName || '';
        var href = this.getAttribute('href');
        showIngredientHero(recipeName, href);
      });
    });

    // If page loaded with ?recette_id=... and hash '#recettes-list', auto-load ingredients
    try {
      var params = new URLSearchParams(window.location.search);
      var rid = params.get('recette_id');
      if (rid && window.location.hash === '#recettes-list') {
        var link = document.querySelector('a.btn-voir-ingredients[data-recipe-id="' + rid + '"]');
        var name = link ? (link.dataset.recipeName || '') : '';
        var href = link ? link.getAttribute('href') : 'recette+ingredient/View/FrontOffice/ingredient/index.php?recette_id=' + encodeURIComponent(rid);
        showIngredientHero(name, href);
      }
    } catch (e) { /* ignore */ }
  });
  </script>

</body>

</html>
