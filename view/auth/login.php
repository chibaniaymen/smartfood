<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFood Admin - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            display: flex;
            height: 100vh;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* LEFT PANEL */
        .left-panel {
            width: 55%;
            background-color: #1a2340;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 10%;
            position: relative;
            overflow: hidden;
        }

        .logo-text {
            position: absolute;
            top: 40px;
            left: 40px;
            color: #f5a623;
            font-size: 24px;
            font-weight: 700;
        }

        .left-panel h1 {
            font-weight: 300;
            font-size: 3rem;
            margin-bottom: 0;
            z-index: 2;
        }

        .left-panel h1 strong {
            font-weight: 700;
            color: #f5a623;
            font-size: 3.5rem;
            display: block;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            margin-top: 15px;
            margin-bottom: 40px;
            z-index: 2;
        }

        .features {
            list-style: none;
            padding: 0;
            z-index: 2;
        }

        .features li {
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .features li::before {
            content: '✓';
            color: #f5a623;
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .bg-icon {
            position: absolute;
            right: -10%;
            bottom: -10%;
            font-size: 30rem;
            opacity: 0.04;
            pointer-events: none;
            z-index: 1;
        }

        .footer-credit {
            position: absolute;
            bottom: 30px;
            left: 40px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
        }

        /* RIGHT PANEL */
        .right-panel {
            width: 45%;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 8%;
            animation: fadeIn 1s ease-out 0.2s both;
        }

        .avatar {
            width: 72px;
            height: 72px;
            background-color: #1a2340;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: white;
            font-size: 32px;
        }

        .right-panel h2 {
            color: #1a2340;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .right-panel p {
            color: #6c757d;
            margin-bottom: 40px;
        }

        .login-form {
            width: 100%;
            max-width: 360px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 40px 15px 45px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #1a2340;
            box-shadow: 0 0 0 3px rgba(26, 35, 64, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.2rem;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .error-box {
            background-color: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: #1a2340;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #f5a623;
            color: #1a2340;
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #1a2340;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 0 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="logo-text">SmartFood</div>
            <h1>Bienvenue sur<br><strong>SmartFood Admin</strong></h1>
            <p class="subtitle">Plateforme intelligente de gestion d'événements</p>
            
            <ul class="features">
                <li>Gestion complète des événements</li>
                <li>Optimisation intelligente par IA</li>
                <li>Tableau de bord analytique en temps réel</li>
            </ul>

            <div class="bg-icon">⭐</div>
            <div class="footer-credit">SmartFood © 2026</div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="avatar">👤</div>
            <h2>Connexion</h2>
            <p>Accédez à votre espace administrateur</p>

            <form class="login-form" action="login.php" method="POST">
                
                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <span class="input-icon">✉️</span>
                    <input type="text" name="username" placeholder="Nom d'utilisateur">
                </div>

                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" id="password" placeholder="Mot de passe">
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                </div>

                <button type="submit" class="submit-btn">Se connecter →</button>
            </form>

            <a href="myEvents.php" class="back-link">← Retour au site</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>
</html>
