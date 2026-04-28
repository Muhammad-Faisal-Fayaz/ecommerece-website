<?php // /includes/footer.php ?>
<footer class="footer">
    <div class="container">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">SHOPWAVE</div>
                <p style="font-size:13px; margin-top:8px; max-width:280px;">
                    Curated goods for modern living. Quality and simplicity in everything we offer.
                </p>
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
        </div>
        <div class="footer-copy">
            &copy; <?= date('Y') ?> ShopWave. Built with PHP &amp; MySQL.
        </div>
    </div>
</footer>

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
