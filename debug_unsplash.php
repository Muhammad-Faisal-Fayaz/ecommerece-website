<?php
// Debug page to verify Unsplash URLs are being generated
session_start();
require_once 'includes/db.php';
require_once 'includes/unsplash.php';

$pageTitle = 'Unsplash Debug - ShopWave';
?>
<?php include 'includes/header.php'; ?>

<div class="container section">
    <h1 class="section-title">🔍 Unsplash Image URL Debug</h1>
    <p style="color: var(--mid);">This page shows the Unsplash URLs being generated for each product.</p>

    <div style="background: var(--cream); padding: 20px; border-radius: 8px; margin: 24px 0;">
        <h3>Product Image URLs</h3>
        
        <?php
        $products = $pdo->query("SELECT id, name, category FROM products LIMIT 12")->fetchAll();
        
        if (empty($products)): ?>
            <p style="color: var(--mid);">No products found. <a href="seed_products.php">Seed the database</a> first.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
                <thead style="background: var(--black); color: white;">
                    <tr>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--light);">Product Name</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--light);">Category</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--light);">Image URL</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid var(--light);">Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php $url = getUnsplashImage($product['name'], $product['category']); ?>
                        <tr style="border-bottom: 1px solid var(--light);">
                            <td style="padding: 12px; border: 1px solid var(--light);">
                                <strong><?= htmlspecialchars($product['name']) ?></strong>
                            </td>
                            <td style="padding: 12px; border: 1px solid var(--light);">
                                <span style="background: var(--black); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= htmlspecialchars($product['category']) ?>
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid var(--light); font-size: 12px; word-break: break-all;">
                                <code><?= htmlspecialchars($url) ?></code>
                            </td>
                            <td style="padding: 12px; border: 1px solid var(--light); text-align: center;">
                                <img src="<?= htmlspecialchars($url) ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid var(--light);"
                                     alt="Product image"
                                     onerror="this.style.border='2px solid red'; this.title='Failed to load';">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 16px; border-radius: 4px; margin: 24px 0;">
        <h3 style="margin-top: 0; color: #2e7d32;">✓ How to Use This Debug Page</h3>
        <ul style="margin: 0; color: #555;">
            <li>Look at the <strong>Preview</strong> column - images should display correctly</li>
            <li>If images show with a red border, the URL is invalid</li>
            <li>If images load, the Unsplash integration is working!</li>
            <li>Check browser console (F12) for any network errors</li>
        </ul>
    </div>

    <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 16px; border-radius: 4px;">
        <h3 style="margin-top: 0;">ℹ️ Troubleshooting</h3>
        <ul style="margin: 0; color: #555;">
            <li><strong>All images show red border?</strong> → Check your internet connection</li>
            <li><strong>Some images fail?</strong> → Specific Unsplash URLs might be down, will retry on refresh</li>
            <li><strong>Still seeing alt text?</strong> → Clear browser cache (Ctrl+Shift+Del) and refresh</li>
        </ul>
    </div>

    <div style="margin-top: 24px; text-align: center;">
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">← Back to Store</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
