<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">Shop<span>Wave</span></div>
    <ul class="admin-nav">
        <li>
            <a href="<?= BASE_URL ?>/admin/index.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/admin/') !== false && strpos($_SERVER['PHP_SELF'], '/products/') === false && strpos($_SERVER['PHP_SELF'], '/orders/') === false && strpos($_SERVER['PHP_SELF'], '/users/') === false) ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/products/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-gem"></i> Products
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/orders/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-receipt"></i> Orders
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/users/index.php" class="<?= strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Customers
            </a>
        </li>
        <li style="margin-top:24px;border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
            <a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-arrow-up-right-from-square"></i> Live Store</a>
        </li>
        <li>
            <form action="<?= BASE_URL ?>/user/logout.php" method="POST">
                <?php csrf_field(); ?>
                <button type="submit" style="width:100%;text-align:left;background:none;border:none;color:inherit;font:inherit;cursor:pointer;display:flex;align-items:center;gap:10px;padding:14px 24px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</aside>
