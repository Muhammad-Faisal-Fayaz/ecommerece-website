<?php
// /admin/products/index.php — Admin product list
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
require_once '../../includes/unsplash.php';
requireAdmin();

$flash    = getFlash();
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Manage Products — Admin';
?>
<?php include '../../includes/header.php'; ?>

<div class="admin-layout">
    <?php include '../_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Products</h1>
            <a href="<?= BASE_URL ?>/admin/products/create.php" class="btn btn-primary">+ Add Product</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php $imgUrl = getLocalProductImageUrl($product); ?>
                            <img src="<?= htmlspecialchars($imgUrl) ?>"
                                style="width:48px;height:48px;object-fit:cover;border-radius:4px;border:1px solid var(--light);">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <div style="font-size:12px;color:var(--mid);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= htmlspecialchars($product['description']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($product['category'] ?? '—') ?></td>
                        <td><strong>$<?= number_format($product['price'], 2) ?></strong></td>
                        <td>
                            <span style="color:<?= $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;font-weight:600;">
                                <?= $product['stock'] ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm" target="_blank">View</a>
                                <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-dark btn-sm">Edit</a>
                                <form method="POST" action="<?= BASE_URL ?>/admin/products/delete.php" style="display:inline;">
                                    <?php csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        data-confirm="Delete '<?= htmlspecialchars($product['name']) ?>'? This cannot be undone.">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--mid);padding:40px;">
                        No products yet. <a href="<?= BASE_URL ?>/admin/products/create.php" style="color:var(--accent);">Add your first product →</a>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
