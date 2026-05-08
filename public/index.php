<?php
require_once __DIR__ . '/../app/config.php';
require_once APP_ROOT . '/app/controllers/AuthController.php';
require_once APP_ROOT . '/app/controllers/UserController.php';
require_once APP_ROOT . '/app/controllers/ProductController.php';

$action = $_GET['action'] ?? 'front_home';

switch ($action) {
    case 'front_home':
        $products = Product::getAll();
        require APP_ROOT . '/app/views/front/home.php';
        break;
    case 'front_login':
        (new AuthController())->frontLogin();
        break;
    case 'front_logout':
        (new AuthController())->logout();
        break;
    case 'front_profile':
        (new UserController())->profile();
        break;
    case 'admin_login':
        (new AuthController())->adminLogin();
        break;
    case 'admin_logout':
        (new AuthController())->adminLogout();
        break;
    case 'admin_dashboard':
        ensureAdmin();
        require APP_ROOT . '/app/views/admin/dashboard.php';
        break;
    case 'admin_users':
        (new UserController())->adminList();
        break;
    case 'admin_user_form':
        (new UserController())->adminForm();
        break;
    case 'admin_user_delete':
        (new UserController())->adminDelete();
        break;
    case 'admin_products':
        (new ProductController())->adminList();
        break;
    case 'admin_product_form':
        (new ProductController())->adminForm();
        break;
    case 'admin_product_delete':
        (new ProductController())->adminDelete();
        break;
    default:
        http_response_code(404);
        echo '<h1>404 - Page introuvable</h1>';
        break;
}
