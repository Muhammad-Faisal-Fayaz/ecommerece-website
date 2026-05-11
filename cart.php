<?php
// /cart.php — Shopping cart page
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/unsplash.php';

$cart  = getCart();
$total = getCartTotal();
$flash = getFlash();
$pageTitle = 'Shopping Cart — ShopWave';
?>
<?php include 'includes/header.php'; ?>

<div class="container section">
    <h1 class="section-title" style="margin-bottom:40px;">Shopping Cart</h1>

    <?php if (empty($cart)): ?>
        <div class="empty-state">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:1fr 340px;gap:48px;align-items:start;">
            <div>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $id => $item): ?>
                            <tr>
                                <td>
                                    <div class="cart-product-info">
                                        <?php 
                                        $unsplashUrl = getUnsplashImage($item['name'], $item['category']);
                                        $imgPath = 'images/products/' . ($item['image'] ?? ''); 
                                        ?>
                                        <?php if ($item['image'] && file_exists($imgPath)): ?>
                                            <img class="cart-thumb" src="<?= BASE_URL ?>/<?= $imgPath ?>" alt="">
                                        <?php else: ?>
                                            <img class="cart-thumb" src="<?= htmlspecialchars($unsplashUrl) ?>" alt="">
                                        <?php endif; ?>
                                        <div>
                                            <div class="cart-product-name">
                                                <a href="<?= BASE_URL ?>/product.php?id=<?= $id ?>"><?= htmlspecialchars($item['name']) ?></a>
                                            </div>
                                            <div class="cart-product-cat"><?= htmlspecialchars($item['category'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" data-action="dec"
                                            onclick="this.nextElementSibling.value = Math.max(1, +this.nextElementSibling.value - 1); updateCartQty(<?= $id ?>, this.nextElementSibling.value)">−</button>
                                        <input class="qty-input" type="number" value="<?= $item['quantity'] ?>" min="1"
                                            onchange="updateCartQty(<?= $id ?>, this.value)">
                                        <button type="button" class="qty-btn" data-action="inc"
                                            onclick="this.previousElementSibling.value = +this.previousElementSibling.value + 1; updateCartQty(<?= $id ?>, this.previousElementSibling.value)">+</button>
                                    </div>
                                </td>
                                <td id="subtotal-<?= $id ?>">
                                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </td>
                                <td>
                                    <form action="<?= BASE_URL ?>/cart_action.php" method="POST">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $id ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            data-confirm="Remove this item from cart?"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top:24px;">
                    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">← Continue Shopping</a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>

                <?php foreach ($cart as $item): ?>
                    <div class="summary-row">
                        <span><?= htmlspecialchars($item['name']) ?> ×<?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="summary-row" style="color:var(--mid);border-top:1px solid var(--light);padding-top:12px;margin-top:8px;">
                    <span>Subtotal</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-row" style="color:var(--mid);">
                    <span>Shipping</span>
                    <span style="color:var(--success);font-weight:600;">Free</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span id="cart-total">$<?= number_format($total, 2) ?></span>
                </div>

                <div style="margin-top:28px;">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary btn-block">Proceed to Checkout →</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/login.php" class="btn btn-primary btn-block">Login to Checkout</a>
                        <p style="text-align:center;font-size:13px;color:var(--mid);margin-top:12px;">
                            Don't have an account? <a href="<?= BASE_URL ?>/user/register.php" style="color:var(--accent);">Register</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
