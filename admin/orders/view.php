<?php
// /admin/orders/view.php — View & update order
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirect(BASE_URL . '/admin/orders/index.php');

$stmt = $pdo->prepare("SELECT o.*, u.name as customer, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) redirect(BASE_URL . '/admin/orders/index.php', 'Order not found.', 'error');

$items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items->execute([$id]);
$orderItems = $items->fetchAll();

$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    verify_csrf();
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    $status  = $_POST['status'];
    if (in_array($status, $allowed)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $id]);
        redirect(BASE_URL . '/admin/orders/view.php?id=' . $id, 'Order status updated!', 'success');
    }
}

$pageTitle = 'Order #' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . ' — Admin';
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h1>
            <a href="<?= BASE_URL ?>/admin/orders/index.php" class="btn btn-outline">← All Orders</a>
        </div>

        <div style="display:grid;grid-template-columns:1fr 300px;gap:32px;">
            <div>
                <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;overflow:hidden;margin-bottom:24px;">
                    <div style="background:var(--black);color:var(--white);padding:16px 24px;font-family:'Cormorant Garamond',serif;font-size:20px;">
                        Customer Details
                    </div>
                    <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);">Name</div>
                            <div style="font-weight:500;margin-top:4px;"><?= htmlspecialchars($order['full_name']) ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);">Email</div>
                            <div style="margin-top:4px;"><?= htmlspecialchars($order['email'] ?? 'Guest') ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);">Phone</div>
                            <div style="margin-top:4px;"><?= htmlspecialchars($order['phone']) ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);">Order Date</div>
                            <div style="margin-top:4px;"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></div>
                        </div>
                        <div style="grid-column:1/-1;">
                            <div style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--mid);">Address</div>
                            <div style="margin-top:4px;"><?= nl2br(htmlspecialchars($order['address'])) ?></div>
                        </div>
                    </div>
                </div>

                <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;overflow:hidden;">
                    <div style="background:var(--black);color:var(--white);padding:16px 24px;font-family:'Cormorant Garamond',serif;font-size:20px;">
                        Ordered Items
                    </div>
                    <div style="padding:8px 0;">
                        <?php foreach ($orderItems as $item): ?>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 24px;border-bottom:1px solid var(--cream);">
                                <div>
                                    <div style="font-weight:500;"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div style="font-size:13px;color:var(--mid);">Qty: <?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></div>
                                </div>
                                <div style="font-weight:600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <div style="display:flex;justify-content:space-between;padding:20px 24px;font-size:20px;font-family:'Cormorant Garamond',serif;font-weight:600;">
                            <span>Total</span>
                            <span>$<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Update -->
            <div>
                <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;padding:28px;">
                    <h3 style="font-size:22px;margin-bottom:20px;">Update Status</h3>

                    <div style="margin-bottom:20px;">
                        <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--mid);margin-bottom:8px;">Current Status</div>
                        <span class="status-badge status-<?= $order['status'] ?>" style="font-size:13px;padding:6px 14px;">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>

                    <form method="POST">
                        <?php csrf_field(); ?>
                        <div class="form-group">
                            <label class="form-label">New Status</label>
                            <select name="status" class="form-control">
                                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
