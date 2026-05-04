<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Controller/ArticleController.php';

$articleController = new ArticleController($pdo);
$articles          = $articleController->getAll(true);
$blogImages        = ['blog_1.png', 'blog_2.png', 'blog_3.png', 'blog_4.png'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>SmartFood — Blog Nutrition &amp; Bien-être</title>
  <meta name="description" content="SmartFood - Votre blog dédié à la nutrition saine, recettes et bien-être. Découvrez nos articles et conseils."/>
  <link rel="icon" href="images/favicon.png" type="image/png"/>

  <!-- Feane CSS -->
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/responsive.css"/>
  
  <!-- 3D Library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

  <style>
    /* ── 3D Background Blobs ── */
    .blob-c {
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      z-index: -1;
      overflow: hidden;
      background: #0c0c0c;
    }
    .blob {
      position: absolute;
      width: 500px; height: 500px;
      background: linear-gradient(135deg, #ffbe33 0%, #e69c00 100%);
      filter: blur(80px);
      border-radius: 50%;
      opacity: 0.15;
      animation: float3d 20s infinite alternate;
    }
    .blob-1 { top: -100px; right: -100px; background: #ffbe33; }
    .blob-2 { bottom: -150px; left: -100px; background: #2D6A4F; animation-delay: -5s; }

    @keyframes float3d {
      0% { transform: translate(0, 0) scale(1); }
      33% { transform: translate(-50px, 50px) scale(1.1); }
      66% { transform: translate(50px, -30px) scale(0.9); }
      100% { transform: translate(0, 0) scale(1); }
    }

    /* ── Overrides SmartFood branding on Feane base ── */
    :root {
      --feane-yellow: #ffbe33;
      --feane-dark:   #222831;
      --feane-white:  #ffffff;
    }

    /* Navbar brand */
    .navbar-brand span {
      color: var(--feane-yellow);
    }

    /* order_online_btn (Feane style) */
    .order_online_btn {
      display: inline-block;
      padding: 8px 30px;
      background-color: var(--feane-yellow);
      color: #ffffff !important;
      border-radius: 45px;
      transition: all 0.3s;
      font-weight: 600;
      font-size: 0.9rem;
    }
    .order_online_btn:hover {
      background-color: #e6a800;
      color: #fff !important;
    }

    /* Hero Background Overlay (to hide the burger) */
    .hero_area .bg-box {
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8));
    }
    .hero_area .bg-box img {
      opacity: 0.3; /* Make the burger very subtle or almost invisible */
      filter: blur(5px);
    }

    /* Alert success */
    .alert-success-sf {
      background: #d4edda;
      border: none;
      color: #155724;
      border-radius: 50px;
      margin-bottom: 30px;
      padding: 12px 24px;
      text-align: center;
    }

    /* Footer nav links */
    .footer_section ul { list-style: none; padding: 0; }
    .footer_section ul li a { color: rgba(255,255,255,0.75); transition: color 0.2s; }
    .footer_section ul li a:hover { color: var(--feane-yellow); }
    .footer_section ul li { margin-bottom: 8px; }
  </style>
</head>
<body>

<!-- Background Blobs -->
<div class="blob-c">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
</div>

<!-- ══════════════════════════════════════════════════
     HERO AREA  (dark background + hero-bg.jpg)
══════════════════════════════════════════════════ -->
<div class="hero_area">

  <!-- Background image box (Feane native pattern) -->
  <div class="bg-box">
    <img src="images/hero-bg.jpg" alt="hero background"/>
  </div>

  <!-- ════ NAVBAR ════ -->
  <header class="header_section">
    <div class="container-fluid">
      <nav class="navbar navbar-expand-lg custom_nav-container">

        <a class="navbar-brand" href="index.php">
          <span>SmartFood</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
          <span></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item active">
              <a class="nav-link" href="index.php">Accueil <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#articles">Articles</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">À propos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="searchCommentaires.php">Commentaires</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="addArticle.php">Blog</a>
            </li>
          </ul>

          <div class="user_option">
            <a href="#" class="user_link">
              <i class="fa fa-user" aria-hidden="true"></i>
            </a>
            <a href="#" class="cart_link">
              <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                   xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                   viewBox="0 0 100 100" xml:space="preserve">
                <g><path d="M75,67H31L20.3,25.5C20.1,24.6,19.3,24,18.4,24H10c-1.1,0-2,0.9-2,2s0.9,2,2,2h6.9L27.6,69.5c0.2,0.9,1,1.5,1.9,1.5h45.5
                   c1.1,0,2-0.9,2-2S76.1,67,75,67z"/><circle cx="35" cy="76" r="4"/><circle cx="69" cy="76" r="4"/>
                  <path d="M78.3,28H27.9l2.9,10H72c0.9,0,1.7,0.6,1.9,1.5l5,18c0.1,0.6,0,1.2-0.4,1.7C78.2,59.6,77.6,60,77,60H31c-1.1,0-2,0.9-2,2
                   s0.9,2,2,2h46.8c1.9,0,3.6-0.9,4.8-2.4c1.1-1.5,1.5-3.4,1-5.2l-5.4-19C78,28,78.2,28,78.3,28z"/></g>
              </svg>
            </a>
            <a href="#" class="nav_search-btn">
              <i class="fa fa-search" aria-hidden="true"></i>
            </a>
            <a href="../../View/BackOffice/dashboard.php" class="order_online_btn">
              Administration
            </a>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <!-- ════ END NAVBAR ════ -->

  <!-- ════ SLIDER / HERO ════ -->
  <section class="slider_section">
    <div id="customCarousel1" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">

        <!-- Slide 1 — SmartFood hero -->
        <div class="carousel-item active">
          <div class="container">
            <div class="row">
              <div class="col-md-10 mx-auto">
                <div class="detail-box" style="text-align: center;">
                  <h1>
                    Articles Nutrition<br/>
                    &amp; Bien-être
                  </h1>
                  <p>
                    Découvrez nos derniers articles sur la santé, des conseils nutritionnels 
                    et des recettes équilibrées pour une vie plus saine. 
                    Tout ce qu'il faut pour nourrir votre corps et votre esprit.
                  </p>
                  <div>
                    <a href="#articles" id="btn-voir-articles">Explorer le Blog</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if (!empty($articles)): ?>
        <!-- Slide 2 — Article à la une -->
        <div class="carousel-item">
          <div class="container">
            <div class="row">
              <div class="col-md-10 mx-auto">
                <div class="detail-box" style="text-align: center;">
                  <h5 style="color:var(--feane-yellow);text-transform:uppercase;letter-spacing:2px;margin-bottom:12px;font-family:'Open Sans',sans-serif;">
                    Article à la une
                  </h5>
                  <h1 style="font-size:2.4rem;">
                    <?php echo htmlspecialchars(mb_substr($articles[0]['title'] ?? '', 0, 50)); ?>
                  </h1>
                  <p>
                    <?php echo htmlspecialchars(mb_substr($articles[0]['content'] ?? '', 0, 130)); ?>…
                  </p>
                  <div>
                    <a href="article.php?id=<?php echo $articles[0]['id'] ?? ''; ?>">
                      Lire l'article &rarr;
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($articles[1])): ?>
        <!-- Slide 3 — Second article -->
        <div class="carousel-item">
          <div class="container">
            <div class="row">
              <div class="col-md-9 mx-auto">
                <div class="detail-box" style="text-align: center;">
                  <h5 style="color:var(--feane-yellow);text-transform:uppercase;letter-spacing:2px;margin-bottom:12px;font-family:'Open Sans',sans-serif;">
                    Dernière Publication
                  </h5>
                  <h1 style="font-size:2.4rem;">
                    <?php echo htmlspecialchars(mb_substr($articles[1]['title'] ?? '', 0, 50)); ?>
                  </h1>
                  <p>
                    <?php echo htmlspecialchars(mb_substr($articles[1]['content'] ?? '', 0, 130)); ?>…
                  </p>
                  <div>
                    <a href="article.php?id=<?php echo $articles[1]['id'] ?? ''; ?>">
                      Lire l'article &rarr;
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div><!-- /.carousel-inner -->

      <ol class="carousel-indicators">
        <li data-target="#customCarousel1" data-slide-to="0" class="active"></li>
        <?php if (!empty($articles)): ?>
        <li data-target="#customCarousel1" data-slide-to="1"></li>
        <?php endif; ?>
        <?php if (!empty($articles[1])): ?>
        <li data-target="#customCarousel1" data-slide-to="2"></li>
        <?php endif; ?>
      </ol>
    </div>
  </section>
  <!-- ════ END SLIDER ════ -->

</div>
<!-- ══════════════════════════════════════════════════
     END HERO AREA
══════════════════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     ARTICLES SECTION  (Feane food_section style)
══════════════════════════════════════════════════ -->
<section id="articles" class="food_section layout_padding-bottom">
  <div class="container">
    <div class="heading_container heading_center">
      <h2>Nos <span>Derniers Articles</span></h2>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <?php
        $msg = '';
        if ($_GET['success'] == 'article_added')   $msg = 'Félicitations ! Votre article a été publié.';
        if ($_GET['success'] == 'article_updated') $msg = 'L\'article a été mis à jour.';
        if ($_GET['success'] == 'article_deleted') $msg = 'L\'article a été supprimé.';
      ?>
      <?php if ($msg): ?>
        <div class="alert-success-sf">
          <i class="fa fa-check-circle"></i> <?php echo $msg; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>


    <div class="filters-content">
      <div class="row grid">
        <?php foreach ($articles as $index => $art): ?>
        <?php
          $catClass = ($index % 3 == 0) ? 'recette' : (($index % 3 == 1) ? 'conseil' : 'nutrition');
          $imgName  = $blogImages[$index % 4];
        ?>
        <div class="col-sm-6 col-lg-4 all <?php echo $catClass; ?>">
          <div class="box">
            <div>
              <div class="img-box">
                <img src="images/<?php echo $imgName; ?>"
                     alt="<?php echo htmlspecialchars($art['title']); ?>"
                     style="object-fit: cover; width: 100%; height: 100%;">
              </div>
              <div class="detail-box">
                <div class="d-flex justify-content-between mb-2">
                  <?php 
                    $readTime = $articleController->calculateReadTime($art['content']); 
                    $trendingIds = array_column($articleController->getTrendingArticles(3), 'id');
                    $isTrending = in_array($art['id'], $trendingIds);
                  ?>
                  <small class="text-muted"><i class="fa fa-clock-o"></i> <?php echo $readTime; ?> min</small>
                  <?php if ($isTrending): ?>
                    <span class="badge bg-warning text-dark"><i class="fa fa-fire"></i> En vogue</span>
                  <?php endif; ?>
                </div>
                <h5><?php echo htmlspecialchars($art['title']); ?></h5>
                <p><?php echo htmlspecialchars(mb_substr(strip_tags($art['content']), 0, 80)); ?>…</p>
                <div class="options">
                  <h6 style="font-size:.75rem;">Posté le <?php echo date('d/m/y', strtotime($art['created_at'])); ?></h6>
                  <a href="article.php?id=<?php echo $art['id']; ?>">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </a>
                </div>
                <!-- Actions édition -->
                <div style="margin-top:12px;display:flex;gap:8px;justify-content:center;">
                  <a href="updateArticle.php?id=<?php echo $art['id']; ?>"
                     class="btn btn-sm btn-outline-warning"
                     style="border-radius:50px;padding:2px 10px;font-size:.8rem;">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <a href="deleteArticle.php?id=<?php echo $art['id']; ?>"
                     class="btn btn-sm btn-outline-danger"
                     style="border-radius:50px;padding:2px 10px;font-size:.8rem;"
                     onclick="return confirm('Supprimer cet article ?');">
                    <i class="fa fa-trash"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="btn-box">
      <a href="addArticle.php" id="btn-proposer-article">Proposer un article</a>
    </div>
  </div>
</section>
<!-- ══════════════════════════════════════════════════
     END ARTICLES SECTION
══════════════════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     ABOUT SECTION  (Feane about_section style)
══════════════════════════════════════════════════ -->
<section id="about" class="about_section layout_padding">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="img-box">
          <img src="images/about-img.png" alt="À propos de SmartFood"/>
        </div>
      </div>
      <div class="col-md-6">
        <div class="detail-box">
          <div class="heading_container">
            <h2>À propos de <span>SmartFood</span></h2>
          </div>
          <p>
            SmartFood est votre espace dédié à une alimentation saine et équilibrée.
            Notre mission est de vous fournir des conseils pratiques, des recettes
            savoureuses et des articles scientifiques pour vous aider à mieux manger
            au quotidien.
          </p>
          <p>
            Rejoignez notre communauté de passionnés de nutrition et découvrez comment
            transformer vos habitudes alimentaires sans sacrifier le plaisir de manger.
          </p>
          <a href="searchCommentaires.php">Nos commentaires</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ══════════════════════════════════════════════════
     END ABOUT SECTION
══════════════════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     OFFER / CONSEILS SECTION
══════════════════════════════════════════════════ -->
<section class="offer_section layout_padding-top" style="padding-bottom:60px;">
  <div class="offer_container">
    <div class="container">
      <div class="heading_container heading_center" style="margin-bottom:0;">
        <h2>Nos <span>Conseils Nutrition</span></h2>
      </div>
      <div class="row" style="margin-top:30px;">
        <?php
        $tips = [
          ['icon'=>'blog_1.png','title'=>'Légumes verts',     'desc'=>'Riches en fibres et vitamines essentielles pour votre santé.'],
          ['icon'=>'blog_2.png','title'=>'Vitamines',      'desc'=>'Des nutriments essentiels à intégrer au quotidien.'],
          ['icon'=>'blog_4.png','title'=>'Bons lipides',      'desc'=>'Avocats, noix et huile d\'olive pour votre équilibre.'],
        ];
        foreach ($tips as $t): ?>
        <div class="col-lg-4">
          <div class="box">
            <div class="img-box">
              <img src="images/<?php echo $t['icon']; ?>"
                   alt="<?php echo $t['title']; ?>"
                   style="object-fit: cover; border-radius: 100%; width: 175px; height: 175px;"/>
            </div>
            <div class="detail-box">
              <h5><?php echo $t['title']; ?></h5>
              <p><?php echo $t['desc']; ?></p>
              <a href="#articles">En savoir plus
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 100 100">
                  <path d="M58,50L32,25l7-7l33,32L39,82l-7-7L58,50z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<!-- ══════════════════════════════════════════════════
     END OFFER SECTION
══════════════════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     FOOTER SECTION  (Feane footer_section style)
══════════════════════════════════════════════════ -->
<section class="footer_section">
  <div class="container">
    <div class="row">
      <!-- Col 1 — Brand -->
      <div class="col-md-4 footer-col">
        <a class="footer-logo" href="index.php">SmartFood</a>
        <p>
          Votre blog dédié à la nutrition saine et au bien-être.
          Découvrez nos articles, conseils et recettes équilibrées.
        </p>
        <div class="footer_social">
          <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
          <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
          <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
          <a href="#"><i class="fa fa-youtube" aria-hidden="true"></i></a>
        </div>
      </div>

      <!-- Col 2 — Navigation -->
      <div class="col-md-4 footer-col">
        <h4>Navigation</h4>
        <ul>
          <li><a href="index.php">Accueil</a></li>
          <li><a href="#articles">Articles</a></li>
          <li><a href="#about">À propos</a></li>
          <li><a href="searchCommentaires.php">Commentaires</a></li>
          <li><a href="../../View/BackOffice/dashboard.php">Administration</a></li>
        </ul>
      </div>

      <!-- Col 3 — Contact -->
      <div class="col-md-4 footer-col">
        <h4>Contact</h4>
        <div class="footer_contact">
          <div class="contact_link_box">
            <a href="#">
              <i class="fa fa-map-marker" aria-hidden="true"></i>
              <span>Tunis, Tunisie</span>
            </a>
            <a href="#">
              <i class="fa fa-phone" aria-hidden="true"></i>
              <span>+216 70 000 000</span>
            </a>
            <a href="#">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              <span>contact@smartfood.tn</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-info">
      <p>
        &copy; <span id="displayYear"></span> SmartFood — Tous droits réservés.
      </p>
    </div>
  </div>
</section>
<!-- ══════════════════════════════════════════════════
     END FOOTER SECTION
══════════════════════════════════════════════════ -->


<!-- JS Feane -->
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>

<!-- Initialize 3D Animation (Vanilla Tilt) -->
<script>
  VanillaTilt.init(document.querySelectorAll(".box"), {
    max: 15,
    speed: 400,
    glare: true,
    "max-glare": 0.2,
    scale: 1.05
  });
</script>

</body>
</html>

