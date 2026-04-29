<?php
// /includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
$cartCount = getCartCount();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'ShopWave' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛍</text></svg>">
</head>
<body>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>

<nav class="navbar">
    <div class="container nav-inner">
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo">Shop<span>Wave</span></a>

        <form class="nav-search" action="<?= BASE_URL ?>/index.php" method="GET">
            <input type="text" name="search" placeholder="Search products…" value="<?= sanitize($_GET['search'] ?? '') ?>">
            <button type="submit">⌕</button>
        </form>

        <ul class="nav-links">
            <?php if (isLoggedIn()): ?>
                <li><a href="<?= BASE_URL ?>/index.php">Shop</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= BASE_URL ?>/admin/index.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>/user/orders.php">My Orders</a></li>
                <li>
                    <a href="<?= BASE_URL ?>/cart.php" class="nav-cart">
                        🛒 Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
                        <?php else: ?>
                            <span id="cart-count" style="display:none" class="cart-badge">0</span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <form action="<?= BASE_URL ?>/user/logout.php" method="POST" style="display:inline">
                        <?php csrf_field(); ?>
                        <button type="submit">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>/user/login.php">Login</a></li>
                <li><a href="<?= BASE_URL ?>/user/register.php" class="btn btn-primary btn-sm">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container" style="padding-top: 20px;">
<?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>
</div>
