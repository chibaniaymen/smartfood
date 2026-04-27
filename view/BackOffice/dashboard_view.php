<?php include 'header.php'; $a = $kaiadminAssets; ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
  <div>
    <h3 class="fw-bold mb-3">SmartFood Dashboard</h3>
    <h6 class="op-7 mb-2">Overview of your events &amp; locations</h6>
  </div>
  <div class="ms-md-auto py-2 py-md-0">
    <button class="btn btn-success btn-round me-2" type="button" data-bs-toggle="collapse" data-bs-target="#chartsCollapse" aria-expanded="false" aria-controls="chartsCollapse">
      <i class="fas fa-chart-line"></i> Afficher les Statistiques
    </button>
    <a href="<?php echo $baseUrl; ?>/events.php?action=list" class="btn btn-label-info btn-round me-2">Manage Events</a>
    <a href="<?php echo $baseUrl; ?>/events.php?action=add" class="btn btn-primary btn-round">Add Event</a>
  </div>
</div>

<!-- Stats Cards -->
<div class="row">
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-primary bubble-shadow-small"><i class="fas fa-calendar-alt"></i></div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers"><p class="card-category">Total Events</p><h4 class="card-title"><?= $totalEvents ?></h4></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-info bubble-shadow-small"><i class="fas fa-map-marker-alt"></i></div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers"><p class="card-category">Locations</p><h4 class="card-title"><?= $totalLocations ?></h4></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-check-circle"></i></div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers"><p class="card-category">Active Events</p><h4 class="card-title"><?= $activeEvents ?></h4></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-icon">
            <div class="icon-big text-center icon-secondary bubble-shadow-small"><i class="fas fa-users"></i></div>
          </div>
          <div class="col col-stats ms-3 ms-sm-0">
            <div class="numbers"><p class="card-category">Total Capacity</p><h4 class="card-title"><?= number_format($totalCapacity) ?></h4></div>
          </div>
        </div>
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
            <thead class="thead-light">
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
            <thead class="thead-light">
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

