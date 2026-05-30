<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/unsplash.php';

// Redirect guests to the landing/login selection page
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/landing.php');
    exit;
}

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort']     ?? 'newest');
$isHomepage = ($search === '' && $category === '');

$params = [];
$sql = 'SELECT * FROM products WHERE 1=1';

if ($search !== '') {
    $sql .= ' AND (name LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category !== '') {
    $sql .= ' AND category = ?';
    $params[] = $category;
}

$sql .= match ($sort) {
    'price_asc'  => ' ORDER BY price ASC',
    'price_desc' => ' ORDER BY price DESC',
    'name'       => ' ORDER BY name ASC',
    default      => ' ORDER BY created_at DESC',
};

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $pdo->query(
    'SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != "" ORDER BY category'
)->fetchAll();

$featured = [];
$categoryCards = [];
if ($isHomepage) {
    $featured = $pdo->query(
        'SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 4'
    )->fetchAll();

    foreach ($cats as $cat) {
        $catName = $cat['category'];
        $sample = $pdo->prepare(
            'SELECT * FROM products WHERE category = ? ORDER BY created_at DESC LIMIT 1'
        );
        $sample->execute([$catName]);
        $row = $sample->fetch();
        if ($row) {
            $categoryCards[] = $row;
        }
    }
}

$categoryImages = [
    'Electronics' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=600&q=80',
    'Clothing'    => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=600&q=80',
    'Accessories' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80',
    'Kitchen'     => 'https://images.unsplash.com/photo-1556910103-1c02745a30bf?w=600&q=80',
    'Sports'      => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&q=80',
];
$pageTitle = 'ShopWave — Premium Online Store';
$bodyClass = $isHomepage ? 'page-home' : '';
$extraCss  = $isHomepage ? [BASE_URL . '/css/home.css'] : [];
$fullWidthLayout = $isHomepage;

$heroSlides = [
    [
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&q=85',
        'eyebrow' => 'Spring Collection 2026',
        'title' => 'Style That <em>Defines</em> You',
        'text' => 'Discover curated fashion, tech, and lifestyle pieces — handpicked for quality and modern living.',
        'cta' => 'Shop Now',
        'link' => '#products',
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1920&q=85',
        'eyebrow' => 'Premium Audio & Tech',
        'title' => 'Sound & <em>Innovation</em>',
        'text' => 'Upgrade your everyday with electronics engineered for performance and elegance.',
        'cta' => 'Explore Tech',
        'link' => BASE_URL . '/index.php?category=Electronics',
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1920&q=85',
        'eyebrow' => 'Limited Time Offer',
        'title' => 'Free Shipping <em>Worldwide</em>',
        'text' => 'On all orders this season. Secure checkout with card or cash on delivery.',
        'cta' => 'View Deals',
        'link' => '#featured',
    ],
];

function render_product_card(array $product, bool $premium = false): void
{
    $cardClass = $premium ? 'product-card product-card-premium' : 'product-card';
    $imgUrl = getLocalProductImageUrl($product);
    ?>
    <article class="<?= $cardClass ?>">
        <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="product-img">
            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
            <?php if ((int) $product['stock'] <= 5 && (int) $product['stock'] > 0): ?>
                <span class="product-badge">Low Stock</span>
            <?php elseif ((int) $product['stock'] === 0): ?>
                <span class="product-badge" style="background:#c0392b;color:#fff;">Sold Out</span>
            <?php endif; ?>
        </a>
        <div class="product-body">
            <p class="product-category"><?= htmlspecialchars($product['category'] ?? '') ?></p>
            <h3 class="product-name">
                <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
            </h3>
            <?php if (!$premium): ?>
                <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
            <?php endif; ?>
            <div class="product-footer">
                <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                <div class="product-quick-add" style="display:flex;gap:8px;">
                    <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">View</a>
                    <?php if ((int) $product['stock'] > 0): ?>
                        <form action="<?= BASE_URL ?>/cart_action.php" method="POST">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Add</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
    <?php
}
?>
<?php include 'includes/header.php'; ?>

<?php if ($isHomepage): ?>

