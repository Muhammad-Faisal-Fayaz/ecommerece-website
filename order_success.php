<?php
// /order_success.php — Order confirmation
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orderId) { header('Location: /index.php'); exit; }

// Verify the order belongs to this user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) { header('Location: /index.php'); exit; }

$items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items->execute([$orderId]);
$orderItems = $items->fetchAll();

$pageTitle = 'Order Confirmed — ShopWave';
?>
<?php include 'includes/header.php'; ?>

<div class="container section">
    <div class="order-success">
        <div class="order-icon">✓</div>
        <h1 style="font-size:52px;margin-bottom:12px;">Order Confirmed!</h1>
        <p style="color:var(--mid);font-size:16px;margin-bottom:8px;">
            Thank you, <?= htmlspecialchars($order['full_name']) ?>!
        </p>
        <p style="color:var(--mid);margin-bottom:48px;">
            Your order <strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong> has been placed successfully.
            We'll have it ready for delivery soon.
        </p>

        <div style="max-width:560px;margin:0 auto;text-align:left;background:var(--white);border:1px solid var(--light);border-radius:8px;overflow:hidden;">
            <div style="background:var(--black);color:var(--white);padding:20px 28px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-family:'Cormorant Garamond',serif;font-size:22px;">Order Details</span>
                <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
            </div>

            <div style="padding:28px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--light);">
                    <div>
                        <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);margin-bottom:4px;">Order ID</div>
                        <div style="font-weight:600;">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);margin-bottom:4px;">Date</div>
                        <div><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);margin-bottom:4px;">Phone</div>
                        <div><?= htmlspecialchars($order['phone']) ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);margin-bottom:4px;">Delivery</div>
                        <div style="color:var(--success);font-weight:600;">Free</div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);margin-bottom:4px;">Address</div>
                        <div><?= nl2br(htmlspecialchars($order['address'])) ?></div>
                    </div>
                </div>

                <!-- Items -->
                <?php foreach ($orderItems as $item): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <div>
                            <div style="font-weight:500;"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div style="font-size:13px;color:var(--mid);">Qty: <?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div style="font-weight:600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                    </div>
                <?php endforeach; ?>

                <div style="display:flex;justify-content:space-between;font-size:20px;font-family:'Cormorant Garamond',serif;font-weight:600;padding-top:16px;border-top:2px solid var(--charcoal);margin-top:16px;">
                    <span>Total</span>
                    <span>$<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:16px;justify-content:center;margin-top:36px;">
            <a href="<?= BASE_URL ?>/user/orders.php" class="btn btn-outline">View All Orders</a>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Continue Shopping →</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
