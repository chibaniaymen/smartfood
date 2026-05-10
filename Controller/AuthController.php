<?php
class AuthController {

    private const USERNAME = 'admin';
    private const PASSWORD = 'aadmin';

    public function login(array $post): array {
        $errors = [];
        $username = trim($post['username'] ?? '');
        $password = trim($post['password'] ?? '');

        // ALL validation is PHP only — zero HTML5 attributes used
        if (empty($username)) $errors[] = "Le nom d'utilisateur est obligatoire.";
        if (empty($password)) $errors[] = "Le mot de passe est obligatoire.";
        if (!empty($errors))  return ['errors' => $errors];

        if ($username !== self::USERNAME || $password !== self::PASSWORD) {
            return ['errors' => ["Identifiants incorrects. Veuillez réessayer."]];
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $username;
        $_SESSION['login_time']      = date('Y-m-d H:i:s');

        return ['success' => true];
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['admin_logged_in']);
    }
}
?>