<section class="hero-slider" aria-label="Featured promotions">
    <?php foreach ($heroSlides as $i => $slide): ?>
        <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" data-slide="<?= $i ?>">
            <div class="hero-slide-bg" style="background-image:url('<?= htmlspecialchars($slide['image']) ?>')"></div>
            <div class="hero-slide-overlay"></div>
            <div class="container hero-slide-content">
                <div class="hero-slide-inner">
                    <p class="hero-eyebrow"><?= htmlspecialchars($slide['eyebrow']) ?></p>
                    <h2><?= $slide['title'] ?></h2>
                    <p><?= htmlspecialchars($slide['text']) ?></p>
                    <div class="hero-actions">
                        <a href="<?= htmlspecialchars($slide['link']) ?>" class="btn btn-primary"><?= htmlspecialchars($slide['cta']) ?></a>
                        <a href="<?= BASE_URL ?>/user/register.php" class="btn btn-hero-outline">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="hero-progress" aria-hidden="true"></div>
    <div class="hero-controls">
        <div class="container hero-controls-inner">
            <div class="hero-dots">
                <?php foreach ($heroSlides as $i => $slide): ?>
                    <button type="button" class="hero-dot <?= $i === 0 ? 'is-active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="hero-arrows">
                <button type="button" class="hero-arrow hero-prev" aria-label="Previous slide"><i class="fa-solid fa-arrow-left"></i></button>
                <button type="button" class="hero-arrow hero-next" aria-label="Next slide"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</section>

<section class="trust-strip">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item">
                <i class="fa-solid fa-truck-fast"></i>
                <div><strong>Free Shipping</strong><span>On qualifying orders</span></div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-shield-halved"></i>
                <div><strong>Secure Payment</strong><span>Stripe &amp; COD supported</span></div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-rotate-left"></i>
                <div><strong>Easy Returns</strong><span>30-day return policy</span></div>
            </div>
            <div class="trust-item">
                <i class="fa-solid fa-headset"></i>
                <div><strong>24/7 Support</strong><span>We're here to help</span></div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($categoryCards)): ?>
<section class="categories-section">
    <div class="container">
        <div class="section-head-center">
            <span class="section-label">Browse by category</span>
            <h2 class="section-title">Shop by Style</h2>
            <p>Find exactly what you're looking for across our curated collections.</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($categoryCards as $catProduct):
                $catName = $catProduct['category'];
                $catImg = $categoryImages[$catName] ?? getLocalProductImageUrl($catProduct);
            ?>
                <a href="<?= BASE_URL ?>/index.php?category=<?= urlencode($catName) ?>" class="category-card">
                    <img src="<?= htmlspecialchars($catImg) ?>" alt="<?= htmlspecialchars($catName) ?>" loading="lazy">
                    <div class="category-card-overlay">
                        <h3><?= htmlspecialchars($catName) ?></h3>
                        <span>Shop collection →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($featured)): ?>
<section class="featured-section" id="featured">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-label">Handpicked for you</span>
                <h2 class="section-title">Featured Products</h2>
            </div>
            <a href="#products" class="btn btn-outline">View All →</a>
        </div>
        <div class="featured-grid">
            <?php foreach ($featured as $product): ?>
                <?php render_product_card($product, true); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="container">
    <div class="promo-banner">
        <div class="promo-banner-content">
            <span class="section-label">Member exclusive</span>
            <h3>Get 10% Off Your First Order</h3>
            <p>Register today and enjoy premium products with exclusive member perks.</p>
            <a href="<?= BASE_URL ?>/user/register.php" class="btn btn-primary">Join ShopWave</a>
        </div>
        <div class="promo-banner-visual" style="background-image:url('https://images.unsplash.com/photo-1483985988350-763728e3685b?w=800&q=80')"></div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<section class="catalog-section" id="products">
    <div class="container">
        <div class="section-head-center">
            <span class="section-label">Our catalog</span>
            <h2 class="section-title">
                <?php if ($search): ?>
                    Results for “<?= htmlspecialchars($search) ?>”
                <?php elseif ($category): ?>
                    <?= htmlspecialchars($category) ?>
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h2>
            <p><?= count($products) ?> premium items · Updated daily</p>
        </div>

        <form class="filter-bar" method="GET" action="<?= BASE_URL ?>/index.php">
            <?php if ($search): ?>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <?php endif; ?>
            <select name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>"
                        <?= $category === $cat['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A–Z</option>
            </select>
            <?php if ($search || $category): ?>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline btn-sm">Clear filters</a>
            <?php endif; ?>
        </form>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="icon">🔍</div>
                <h3>No products found</h3>
                <p>Try a different search term or category.</p>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Browse All</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php render_product_card($product, false); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$extraJs = $isHomepage ? [BASE_URL . '/js/home.js'] : [];
include 'includes/footer.php';
?>
