<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
header('Content-Type: application/json; charset=utf-8');

function getDb(): PDO
{
    require_once __DIR__ . '/smartfood/config/Database.php';
    return Database::getInstance();
}

try {
    $db = null;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $rawBody = trim(file_get_contents('php://input'));
    if ($rawBody === '') {
        throw new Exception('Request body is empty.');
    }

    $data = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON body.');
    }

    $action = $data['action'] ?? 'create_restaurant';
    $db = getDb();

    switch ($action) {
        case 'create_restaurant':
            if (!isset($data['restaurant']) || !is_array($data['restaurant'])) {
                throw new Exception('Missing restaurant object.');
            }
            if (!isset($data['products']) || !is_array($data['products'])) {
                throw new Exception('Missing products array.');
            }

            $restaurant = $data['restaurant'];
            $requiredRestaurantFields = [
                'name',
                'slug',
                'cuisine',
                'emoji',
                'description',
                'address',
                'delivery_time',
                'min_order',
                'badge',
                'cover_color'
            ];

            foreach ($requiredRestaurantFields as $field) {
                if (!isset($restaurant[$field])) {
                    throw new Exception('Missing restaurant field: ' . $field);
                }
            }

            $products = $data['products'];
            if (count($products) === 0) {
                throw new Exception('Products array must contain at least one product.');
            }

            $rating = 0;
            $votes = 0;

            $db->beginTransaction();

            $insertRestaurantSql = "INSERT INTO restaurants (name, slug, cuisine, description, address, rating, votes, delivery_time, min_order, badge, emoji, cover_color) VALUES (:name, :slug, :cuisine, :description, :address, :rating, :votes, :delivery_time, :min_order, :badge, :emoji, :cover_color)";
            $insertRestaurantStmt = $db->prepare($insertRestaurantSql);
            $insertRestaurantStmt->execute([
                ':name' => $restaurant['name'],
                ':slug' => $restaurant['slug'],
                ':cuisine' => $restaurant['cuisine'],
                ':description' => $restaurant['description'],
                ':address' => $restaurant['address'],
                ':rating' => $rating,
                ':votes' => $votes,
                ':delivery_time' => $restaurant['delivery_time'],
                ':min_order' => $restaurant['min_order'],
                ':badge' => $restaurant['badge'],
                ':emoji' => $restaurant['emoji'],
                ':cover_color' => $restaurant['cover_color'],
            ]);

            $restaurantId = (int) $db->lastInsertId();

            $insertProductSql = "INSERT INTO products (restaurant_id, name, category, display_category, price, unit, description, rating, votes, badge, emoji) VALUES (:restaurant_id, :name, :category, :display_category, :price, :unit, :description, :rating, :votes, :badge, :emoji)";
            $insertProductStmt = $db->prepare($insertProductSql);

            $productsInserted = 0;
            foreach ($products as $index => $product) {
                if (!is_array($product)) {
                    throw new Exception('Product at index ' . $index . ' must be an object.');
                }

                $requiredProductFields = ['name', 'emoji', 'category', 'price', 'unit', 'badge', 'rating', 'description'];
                foreach ($requiredProductFields as $field) {
                    if (!isset($product[$field])) {
                        throw new Exception('Missing product field: ' . $field . ' at index ' . $index);
                    }
                }

                $price = is_numeric($product['price']) ? (float) $product['price'] : 0.0;
                $ratingValue = is_numeric($product['rating']) ? (float) $product['rating'] : 0.0;
                $votesValue = 0;

                $insertProductStmt->execute([
                    ':restaurant_id' => $restaurantId,
                    ':name' => $product['name'],
                    ':category' => $product['category'],
                    ':display_category' => $product['category'],
                    ':price' => $price,
                    ':unit' => $product['unit'],
                    ':description' => $product['description'],
                    ':rating' => $ratingValue,
                    ':votes' => $votesValue,
                    ':badge' => $product['badge'],
                    ':emoji' => $product['emoji'],
                ]);
                $productsInserted++;
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'restaurant_id' => $restaurantId,
                'products_inserted' => $productsInserted
            ]);
            exit;

        case 'update_restaurant':
            if (!isset($data['restaurant']) || !is_array($data['restaurant'])) {
                throw new Exception('Missing restaurant object.');
            }
            $restaurant = $data['restaurant'];
            if (empty($restaurant['id'])) {
                throw new Exception('Missing restaurant id.');
            }

            $requiredRestaurantFields = [
                'name',
                'slug',
                'cuisine',
                'emoji',
                'description',
                'address',
                'delivery_time',
                'min_order',
                'badge',
                'cover_color'
            ];

            foreach ($requiredRestaurantFields as $field) {
                if (!isset($restaurant[$field])) {
                    throw new Exception('Missing restaurant field: ' . $field);
                }
            }

            $restaurantId = (int) $restaurant['id'];

            $db->beginTransaction();

            $updateRestaurantSql = "UPDATE restaurants SET name = :name, slug = :slug, cuisine = :cuisine, emoji = :emoji, description = :description, address = :address, delivery_time = :delivery_time, min_order = :min_order, badge = :badge, cover_color = :cover_color WHERE id = :id";
            $updateRestaurantStmt = $db->prepare($updateRestaurantSql);
            $updateRestaurantStmt->execute([
                ':name' => $restaurant['name'],
                ':slug' => $restaurant['slug'],
                ':cuisine' => $restaurant['cuisine'],
                ':emoji' => $restaurant['emoji'],
                ':description' => $restaurant['description'],
                ':address' => $restaurant['address'],
                ':delivery_time' => $restaurant['delivery_time'],
                ':min_order' => $restaurant['min_order'],
                ':badge' => $restaurant['badge'],
                ':cover_color' => $restaurant['cover_color'],
                ':id' => $restaurantId,
            ]);

            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $index => $product) {
                    if (!is_array($product)) {
                        throw new Exception('Product at index ' . $index . ' must be an object.');
                    }

                    $requiredProductFields = ['name', 'emoji', 'category', 'price', 'unit', 'badge', 'rating', 'description'];
                    foreach ($requiredProductFields as $field) {
                        if (!isset($product[$field])) {
                            throw new Exception('Missing product field: ' . $field . ' at index ' . $index);
                        }
                    }

                    $productId = isset($product['id']) ? (int) $product['id'] : null;
                    $price = is_numeric($product['price']) ? (float) $product['price'] : 0.0;
                    $ratingValue = is_numeric($product['rating']) ? (float) $product['rating'] : 0.0;
                    $votesValue = isset($product['votes']) ? (int) $product['votes'] : 0;

                    if ($productId) {
                        $updateProductSql = "UPDATE products SET restaurant_id = :restaurant_id, name = :name, category = :category, display_category = :display_category, price = :price, unit = :unit, description = :description, rating = :rating, votes = :votes, badge = :badge, emoji = :emoji WHERE id = :id";
                        $updateProductStmt = $db->prepare($updateProductSql);
                        $updateProductStmt->execute([
                            ':restaurant_id' => $restaurantId,
                            ':name' => $product['name'],
                            ':category' => $product['category'],
                            ':display_category' => $product['category'],
                            ':price' => $price,
                            ':unit' => $product['unit'],
                            ':description' => $product['description'],
                            ':rating' => $ratingValue,
                            ':votes' => $votesValue,
                            ':badge' => $product['badge'],
                            ':emoji' => $product['emoji'],
                            ':id' => $productId,
                        ]);
                    } else {
                        $insertProductSql = "INSERT INTO products (restaurant_id, name, category, display_category, price, unit, description, rating, votes, badge, emoji) VALUES (:restaurant_id, :name, :category, :display_category, :price, :unit, :description, :rating, :votes, :badge, :emoji)";
                        $insertProductStmt = $db->prepare($insertProductSql);
                        $insertProductStmt->execute([
                            ':restaurant_id' => $restaurantId,
                            ':name' => $product['name'],
                            ':category' => $product['category'],
                            ':display_category' => $product['category'],
                            ':price' => $price,
                            ':unit' => $product['unit'],
                            ':description' => $product['description'],
                            ':rating' => $ratingValue,
                            ':votes' => $votesValue,
                            ':badge' => $product['badge'],
                            ':emoji' => $product['emoji'],
                        ]);
                    }
                }
            }

            $db->commit();

            echo json_encode(['success' => true, 'restaurant_id' => $restaurantId]);
            exit;

        case 'delete_restaurant':
            if (empty($data['restaurant_id'])) {
                throw new Exception('Missing restaurant id.');
            }

            $restaurantId = (int) $data['restaurant_id'];
            $db->beginTransaction();
            $deleteProductsStmt = $db->prepare('DELETE FROM products WHERE restaurant_id = :restaurant_id');
            $deleteProductsStmt->execute([':restaurant_id' => $restaurantId]);
            $deleteRestaurantStmt = $db->prepare('DELETE FROM restaurants WHERE id = :id');
            $deleteRestaurantStmt->execute([':id' => $restaurantId]);
            $db->commit();

            echo json_encode(['success' => true, 'restaurant_id' => $restaurantId]);
            exit;

        case 'update_product':
            if (!isset($data['product']) || !is_array($data['product'])) {
                throw new Exception('Missing product object.');
            }
            $product = $data['product'];
            if (empty($product['id'])) {
                throw new Exception('Missing product id.');
            }

            $requiredProductFields = ['restaurant_id', 'name', 'category', 'price', 'unit', 'description', 'badge', 'emoji'];
            foreach ($requiredProductFields as $field) {
                if (!isset($product[$field])) {
                    throw new Exception('Missing product field: ' . $field);
                }
            }

            $productId = (int) $product['id'];
            $restaurantId = (int) $product['restaurant_id'];
            $price = is_numeric($product['price']) ? (float) $product['price'] : 0.0;

            $updateProductSql = "UPDATE products SET restaurant_id = :restaurant_id, name = :name, category = :category, display_category = :display_category, price = :price, unit = :unit, description = :description, badge = :badge, emoji = :emoji WHERE id = :id";
            $updateProductStmt = $db->prepare($updateProductSql);
            $updateProductStmt->execute([
                ':restaurant_id' => $restaurantId,
                ':name' => $product['name'],
                ':category' => $product['category'],
                ':display_category' => $product['category'],
                ':price' => $price,
                ':unit' => $product['unit'],
                ':description' => $product['description'],
                ':badge' => $product['badge'],
                ':emoji' => $product['emoji'],
                ':id' => $productId,
            ]);

            echo json_encode(['success' => true, 'product_id' => $productId]);
            exit;

        case 'delete_product':
            if (empty($data['product_id'])) {
                throw new Exception('Missing product id.');
            }

            $productId = (int) $data['product_id'];
            $deleteProductStmt = $db->prepare('DELETE FROM products WHERE id = :id');
            $deleteProductStmt->execute([':id' => $productId]);

            echo json_encode(['success' => true, 'product_id' => $productId]);
            exit;

        default:
            throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    if ($db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
