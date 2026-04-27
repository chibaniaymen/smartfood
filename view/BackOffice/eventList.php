<?php include 'header.php'; ?>
<div class="page-header">
    <h3 class="fw-bold mb-3">Events List</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="index.html"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="events.php?action=list">Events</a></li>
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
                <div class="icon-big text-center icon-primary bubble-shadow-small"><i class="fas fa-calendar-alt"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Total Events</p><h4 class="card-title"><?= $stats['total'] ?></h4></div>
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
                <div class="numbers"><p class="card-category">Active Events</p><h4 class="card-title"><?= $stats['active'] ?></h4></div>
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
                <div class="icon-big text-center icon-warning bubble-shadow-small"><i class="fas fa-euro-sign"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Avg Price</p><h4 class="card-title">€<?= $stats['avg_price'] ?></h4></div>
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
                <div class="icon-big text-center icon-secondary bubble-shadow-small"><i class="far fa-clock"></i></div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers"><p class="card-category">Events This Month</p><h4 class="card-title"><?= $stats['this_month'] ?></h4></div>
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
            <h4 class="card-title">Event List</h4>
            
            <!-- Search Form & Actions -->
            <form action="events.php" method="GET" class="d-flex flex-grow-1 ms-4">
              <input type="hidden" name="action" value="list">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by title, desc, status..." value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Recherche</button>
                
                <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-sort"></i> Trier
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="events.php?action=list&sort=event_date&order=ASC&search=<?= urlencode($search ?? '') ?>">Date (Ascending)</a></li>
                  <li><a class="dropdown-item" href="events.php?action=list&sort=event_date&order=DESC&search=<?= urlencode($search ?? '') ?>">Date (Descending)</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="events.php?action=list&sort=price&order=ASC&search=<?= urlencode($search ?? '') ?>">Price (Low to High)</a></li>
                  <li><a class="dropdown-item" href="events.php?action=list&sort=price&order=DESC&search=<?= urlencode($search ?? '') ?>">Price (High to Low)</a></li>
                </ul>

                <!-- New Statistique Button -->
                <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#statsCollapse" aria-expanded="false" aria-controls="statsCollapse">
                  <i class="fas fa-chart-bar"></i> Statistiques
                </button>

                <?php if(!empty($search)): ?>
                  <a href="events.php?action=list" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
              </div>
            </form>

            <a href="events.php?action=add" class="btn btn-primary btn-round ms-auto">
              <i class="fa fa-plus"></i> Add Event
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="thead-light">
                <tr>
                  <th><a href="events.php?action=list&sort=id&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">ID <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=title&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Title <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=event_date&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Date <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=location_name&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Location <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=price&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Price <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=status&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Status <i class="fa fa-sort"></i></a></th>
                  <th>Actions</th>
                </tr>
              </thead>
                        <tbody>
                            <?php foreach ($events as $e): ?>
                            <tr>
                                <td><?php echo $e['id']; ?></td>
                                <td><?php echo htmlspecialchars($e['title']); ?></td>
                                <td><?php echo ($e['event_date'] && $e['event_date'] != '0000-00-00 00:00:00') ? date('Y-m-d H:i', strtotime($e['event_date'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($e['location_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $e['price'] ? '€'.$e['price'] : 'Free'; ?></td>
                                <td>
                                    <?php if ($e['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($e['status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo ucfirst($e['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="events.php?action=edit&id=<?php echo $e['id']; ?>" class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="events.php?action=delete&id=<?php echo $e['id']; ?>" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this event?');">
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
