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

function getDb(): PDO
{
    require_once __DIR__ . '/smartfood/config/Database.php';
    return Database::getInstance();
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orderId <= 0) {
    http_response_code(400);
    echo 'Facture introuvable.';
    exit;
}

$db = getDb();
$orderStmt = $db->prepare('SELECT * FROM orders WHERE id = :id');
$orderStmt->execute([':id' => $orderId]);
$order = $orderStmt->fetch();
if (!$order) {
    http_response_code(404);
    echo 'Facture introuvable.';
    exit;
}

$itemsStmt = $db->prepare('SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC');
$itemsStmt->execute([':order_id' => $orderId]);
$items = $itemsStmt->fetchAll();

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Facture #<?php echo (int) $order['id']; ?></title>
  <style>
    body { font-family: Arial, sans-serif; color: #1f2937; background: #f9fafb; margin: 0; padding: 24px; }
    .invoice { max-width: 860px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .brand { font-size: 24px; font-weight: 700; color: #2d6a4f; }
    .meta { text-align: right; font-size: 14px; color: #6b7280; }
    .section { margin-bottom: 18px; }
    .section h3 { font-size: 16px; margin: 0 0 8px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 14px; }
    th { background: #f3f4f6; }
    .totals { margin-top: 16px; display: flex; justify-content: flex-end; }
    .totals table { width: 260px; }
    .totals td { text-align: right; }
    .totals td:first-child { text-align: left; }
    .print { margin-top: 18px; text-align: right; }
    .print button { background: #2d6a4f; color: #fff; border: none; padding: 10px 18px; border-radius: 999px; cursor: pointer; }
    @media print { .print { display: none; } body { background: #fff; padding: 0; } .invoice { box-shadow: none; border-radius: 0; } }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="header">
      <div class="brand">SmartFood</div>
      <div class="meta">
        Facture #<?php echo (int) $order['id']; ?><br />
        <?php echo escapeHtml($order['created_at']); ?>
      </div>
    </div>

    <div class="section">
      <h3>Client</h3>
      <div><?php echo escapeHtml($order['customer_name']); ?></div>
      <?php if (!empty($order['customer_phone'])): ?>
        <div><?php echo escapeHtml($order['customer_phone']); ?></div>
      <?php endif; ?>
      <?php if (!empty($order['customer_email'])): ?>
        <div><?php echo escapeHtml($order['customer_email']); ?></div>
      <?php endif; ?>
      <?php if (!empty($order['delivery_address'])): ?>
        <div><?php echo escapeHtml($order['delivery_address']); ?></div>
      <?php endif; ?>
    </div>

    <div class="section">
      <h3>Articles</h3>
      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th>Prix</th>
            <th>Quantite</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?php echo escapeHtml($item['product_name']); ?></td>
              <td><?php echo number_format((float) $item['unit_price'], 2); ?> DT</td>
              <td><?php echo (int) $item['quantity']; ?></td>
              <td><?php echo number_format((float) $item['line_total'], 2); ?> DT</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="totals">
      <table>
        <tr>
          <td>Sous-total</td>
          <td><?php echo number_format((float) $order['subtotal'], 2); ?> DT</td>
        </tr>
        <tr>
          <td>Taxe</td>
          <td><?php echo number_format((float) $order['tax'], 2); ?> DT</td>
        </tr>
        <tr>
          <td><strong>Total</strong></td>
          <td><strong><?php echo number_format((float) $order['total'], 2); ?> DT</strong></td>
        </tr>
      </table>
    </div>

    <div class="print">
      <button onclick="window.print()">Imprimer / Exporter PDF</button>
    </div>
  </div>
</body>
</html>
