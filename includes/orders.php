<?php
// /includes/orders.php — Shared order helpers

function fulfill_order_items(PDO $pdo, int $orderId, array $cart): void
{
    $itemStmt = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?,?,?,?,?)'
    );

    foreach ($cart as $productId => $item) {
        $itemStmt->execute([
            $orderId,
            $productId,
            $item['name'],
            $item['quantity'],
            $item['price'],
        ]);

        $pdo->prepare(
            'UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?'
        )->execute([$item['quantity'], $productId, $item['quantity']]);
    }
}

function create_order(
    PDO $pdo,
    int $userId,
    string $fullName,
    string $address,
    string $phone,
    float $total,
    string $paymentMethod,
    string $paymentStatus = 'pending'
): int {
    $stmt = $pdo->prepare(
        'INSERT INTO orders (user_id, full_name, address, phone, total_amount, payment_method, payment_status)
         VALUES (?,?,?,?,?,?,?)'
    );
    $stmt->execute([$userId, $fullName, $address, $phone, $total, $paymentMethod, $paymentStatus]);
    return (int) $pdo->lastInsertId();
}

function save_order_pending_cart(PDO $pdo, int $orderId, array $cart): void
{
    $pdo->prepare('UPDATE orders SET pending_cart_json = ? WHERE id = ?')
        ->execute([json_encode($cart), $orderId]);
}

function load_pending_cart_from_order(array $order): ?array
{
    if (empty($order['pending_cart_json'])) {
        return null;
    }
    $cart = json_decode($order['pending_cart_json'], true);
    return is_array($cart) ? $cart : null;
}

/**
 * Mark a Stripe order as paid and fulfill line items (idempotent).
 */
function complete_stripe_order(PDO $pdo, int $orderId, string $sessionId): bool
{
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order || $order['payment_method'] !== 'stripe') {
        return false;
    }

    if ($order['payment_status'] === 'paid') {
        return true;
    }

    $cart = load_pending_cart_from_order($order);

    try {
        $pdo->beginTransaction();

        $check = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
        $check->execute([$orderId]);

        if ((int) $check->fetchColumn() === 0) {
            if (!$cart) {
                throw new RuntimeException('No pending cart data for order ' . $orderId);
            }
            fulfill_order_items($pdo, $orderId, $cart);
        }

        $pdo->prepare(
            'UPDATE orders SET payment_status = ?, stripe_session_id = ?, status = ?, pending_cart_json = NULL WHERE id = ?'
        )->execute(['paid', $sessionId, 'processing', $orderId]);

        $pdo->commit();
        send_order_confirmation_email($pdo, $orderId);
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('complete_stripe_order: ' . $e->getMessage());
        return false;
    }
}

function send_order_confirmation_email(PDO $pdo, int $orderId): void
{
    require_once __DIR__ . '/mailer.php';

    $stmt = $pdo->prepare(
        'SELECT o.*, u.email, u.name AS account_name
         FROM orders o
         LEFT JOIN users u ON o.user_id = u.id
         WHERE o.id = ?'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order || empty($order['email'])) {
        return;
    }

    $items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $items->execute([$orderId]);
    $orderItems = $items->fetchAll();

    $rows = '';
    foreach ($orderItems as $item) {
        $line = number_format($item['price'] * $item['quantity'], 2);
        $rows .= '<tr>
            <td style="padding:8px 0;border-bottom:1px solid #f4f1eb;">' . htmlspecialchars($item['product_name']) . ' × ' . (int) $item['quantity'] . '</td>
            <td style="padding:8px 0;border-bottom:1px solid #f4f1eb;text-align:right;">$' . $line . '</td>
        </tr>';
    }

    $orderNum = str_pad($order['id'], 6, '0', STR_PAD_LEFT);
    $paymentLabel = strtoupper($order['payment_method'] ?? 'cod');
    $total = number_format($order['total_amount'], 2);

    $content = "
        <p>Hi <strong>" . htmlspecialchars($order['full_name']) . "</strong>,</p>
        <p>Your order <strong>#{$orderNum}</strong> has been confirmed.</p>
        <p><strong>Payment:</strong> {$paymentLabel} &nbsp;|&nbsp; <strong>Status:</strong> " . ucfirst($order['payment_status']) . "</p>
        <table width=\"100%\" style=\"margin:16px 0;\">{$rows}</table>
        <p style=\"font-size:18px;font-weight:bold;\">Total: \${$total}</p>
        <p style=\"color:#6b6b6b;font-size:14px;\">Delivery address:<br>" . nl2br(htmlspecialchars($order['address'])) . "</p>
    ";

    $app = app_config('app_name', 'ShopWave');
    send_app_email(
        $order['email'],
        $order['account_name'] ?? $order['full_name'],
        "{$app} — Order #{$orderNum} confirmed",
        email_template('Order confirmed', $content),
        "Your order #{$orderNum} total is \${$total}."
    );
}
