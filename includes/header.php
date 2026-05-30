<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/ui.php';

$cartCount = getCartCount();
$flash = getFlash();
$bodyClass = $bodyClass ?? '';
$extraCss = $extraCss ?? [];
$fullWidthLayout = $fullWidthLayout ?? false;

if (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') !== false) {
    $bodyClass = trim($bodyClass . ' page-admin');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ShopWave — Premium curated ecommerce. Discover exceptional products with secure checkout.">
    <title><?= $pageTitle ?? 'ShopWave' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/shopwave-theme.css">
    <?php foreach ($extraCss as $href): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($href) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✦</text></svg>">
</head>
<body class="<?= htmlspecialchars(trim($bodyClass)) ?>">

<script>window.BASE_URL = '<?= BASE_URL ?>';</script>

<?php if (strpos($bodyClass, 'page-admin') === false): ?>
<nav class="navbar">
    <div class="container nav-inner">
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo">Shop<span>Wave</span></a>

        <form class="nav-search" action="<?= BASE_URL ?>/index.php" method="GET">
            <input type="text" name="search" placeholder="Search the collection…" value="<?= sanitize($_GET['search'] ?? '') ?>">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-search"></i></button>
        </form>

        <button type="button" class="nav-toggle" aria-label="Open menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/index.php#products">Shop</a></li>
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= BASE_URL ?>/admin/index.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>/user/orders.php">Orders</a></li>
                <li><a href="<?= BASE_URL ?>/user/profile.php">Profile</a></li>
                <li>
                    <a href="<?= BASE_URL ?>/cart.php" class="nav-cart" aria-label="Cart">
                        <i class="fa-solid fa-shopping-bag"></i>
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
                        <button type="submit" aria-label="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>/user/login.php">Login</a></li>
                <li><a href="<?= BASE_URL ?>/user/register.php" class="btn btn-primary btn-sm">Join</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<?php endif; ?>

<?php if ($flash): ?>
    <?php if ($fullWidthLayout): ?>
        <div class="flash-bar">
            <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
        </div>
    <?php else: ?>
        <div class="container" style="padding-top:16px;">
            <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<main class="main-animate">
