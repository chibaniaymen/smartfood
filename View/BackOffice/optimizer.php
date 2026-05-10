<?php 
require_once __DIR__ . '/../../controller/EventController.php';
require_once __DIR__ . '/../../controller/LocationController.php';

$eventCtrl = new EventController();
$locCtrl = new LocationController();
$allEvents = $eventCtrl->list('', 'event_date', 'ASC');
$allLocations = $locCtrl->list();

$currentMonth = date('Y-m');
$prevMonth = date('Y-m', strtotime('-1 month'));

// Stats Calculation
$currentMonthEvents = [];
$prevMonthEvents = [];

foreach ($allEvents as $e) {
    $eMonth = date('Y-m', strtotime($e['event_date']));
    if ($eMonth === $currentMonth) $currentMonthEvents[] = $e;
    if ($eMonth === $prevMonth) $prevMonthEvents[] = $e;
}

$currCreated = count($currentMonthEvents);
$prevCreated = count($prevMonthEvents);
$createdDiff = $prevCreated > 0 ? (($currCreated - $prevCreated) / $prevCreated) * 100 : ($currCreated > 0 ? 100 : 0);
$createdArrow = $createdDiff >= 0 ? '<i class="fas fa-arrow-up text-success"></i>' : '<i class="fas fa-arrow-down text-danger"></i>';

$currRev = array_sum(array_map(function($e) { return $e['price'] * $e['max_attendees']; }, $currentMonthEvents));
$prevRev = array_sum(array_map(function($e) { return $e['price'] * $e['max_attendees']; }, $prevMonthEvents));
$revDiff = $prevRev > 0 ? (($currRev - $prevRev) / $prevRev) * 100 : ($currRev > 0 ? 100 : 0);
$revArrow = $revDiff >= 0 ? '<i class="fas fa-arrow-up text-success"></i>' : '<i class="fas fa-arrow-down text-danger"></i>';

// Recommendations Logic
$recommendations = [];

foreach ($allEvents as $e) {
    // 1. Past active events
    if ($e['status'] === 'active' && strtotime($e['event_date']) < time()) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-danger">Clôture Requise</span>',
            'icon' => '<i class="fas fa-exclamation-triangle text-danger fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'message' => 'Date passée mais l\'événement est toujours actif.',
            'action' => '<form action="events.php?action=complete&id='.$e['id'].'" method="POST" class="m-0"><button type="submit" class="btn btn-sm btn-danger btn-round"><i class="fas fa-times-circle"></i> Clôturer</button></form>'
        ];
    }
    // 2. Missing description
    if (empty(trim($e['description']))) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-warning">Incomplet</span>',
            'icon' => '<i class="fas fa-file-alt text-warning fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'message' => 'Aucune description fournie pour le public.',
            'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-warning btn-round text-white"><i class="fas fa-edit"></i> Rédiger</a>'
        ];
    }
    // 3. Price missing or 0
    if ($e['price'] == 0 || is_null($e['price'])) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-info">Revenu</span>',
            'icon' => '<i class="fas fa-euro-sign text-info fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'message' => 'Prix non défini ou gratuit. Vérifiez si c\'est intentionnel.',
            'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-info btn-round"><i class="fas fa-tags"></i> Fixer prix</a>'
        ];
    }
    // 4. Upcoming in < 3 days
    $daysUntil = (strtotime($e['event_date']) - time()) / 86400;
    if ($e['status'] === 'active' && $daysUntil > 0 && $daysUntil <= 3) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-primary">Urgent</span>',
            'icon' => '<i class="fas fa-bolt text-primary fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'message' => 'L\'événement a lieu dans moins de 3 jours.',
            'action' => '<button class="btn btn-sm btn-primary btn-round" onclick="alert(\'Simulation: Emails de rappel envoyés avec succès !\')"><i class="fas fa-envelope"></i> Envoyer Rappel</button>'
        ];
    }
    // 5. Capacity Check
    if ($e['location_id']) {
        $loc = null;
        foreach($allLocations as $l) { if ($l['id'] == $e['location_id']) { $loc = $l; break; } }
        if ($loc && $loc['capacity'] > 0) {
            $fillRate = ($e['max_attendees'] / $loc['capacity']) * 100;
            if ($fillRate < 30 && $e['status'] === 'active' && $daysUntil > 0) {
                 $recommendations[] = [
                    'type_badge' => '<span class="badge badge-secondary">Marketing</span>',
                    'icon' => '<i class="fas fa-bullhorn text-secondary fa-lg"></i>',
                    'title' => htmlspecialchars($e['title']),
                    'message' => 'Taux de remplissage critique (< 30%). Besoin de visibilité.',
                    'action' => '<button class="btn btn-sm btn-secondary btn-round" onclick="alert(\'Redirection vers le module Marketing...\')"><i class="fas fa-ad"></i> Booster</button>'
                ];
            } elseif ($fillRate >= 100 && $e['status'] === 'active') {
                 $recommendations[] = [
                    'type_badge' => '<span class="badge badge-danger">Saturé</span>',
                    'icon' => '<i class="fas fa-ban text-danger fa-lg"></i>',
                    'title' => htmlspecialchars($e['title']),
                    'message' => 'Capacité maximale atteinte (100%). Surréservation possible.',
                    'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-danger btn-round"><i class="fas fa-lock"></i> Bloquer Ventes</a>'
                ];
            }
        }
    }
}

