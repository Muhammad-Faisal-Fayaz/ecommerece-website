<aside class="admin-sidebar">
    <ul class="admin-nav">
        <li>
            <a href="<?= BASE_URL ?>/admin/index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'class="active"' : '' ?>>
                📊 Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/products/index.php" <?= strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'class="active"' : '' ?>>
                📦 Products
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/orders/index.php" <?= strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'class="active"' : '' ?>>
                🛒 Orders
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/users/index.php" <?= strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'class="active"' : '' ?>>
                👥 Users
            </a>
        </li>
        <li><a href="<?= BASE_URL ?>/index.php">🏠 View Store</a></li>
    </ul>
</aside>
