<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - <?= htmlspecialchars($event ? $event->getTitle() : 'Événement inconnu') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background-color: #1a1a2e; }
        .text-gold { color: #f5a623; }
        .bg-gold { background-color: #f5a623; color: white; }
        .btn-gold-outline { border: 1px solid #f5a623; color: #f5a623; }
        .btn-gold-outline:hover { background-color: #f5a623; color: white; }
        .star-picker i { font-size: 2rem; color: #ccc; cursor: pointer; transition: color 0.2s; }
        .star-picker i.fas { color: #f5a623; }
        .avatar { width: 50px; height: 50px; border-radius: 50%; background-color: #1a1a2e; color: #f5a623; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body>

<!-- Section 1 : Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-gold fw-bold fs-3" href="myEvents.php">Feane Events</a>
        <a href="myEvents.php" class="btn btn-gold-outline"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    </div>
</nav>

<div class="container py-5">
    
    <!-- Section 2 : Hero Card -->
    <?php
        $status = $event ? $event->getStatus() : 'active';
        $statusClass = 'bg-success';
        if ($status === 'cancelled') $statusClass = 'bg-danger';
        elseif ($status === 'completed') $statusClass = 'bg-primary';
    ?>
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body text-center py-5">
            <span class="badge <?= $statusClass ?> mb-3 px-3 py-2"><?= strtoupper(htmlspecialchars($status)) ?></span>
            <h1 class="fw-bold mb-3"><?= htmlspecialchars($event ? $event->getTitle() : 'Titre non disponible') ?></h1>
            <p class="fs-5 text-muted mb-3">
                <i class="fa-regular fa-calendar text-gold"></i> <?= date('d/m/Y H:i', strtotime($event ? $event->getEventDate() : 'now')) ?>
            </p>
            <div class="fs-4 fw-bold text-dark">
                <?= number_format((float)($event ? $event->getPrice() : 0), 2) ?> €
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            
            <!-- Section 3 : Rating Summary Card -->
            <?php
                $avgRating = (float)($stats['avg_rating'] ?? 0);
                $totalReviews = (int)($stats['total_reviews'] ?? 0);
                
                $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                foreach (($reviews ?? []) as $rev) {
                    $r = (int)($rev['rating'] ?? 0);
                    if ($r >= 1 && $r <= 5) {
                        $starCounts[$r]++;
                    }
                }
            ?>
            <div class="card shadow-sm border-0 p-4 mb-4 text-center">
                <h4 class="mb-4">Note Globale</h4>
                <div class="display-3 fw-bold text-gold mb-2"><?= number_format($avgRating, 1) ?></div>
                <div class="mb-2 fs-5">
                    <?php 
                    $roundedAvg = round($avgRating);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $roundedAvg) {
                            echo '<i class="fas fa-star text-gold"></i>';
                        } else {
                            echo '<i class="far fa-star text-gold"></i>';
                        }
                    }
                    ?>
                </div>
                <p class="text-muted mb-4"><?= $totalReviews ?> avis</p>
                
                <?php 
                $denominator = max(1, $totalReviews);
                foreach ([5, 4, 3, 2, 1] as $starLevel): 
                    $count = $starCounts[$starLevel];
                    $percent = ($count / $denominator) * 100;
                ?>
                <div class="d-flex align-items-center mb-2" style="font-size: 0.9rem;">
                    <div style="width: 40px; text-align: right;" class="text-muted"><?= $starLevel ?> <i class="fas fa-star text-gold"></i></div>
                    <div class="flex-grow-1 mx-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-gold" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div style="width: 30px; text-align: left;" class="text-muted"><?= $count ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Section 5 & 6 : Write Review Form Card with Error Display -->
            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-4">Écrire un avis</h4>
                
                <!-- Section 6 : Error display -->
                <?php $errs = $errors ?? []; if (!empty($errs)): ?>
                    <?php foreach ($errs as $err): ?>
                        <div class="alert alert-danger p-2 mb-2">
                            <?= htmlspecialchars($err) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form action="review.php?action=add&event_id=<?= (int)($eventId ?? 0) ?>" method="POST">
                    
                    <div class="mb-3 text-center">
                        <label class="form-label text-muted d-block mb-2">Votre note</label>
                        <div class="star-picker" id="interactiveStarPicker">
                            <i class="far fa-star" data-value="1"></i>
                            <i class="far fa-star" data-value="2"></i>
                            <i class="far fa-star" data-value="3"></i>
                            <i class="far fa-star" data-value="4"></i>
                            <i class="far fa-star" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingHiddenInput" value="0">
                    </div>

                    <div class="mb-3">
                        <input type="text" name="author_name" class="form-control" placeholder="Votre nom">
                    </div>

                    <div class="mb-4">
                        <textarea name="comment" class="form-control" rows="4" placeholder="Votre commentaire minimum 10 caractères"></textarea>
                    </div>

                    <button type="submit" class="btn w-100 py-2" style="background-color: #f5a623; color: white;">Publier mon avis</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <h3 class="mb-4 fw-bold">Derniers avis</h3>
            
            <?php $revs = $reviews ?? []; if (count($revs) === 0): ?>
                <!-- Section 7 : Empty State -->
                <div class="card shadow-sm border-0 p-5 text-center h-100 d-flex flex-column justify-content-center">
                    <div>
                        <i class="fa-regular fa-comments text-muted mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                        <h4 class="text-muted">Aucun avis n'a encore été publié.</h4>
                        <p class="text-muted">Soyez le premier à partager votre expérience !</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Section 4 : Reviews List -->
                <?php foreach ($revs as $review): 
                    $author = $review['author_name'] ?? 'Anonyme';
                    $initial = strtoupper(substr($author, 0, 1));
                    $date = date('d/m/Y', strtotime($review['created_at'] ?? 'now'));
                    $rValue = (int)($review['rating'] ?? 0);
                    $commentText = $review['comment'] ?? '';
                ?>
                <div class="card shadow-sm border-0 p-4 mb-3">
                    <div class="d-flex gap-3">
                        <div class="avatar flex-shrink-0"><?= htmlspecialchars($initial) ?></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0 fw-bold"><?= htmlspecialchars($author) ?></h5>
                                <small class="text-muted"><?= htmlspecialchars($date) ?></small>
                            </div>
                            <div class="mb-2">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $rValue): ?>
                                        <i class="fas fa-star text-gold"></i>
                                    <?php else: ?>
                                        <i class="far fa-star" style="color:#ccc;"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($commentText)) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stars = document.querySelectorAll("#interactiveStarPicker i");
        const hiddenInput = document.getElementById("ratingHiddenInput");
        
        stars.forEach(function(star) {
            star.addEventListener("click", function() {
                const val = parseInt(this.getAttribute("data-value"));
                hiddenInput.value = val;
                
                stars.forEach(function(s) {
                    const sVal = parseInt(s.getAttribute("data-value"));
                    if (sVal <= val) {
                        s.classList.remove("far");
                        s.classList.add("fas");
                        s.style.color = "#f5a623";
                    } else {
                        s.classList.remove("fas");
                        s.classList.add("far");
                        s.style.color = "#ccc";
                    }
                });
            });
        });
    });
</script>
</body>
</html>