// 6. Cancelled events still in DB > 7 days
foreach ($allEvents as $e) {
    if ($e['status'] === 'cancelled' && strtotime($e['event_date']) < strtotime('-7 days')) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-dark">Nettoyage</span>',
            'icon' => '<i class="fas fa-broom text-dark fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'priority' => 1,
            'message' => 'Événement annulé depuis plus de 7 jours. Pensez à le supprimer.',
            'action' => '<a href="events.php?action=delete&id='.$e['id'].'" onclick="return confirm(\'Supprimer cet événement annulé ?\')" class="btn btn-sm btn-outline-dark btn-round"><i class="fas fa-trash-alt"></i> Supprimer</a>'
        ];
    }
}

// 7. Max attendees not set
foreach ($allEvents as $e) {
    if (($e['max_attendees'] == 0 || is_null($e['max_attendees'])) && $e['status'] === 'active') {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-warning">Capacité</span>',
            'icon' => '<i class="fas fa-users text-warning fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'priority' => 2,
            'message' => 'Nombre max de participants non défini. Impossible de calculer le taux de remplissage.',
            'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-warning btn-round text-white"><i class="fas fa-user-plus"></i> Définir</a>'
        ];
    }
}

// 8. Event too far in future (> 6 months)
foreach ($allEvents as $e) {
    if ($e['status'] === 'active' && strtotime($e['event_date']) > strtotime('+6 months')) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-info">Planification</span>',
            'icon' => '<i class="fas fa-hourglass-half text-info fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'priority' => 1,
            'message' => 'Événement prévu dans plus de 6 mois. Vérifiez que la salle est toujours réservée.',
            'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-info btn-round"><i class="fas fa-calendar-check"></i> Vérifier</a>'
        ];
    }
}

// 9. Duplicate titles detection
$titleCounts = [];
foreach ($allEvents as $e) { $titleCounts[$e['title']] = ($titleCounts[$e['title']] ?? 0) + 1; }
foreach ($allEvents as $e) {
    if ($titleCounts[$e['title']] > 1) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-secondary">Doublon</span>',
            'icon' => '<i class="fas fa-clone text-secondary fa-lg"></i>',
            'title' => htmlspecialchars($e['title']),
            'priority' => 1,
            'message' => 'Titre en double ('.$titleCounts[$e['title']].' occurrences). Risque de confusion.',
            'action' => '<a href="events.php?action=edit&id='.$e['id'].'" class="btn btn-sm btn-outline-secondary btn-round"><i class="fas fa-pen"></i> Renommer</a>'
        ];
        $titleCounts[$e['title']] = 0; // show only once
    }
}

// 10. Location unused
$thirtyDaysAgo = strtotime('-30 days');
foreach ($allLocations as $l) {
    $lastUsed = 0;
    foreach ($allEvents as $e) {
        if ($e['location_id'] == $l['id']) {
            $eTime = strtotime($e['event_date']);
            if ($eTime > $lastUsed) $lastUsed = $eTime;
        }
    }
    if ($lastUsed < $thirtyDaysAgo && $lastUsed > 0) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-dark">Lieu Inactif</span>',
            'icon' => '<i class="fas fa-building text-dark fa-lg"></i>',
            'title' => htmlspecialchars($l['name']),
            'priority' => 1,
            'message' => 'Lieu inutilisé depuis plus de 30 jours.',
            'action' => '<a href="locations.php?action=edit&id='.$l['id'].'" class="btn btn-sm btn-dark btn-round"><i class="fas fa-search"></i> Inspecter</a>'
        ];
    } elseif ($lastUsed == 0) {
        $recommendations[] = [
            'type_badge' => '<span class="badge badge-dark">Lieu Fantôme</span>',
            'icon' => '<i class="fas fa-ghost text-dark fa-lg"></i>',
            'title' => htmlspecialchars($l['name']),
            'priority' => 1,
            'message' => 'Lieu ajouté mais jamais utilisé.',
            'action' => '<a href="locations.php?action=delete&id='.$l['id'].'" onclick="return confirm(\'Supprimer ce lieu inutilisé ?\')" class="btn btn-sm btn-outline-danger btn-round"><i class="fas fa-trash"></i> Nettoyer</a>'
        ];
    }
}

// Add default priority where missing
foreach ($recommendations as &$r) { if (!isset($r['priority'])) $r['priority'] = 2; }
unset($r);

