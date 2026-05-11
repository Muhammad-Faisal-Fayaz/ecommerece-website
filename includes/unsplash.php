<?php
/**
 * Image Helper for Product Images
 * Uses Pexels API for relevant product images
 * Caches downloaded images locally for fast subsequent loads.
 */

// Pexels API for relevant images
define('PEXELS_API_KEY', 'q8KsIQNsenOVWbKuf748m0O9EsyaEi65wdnTRtTHi566SXP1nYSQgP39');
define('USE_PEXELS_API', true); // Set to true if you have an API key

/**
 * Resolve the best image URL for a product.
 * This caches the remote image locally to speed up future page loads.
 */
function resolveProductImageUrl(array $product) {
    if (!empty($product['image']) && filter_var($product['image'], FILTER_VALIDATE_URL)) {
        return $product['image'];
    }

    $localFile = $product['image'] ?? '';
    $localPath = __DIR__ . '/../images/products/' . basename($localFile);

    if (!empty($localFile) && file_exists($localPath)) {
        return BASE_URL . '/images/products/' . rawurlencode(basename($localFile));
    }

    $remoteUrl = getProductRemoteImageUrl($product['name'], $product['category']);
    if ($remoteUrl) {
        $savedFile = downloadRemoteImage($remoteUrl, $product['id']);
        if ($savedFile) {
            if (isset($GLOBALS['pdo'])) {
                $stmt = $GLOBALS['pdo']->prepare('UPDATE products SET image = ? WHERE id = ?');
                $stmt->execute([$savedFile, $product['id']]);
            }
            return BASE_URL . '/images/products/' . rawurlencode($savedFile);
        }
        return $remoteUrl;
    }

    return BASE_URL . '/images/products/default.jpg';
}

/**
 * Retrieve a remote image URL for a product using API or fallback.
 */
function getProductRemoteImageUrl($productName, $category) {
    if (USE_PEXELS_API && PEXELS_API_KEY !== 'YOUR_PEXELS_API_KEY_HERE') {
        $url = getImageFromPexelsAPI($productName);
        if ($url) {
            return $url;
        }

        $categoryKeyword = getCategoryKeywords($category);
        $url = getImageFromPexelsAPI($categoryKeyword);
        if ($url) {
            return $url;
        }
    }

    $searchTerm = getSmartSearchTerm($productName, $category);
    return getPicsumImage($searchTerm);
}

/**
 * Download a remote image into the local product images folder.
 */
function downloadRemoteImage($url, $productId) {
    $imageData = @file_get_contents($url);
    if (!$imageData) {
        return null;
    }

    $extension = 'jpg';
    $info = @getimagesizefromstring($imageData);
    if ($info && isset($info['mime'])) {
        switch ($info['mime']) {
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
            default:
                $extension = 'jpg';
        }
    } else {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        if (!empty($pathInfo['extension'])) {
            $ext = strtolower($pathInfo['extension']);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $extension = $ext === 'jpeg' ? 'jpg' : $ext;
            }
        }
    }

    $filename = "product_{$productId}.{$extension}";
    $destination = __DIR__ . '/../images/products/' . $filename;
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }

    if (@file_put_contents($destination, $imageData) === false) {
        return null;
    }

    return $filename;
}

/**
 * Get smart search term for the product.
 */
function getSmartSearchTerm($productName, $category) {
    $term = strtolower(trim($productName));
    $term = preg_replace('/[^a-z0-9\s]/i', ' ', $term);
    $term = preg_replace('/\s+/', ' ', $term);

    $commonWords = ['pack', 'set', 'kit', 'piece', 'pcs', 'qty', 'quantity', 'the', 'a', 'an'];
    foreach ($commonWords as $word) {
        $term = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', '', $term);
    }
    $term = trim(preg_replace('/\s+/', ' ', $term));

    $categoryBoost = [
        'Electronics' => 'tech gadget',
        'Clothing' => 'fashion apparel',
        'Accessories' => 'luxury style',
        'Kitchen' => 'cookware utensil',
        'Sports' => 'fitness equipment',
        'Books' => 'literature reading',
        'Home' => 'interior decor',
        'Other' => 'product item'
    ];

    $boost = $categoryBoost[$category] ?? 'product';
    if (!empty($term) && strlen($term) > 2) {
        return $term . ' ' . $boost;
    }

    return getCategoryKeywords($category) . ' ' . $boost;
}

/**
 * Get a fallback Picsum image URL.
 */
function getPicsumImage($searchTerm) {
    $query = urlencode($searchTerm);
    return "https://picsum.photos/600/600?random={$query}";
}

/**
 * Fetch image from Pexels API with search query.
 */
function getImageFromPexelsAPI($query) {
    if (empty(trim($query)) || PEXELS_API_KEY === 'YOUR_PEXELS_API_KEY_HERE') {
        return null;
    }

    $url = 'https://api.pexels.com/v1/search?query=' . urlencode($query) . '&per_page=1&orientation=square';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Authorization: ' . PEXELS_API_KEY . "\r\n",
            'timeout' => 5
        ]
    ]);

    try {
        $response = @file_get_contents($url, false, $context);
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['photos']) && !empty($data['photos'])) {
                $photo = $data['photos'][0];
                return $photo['src']['medium'];
            }
        }
    } catch (Exception $e) {
        // Fallback
    }

    return null;
}

/**
 * Get default search keywords by category.
 */
function getCategoryKeywords($category) {
    $categoryKeywords = [
        'Electronics' => 'electronics gadgets',
        'Clothing' => 'clothing shirt',
        'Accessories' => 'watch jewelry',
        'Kitchen' => 'kitchen cookware',
        'Sports' => 'sports equipment',
        'Books' => 'books reading',
        'Home' => 'home decor',
        'Other' => 'products'
    ];

    return $categoryKeywords[$category] ?? 'products';
}

/**
 * Get a default image per category.
 */
function getDefaultUnsplashImage($category) {
    $defaults = [
        'Electronics' => 'https://images.unsplash.com/photo-1505228395891-9a51fb63ce97?w=600&h=600&fit=crop&q=80',
        'Clothing' => 'https://images.unsplash.com/photo-1505298346881-b72b27e84530?w=600&h=600&fit=crop&q=80',
        'Accessories' => 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?w=600&h=600&fit=crop&q=80',
        'Kitchen' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop&q=80',
        'Sports' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&h=600&fit=crop&q=80',
        'Books' => 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=600&fit=crop&q=80',
        'Home' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop&q=80',
        'Other' => 'https://images.unsplash.com/photo-1505228395891-9a51fb63ce97?w=600&h=600&fit=crop&q=80'
    ];

    return $defaults[$category] ?? $defaults['Other'];
}

/**
 * Generate Unsplash image URL with custom dimensions.
 */
function generateUnsplashUrl($keywords, $width = 600, $height = 600) {
    $query = urlencode(trim($keywords));
    return "https://source.unsplash.com/{$width}x{$height}/?{$query}";
}

/**
 * Get a different image variation of the same keywords.
 */
function getRandomUnsplashVariation($keywords, $width = 600, $height = 600) {
    return generateUnsplashUrl($keywords, $width, $height);
}
