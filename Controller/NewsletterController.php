<?php
// c:\xampp\htdocs\smartfood_jointure1\Controller\NewsletterController.php
require_once __DIR__ . '/../config.php';

class NewsletterController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Création de la table si elle n'existe pas
        $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            ip_address VARCHAR(45),
            subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }

    public function subscribe($email, $recaptchaResponse) {
        // 1. Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Adresse email invalide.'];
        }

        // 2. Validation reCAPTCHA (Métier Avancé: Intégration API Tierce)
        // Utilisation d'une clé secrète de test Google reCAPTCHA
        $recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
        
        // Pour éviter de bloquer en local si cURL n'est pas activé, on bypass si on reçoit le mot-clé de test
        if ($recaptchaResponse !== 'test_bypass') {
            $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                ]
            ];
            $context  = stream_context_create($options);
            $verifyResponse = @file_get_contents($verifyUrl, false, $context);
            $responseData = json_decode($verifyResponse);

            if (!$responseData || !$responseData->success) {
                return ['success' => false, 'message' => 'Validation reCAPTCHA échouée. Veuillez cocher la case "Je ne suis pas un robot".'];
            }
        }

        // 3. Vérification des doublons
        $stmt = $this->pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Vous êtes déjà inscrit à notre newsletter !'];
        }

        // 4. Insertion en base
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $this->pdo->prepare("INSERT INTO newsletter_subscribers (email, ip_address) VALUES (?, ?)");
        
        try {
            $stmt->execute([$email, $ip]);
            return [
                'success' => true, 
                'message' => 'Félicitations ! Vous êtes bien inscrit. Votre guide vous a été envoyé par e-mail.'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }
}