// Count by severity for summary bar
$countDanger = count(array_filter($recommendations, fn($r) => strpos($r['type_badge'], 'danger') !== false));
$countWarning = count(array_filter($recommendations, fn($r) => strpos($r['type_badge'], 'warning') !== false));
$countInfo = count(array_filter($recommendations, fn($r) => strpos($r['type_badge'], 'info') !== false || strpos($r['type_badge'], 'primary') !== false));
$countLow = count($recommendations) - $countDanger - $countWarning - $countInfo;

// Calendar Setup
$firstDayOfMonth = date('Y-m-01');
$daysInMonth = date('t');
$firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
?>
<?php include 'header.php'; ?>
<style>
/* === PAGE === */
.opt-page { animation: pageIn 0.6s ease both; }
@keyframes pageIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }
@keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-6px); } }

/* === STAT CARDS === */
.stat-card { border:none; border-radius:16px; overflow:hidden; transition:all 0.3s ease; animation:fadeUp 0.5s ease both; }
.stat-card:hover { transform:translateY(-5px); box-shadow:0 15px 40px rgba(0,0,0,0.12); }
.stat-card .stat-icon { width:60px; height:60px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:#fff; }
.stat-card .stat-value { font-size:2.2rem; font-weight:800; line-height:1.1; }
.stat-card .stat-label { font-size:0.85rem; color:#8898aa; font-weight:500; }
.stat-card .stat-trend { font-size:0.8rem; font-weight:600; padding:3px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; }
.trend-up { background:rgba(40,167,69,0.12); color:#28a745; }
.trend-down { background:rgba(220,53,69,0.12); color:#dc3545; }

/* === CALENDAR === */
.calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
.cal-header { font-weight:700; text-align:center; padding:12px 0; color:#8898aa; font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; }
.cal-day { min-height:90px; padding:8px; border-radius:10px; background:#fff; border:1px solid #eef0f8; position:relative; transition:all 0.2s ease; cursor:default; }
.cal-day:hover { border-color:#c8cfe8; box-shadow:0 4px 12px rgba(0,0,0,0.06); }
.cal-day.empty { background:#fafbfd; border-color:transparent; }
.cal-day.today { border:2px solid #667eea; background:linear-gradient(135deg,rgba(102,126,234,0.04),rgba(118,75,162,0.04)); }
.cal-date { font-weight:700; font-size:0.85rem; color:#525f7f; margin-bottom:4px; }
.cal-day.today .cal-date { color:#667eea; }
.cal-event { font-size:0.7rem; padding:2px 6px; margin-top:2px; border-radius:4px; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bg-ev-active { background:linear-gradient(135deg,#28a745,#55d77a); }
.bg-ev-completed { background:linear-gradient(135deg,#6c757d,#adb5bd); }
.bg-ev-cancelled { background:linear-gradient(135deg,#dc3545,#ff6b6b); }

/* === RECOMMENDATIONS === */
.rec-card { border-radius:12px; border:1px solid #eef0f8; padding:18px 20px 18px 24px; margin-bottom:12px; background:#fff; transition:all 0.3s cubic-bezier(.25,.8,.25,1); position:relative; overflow:hidden; animation:fadeUp 0.4s ease both; }
.rec-card:hover { transform:translateX(4px); box-shadow:0 8px 25px rgba(0,0,0,0.08); border-color:#d0d5e8; }
.rec-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; }
.rec-card.sev-danger::before { background:#dc3545; }
.rec-card.sev-warning::before { background:#ffc107; }
.rec-card.sev-info::before { background:#17a2b8; }
.rec-card.sev-dark::before { background:#525f7f; }
.rec-icon-circle { width:44px; height:44px; min-width:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1rem; color:#fff; }
.rec-icon-circle.ic-danger { background:linear-gradient(135deg,#dc3545,#ff6b6b); }
.rec-icon-circle.ic-warning { background:linear-gradient(135deg,#f7971e,#ffd200); color:#5a3e00; }
.rec-icon-circle.ic-info { background:linear-gradient(135deg,#17a2b8,#4dd0e1); }
.rec-icon-circle.ic-dark { background:linear-gradient(135deg,#525f7f,#8898aa); }
.rec-num { position:absolute; top:10px; right:12px; width:22px; height:22px; border-radius:6px; background:#eef0f8; color:#525f7f; font-weight:700; font-size:0.7rem; display:flex; align-items:center; justify-content:center; }
.rec-summary-card { border-radius:14px; padding:20px; text-align:center; color:#fff; transition:all 0.3s ease; position:relative; overflow:hidden; }
.rec-summary-card:hover { transform:translateY(-4px); box-shadow:0 10px 30px rgba(0,0,0,0.15); }
.rec-summary-card::after { content:''; position:absolute; top:-30px; right:-30px; width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.1); }
.rec-summary-card .rc-count { font-size:2.4rem; font-weight:800; line-height:1; }
.rec-summary-card .rc-label { font-size:0.75rem; opacity:0.85; margin-top:6px; text-transform:uppercase; letter-spacing:1px; font-weight:600; }
.filter-btn { cursor:pointer; transition:all 0.25s ease; border-radius:8px !important; padding:7px 16px !important; font-weight:600; font-size:0.8rem; border:1px solid rgba(255,255,255,0.2) !important; }
.filter-btn:hover, .filter-btn.active-filter { background:rgba(255,255,255,0.2) !important; box-shadow:0 4px 15px rgba(0,0,0,0.15); }
.rec-progress { height:3px; border-radius:2px; background:#eef0f8; margin-top:8px; overflow:hidden; }
.rec-progress-bar { height:100%; border-radius:2px; background:linear-gradient(90deg,#667eea,#764ba2); background-size:200% 100%; animation:shimmer 2.5s linear infinite; }
.section-divider { height:1px; background:linear-gradient(90deg,transparent,#dde1f0,transparent); margin:8px 0 24px; }
</style>


<div class="opt-page">
<div class="page-header">
    <h3 class="fw-bold mb-3"><i class="fas fa-brain me-2" style="color:#667eea;"></i> Smart Optimizer</h3>
    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="dashboard_view.php"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Smart Optimizer</a></li>
    </ul>
</div>

<!-- AI Buttons Row -->
<div class="d-flex gap-2 mb-3 flex-wrap" style="animation:fadeUp 0.4s ease both;">
  <button class="btn" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:10px;font-weight:600;padding:9px 20px;transition:all 0.3s;" onclick="generateReport()" id="btnReport"><i class="fas fa-file-alt me-1"></i> Générer rapport du mois</button>
  <button class="btn" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;border:none;border-radius:10px;font-weight:600;padding:9px 20px;transition:all 0.3s;" onclick="analyzeSentiment()" id="btnSentiment"><i class="fas fa-comment-dots me-1"></i> Analyser le ton des events</button>
</div>

<!-- ═══ STATS ═══ -->
<div class="row">
    <?php
    $totalEvents = count($allEvents);
    $activeEvents = count(array_filter($allEvents, fn($e) => $e['status'] === 'active'));
    $totalLocations = count($allLocations);
    $statCards = [
        ['icon'=>'fas fa-calendar-alt','bg'=>'linear-gradient(135deg,#667eea,#764ba2)','label'=>'Événements ce mois','value'=>$currCreated,'trend'=>$createdDiff,'prev'=>$prevCreated],
        ['icon'=>'fas fa-euro-sign','bg'=>'linear-gradient(135deg,#11998e,#38ef7d)','label'=>'Revenu potentiel','value'=>'€'.number_format($currRev,0),'trend'=>$revDiff,'prev'=>'€'.number_format($prevRev,0)],
        ['icon'=>'fas fa-bolt','bg'=>'linear-gradient(135deg,#f7971e,#ffd200)','label'=>'Événements actifs','value'=>$activeEvents,'trend'=>null,'prev'=>null],
        ['icon'=>'fas fa-tasks','bg'=>'linear-gradient(135deg,#ee5a24,#f0932b)','label'=>'Actions requises','value'=>count($recommendations),'trend'=>null,'prev'=>null],
    ];
    foreach ($statCards as $i => $s): ?>
    <div class="col-md-3 col-6 mb-4">
        <div class="stat-card card" style="animation-delay:<?php echo $i*0.1; ?>s;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background:<?php echo $s['bg']; ?>;">
                        <i class="<?php echo $s['icon']; ?>"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo $s['value']; ?></div>
                        <div class="stat-label"><?php echo $s['label']; ?></div>
                    </div>
                </div>
                <?php if ($s['trend'] !== null): ?>
                <div class="mt-2">
                    <span class="stat-trend <?php echo $s['trend'] >= 0 ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fas fa-arrow-<?php echo $s['trend'] >= 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo number_format(abs($s['trend']),1); ?>%
                    </span>
                    <small class="text-muted ms-1">vs <?php echo $s['prev']; ?></small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ═══ CALENDAR ═══ -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius:16px; animation:fadeUp 0.6s ease both; animation-delay:0.3s;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius:16px 16px 0 0; padding:20px 24px;">
                <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2" style="color:#667eea;"></i> <?php echo date('F Y'); ?></h5>
                <div class="d-flex gap-2">
                    <span class="badge" style="background:linear-gradient(135deg,#28a745,#55d77a); padding:6px 12px;">● Actif</span>
                    <span class="badge" style="background:linear-gradient(135deg,#6c757d,#adb5bd); padding:6px 12px;">● Clôturé</span>
                    <span class="badge" style="background:linear-gradient(135deg,#dc3545,#ff6b6b); padding:6px 12px;">● Annulé</span>
                </div>
            </div>
            <div class="card-body pt-0 px-4 pb-4">
                <div class="calendar">
                    <?php foreach(['LUN','MAR','MER','JEU','VEN','SAM','DIM'] as $d): ?>
                        <div class="cal-header"><?php echo $d; ?></div>
                    <?php endforeach; ?>
                    <?php
                    $dayCount = 1;
                    $fdow = date('N', strtotime($firstDayOfMonth));
                    for ($i = 1; $i < $fdow; $i++) echo '<div class="cal-day empty"></div>';
                    while ($dayCount <= $daysInMonth) {
                        $isToday = ($dayCount == date('j') && date('Y-m') == $currentMonth) ? ' today' : '';
                        echo '<div class="cal-day'.$isToday.'">';
                        echo '<div class="cal-date">'.$dayCount.'</div>';
                        $dateStr = date('Y-m-').str_pad($dayCount, 2, '0', STR_PAD_LEFT);
                        foreach ($currentMonthEvents as $e) {
                            if (strpos($e['event_date'], $dateStr) === 0) {
                                $sc = 'bg-ev-'.$e['status'];
                                if (!in_array($e['status'], ['active','completed','cancelled'])) $sc = 'bg-secondary';
                                echo '<div class="cal-event '.$sc.'" title="'.htmlspecialchars($e['title']).'">'.htmlspecialchars($e['title']).'</div>';
                            }
                        }
                        echo '</div>';
                        $dayCount++; $fdow++;
                        if ($fdow > 7 && $dayCount <= $daysInMonth) $fdow = 1;
                    }
                    while ($fdow <= 7 && $fdow > 1) { echo '<div class="cal-day empty"></div>'; $fdow++; }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ DIAGNOSTIC INTELLIGENT ═══ -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden; animation:fadeUp 0.6s ease both; animation-delay:0.5s;">
            <div style="background:linear-gradient(135deg,#1a1e2e,#2d3561); padding:28px 30px 20px;">
                <div class="d-flex align-items-start justify-content-between flex-wrap">
                    <div class="mb-3">
                        <h4 class="fw-bold mb-1" style="color:#fff; font-size:1.3rem;"><i class="fas fa-robot me-2" style="color:#ffbe33;"></i> Diagnostic Intelligent</h4>
                        <p class="mb-0" style="color:rgba(255,255,255,0.55); font-size:0.85rem;">Le système analyse en temps réel vos données pour identifier les optimisations possibles.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm filter-btn active-filter" style="color:#fff;" onclick="filterRecs('all',this)"><i class="fas fa-border-all me-1"></i>Tout</button>
                        <button class="btn btn-sm filter-btn" style="color:#ff6b6b;" onclick="filterRecs('danger',this)"><i class="fas fa-fire me-1"></i><?php echo $countDanger; ?></button>
                        <button class="btn btn-sm filter-btn" style="color:#ffd200;" onclick="filterRecs('warning',this)"><i class="fas fa-exclamation me-1"></i><?php echo $countWarning; ?></button>
                        <button class="btn btn-sm filter-btn" style="color:#4dd0e1;" onclick="filterRecs('info',this)"><i class="fas fa-info me-1"></i><?php echo $countInfo; ?></button>
                        <button class="btn btn-sm filter-btn" style="color:#adb5bd;" onclick="filterRecs('dark',this)"><i class="fas fa-building me-1"></i><?php echo $countLow; ?></button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3 col-6 mb-2"><div class="rec-summary-card" style="background:linear-gradient(135deg,#ee5a24,#d63031);"><i class="fas fa-fire" style="font-size:1.2rem;"></i><div class="rc-count"><?php echo $countDanger; ?></div><div class="rc-label">Critiques</div></div></div>
                    <div class="col-md-3 col-6 mb-2"><div class="rec-summary-card" style="background:linear-gradient(135deg,#f7971e,#ffd200);"><i class="fas fa-exclamation-triangle" style="font-size:1.2rem;"></i><div class="rc-count"><?php echo $countWarning; ?></div><div class="rc-label">Attention</div></div></div>
                    <div class="col-md-3 col-6 mb-2"><div class="rec-summary-card" style="background:linear-gradient(135deg,#0abde3,#48dbfb);"><i class="fas fa-lightbulb" style="font-size:1.2rem;"></i><div class="rc-count"><?php echo $countInfo; ?></div><div class="rc-label">Informations</div></div></div>
                    <div class="col-md-3 col-6 mb-2"><div class="rec-summary-card" style="background:linear-gradient(135deg,#667eea,#764ba2);"><i class="fas fa-clipboard-check" style="font-size:1.2rem;"></i><div class="rc-count"><?php echo count($recommendations); ?></div><div class="rc-label">Total</div></div></div>
                </div>
            </div>

            <div style="background:#f7f8fc; padding:24px 30px;">
                <?php if (empty($recommendations)): ?>
                    <div class="text-center py-5" style="animation:float 3s ease-in-out infinite;">
                        <i class="fas fa-check-circle fa-4x mb-3" style="color:#28a745;"></i>
                        <h4 class="fw-bold">Tout est parfaitement optimisé !</h4>
                        <p class="text-muted">Aucune action requise. Continuez comme ça.</p>
                    </div>
                <?php else: ?>
                    <div id="recContainer">
                    <?php $recNum = 1; foreach ($recommendations as $r):
                        $sev = 'dark';
                        if (strpos($r['type_badge'], 'danger') !== false) $sev = 'danger';
                        elseif (strpos($r['type_badge'], 'warning') !== false) $sev = 'warning';
                        elseif (strpos($r['type_badge'], 'info') !== false || strpos($r['type_badge'], 'primary') !== false) $sev = 'info';
                        preg_match('/fa-([a-z-]+)/', $r['icon'], $m);
                        $fa = $m[0] ?? 'fa-circle';
                    ?>
                        <div class="rec-card sev-<?php echo $sev; ?>" data-category="<?php echo $sev; ?>" style="animation-delay:<?php echo ($recNum-1)*0.06; ?>s;">
                            <span class="rec-num"><?php echo $recNum++; ?></span>
                            <div class="d-flex align-items-center">
                                <div class="rec-icon-circle ic-<?php echo $sev; ?> me-3"><i class="fas <?php echo $fa; ?>"></i></div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                        <?php echo $r['type_badge']; ?>
                                        <strong style="font-size:0.95rem; color:#32325d;"><?php echo $r['title']; ?></strong>
                                    </div>
                                    <p class="mb-0" style="font-size:0.85rem; color:#8898aa;"><?php echo $r['message']; ?></p>
                                    <div class="rec-progress"><div class="rec-progress-bar" style="width:<?php echo rand(35,95); ?>%;"></div></div>
                                </div>
                                <div class="ms-3 flex-shrink-0"><?php echo $r['action']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div><!-- /opt-page -->

<script>
function filterRecs(cat, btn) {
    document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active-filter'); });
    btn.classList.add('active-filter');
    var num = 0;
    document.querySelectorAll('#recContainer .rec-card').forEach(function(card){
        if (cat === 'all' || card.dataset.category === cat) {
            card.style.display = '';
            card.style.animation = 'none';
            card.offsetHeight;
            card.style.animation = 'fadeUp 0.35s ease both';
            card.style.animationDelay = (num * 0.05) + 's';
            num++;
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<!-- ═══ IA-6: Rapport Mensuel ═══ -->
<div id="reportSection" style="display:none;" class="mb-4">
  <div class="card" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1a1e2e,#2d3561);border:none;padding:16px 24px;">
      <h5 class="text-white mb-0"><i class="fas fa-clipboard-list me-2" style="color:#667eea;"></i>Rapport Mensuel IA</h5>
    </div>
    <div class="card-body" id="reportContent" style="line-height:1.8;color:#32325d;text-align:justify;background:linear-gradient(135deg,#f8f9fc,#eef0f8);"></div>
    <div class="card-footer text-end" style="border:none;background:transparent;">
      <button class="btn btn-sm" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border-radius:8px;" onclick="printReport()"><i class="fas fa-print me-1"></i> Imprimer</button>
    </div>
  </div>
</div>

<!-- ═══ IA-7: Prévision ═══ -->
<div class="card mb-4" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
  <div class="card-header" style="background:linear-gradient(135deg,#1a1e2e,#2d3561);border:none;padding:16px 24px;">
    <h5 class="text-white mb-0"><i class="fas fa-chart-line me-2" style="color:#38ef7d;"></i>Prévision IA — Revenu mois prochain</h5>
  </div>
  <div class="card-body">
    <div class="row g-4 align-items-center">
      <div class="col-md-4">
        <label class="fw-bold text-muted" style="font-size:0.82rem;">Nombre d'events prévus</label>
        <input type="range" class="form-range" min="1" max="20" value="5" id="sliderEvents" oninput="document.getElementById('valEvents').textContent=this.value">
        <div class="text-center fw-bold" style="color:#667eea;"><span id="valEvents">5</span></div>
      </div>
      <div class="col-md-4">
        <label class="fw-bold text-muted" style="font-size:0.82rem;">Prix moyen (€)</label>
        <input type="range" class="form-range" min="0" max="500" value="100" step="10" id="sliderPrice" oninput="document.getElementById('valPrice').textContent=this.value+'€'">
        <div class="text-center fw-bold" style="color:#11998e;"><span id="valPrice">100€</span></div>
      </div>
      <div class="col-md-4">
        <label class="fw-bold text-muted" style="font-size:0.82rem;">Capacité moyenne</label>
        <input type="range" class="form-range" min="10" max="500" value="100" step="10" id="sliderCapacity" oninput="document.getElementById('valCapacity').textContent=this.value">
        <div class="text-center fw-bold" style="color:#f7971e;"><span id="valCapacity">100</span></div>
      </div>
    </div>
    <div class="text-center mt-3">
      <button class="btn" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;border:none;border-radius:10px;font-weight:600;padding:10px 28px;" onclick="predictRevenue()" id="btnPredict"><i class="fas fa-calculator me-1"></i> Calculer la prévision</button>
    </div>
    <div id="predictionResult" class="text-center mt-4" style="display:none;"></div>
  </div>
</div>

<!-- ═══ IA-8: Sentiment ═══ -->
<div id="sentimentSection" style="display:none;" class="mb-4">
  <div class="card" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1a1e2e,#2d3561);border:none;padding:16px 24px;">
      <h5 class="text-white mb-0"><i class="fas fa-smile me-2" style="color:#ffd200;"></i>Analyse de Sentiment IA</h5>
    </div>
    <div class="card-body p-0" id="sentimentContent"></div>
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
    document.querySelector('.opt-page').prepend(div);
}
function parseJSON(text){try{const clean=text.replace(/```json|```/g,'').trim();const m=clean.match(/\{[\s\S]*\}/);return m?JSON.parse(m[0]):null;}catch(e){return null;}}
function setLoading(btn,loading,origHtml){if(loading){btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Analyse...';}else{btn.disabled=false;btn.innerHTML=origHtml;}}

// --- IA-6: Rapport ---
const monthlyStats = <?php
    $mostUsedLoc = '';
    $locUsage = [];
    foreach($allEvents as $ev) { if($ev['location_id']) $locUsage[$ev['location_id']] = ($locUsage[$ev['location_id']]??0)+1; }
    if(!empty($locUsage)) { arsort($locUsage); $topLocId=array_key_first($locUsage); foreach($allLocations as $ll) { if($ll['id']==$topLocId) { $mostUsedLoc=$ll['name']; break; } } }
    $maxPriceEvent = ''; $maxPrice = 0;
    foreach($allEvents as $ev) { if($ev['price'] > $maxPrice) { $maxPrice=$ev['price']; $maxPriceEvent=$ev['title']; } }
    $cancelled = count(array_filter($allEvents, fn($e)=>$e['status']==='cancelled'));
    echo json_encode([
        'total_events' => count($allEvents),
        'events_actifs' => count(array_filter($allEvents, fn($e)=>$e['status']==='active')),
        'events_annules' => $cancelled,
        'revenu_potentiel' => $currRev,
        'lieu_plus_utilise' => $mostUsedLoc,
        'event_plus_cher' => $maxPriceEvent,
        'prix_max' => $maxPrice
    ]);
?>;
async function generateReport() {
    const btn = document.getElementById('btnReport');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    document.getElementById('reportSection').style.display = 'block';
    document.getElementById('reportContent').innerHTML = '<div class="text-center p-4"><span class="spinner-border"></span><p class="mt-2 text-muted">Rédaction en cours...</p></div>';
    try {
        const prompt = `Tu es directeur d'une salle événementielle. Rédige un rapport mensuel professionnel (4-5 phrases, ton formel, nous) basé sur ces KPIs : ${JSON.stringify(monthlyStats)}. Termine par une recommandation stratégique concrète pour le mois prochain. Réponds uniquement avec le texte du rapport, sans titre ni formatage.`;
        const text = await callOllama(prompt);
        document.getElementById('reportContent').innerHTML = '<p style="padding:20px;margin:0;">'+text.replace(/\n/g,'<br>')+'</p>';
    } catch(e) {
        showApiError(e.message);
        document.getElementById('reportContent').innerHTML = '<p class="text-danger p-3">Erreur API: '+e.message+'</p>';
    } finally { setLoading(btn, false, origHtml); }
}
function printReport() {
    const content = document.getElementById('reportContent').innerHTML;
    const w = window.open('','','width=800,height=600');
    w.document.write('<html><head><title>Rapport Mensuel</title><style>body{font-family:Arial,sans-serif;padding:40px;line-height:1.8;color:#333;}h1{color:#667eea;}</style></head><body><h1>Rapport Mensuel - Feane Events</h1>'+content+'</body></html>');
    w.document.close();
    w.print();
}

// --- IA-7: Prévision ---
async function predictRevenue() {
    const btn = document.getElementById('btnPredict');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    const resultDiv = document.getElementById('predictionResult');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<span class="spinner-border"></span>';
    try {
        const nbEvents = document.getElementById('sliderEvents').value;
        const prix = document.getElementById('sliderPrice').value;
        const capacite = document.getElementById('sliderCapacity').value;
        const prompt = `Tu es analyste financier événementiel. Données actuelles : ${JSON.stringify(monthlyStats)}. Simulation : ${nbEvents} events prévus, prix moyen ${prix}€, ${capacite} personnes par event. Calcule le revenu potentiel avec intervalle de confiance à 80%. Réponds UNIQUEMENT en JSON valide sans backticks : { "revenu_estime": NUMBER, "intervalle_min": NUMBER, "intervalle_max": NUMBER, "commentaire": STRING }`;
        const raw = await callOllama(prompt);
        const r = parseJSON(raw);
        if(r && r.revenu_estime) {
            const pct = r.intervalle_max > 0 ? ((r.revenu_estime - r.intervalle_min) / (r.intervalle_max - r.intervalle_min)) * 100 : 50;
            resultDiv.innerHTML = `
                <h2 class="text-success" style="font-weight:800;">€${r.revenu_estime.toLocaleString()}</h2>
                <div class="text-muted mb-3" style="font-size:0.88rem;">Revenu estimé du mois prochain</div>
                <div style="max-width:400px;margin:0 auto;">
                    <div class="d-flex justify-content-between" style="font-size:0.8rem;color:#8898aa;">
                        <span>€${r.intervalle_min.toLocaleString()}</span>
                        <span>€${r.intervalle_max.toLocaleString()}</span>
                    </div>
                    <div style="height:8px;background:#eef0f8;border-radius:4px;overflow:hidden;position:relative;">
                        <div style="position:absolute;left:0;height:100%;width:100%;background:linear-gradient(90deg,#ee5a24,#f7971e,#38ef7d);border-radius:4px;"></div>
                        <div style="position:absolute;left:${pct}%;top:-4px;width:4px;height:16px;background:#1a1e2e;border-radius:2px;"></div>
                    </div>
                </div>
                <em class="text-muted" style="margin-top:16px;display:block;font-size:0.9rem;">${r.commentaire}</em>
            `;
        } else { resultDiv.innerHTML = '<p class="text-danger">Erreur de parsing</p>'; }
    } catch(e) { showApiError(e.message); resultDiv.innerHTML = '<p class="text-danger">Erreur API: '+e.message+'</p>'; }
    finally { setLoading(btn, false, origHtml); }
}

// --- IA-8: Sentiment ---
const descriptionsData = <?php echo json_encode(array_map(function($e){return['id'=>$e['id'],'title'=>$e['title'],'desc'=>$e['description']??''];}, $allEvents)); ?>;
async function analyzeSentiment() {
    if(descriptionsData.filter(d => d.desc && d.desc.trim().length > 0).length === 0) {
        document.getElementById('sentimentSection').style.display = 'block';
        document.getElementById('sentimentContent').innerHTML = '<div class="alert alert-warning m-3">Aucune description à analyser</div>';
        return;
    }
    const btn = document.getElementById('btnSentiment');
    const origHtml = btn.innerHTML;
    setLoading(btn, true);
    document.getElementById('sentimentSection').style.display = 'block';
    document.getElementById('sentimentContent').innerHTML = '<div class="text-center p-4"><span class="spinner-border"></span><p class="mt-2 text-muted">Analyse en cours...</p></div>';
    try {
        const prompt = `Analyse le sentiment de chaque description d'événement. Réponds UNIQUEMENT en JSON valide sans backticks : { "analyses": [ { "id": NUMBER, "sentiment": "positif"|"neutre"|"negatif", "score": NUMBER entre 0 et 100, "mot_cle": STRING } ] } Données : ${JSON.stringify(descriptionsData)}`;
        const raw = await callOllama(prompt);
        const result = parseJSON(raw);
        if(result && result.analyses) {
            const positif = result.analyses.filter(a=>a.sentiment==='positif').length;
            const pct = result.analyses.length > 0 ? Math.round((positif/result.analyses.length)*100) : 0;
            let html = '<div class="table-responsive"><table class="table mb-0" style="font-size:0.88rem;"><thead style="background:linear-gradient(135deg,#f8f9fc,#eef0f8);"><tr><th>Titre</th><th>Sentiment</th><th>Score</th><th>Mot-clé</th></tr></thead><tbody>';
            result.analyses.forEach(a => {
                const sentCls = a.sentiment==='positif'?'background:linear-gradient(135deg,#28a745,#55d77a)':a.sentiment==='negatif'?'background:linear-gradient(135deg,#dc3545,#ff6b6b)':'background:linear-gradient(135deg,#6c757d,#adb5bd)';
                const barColor = a.sentiment==='positif'?'#28a745':a.sentiment==='negatif'?'#dc3545':'#6c757d';
                const ev = descriptionsData.find(d=>d.id===a.id);
                html += `<tr><td class="fw-bold">${ev?ev.title:'#'+a.id}</td><td><span class="badge text-white" style="${sentCls};padding:5px 12px;border-radius:6px;">${a.sentiment}</span></td><td><div style="display:flex;align-items:center;gap:8px;"><div class="progress" style="flex:1;height:6px;"><div class="progress-bar" style="width:${a.score}%;background:${barColor};"></div></div><span class="fw-bold" style="font-size:0.8rem;">${a.score}%</span></div></td><td><span style="background:rgba(102,126,234,0.1);color:#667eea;padding:3px 10px;border-radius:6px;font-size:0.8rem;font-weight:600;">${a.mot_cle}</span></td></tr>`;
            });
            html += `</tbody></table></div><div class="p-3 text-center" style="background:linear-gradient(135deg,#f8f9fc,#eef0f8);border-top:1px solid #eef0f8;"><strong style="color:#32325d;">📊 ${pct}% des descriptions sont positives</strong></div>`;
            document.getElementById('sentimentContent').innerHTML = html;
        } else { document.getElementById('sentimentContent').innerHTML = '<p class="text-danger p-3">Erreur de parsing</p>'; }
    } catch(e) { showApiError(e.message); document.getElementById('sentimentContent').innerHTML = '<p class="text-danger p-3">Erreur API: '+e.message+'</p>'; }
    finally { setLoading(btn, false, origHtml); }
}
</script>

<?php include 'footer.php'; ?>
