<?php
// Cache product images from Pexels to database
require_once 'includes/db.php';
require_once 'includes/unsplash.php';

echo "<h2>Caching Product Images...</h2>";

// Get all products without cached images
$stmt = $pdo->query("SELECT id, name, category, image FROM products WHERE image = 'default.jpg'");
$products = $stmt->fetchAll();

$cached = 0;
foreach ($products as $product) {
    // Get image from Pexels API
    $imageUrl = getImageFromPexelsAPI($product['name']);
    
    if (!$imageUrl) {
        $imageUrl = getImageFromPexelsAPI(getCategoryKeywords($product['category']));
    }
    
    if ($imageUrl) {
        // Save to database
        $updateStmt = $pdo->prepare("UPDATE products SET image = ? WHERE id = ?");
        $updateStmt->execute([$imageUrl, $product['id']]);
        $cached++;
        echo "✓ Cached image for: {$product['name']}<br>";
    }
}

echo "<p style='color:green;font-weight:bold;'>Cached $cached products!</p>";
echo "<p><a href='index.php'>← Back to Store</a></p>";
?>
