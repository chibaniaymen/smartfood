<?php
/**
 * AI Insights Dashboard Premium (Netflix/Apple Style)
 */
define('BO_ACCESS', true);
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Controller/ArticleController.php';
require_once __DIR__ . '/../../../Controller/CommentaireController.php';

$articleController = new ArticleController($pdo);
$commentController = new CommentaireController($pdo);

// Fallback Admin
if (isset($_GET['type'])) {
    if ($_GET['type'] === 'articles') {
        $articleController->exportCSV();
    } elseif ($_GET['type'] === 'comments') {
        $commentController->exportCSV();
    }
}

$pageTitle  = 'AI Insights Dashboard';
$activeMenu = 'export';

// Fetch AI Report
$aiReport = $articleController->generateAiSnapshotReport();
$userProfile = $aiReport['user_profile'];
$recommendations = $aiReport['recommendations'];
$trending = $aiReport['trending'];
$aiConfidence = $aiReport['ai_confidence'];
$kpis = $aiReport['kpis'];
$charts = $aiReport['charts'];

// Préparation des données pour Chart.js (JSON)
$viewsDates = array_column($charts['views_over_time'], 'date');
$viewsCounts = array_column($charts['views_over_time'], 'views');

$topTag = !empty($userProfile) ? $userProfile[0]['tag'] : 'N/A';

$catLabels = array_column($charts['categories'], 'category');
$catCounts = array_column($charts['categories'], 'count');
$topTagsLabels = array_column($userProfile, 'tag');
$topTagsScores = array_column($userProfile, 'total_score');

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Import Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Premium UI CSS -->
<style>
/* Global Cinematic Background */
.premium-dashboard {
    background: linear-gradient(135deg, #0B0F19 0%, #111827 100%);
    color: #e5e5e5;
    padding: 30px;
    border-radius: 24px;
    margin-bottom: 30px;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    position: relative;
    overflow: hidden;
}

/* Subtle Animated Noise Overlay */
.premium-dashboard::before {
    content: "";
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="noiseFilter"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23noiseFilter)"/%3E%3C/svg%3E');
    opacity: 0.03;
    pointer-events: none;
    z-index: 0;
}

.dashboard-content {
    position: relative;
    z-index: 1;
}

/* Header Apple Style */
.premium-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.header-titles h2 {
    color: #ffffff;
    font-size: 2.2rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    margin: 0 0 5px 0;
}
.header-titles p {
    color: #9ca3af;
    font-size: 0.95rem;
    margin: 0;
}

