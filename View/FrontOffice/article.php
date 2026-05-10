<?php
require_once __DIR__ . '/../../config.php';


require_once __DIR__ . '/../../controller/ArticleController.php';
require_once __DIR__ . '/../../controller/CommentaireController.php';



$articleController     = new ArticleController($pdo);
$commentaireController = new CommentaireController($pdo);

if (!isset($_GET['id']) || !isValidId($_GET['id'])) redirect('index.php');
$articleId = (int)$_GET['id'];
$article   = $articleController->getById($articleId);
if (!$article || $article['status'] !== 'published') redirect('index.php');

$comments    = $commentaireController->getByArticle($articleId, true); // Only approved

// Tracking de la vue
$articleController->trackView($articleId);

// Métier Avancé: Recommandations IA Hybride
$aiRecommendations = $articleController->recommendations($articleId);

$errors   = [];
$success  = '';
$formData = ['author' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $formData['author']  = trim($_POST['author']  ?? '');
    $formData['content'] = trim($_POST['content'] ?? '');
    $result = $commentaireController->create($articleId, $formData);
    if ($result['success']) {
        $success  = $result['message'];
        $formData = ['author' => '', 'content' => ''];
        $comments = $commentaireController->getByArticle($articleId, true);
    } else {
        $errors['general'] = $result['message'];
    }
}

