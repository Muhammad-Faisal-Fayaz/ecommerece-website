<?php
// /includes/invoice.php — PDF invoice generation

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function fetch_order_for_invoice(PDO $pdo, int $orderId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT o.*, u.email AS customer_email, u.name AS account_name
         FROM orders o
         LEFT JOIN users u ON o.user_id = u.id
         WHERE o.id = ?'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        return null;
    }

    $items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $items->execute([$orderId]);
    $order['items'] = $items->fetchAll();
    return $order;
}

function can_access_order_invoice(array $order): bool
{
    if (isAdmin()) {
        return true;
    }
    return isLoggedIn() && isset($_SESSION['user_id']) && (int) $order['user_id'] === (int) $_SESSION['user_id'];
}

function build_invoice_html(array $order): string
{
    $app = htmlspecialchars(app_config('app_name', 'ShopWave'));
    $orderNum = str_pad($order['id'], 6, '0', STR_PAD_LEFT);
    $date = date('F j, Y', strtotime($order['created_at']));
    $paymentMethod = ($order['payment_method'] ?? 'cod') === 'stripe' ? 'Card (Stripe)' : 'Cash on Delivery';
    $paymentStatus = ucfirst($order['payment_status'] ?? 'paid');
    $status = ucfirst($order['status']);

    $rows = '';
    foreach ($order['items'] as $item) {
        $line = number_format($item['price'] * $item['quantity'], 2);
        $rows .= '<tr>
            <td>' . htmlspecialchars($item['product_name']) . '</td>
            <td style="text-align:center;">' . (int) $item['quantity'] . '</td>
            <td style="text-align:right;">$' . number_format($item['price'], 2) . '</td>
            <td style="text-align:right;">$' . $line . '</td>
        </tr>';
    }

    $total = number_format($order['total_amount'], 2);
    $customerEmail = htmlspecialchars($order['customer_email'] ?? '—');
    $fullName = htmlspecialchars($order['full_name']);
    $phone = htmlspecialchars($order['phone']);
    $address = nl2br(htmlspecialchars($order['address']));

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #2a2a2a; }
  h1 { font-size: 22px; margin: 0 0 4px; }
  .muted { color: #6b6b6b; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th { background: #0a0a0a; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
  td { padding: 8px; border-bottom: 1px solid #e8e4dc; }
  .totals { margin-top: 16px; text-align: right; font-size: 16px; font-weight: bold; }
  .box { border: 1px solid #d8d4cb; padding: 12px; margin-top: 16px; }
</style>
</head>
<body>
  <h1>{$app}</h1>
  <p class="muted">INVOICE #{$orderNum} &nbsp;·&nbsp; {$date}</p>

  <table style="width:100%;margin-top:24px;">
    <tr>
      <td style="width:50%;vertical-align:top;">
        <strong>Bill to</strong><br>
        {$fullName}<br>
        {$customerEmail}<br>
        {$phone}
      </td>
      <td style="width:50%;vertical-align:top;text-align:right;">
        <strong>Order status</strong><br>
        {$status}<br><br>
        <strong>Payment</strong><br>
        {$paymentMethod} — {$paymentStatus}
      </td>
    </tr>
  </table>

  <div class="box">
    <strong>Delivery address</strong><br>
    {$address}
  </div>

  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th style="text-align:center;">Qty</th>
        <th style="text-align:right;">Unit</th>
        <th style="text-align:right;">Line total</th>
      </tr>
    </thead>
    <tbody>{$rows}</tbody>
  </table>

  <p class="totals">Total: \${$total}</p>
  <p class="muted" style="margin-top:32px;">Thank you for shopping with {$app}.</p>
</body>
</html>
HTML;
}

function generate_invoice_pdf(array $order): string
{
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml(build_invoice_html($order));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return $dompdf->output();
}

function stream_invoice_pdf(PDO $pdo, int $orderId): void
{
    $order = fetch_order_for_invoice($pdo, $orderId);
    if (!$order || !can_access_order_invoice($order)) {
        http_response_code(403);
        die('Access denied.');
    }

    if ($order['payment_status'] !== 'paid') {
        http_response_code(400);
        die('Invoice is available only for paid orders.');
    }

    $pdf = generate_invoice_pdf($order);
    $filename = 'invoice-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}
