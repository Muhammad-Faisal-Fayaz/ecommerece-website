<?php
$extraJs = $extraJs ?? [];
?>
</main>

<footer class="footer footer-premium">
    <div class="container">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">SHOP<span style="color:var(--gold-light)">WAVE</span></div>
                <p style="font-size:13px; margin-top:12px; max-width:300px; line-height:1.7;">
                    A new standard in online retail — curated quality, seamless checkout, design that speaks before words do.
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
            <div>
                <h4>Shop</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/index.php">All Products</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?category=Electronics">Electronics</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?category=Clothing">Clothing</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?category=Accessories">Accessories</a></li>
                </ul>
            </div>
            <div>
                <h4>Account</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/user/login.php">Login</a></li>
                    <li><a href="<?= BASE_URL ?>/user/register.php">Register</a></li>
                    <li><a href="<?= BASE_URL ?>/user/orders.php">My Orders</a></li>
                    <li><a href="<?= BASE_URL ?>/cart.php">Cart</a></li>
                </ul>
            </div>
            <div>
                <h4>Newsletter</h4>
                <p style="font-size:13px;margin-bottom:8px;">Exclusive drops &amp; early access.</p>
                <form class="footer-newsletter" onsubmit="return false;">
                    <input type="email" placeholder="you@email.com" aria-label="Email">
                    <button type="button" class="btn btn-primary btn-sm">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-copy">
            &copy; <?= date('Y') ?> ShopWave. Crafted for those who expect more.
        </div>
    </div>
</footer>

<script src="<?= BASE_URL ?>/js/ui.js"></script>
<script src="<?= BASE_URL ?>/js/main.js"></script>
<?php foreach ($extraJs as $src): ?>
    <script src="<?= htmlspecialchars($src) ?>"></script>
<?php endforeach; ?>
</body>
</html>