$readTime = max(1, ceil(str_word_count($article['content']) / 200));
$icons    = ['🥦','🍎','🥑','🫐','🌿','🍋','🥕','🫚'];
$icon     = $icons[$article['id'] % count($icons)];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo h($article['title']); ?> — SmartFood</title>
  <link rel="icon" href="images/favicon.png" type="image/png"/>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/responsive.css"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green: #2D6A4F; --green-lt: #52B788; --orange: #E76F51;
      --dark: #1a1a2e; --muted: #6c757d; --border: #e9ecef;
      --radius: 16px;
      --ff-head: 'Playfair Display', Georgia, serif;
      --ff-body: 'Inter', sans-serif;
    }
    body { font-family: var(--ff-body); background: #f8f9fa; color: var(--dark); }

    /* NAV */
    .nav { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; height: 68px; }
    .nav-brand { font-family: var(--ff-head); font-size: 1.6rem; font-weight: 700; color: var(--green); text-decoration: none; }
    .nav-brand span { color: var(--orange); }
    .nav-back { display: flex; align-items: center; gap: 8px; color: var(--muted); text-decoration: none; font-size: .85rem; font-weight: 500; padding: 8px 16px; border-radius: 50px; border: 1.5px solid var(--border); transition: all .2s; }
    .nav-back:hover { border-color: var(--green); color: var(--green); text-decoration: none; }

    /* HERO */
    .article-hero { background: linear-gradient(135deg, var(--green), #1B4332); padding: 60px 40px; display: flex; align-items: center; gap: 40px; }
    .article-hero-icon { font-size: 6rem; flex-shrink: 0; }
    .article-hero-content h1 { font-family: var(--ff-head); font-size: 2.2rem; color: white; line-height: 1.25; margin-bottom: 16px; }
    .hero-meta { display: flex; gap: 20px; font-size: .82rem; color: rgba(255,255,255,.75); }
    .hero-meta i { margin-right: 5px; }

    /* LAYOUT */
    .article-layout { max-width: 1100px; margin: 0 auto; padding: 48px 24px 80px; display: grid; grid-template-columns: 1fr 300px; gap: 40px; }

    /* BODY */
    .article-body { background: white; border-radius: var(--radius); padding: 48px 52px; box-shadow: 0 2px 20px rgba(0,0,0,.06); }
    .article-lead { font-size: 1.1rem; color: #495057; line-height: 1.8; padding-bottom: 24px; margin-bottom: 24px; border-bottom: 2px solid var(--border); font-style: italic; }
    .article-content { font-size: 1rem; line-height: 1.85; color: #3d3d3d; white-space: pre-wrap; }
    .share-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 20px 0; margin: 32px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
    .share-bar span { font-size: .85rem; font-weight: 600; }
    .share-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 50px; font-size: .8rem; font-weight: 500; text-decoration: none; }
    .share-btn.tw { background: #1DA1F2; color: white; }
    .share-btn.fb { background: #1877F2; color: white; }

    /* READING PROGRESS */
    .reading-progress { position: fixed; top: 68px; left: 0; right: 0; height: 3px; background: var(--border); z-index: 99; }
    .reading-progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, var(--green), var(--orange)); transition: width .1s; }

    /* COMMENTS */
    .comments-section { margin-top: 40px; }
    .section-heading { font-family: var(--ff-head); font-size: 1.5rem; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .section-heading::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .comment-card { display: flex; gap: 16px; margin-bottom: 20px; }
    .comment-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, var(--green-lt), var(--green)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: .9rem; flex-shrink: 0; text-transform: uppercase; }
    .comment-bubble { flex: 1; background: #f8f9fa; border-radius: 0 12px 12px 12px; padding: 14px 18px; }
    .comment-author { font-weight: 600; font-size: .88rem; }
    .comment-date { font-size: .75rem; color: #adb5bd; margin-left: 10px; }
    .comment-text { font-size: .88rem; color: #495057; line-height: 1.65; margin-top: 6px; }
    .no-comments { text-align: center; padding: 32px; background: #f8f9fa; border-radius: var(--radius); color: var(--muted); }

    /* FORM */
    .comment-form-wrap { background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border: 1.5px solid #86efac; border-radius: var(--radius); padding: 28px 32px; margin-top: 32px; }
    .form-title { font-family: var(--ff-head); font-size: 1.2rem; color: var(--dark); margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: .82rem; font-weight: 600; }
    .form-control { padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: .9rem; font-family: var(--ff-body); transition: border-color .2s; width: 100%; background: white; }
    .form-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(45,106,79,.1); }
    .form-control.is-invalid { border-color: #dc3545; }
    .error-msg { color: #dc3545; font-size: .78rem; display: none; margin-top: 4px; }
    .error-msg.show { display: block; }
    .char-counter { font-size: .75rem; color: #adb5bd; text-align: right; margin-top: 4px; }
    .char-counter.warn { color: #fd7e14; }
    .char-counter.danger { color: #dc3545; }
    .btn-submit { background: var(--green); color: white; border: none; padding: 12px 32px; border-radius: 50px; font-size: .92rem; font-weight: 600; cursor: pointer; font-family: var(--ff-body); display: inline-flex; align-items: center; gap: 8px; transition: background .2s; }
    .btn-submit:hover { background: #1b4332; }
    .alert-success { background: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; padding: 12px 16px; border-radius: 10px; font-size: .88rem; margin-bottom: 16px; }
    .alert-danger  { background: #f8d7da; border: 1px solid #f5c2c7; color: #842029; padding: 12px 16px; border-radius: 10px; font-size: .88rem; margin-bottom: 16px; }

    /* SIDEBAR */
    .sidebar { display: flex; flex-direction: column; gap: 24px; }
    .sidebar-card { background: white; border-radius: var(--radius); padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
    .sidebar-title { font-family: var(--ff-head); font-size: 1.05rem; color: var(--dark); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--green); display: inline-block; }
    .related-card { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid var(--border); text-decoration: none; color: inherit; }
    .related-card:last-child { border-bottom: none; }
    .related-card:hover { color: var(--green); text-decoration: none; }
    .related-icon { width: 48px; height: 48px; border-radius: 8px; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .related-title { font-size: .82rem; font-weight: 600; line-height: 1.35; margin-bottom: 4px; }
    .related-date  { font-size: .72rem; color: #adb5bd; }

    /* FOOTER */
    .footer { background: var(--dark); color: rgba(255,255,255,.5); padding: 28px 40px; display: flex; justify-content: space-between; align-items: center; font-size: .82rem; }
    .footer a { color: rgba(255,255,255,.5); text-decoration: none; }
    .footer a:hover { color: white; }
    .footer-brand { font-family: var(--ff-head); font-size: 1.2rem; color: white; margin-bottom: 4px; }

    @media (max-width: 900px) {
      .article-layout { grid-template-columns: 1fr; }
      .article-hero { flex-direction: column; padding: 32px 24px; gap: 20px; }
      .article-hero-content h1 { font-size: 1.6rem; }
      .article-body { padding: 28px 24px; }
      .nav { padding: 0 16px; }
    }
  </style>
</head>
<body>

<div class="reading-progress">
  <div class="reading-progress-bar" id="progressBar"></div>
</div>

  <!-- ════ NAVBAR ════ -->
  <header class="header_section" style="background-color: #222831;">
    <div class="container-fluid">
      <nav class="navbar navbar-expand-lg custom_nav-container">

        <a class="navbar-brand" href="index.php">
          <span>SmartFood</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
          <span></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php#articles">Articles</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php#about">À propos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="searchCommentaires.php">Commentaires</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="addArticle.php">Blog <span class="sr-only">(current)</span></a>
            </li>
          </ul>

          <div class="user_option">
            <a href="#" class="user_link">
              <i class="fa fa-user" aria-hidden="true"></i>
            </a>
            <a href="#" class="cart_link">
              <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                   xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                   viewBox="0 0 100 100" xml:space="preserve">
                <g><path d="M75,67H31L20.3,25.5C20.1,24.6,19.3,24,18.4,24H10c-1.1,0-2,0.9-2,2s0.9,2,2,2h6.9L27.6,69.5c0.2,0.9,1,1.5,1.9,1.5h45.5
                   c1.1,0,2-0.9,2-2S76.1,67,75,67z"/><circle cx="35" cy="76" r="4"/><circle cx="69" cy="76" r="4"/>
                  <path d="M78.3,28H27.9l2.9,10H72c0.9,0,1.7,0.6,1.9,1.5l5,18c0.1,0.6,0,1.2-0.4,1.7C78.2,59.6,77.6,60,77,60H31c-1.1,0-2,0.9-2,2
                   s0.9,2,2,2h46.8c1.9,0,3.6-0.9,4.8-2.4c1.1-1.5,1.5-3.4,1-5.2l-5.4-19C78,28,78.2,28,78.3,28z"/></g>
              </svg>
            </a>
            <a href="#" class="nav_search-btn">
              <i class="fa fa-search" aria-hidden="true"></i>
            </a>
            <a href="../../View/BackOffice/dashboard.php" class="order_online_btn">
              Administration
            </a>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <!-- ════ END NAVBAR ════ -->

<div class="article-hero">
  <div class="article-hero-icon"><?php echo $icon; ?></div>
  <div class="article-hero-content">
    <h1><?php echo h($article['title']); ?></h1>
    <div class="hero-meta">
      <span><i class="fa fa-calendar-o"></i><?php echo date('d M Y', strtotime($article['created_at'])); ?></span>
      <span><i class="fa fa-clock-o"></i><?php echo $readTime; ?> min de lecture</span>
      <span><i class="fa fa-comments"></i><?php echo count($comments); ?> commentaire(s)</span>
    </div>
  </div>
</div>

<div class="article-layout">


    <article class="article-body" id="articleBody">
      <p class="article-lead"><?php echo h(substr($article['content'], 0, 200)); ?>…</p>
      
      <!-- AI Voice Reader Component (Premium Feature) -->
      <div class="ai-reader-box" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); border: 1px solid #e9ecef; border-radius: 12px; padding: 15px 20px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <button id="btnPlaySpeech" style="width: 45px; height: 45px; border-radius: 50%; background: #2D6A4F; border: none; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(45, 106, 79, 0.3);">
            <i class="fa fa-play" id="playIcon"></i>
        </button>
        <div style="flex: 1;">
            <div style="font-weight: 700; color: #1a1a2e; font-size: 0.95rem; margin-bottom: 2px; display: flex; align-items: center; gap: 8px;">
                Écouter l'article <span style="background: #E76F51; color: white; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">AI Voice</span>
            </div>
            <div style="font-size: 0.8rem; color: #6c757d;" id="speechStatus">Appuyez sur play pour démarrer la lecture audio.</div>
        </div>
        <div style="width: 60px; height: 30px; display: flex; align-items: center; justify-content: space-between;" class="audio-waves" id="audioWaves">
            <!-- Animated bars injected via JS when playing -->
            <span style="width:4px; height:4px; background:#2D6A4F; border-radius:2px; display:inline-block;"></span>
            <span style="width:4px; height:4px; background:#2D6A4F; border-radius:2px; display:inline-block;"></span>
            <span style="width:4px; height:4px; background:#2D6A4F; border-radius:2px; display:inline-block;"></span>
            <span style="width:4px; height:4px; background:#2D6A4F; border-radius:2px; display:inline-block;"></span>
            <span style="width:4px; height:4px; background:#2D6A4F; border-radius:2px; display:inline-block;"></span>
        </div>
      </div>

      <div class="article-content" id="mainContentText"><?php echo nl2br(h(substr($article['content'], 200))); ?></div>

      <style>
          @keyframes wave {
              0% { height: 4px; }
              50% { height: 25px; }
              100% { height: 4px; }
          }
          .audio-waves.playing span {
              animation: wave 1s infinite ease-in-out;
          }
          .audio-waves.playing span:nth-child(1) { animation-delay: 0.0s; }
          .audio-waves.playing span:nth-child(2) { animation-delay: 0.2s; }
          .audio-waves.playing span:nth-child(3) { animation-delay: 0.4s; }
          .audio-waves.playing span:nth-child(4) { animation-delay: 0.6s; }
          .audio-waves.playing span:nth-child(5) { animation-delay: 0.8s; }
      </style>

      <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnPlay = document.getElementById('btnPlaySpeech');
            const playIcon = document.getElementById('playIcon');
            const statusText = document.getElementById('speechStatus');
            const waves = document.getElementById('audioWaves');
            
            // On récupère le texte pur à lire (sans les balises HTML)
            const articleText = <?php echo json_encode(html_entity_decode(strip_tags($article['title'] . ". " . $article['content']))); ?>;
            
            let synth = window.speechSynthesis;
            let utterance = new SpeechSynthesisUtterance(articleText);
            utterance.lang = 'fr-FR'; // Voix française
            utterance.rate = 1.0;     // Vitesse normale
            utterance.pitch = 1.0;    // Tonalité
            
            let isPlaying = false;

            // Charger les voix dispo
            function loadVoices() {
                let voices = synth.getVoices();
                let frVoice = voices.find(v => v.lang.includes('fr') && (v.name.includes('Google') || v.name.includes('Premium') || v.name.includes('Natural')));
                if(frVoice) utterance.voice = frVoice; // Préférer une voix premium si dispo
            }
            
            if (synth.onvoiceschanged !== undefined) {
                synth.onvoiceschanged = loadVoices;
            }
            loadVoices();

            btnPlay.addEventListener('click', function() {
                if (synth.paused) {
                    synth.resume();
                    setPlayingState();
                } else if (synth.speaking && isPlaying) {
                    synth.pause();
                    setPausedState();
                } else {
                    // Démarrer de zéro
                    synth.cancel(); // Arrêter toute lecture précédente
                    synth.speak(utterance);
                    setPlayingState();
                }
            });

            utterance.onend = function() {
                setPausedState();
                statusText.innerText = 'Lecture terminée.';
            };
            
            utterance.onerror = function(e) {
                console.error("SpeechSynthesis Error:", e);
                setPausedState();
                statusText.innerText = 'Erreur lors de la lecture.';
            };

            function setPlayingState() {
                isPlaying = true;
                playIcon.className = 'fa fa-pause';
                btnPlay.style.background = '#E76F51';
                statusText.innerText = 'Lecture en cours...';
                waves.classList.add('playing');
            }

            function setPausedState() {
                isPlaying = false;
                playIcon.className = 'fa fa-play';
                btnPlay.style.background = '#2D6A4F';
                statusText.innerText = 'Lecture en pause.';
                waves.classList.remove('playing');
            }
            
            // Couper la voix si l'utilisateur quitte la page
            window.addEventListener('beforeunload', function() {
                synth.cancel();
            });
        });
      </script>

    <!-- SCRIPTS POUR EXPORT PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    function generatePDF() {
        // 1. On récupère les données proprement
        const title = "<?php echo addslashes(h($article['title'])); ?>";
        const date = "<?php echo date('d/m/Y', strtotime($article['created_at'])); ?>";
        const content = document.querySelector('.article-content').innerHTML;
        const lead = document.querySelector('.article-lead').innerText;

        // 2. On crée un template HTML propre pour le PDF
        const pdfTemplate = `
            <div style="padding:40px; font-family:serif;">
                <h1 style="color:#2D6A4F; border-bottom:2px solid #2D6A4F; padding-bottom:10px; font-size:24pt;">${title}</h1>
                <p style="color:#888; font-style:italic; margin-bottom:20px;">Publié le ${date}</p>
                <p style="font-size:14pt; color:#444; font-style:italic; margin-bottom:20px;">${lead}</p>
                <div style="font-size:12pt; line-height:1.6; text-align:justify;">${content}</div>
                <div style="margin-top:50px; border-top:1px solid #ccc; padding-top:10px; font-size:10pt; color:#999; text-align:center;">
                    © SmartFood - Document généré le ${new Date().toLocaleDateString()}
                </div>
            </div>
        `;

        // 3. On génère le PDF à partir de ce template virtuel
        const opt = {
            margin:       [10, 10],
            filename:     'SmartFood_' + title.substring(0, 30) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(pdfTemplate).save();
    }
    </script>

      <?php
        $protocol     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $articleUrl   = urlencode($protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $articleTitle = urlencode($article['title']);
        $whatsappText = urlencode('Découvrez cet article SmartFood : ' . $article['title'] . ' → ' . urldecode($articleUrl));
        $twitterUrl   = 'https://twitter.com/intent/tweet?url=' . $articleUrl . '&text=' . $articleTitle . '&via=SmartFood';
        $facebookUrl  = 'https://www.facebook.com/sharer/sharer.php?u=' . $articleUrl;
        $whatsappUrl  = 'https://wa.me/?text=' . $whatsappText;
      ?>

      <style>
        /* ── Premium Share Section ── */
        .share-section {
          margin-top: 32px;
          padding: 24px 28px;
          background: linear-gradient(135deg, #f8fffe 0%, #f0f9f5 100%);
          border-radius: 20px;
          border: 1px solid rgba(45,106,79,.12);
          box-shadow: 0 4px 20px rgba(45,106,79,.07);
        }
        .share-section-header {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-bottom: 18px;
        }
        .share-section-header .share-icon-circle {
          width: 38px; height: 38px;
          background: linear-gradient(135deg, #2D6A4F, #40916C);
          border-radius: 50%;
          display: flex; align-items: center; justify-content: center;
          color: #fff; font-size: 15px;
          box-shadow: 0 4px 12px rgba(45,106,79,.35);
        }
        .share-section-header span {
          font-weight: 700; font-size: 1rem; color: #1a3d2b;
          letter-spacing: .01em;
        }
        .share-section-header small {
          color: #888; font-size: .8rem; margin-left: 4px; font-weight: 400;
        }

        .share-buttons-row {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          align-items: center;
        }

        /* ── Generic Button Base ── */
        .sp-btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 9px 20px;
          border-radius: 50px;
          font-size: .875rem;
          font-weight: 600;
          border: none;
          cursor: pointer;
          text-decoration: none;
          color: #fff;
          letter-spacing: .02em;
          position: relative;
          overflow: hidden;
          transition: transform .25s cubic-bezier(.34,1.56,.64,1),
                      box-shadow .25s ease,
                      filter .2s ease;
          -webkit-tap-highlight-color: transparent;
        }
        .sp-btn i { font-size: 1rem; flex-shrink: 0; }

        /* Ripple */
        .sp-btn::after {
          content: '';
          position: absolute;
          inset: 0;
          border-radius: inherit;
          background: rgba(255,255,255,.18);
          opacity: 0;
          transition: opacity .2s;
        }
        .sp-btn:hover::after { opacity: 1; }
        .sp-btn:active { transform: scale(.96) !important; }

        /* Hover lift + glow */
        .sp-btn:hover {
          transform: translateY(-3px) scale(1.04);
          filter: brightness(1.08);
        }

        /* Twitter / X */
        .sp-btn.tw {
          background: linear-gradient(135deg, #1a91da, #0d77b5);
          box-shadow: 0 4px 14px rgba(26,145,218,.35);
        }
        .sp-btn.tw:hover { box-shadow: 0 8px 22px rgba(26,145,218,.5); }

        /* Facebook */
        .sp-btn.fb {
          background: linear-gradient(135deg, #1877f2, #0d5ecf);
          box-shadow: 0 4px 14px rgba(24,119,242,.35);
        }
        .sp-btn.fb:hover { box-shadow: 0 8px 22px rgba(24,119,242,.5); }

        /* WhatsApp */
        .sp-btn.wa {
          background: linear-gradient(135deg, #25D366, #128C7E);
          box-shadow: 0 4px 14px rgba(37,211,102,.35);
        }
        .sp-btn.wa:hover { box-shadow: 0 8px 22px rgba(37,211,102,.5); }

        /* Instagram */
        .sp-btn.ig {
          background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
          box-shadow: 0 4px 14px rgba(220,39,67,.35);
        }
        .sp-btn.ig:hover { box-shadow: 0 8px 22px rgba(220,39,67,.5); }

        /* Copy Link */
        .sp-btn.cp {
          background: linear-gradient(135deg, #52525b, #3f3f46);
          box-shadow: 0 4px 14px rgba(82,82,91,.3);
        }
        .sp-btn.cp:hover { box-shadow: 0 8px 22px rgba(82,82,91,.45); }

        /* PDF — pushed right */
        .sp-btn.pdf {
          background: linear-gradient(135deg, #e63946, #c1121f);
          box-shadow: 0 4px 14px rgba(230,57,70,.35);
          margin-left: auto;
        }
        .sp-btn.pdf:hover { box-shadow: 0 8px 22px rgba(230,57,70,.5); }

        /* Divider */
        .share-divider {
          width: 1px; height: 28px;
          background: rgba(45,106,79,.15);
          border-radius: 2px;
          flex-shrink: 0;
        }
      </style>

      <div class="share-section">
        <div class="share-section-header">
          <div class="share-icon-circle"><i class="fa fa-share-alt"></i></div>
          <span>Partager cet article <small>— Faites passer le mot !</small></span>
        </div>

        <div class="share-buttons-row">

          <!-- Twitter / X -->
          <a href="<?php echo $twitterUrl; ?>" target="_blank" rel="noopener noreferrer"
             class="sp-btn tw" title="Partager sur Twitter / X">
            <i class="fa fa-twitter"></i> Twitter
          </a>

          <!-- Facebook -->
          <a href="<?php echo $facebookUrl; ?>" target="_blank" rel="noopener noreferrer"
             class="sp-btn fb" title="Partager sur Facebook">
            <i class="fa fa-facebook"></i> Facebook
          </a>

          <!-- WhatsApp -->
          <a href="<?php echo $whatsappUrl; ?>" target="_blank" rel="noopener noreferrer"
             class="sp-btn wa" title="Partager sur WhatsApp">
            <i class="fa fa-whatsapp"></i> WhatsApp
          </a>

          <!-- Instagram -->
          <button onclick="shareInstagram()" class="sp-btn ig" title="Partager sur Instagram">
            <i class="fa fa-instagram"></i> Instagram
          </button>

          <!-- Divider -->
          <div class="share-divider"></div>

          <!-- Copy Link -->
          <button onclick="copyArticleLink()" class="sp-btn cp" id="copyLinkBtn" title="Copier le lien">
            <i class="fa fa-link" id="copyIcon"></i>
            <span id="copyLinkText">Copier le lien</span>
          </button>

          <!-- PDF -->
          <button onclick="generatePDF()" class="sp-btn pdf" title="Télécharger en PDF">
            <i class="fa fa-file-pdf-o"></i> PDF
          </button>

        </div>
      </div>

      <script>
      function copyArticleLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
          const btn  = document.getElementById('copyLinkBtn');
          const text = document.getElementById('copyLinkText');
          const icon = document.getElementById('copyIcon');
          text.textContent = 'Copié !';
          icon.className = 'fa fa-check';
          btn.style.background = 'linear-gradient(135deg,#16a34a,#15803d)';
          setTimeout(() => {
            text.textContent = 'Copier le lien';
            icon.className = 'fa fa-link';
            btn.style.background = '';
          }, 2500);
        });
      }

      function shareInstagram() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
          alert('✅ Lien copié dans le presse-papiers !\n\nOuvrez Instagram, créez une Story ou un Post, puis collez le lien.');
          window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
        }).catch(() => {
          window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
        });
      }
      </script>
    </article>

    <!-- COMMENTAIRES -->
    <div class="comments-section">
      <h2 class="section-heading">
        <i class="fa fa-comments" style="color:var(--green);"></i>
        Commentaires (<?php echo count($comments); ?>)
      </h2>

      <?php if (empty($comments)): ?>
        <div class="no-comments">💬 Soyez le premier à commenter cet article !</div>
      <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-card">
          <div class="comment-avatar"><?php echo mb_substr($comment['author'], 0, 2); ?></div>
          <div class="comment-bubble">
            <span class="comment-author"><?php echo h($comment['author']); ?></span>
            <span class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
            <p class="comment-text"><?php echo nl2br(h($comment['content'])); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- FORMULAIRE -->
    <div class="comment-form-wrap">
      <div class="form-title">✍️ Laisser un commentaire</div>

      <?php if ($success): ?>
        <div class="alert-success">✅ <?php echo h($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors['general'])): ?>
        <div class="alert-danger">❌ <?php echo h($errors['general']); ?></div>
      <?php endif; ?>

      <form method="POST" id="commentForm" novalidate>
        <input type="hidden" name="add_comment" value="1"/>
        <div class="form-group">
          <label for="author">Votre nom *</label>
          <input type="text" id="author" name="author" class="form-control"
                 placeholder="Marie Dupont"
                 value="<?php echo h($formData['author']); ?>"/>
          <div class="error-msg" id="err-author"></div>
        </div>
        <div class="form-group">
          <label for="content">Votre commentaire *</label>
          <textarea id="content" name="content" class="form-control" rows="4"
                    placeholder="Partagez votre avis…"
                    maxlength="1000"><?php echo h($formData['content']); ?></textarea>
          <div class="char-counter" id="charCount">0 / 1000</div>
          <div class="error-msg" id="err-content"></div>
        </div>
        <button type="button" class="btn-submit" onclick="validateAndSubmit()">
          <i class="fa fa-paper-plane"></i> Publier
        </button>
      </form>
    </div>
  </main>

  <aside class="sidebar">

    <?php if (!empty($aiRecommendations)): ?>
    <div class="sidebar-card">
      <div class="sidebar-title">🔥 Articles recommandés</div>
      <?php foreach ($aiRecommendations as $rel): ?>
      <?php $relIcon = $icons[$rel['id'] % count($icons)]; ?>
      <a href="article.php?id=<?php echo $rel['id']; ?>" class="related-card" style="flex-direction: column; align-items: stretch;" title="Score IA: <?php echo $rel['ai_score']; ?>">
        <div style="display:flex; gap:12px; width: 100%;">
          <div class="related-icon"><?php echo $relIcon; ?></div>
          <div style="width: 100%;">
            <div class="related-title"><?php echo h(mb_substr($rel['title'], 0, 50)); ?>…</div>
            <div class="related-date" style="display:flex; justify-content:space-between;">
              <span><?php echo date('d/m/Y', strtotime($rel['created_at'])); ?></span>
              <span style="color:var(--orange);font-weight:600;"><i class="fa fa-star"></i> <?php echo number_format($rel['ai_score'], 1); ?></span>
            </div>
          </div>
        </div>
        <?php if (!empty($rel['ai_reasons'])): ?>
        <div style="margin-top: 10px; font-size: 0.75rem; color: #6c757d; background: #f8f9fa; padding: 8px; border-radius: 6px; border-left: 2px solid var(--orange);">
          <div style="font-weight:bold; margin-bottom:4px;">💡 Pourquoi cet article ?</div>
          <ul style="margin:0; padding-left:14px; line-height:1.4;">
            <?php foreach($rel['ai_reasons'] as $reason): ?>
              <li><?php echo $reason; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="sidebar-card" style="text-align:center;">
      <div style="font-size:2.5rem;margin-bottom:12px;">🌿</div>
      <div style="font-family:var(--ff-head);font-size:1.05rem;margin-bottom:6px;">Équipe SmartFood</div>
      <p style="font-size:.8rem;color:var(--muted);line-height:1.6;">Nutritionnistes passionnés, nous partageons des conseils fondés sur la science pour mieux manger au quotidien.</p>
    </div>

    <div class="sidebar-card" style="background:linear-gradient(135deg,#fff8f0,#fff3e0);border-left:4px solid var(--orange);">
      <p style="font-family:var(--ff-head);font-size:1rem;font-style:italic;color:var(--dark);line-height:1.6;">"Que ton aliment soit ta première médecine."</p>
      <p style="font-size:.78rem;color:var(--muted);margin-top:8px;">— Hippocrate</p>
    </div>

  </aside>
</div>

<section class="footer_section footer_bg">
  <div class="container">
    <div class="row">
      <div class="col-md-4 footer-col">
        <h4>SmartFood</h4>
        <p>Votre blog dédié à la nutrition saine et au bien-être.</p>
      </div>
      <div class="col-md-4 footer-col">
        <h4>Navigation</h4>
        <ul style="list-style:none;padding:0;">
          <li><a href="index.php" style="color:rgba(255,255,255,.7);">Accueil</a></li>
          <li><a href="index.php" style="color:rgba(255,255,255,.7);">Blog</a></li>
          <li><a href="../../View/BackOffice/dashboard.php" style="color:rgba(255,255,255,.7);">Administration</a></li>
        </ul>
      </div>
      <div class="col-md-4 footer-col">
        <h4>Contact</h4>
        <p style="color:rgba(255,255,255,.7);"><i class="fa fa-envelope me-2"></i> contact@smartfood.tn</p>
      </div>
    </div>
    <div class="footer-info">
      <p>&copy; <?php echo date('Y'); ?> SmartFood — Tous droits réservés.</p>
    </div>
  </div>
</section>

<script>
window.addEventListener('scroll', function() {
  var body = document.getElementById('articleBody');
  if (!body) return;
  var pct = Math.min(100, Math.max(0, (-body.getBoundingClientRect().top / body.offsetHeight) * 100));
  document.getElementById('progressBar').style.width = pct + '%';
});

var contentEl = document.getElementById('content');
var charEl    = document.getElementById('charCount');

contentEl.addEventListener('input', function() {
  var len = this.value.length;
  charEl.textContent = len + ' / 1000';
  charEl.className = 'char-counter' + (len > 900 ? ' danger' : len > 700 ? ' warn' : '');
  if (len >= 5) clearErr('content');
});
document.getElementById('author').addEventListener('input', function() {
  if (this.value.trim().length >= 2) clearErr('author');
});

function showErr(id, msg) {
  var el = document.getElementById(id);
  var err = document.getElementById('err-' + id);
  el.classList.add('is-invalid');
  err.textContent = msg; err.classList.add('show');
}
function clearErr(id) {
  document.getElementById(id).classList.remove('is-invalid');
  document.getElementById('err-' + id).classList.remove('show');
}
function validateAndSubmit() {
  var ok = true;
  var author  = document.getElementById('author').value.trim();
  var content = document.getElementById('content').value.trim();
  if (author === '')         { showErr('author',  'Le nom est obligatoire.');           ok = false; }
  else if (author.length<2)  { showErr('author',  'Minimum 2 caractères.');             ok = false; }
  else if (author.length>100){ showErr('author',  'Maximum 100 caractères.');           ok = false; }
  else clearErr('author');
  if (content === '')          { showErr('content', 'Le commentaire est obligatoire.');   ok = false; }
  else if (content.length<5)   { showErr('content', 'Minimum 5 caractères.');            ok = false; }
  else if (content.length>1000){ showErr('content', 'Maximum 1000 caractères.');         ok = false; }
  else clearErr('content');
  if (ok) document.getElementById('commentForm').submit();
  else document.querySelector('.is-invalid')?.scrollIntoView({behavior:'smooth',block:'center'});
}
window.addEventListener('load', function() {
  var len = contentEl.value.length;
  if (len) charEl.textContent = len + ' / 1000';
});
</script>
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>
</body>
</html>

