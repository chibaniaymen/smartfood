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

    $customer = $data['customer'] ?? null;
    $items = $data['items'] ?? null;

    if (!is_array($customer) || empty($customer['name'])) {
        throw new Exception('Missing customer name.');
    }
    if (!is_array($items) || count($items) === 0) {
        throw new Exception('Items array is empty.');
    }

    $db = getDb();
    $db->beginTransaction();

    $subtotal = 0.0;
    foreach ($items as $item) {
        $qty = isset($item['qty']) && is_numeric($item['qty']) ? (int) $item['qty'] : 0;
        $price = isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : 0.0;
        if ($qty <= 0) {
            continue;
        }
        $subtotal += $qty * $price;
    }

    if ($subtotal <= 0) {
        throw new Exception('Order total is zero.');
    }

    $tax = 0.0;
    $total = $subtotal + $tax;

    $insertOrderSql = 'INSERT INTO orders (customer_name, customer_phone, customer_email, delivery_address, notes, status, subtotal, tax, total) VALUES (:customer_name, :customer_phone, :customer_email, :delivery_address, :notes, :status, :subtotal, :tax, :total)';
    $insertOrderStmt = $db->prepare($insertOrderSql);
    $insertOrderStmt->execute([
        ':customer_name' => $customer['name'],
        ':customer_phone' => $customer['phone'] ?? null,
        ':customer_email' => $customer['email'] ?? null,
        ':delivery_address' => $customer['address'] ?? null,
        ':notes' => $customer['notes'] ?? null,
        ':status' => 'pending',
        ':subtotal' => $subtotal,
        ':tax' => $tax,
        ':total' => $total,
    ]);

    $orderId = (int) $db->lastInsertId();

    $findProductStmt = $db->prepare('SELECT id FROM products WHERE name = :name LIMIT 1');
    $insertItemSql = 'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total) VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity, :line_total)';
    $insertItemStmt = $db->prepare($insertItemSql);

    foreach ($items as $item) {
        $name = trim((string) ($item['name'] ?? ''));
        $qty = isset($item['qty']) && is_numeric($item['qty']) ? (int) $item['qty'] : 0;
        $price = isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : 0.0;
        if ($name === '' || $qty <= 0) {
            continue;
        }

        $productId = null;
        $findProductStmt->execute([':name' => $name]);
        $productRow = $findProductStmt->fetch();
        if ($productRow && isset($productRow['id'])) {
            $productId = (int) $productRow['id'];
        }
        if ($productId === null) {
            throw new Exception('Product not found for item: ' . $name);
        }

        $lineTotal = $qty * $price;
        $insertItemStmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':product_name' => $name,
            ':unit_price' => $price,
            ':quantity' => $qty,
            ':line_total' => $lineTotal,
        ]);
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $orderId
    ]);
    exit;
} catch (Exception $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
