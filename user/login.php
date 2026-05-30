<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email.';
    } elseif (empty($password)) {
        $errors[] = 'Password is required.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
                $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_BCRYPT), $user['id']]);
            }

            $redirect = $user['role'] === 'admin' ? BASE_URL . '/admin/index.php' : BASE_URL . '/index.php';
            redirect($redirect, 'Welcome back, ' . $user['name'] . '!', 'success');
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Sign In — ShopWave';
$authTitle = 'Welcome <em>Back</em>';
$authSubtitle = 'Sign in to access your orders, wishlist, and exclusive member offers.';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/auth_wrap_start.php'; ?>

        <h2 class="form-title">Sign In</h2>
        <p class="form-sub">Enter your credentials to continue.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/user/login.php">
            <?php csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required autofocus>
            </div>
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <label class="form-label" style="margin:0;">Password</label>
                    <a href="<?= BASE_URL ?>/user/forgot-password.php" style="font-size:13px;color:var(--gold);">Forgot?</a>
                </div>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="padding:14px;margin-top:8px;">Sign In</button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--ink-soft);">
            New to ShopWave? <a href="<?= BASE_URL ?>/user/register.php" style="color:var(--gold);font-weight:600;">Create account</a>
        </p>

<?php include '../includes/auth_wrap_end.php'; ?>
<?php include '../includes/footer.php'; ?>
