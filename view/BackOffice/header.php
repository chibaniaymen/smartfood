<?php
// Determine the base path for KaiaAdmin assets and links relative to the web root
// Assuming the project root is /smartfood (or / if running directly via php -S)
$baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
// If the project is inside a subfolder like /smartfood, we need to append it.
// We can auto-detect it by looking at the script name.
$scriptName = $_SERVER['SCRIPT_NAME'];
$projectRoot = substr($scriptName, 0, strpos($scriptName, '/', 1));
if ($projectRoot !== '/view' && $projectRoot !== '/dashboard.php' && $projectRoot !== '/events.php') {
    $baseUrl .= $projectRoot;
}

$kaiadminAssets = $baseUrl . '/view/BackOffice/dashboard_kaiadmin/assets';

// Determine active page for sidebar highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$currentAction = $_GET['action'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SmartFood Admin Dashboard</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="<?php echo $kaiadminAssets; ?>/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="<?php echo $kaiadminAssets; ?>/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["<?php echo $kaiadminAssets; ?>/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo $kaiadminAssets; ?>/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $kaiadminAssets; ?>/css/plugins.min.css" />
    <link rel="stylesheet" href="<?php echo $kaiadminAssets; ?>/css/kaiadmin.min.css" />

    <style>
      /* SmartFood brand color overrides */
      .btn-primary {
        background-color: #ffbe33 !important;
        border-color: #ffbe33 !important;
        color: #222831 !important;
        font-weight: bold;
      }
      .btn-primary:hover {
        background-color: #e69c00 !important;
        border-color: #e69c00 !important;
        color: #222831 !important;
      }
      .sidebar .nav > .nav-item.active > a {
        background: rgba(255, 190, 51, 0.15);
        border-left: 3px solid #ffbe33;
      }
      .sidebar .nav > .nav-item.active > a i,
      .sidebar .nav > .nav-item.active > a p {
        color: #ffbe33 !important;
      }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="<?php echo $baseUrl; ?>/view/BackOffice/dashboard.php" class="logo">
              <span style="color:#ffbe33; font-size:20px; font-weight:700;">
                🍽️ SmartFood
              </span>
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <!-- Dashboard -->
              <li class="nav-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="<?php echo $baseUrl; ?>/view/BackOffice/dashboard.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>

              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Management</h4>
              </li>

              <!-- Events -->
              <li class="nav-item <?php echo $currentPage == 'events.php' ? 'active' : ''; ?>">
                <a href="<?php echo $baseUrl; ?>/events.php?action=list">
                  <i class="fas fa-calendar-alt"></i>
                  <p>Manage Events</p>
                </a>
              </li>

              <!-- Locations -->
              <li class="nav-item <?php echo $currentPage == 'locations.php' ? 'active' : ''; ?>">
                <a href="<?php echo $baseUrl; ?>/locations.php?action=list">
                  <i class="fas fa-map-marker-alt"></i>
                  <p>Manage Locations</p>
                </a>
              </li>

              <!-- Smart Optimization -->
              <li class="nav-item <?php echo $currentPage == 'optimizer.php' ? 'active' : ''; ?>">
                <a href="<?php echo $baseUrl; ?>/optimizer.php">
                  <i class="fas fa-brain"></i>
                  <p>Smart Optimization</p>
                </a>
              </li>

              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Components</h4>
              </li>

              <!-- Base Components -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-layer-group"></i>
                  <p>Base</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/avatars.html"><span class="sub-item">Avatars</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/buttons.html"><span class="sub-item">Buttons</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/gridsystem.html"><span class="sub-item">Grid System</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/panels.html"><span class="sub-item">Panels</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/notifications.html"><span class="sub-item">Notifications</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/sweetalert.html"><span class="sub-item">Sweet Alert</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/font-awesome-icons.html"><span class="sub-item">Font Awesome Icons</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/simple-line-icons.html"><span class="sub-item">Simple Line Icons</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/components/typography.html"><span class="sub-item">Typography</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Sidebar Layouts -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#sidebarLayouts">
                  <i class="fas fa-th-list"></i>
                  <p>Sidebar Layouts</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="sidebarLayouts">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/sidebar-style-2.html"><span class="sub-item">Sidebar Style 2</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/icon-menu.html"><span class="sub-item">Icon Menu</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Forms -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#forms">
                  <i class="fas fa-pen-square"></i>
                  <p>Forms</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="forms">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/forms/forms.html"><span class="sub-item">Basic Form</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Tables -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#tables">
                  <i class="fas fa-table"></i>
                  <p>Tables</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="tables">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/tables/tables.html"><span class="sub-item">Basic Table</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/tables/datatables.html"><span class="sub-item">Datatables</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Maps -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#maps">
                  <i class="fas fa-map-marker-alt"></i>
                  <p>Maps</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="maps">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/maps/googlemaps.html"><span class="sub-item">Google Maps</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/maps/jsvectormap.html"><span class="sub-item">Jsvectormap</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Charts -->
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts">
                  <i class="far fa-chart-bar"></i>
                  <p>Charts</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts">
                  <ul class="nav nav-collapse">
                    <li><a href="view/BackOffice/dashboard_kaiadmin/charts/charts.html"><span class="sub-item">Chart Js</span></a></li>
                    <li><a href="view/BackOffice/dashboard_kaiadmin/charts/sparkline.html"><span class="sub-item">Sparkline</span></a></li>
                  </ul>
                </div>
              </li>

              <!-- Widgets -->
              <li class="nav-item">
                <a href="view/BackOffice/dashboard_kaiadmin/widgets.html">
                  <i class="fas fa-desktop"></i>
                  <p>Widgets</p>
                  <span class="badge badge-success">4</span>
                </a>
              </li>

              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Navigation</h4>
              </li>

              <!-- Website Home -->
              <li class="nav-item">
                <a href="<?php echo $baseUrl; ?>/index.php">
                  <i class="fas fa-globe"></i>
                  <p>Website Home</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <div class="logo-header" data-background-color="dark">
              <a href="<?php echo $baseUrl; ?>/view/BackOffice/dashboard.php" class="logo">
                <span style="color:#ffbe33; font-size:18px; font-weight:700;">🍽️ SmartFood</span>
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
          </div>
          <!-- Navbar Header -->
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input type="text" placeholder="Search ..." class="form-control" />
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                  <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input type="text" placeholder="Search ..." class="form-control" />
                      </div>
                    </form>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-envelope"></i>
                  </a>
                  <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                    <li>
                      <div class="dropdown-title d-flex justify-content-between align-items-center">
                        Messages
                        <a href="#" class="small">Mark all as read</a>
                      </div>
                    </li>
                    <li>
                      <div class="message-notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="#">
                            <div class="notif-img">
                              <img src="<?php echo $kaiadminAssets; ?>/img/jm_denis.jpg" alt="Img Profile" />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jimmy Denis</span>
                              <span class="block"> How are you ? </span>
                              <span class="time">5 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img src="<?php echo $kaiadminAssets; ?>/img/chadengle.jpg" alt="Img Profile" />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Chad</span>
                              <span class="block"> Ok, Thanks ! </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img src="<?php echo $kaiadminAssets; ?>/img/mlane.jpg" alt="Img Profile" />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jhon Doe</span>
                              <span class="block">Ready for the meeting today...</span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img src="<?php echo $kaiadminAssets; ?>/img/talha.jpg" alt="Img Profile" />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Talha</span>
                              <span class="block"> Hi, Apa Kabar ? </span>
                              <span class="time">17 minutes ago</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="javascript:void(0);">See all messages<i class="fa fa-angle-right"></i></a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bell"></i>
                    <span class="notification">4</span>
                  </a>
                  <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                    <li>
                      <div class="dropdown-title">You have 4 new notifications</div>
                    </li>
                    <li>
                      <div class="notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="#">
                            <div class="notif-icon notif-primary"><i class="fa fa-user-plus"></i></div>
                            <div class="notif-content">
                              <span class="block"> New user registered </span>
                              <span class="time">5 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-icon notif-success"><i class="fa fa-comment"></i></div>
                            <div class="notif-content">
                              <span class="block">New event was created</span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img src="<?php echo $kaiadminAssets; ?>/img/profile2.jpg" alt="Img Profile" />
                            </div>
                            <div class="notif-content">
                              <span class="block">Reza send messages to you</span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-icon notif-danger"><i class="fa fa-heart"></i></div>
                            <div class="notif-content">
                              <span class="block"> Farrah liked Admin </span>
                              <span class="time">17 minutes ago</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i></a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fas fa-layer-group"></i>
                  </a>
                  <div class="dropdown-menu quick-actions animated fadeIn">
                    <div class="quick-actions-header">
                      <span class="title mb-1">Quick Actions</span>
                      <span class="subtitle op-7">Shortcuts</span>
                    </div>
                    <div class="quick-actions-scroll scrollbar-outer">
                      <div class="quick-actions-items">
                        <div class="row m-0">
                          <a class="col-6 col-md-4 p-0" href="<?php echo $baseUrl; ?>/events.php?action=add">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-danger rounded-circle"><i class="far fa-calendar-alt"></i></div>
                              <span class="text">New Event</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="<?php echo $baseUrl; ?>/locations.php?action=add">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-warning rounded-circle"><i class="fas fa-map"></i></div>
                              <span class="text">New Location</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="<?php echo $baseUrl; ?>/events.php?action=list">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-info rounded-circle"><i class="fas fa-file-excel"></i></div>
                              <span class="text">Reports</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-success rounded-circle"><i class="fas fa-envelope"></i></div>
                              <span class="text">Emails</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-primary rounded-circle"><i class="fas fa-file-invoice-dollar"></i></div>
                              <span class="text">Invoice</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-secondary rounded-circle"><i class="fas fa-credit-card"></i></div>
                              <span class="text">Payments</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                      <img src="<?php echo $kaiadminAssets; ?>/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                    </span>
                  </a>
                  <a href="<?php echo $baseUrl; ?>/logout.php" 
                     style="background:#e53e3e;color:white;padding:6px 16px; border-radius:6px;text-decoration:none;font-size:13px; transition:all 0.2s; margin-left: 15px;">
                    Déconnexion
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img src="<?php echo $kaiadminAssets; ?>/img/profile.jpg" alt="image profile" class="avatar-img rounded" />
                          </div>
                          <div class="u-text">
                            <h4>Admin</h4>
                            <p class="text-muted">admin@smartfood.com</p>
                            <a href="#" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Balance</a>
                        <a class="dropdown-item" href="#">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">

