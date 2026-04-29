<?php
// /admin/products/edit.php — Edit product
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirect(BASE_URL . '/admin/products/index.php');

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) redirect(BASE_URL . '/admin/products/index.php', 'Product not found.', 'error');

$errors = [];
$flash  = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price']       ?? '');
    $stock       = trim($_POST['stock']       ?? '0');
    $category    = trim($_POST['category']    ?? '');

    if (strlen($name) < 2) $errors[] = 'Product name is required.';
    if (!is_numeric($price) || $price < 0) $errors[] = 'Valid price is required.';
    if (!is_numeric($stock) || $stock < 0)  $errors[] = 'Valid stock is required.';

    $imageName = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, GIF, WEBP images are allowed.';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image must be under 5MB.';
        } else {
            $newName   = uniqid('product_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../images/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName)) {
                // Delete old image
                $old = $uploadDir . $product['image'];
                if ($product['image'] !== 'default.jpg' && file_exists($old)) unlink($old);
                $imageName = $newName;
            } else {
                $errors[] = 'Image upload failed.';
            }
        }
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image=? WHERE id=?")
            ->execute([$name, $description, $price, $stock, $category, $imageName, $id]);
        redirect(BASE_URL . '/admin/products/index.php', 'Product updated successfully!', 'success');
    }

    // Re-populate from POST on error
    $product = array_merge($product, compact('name', 'description', 'price', 'stock', 'category'));
}

$pageTitle = 'Edit Product — Admin';
$categories = ['Electronics', 'Clothing', 'Accessories', 'Kitchen', 'Sports', 'Books', 'Home', 'Other'];
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Edit Product</h1>
            <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline">← Back</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;padding:40px;max-width:700px;">
            <form method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>

                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (USD) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock" class="form-control" min="0" value="<?= htmlspecialchars($product['stock']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">— Select Category —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $product['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <?php $imgPath = '../../images/products/' . $product['image']; ?>
                    <?php if ($product['image'] && file_exists($imgPath)): ?>
                        <img src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($product['image']) ?>"
                            style="max-width:150px;border-radius:6px;margin-bottom:12px;display:block;border:1px solid var(--light);">
                    <?php endif; ?>
                    <input type="file" name="image" id="product_image_file" class="form-control" accept="image/*">
                    <p style="font-size:12px;color:var(--mid);margin-top:6px;">Leave empty to keep current image.</p>
                    <img id="image_preview" style="display:none;max-width:200px;margin-top:12px;border-radius:6px;">
                </div>

                <div style="display:flex;gap:16px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