/* Glowing AI Badge */
.ai-badge-container {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.ai-badge {
    background: rgba(229, 9, 20, 0.15);
    color: #ff3333;
    padding: 8px 16px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 0.9rem;
    border: 1px solid rgba(229, 9, 20, 0.5);
    box-shadow: 0 0 20px rgba(229, 9, 20, 0.2);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 2;
}
.ai-badge.High { color: #10b981; border-color: rgba(16, 185, 129, 0.5); background: rgba(16, 185, 129, 0.15); box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
.ai-badge.Medium { color: #f59e0b; border-color: rgba(245, 158, 11, 0.5); background: rgba(245, 158, 11, 0.15); box-shadow: 0 0 20px rgba(245, 158, 11, 0.2); }

/* Pulse Ring Animation */
@keyframes pulse-ring {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1.5); opacity: 0; }
}
.pulse-ring {
    position: absolute;
    width: 100%; height: 100%;
    border-radius: 30px;
    border: 2px solid #e50914;
    animation: pulse-ring 2s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
    z-index: 1;
}
.ai-badge.High ~ .pulse-ring { border-color: #10b981; }
.ai-badge.Medium ~ .pulse-ring { border-color: #f59e0b; }

/* KPI Cards */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.kpi-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.kpi-card:hover {
    background: rgba(255, 255, 255, 0.06);
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
}
.kpi-value {
    font-size: 2.2rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 5px;
    font-family: 'Courier New', Courier, monospace;
}
.kpi-label {
    font-size: 0.85rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}
.kpi-glow-1:hover { box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3); }
.kpi-glow-2:hover { box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
.kpi-glow-3:hover { box-shadow: 0 10px 30px rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); }
.kpi-glow-4:hover { box-shadow: 0 10px 30px rgba(229, 9, 20, 0.15); border-color: rgba(229, 9, 20, 0.3); }

/* Section Titles */
.section-title {
    color: #f3f4f6;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Netflix Row - Recommendations */
.netflix-row-container {
    overflow-x: auto;
    padding: 10px 0 30px 0;
    margin: 0 -10px 20px -10px;
    scroll-behavior: smooth;
}
.netflix-row-container::-webkit-scrollbar { height: 6px; }
.netflix-row-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
.netflix-row {
    display: flex;
    gap: 20px;
    padding: 0 10px;
    width: max-content;
}
.movie-card {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    width: 320px;
    min-height: 200px;
    padding: 20px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}
.movie-card:hover {
    transform: scale(1.06) translateY(-5px);
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(229, 9, 20, 0.5);
    box-shadow: 0 15px 35px rgba(0,0,0,0.6), 0 0 20px rgba(229, 9, 20, 0.2);
    z-index: 10;
}
.movie-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.4;
    margin-bottom: 10px;
}
.movie-score {
    color: #f59e0b;
    font-weight: bold;
    font-size: 0.9rem;
    margin-bottom: 15px;
}
.ai-reasons-panel {
    margin-top: auto;
    background: linear-gradient(180deg, rgba(229,9,20,0.05) 0%, rgba(229,9,20,0.15) 100%);
    border-radius: 8px;
    padding: 12px;
    border-left: 3px solid #e50914;
    font-size: 0.8rem;
    color: #d1d5db;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.movie-card:hover .ai-reasons-panel {
    opacity: 1;
}

/* Analytics Charts Section */
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 40px;
}
.chart-card {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 20px;
}
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
.chart-container.small {
    height: 250px;
}

/* Animations */
.fade-in {
    animation: fadeIn 0.8s ease-out forwards;
    opacity: 0;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }

/* Admin fallback */
.admin-fallback {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed rgba(255,255,255,0.1);
    text-align: right;
}
.admin-fallback a {
    color: #6b7280;
    font-size: 0.8rem;
    text-decoration: none;
    margin-left: 20px;
    transition: color 0.2s;
}
.admin-fallback a:hover { color: #e5e5e5; }

/* ── 3D Background Blobs (from FrontOffice index.php) ── */
.blob-c {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  z-index: -1;
  overflow: hidden;
  background: #0b0f19;
}
.blob {
  position: absolute;
  width: 500px; height: 500px;
  background: linear-gradient(135deg, #ffbe33 0%, #e69c00 100%);
  filter: blur(80px);
  border-radius: 50%;
  opacity: 0.15;
  animation: float3d 20s infinite alternate ease-in-out;
}
.blob-1 { top: -100px; right: -100px; background: #ffbe33; }
.blob-2 { bottom: -150px; left: -100px; background: #2D6A4F; animation-delay: -5s; }

@keyframes float3d {
  0% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(-50px, 50px) scale(1.1); }
  66% { transform: translate(50px, -30px) scale(0.9); }
  100% { transform: translate(0, 0) scale(1); }
}

.premium-dashboard {
    /* Assure que le dashboard passe au-dessus des blobs */
    position: relative;
    z-index: 1;
}
</style>

<!-- Background Blobs -->
<div class="blob-c">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
</div>

<div class="premium-dashboard" id="smartfood-dashboard">
    <div class="dashboard-content">
        
        <!-- Header -->
        <div class="premium-header fade-in">
            <div class="header-titles">
                <h2>AI Insights Dashboard</h2>
                <p>Live intelligence from SmartFood System &bull; <?php echo date('M d, Y'); ?></p>
            </div>
            <div class="ai-badge-container">
                <div class="ai-badge <?php echo $aiConfidence; ?>">
                    <i class="fas fa-brain"></i> Confidence: <?php echo $aiConfidence; ?>
                </div>
                <div class="pulse-ring"></div>
            </div>
        </div>

        <!-- KPIs -->
        <div class="kpi-row fade-in delay-1">
            <div class="kpi-card kpi-glow-1">
                <div class="kpi-value counter" data-target="<?php echo $kpis['total_articles']; ?>">0</div>
                <div class="kpi-label">Total Articles</div>
            </div>
            <div class="kpi-card kpi-glow-2">
                <div class="kpi-value"><?php echo strtoupper($topTag); ?></div>
                <div class="kpi-label">Top Recommended Tag</div>
            </div>
            <div class="kpi-card kpi-glow-3">
                <div class="kpi-value counter" data-target="<?php echo $kpis['engagement_rate']; ?>" data-suffix="%">0</div>
                <div class="kpi-label">Global Engagement</div>
            </div>
            <div class="kpi-card kpi-glow-4">
                <div class="kpi-value counter" data-target="<?php echo $kpis['history_count']; ?>">0</div>
                <div class="kpi-label">Session Actions</div>
            </div>
        </div>

        <!-- Netflix Row -->
        <div class="section-title fade-in delay-2"><i class="fas fa-play text-danger"></i> Top Picks For You</div>
        <div class="netflix-row-container fade-in delay-2">
            <div class="netflix-row">
                <?php if(empty($recommendations)): ?>
                    <p style="color:#6b7280; padding-left:10px;">Keep exploring the site to unlock personalized recommendations.</p>
                <?php else: ?>
                    <?php foreach($recommendations as $rec): ?>
                        <div class="movie-card">
                            <div class="movie-title"><?php echo htmlspecialchars(mb_substr($rec['title'], 0, 55)); ?>…</div>
                            <div class="movie-score"><i class="fas fa-star"></i> <?php echo number_format($rec['ai_score'], 2); ?> Match</div>
                            <?php if(!empty($rec['ai_reasons'])): ?>
                                <div class="ai-reasons-panel">
                                    <div style="font-weight:700; margin-bottom:5px; color:#fff;">💡 Why AI Chose This:</div>
                                    <ul style="padding-left:15px; margin:0; line-height:1.5;">
                                        <?php foreach($rec['ai_reasons'] as $reason): ?>
                                            <li><?php echo $reason; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- YouTube Studio Charts -->
        <div class="section-title fade-in delay-3"><i class="fas fa-chart-line text-info"></i> Global Analytics</div>
        <div class="charts-grid fade-in delay-3">
            <!-- Line Chart -->
            <div class="chart-card">
                <h4 style="color:#9ca3af; font-size:0.9rem; margin-bottom:15px; font-weight:600;">PLATFORM VIEWS OVER TIME</h4>
                <div class="chart-container">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:24px;">
                <!-- Bar Chart -->
                <div class="chart-card">
                    <h4 style="color:#9ca3af; font-size:0.9rem; margin-bottom:15px; font-weight:600;">YOUR TOP INTERESTS</h4>
                    <div class="chart-container small">
                        <canvas id="tagsChart"></canvas>
                    </div>
                </div>
                
                <!-- Pie Chart -->
                <div class="chart-card">
                    <h4 style="color:#9ca3af; font-size:0.9rem; margin-bottom:15px; font-weight:600;">CONTENT CATEGORIES</h4>
                    <div class="chart-container small">
                        <canvas id="catChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-fallback">
            <span>Scan to View on Mobile:</span>
            <a href="#" onclick="showMobileQR('articles'); return false;"><i class="fas fa-qrcode"></i> Aperçu Articles</a>
            <a href="#" onclick="showMobileQR('comments'); return false;"><i class="fas fa-qrcode"></i> Aperçu Commentaires</a>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(5px);">
    <div style="background:#fff; padding:30px; border-radius:16px; text-align:center; max-width:350px; position:relative; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
        <button onclick="document.getElementById('qrModal').style.display='none'" style="position:absolute; top:10px; right:15px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#666;">&times;</button>
        <h3 style="color:#111827; margin-top:0; font-size:1.2rem; font-weight:700;" id="qrTitle">Scanner le QR Code</h3>
        <p style="color:#6b7280; font-size:0.9rem; margin-bottom:20px;">Utilisez l'appareil photo de votre téléphone pour voir l'aperçu mobile.</p>
        <div id="qrCodeContainer" style="margin-bottom:20px; min-height:200px; display:flex; align-items:center; justify-content:center;">
            <img id="qrImage" src="" alt="QR Code" style="display:none; max-width:100%; border-radius:8px;">
            <div id="qrLoader" style="color:#6b7280;">Génération...</div>
        </div>
    </div>
</div>

<script>
function showMobileQR(type) {
    const modal = document.getElementById('qrModal');
    const qrImage = document.getElementById('qrImage');
    const loader = document.getElementById('qrLoader');
    const title = document.getElementById('qrTitle');
    
    title.textContent = type === 'articles' ? 'Aperçu Mobile - Articles' : 'Aperçu Mobile - Commentaires';
    modal.style.display = 'flex';
    qrImage.style.display = 'none';
    loader.style.display = 'block';

    // Construct the absolute URL to the mobile_preview.php page
    const protocol = window.location.protocol;
    let host = window.location.host;
    
    // Auto-detect and fix localhost issue for mobile scanning
    // If the admin is on localhost, the phone won't be able to reach it. We inject the LAN IP.
    if (host.includes('localhost')) {
        const lanIp = '<?php echo getHostByName(getHostName()); ?>';
        host = host.replace('localhost', lanIp);
    }
    
    const path = window.location.pathname; // .../export_data.php
    const baseUrl = path.substring(0, path.lastIndexOf('/'));
    
    // Example: http://192.168.x.x/smartfood_jointure1/View/BackOffice/Jointure/mobile_preview.php?type=articles
    const mobileUrl = encodeURIComponent(`${protocol}//${host}${baseUrl}/mobile_preview.php?type=${type}`);
    
    // Generate QR Code via API
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${mobileUrl}&margin=10`;
    
    qrImage.onload = function() {
        loader.style.display = 'none';
        qrImage.style.display = 'block';
    };
    qrImage.src = qrApiUrl;
}
</script>

<script>
// --- Animated Counters ---
document.querySelectorAll('.counter').forEach(counter => {
    const target = +counter.getAttribute('data-target');
    const suffix = counter.getAttribute('data-suffix') || '';
    const duration = 1500; // ms
    const stepTime = Math.abs(Math.floor(duration / (target || 1)));
    let current = 0;
    
    if (target > 0) {
        const timer = setInterval(() => {
            current += Math.ceil(target / 50);
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.innerText = current + suffix;
        }, 30);
    }
});

// --- Chart.js Configuration ---
Chart.defaults.color = '#9ca3af';
Chart.defaults.font.family = "'Inter', sans-serif";

// 1. Line Chart (Views over time)
const viewsCtx = document.getElementById('viewsChart').getContext('2d');
let gradientBlue = viewsCtx.createLinearGradient(0, 0, 0, 400);
gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

new Chart(viewsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($viewsDates); ?>,
        datasets: [{
            label: 'Views',
            data: <?php echo json_encode($viewsCounts); ?>,
            borderColor: '#3b82f6',
            backgroundColor: gradientBlue,
            borderWidth: 3,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#3b82f6',
            pointBorderWidth: 2,
            pointRadius: 4,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false } },
            x: { grid: { display: false }, border: { display: false } }
        }
    }
});

// 2. Bar Chart (Top Interests)
const tagsCtx = document.getElementById('tagsChart').getContext('2d');
new Chart(tagsCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($topTagsLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($topTagsScores); ?>,
            backgroundColor: 'rgba(229, 9, 20, 0.8)',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { display: false, beginAtZero: true },
            x: { grid: { display: false }, border: { display: false } }
        }
    }
});

// 3. Doughnut Chart (Categories)
const catCtx = document.getElementById('catChart').getContext('2d');
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($catLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($catCounts); ?>,
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderWidth: 0,
            hoverOffset: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
