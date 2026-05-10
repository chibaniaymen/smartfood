<?php
// Simple Order model (no autoload) for the MVC refactor
require_once __DIR__ . '/../../smartfood/config/Database.php';

class OrderModel
{
    public static function findWithItems(int $id): ?array
    {
        $db = \Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $itemsStmt = $db->prepare('SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC');
        $itemsStmt->execute([':order_id' => $id]);
        $order['items'] = $itemsStmt->fetchAll();
        return $order;
    }

    public static function createFromPayload(array $customer, array $items): int
    {
        $db = \Database::getInstance();
        $db->beginTransaction();
        try {
            $subtotal = 0.0;
            foreach ($items as $item) {
                $qty = isset($item['qty']) && is_numeric($item['qty']) ? (int)$item['qty'] : 0;
                $price = isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : 0.0;
                if ($qty <= 0) continue;
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

            $orderId = (int)$db->lastInsertId();

            $findProductStmt = $db->prepare('SELECT id FROM products WHERE name = :name LIMIT 1');
            $insertItemSql = 'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total) VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity, :line_total)';
            $insertItemStmt = $db->prepare($insertItemSql);

            foreach ($items as $item) {
                $name = trim((string)($item['name'] ?? ''));
                $qty = isset($item['qty']) && is_numeric($item['qty']) ? (int)$item['qty'] : 0;
                $price = isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : 0.0;
                if ($name === '' || $qty <= 0) continue;

                $productId = null;
                $findProductStmt->execute([':name' => $name]);
                $productRow = $findProductStmt->fetch();
                if ($productRow && isset($productRow['id'])) {
                    $productId = (int)$productRow['id'];
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
            return $orderId;
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }
}
