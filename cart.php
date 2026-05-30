<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/unsplash.php';

$cart  = getCart();
$total = getCartTotal();
$pageTitle = 'Shopping Cart — ShopWave';
?>
<?php include 'includes/header.php'; ?>

<?php ui_page_hero('Your Cart', empty($cart) ? 'Your bag is waiting to be filled' : count($cart) . ' item(s) ready for checkout', 'Shopping'); ?>

<div class="container page-content-tight">
    <?php if (empty($cart)): ?>
        <div class="empty-state">
            <div class="icon"><i class="fa-solid fa-bag-shopping"></i></div>
            <h3>Your cart is empty</h3>
            <p>Discover pieces crafted for modern living.</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Explore Collection</a>
        </div>
    <?php else: ?>
        <?php ui_checkout_steps(1); ?>

        <div class="cart-layout">
            <div>
                <?php foreach ($cart as $id => $item):
                    $imgUrl = getLocalProductImageUrl($item);
                ?>
                    <article class="cart-item-card">
                        <img class="cart-thumb" src="<?= htmlspecialchars($imgUrl) ?>" alt="">
                        <div>
                            <div class="cart-product-name">
                                <a href="<?= BASE_URL ?>/product.php?id=<?= $id ?>"><?= htmlspecialchars($item['name']) ?></a>
                            </div>
                            <div class="cart-product-cat"><?= htmlspecialchars($item['category'] ?? '') ?></div>
                            <div style="margin-top:8px;font-weight:600;color:var(--gold);">$<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" data-action="dec"
                                onclick="var i=this.nextElementSibling;i.value=Math.max(1,+i.value-1);updateCartQty(<?= $id ?>,i.value)">−</button>
                            <input class="qty-input" type="number" value="<?= $item['quantity'] ?>" min="1"
                                onchange="updateCartQty(<?= $id ?>, this.value)">
                            <button type="button" class="qty-btn" data-action="inc"
                                onclick="var i=this.previousElementSibling;i.value=+i.value+1;updateCartQty(<?= $id ?>,i.value)">+</button>
                        </div>
                        <div style="text-align:right;">
                            <div id="subtotal-<?= $id ?>" style="font-family:var(--font-display);font-size:22px;font-weight:600;">
                                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </div>
                            <form action="<?= BASE_URL ?>/cart_action.php" method="POST" style="margin-top:12px;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this item?">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
            </div>

            <aside class="cart-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart as $item): ?>
                    <div class="summary-row">
                        <span><?= htmlspecialchars($item['name']) ?> ×<?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row" style="padding-top:12px;margin-top:8px;border-top:1px solid rgba(255,255,255,0.1);">
                    <span>Shipping</span>
                    <span style="color:var(--gold-light);">Free</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="cart-total">$<?= number_format($total, 2) ?></span>
                </div>
                <div style="margin-top:28px;">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary btn-block" style="padding:16px;">Proceed to Checkout →</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/login.php" class="btn btn-primary btn-block" style="padding:16px;">Login to Checkout</a>
                        <p style="text-align:center;font-size:13px;margin-top:14px;opacity:0.7;">
                            New? <a href="<?= BASE_URL ?>/user/register.php" style="color:var(--gold-light);">Create account</a>
                        </p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
