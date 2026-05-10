<?php
require_once __DIR__ . '/../../controller/EventController.php';
require_once __DIR__ . '/../../controller/LocationController.php';

$eventController = new EventController();
$locationController = new LocationController();

$events = $eventController->list();
$locations = $locationController->list();
$eventStats = $eventController->getStats();
$locationStats = $locationController->getStats();

$totalEvents = (int)($eventStats['total'] ?? 0);
$activeEvents = (int)($eventStats['active'] ?? 0);
$totalLocations = (int)($locationStats['total'] ?? 0);
$totalCapacity = (int)($locationStats['total_capacity'] ?? 0);
$totalRevenue = array_reduce($events, fn($sum, $event) => $sum + ((float)($event['price'] ?? 0)), 0.0);
$upcomingEvents = array_filter($events, fn($event) => !empty($event['event_date']) && strtotime($event['event_date']) > time());

include 'header.php';
$a = $kaiadminAssets;
?>
<style>
@keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
.db-page { animation:fadeUp 0.5s ease both; }

/* Header Bar */
.db-header { background:linear-gradient(135deg,#1a1e2e,#2d3561); border-radius:16px; padding:24px 30px; margin-bottom:24px; position:relative; overflow:hidden; }
.db-header::before { content:''; position:absolute; top:-40px; right:-40px; width:140px; height:140px; border-radius:50%; background:rgba(102,126,234,0.08); }
.db-header h3 { color:#fff; font-weight:800; font-size:1.5rem; margin:0; }
.db-header p { color:rgba(255,255,255,0.45); font-size:0.88rem; margin:4px 0 0; }
.db-header .btn-hdr { border-radius:10px; font-weight:600; font-size:0.82rem; padding:9px 20px; border:1px solid rgba(255,255,255,0.12); transition:all 0.3s ease; }
.db-header .btn-hdr:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,0.2); }
.db-header .btn-stats-toggle { background:rgba(40,167,69,0.2); color:#55d77a; border-color:rgba(40,167,69,0.3); }
.db-header .btn-manage { background:rgba(23,162,184,0.2); color:#4dd0e1; border-color:rgba(23,162,184,0.3); }
.db-header .btn-add-ev { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; border:none; }
.db-header .btn-add-ev:hover { box-shadow:0 6px 20px rgba(102,126,234,0.4); }

/* 3D Stat Cards */
.stat3d { border:none; border-radius:18px; overflow:hidden; position:relative; transition:all 0.4s cubic-bezier(.25,.8,.25,1); cursor:default; animation:fadeUp 0.5s ease both; }
.stat3d:hover { transform:translateY(-8px) scale(1.02); }
.stat3d .stat3d-bg { padding:26px 22px; position:relative; z-index:1; color:#fff; }
.stat3d .stat3d-icon { width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff; background:rgba(255,255,255,0.2); backdrop-filter:blur(8px); box-shadow:0 8px 20px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.3); border:1px solid rgba(255,255,255,0.25); }
.stat3d .stat3d-value { font-size:2.2rem; font-weight:800; line-height:1; text-shadow:0 2px 8px rgba(0,0,0,0.12); }
.stat3d .stat3d-label { font-size:0.78rem; opacity:0.85; font-weight:500; letter-spacing:0.5px; text-transform:uppercase; }
.stat3d::before { content:''; position:absolute; top:-35px; right:-35px; width:110px; height:110px; border-radius:50%; background:rgba(255,255,255,0.07); z-index:0; }
.stat3d::after { content:''; position:absolute; bottom:-25px; left:-15px; width:70px; height:70px; border-radius:50%; background:rgba(255,255,255,0.05); z-index:0; }
.stat3d .stat3d-bar { height:4px; border-radius:2px; background:rgba(255,255,255,0.18); margin-top:12px; overflow:hidden; }
.stat3d .stat3d-bar-fill { height:100%; border-radius:2px; background:rgba(255,255,255,0.55); }

/* Tables Premium */
.db-page .card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.06); overflow:hidden; }
.db-page .table thead { background:linear-gradient(135deg,#1a1e2e,#2d3561); }
.db-page .table thead th { color:#c8d0e0 !important; font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.7px; padding:13px 12px; border:none; }
.db-page .table tbody tr { transition:all 0.2s ease; border-left:3px solid transparent; }
.db-page .table tbody tr:hover { background:#f7f8fc !important; border-left-color:#667eea; }
.db-page .table tbody td { padding:12px; vertical-align:middle; color:#525f7f; font-size:0.88rem; }
.db-page .badge { font-size:0.73rem; padding:5px 12px; border-radius:6px; font-weight:600; }
.db-page .badge.bg-success { background:linear-gradient(135deg,#28a745,#55d77a) !important; }
.db-page .badge.bg-danger { background:linear-gradient(135deg,#dc3545,#ff6b6b) !important; }
.db-page .badge.bg-secondary { background:linear-gradient(135deg,#6c757d,#adb5bd) !important; }

/* Team Members */
.db-page .item-list { transition:all 0.2s ease; border-radius:10px; padding:6px 8px; margin:0 -8px; }
.db-page .item-list:hover { background:#f7f8fc; }
.db-page .item-list .avatar-img { border:2px solid #eef0f8; transition:all 0.3s ease; }
.db-page .item-list:hover .avatar-img { border-color:#667eea; transform:scale(1.08); }

/* Gallery */
.db-page .img-fluid { border-radius:12px !important; transition:all 0.3s ease; }
.db-page .img-fluid:hover { transform:scale(1.05); box-shadow:0 8px 25px rgba(0,0,0,0.15); }
</style>

<div class="db-page">

<!-- Premium Header -->
<div class="db-header d-flex align-items-center flex-column flex-md-row gap-3">
  <div class="flex-grow-1">
    <h3><i class="fas fa-tachometer-alt me-2" style="color:#667eea;"></i>SmartFood Dashboard</h3>
    <p>Overview of your events & locations — real-time analytics</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <button class="btn btn-hdr btn-stats-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#chartsCollapse">
      <i class="fas fa-chart-line me-1"></i> Statistiques
    </button>
    <a href="<?php echo $baseUrl; ?>/events.php?action=list" class="btn btn-hdr btn-manage">
      <i class="fas fa-list me-1"></i> Manage Events
    </a>
      <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/FrontOffice/recette/listRecettes.php" class="btn btn-hdr btn-manage">
        <i class="fas fa-utensils me-1"></i> Manage Recettes
      </a>
      <a href="<?php echo $baseUrl; ?>/recette+ingredient/View/BackOffice/ingredient/listingredient.php" class="btn btn-hdr btn-manage">
        <i class="fas fa-carrot me-1"></i> Manage Ingredients
      </a>
    <a href="<?php echo $baseUrl; ?>/events.php?action=add" class="btn btn-hdr btn-add-ev">
      <i class="fas fa-plus me-1"></i> Add Event
    </a>
  </div>
</div>

<!-- 3D Stats -->
<div class="row mb-3">
  <div class="col-sm-6 col-md-3 mb-3">
    <div class="stat3d" style="animation-delay:0s;">
      <div class="stat3d-bg" style="background:linear-gradient(135deg,#667eea,#764ba2); box-shadow:0 10px 30px rgba(102,126,234,0.35);">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="stat3d-icon"><i class="fas fa-calendar-alt"></i></div>
          <div class="text-end"><div class="stat3d-value"><?= $totalEvents ?></div></div>
        </div>
        <div class="stat3d-label">Total Events</div>
        <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:100%;"></div></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3 mb-3">
    <div class="stat3d" style="animation-delay:0.1s;">
      <div class="stat3d-bg" style="background:linear-gradient(135deg,#11998e,#38ef7d); box-shadow:0 10px 30px rgba(17,153,142,0.35);">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="stat3d-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="text-end"><div class="stat3d-value"><?= $totalLocations ?></div></div>
        </div>
        <div class="stat3d-label">Locations</div>
        <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:<?= $totalLocations > 0 ? min(100, $totalLocations * 20) : 0 ?>%;"></div></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3 mb-3">
    <div class="stat3d" style="animation-delay:0.2s;">
      <div class="stat3d-bg" style="background:linear-gradient(135deg,#f7971e,#ffd200); box-shadow:0 10px 30px rgba(247,151,30,0.35);">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="stat3d-icon"><i class="fas fa-check-circle"></i></div>
          <div class="text-end"><div class="stat3d-value"><?= $activeEvents ?></div></div>
        </div>
        <div class="stat3d-label">Active Events</div>
        <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:<?= $totalEvents > 0 ? round(($activeEvents/$totalEvents)*100) : 0 ?>%;"></div></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3 mb-3">
    <div class="stat3d" style="animation-delay:0.3s;">
      <div class="stat3d-bg" style="background:linear-gradient(135deg,#ee5a24,#f0932b); box-shadow:0 10px 30px rgba(238,90,36,0.35);">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="stat3d-icon"><i class="fas fa-users"></i></div>
          <div class="text-end"><div class="stat3d-value"><?= number_format($totalCapacity) ?></div></div>
        </div>
        <div class="stat3d-label">Total Capacity</div>
        <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:80%;"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row (Hidden by default, toggled via button) -->
<div class="collapse" id="chartsCollapse">
  <div class="row">
    <div class="col-md-8">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-head-row">
            <div class="card-title">Event Statistics</div>
            <div class="card-tools">
              <a href="#" class="btn btn-label-success btn-round btn-sm me-2"><span class="btn-label"><i class="fa fa-pencil"></i></span>Export</a>
              <a href="#" class="btn btn-label-info btn-round btn-sm"><span class="btn-label"><i class="fa fa-print"></i></span>Print</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container" style="min-height: 375px"><canvas id="statisticsChart"></canvas></div>
          <div id="myChartLegend"></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-primary card-round">
        <div class="card-header">
          <div class="card-head-row">
            <div class="card-title">Revenue</div>
          </div>
          <div class="card-category">From all events</div>
        </div>
        <div class="card-body pb-0">
          <div class="mb-4 mt-2"><h1>€<?= number_format($totalRevenue, 2) ?></h1></div>
          <div class="pull-in"><canvas id="dailySalesChart"></canvas></div>
        </div>
      </div>
      <div class="card card-round">
        <div class="card-body pb-0">
          <div class="h1 fw-bold float-end text-primary">+5%</div>
          <h2 class="mb-2"><?= count($upcomingEvents) ?></h2>
          <p class="text-muted">Upcoming Events</p>
          <div class="pull-in sparkline-fix"><div id="lineChart"></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Events Table + Locations Table -->
<div class="row">
  <div class="col-md-8">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row card-tools-still-right">
          <h4 class="card-title">Events Table</h4>
          <div class="card-tools">
            <a href="<?php echo $baseUrl; ?>/events.php?action=add" class="btn btn-primary btn-sm btn-round">+ Add Event</a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr><th>ID</th><th>Title</th><th>Date</th><th>Location</th><th>Price</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($events as $e): ?>
              <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['title']) ?></td>
                <td><?= ($e['event_date'] && $e['event_date'] != '0000-00-00 00:00:00') ? date('d M Y, H:i', strtotime($e['event_date'])) : 'N/A' ?></td>
                <td><?= htmlspecialchars($e['location_name'] ?? 'N/A') ?></td>
                <td><?= $e['price'] ? '€'.$e['price'] : 'Free' ?></td>
                <td>
                  <?php if ($e['status']=='active'): ?><span class="badge bg-success">Active</span>
                  <?php elseif ($e['status']=='cancelled'): ?><span class="badge bg-danger">Cancelled</span>
                  <?php else: ?><span class="badge bg-secondary"><?= ucfirst($e['status']) ?></span><?php endif; ?>
                </td>
                <td>
                  <a href="<?php echo $baseUrl; ?>/events.php?action=edit&id=<?= $e['id'] ?>" class="btn btn-link btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                  <a href="<?php echo $baseUrl; ?>/events.php?action=delete&id=<?= $e['id'] ?>" class="btn btn-link btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fa fa-times"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-round">
      <div class="card-body">
        <div class="card-head-row card-tools-still-right">
          <div class="card-title">Team Members</div>
        </div>
        <div class="card-list py-4">
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Jimmy Denis</div><div class="status">Event Manager</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Chad</div><div class="status">Chef Coordinator</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/talha.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Talha</div><div class="status">Front End Designer</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/mlane.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Jhon Doe</div><div class="status">Marketing</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/sauro.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Sauro</div><div class="status">Logistics</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
          <div class="item-list">
            <div class="avatar"><img src="<?= $a ?>/img/arashmil.jpg" alt="..." class="avatar-img rounded-circle" /></div>
            <div class="info-user ms-3"><div class="username">Arash</div><div class="status">Support</div></div>
            <button class="btn btn-icon btn-link op-8 me-1"><i class="far fa-envelope"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Locations Table + Geolocation Map -->
<div class="row">
  <div class="col-md-7">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row card-tools-still-right">
          <h4 class="card-title">Locations Table</h4>
          <div class="card-tools">
            <a href="<?php echo $baseUrl; ?>/locations.php?action=add" class="btn btn-primary btn-sm btn-round">+ Add Location</a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr><th>ID</th><th>Name</th><th>Address</th><th>City</th><th>Country</th><th>Capacity</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($locations as $l): ?>
              <tr>
                <td><?= $l['id'] ?></td>
                <td><?= htmlspecialchars($l['name']) ?></td>
                <td><?= htmlspecialchars($l['address']) ?></td>
                <td><?= htmlspecialchars($l['city']) ?></td>
                <td><?= htmlspecialchars($l['country']) ?></td>
                <td><?= $l['capacity'] ?></td>
                <td>
                  <a href="<?php echo $baseUrl; ?>/locations.php?action=edit&id=<?= $l['id'] ?>" class="btn btn-link btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                  <a href="<?php echo $baseUrl; ?>/locations.php?action=delete&id=<?= $l['id'] ?>" class="btn btn-link btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fa fa-times"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card card-round">
      <div class="card-header">
        <div class="card-head-row card-tools-still-right">
          <h4 class="card-title">Users Geolocation</h4>
        </div>
        <p class="card-category">Distribution of users worldwide</p>
      </div>
      <div class="card-body">
        <div class="table-responsive table-hover table-sales">
          <table class="table">
            <tbody>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/fr.png" alt="france"/></div></td><td>France</td><td class="text-end">2,320</td><td class="text-end">42.18%</td></tr>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/us.png" alt="usa"/></div></td><td>USA</td><td class="text-end">240</td><td class="text-end">4.36%</td></tr>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/gb.png" alt="uk"/></div></td><td>UK</td><td class="text-end">119</td><td class="text-end">2.16%</td></tr>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/de.png" alt="germany"/></div></td><td>Germany</td><td class="text-end">1,081</td><td class="text-end">19.65%</td></tr>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/it.png" alt="italy"/></div></td><td>Italy</td><td class="text-end">1,100</td><td class="text-end">20%</td></tr>
              <tr><td><div class="flag"><img src="<?= $a ?>/img/flags/es.png" alt="spain"/></div></td><td>Spain</td><td class="text-end">640</td><td class="text-end">11.63%</td></tr>
            </tbody>
          </table>
        </div>
        <div class="mapcontainer"><div id="world-map" class="w-100" style="height: 250px"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- Gallery Row - Using all product/example images -->
<div class="row">
  <div class="col-md-12">
    <div class="card card-round">
      <div class="card-header"><h4 class="card-title">Event Gallery</h4></div>
      <div class="card-body">
        <div class="row">
          <?php
          $imgs = ['examples/example1-300x300.jpg','examples/example2-300x300.jpg','examples/example3-300x300.jpg',
            'examples/example4-300x300.jpg','examples/example5-300x300.jpg','examples/example6-300x300.jpg',
            'examples/example7-300x300.jpg','examples/example8-300x300.jpg','examples/example9-300x300.jpg',
            'examples/example10-300x300.jpg','examples/example11-300x300.jpg','examples/example12-300x300.jpg'];
          foreach ($imgs as $i => $img): ?>
          <div class="col-md-2 col-4 mb-3">
            <img src="<?= $a ?>/img/<?= $img ?>" alt="Gallery <?= $i+1 ?>" class="img-fluid rounded shadow-sm" style="width:100%;height:120px;object-fit:cover;">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Products Row -->
<div class="row">
  <div class="col-md-12">
    <div class="card card-round">
      <div class="card-header"><h4 class="card-title">Featured Products</h4></div>
      <div class="card-body">
        <div class="row">
          <?php for ($p=1; $p<=8; $p++): $ext = $p<=5 ? 'jpg' : ($p<=8 ? 'jpg' : 'jpeg'); ?>
          <div class="col-md-3 col-6 mb-3">
            <div class="card shadow-sm">
              <img src="<?= $a ?>/img/examples/product<?= $p ?>.<?= $ext ?>" alt="Product <?= $p ?>" class="card-img-top" style="height:160px;object-fit:cover;">
              <div class="card-body p-2 text-center"><small class="fw-bold">Product <?= $p ?></small></div>
            </div>
          </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Blog & Illustrations Row -->
<div class="row">
  <div class="col-md-4">
    <div class="card card-round">
      <img src="<?= $a ?>/img/blogpost.jpg" class="card-img-top" alt="Blog" style="height:200px;object-fit:cover;">
      <div class="card-body">
        <h5 class="card-title">Latest Blog Post</h5>
        <p class="card-text text-muted">Discover tips for organizing the perfect food event this season.</p>
        <a href="#" class="btn btn-primary btn-sm btn-round">Read More</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-round text-center p-4">
      <img src="<?= $a ?>/img/undraw/undraw_creative_team_r90h.svg" alt="Team" style="height:180px;" class="mx-auto">
      <div class="card-body"><h5>Creative Team</h5><p class="text-muted">Our team is ready to help you succeed.</p></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-round text-center p-4">
      <img src="<?= $a ?>/img/undraw/undraw_upgrade_06a0.svg" alt="Upgrade" style="height:180px;" class="mx-auto">
      <div class="card-body"><h5>Upgrade Your Plan</h5><p class="text-muted">Unlock premium features for your events.</p></div>
    </div>
  </div>
</div>

<!-- More Illustrations -->
<div class="row">
  <div class="col-md-3">
    <div class="card card-round text-center p-3">
      <img src="<?= $a ?>/img/undraw/undraw_Hello_qnas.svg" alt="Hello" style="height:120px;" class="mx-auto mb-2">
      <small class="fw-bold">Welcome!</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-round text-center p-3">
      <img src="<?= $a ?>/img/undraw/undraw_tabs_jf82.svg" alt="Tabs" style="height:120px;" class="mx-auto mb-2">
      <small class="fw-bold">Organize</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-round text-center p-3">
      <img src="<?= $a ?>/img/undraw/undraw_update_uxn2.svg" alt="Update" style="height:120px;" class="mx-auto mb-2">
      <small class="fw-bold">Stay Updated</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-round text-center p-3">
      <img src="<?= $a ?>/img/undraw/undraw_blank_canvas_3rbb.svg" alt="Canvas" style="height:120px;" class="mx-auto mb-2">
      <small class="fw-bold">Create</small>
    </div>
  </div>
</div>

<!-- Sparkline Cards -->
<div class="row">
  <div class="col-md-4">
    <div class="card card-round">
      <div class="card-body">
        <h2 class="mb-2">€<?= number_format($totalRevenue, 0) ?></h2>
        <p class="text-muted">Total Revenue</p>
        <div class="pull-in sparkline-fix"><div id="lineChart2"></div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-round">
      <div class="card-body">
        <h2 class="mb-2"><?= $totalLocations ?></h2>
        <p class="text-muted">Active Venues</p>
        <div class="pull-in sparkline-fix"><div id="lineChart3"></div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-round">
      <div class="card-body text-center p-4">
        <img src="<?= $a ?>/img/undraw/undraw_sign_in_e6hj.svg" alt="Sign In" style="height:120px;" class="mx-auto mb-3">
        <h5>Secure Access</h5>
        <p class="text-muted small">Your dashboard is protected.</p>
      </div>
    </div>
  </div>
</div>

<!-- Logo & Visa images -->
<div class="row mb-4">
  <div class="col-md-3 text-center">
    <div class="card card-round p-3"><img src="<?= $a ?>/img/logoproduct.svg" alt="Logo" style="height:60px;"></div>
  </div>
  <div class="col-md-3 text-center">
    <div class="card card-round p-3"><img src="<?= $a ?>/img/logoproduct3.svg" alt="Logo3" style="height:60px;"></div>
  </div>
  <div class="col-md-3 text-center">
    <div class="card card-round p-3"><img src="<?= $a ?>/img/visa.svg" alt="Visa" style="height:60px;"></div>
  </div>
  <div class="col-md-3 text-center">
    <div class="card card-round p-3"><img src="<?= $a ?>/img/kaiadmin/logo_dark.svg" alt="KaiaAdmin" style="height:40px;"></div>
  </div>
</div>

</div><!-- /db-page -->
<?php include 'footer.php'; ?>

<!-- Dashboard Charts Script -->
<script>
$(document).ready(function(){
  // Event Statistics Chart
  var ctx = document.getElementById('statisticsChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
      datasets: [{
        label: "Events", borderColor: '#f3545d', pointBackgroundColor: 'rgba(243,84,93,.6)',
        pointRadius: 0, backgroundColor: 'rgba(243,84,93,.4)', legendColor: '#f3545d',
        fill: true, borderWidth: 2,
        data: [<?php
          $m = array_fill(0,12,0);
          foreach($events as $ev){ $mi = (int)date('n', strtotime($ev['event_date']))-1; $m[$mi]++; }
          echo implode(',', $m);
        ?>]
      },{
        label: "Revenue (€)", borderColor: '#fdaf4b', pointBackgroundColor: 'rgba(253,175,75,.6)',
        pointRadius: 0, backgroundColor: 'rgba(253,175,75,.4)', legendColor: '#fdaf4b',
        fill: true, borderWidth: 2,
        data: [<?php
          $r = array_fill(0,12,0);
          foreach($events as $ev){ $mi = (int)date('n', strtotime($ev['event_date']))-1; $r[$mi]+=$ev['price']??0; }
          echo implode(',', $r);
        ?>]
      }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:true}},
      scales:{y:{beginAtZero:true}} }
  });

  // Daily Sales Chart
  var ctx2 = document.getElementById('dailySalesChart').getContext('2d');
  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
      datasets: [{label:"Sales", backgroundColor:'rgba(255,255,255,0.4)', borderColor:'rgba(255,255,255,0)',
        data:[65,59,80,81,56,55,40], borderWidth:0, borderRadius:3}]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
      scales:{x:{display:false},y:{display:false}} }
  });

  // Sparklines
  $("#lineChart").sparkline([102,109,120,99,110,105,115],{type:"line",height:"70",width:"100%",lineWidth:"2",lineColor:"#177dff",fillColor:"rgba(23,125,255,0.14)"});
  $("#lineChart2").sparkline([99,125,122,105,110,124,115],{type:"line",height:"70",width:"100%",lineWidth:"2",lineColor:"#f3545d",fillColor:"rgba(243,84,93,.14)"});
  $("#lineChart3").sparkline([105,103,123,100,95,105,115],{type:"line",height:"70",width:"100%",lineWidth:"2",lineColor:"#ffa534",fillColor:"rgba(255,165,52,.14)"});

  // World Map
  if(typeof jsVectorMap !== 'undefined'){
    new jsVectorMap({
      map:"world", selector:"#world-map",
      zoomButtons:false,
      regionStyle:{initial:{fill:"#d1d5db"},hover:{fillOpacity:1,fill:"#ffbe33"}},
      labels:{regions:{render:function(e){return e}}},
    });
  }
});
</script>
