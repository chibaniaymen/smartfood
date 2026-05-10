<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feane Events - Browse All Events</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0f0f12;
            --accent-gold: #f5a623;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Open Sans', sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar-custom {
            background-color: transparent;
            position: fixed;
            width: 100%;
            z-index: 1000;
            padding: 20px 0;
            transition: all 0.4s ease;
        }
        .navbar-custom.scrolled {
            background-color: rgba(10, 10, 12, 0.98);
            backdrop-filter: blur(15px);
            padding: 10px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.7);
            border-bottom: 1px solid var(--glass-border);
        }
        .navbar-brand {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: #fff !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .nav-link {
            color: #fff !important;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1.5px;
            font-size: 0.9rem;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--accent-gold) !important;
        }
        .btn-admin {
            background-color: var(--accent-gold);
            color: #fff !important;
            border-radius: 45px;
            padding: 10px 25px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.3);
        }
        .btn-admin:hover {
            background-color: #d48c1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 166, 35, 0.5);
        }

        /* Parallax Hero */
        .hero-section {
            height: 100vh;
            background-image: linear-gradient(to bottom, rgba(10, 10, 15, 0.6), rgba(15, 15, 18, 1)), url('view/FrontOffice/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Parallax effect */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero-content {
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            padding: 0 20px;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .hero-title {
            font-family: 'Dancing Script', cursive;
            font-size: 5.5rem;
            color: var(--accent-gold);
            text-shadow: 0 0 30px rgba(245, 166, 35, 0.4);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero-divider {
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
            margin: 0 auto 30px;
            border-radius: 5px;
        }
        .hero-subtitle {
            font-size: 1.3rem;
            color: #dcdcdc;
            font-weight: 300;
            letter-spacing: 1px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Stats Ribbon */
        .stats-ribbon {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            padding: 50px 0;
            position: relative;
            z-index: 10;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .stat-item:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--accent-gold);
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 0 0 20px rgba(245, 166, 35, 0.2);
        }
        .stat-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #888;
            font-weight: 600;
        }

        /* Filters and Search */
        .filter-section {
            padding: 60px 0 40px;
        }
        .search-bar {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 8px 10px 8px 25px;
            display: flex;
            align-items: center;
            max-width: 650px;
            margin: 0 auto 40px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
            transition: border-color 0.3s ease;
        }
        .search-bar:focus-within {
            border-color: var(--accent-gold);
        }
        .search-input {
            background: transparent;
            border: none;
            color: #fff;
            flex-grow: 1;
            font-size: 1.1rem;
            outline: none;
        }
        .search-input::placeholder { color: #666; }
        .search-btn {
            background: linear-gradient(135deg, #f5a623, #d48c1a);
            border: none;
            border-radius: 50px;
            color: #fff;
            padding: 12px 35px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.3);
        }
        .search-btn:hover {
            box-shadow: 0 6px 20px rgba(245, 166, 35, 0.5);
            transform: scale(1.02);
        }
        .filter-chips {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #aaa;
            padding: 10px 30px;
            border-radius: 40px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .chip:hover, .chip.active {
            background: rgba(245, 166, 35, 0.1);
            color: var(--accent-gold);
            border-color: var(--accent-gold);
        }

        /* Glass-morphism Events Grid */
        .events-container {
            padding-bottom: 100px;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        .event-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-top: 2px solid var(--accent-gold);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .event-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(245, 166, 35, 0.15);
            border-color: rgba(245, 166, 35, 0.3);
            background: rgba(255, 255, 255, 0.06);
        }
        
        .event-img {
            height: 220px;
            background: linear-gradient(135deg, #111, #222);
            position: relative;
            overflow: hidden;
        }
        .event-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
            opacity: 0.7;
        }
        .event-card:hover .event-img img {
            transform: scale(1.1);
            opacity: 0.9;
        }
        
        /* Badges */
        .status-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
        }
        .status-active { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .status-completed { background: #007bff; color: white; }
        
        .price-pill {
            position: absolute;
            bottom: -25px;
            right: 25px;
            background: linear-gradient(45deg, #f5a623 0%, #ffbe33 100%);
            color: #fff;
            padding: 12px 25px;
            border-radius: 30px 5px 30px 5px; /* Creative morphing shape */
            font-weight: 900;
            font-size: 1.25rem;
            z-index: 10;
            border: 3px solid var(--bg-dark);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .event-card:hover .price-pill {
            transform: translateY(-8px) scale(1.05);
            border-radius: 5px 30px 5px 30px; /* Shape shifts on hover! */
            background: linear-gradient(45deg, #ffbe33 0%, #f5a623 100%);
            box-shadow: 0 15px 30px rgba(245, 87, 108, 0.6);
            border-color: #fff;
        }
        
        .card-body {
            padding: 35px 25px 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .event-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #fff;
            line-height: 1.3;
        }
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
            color: #a0a0a0;
            font-size: 0.95rem;
        }
        .event-meta i {
            color: var(--accent-gold);
            width: 25px;
            text-align: center;
        }
        
        /* Stars display */
        .stars-wrapper {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            width: fit-content;
        }
        .stars-wrapper i {
            color: var(--accent-gold);
            font-size: 0.9rem;
        }
        
        .card-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .rating-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .stars {
            color: var(--accent-gold);
            font-size: 0.85rem;
        }
        .review-count {
            color: #666;
            font-size: 0.75rem;
        }
        
        .card-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-action {
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-view {
            background: transparent;
            border: 2px solid var(--accent-gold);
            color: var(--accent-gold);
        }
        .btn-view:hover {
            background: var(--accent-gold);
            color: #fff;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.4);
        }
        .btn-review {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            color: #ccc;
        }
        .btn-review:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-color: rgba(255,255,255,0.2);
        }
        
        /* New Badge */
        .new-badge {
            background: #ffbe33;
            color: #000;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 800;
            margin-right: 8px;
            vertical-align: middle;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(to bottom, #222, var(--accent-gold)); 
            border-radius: 10px; 
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-gold); }

        /* Smooth Entrance Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .event-card {
            animation: fadeInUp 0.8s ease backwards;
        }
        .event-card:nth-child(1) { animation-delay: 0.1s; }
        .event-card:nth-child(2) { animation-delay: 0.2s; }
        .event-card:nth-child(3) { animation-delay: 0.3s; }
        .event-card:nth-child(4) { animation-delay: 0.4s; }
        .event-card:nth-child(5) { animation-delay: 0.5s; }
        .event-card:nth-child(6) { animation-delay: 0.6s; }

        /* Enhanced Search & Filters */
        .search-input:focus {
            box-shadow: 0 0 20px rgba(245, 166, 35, 0.2);
            border-color: var(--accent-gold);
            background: rgba(255,255,255,0.08);
        }
        .search-btn:active { transform: scale(0.95); }
        .chip:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            transform: translateY(-2px);
        }
        .chip.active {
            background: var(--accent-gold);
            color: #000;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(245, 166, 35, 0.4);
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px 0;
            text-align: center;
            border-top: 1px solid var(--glass-border);
            color: #888;
            font-size: 0.95rem;
            margin-top: 80px;
        }

        /* Autocomplete */
        .search-bar {
            position: relative;
        }
        .autocomplete-items {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 5px;
            z-index: 99;
            background: rgba(15, 15, 18, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            display: none;
        }
        .autocomplete-items div {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #ccc;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .autocomplete-items div:last-child { border-bottom: none; }
        .autocomplete-items div:hover {
            background: rgba(245, 166, 35, 0.15);
            color: var(--accent-gold);
        }
        .autocomplete-items div strong { color: #fff; }
        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-120%);
            min-width: 300px;
            max-width: calc(100% - 40px);
            z-index: 2100;
            padding: 14px 18px;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.25);
            color: #fff;
            font-weight: 600;
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: transform 0.35s ease, opacity 0.35s ease;
        }
        .toast-notification.toast-visible {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .toast-notification.toast-success {
            background: rgba(40, 167, 69, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .toast-notification.toast-error {
            background: rgba(220, 53, 69, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .toast-notification .toast-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
    </style>
</head>
<body>

    <!-- Transparent Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">Feane Events</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars" style="color: white; font-size: 1.5rem;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="myEvents.php">Events</a></li>
                </ul>
                
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
        <div id="eventToast" class="toast-notification toast-success">
            <span>Your booking was successful. Thank you!</span>
            <button type="button" class="toast-close" aria-label="Close">&times;</button>
        </div>
    <?php elseif (!empty($_GET['error'])): ?>
        <div id="eventToast" class="toast-notification toast-error">
            <span><?php echo htmlspecialchars($_GET['error']); ?></span>
            <button type="button" class="toast-close" aria-label="Close">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Full-screen Parallax Hero -->
    <section class="hero-section">
        <div class="hero-content container">
            <h1 class="hero-title">Browse All Events</h1>
            <div class="hero-divider"></div>
            <p class="hero-subtitle">Immerse yourself in extraordinary moments. From exclusive culinary workshops to grand concerts, find the perfect event tailored for you.</p>
        </div>
    </section>

    <?php
        // Prepare Data Defensively
        $evts = $events ?? [];
        $totalEvents = count($evts);
        
        $locations = [];
        $thisMonth = 0;
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        foreach ($evts as $evt) {
            $loc = $evt['location_id'] ?? $evt['location_name'] ?? 'Unknown';
            $locations[$loc] = true;
            
            $evtDate = strtotime($evt['event_date'] ?? 'now');
            if (date('m', $evtDate) === $currentMonth && date('Y', $evtDate) === $currentYear) {
                $thisMonth++;
            }
        }
        $totalLocations = count($locations);
    ?>

    <!-- Stats Animated Ribbon -->
    <div class="stats-ribbon">
        <div class="container">
            <div class="row">
                <div class="col-md-4 stat-item">
                    <div class="stat-number" data-target="<?= $totalEvents ?>">0</div>
                    <div class="stat-label">Total Events</div>
                </div>
                <div class="col-md-4 stat-item">
                    <div class="stat-number" data-target="<?= $totalLocations ?>">0</div>
                    <div class="stat-label">Unique Locations</div>
                </div>
                <div class="col-md-4 stat-item">
                    <div class="stat-number" data-target="<?= $thisMonth ?>">0</div>
                    <div class="stat-label">Events This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="filter-section container">
        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search for events by name, location or keyword..." autocomplete="off">
            <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            <div id="autocomplete-list" class="autocomplete-items"></div>
        </div>
        <div class="filter-chips">
            <div class="chip active" data-filter="all">All</div>
            <div class="chip" data-filter="today">Today</div>
            <div class="chip" data-filter="expensive">Expensive</div>
            <div class="chip" data-filter="not-expensive">Not Expensive</div>
        </div>
    </div>

    <!-- Glass-Morphism Grid -->
    <div class="events-container container">
        <?php if ($totalEvents === 0): ?>
            <div class="text-center" style="padding: 100px 0; background: var(--glass-bg); border-radius: 20px; border: 1px dashed var(--glass-border);">
                <i class="fa-regular fa-calendar-xmark" style="font-size: 5rem; color: #444; margin-bottom: 25px;"></i>
                <h3 style="color: #888; font-weight: 300;">Aucun événement n'est disponible pour le moment.</h3>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($evts as $event): 
                    $eid = (int)($event['id'] ?? 0);
                    $status = strtolower($event['status'] ?? 'active');
                    $statusClass = 'status-active';
                    if ($status === 'cancelled') $statusClass = 'status-cancelled';
                    elseif ($status === 'completed') $statusClass = 'status-completed';
                    
                    $price = (float)($event['price'] ?? 0);
                    $priceText = $price > 0 ? number_format($price, 2) . ' €' : 'Gratuit';
                    
                    $dateStr = date('d M Y, H:i', strtotime($event['event_date'] ?? 'now'));
                    $location = $event['location_name'] ?? $event['location_id'] ?? 'Lieu à définir';
                    $title = $event['title'] ?? 'Événement Sans Titre';
                    
                    $stats = $reviewStats[$eid] ?? [];
                    $avg = (float)($stats['avg_rating'] ?? 0);
                    $totalRevs = (int)($stats['total_reviews'] ?? 0);
                ?>
                <div class="event-card" 
                     data-title="<?= htmlspecialchars(strtolower($title)) ?>" 
                     data-location="<?= htmlspecialchars(strtolower($location)) ?>"
                     data-price="<?= (float)$price ?>"
                     data-date="<?= date('Y-m-d', strtotime($event['event_date'] ?? 'now')) ?>">
                    <div class="event-img">
                        <img src="view/FrontOffice/images/event_default.png" alt="Event Cover">
                        <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
                        <span class="price-pill">
                            <i class="fa-solid fa-ticket-simple"></i> <?= htmlspecialchars($priceText) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h3 class="event-title"><?= htmlspecialchars($title) ?></h3>
                        
                        <div class="event-meta">
                            <div><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars($dateStr) ?></div>
                            <div><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars((string)$location) ?></div>
                        </div>
                        
                        <div class="stars-wrapper">
                            <?php if ($totalRevs > 0): ?>
                                <div>
                                    <?php 
                                    $rounded = round($avg);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rounded ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star" style="color: #666;"></i>';
                                    }
                                    ?>
                                </div>
                                <strong style="color: var(--accent-gold); font-size: 1.1rem;"><?= number_format($avg, 1) ?></strong>
                                <span style="font-size: 0.85rem; color: #999;">(<?= $totalRevs ?> avis)</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">NOUVEAU</span>
                                <span style="font-size: 0.85rem; color: #999; margin-left: 5px;">Aucun avis</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-actions">
                            <a href="events.php?action=show&id=<?= $eid ?>" class="btn-action btn-view">
                                <i class="fa-solid fa-ticket-simple"></i> Book
                            </a>
                            <a href="review.php?action=show&event_id=<?= $eid ?>" class="btn-action btn-review">
                                <i class="fa-solid fa-comment-dots"></i> Avis & Commenter
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Feane Events. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Vanilla JS Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navbar Scroll Transition
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 80) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Animated Counters for Stats Ribbon
            const counters = document.querySelectorAll('.stat-number');
            const animationSpeed = 150; // lower is faster
            
            const runCounterAnimation = () => {
                counters.forEach(counter => {
                    const updateCount = () => {
                        const target = +counter.getAttribute('data-target');
                        const current = +counter.innerText;
                        const increment = target / animationSpeed;
                        
                        if (current < target) {
                            counter.innerText = Math.ceil(current + increment);
                            setTimeout(updateCount, 15);
                        } else {
                            counter.innerText = target;
                        }
                    };
                    updateCount();
                });
            };

            // Intersection Observer to trigger animation when scrolled into view
            const statsRibbon = document.querySelector('.stats-ribbon');
            if (statsRibbon && window.IntersectionObserver) {
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        runCounterAnimation();
                        observer.disconnect();
                    }
                }, { threshold: 0.5 });
                observer.observe(statsRibbon);
            } else {
                runCounterAnimation(); // Fallback
            }

            // Filter Chips Interaction (Visual only as per UI design)
            const chips = document.querySelectorAll('.chip');
            let currentFilter = 'all';
            
            chips.forEach(chip => {
                chip.addEventListener('click', function() {
                    chips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter') || 'all';
                    if (typeof applyAllFilters === 'function') applyAllFilters();
                });
            });

            // Autocomplete Search System
            const eventsData = <?= json_encode(array_map(function($e) {
                return [
                    'id' => $e['id'] ?? 0,
                    'title' => $e['title'] ?? '',
                    'location' => $e['location_name'] ?? $e['location_id'] ?? ''
                ];
            }, $evts)) ?>;
            
            const searchInput = document.getElementById('searchInput');
            const autocompleteList = document.getElementById('autocomplete-list');
            const eventCards = document.querySelectorAll('.event-card');

            function applyAllFilters() {
                const query = searchInput.value.toLowerCase().trim();
                const today = new Date().toISOString().split('T')[0];
                let foundAny = false;

                eventCards.forEach(card => {
                    const title = card.getAttribute('data-title');
                    const location = card.getAttribute('data-location');
                    const price = parseFloat(card.getAttribute('data-price'));
                    const date = card.getAttribute('data-date');

                    let matchesSearch = title.includes(query) || location.includes(query);
                    let matchesChip = true;

                    if (currentFilter === 'today') {
                        matchesChip = (date === today);
                    } else if (currentFilter === 'expensive') {
                        matchesChip = (price > 100);
                    } else if (currentFilter === 'not-expensive') {
                        matchesChip = (price < 100);
                    }

                    if (matchesSearch && matchesChip) {
                        card.style.display = 'block';
                        foundAny = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show a "no results" message if needed
                let noResultsMsg = document.getElementById('no-results-msg');
                if (!foundAny) {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.id = 'no-results-msg';
                        noResultsMsg.className = 'text-center py-5 w-100';
                        noResultsMsg.innerHTML = `<h4 style="color: #888; background: rgba(255,255,255,0.05); padding: 40px; border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1);"><i class="fa-solid fa-face-frown" style="font-size: 3rem; display: block; margin-bottom: 15px; color: var(--accent-gold);"></i> Aucun événement ne correspond à ces critères.</h4>`;
                        document.querySelector('.events-grid').appendChild(noResultsMsg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }

            // Chip Filtering
            chips.forEach(chip => {
                chip.addEventListener('click', function() {
                    chips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    applyAllFilters();
                });
            });

            if (searchInput && autocompleteList) {
                searchInput.addEventListener('input', function() {
                    let val = this.value;
                    autocompleteList.innerHTML = '';
                    
                    applyAllFilters();

                    if (!val) {
                        autocompleteList.style.display = 'none';
                        return false;
                    }
                    
                    let count = 0;
                    let html = '';
                    for (let i = 0; i < eventsData.length; i++) {
                        if (eventsData[i].title.toUpperCase().includes(val.toUpperCase()) || 
                            eventsData[i].location.toUpperCase().includes(val.toUpperCase())) {
                            
                            let regex = new RegExp("(" + val + ")", "gi");
                            let titleStr = eventsData[i].title.replace(regex, "<strong>$1</strong>");
                            
                            html += `<div onclick="selectEvent('${eventsData[i].title.replace(/'/g, "\\'")}')">`;
                            html += `<i class="fa-solid fa-ticket-simple" style="color:var(--accent-gold); font-size: 0.9rem;"></i> `;
                            html += `<span>${titleStr}</span>`;
                            html += `<span style="margin-left:auto; font-size:0.8rem; color:#888;"><i class="fa-solid fa-location-dot"></i> ${eventsData[i].location}</span>`;
                            html += `</div>`;
                            count++;
                        }
                        if (count >= 7) break;
                    }
                    
                    if (count > 0) {
                        autocompleteList.innerHTML = html;
                        autocompleteList.style.display = 'block';
                    } else {
                        autocompleteList.style.display = 'none';
                    }
                });

                window.selectEvent = function(title) {
                    searchInput.value = title;
                    autocompleteList.style.display = 'none';
                    applyAllFilters();
                };

                document.querySelector('.search-btn').addEventListener('click', () => {
                    applyAllFilters();
                });

                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        applyAllFilters();
                        autocompleteList.style.display = 'none';
                    }
                });

                document.addEventListener('click', function (e) {
                    if (e.target !== searchInput) {
                        autocompleteList.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toast = document.getElementById('eventToast');
            if (!toast) return;

            toast.classList.add('toast-visible');
            var timer = setTimeout(function () {
                toast.classList.remove('toast-visible');
            }, 5000);

            var closeButton = toast.querySelector('.toast-close');
            if (closeButton) {
                closeButton.addEventListener('click', function () {
                    toast.classList.remove('toast-visible');
                    clearTimeout(timer);
                });
            }
        });
    </script>
</body>
</html>
