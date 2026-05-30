<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orderId) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$orderId]);
$orderItems = $items->fetchAll();
$orderNum = str_pad($order['id'], 6, '0', STR_PAD_LEFT);

$pageTitle = 'Order Confirmed — ShopWave';
?>
<?php include 'includes/header.php'; ?>

<?php ui_page_hero('Order Confirmed', 'Thank you, ' . $order['full_name'], 'Success'); ?>

<div class="container page-content-tight">
    <div class="success-page" style="padding-top:0;">
        <div class="success-ring"><i class="fa-solid fa-check"></i></div>
        <h1 style="font-family:var(--font-display);font-size:clamp(32px,4vw,48px);margin-bottom:12px;">You're All Set!</h1>
        <p style="color:var(--ink-soft);max-width:480px;margin:0 auto 8px;">
            Order <strong>#<?= $orderNum ?></strong> is confirmed. We'll prepare it for delivery soon.
        </p>

        <div class="success-card">
            <div class="order-card-head" style="border-radius:0;">
                <span style="font-family:var(--font-display);font-size:22px;">Order #<?= $orderNum ?></span>
                <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
            </div>
            <div style="padding:28px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:20px;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--line);">
                    <div><label style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--ink-soft);">Date</label><div><?= date('M j, Y', strtotime($order['created_at'])) ?></div></div>
                    <div><label style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--ink-soft);">Payment</label><div><?= ($order['payment_method'] ?? 'cod') === 'stripe' ? 'Card' : 'COD' ?> · <?= ucfirst($order['payment_status'] ?? 'paid') ?></div></div>
                    <div><label style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--ink-soft);">Phone</label><div><?= htmlspecialchars($order['phone']) ?></div></div>
                </div>
                <p style="font-size:13px;color:var(--ink-soft);margin-bottom:16px;"><strong>Ship to:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
                <?php foreach ($orderItems as $item): ?>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--line);">
                        <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                        <strong>$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div style="display:flex;justify-content:space-between;font-family:var(--font-display);font-size:24px;font-weight:600;padding-top:16px;margin-top:8px;">
                    <span>Total</span>
                    <span style="color:var(--gold);">$<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:center;margin-top:32px;flex-wrap:wrap;">
            <?php if (($order['payment_status'] ?? 'paid') === 'paid'): ?>
                <a href="<?= BASE_URL ?>/invoice.php?id=<?= $order['id'] ?>" class="btn btn-outline" target="_blank"><i class="fa-solid fa-file-pdf"></i> Invoice</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/user/orders.php" class="btn btn-outline">My Orders</a>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
