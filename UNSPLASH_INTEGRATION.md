# Unsplash Integration Guide

Your e-commerce store now integrates with **Unsplash** to automatically fetch beautiful, high-quality product images without storing them locally.

## 📋 Overview

Instead of uploading and storing product images locally, the system now:
- Fetches relevant images from Unsplash based on product name and category
- Displays different images for each product
- Reduces storage requirements
- Provides professional, royalty-free images

## 🎯 How It Works

### File: `includes/unsplash.php`

This helper file contains functions that generate Unsplash image URLs:

```php
// Get image for a product
$imageUrl = getUnsplashImage($productName, $category);
```

**Functions:**

| Function | Purpose |
|----------|---------|
| `getUnsplashImage($name, $category)` | Get Unsplash image URL for a product |
| `buildSearchKeywords($name, $category)` | Build smart search keywords |
| `fetchUnsplashImage($keywords)` | Fetch image from Unsplash API |
| `getDefaultUnsplashImage($category)` | Get fallback image for category |
| `generateUnsplashUrl($keywords, $width, $height)` | Generate custom-sized URL |
| `getRandomUnsplashVariation($keywords, $width, $height)` | Get random image variation |

## 🔄 Integration Points

### 1. **Homepage & Product Listing** (`index.php`)
```php
$unsplashUrl = getUnsplashImage($product['name'], $product['category']);
?>
<img src="<?= htmlspecialchars($unsplashUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
```

### 2. **Product Detail Page** (`product.php`)
```php
$unsplashUrl = getUnsplashImage($product['name'], $product['category']);
```

### 3. **Admin Panel** (`admin/products/index.php`)
```php
$unsplashUrl = getUnsplashImage($product['name'], $product['category']);
```

## 🎨 Category Mapping

The system maps product categories to relevant Unsplash search terms:

| Category | Search Terms |
|----------|--------------|
| Electronics | gadgets, technology |
| Clothing | fashion, clothes, apparel |
| Accessories | accessories, style |
| Kitchen | kitchen, cookware, cooking |
| Sports | sports, fitness, athletic |
| Books | books, reading, library |
| Home | home, interior, decoration |
| Other | products, items |

## 🖼️ Image URLs

**Unsplash Source Format:**
```
https://source.unsplash.com/{WIDTH}x{HEIGHT}/?{KEYWORDS}
```

**Examples:**
- `https://source.unsplash.com/600x600/?wireless headphones`
- `https://source.unsplash.com/600x600/?jeans clothing`
- `https://source.unsplash.com/600x600/?kitchen cookware`

**Default URLs** (used when search fails):
- Electronics: `https://images.unsplash.com/photo-1505228395891-9a51fb63ce97?w=600&h=600&fit=crop`
- Clothing: `https://images.unsplash.com/photo-1505298346881-b72b27e84530?w=600&h=600&fit=crop`
- Accessories: `https://images.unsplash.com/photo-1483389127117-b6a2102724ae?w=600&h=600&fit=crop`
- Kitchen: `https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=600&h=600&fit=crop`
- Sports: `https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&h=600&fit=crop`
- Books: `https://images.unsplash.com/photo-1507842217343-583f20270319?w=600&h=600&fit=crop`
- Home: `https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop`
- Other: `https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&h=600&fit=crop`

## ⚙️ Configuration

To customize image dimensions or search behavior, edit `includes/unsplash.php`:

```php
// Change default dimensions
function getUnsplashImage($productName, $category) {
    // Modify width/height here
    $imageUrl = fetchUnsplashImage($keywords); // Currently 600x600
    return $imageUrl ?: getDefaultUnsplashImage($category);
}
```

## 🛒 Fallback System

The system uses a **graceful fallback**:

1. **First**: Try to load local image if it exists
2. **Second**: Fetch from Unsplash based on product name and category
3. **Third**: Use default category image if Unsplash request fails

This ensures images always display, even if Unsplash is temporarily unavailable.

## 📝 Notes

- ✓ **No API Key Required**: Uses Unsplash's free public API
- ✓ **Automatic**: Images are fetched dynamically per request
- ✓ **Random Variation**: Different images shown on page refreshes
- ✓ **Royalty-Free**: All images are free to use commercially
- ✓ **Responsive**: Images scale to any size needed

## 🚀 Using Custom Images

You can still upload custom local images:

1. Go to **Admin Panel** → **Products**
2. Create or Edit a product
3. Upload a custom image file
4. The system will use your image instead of Unsplash

**Priority Order:**
- Local uploaded image (if exists)
- Unsplash image (if local doesn't exist)

## 📊 Performance

- Images are served directly from Unsplash's CDN
- No additional server resources used for image storage
- Fast loading times with lazy loading
- Reduced database storage requirements

## 🔗 Unsplash Attribution

While not required, you can optionally attribute Unsplash in your footer:

```html
<a href="https://unsplash.com">Photos by Unsplash</a>
```

## 🐛 Troubleshooting

**Issue**: Images not loading
- **Solution**: Check internet connection; Unsplash may be temporarily unavailable. Default images will display.

**Issue**: Same image showing for all products
- **Solution**: This is normal for different products with similar names. Refresh the page to see different random variations.

**Issue**: Wrong product category image
- **Solution**: Verify the product's category is correctly set in the admin panel.

---

**Updated**: Your e-commerce store now has professional Unsplash images across all categories! 🎉
