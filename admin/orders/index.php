<?php
// /admin/orders/index.php — All orders
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

$flash = getFlash();
$orders = $pdo->query("SELECT o.*, u.name as customer FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

$pageTitle = 'Orders — Admin';
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">All Orders</h1>
            <span style="color:var(--mid);font-size:14px;"><?= count($orders) ?> orders</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= htmlspecialchars($order['customer'] ?? $order['full_name']) ?></td>
                        <td><?= htmlspecialchars($order['phone']) ?></td>
                        <td><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                        <td style="font-size:13px;">
                            <?= ($order['payment_method'] ?? 'cod') === 'stripe' ? 'Stripe' : 'COD' ?>
                            <br><span style="color:var(--mid);"><?= ucfirst($order['payment_status'] ?? 'pending') ?></span>
                        </td>
                        <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/orders/view.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" style="text-align:center;color:var(--mid);padding:40px;">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
