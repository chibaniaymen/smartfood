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
    <style>
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
<body class="sub_page">
    <div class="hero_area">
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.php"><span>Feane</span></a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item active"><a class="nav-link" href="myEvents.php">My Events</a></li>
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

