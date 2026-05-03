<?php
// Mocking the layout structure for FrontOffice to keep it consistent with the template
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Feane - My Events</title>
    <link rel="stylesheet" type="text/css" href="view/FrontOffice/css/bootstrap.css" />
    <link href="view/FrontOffice/css/style.css" rel="stylesheet" />
    <link href="view/FrontOffice/css/responsive.css" rel="stylesheet" />
    <link href="view/FrontOffice/css/font-awesome.min.css" rel="stylesheet" />
</head>
<body class="sub_page">
    <div class="hero_area">
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.php"><span>Feane</span></a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item active"><a class="nav-link" href="events.php?action=front">My Events</a></li>
                            <li class="nav-item">
                                <a class="btn btn-warning ml-lg-3" href="view/BackOffice/dashboard.php" style="background-color: #ffbe33; color: white; border-radius: 20px; font-weight: bold;">Admin</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    </div>

    <section class="food_section layout_padding">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>My Events</h2>
            </div>
            <div class="row">
                <?php foreach ($events as $e): ?>
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="box" style="background-color: #121212; border: 1px solid rgba(255,255,255,0.05); color: white; border-radius: 15px; padding: 25px;">
                        <div class="detail-box">
                            <h5 style="color: #ffbe33;"><?php echo htmlspecialchars($e['title']); ?></h5>
                            <p><?php echo substr(htmlspecialchars($e['description']), 0, 100); ?>...</p>
                            <div class="options">
                                <h6><?php echo date('M d, Y', strtotime($e['event_date'])); ?></h6>
                                <a href="events.php?action=show&id=<?php echo $e['id']; ?>" style="background-color: #ffbe33; color: white; border-radius: 45px; padding: 5px 20px;">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer_section">
        <div class="container">
            <p>&copy; 2026 All Rights Reserved By Feane</p>
        </div>
    </footer>
</body>
</html>

