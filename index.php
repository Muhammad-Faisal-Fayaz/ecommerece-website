<?php
// /index.php — Homepage with product listing, search, filter
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/unsplash.php';

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort']     ?? 'newest');

$params = [];
$sql = "SELECT * FROM products WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= match($sort) {
    'price_asc'  => " ORDER BY price ASC",
    'price_desc' => " ORDER BY price DESC",
    'name'       => " ORDER BY name ASC",
    default      => " ORDER BY created_at DESC",
};

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$cats = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category")->fetchAll();

$pageTitle = 'ShopWave — Premium Products';
?>
<?php include 'includes/header.php'; ?>

<?php if (!$search && !$category): ?>
<!-- Hero only on clean homepage -->
<div class="hero">
    <div class="container">
        <p class="hero-tag">✦ New Arrivals ✦</p>
        <h1>Discover <em>Exceptional</em><br>Products</h1>
        <p>Hand-picked for quality, designed for modern living.</p>
        <a href="#products" class="btn btn-primary">Explore Collection</a>
    </div>
</div>
<?php endif; ?>

<div class="container section" id="products">
    <div class="section-header">
        <h2 class="section-title">
            <?php if ($search): ?>
                Results for "<?= htmlspecialchars($search) ?>"
            <?php elseif ($category): ?>
                <?= htmlspecialchars($category) ?>
            <?php else: ?>
                All Products
            <?php endif; ?>
        </h2>
        <span class="section-sub"><?= count($products) ?> items</span>
    </div>

    <!-- Filter Bar -->
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
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
        </select>

        <?php if ($search || $category): ?>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline btn-sm">✕ Clear</a>
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
                <div class="product-card">
                    <div class="product-img">
                        <?php
                        // Use Unsplash image directly
                        $unsplashUrl = getUnsplashImage($product['name'], $product['category']);
                        $imgPath = 'images/products/' . $product['image'];
                        
                        if ($product['image'] && file_exists($imgPath)): ?>
                            <img src="<?= BASE_URL ?>/<?= $imgPath ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($unsplashUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                        <?php endif; ?>
                        <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                            <span class="product-badge">Low Stock</span>
                        <?php elseif ($product['stock'] === 0): ?>
                            <span class="product-badge" style="background:#c0392b;color:#fff;">Sold Out</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-body">
                        <p class="product-category"><?= htmlspecialchars($product['category'] ?? '') ?></p>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="product-footer">
                            <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                            <div style="display:flex;gap:8px;">
                                <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">View</a>
                                <?php if ($product['stock'] > 0): ?>
                                    <form action="<?= BASE_URL ?>/cart_action.php" method="POST">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
