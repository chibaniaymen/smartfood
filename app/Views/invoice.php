<?php
// Expects $orderVar variable (array) provided by controller
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$order = $orderVar;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Facture #<?php echo (int)$order['id']; ?></title>
  <style>
    body { font-family: Arial, sans-serif; color: #1f2937; background: #f9fafb; margin: 0; padding: 24px; }
    .invoice { max-width: 860px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .brand { font-size: 24px; font-weight: 700; color: #2d6a4f; }
    .meta { text-align: right; font-size: 14px; color: #6b7280; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 14px; }
    th { background: #f3f4f6; }
    .totals { margin-top: 16px; display: flex; justify-content: flex-end; }
    .totals table { width: 260px; }
    .totals td { text-align: right; }
    .totals td:first-child { text-align: left; }
    .print { margin-top: 18px; text-align: right; }
    .print button, .print a { background: #2d6a4f; color: #fff; border: none; padding: 10px 18px; border-radius: 999px; cursor:pointer; text-decoration:none }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="header">
      <div class="brand">SmartFood</div>
      <div class="meta">
        Facture #<?php echo (int)$order['id']; ?><br />
        <?php echo esc($order['created_at']); ?>
      </div>
    </div>

    <div class="section">
      <h3>Client</h3>
      <div><?php echo esc($order['customer_name']); ?></div>
      <?php if (!empty($order['customer_phone'])): ?><div><?php echo esc($order['customer_phone']); ?></div><?php endif; ?>
      <?php if (!empty($order['customer_email'])): ?><div><?php echo esc($order['customer_email']); ?></div><?php endif; ?>
      <?php if (!empty($order['delivery_address'])): ?><div><?php echo esc($order['delivery_address']); ?></div><?php endif; ?>
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
          <?php foreach ($order['items'] as $item): ?>
            <tr>
              <td><?php echo esc($item['product_name']); ?></td>
              <td><?php echo number_format((float)$item['unit_price'],2); ?> DT</td>
              <td><?php echo (int)$item['quantity']; ?></td>
              <td><?php echo number_format((float)$item['line_total'],2); ?> DT</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="totals">
      <table>
        <tr><td>Sous-total</td><td><?php echo number_format((float)$order['subtotal'],2); ?> DT</td></tr>
        <tr><td>Taxe</td><td><?php echo number_format((float)$order['tax'],2); ?> DT</td></tr>
        <tr><td><strong>Total</strong></td><td><strong><?php echo number_format((float)$order['total'],2); ?> DT</strong></td></tr>
      </table>
    </div>

    <div class="print">
      <button onclick="window.print()">Imprimer / Exporter PDF</button>
      <a href="/generate_invoice_pdf.php?id=<?php echo (int)$order['id']; ?>">Télécharger PDF</a>
    </div>
  </div>
</body>
</html>
