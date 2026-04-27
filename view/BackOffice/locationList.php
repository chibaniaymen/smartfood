<?php include 'header.php'; ?>
<div class="page-header">
    <h3 class="fw-bold mb-3">Locations List</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="index.html"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="locations.php?action=list">Locations</a></li>
    </ul>
</div>
  <!-- Stats Cards (Hidden by default, toggled via button) -->
  <div class="collapse" id="statsCollapse">
    <div class="row">
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small"><i class="fas fa-map-marker-alt"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Total Locations</p><h4 class="card-title"><?= $stats['total'] ?></h4></div>
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
                <div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-users"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Total Capacity</p><h4 class="card-title"><?= number_format($stats['total_capacity']) ?></h4></div>
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
                <div class="icon-big text-center icon-warning bubble-shadow-small"><i class="fas fa-city"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Cities</p><h4 class="card-title"><?= $stats['cities'] ?></h4></div>
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
                <div class="icon-big text-center icon-secondary bubble-shadow-small"><i class="fas fa-building"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Max Capacity</p><h4 class="card-title"><?= number_format($stats['max_capacity']) ?></h4></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h4 class="card-title">Location List</h4>
            
            <!-- Search Form & Actions -->
            <form action="locations.php" method="GET" class="d-flex flex-grow-1 ms-4">
              <input type="hidden" name="action" value="list">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, city..." value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Recherche</button>
                
                <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-sort"></i> Trier
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="locations.php?action=list&sort=name&order=ASC&search=<?= urlencode($search ?? '') ?>">Name (A-Z)</a></li>
                  <li><a class="dropdown-item" href="locations.php?action=list&sort=name&order=DESC&search=<?= urlencode($search ?? '') ?>">Name (Z-A)</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="locations.php?action=list&sort=capacity&order=DESC&search=<?= urlencode($search ?? '') ?>">Capacity (Highest First)</a></li>
                  <li><a class="dropdown-item" href="locations.php?action=list&sort=capacity&order=ASC&search=<?= urlencode($search ?? '') ?>">Capacity (Lowest First)</a></li>
                </ul>

                <!-- New Statistique Button -->
                <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#statsCollapse" aria-expanded="false" aria-controls="statsCollapse">
                  <i class="fas fa-chart-bar"></i> Statistiques
                </button>

                <?php if(!empty($search)): ?>
                  <a href="locations.php?action=list" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
              </div>
            </form>

            <a href="locations.php?action=add" class="btn btn-primary btn-round ms-auto">
              <i class="fa fa-plus"></i> Add Location
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="thead-light">
                <tr>
                  <th><a href="locations.php?action=list&sort=id&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">ID <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=name&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Name <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=address&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Address <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=city&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">City <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=capacity&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Capacity <i class="fa fa-sort"></i></a></th>
                  <th>Actions</th>
                </tr>
              </thead>
                        <tbody>
                            <?php foreach ($locations as $l): ?>
                            <tr>
                                <td><?php echo $l['id']; ?></td>
                                <td><?php echo htmlspecialchars($l['name']); ?></td>
                                <td><?php echo htmlspecialchars($l['address']); ?></td>
                                <td><?php echo htmlspecialchars($l['city']); ?></td>
                                <td><?php echo $l['capacity']; ?></td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="locations.php?action=edit&id=<?php echo $l['id']; ?>" class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="locations.php?action=delete&id=<?php echo $l['id']; ?>" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this location?');">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
