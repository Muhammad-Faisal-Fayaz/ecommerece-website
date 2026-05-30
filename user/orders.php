<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'My Orders — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<?php ui_page_hero('My Orders', count($orders) . ' order(s) in your history', 'Account'); ?>

<div class="container page-content-tight">
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="icon"><i class="fa-solid fa-box-open"></i></div>
            <h3>No orders yet</h3>
            <p>Your purchases will appear here.</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order):
            $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
        ?>
            <article class="order-card">
                <div class="order-card-head">
                    <div class="order-card-meta">
                        <div><label>Order</label><div>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div></div>
                        <div><label>Date</label><div><?= date('M j, Y', strtotime($order['created_at'])) ?></div></div>
                        <div><label>Total</label><div style="color:var(--gold-light);">$<?= number_format($order['total_amount'], 2) ?></div></div>
                        <div><label>Payment</label><div><?= ($order['payment_method'] ?? 'cod') === 'stripe' ? 'Card' : 'COD' ?> · <?= ucfirst($order['payment_status'] ?? 'paid') ?></div></div>
                    </div>
                    <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
                </div>
                <div class="order-card-body">
                    <?php foreach ($items as $item): ?>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--line);">
                            <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                            <strong>$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <p style="font-size:13px;color:var(--ink-soft);margin-top:12px;"><?= htmlspecialchars($order['address']) ?></p>
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <a href="<?= BASE_URL ?>/invoice.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm" style="margin-top:16px;" target="_blank">
                            <i class="fa-solid fa-file-pdf"></i> Invoice
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
