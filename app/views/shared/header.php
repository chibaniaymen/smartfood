<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SmartFood MVC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; color:#222; margin:0; padding:0; }
        .sf-container { max-width: 1080px; margin: 0 auto; padding: 24px; }
        .sf-header { background: #1a3c2e; color:#fff; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; }
        .sf-header a { color:#e2f6e7; margin-left:16px; text-decoration:none; }
        .sf-card { background:#fff; border-radius:18px; box-shadow:0 10px 30px rgba(0,0,0,0.08); padding:24px; margin-bottom:24px; }
        .sf-btn { background:#2d6a4f; color:#fff; padding:12px 20px; border:none; border-radius:999px; cursor:pointer; text-decoration:none; display:inline-block; }
        .sf-btn-alt { background:#fff; color:#2d6a4f; border:1px solid #2d6a4f; }
        .sf-alert { padding:12px 18px; border-radius:12px; margin-bottom:18px; }
        .sf-alert.error { background:#ffe6e6; color:#a4161a; }
        .sf-alert.success { background:#e6ffe6; color:#1b5e20; }
        table { width:100%; border-collapse:collapse; margin-top:18px; }
        th, td { text-align:left; padding:12px; border-bottom:1px solid #e0e0e0; }
        th { background:#f8faf8; }
        label { display:block; margin-bottom:8px; font-weight:600; }
        input, select, textarea { width:100%; padding:10px 12px; margin-bottom:16px; border:1px solid #d1d5db; border-radius:12px; }
    </style>
</head>
<body>
<header class="sf-header">
    <div>
        <strong>SmartFood MVC</strong>
    </div>
    <nav>
        <a href="index.php?action=front_home">Accueil</a>
        <?php if (isLoggedIn()): ?>
            <a href="index.php?action=front_profile">Mon profil</a>
            <a href="index.php?action=front_logout">Déconnexion</a>
        <?php else: ?>
            <a href="index.php?action=front_login">Connexion FO</a>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
            <a href="index.php?action=admin_dashboard">Back office</a>
            <a href="index.php?action=admin_logout">Déconnexion BO</a>
        <?php else: ?>
            <a href="index.php?action=admin_login">Connexion BO</a>
        <?php endif; ?>
    </nav>
</header>
<div class="sf-container">
