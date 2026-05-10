<?php
require_once __DIR__ . '/../Models/Order.php';

class OrderController
{
    public static function handleCreateOrder()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method.');
            }
            $rawBody = trim(file_get_contents('php://input'));
            if ($rawBody === '') throw new Exception('Request body is empty.');
            $data = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new Exception('Invalid JSON body.');

            $customer = $data['customer'] ?? null;
            $items = $data['items'] ?? null;
            if (!is_array($customer) || empty($customer['name'])) throw new Exception('Missing customer name.');
            if (!is_array($items) || count($items) === 0) throw new Exception('Items array is empty.');

            $orderId = OrderModel::createFromPayload($customer, $items);

            echo json_encode(['success' => true, 'order_id' => $orderId]);
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    public static function handleShowInvoice(int $orderId)
    {
        if ($orderId <= 0) {
            http_response_code(400);
            echo 'Facture introuvable.';
            exit;
        }
        $order = OrderModel::findWithItems($orderId);
        if (!$order) {
            http_response_code(404);
            echo 'Facture introuvable.';
            exit;
        }
        // Render view
        $orderVar = $order; // for clarity in view
        require __DIR__ . '/../Views/invoice.php';
        exit;
    }
}
