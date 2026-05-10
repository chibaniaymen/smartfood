<?php
// Mocking the layout structure for FrontOffice to keep it consistent with the template
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Feane - Book Event</title>
    <link rel="stylesheet" type="text/css" href="view/FrontOffice/css/bootstrap.css" />
    <link href="view/FrontOffice/css/style.css" rel="stylesheet" />
    <link href="view/FrontOffice/css/responsive.css" rel="stylesheet" />
    <link href="view/FrontOffice/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        .bg-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('view/FrontOffice/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(25px) brightness(0.25) grayscale(0.3);
            z-index: -1;
            transform: scale(1.1);
        }
        body.sub_page {
            background-color: transparent !important;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .food_section {
            background: transparent !important;
            flex-grow: 1;
        }
        .glass-box {
            background-color: rgba(15, 15, 20, 0.8) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 6px solid #ffbe33 !important;
            border-radius: 20px !important;
            padding: 40px !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 190, 51, 0.15) inset;
            color: white;
            position: relative;
        }
        /* Creative glowing top edge */
        .glass-box::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #f5a623, #d48c1a, #f5a623);
            border-radius: 20px 20px 0 0;
            z-index: 10;
        }
        .glass-input {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: white !important;
            transition: all 0.3s;
            border-radius: 8px;
        }
        .glass-input:focus {
            background-color: rgba(255, 255, 255, 0.12) !important;
            border-color: #ffbe33 !important;
            box-shadow: 0 0 15px rgba(255, 190, 51, 0.2) !important;
        }
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .text-label {
            color: #e0e0e0;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .footer_section {
            background: rgba(0,0,0,0.5) !important;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="sub_page">
    <div class="bg-blur"></div>
    <div class="hero_area">
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.php"><span>Feane</span></a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item active"><a class="nav-link" href="myEvents.php">Events</a></li>
                            
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    </div>

    <section class="food_section layout_padding">
        <div class="container">
            <?php if (!$event): ?>
                <div class="heading_container heading_center">
                    <h2>Event Not Found</h2>
                    <a href="myEvents.phpn btn-warning mt-4">Back to Events</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Event Details Column -->
                    <div class="col-md-5 mb-4">
                        <div class="glass-box">
                            <div class="detail-box">
                                <h2 style="color: #ffbe33; font-weight: 700; margin-bottom: 25px; font-family: 'Dancing Script', cursive; font-size: 3.5rem; text-shadow: 0 4px 10px rgba(255,190,51,0.3);"><?= htmlspecialchars($event->getTitle()) ?></h2>
                                <h6 class="mt-3 text-white" style="font-size: 1.15rem;"><i class="fa fa-calendar" style="color:#ffbe33; width: 25px;"></i> <?= date('F d, Y - H:i', strtotime($event->getEventDate())) ?></h6>
                                <h6 class="mt-3 text-white" style="font-size: 1.15rem;"><i class="fa fa-ticket" style="color:#ffbe33; width: 25px;"></i> €<?= number_format($event->getPrice(), 2) ?> per ticket</h6>
                                
                                <hr style="border-color: rgba(255,255,255,0.1); margin: 30px 0;">
                                <p class="mt-3" style="color: #ccc; line-height: 1.8; font-size: 1.05rem;"><?= nl2br(htmlspecialchars($event->getDescription())) ?></p>
                                
                                <div class="mt-5">
                                    <span class="badge badge-<?= $event->getStatus() == 'active' ? 'success' : 'danger' ?> px-4 py-2" style="font-size: 1rem; border-radius: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                        <?= ucfirst($event->getStatus()) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Form Column -->
                    <div class="col-md-7">
                        <div class="glass-box">
                            <h3 class="mb-4" style="color: #fff; font-weight: 700; font-size: 2rem;">Book Your Tickets</h3>
                            
                            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                                <div class="alert alert-success" style="background: rgba(40,167,69,0.2); border: 1px solid rgba(40,167,69,0.5); color: #fff; border-radius: 10px;">
                                    <strong><i class="fa fa-check-circle"></i> Success!</strong> Your reservation has been placed successfully.
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger" style="background: rgba(220,53,69,0.2); border: 1px solid rgba(220,53,69,0.5); color: #fff; border-radius: 10px;">
                                    <strong><i class="fa fa-exclamation-triangle"></i> Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($event->getStatus() !== 'active' || strtotime($event->getEventDate()) < time()): ?>
                                <div class="alert alert-warning" style="background: rgba(255,193,7,0.2); border: 1px solid rgba(255,193,7,0.5); color: #fff; border-radius: 10px;">
                                    <i class="fa fa-info-circle"></i> This event is no longer accepting reservations.
                                </div>
                            <?php else: ?>
                                <form action="reservations.php?action=book_front" method="POST" id="frontReservationForm" novalidate>
                                    <input type="hidden" name="event_id" value="<?= $event->getId() ?>">
                                    <input type="hidden" name="status" value="pending">
                                    
                                    <div class="form-row row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_name" class="text-label">Full Name</label>
                                            <input type="text" class="form-control glass-input" id="client_name" name="client_name" placeholder="John Doe">
                                            <small id="nameErr" class="text-danger" style="color: #ff6b6b !important; font-weight: bold;"></small>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_email" class="text-label">Email Address</label>
                                            <input type="text" class="form-control glass-input" id="client_email" name="client_email" placeholder="john@example.com">
                                            <small id="emailErr" class="text-danger" style="color: #ff6b6b !important; font-weight: bold;"></small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_phone" class="text-label">Phone Number</label>
                                            <input type="text" class="form-control glass-input" id="client_phone" name="client_phone" placeholder="+33612345678">
                                            <small id="phoneErr" class="text-danger" style="color: #ff6b6b !important; font-weight: bold;"></small>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="nb_tickets" class="text-label">Number of Tickets</label>
                                            <input type="text" class="form-control glass-input" id="nb_tickets" name="nb_tickets" value="1">
                                            <small id="ticketsErr" class="text-danger" style="color: #ff6b6b !important; font-weight: bold;"></small>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="notes" class="text-label">Special Requests / Notes</label>
                                        <textarea class="form-control glass-input" id="notes" name="notes" rows="3" placeholder="Any special requirements?"></textarea>
                                        <small id="notesErr" class="text-danger" style="color: #ff6b6b !important; font-weight: bold;"></small>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
                                        <h4 class="mb-0" style="color: #fff; font-weight: bold;">Total: <span id="totalPriceDisplay" style="color: #ffbe33; font-size: 2rem;">€<?= number_format($event->getPrice(), 2) ?></span></h4>
                                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #f5a623, #d48c1a); color: white; border-radius: 45px; padding: 12px 35px; font-weight: bold; box-shadow: 0 5px 15px rgba(245, 166, 35, 0.4); border: none; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px;">
                                            Confirm Booking
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer_section mt-5">
        <div class="container">
            <p>&copy; 2026 All Rights Reserved By Feane</p>
        </div>
    </footer>

    <?php if ($event && $event->getStatus() === 'active'): ?>
    <script>
        // Custom JS Validation for FrontOffice (mirroring BackOffice JS)
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('frontReservationForm');
            const ticketsInput = document.getElementById('nb_tickets');
            const totalDisplay = document.getElementById('totalPriceDisplay');
            const unitPrice = <?= (float)$event->getPrice() ?>;

            // Update Total Price Dynamic
            if (ticketsInput) {
                ticketsInput.addEventListener('keyup', function() {
                    let num = parseInt(this.value, 10);
                    if (!isNaN(num) && num > 0) {
                        totalDisplay.innerText = '€' + (num * unitPrice).toFixed(2);
                    } else {
                        totalDisplay.innerText = '€0.00';
                    }
                });
            }
            
            if (form) {
                form.addEventListener('submit', function (e) {
                    let isValid = true;

                    const name = document.getElementById('client_name').value.trim();
                    const email = document.getElementById('client_email').value.trim();
                    const phone = document.getElementById('client_phone').value.trim();
                    const tickets = ticketsInput.value.trim();
                    const notes = document.getElementById('notes').value.trim();

                    document.getElementById('nameErr').innerText = "";
                    document.getElementById('emailErr').innerText = "";
                    document.getElementById('phoneErr').innerText = "";
                    document.getElementById('ticketsErr').innerText = "";
                    document.getElementById('notesErr').innerText = "";

                    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']+$/;
                    if (name.length === 0) {
                        document.getElementById('nameErr').innerText = "Client name is required.";
                        isValid = false;
                    } else if (name.length < 3) {
                        document.getElementById('nameErr').innerText = "Client name must be at least 3 characters.";
                        isValid = false;
                    } else if (!nameRegex.test(name)) {
                        document.getElementById('nameErr').innerText = "Only letters, spaces, and hyphens are allowed.";
                        isValid = false;
                    }

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email.length === 0) {
                        document.getElementById('emailErr').innerText = "Email is required.";
                        isValid = false;
                    } else if (!emailRegex.test(email)) {
                        document.getElementById('emailErr').innerText = "Please enter a valid email address.";
                        isValid = false;
                    }

                    const phoneRegex = /^\+?[0-9\s\-]{8,15}$/;
                    if (phone.length === 0) {
                        document.getElementById('phoneErr').innerText = "Phone number is required.";
                        isValid = false;
                    } else if (!phoneRegex.test(phone) || phone.replace(/[\s\-+]/g, '').length < 8) {
                        document.getElementById('phoneErr').innerText = "Please enter a valid phone number.";
                        isValid = false;
                    }

                    const ticketNum = parseInt(tickets, 10);
                    if (tickets.length === 0) {
                        document.getElementById('ticketsErr').innerText = "Number of tickets is required.";
                        isValid = false;
                    } else if (isNaN(ticketNum)) {
                        document.getElementById('ticketsErr').innerText = "Must be a valid number.";
                        isValid = false;
                    } else if (ticketNum < 1) {
                        document.getElementById('ticketsErr').innerText = "You must reserve at least 1 ticket.";
                        isValid = false;
                    } else if (ticketNum > 10) {
                        document.getElementById('ticketsErr').innerText = "Maximum 10 tickets allowed per booking.";
                        isValid = false;
                    }

                    if (notes.length > 500) {
                        document.getElementById('notesErr').innerText = "Notes must not exceed 500 characters.";
                        isValid = false;
                    }

                    if (!isValid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>

