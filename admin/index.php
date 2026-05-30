<?php
// /admin/index.php — Admin dashboard
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../includes/inventory.php';
requireAdmin();

$flash = getFlash();
$lowStockProducts = get_low_stock_products($pdo);
$outOfStockCount  = count_out_of_stock($pdo);
$lowStockThreshold = low_stock_threshold();

// Stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalRevenue  = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, u.name as customer FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

$pageTitle = 'Admin Dashboard — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Dashboard</h1>
            <a href="<?= BASE_URL ?>/admin/products/create.php" class="btn btn-primary">+ Add Product</a>
        </div>

        <?php if (!empty($lowStockProducts)): ?>
            <div class="alert-banner alert-banner-warning">
                <div>
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Low stock alert</strong>
                    <span><?= count($lowStockProducts) ?> product(s) at or below <?= $lowStockThreshold ?> units
                        <?php if ($outOfStockCount > 0): ?> — <?= $outOfStockCount ?> out of stock<?php endif; ?>
                    </span>
                </div>
                <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline btn-sm">View products</a>
            </div>
            <div class="low-stock-list">
                <?php foreach (array_slice($lowStockProducts, 0, 6) as $p): ?>
                    <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['id'] ?>" class="low-stock-chip <?= (int)$p['stock'] === 0 ? 'low-stock-chip-danger' : '' ?>">
                        <?= htmlspecialchars($p['name']) ?>
                        <span><?= (int) $p['stock'] ?> left</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-value"><?= $totalProducts ?></div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalOrders ?></div>
                <div class="stat-label">Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="font-size:30px;">$<?= number_format($totalRevenue, 0) ?></div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>

        <h2 style="font-size:28px;margin-bottom:20px;">Recent Orders</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= htmlspecialchars($order['customer'] ?? $order['full_name']) ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/orders/view.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--mid);padding:40px;">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
