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
</head>
<body class="sub_page">
    <div class="hero_area">
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.html"><span>Feane</span></a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                            <li class="nav-item active"><a class="nav-link" href="events.php?action=front">Events</a></li>
                            <li class="nav-item"><a class="nav-link" href="view/BackOffice/dashboard.php">Admin</a></li>
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
                    <a href="events.php?action=front" class="btn btn-warning mt-4">Back to Events</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Event Details Column -->
                    <div class="col-md-5 mb-4">
                        <div class="box" style="background-color: #222831; color: white; border-radius: 15px; padding: 30px;">
                            <div class="detail-box">
                                <h2 style="color: #ffbe33;"><?= htmlspecialchars($event->getTitle()) ?></h2>
                                <h6 class="mt-3 text-white"><i class="fa fa-calendar"></i> <?= date('F d, Y - H:i', strtotime($event->getEventDate())) ?></h6>
                                <h6 class="mt-2 text-white"><i class="fa fa-ticket"></i> €<?= number_format($event->getPrice(), 2) ?> per ticket</h6>
                                
                                <hr style="border-color: #555;">
                                <p class="mt-3"><?= nl2br(htmlspecialchars($event->getDescription())) ?></p>
                                
                                <div class="mt-4">
                                    <span class="badge badge-<?= $event->getStatus() == 'active' ? 'success' : 'danger' ?> p-2">
                                        <?= ucfirst($event->getStatus()) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Form Column -->
                    <div class="col-md-7">
                        <div class="box" style="background-color: #f8f9fa; border-radius: 15px; padding: 30px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <h3 class="mb-4">Book Your Tickets</h3>
                            
                            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                                <div class="alert alert-success">
                                    <strong>Success!</strong> Your reservation has been placed successfully.
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger">
                                    <strong>Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($event->getStatus() !== 'active' || strtotime($event->getEventDate()) < time()): ?>
                                <div class="alert alert-warning">
                                    This event is no longer accepting reservations.
                                </div>
                            <?php else: ?>
                                <!-- NO HTML5 Validation allowed -->
                                <form action="reservations.php?action=book_front" method="POST" id="frontReservationForm" novalidate>
                                    <input type="hidden" name="event_id" value="<?= $event->getId() ?>">
                                    <input type="hidden" name="status" value="pending">
                                    
                                    <div class="form-row row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_name">Full Name</label>
                                            <input type="text" class="form-control" id="client_name" name="client_name" placeholder="John Doe">
                                            <small id="nameErr" class="text-danger"></small>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_email">Email Address</label>
                                            <input type="text" class="form-control" id="client_email" name="client_email" placeholder="john@example.com">
                                            <small id="emailErr" class="text-danger"></small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="client_phone">Phone Number</label>
                                            <input type="text" class="form-control" id="client_phone" name="client_phone" placeholder="+33612345678">
                                            <small id="phoneErr" class="text-danger"></small>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="nb_tickets">Number of Tickets</label>
                                            <input type="text" class="form-control" id="nb_tickets" name="nb_tickets" value="1">
                                            <small id="ticketsErr" class="text-danger"></small>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="notes">Special Requests / Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        <small id="notesErr" class="text-danger"></small>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="text-primary mb-0">Total: <span id="totalPriceDisplay">€<?= number_format($event->getPrice(), 2) ?></span></h4>
                                        <button type="submit" class="btn" style="background-color: #ffbe33; color: white; border-radius: 45px; padding: 10px 30px;">
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

