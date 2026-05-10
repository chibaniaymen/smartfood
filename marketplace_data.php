<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

/* ================================================
   SMARTFOOD - marketplace_data.php
   Backend pur : connexion DB + données JSON
   Inclus par marketplace.php via fetch() ou
   directement inclus en haut de marketplace.php
   ================================================ */

function getDb(): PDO
{
    require_once __DIR__ . '/smartfood/config/Database.php';
    return Database::getInstance();
}

$db = getDb();

// ---- Récupérer tous les restaurants ----
$sqlRestaurants = "SELECT * FROM restaurants ORDER BY rating DESC";
$restaurants = $db->query($sqlRestaurants)->fetchAll();

// ---- Récupérer tous les produits avec infos restaurant ----
$sqlProducts = "
    SELECT p.*, r.slug AS restaurant_slug, r.name AS restaurant_name
    FROM products p
    JOIN restaurants r ON p.restaurant_id = r.id
    ORDER BY p.name ASC
";
$products = $db->query($sqlProducts)->fetchAll();

$orders = [];
try {
    $ordersSql = 'SELECT id, customer_name, customer_phone, total, status, created_at FROM orders ORDER BY created_at DESC';
    $orders = $db->query($ordersSql)->fetchAll();
} catch (Throwable $e) {
    $orders = [];
}

if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json; charset=utf-8');

    if (isset($_GET['restaurant_id'])) {
        $restaurantId = (int) $_GET['restaurant_id'];
        $db = getDb();

        $restaurantSql = 'SELECT * FROM restaurants WHERE id = :id';
        $restaurantStmt = $db->prepare($restaurantSql);
        $restaurantStmt->execute([':id' => $restaurantId]);
        $restaurantResult = $restaurantStmt->fetch();

        $products = [];
        if ($restaurantResult) {
            $productsSql = 'SELECT * FROM products WHERE restaurant_id = :restaurant_id ORDER BY name ASC';
            $productsStmt = $db->prepare($productsSql);
            $productsStmt->execute([':restaurant_id' => $restaurantId]);
            $products = $productsStmt->fetchAll();
        }

        echo json_encode([
            'success' => true,
            'restaurant' => $restaurantResult,
            'products' => $products
        ]);
        exit;
    }

    if (isset($_GET['product_id'])) {
        $productId = (int) $_GET['product_id'];
        $db = getDb();

        $productSql = 'SELECT p.*, r.slug AS restaurant_slug, r.name AS restaurant_name, r.id AS restaurant_id, r.cuisine, r.emoji AS restaurant_emoji, r.description AS restaurant_description, r.address, r.delivery_time, r.min_order, r.badge AS restaurant_badge, r.cover_color FROM products p JOIN restaurants r ON p.restaurant_id = r.id WHERE p.id = :id';
        $productStmt = $db->prepare($productSql);
        $productStmt->execute([':id' => $productId]);
        $row = $productStmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Produit introuvable.']);
            exit;
        }

        $restaurant = [
            'id' => $row['restaurant_id'],
            'name' => $row['restaurant_name'],
            'slug' => $row['restaurant_slug'],
            'cuisine' => $row['cuisine'],
            'emoji' => $row['restaurant_emoji'],
            'description' => $row['restaurant_description'],
            'address' => $row['address'],
            'delivery_time' => $row['delivery_time'],
            'min_order' => $row['min_order'],
            'badge' => $row['restaurant_badge'],
            'cover_color' => $row['cover_color']
        ];

        $product = [
            'id' => $row['id'],
            'restaurant_id' => $row['restaurant_id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'price' => $row['price'],
            'unit' => $row['unit'],
            'description' => $row['description'],
            'badge' => $row['badge'],
            'emoji' => $row['emoji'],
            'rating' => $row['rating'],
            'votes' => $row['votes']
        ];

        echo json_encode([
            'success' => true,
            'restaurant' => $restaurant,
            'products' => [$product]
        ]);
        exit;
    }

    echo json_encode([
        'restaurants' => $restaurants,
        'products' => $products,
        'orders' => $orders
    ]);
    exit;
}