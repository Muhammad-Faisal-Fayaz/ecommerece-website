<?php
// /product.php — Product detail page
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: /index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) { header('Location: /index.php'); exit; }

$flash = getFlash();
$pageTitle = htmlspecialchars($product['name']) . ' — ShopWave';
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <p class="breadcrumb" style="padding-top:24px;">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <span>/</span>
        <a href="<?= BASE_URL ?>/index.php?category=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a>
        <span>›</span>
        <?= htmlspecialchars($product['name']) ?>
    </p>

    <div class="product-detail">
        <div class="detail-img">
            <?php
            $imgPath = 'images/products/' . $product['image'];
            if ($product['image'] && file_exists($imgPath)): ?>
                <img src="<?= BASE_URL ?>/<?= $imgPath ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <span style="font-size:120px;opacity:0.2;">📦</span>
            <?php endif; ?>
        </div>

        <div class="detail-info">
            <p class="detail-category"><?= htmlspecialchars($product['category'] ?? '') ?></p>
            <h1 class="detail-name"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="detail-price">$<?= number_format($product['price'], 2) ?></div>

            <div class="detail-stock">
                <?php if ($product['stock'] > 0): ?>
                    <span>✓ In Stock</span> — <?= $product['stock'] ?> available
                <?php else: ?>
                    <span style="color:var(--danger);">✗ Out of Stock</span>
                <?php endif; ?>
            </div>

            <p class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <?php if ($product['stock'] > 0): ?>
                <form action="<?= BASE_URL ?>/cart_action.php" method="POST" style="display:flex;gap:16px;align-items:center;">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <div class="qty-control">
                        <button type="button" class="qty-btn" data-action="dec">−</button>
                        <input class="qty-input" type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="qty-btn" data-action="inc">+</button>
                    </div>

                    <button type="submit" class="btn btn-primary" style="flex:1;">
                        🛒 Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-outline btn-block" disabled>Out of Stock</button>
            <?php endif; ?>

            <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--light);display:flex;gap:24px;">
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">← Continue Shopping</a>
                <a href="<?= BASE_URL ?>/cart.php" class="btn btn-dark">View Cart 🛒</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
