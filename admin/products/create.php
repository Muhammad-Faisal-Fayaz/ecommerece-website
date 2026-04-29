<?php
// /admin/products/create.php — Add new product
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

$errors = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price']       ?? '');
    $stock       = trim($_POST['stock']       ?? '0');
    $category    = trim($_POST['category']    ?? '');

    $values = compact('name', 'description', 'price', 'stock', 'category');

    if (strlen($name) < 2) $errors[] = 'Product name is required.';
    if (!is_numeric($price) || $price < 0) $errors[] = 'Valid price is required.';
    if (!is_numeric($stock) || $stock < 0)  $errors[] = 'Valid stock quantity is required.';

    // Handle image upload
    $imageName = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, GIF, WEBP images are allowed.';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image must be under 5MB.';
        } else {
            $imageName = uniqid('product_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../images/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $errors[] = 'Image upload failed. Check folder permissions.';
                $imageName = 'default.jpg';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $description, $price, $stock, $category, $imageName]);
        redirect(BASE_URL . '/admin/products/index.php', 'Product created successfully!', 'success');
    }
}

$pageTitle = 'Add Product — Admin';
$categories = ['Electronics', 'Clothing', 'Accessories', 'Kitchen', 'Sports', 'Books', 'Home', 'Other'];
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Add Product</h1>
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
                    <input type="text" name="name" class="form-control"
                        value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($values['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (USD) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0"
                            value="<?= htmlspecialchars($values['price'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock" class="form-control" min="0"
                            value="<?= htmlspecialchars($values['stock'] ?? '0') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">— Select Category —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($values['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" id="product_image_file" class="form-control" accept="image/*">
                    <p style="font-size:12px;color:var(--mid);margin-top:6px;">JPG, PNG, WEBP. Max 5MB.</p>
                    <img id="image_preview" style="display:none;max-width:200px;margin-top:12px;border-radius:6px;border:1px solid var(--light);">
                </div>

                <div style="display:flex;gap:16px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">Create Product</button>
                    <a href="<?= BASE_URL ?>/admin/products/index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
