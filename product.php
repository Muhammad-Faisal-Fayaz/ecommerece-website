<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/unsplash.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']) . ' — ShopWave';
$imgUrl = resolveProductImageUrl($product);
?>
<?php include 'includes/header.php'; ?>

<?php ui_page_hero($product['name'], 'Premium quality · Curated for you', $product['category'] ?? 'Product'); ?>

<div class="container page-content product-detail-page">
    <?php ui_breadcrumb([
        ['label' => 'Home', 'url' => BASE_URL . '/index.php'],
        ['label' => $product['category'] ?? 'Shop', 'url' => BASE_URL . '/index.php?category=' . urlencode($product['category'] ?? '')],
        ['label' => $product['name'], 'url' => ''],
    ]); ?>

    <div class="product-detail">
        <div class="detail-img">
            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <div class="detail-info">
            <p class="detail-category"><?= htmlspecialchars($product['category'] ?? '') ?></p>
            <h1 class="detail-name"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="detail-price">$<?= number_format($product['price'], 2) ?></div>

            <div class="detail-stock">
                <?php if ($product['stock'] > 0): ?>
                    <span style="color:var(--success);font-weight:600;">● In Stock</span> — <?= (int) $product['stock'] ?> available
                <?php else: ?>
                    <span style="color:var(--danger);font-weight:600;">● Sold Out</span>
                <?php endif; ?>
            </div>

            <div class="detail-perks">
                <div class="detail-perk"><i class="fa-solid fa-truck-fast"></i> Free shipping</div>
                <div class="detail-perk"><i class="fa-solid fa-shield-halved"></i> Secure pay</div>
                <div class="detail-perk"><i class="fa-solid fa-rotate-left"></i> Easy returns</div>
            </div>

            <p class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <?php if ($product['stock'] > 0): ?>
                <form action="<?= BASE_URL ?>/cart_action.php" method="POST" style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="qty-control">
                        <button type="button" class="qty-btn" data-action="dec">−</button>
                        <input class="qty-input" type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="qty-btn" data-action="inc">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary" style="flex:1;min-width:200px;padding:16px 32px;">
                        <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-outline btn-block" disabled style="opacity:0.6;">Currently Unavailable</button>
            <?php endif; ?>

            <div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
                <a href="<?= BASE_URL ?>/cart.php" class="btn btn-dark"><i class="fa-solid fa-shopping-bag"></i> View Cart</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
