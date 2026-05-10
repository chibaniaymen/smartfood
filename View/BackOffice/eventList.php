<?php include 'header.php'; ?>
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
.ev-page { animation: fadeUp 0.5s ease both; }
.ev-page .card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.06); overflow:hidden; }
.ev-page .card-header { background:#fff; border-bottom:1px solid #eef0f8; padding:20px 24px; }
.ev-page .card-header .card-title { font-weight:700; color:#32325d; font-size:1.15rem; }
.ev-page .toolbar-header { background:linear-gradient(135deg,#1a1e2e,#2d3561); border-radius:16px 16px 0 0 !important; padding:18px 24px !important; border-bottom:none !important; }
.ev-page .toolbar-header .card-title { color:#fff !important; font-size:1.1rem; margin:0; white-space:nowrap; }
.ev-page .toolbar-header .form-control { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); color:#fff; border-radius:10px 0 0 10px; padding:9px 16px; font-size:0.88rem; }
.ev-page .toolbar-header .form-control::placeholder { color:rgba(255,255,255,0.4); }
.ev-page .toolbar-header .form-control:focus { background:rgba(255,255,255,0.18); border-color:rgba(102,126,234,0.6); box-shadow:0 0 0 3px rgba(102,126,234,0.2); color:#fff; }
.ev-page .toolbar-header .btn { border-radius:8px; font-weight:600; font-size:0.82rem; padding:8px 16px; border:1px solid rgba(255,255,255,0.15); transition:all 0.25s ease; }
.ev-page .toolbar-header .btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
.ev-page .toolbar-header .btn-search { background:linear-gradient(135deg,#667eea,#764ba2); border:none; color:#fff; border-radius:0 10px 10px 0; }
.ev-page .toolbar-header .btn-sort { background:rgba(23,162,184,0.2); color:#4dd0e1; border-color:rgba(23,162,184,0.3); }
.ev-page .toolbar-header .btn-stats { background:rgba(40,167,69,0.2); color:#55d77a; border-color:rgba(40,167,69,0.3); }
.ev-page .toolbar-header .btn-clear { background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); }
.ev-page .toolbar-header .btn-add { background:linear-gradient(135deg,#667eea,#764ba2); border:none; color:#fff; border-radius:10px; padding:9px 22px; font-weight:700; }
.ev-page .toolbar-header .btn-add:hover { box-shadow:0 6px 20px rgba(102,126,234,0.4); }
.ev-page .toolbar-header .dropdown-menu { background:#1e2235; border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:8px; margin-top:8px !important; }
.ev-page .toolbar-header .dropdown-item { color:#c8d0e0; border-radius:6px; padding:8px 14px; font-size:0.85rem; transition:all 0.2s ease; }
.ev-page .toolbar-header .dropdown-item:hover { background:rgba(102,126,234,0.2); color:#fff; }
.ev-page .toolbar-header .dropdown-divider { border-color:rgba(255,255,255,0.08); }
.ev-page .table thead { background:linear-gradient(135deg,#1a1e2e,#2d3561); }
.ev-page .table thead th { color:#fff !important; font-weight:600; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.8px; padding:14px 12px; border:none; }
.ev-page .table thead th a { color:#c8d0e0 !important; text-decoration:none; }
.ev-page .table thead th a:hover { color:#fff !important; }
.ev-page .table thead th a i { color:#667eea; }
.ev-page .table tbody tr { transition:all 0.2s ease; border-left:3px solid transparent; }
.ev-page .table tbody tr:hover { background:#f7f8fc !important; border-left-color:#667eea; transform:translateX(2px); }
.ev-page .table tbody td { padding:14px 12px; vertical-align:middle; color:#525f7f; font-size:0.9rem; }
.ev-page .badge { font-size:0.75rem; padding:5px 12px; border-radius:6px; font-weight:600; letter-spacing:0.3px; }
.ev-page .badge.bg-success { background:linear-gradient(135deg,#28a745,#55d77a) !important; }
.ev-page .badge.bg-danger { background:linear-gradient(135deg,#dc3545,#ff6b6b) !important; }
.ev-page .badge.bg-secondary { background:linear-gradient(135deg,#6c757d,#adb5bd) !important; }
.ev-page .card-stats { border-radius:14px; border:none; transition:all 0.3s ease; animation:fadeUp 0.4s ease both; }
.ev-page .card-stats:hover { transform:translateY(-4px); box-shadow:0 10px 30px rgba(0,0,0,0.1); }

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
.ev-page .form-button-action .btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; margin:0 2px; transition:all 0.2s ease; }
.ev-page .form-button-action .btn:hover { transform:scale(1.15); }
.ev-page .input-group .form-control { border-radius:10px 0 0 10px; border-color:#dde1f0; }
.ev-page .input-group .form-control:focus { border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,0.15); }
.ev-page .btn-primary { background:linear-gradient(135deg,#667eea,#764ba2); border:none; border-radius:10px; font-weight:600; padding:8px 20px; transition:all 0.3s ease; }
.ev-page .btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(102,126,234,0.35); }
.ev-page .live-countdown span { padding:3px 8px; border-radius:6px; font-size:0.78rem; }
.ev-page .live-countdown .text-primary { background:rgba(102,126,234,0.1); color:#667eea !important; }
.ev-page .live-countdown .text-warning { background:rgba(247,151,30,0.1); color:#f7971e !important; }
.ev-page .live-countdown .text-danger { background:rgba(220,53,69,0.1); }
/* AI Styles */
.ai-badge { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:8px;font-weight:700;font-size:0.82rem;cursor:help; }
.ai-badge.green { background:rgba(40,167,69,0.15);color:#28a745; }
.ai-badge.orange { background:rgba(247,151,30,0.15);color:#f7971e; }
.ai-badge.red { background:rgba(220,53,69,0.15);color:#dc3545; }
.btn-ai { background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:8px;font-size:0.75rem;padding:5px 10px;font-weight:600;transition:all 0.3s; }
.btn-ai:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(102,126,234,0.3);color:#fff; }
.btn-ai-sm { font-size:0.7rem;padding:3px 8px; }
.btn-ai-global { background:linear-gradient(135deg,#ee5a24,#f0932b);font-size:0.82rem;padding:8px 16px; }
.anomaly-panel { background:linear-gradient(135deg,#f8f9fc,#eef0f8);border-radius:0 0 16px 16px;border-top:2px solid #667eea; }
.anomaly-row { display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #eef0f8;transition:background 0.2s; }
.anomaly-row:hover { background:rgba(102,126,234,0.05); }
.anomaly-row:last-child { border-bottom:none; }
.sev-haute { background:linear-gradient(135deg,#dc3545,#ff6b6b) !important; }
.sev-moyenne { background:linear-gradient(135deg,#f7971e,#ffd200) !important; color:#333 !important; }
.sev-faible { background:linear-gradient(135deg,#28a745,#55d77a) !important; }
</style>

<div class="ev-page">
<div class="page-header">
    <h3 class="fw-bold mb-3"><i class="fas fa-calendar-alt me-2" style="color:#667eea;"></i> Events List</h3>
    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="index.php"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="events.php?action=list">Events</a></li>
    </ul>
</div>
  <div class="collapse" id="statsCollapse">
    <div class="row mb-3">
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#667eea,#764ba2); box-shadow:0 10px 30px rgba(102,126,234,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-calendar-alt"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= $stats['total'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Total Events</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:100%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.1s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#11998e,#38ef7d); box-shadow:0 10px 30px rgba(17,153,142,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-check-circle"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= $stats['active'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Active Events</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:<?= $stats['total'] > 0 ? round(($stats['active']/$stats['total'])*100) : 0 ?>%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.2s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#f7971e,#ffd200); box-shadow:0 10px 30px rgba(247,151,30,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="fas fa-euro-sign"></i></div>
              <div class="text-end">
                <div class="stat3d-value">€<?= $stats['avg_price'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Avg Price</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:65%;"></div></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3">
        <div class="stat3d" style="animation-delay:0.3s;">
          <div class="stat3d-bg" style="background:linear-gradient(135deg,#ee5a24,#f0932b); box-shadow:0 10px 30px rgba(238,90,36,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="stat3d-icon"><i class="far fa-clock"></i></div>
              <div class="text-end">
                <div class="stat3d-value"><?= $stats['this_month'] ?></div>
              </div>
            </div>
            <div class="stat3d-label">Events This Month</div>
            <div class="stat3d-bar"><div class="stat3d-bar-fill" style="width:<?= $stats['total'] > 0 ? round(($stats['this_month']/$stats['total'])*100) : 0 ?>%;"></div></div>
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
            <h4 class="card-title"><i class="fas fa-list me-2" style="color:#667eea;"></i>Event List</h4>
            
            <form action="events.php" method="GET" class="d-flex flex-grow-1">
              <input type="hidden" name="action" value="list">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="🔍  Rechercher un événement..." value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-search"><i class="fa fa-search me-1"></i> Recherche</button>
              </div>
            </form>

            <div class="d-flex gap-2">
                <div class="dropdown">
                  <button class="btn btn-sort dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-sort me-1"></i> Trier
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="events.php?action=list&sort=event_date&order=ASC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-sort-amount-up me-2" style="color:#4dd0e1;"></i>Date (Ascending)</a></li>
                    <li><a class="dropdown-item" href="events.php?action=list&sort=event_date&order=DESC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-sort-amount-down me-2" style="color:#4dd0e1;"></i>Date (Descending)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="events.php?action=list&sort=price&order=ASC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-arrow-up me-2" style="color:#55d77a;"></i>Price (Low to High)</a></li>
                    <li><a class="dropdown-item" href="events.php?action=list&sort=price&order=DESC&search=<?= urlencode($search ?? '') ?>"><i class="fas fa-arrow-down me-2" style="color:#ff6b6b;"></i>Price (High to Low)</a></li>
                  </ul>
                </div>

                <button class="btn btn-stats" type="button" data-bs-toggle="collapse" data-bs-target="#statsCollapse" aria-expanded="false" aria-controls="statsCollapse">
                  <i class="fas fa-chart-bar me-1"></i> Stats
                </button>

                <?php if(!empty($search)): ?>
                  <a href="events.php?action=list" class="btn btn-clear"><i class="fas fa-times me-1"></i> Clear</a>
                <?php endif; ?>

                <a href="events.php?action=add" class="btn btn-add">
                  <i class="fa fa-plus me-1"></i> Add Event
                </a>
                <button class="btn btn-ai btn-ai-global" onclick="analyzeAllAnomalies()" id="btnAnomalies">
                  <i class="fas fa-search me-1"></i> Analyser anomalies
                </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th><a href="events.php?action=list&sort=id&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">ID <i class="fa fa-sort"></i></a></th>
                  <th>Catégorie</th>
                  <th><a href="events.php?action=list&sort=title&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Title <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=event_date&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Date <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=location_name&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Location <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=price&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Price <i class="fa fa-sort"></i></a></th>
                  <th><a href="events.php?action=list&sort=status&order=<?= $nextOrder ?>&search=<?= urlencode($search ?? '') ?>" class="text-dark">Status <i class="fa fa-sort"></i></a></th>
                  <th>IA Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
                        <tbody>
                            <?php foreach ($events as $e): ?>
                            <?php
                                $titleLow = strtolower($e['title']);
                                if (strpos($titleLow, 'workshop') !== false) {
                                    $catBadge = '<span class="badge" style="background-color: #007bff; color: white;">Workshop</span>';
                                } elseif (strpos($titleLow, 'conference') !== false) {
                                    $catBadge = '<span class="badge" style="background-color: #6f42c1; color: white;">Conference</span>';
                                } elseif (strpos($titleLow, 'concert') !== false) {
                                    $catBadge = '<span class="badge" style="background-color: #e83e8c; color: white;">Concert</span>';
                                } else {
                                    $catBadge = '<span class="badge bg-secondary text-white">Autre</span>';
                                }
                            ?>
                            <tr>
                                <td><?php echo $e['id']; ?></td>
                                <td><?php echo $catBadge; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($e['title']); ?>
                                    <div class="live-countdown mt-1" style="font-size: 0.85rem; font-weight: 600;" data-date="<?php echo $e['event_date']; ?>" data-status="<?php echo $e['status']; ?>"></div>
                                </td>
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
                                
                                <td>
                                    <div class="form-button-action">
                                        <a href="events.php?action=edit&id=<?php echo $e['id']; ?>" class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="events.php?action=duplicate&id=<?php echo $e['id']; ?>" method="POST" style="display:inline;">
                                            <button type="submit" class="btn btn-link btn-info" data-bs-toggle="tooltip" title="Dupliquer">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </form>
                                        <?php if(strlen($e['description'] ?? '') < 20): ?>
                                        <button class="btn btn-link btn-primary" onclick="generateDescription(<?php echo $e['id']; ?>,'<?php echo addslashes(htmlspecialchars($e['title'])); ?>','<?php echo $e['event_date']; ?>',<?php echo $e['price'] ?? 0; ?>,<?php echo $e['max_attendees'] ?? 0; ?>)" data-bs-toggle="tooltip" title="Générer description IA">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                        <?php endif; ?>
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

<!-- Anomaly Panel -->
<div id="anomalyPanel" class="anomaly-panel p-4" style="display:none;"></div>

<!-- Description Generation Modal -->
<div class="modal fade" id="descModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1a1e2e,#2d3561);border:none;">
        <h5 class="modal-title text-white"><i class="fas fa-magic me-2" style="color:#667eea;"></i>Description générée par IA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="descTextarea" class="form-control" rows="5" style="border-radius:10px;border-color:#dde1f0;"></textarea>
      </div>
      <div class="modal-footer" style="border:none;">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
        <button class="btn btn-ai" id="btnRegenerate" onclick="regenerateDesc()"><i class="fas fa-sync me-1"></i> Régénérer</button>
        <button class="btn btn-ai" style="background:linear-gradient(135deg,#28a745,#55d77a);" id="btnSaveDesc" onclick="saveDescription()"><i class="fas fa-save me-1"></i> Sauvegarder</button>
      </div>
    </div>
  </div>
</div>

<script>
// --- Countdown ---
document.addEventListener('DOMContentLoaded', function() {
    function updateCountdowns() {
        const now = new Date().getTime();
        document.querySelectorAll('.live-countdown').forEach(el => {
            if (el.dataset.status !== 'active') return;
            const dateStr = el.dataset.date.replace(/-/g, '/');
            const eventDate = new Date(dateStr).getTime();
            if (isNaN(eventDate)) return;
            const diff = eventDate - now;
            if (diff < 0) { el.innerHTML = '<span class="text-danger"><i class="fas fa-clock"></i> Dépassé</span>'; return; }
            const days = Math.floor(diff / (1000*60*60*24));
            if (days > 0) { el.innerHTML = '<span class="text-primary"><i class="fas fa-clock"></i> J-'+days+'</span>'; }
            else { const h=Math.floor((diff%(1000*60*60*24))/(1000*60*60)),m=Math.floor((diff%(1000*60*60))/(1000*60)); el.innerHTML='<span class="text-warning"><i class="fas fa-clock"></i> Dans '+h+'h '+m+'min</span>'; }
        });
    }
    updateCountdowns();
    setInterval(updateCountdowns, 60000);
});

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
    document.querySelector('.ev-page').prepend(div);
}
function parseJSON(text) {
    try { const clean = text.replace(/```json|```/g,'').trim(); const m=clean.match(/\{[\s\S]*\}/); return m ? JSON.parse(m[0]) : null; } catch(e) { return null; }
}
function setLoading(btn, loading, originalHtml) {
    if(loading) { btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Analyse...'; }
    else { btn.disabled=false; btn.innerHTML=originalHtml; }
}

// --- IA-1: Score de succès ---
async function analyzeEvent(id, title, date, price, maxAtt, status) {
    const cell = document.getElementById('ai-score-'+id);
    const btn = cell.querySelector('button');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    try {
        const prompt = `Tu es un expert en événementiel. Analyse cet événement et donne-lui un score de succès de 0 à 100 avec une justification courte (2 phrases max). Titre: ${title}, Date: ${date}, Prix: ${price}€, Capacité max: ${maxAtt} personnes, Statut: ${status}. Réponds UNIQUEMENT en JSON valide sans backticks : { "score": NUMBER, "justification": STRING }`;
        const raw = await callOllama(prompt);
        const result = parseJSON(raw);
        if(result && typeof result.score === 'number') {
            const cls = result.score >= 70 ? 'green' : result.score >= 40 ? 'orange' : 'red';
            cell.innerHTML = `<span class="ai-badge ${cls}" title="${result.justification.replace(/"/g,'&quot;')}" data-bs-toggle="tooltip">${result.score}/100</span><div class="text-muted" style="font-size:0.72rem;margin-top:3px;">${result.justification}</div>`;
            const el = cell.querySelector('[data-bs-toggle="tooltip"]');
            if(el && typeof bootstrap !== 'undefined') new bootstrap.Tooltip(el);
        } else { cell.innerHTML = '<span class="text-muted" style="font-size:0.8rem;">Erreur parsing</span>'; }
    } catch(e) { showApiError(e.message); cell.innerHTML = '<span class="text-danger" style="font-size:0.8rem;">Erreur API</span>'; }
}

// --- IA-2: Description auto ---
let currentDescId = null;
let currentDescParams = {};
async function generateDescription(id, title, date, price, maxAtt) {
    currentDescId = id;
    currentDescParams = {title,date,price,maxAtt};
    const modal = new bootstrap.Modal(document.getElementById('descModal'));
    document.getElementById('descTextarea').value = 'Génération en cours...';
    modal.show();
    await doGenerateDesc();
}
async function doGenerateDesc() {
    const btn = document.getElementById('btnRegenerate');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    try {
        const p = currentDescParams;
        const prompt = `Rédige une description courte et attrayante (3 phrases max, ton professionnel) pour un événement intitulé '${p.title}' prévu le ${p.date} au prix de ${p.price}€ pour ${p.maxAtt} personnes maximum. Réponds uniquement avec la description, sans guillemets ni backticks ni formatage.`;
        const text = await callOllama(prompt);
        document.getElementById('descTextarea').value = text;
    } catch(e) { showApiError(e.message); document.getElementById('descTextarea').value = 'Erreur lors de la génération.'; }
    finally { setLoading(btn, false, origHtml); }
}
function regenerateDesc() { doGenerateDesc(); }
function saveDescription() {
    const desc = document.getElementById('descTextarea').value;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'events.php?action=update_desc&id='+currentDescId;
    const input = document.createElement('input');
    input.type='hidden'; input.name='description'; input.value=desc;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// --- IA-3: Anomalies ---
async function analyzeAllAnomalies() {
    const btn = document.getElementById('btnAnomalies');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    const panel = document.getElementById('anomalyPanel');
    panel.style.display = 'block';
    panel.innerHTML = '<div class="text-center p-4"><span class="spinner-border"></span><p class="mt-2 text-muted">Analyse IA en cours...</p></div>';
    try {
        const events = <?php echo json_encode(array_map(function($e){return['id'=>$e['id'],'title'=>$e['title'],'description'=>$e['description']??'','event_date'=>$e['event_date'],'price'=>$e['price'],'max_attendees'=>$e['max_attendees'],'status'=>$e['status']];}, $events)); ?>;
        const prompt = `Tu es un auditeur événementiel expert et méticuleux. Analyse cette liste d'événements et identifie de manière intelligente TOUTES les anomalies, incohérences ou risques métier. 
Vérifie notamment : 
1) Incohérences (ex: événement très haut de gamme mais gratuit, ou prix très élevé sans description). 
2) Statuts illogiques (ex: date passée mais statut "active", ou événement futur déjà "completed"). 
3) Données invalides (ex: capacité = 0 ou > 10000, prix négatif). 
4) Qualité des données (ex: titres tout en minuscules comme "minimum", titre trop court, description manquante). 
Pour chaque anomalie, explique PRÉCISÉMENT l'incohérence et propose une solution dans le champ "probleme". 
Réponds UNIQUEMENT en JSON valide sans backticks : { "anomalies": [ { "id": NUMBER, "probleme": STRING, "severite": "haute"|"moyenne"|"faible" } ] } 
Données : ${JSON.stringify(events)}`;
        const raw = await callOllama(prompt);
        const result = parseJSON(raw);
        if(result && result.anomalies) {
            if(result.anomalies.length === 0) {
                panel.innerHTML = '<div class="alert alert-success text-center m-3">✅ Aucune anomalie détectée</div>';
            } else {
                let html = '<h6 class="fw-bold mb-3" style="color:#32325d;"><i class="fas fa-exclamation-triangle me-2" style="color:#f7971e;"></i>'+result.anomalies.length+' anomalie(s) détectée(s)</h6>';
                result.anomalies.forEach(a => {
                    const sevCls = a.severite==='haute'?'sev-haute':a.severite==='moyenne'?'sev-moyenne':'sev-faible';
                    html += `<div class="anomaly-row"><span class="badge ${sevCls}" style="font-size:0.75rem;padding:5px 12px;border-radius:6px;">${a.severite.toUpperCase()}</span><span style="flex:1;color:#525f7f;"><strong>Event #${a.id}</strong> — ${a.probleme}</span><button class="btn btn-ai btn-ai-sm" onclick="document.querySelector('tr td').closest('table').querySelectorAll('tr').forEach(r=>{if(r.querySelector('td')&&r.querySelector('td').textContent.trim()==${a.id}){r.style.background='#fff3cd';r.scrollIntoView({behavior:'smooth',block:'center'})}})">🎯 Corriger</button></div>`;
                });
                panel.innerHTML = html;
            }
        } else { panel.innerHTML = '<div class="text-center p-4 text-danger">Erreur de parsing de la réponse IA</div>'; }
    } catch(e) { showApiError(e.message); panel.innerHTML = '<div class="text-center p-4 text-danger">Erreur API: '+e.message+'</div>'; }
    finally { setLoading(btn, false, origHtml); }
}
</script>
</div><!-- /ev-page -->
<?php include 'footer.php'; ?>
