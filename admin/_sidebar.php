<aside class="admin-sidebar">
    <ul class="admin-nav">
        <li>
            <a href="<?= BASE_URL ?>/admin/index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'class="active"' : '' ?>>
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/products/index.php" <?= strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'class="active"' : '' ?>>
                <i class="fas fa-box-open"></i> Products
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/orders/index.php" <?= strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'class="active"' : '' ?>>
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/users/index.php" <?= strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'class="active"' : '' ?>>
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-store"></i> View Store</a></li>
    </ul>
</aside>
