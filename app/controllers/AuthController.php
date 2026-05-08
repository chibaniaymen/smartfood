<?php
class AuthController
{
    public function frontLogin()
    {
        if (isLoggedIn()) {
            redirect('front_home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = User::authenticate($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($user && $user['role'] === 'user') {
                $_SESSION['user'] = $user;
                redirect('front_home');
            }
            $error = 'Identifiants incorrects ou non autorisés pour le front office.';
        }

        require APP_ROOT . '/app/views/front/login.php';
    }

    public function adminLogin()
    {
        if (isAdmin()) {
            redirect('admin_dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = User::authenticate($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($user && $user['role'] === 'admin') {
                $_SESSION['user'] = $user;
                redirect('admin_dashboard');
            }
            $error = 'Identifiants incorrects ou non autorisés pour l’administration.';
        }

        require APP_ROOT . '/app/views/admin/login.php';
    }

    public function logout()
    {
        session_destroy();
        session_start();
        redirect('front_login');
    }

    public function adminLogout()
    {
        session_destroy();
        session_start();
        redirect('admin_login');
    }
}
