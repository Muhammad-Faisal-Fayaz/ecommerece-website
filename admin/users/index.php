<?php
// /admin/users/index.php — User management
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

$flash = getFlash();
$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u ORDER BY u.created_at DESC")->fetchAll();

$pageTitle = 'Users — Admin';
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Users</h1>
            <span style="color:var(--mid);font-size:14px;"><?= count($users) ?> registered</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Orders</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="status-badge" style="background:<?= $user['role'] === 'admin' ? '#c8a96e20' : '#eee' ?>;color:<?= $user['role'] === 'admin' ? 'var(--accent-d)' : 'var(--mid)' ?>;">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= $user['order_count'] ?></td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
