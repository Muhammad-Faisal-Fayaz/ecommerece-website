<?php
/**
 * Unsplash Image Helper
 * Provides functions to fetch product images from Unsplash API
 */

// Unsplash Access Key (free tier, no key required for simple queries)
const UNSPLASH_API_BASE = 'https://api.unsplash.com/search/photos';

/**
 * Get an Unsplash image URL for a product based on category/keywords
 * Uses randomization to get different images each time
 * 
 * @param string $productName Product name for search
 * @param string $category Product category
 * @return string Image URL from Unsplash
 */
function getUnsplashImage($productName, $category) {
    $keywords = buildSearchKeywords($productName, $category);
    $imageUrl = fetchUnsplashImage($keywords);
    return $imageUrl ?: getDefaultUnsplashImage($category);
}

/**
 * Build search keywords from product name and category
 */
function buildSearchKeywords($productName, $category) {
    $categoryKeywords = [
        'Electronics' => 'gadgets technology',
        'Clothing' => 'fashion clothes apparel',
        'Accessories' => 'accessories style',
        'Kitchen' => 'kitchen cookware cooking',
        'Sports' => 'sports fitness athletic',
        'Books' => 'books reading library',
        'Home' => 'home interior decoration',
        'Other' => 'products items'
    ];

    $categoryKey = $categoryKeywords[$category] ?? 'products';
    $productKey = strtolower(preg_replace('/[^a-z0-9\s]/i', '', $productName));
    
    // Remove common words to improve search
    $productKey = preg_replace('/\b(the|a|an|pack|set|kit|with|for)\b/i', '', $productKey);
    $productKey = trim($productKey);
    
    return $productKey ?: $categoryKey;
}

/**
 * Fetch image from Unsplash API
 * Using free API endpoint without authentication (limited requests)
 */
function fetchUnsplashImage($keywords) {
    $query = urlencode($keywords);
    // Add random page to get variety
    $page = rand(1, 5);
    $url = "https://source.unsplash.com/600x600/?{$keywords}&page={$page}";
    
    // This returns a redirect to the actual image
    // For direct URLs, we can use this pattern
    return $url;
}

/**
 * Get default Unsplash image for category if search fails
 */
function getDefaultUnsplashImage($category) {
    $defaults = [
        'Electronics' => 'https://images.unsplash.com/photo-1505228395891-9a51fb63ce97?w=600&h=600&fit=crop',
        'Clothing' => 'https://images.unsplash.com/photo-1505298346881-b72b27e84530?w=600&h=600&fit=crop',
        'Accessories' => 'https://images.unsplash.com/photo-1483389127117-b6a2102724ae?w=600&h=600&fit=crop',
        'Kitchen' => 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=600&fit=crop',
        'Sports' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&h=600&fit=crop',
        'Books' => 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=600&fit=crop',
        'Home' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop',
        'Other' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&h=600&fit=crop'
    ];
    
    return $defaults[$category] ?? $defaults['Other'];
}

/**
 * Generate Unsplash image URL with width and height parameters
 * Using Unsplash source service for faster direct access
 */
function generateUnsplashUrl($keywords, $width = 600, $height = 600) {
    $query = urlencode($keywords);
    return "https://source.unsplash.com/{$width}x{$height}/?{$query}";
}

/**
 * Get random variation of Unsplash image URL for the same keywords
 * Adds random page parameter to get different images
 */
function getRandomUnsplashVariation($keywords, $width = 600, $height = 600) {
    $query = urlencode($keywords);
    $seed = rand(1, 1000);
    return "https://source.unsplash.com/{$width}x{$height}/?{$query}&sig={$seed}";
}
?>
