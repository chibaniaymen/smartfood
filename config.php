<?php
define('OLLAMA_URL',   'http://localhost:11434/api/generate');
define('OLLAMA_MODEL', 'mistral');

class Config {
    private static $pdo = null;

    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=feane_events',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Erreur DB: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function callAI(string $prompt): string {
        $payload = json_encode([
            'model'  => OLLAMA_MODEL,
            'prompt' => $prompt,
            'stream' => false
        ]);

        $ch = curl_init(OLLAMA_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 60,
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return 'Ollama non disponible. Assurez-vous que le terminal est ouvert.';
        }

        $data = json_decode($response, true);
        return $data['response'] ?? 'Réponse vide.';
    }
}
?>
