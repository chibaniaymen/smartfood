<?php
/**
 * Layout partagé BackOffice — Header + Sidebar Kaiadmin
 * Requiert : $pageTitle (string), $activeMenu (string)
 */
if (!defined('BO_ACCESS')) {
    header('Location: ../dashboard.php');
    exit;
}

// Calcul dynamique du chemin relatif vers assets
$callerFile = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? __FILE__;
$callerDir  = dirname(realpath($callerFile));
$boDir      = realpath(__DIR__ . '/..');
$relParts   = explode(DIRECTORY_SEPARATOR, trim(str_replace($boDir, '', $callerDir), DIRECTORY_SEPARATOR));
$relDepth   = ($relParts[0] === '') ? 0 : count($relParts);
$assetPath  = str_repeat('../', $relDepth + 1) . 'BackOffice/assets';
$backPath   = str_repeat('../', $relDepth);  // chemin vers BackOffice/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no"/>
  <title><?php echo htmlspecialchars($pageTitle ?? 'Admin'); ?> — SmartFood Admin</title>

  <script src="<?php echo $assetPath; ?>/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"],
        urls: ["<?php echo $assetPath; ?>/css/fonts.min.css"]
      },
      active: function(){ sessionStorage.fonts = true; }
    });
  </script>

  <link rel="stylesheet" href="<?php echo $assetPath; ?>/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="<?php echo $assetPath; ?>/css/plugins.min.css"/>
  <link rel="stylesheet" href="<?php echo $assetPath; ?>/css/kaiadmin.min.css"/>

  <style>
    :root { --sf-green:#2D6A4F; --sf-orange:#E76F51; --sf-green-lt:#52B788; }
    .sf-active > a { background:rgba(149,213,178,.15) !important; border-radius:8px; color:#95D5B2 !important; }
    .sf-active > a i { color:#95D5B2 !important; }
    .page-header-sf { background:white; padding:20px 28px; border-radius:12px; margin-bottom:24px; box-shadow:0 2px 10px rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
    .page-header-sf h1 { font-size:1.3rem; font-weight:700; color:#1a1a2e; margin:0; }
    .breadcrumb-sf { font-size:.8rem; color:#adb5bd; margin:4px 0 0; }
    .breadcrumb-sf a { color:var(--sf-green); text-decoration:none; }
    .page-inner { padding-top:10px !important; }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- SIDEBAR -->
  <div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
      <div class="logo-header" data-background-color="dark">
        <a href="<?php echo $backPath; ?>dashboard.php" class="logo" style="text-decoration:none;display:flex;align-items:center;gap:8px;">
          <span style="font-size:1.4rem;">🥦</span>
          <span style="font-size:1.2rem;font-weight:700;color:#fff;">Smart<span style="color:#95D5B2;">Food</span></span>
        </a>
        <div class="nav-toggle">
          <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
          <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
        </div>
        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
      </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
        <ul class="nav nav-secondary">

          <li class="nav-item <?php echo $activeMenu==='dashboard' ? 'sf-active active' : ''; ?>">
            <a href="<?php echo $backPath; ?>dashboard.php">
              <i class="fas fa-home"></i><p>Tableau de bord</p>
            </a>
          </li>

          <li class="nav-section">
            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
            <h4 class="text-section">Contenu</h4>
          </li>

          <li class="nav-item <?php echo str_contains($activeMenu,'article') ? 'sf-active active' : ''; ?>">
            <a data-bs-toggle="collapse" href="#menuArticles"
               class="<?php echo str_contains($activeMenu,'article') ? '' : 'collapsed'; ?>"
               aria-expanded="<?php echo str_contains($activeMenu,'article') ? 'true' : 'false'; ?>">
              <i class="fas fa-newspaper"></i><p>Articles</p><span class="caret"></span>
            </a>
            <div class="collapse <?php echo str_contains($activeMenu,'article') ? 'show' : ''; ?>" id="menuArticles">
              <ul class="nav nav-collapse">
                <li class="<?php echo $activeMenu==='article-list' ? 'active' : ''; ?>">
                  <a href="<?php echo $backPath; ?>Article/list.php"><span class="sub-item">Liste des articles</span></a>
                </li>
                <li class="<?php echo $activeMenu==='article-add' ? 'active' : ''; ?>">
                  <a href="<?php echo $backPath; ?>Article/add.php"><span class="sub-item">Ajouter un article</span></a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item <?php echo str_contains($activeMenu,'comment') ? 'sf-active active' : ''; ?>">
            <a data-bs-toggle="collapse" href="#menuCommentaires"
               class="<?php echo str_contains($activeMenu,'comment') ? '' : 'collapsed'; ?>"
               aria-expanded="<?php echo str_contains($activeMenu,'comment') ? 'true' : 'false'; ?>">
              <i class="fas fa-comments"></i><p>Commentaires</p><span class="caret"></span>
            </a>
            <div class="collapse <?php echo str_contains($activeMenu,'comment') ? 'show' : ''; ?>" id="menuCommentaires">
              <ul class="nav nav-collapse">
                <li class="<?php echo $activeMenu==='comment-list' ? 'active' : ''; ?>">
                  <a href="<?php echo $backPath; ?>Commentaire/list.php"><span class="sub-item">Liste des commentaires</span></a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-section">
            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
            <h4 class="text-section">Site</h4>
          </li>

          <li class="nav-item">
            <a href="<?php echo $backPath; ?>../FrontOffice/index.php" target="_blank">
              <i class="fas fa-external-link-alt"></i><p>Voir le site</p>
            </a>
          </li>

        </ul>
      </div>
    </div>
  </div>
  <!-- END SIDEBAR -->

  <div class="main-panel">

    <!-- TOPBAR -->
    <div class="main-header">
      <div class="main-header-logo">
        <div class="logo-header" data-background-color="dark">
          <a href="<?php echo $backPath; ?>dashboard.php" class="logo" style="text-decoration:none;display:flex;align-items:center;gap:6px;">
            <span style="font-size:1.2rem;">🥦</span>
            <span style="font-size:1rem;font-weight:700;color:#fff;">Smart<span style="color:#95D5B2;">Food</span></span>
          </a>
          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
          </div>
          <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
      </div>
      <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
          <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
              <div class="input-group-prepend">
                <button type="button" class="btn btn-search pe-1"><i class="fa fa-search search-icon"></i></button>
              </div>
              <input type="text" placeholder="Rechercher..." class="form-control"/>
            </div>
          </nav>
          <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

            <li class="nav-item topbar-icon dropdown hidden-caret">
              <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                 data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell"></i>
                <span class="notification">2</span>
              </a>
              <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                <li><div class="dropdown-title">2 nouvelles notifications</div></li>
                <li>
                  <div class="notif-scroll scrollbar-outer">
                    <div class="notif-center">
                      <a href="<?php echo $backPath; ?>Commentaire/list.php">
                        <div class="notif-icon notif-success"><i class="fa fa-comment"></i></div>
                        <div class="notif-content">
                          <span class="block">Nouveau commentaire reçu</span>
                          <span class="time">Blog SmartFood</span>
                        </div>
                      </a>
                      <a href="<?php echo $backPath; ?>Article/add.php">
                        <div class="notif-icon notif-primary"><i class="fa fa-newspaper"></i></div>
                        <div class="notif-content">
                          <span class="block">Publier un nouvel article</span>
                          <span class="time">Action rapide</span>
                        </div>
                      </a>
                    </div>
                  </div>
                </li>
                <li><a class="see-all" href="<?php echo $backPath; ?>Commentaire/list.php">Tous les commentaires <i class="fa fa-angle-right"></i></a></li>
              </ul>
            </li>

            <li class="nav-item topbar-user dropdown hidden-caret">
              <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                <div class="avatar-sm">
                  <span style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#52B788,#2D6A4F);color:white;font-weight:700;">AD</span>
                </div>
                <span class="profile-username">
                  <span class="op-7">Hi,</span>
                  <span class="fw-bold">Admin</span>
                </span>
              </a>
              <ul class="dropdown-menu dropdown-user animated fadeIn">
                <div class="dropdown-user-scroll scrollbar-outer">
                  <li>
                    <div class="user-box">
                      <div class="avatar-lg" style="display:flex;align-items:center;justify-content:center;width:55px;height:55px;border-radius:8px;background:linear-gradient(135deg,#52B788,#2D6A4F);font-size:1.5rem;">🥦</div>
                      <div class="u-text">
                        <h4>SmartFood</h4>
                        <p class="text-muted">admin@smartfood.tn</p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo $backPath; ?>dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    <a class="dropdown-item" href="<?php echo $backPath; ?>Article/list.php"><i class="fas fa-newspaper me-2"></i>Articles</a>
                    <a class="dropdown-item" href="<?php echo $backPath; ?>Commentaire/list.php"><i class="fas fa-comments me-2"></i>Commentaires</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo $backPath; ?>../FrontOffice/index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Voir le site</a>
                  </li>
                </div>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </div>
    <!-- END TOPBAR -->

    <div class="container">
      <div class="page-inner">
