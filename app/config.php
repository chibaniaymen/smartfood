<?php
// SMARTFOOD MVC - Configuration commune
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'smartfood');

define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', APP_ROOT . '/public');

autoLoadClasses();

function autoLoadClasses()
{
    spl_autoload_register(function ($class) {
        $paths = [
            APP_ROOT . '/controllers/' . $class . '.php',
            APP_ROOT . '/models/' . $class . '.php',
        ];

        foreach ($paths as $file) {
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });
}

function redirect($action)
{
    $location = dirname($_SERVER['PHP_SELF']) . '/index.php?action=' . urlencode($action);
    header('Location: ' . $location);
    exit;
}

function isLoggedIn()
{
    return !empty($_SESSION['user']);
}

function isAdmin()
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function ensureLoggedIn()
{
    if (!isLoggedIn()) {
        redirect('front_login');
    }
}

function ensureAdmin()
{
    if (!isAdmin()) {
        redirect('admin_login');
    }
}
