<?php
// /user/orders.php — User's order history
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$flash = getFlash();
$pageTitle = 'My Orders — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<div class="container section">
    <div class="section-header">
        <h1 class="section-title">My Orders</h1>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline btn-sm">← Continue Shopping</a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="icon">📦</div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders. Start shopping now!</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Shop Now</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
            ?>
            <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;margin-bottom:24px;overflow:hidden;">
                <div style="background:var(--cream);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div style="display:flex;gap:32px;align-items:center;">
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--mid);">Order ID</div>
                            <div style="font-weight:600;">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--mid);">Date</div>
                            <div><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--mid);">Total</div>
                            <div style="font-weight:600;">$<?= number_format($order['total_amount'], 2) ?></div>
                        </div>
                    </div>
                    <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
                </div>

                <div style="padding:20px 24px;">
                    <?php foreach ($items as $item): ?>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--cream);">
                            <div>
                                <div style="font-weight:500;"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div style="font-size:13px;color:var(--mid);">Qty: <?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></div>
                            </div>
                            <div style="font-weight:600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top:16px;font-size:13px;color:var(--mid);">
                        <strong>Shipped to:</strong> <?= htmlspecialchars($order['address']) ?> | 📞 <?= htmlspecialchars($order['phone']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
