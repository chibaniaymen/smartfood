<?php
class UserController
{
    public function profile()
    {
        ensureLoggedIn();
        $user = User::current();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'role' => $user['role'],
            ];
            User::update($user['id'], $data);
            $_SESSION['user'] = User::getById($user['id']);
            $message = 'Profil mis à jour avec succès.';
        }
        require APP_ROOT . '/app/views/front/profile.php';
    }

    public function adminList()
    {
        ensureAdmin();
        $users = User::getAll();
        require APP_ROOT . '/app/views/admin/users.php';
    }

    public function adminForm()
    {
        ensureAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $user = $id ? User::getById($id) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'role' => trim($_POST['role'] ?? 'user'),
            ];

            if ($id) {
                User::update($id, $data);
            } else {
                if (empty($data['password'])) {
                    $error = 'Le mot de passe est obligatoire pour créer un utilisateur.';
                } else {
                    User::create($data);
                }
            }
            if (empty($error)) {
                redirect('admin_users');
            }
        }

        require APP_ROOT . '/app/views/admin/user_form.php';
    }

    public function adminDelete()
    {
        ensureAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            User::delete($id);
        }
        redirect('admin_users');
    }
}
