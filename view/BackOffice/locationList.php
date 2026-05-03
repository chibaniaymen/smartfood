<?php include 'header.php'; ?>
<?php 
require_once __DIR__ . '/../../controller/EventController.php';
$eventController = new EventController();
$allEvents = $eventController->list(); 
?>
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
.loc-page { animation: fadeUp 0.5s ease both; }
.loc-page .card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.06); overflow:hidden; }
.loc-page .card-header { background:#fff; border-bottom:1px solid #eef0f8; padding:20px 24px; }
.loc-page .card-header .card-title { font-weight:700; color:#32325d; font-size:1.15rem; }
.loc-page .toolbar-header { background:linear-gradient(135deg,#1a1e2e,#2d3561); border-radius:16px 16px 0 0 !important; padding:18px 24px !important; border-bottom:none !important; }
.loc-page .toolbar-header .card-title { color:#fff !important; font-size:1.1rem; margin:0; white-space:nowrap; }
.loc-page .toolbar-header .form-control { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); color:#fff; border-radius:10px 0 0 10px; padding:9px 16px; font-size:0.88rem; }
.loc-page .toolbar-header .form-control::placeholder { color:rgba(255,255,255,0.4); }
.loc-page .toolbar-header .form-control:focus { background:rgba(255,255,255,0.18); border-color:rgba(17,153,142,0.6); box-shadow:0 0 0 3px rgba(17,153,142,0.2); color:#fff; }
.loc-page .toolbar-header .btn { border-radius:8px; font-weight:600; font-size:0.82rem; padding:8px 16px; border:1px solid rgba(255,255,255,0.15); transition:all 0.25s ease; }
.loc-page .toolbar-header .btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
.loc-page .toolbar-header .btn-search { background:linear-gradient(135deg,#11998e,#38ef7d); border:none; color:#fff; border-radius:0 10px 10px 0; }
.loc-page .toolbar-header .btn-sort { background:rgba(23,162,184,0.2); color:#4dd0e1; border-color:rgba(23,162,184,0.3); }
.loc-page .toolbar-header .btn-stats { background:rgba(40,167,69,0.2); color:#55d77a; border-color:rgba(40,167,69,0.3); }
.loc-page .toolbar-header .btn-clear { background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); }
.loc-page .toolbar-header .btn-add { background:linear-gradient(135deg,#11998e,#38ef7d); border:none; color:#fff; border-radius:10px; padding:9px 22px; font-weight:700; }
.loc-page .toolbar-header .btn-add:hover { box-shadow:0 6px 20px rgba(17,153,142,0.4); }
.loc-page .toolbar-header .dropdown-menu { background:#1e2235; border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:8px; margin-top:8px !important; }
.loc-page .toolbar-header .dropdown-item { color:#c8d0e0; border-radius:6px; padding:8px 14px; font-size:0.85rem; transition:all 0.2s ease; }
.loc-page .toolbar-header .dropdown-item:hover { background:rgba(17,153,142,0.2); color:#fff; }
.loc-page .toolbar-header .dropdown-divider { border-color:rgba(255,255,255,0.08); }
.loc-page .table thead { background:linear-gradient(135deg,#1a1e2e,#2d3561); }
.loc-page .table thead th { color:#fff !important; font-weight:600; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.8px; padding:14px 12px; border:none; }
.loc-page .table thead th a { color:#c8d0e0 !important; text-decoration:none; }
.loc-page .table thead th a:hover { color:#fff !important; }
.loc-page .table thead th a i { color:#667eea; }
.loc-page .table tbody tr { transition:all 0.2s ease; border-left:3px solid transparent; }
.loc-page .table tbody tr:hover { background:#f7f8fc !important; border-left-color:#11998e; transform:translateX(2px); }
.loc-page .table tbody tr[data-bs-toggle='collapse'] { cursor:pointer; }
.loc-page .table tbody td { padding:14px 12px; vertical-align:middle; color:#525f7f; font-size:0.9rem; }
.loc-page .badge { font-size:0.75rem; padding:5px 12px; border-radius:6px; font-weight:600; letter-spacing:0.3px; }
.loc-page .badge.bg-success { background:linear-gradient(135deg,#28a745,#55d77a) !important; }
.loc-page .badge.bg-danger { background:linear-gradient(135deg,#dc3545,#ff6b6b) !important; }
.loc-page .badge.bg-warning { background:linear-gradient(135deg,#f7971e,#ffd200) !important; }
.loc-page .card-stats { border-radius:14px; border:none; transition:all 0.3s ease; animation:fadeUp 0.4s ease both; }
.loc-page .card-stats:hover { transform:translateY(-4px); box-shadow:0 10px 30px rgba(0,0,0,0.1); }

/* 3D Stats */
.stat3d { border:none; border-radius:18px; overflow:hidden; position:relative; transition:all 0.4s cubic-bezier(.25,.8,.25,1); cursor:default; }
.stat3d:hover { transform:translateY(-8px) scale(1.02); }
.stat3d .stat3d-bg { padding:28px 24px; position:relative; z-index:1; color:#fff; }
.stat3d .stat3d-icon { width:64px; height:64px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.6rem; color:#fff; background:rgba(255,255,255,0.2); backdrop-filter:blur(8px); box-shadow:0 8px 24px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.3); border:1px solid rgba(255,255,255,0.25); }
.stat3d .stat3d-value { font-size:2.4rem; font-weight:800; line-height:1; text-shadow:0 2px 8px rgba(0,0,0,0.15); }
.stat3d .stat3d-label { font-size:0.82rem; opacity:0.85; font-weight:500; letter-spacing:0.5px; text-transform:uppercase; }
.stat3d::before { content:''; position:absolute; top:-40px; right:-40px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.08); z-index:0; }
.stat3d::after { content:''; position:absolute; bottom:-30px; left:-20px; width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.06); z-index:0; }
.stat3d .stat3d-bar { height:4px; border-radius:2px; background:rgba(255,255,255,0.2); margin-top:14px; overflow:hidden; }
.stat3d .stat3d-bar-fill { height:100%; border-radius:2px; background:rgba(255,255,255,0.6); }
.loc-page .form-button-action .btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; margin:0 2px; transition:all 0.2s ease; }
.loc-page .form-button-action .btn:hover { transform:scale(1.15); }
.loc-page .input-group .form-control { border-radius:10px 0 0 10px; border-color:#dde1f0; }
.loc-page .input-group .form-control:focus { border-color:#11998e; box-shadow:0 0 0 3px rgba(17,153,142,0.15); }
.loc-page .btn-primary { background:linear-gradient(135deg,#11998e,#38ef7d); border:none; border-radius:10px; font-weight:600; padding:8px 20px; transition:all 0.3s ease; }
.loc-page .btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(17,153,142,0.35); }
.loc-page .collapse td { border-left:3px solid #11998e; }
.loc-page .collapse .bg-light { background:linear-gradient(135deg,#f7f8fc,#eef0f8) !important; border-radius:0 0 12px 12px; }
.loc-page .fa-info-circle { color:#667eea !important; transition:transform 0.2s ease; }
.loc-page tr:hover .fa-info-circle { transform:scale(1.3); }
/* AI Styles */
.btn-ai { background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;border:none;border-radius:8px;font-size:0.75rem;padding:5px 10px;font-weight:600;transition:all 0.3s; }
.btn-ai:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(17,153,142,0.3);color:#fff; }
.btn-ai-sm { font-size:0.7rem;padding:3px 8px; }
.match-card { border:none;border-radius:14px;overflow:hidden;transition:all 0.3s ease;box-shadow:0 4px 20px rgba(0,0,0,0.06); }
.match-card:hover { transform:translateY(-6px);box-shadow:0 12px 30px rgba(0,0,0,0.12); }
.match-score { font-size:1.8rem;font-weight:800;background:linear-gradient(135deg,#11998e,#38ef7d);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
.match-section { background:linear-gradient(135deg,#f8f9fc,#eef0f8);border-radius:16px;padding:30px;margin-top:20px;border:1px solid #eef0f8; }
</style>

<div class="loc-page">
<div class="page-header">
    <h3 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2" style="color:#11998e;"></i> Locations List</h3>
    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="index.php"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="locations.php?action=list">Locations</a></li>
    </ul>
</div>
  <div class="collapse" id="statsCollapse">
    <div class="row mb-3">
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#11998e,#38ef7d); box-shadow:0 10px 30px rgba(17,153,142,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-map-marker-alt"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= $stats['total'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Total Locations</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:100%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.1s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#667eea,#764ba2); box-shadow:0 10px 30px rgba(102,126,234,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-users"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= number_format($stats['total_capacity']) ?></div>
              </div>
            </div>
            <div class="stat3d-label">Total Capacity</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:75%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.2s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#f7971e,#ffd200); box-shadow:0 10px 30px rgba(247,151,30,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-city"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= $stats['cities'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Cities</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:50%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.3s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#ee5a24,#f0932b); box-shadow:0 10px 30px rgba(238,90,36,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-building"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= number_format($stats['max_capacity']) ?></div>
              </div>
            </div>
            <div class="stat3d-label">Max Capacity</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:85%;"></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header toolbar-header">
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <h4 class="card-title"><i class="fas fa-map-marker-alt me-2" style="color:#38ef7d;"></i>Location List</h4>
            
            <form action="locations.php" method="GET" class="d-flex flex-grow-1">
              <input type="hidden" name="action" value="list">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="🔍  Rechercher un lieu..." value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-search"><i class="fa fa-search me-1"></i> Recherche</button>
              </div>
            </form>

            <div class="d-flex gap-2">
                <div class="dropdown">
                  <button class="btn btn-sort dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-sort me-1"></i> Trier
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="locations.php?action=list&sort=name&order=ASC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-sort-alpha-down me-2" style="color:#4dd0e1;"></i>Name (A-Z)</a></li>
                    <li><a class="dropdown-item" href="locations.php?action=list&sort=name&order=DESC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-sort-alpha-up me-2" style="color:#4dd0e1;"></i>Name (Z-A)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="locations.php?action=list&sort=capacity&order=DESC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-arrow-up me-2" style="color:#55d77a;"></i>Capacity (Highest)</a></li>
                    <li><a class="dropdown-item" href="locations.php?action=list&sort=capacity&order=ASC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-arrow-down me-2" style="color:#ff6b6b;"></i>Capacity (Lowest)</a></li>
                  </ul>
                </div>

                <button class="btn btn-stats" type="button" data-bs-toggle="collapse" data-bs-target="#statsCollapse" aria-expanded="false" aria-controls="statsCollapse">
                  <i class="fas fa-chart-bar me-1"></i> Stats
                </button>

                <?php if(!empty($search)): ?>
                  <a href="locations.php?action=list" class="btn btn-clear"><i class="fas fa-times me-1"></i> Clear</a>
                <?php endif; ?>

                <a href="locations.php?action=add" class="btn btn-add">
                  <i class="fa fa-plus me-1"></i> Add Location
                </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th><a href="locations.php?action=list&sort=id&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">ID <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=name&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Name <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=city&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">City <i class="fa fa-sort"></i></a></th>
                  <th><a href="locations.php?action=list&sort=capacity&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Capacity <i class="fa fa-sort"></i></a></th>
                  <th>Disponibilité (Ce mois)</th>
                  <th>Actions</th>
                </tr>
              </thead>
                        <tbody>
                            <?php foreach ($locations as $l): ?>
                            <?php
                                // Logic for events linked to this location
                                $locEvents = array_filter($allEvents, function($e) use ($l) {
                                    return $e['location_id'] == $l['id'] && strtotime($e['event_date']) >= time();
                                });
                                usort($locEvents, function($a, $b) { return strtotime($a['event_date']) - strtotime($b['event_date']); });

                                $currentMonthEvents = array_filter($allEvents, function($e) use ($l) {
                                    return $e['location_id'] == $l['id'] && date('Y-m', strtotime($e['event_date'])) == date('Y-m');
                                });
                                $totalAttendeesThisMonth = array_sum(array_column($currentMonthEvents, 'max_attendees'));

                                if ($totalAttendeesThisMonth == 0) {
                                    $availBadge = '<span class="badge bg-success">Libre</span>';
                                } elseif ($totalAttendeesThisMonth >= $l['capacity']) {
                                    $availBadge = '<span class="badge bg-danger">Complet</span>';
                                } else {
                                    $availBadge = '<span class="badge bg-warning text-dark">Partiel</span>';
                                }

                                $upcoming3 = array_slice($locEvents, 0, 3);
                                $tooltipText = empty($upcoming3) ? 'Aucun événement à venir' : implode(' | ', array_map(function($e) { return htmlspecialchars($e['title']) . ' (' . date('d/m', strtotime($e['event_date'])) . ')'; }, $upcoming3));
                            ?>
                            <tr data-bs-toggle="collapse" data-bs-target="#collapseLoc<?php echo $l['id']; ?>" style="cursor: pointer;">
                                <td><?php echo $l['id']; ?></td>
                                <td data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($tooltipText); ?>">
                                    <strong><?php echo htmlspecialchars($l['name']); ?></strong> <i class="fas fa-info-circle text-muted ms-1"></i>
                                </td>
                                <td><?php echo htmlspecialchars($l['city']); ?></td>
                                <td><?php echo $l['capacity']; ?></td>
                                <td><?php echo $availBadge; ?></td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="locations.php?action=edit&id=<?php echo $l['id']; ?>" class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-link btn-primary" onclick="event.stopPropagation();generatePitch(<?php echo $l['id']; ?>,'<?php echo addslashes(htmlspecialchars($l['name'])); ?>','<?php echo addslashes(htmlspecialchars($l['city'])); ?>','<?php echo addslashes(htmlspecialchars($l['country'] ?? '')); ?>',<?php echo $l['capacity']; ?>)" data-bs-toggle="tooltip" title="Générer pitch IA">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                        <a href="locations.php?action=delete&id=<?php echo $l['id']; ?>" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this location?');">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="collapse" id="collapseLoc<?php echo $l['id']; ?>">
                                <td colspan="6" class="p-0">
                                    <div class="p-3 bg-light border-bottom">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-map-marker-alt text-primary"></i> Adresse complète:</strong><br>
                                                <?php echo htmlspecialchars($l['address']); ?>, <?php echo htmlspecialchars($l['city']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-calendar-alt text-primary"></i> Événements à venir:</strong>
                                                <?php if(empty($locEvents)): ?>
                                                    <p class="text-muted mb-0">Aucun événement prévu.</p>
                                                <?php else: ?>
                                                    <ul class="mb-0 ps-3">
                                                    <?php foreach($locEvents as $le): ?>
                                                        <li><?php echo htmlspecialchars($le['title']); ?> <small class="text-muted">(<?php echo date('d/m/Y H:i', strtotime($le['event_date'])); ?>)</small></li>
                                                    <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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

<!-- IA-5: Matching Section -->
<div class="match-section">
  <h5 class="fw-bold mb-3" style="color:#32325d;"><i class="fas fa-crosshairs me-2" style="color:#11998e;"></i>Trouver le meilleur lieu pour un événement</h5>
  <div class="row g-3 mb-3">
    <div class="col-md-3"><input type="text" id="matchTitle" class="form-control" placeholder="Titre de l'événement" style="border-radius:10px;"></div>
    <div class="col-md-3"><input type="text" id="matchDesc" class="form-control" placeholder="Description courte" style="border-radius:10px;"></div>
    <div class="col-md-2"><input type="number" id="matchPersons" class="form-control" placeholder="Nb personnes" min="1" style="border-radius:10px;"></div>
    <div class="col-md-2"><input type="number" id="matchBudget" class="form-control" placeholder="Budget max (€)" min="0" style="border-radius:10px;"></div>
    <div class="col-md-2"><button class="btn btn-ai w-100" style="padding:10px;font-size:0.88rem;" onclick="findBestMatch()" id="btnMatch"><i class="fas fa-search me-1"></i> Trouver</button></div>
  </div>
  <div id="matchResults" class="row g-3"></div>
</div>

<!-- Pitch Modal -->
<div class="modal fade" id="pitchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1a1e2e,#2d3561);border:none;">
        <h5 class="modal-title text-white"><i class="fas fa-bullhorn me-2" style="color:#38ef7d;"></i>Pitch Marketing IA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="pitchContent" style="font-size:1.05rem;line-height:1.7;color:#32325d;"></div>
      </div>
      <div class="modal-footer" style="border:none;">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-ai" onclick="copyPitch()"><i class="fas fa-copy me-1"></i> Copier</button>
      </div>
    </div>
  </div>
</div>

<script>
// --- Ollama AI Helper ---
async function callOllama(prompt) {
    try {
        const response = await fetch('ajax_ai.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt: prompt })
        });
        const data = await response.json();
        if (data.error) throw new Error(data.error);
        return data.response.replace(/```json|```/g, '').trim();
    } catch (error) {
        throw error;
    }
}
function showApiError(message) {
    const div = document.createElement('div');
    div.className = 'alert alert-danger alert-dismissible fade show mt-2';
    div.innerHTML = `❌ <strong>Erreur IA :</strong> ${message}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>`;
    document.querySelector('.loc-page').prepend(div);
}
function parseJSON(text) { try{const clean=text.replace(/```json|```/g,'').trim();const m=clean.match(/\{[\s\S]*\}/);return m?JSON.parse(m[0]):null;}catch(e){return null;} }
function setLoading(btn,loading,origHtml){if(loading){btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Analyse...';}else{btn.disabled=false;btn.innerHTML=origHtml;}}

// --- IA-4: Pitch Marketing ---
let currentPitchText = '';
async function generatePitch(id, name, city, country, capacity) {
    const modal = new bootstrap.Modal(document.getElementById('pitchModal'));
    document.getElementById('pitchContent').innerHTML = '<div class="text-center"><span class="spinner-border"></span><p class="mt-2 text-muted">Génération en cours...</p></div>';
    modal.show();
    try {
        const prompt = `Rédige un pitch marketing accrocheur (2-3 phrases, style événementiel luxe) pour ce lieu : Nom: ${name}, Ville: ${city}, Pays: ${country}, Capacité: ${capacity} personnes. Réponds uniquement avec le texte, sans guillemets ni backticks.`;
        const text = await callOllama(prompt);
        currentPitchText = text;
        document.getElementById('pitchContent').innerHTML = '<p style="font-style:italic;">' + text.replace(/\n/g, '<br>') + '</p>';
    } catch(e) {
        showApiError(e.message);
        document.getElementById('pitchContent').innerHTML = '<p class="text-danger">Erreur API</p>';
    }
}
function copyPitch() {
    navigator.clipboard.writeText(currentPitchText).then(() => {
        const btn = document.querySelector('#pitchModal .btn-ai');
        btn.innerHTML = '<i class="fas fa-check me-1"></i> Copié !';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy me-1"></i> Copier', 2000);
    });
}

// --- IA-5: Matching ---
async function findBestMatch() {
    const btn = document.getElementById('btnMatch');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    const resultsDiv = document.getElementById('matchResults');
    resultsDiv.innerHTML = '<div class="col-12 text-center p-4"><span class="spinner-border"></span><p class="mt-2 text-muted">Analyse IA en cours...</p></div>';
    try {
        const titre = document.getElementById('matchTitle').value;
        const desc = document.getElementById('matchDesc').value;
        const nb = document.getElementById('matchPersons').value;
        const budget = document.getElementById('matchBudget').value;
        const locations = <?php echo json_encode(array_map(function($l){return['id'=>$l['id'],'nom'=>$l['name'],'ville'=>$l['city'],'pays'=>$l['country']??'','capacite'=>$l['capacity']];}, $locations)); ?>;
        const prompt = `Tu es un conseiller événementiel expert. Un client cherche un lieu pour : Titre: ${titre}, Description: ${desc}, Personnes: ${nb}, Budget: ${budget}€. Lieux disponibles : ${JSON.stringify(locations)}. Recommande le TOP 3 des lieux les plus adaptés. Réponds UNIQUEMENT en JSON valide sans backticks : { "recommandations": [ { "id": NUMBER, "nom": STRING, "score": NUMBER, "raison": STRING } ] }`;
        const raw = await callOllama(prompt);
        const result = parseJSON(raw);
        if(result && result.recommandations) {
            let html = '';
            result.recommandations.forEach((r, i) => {
                const stars = '⭐'.repeat(Math.round(r.score / 20));
                const medals = ['🥇','🥈','🥉'];
                html += `<div class="col-md-4"><div class="match-card card"><div class="card-body text-center p-4"><div class="mb-2" style="font-size:2rem;">${medals[i]||''}</div><div class="match-score">${r.score}/100</div><div class="mb-2">${stars}</div><h6 class="fw-bold" style="color:#32325d;">${r.nom}</h6><p class="text-muted" style="font-size:0.88rem;">${r.raison}</p><a href="locations.php?action=show&id=${r.id}" class="btn btn-ai btn-ai-sm">👁️ Voir</a></div></div></div>`;
            });
            resultsDiv.innerHTML = html;
        } else { resultsDiv.innerHTML = '<div class="col-12 text-center text-danger">Erreur de parsing</div>'; }
    } catch(e) { showApiError(e.message); resultsDiv.innerHTML = '<div class="col-12 text-center text-danger">Erreur API: '+e.message+'</div>'; }
    finally { setLoading(btn, false, origHtml); }
}
</script>
</div><!-- /loc-page -->
<?php include 'footer.php'; ?>
