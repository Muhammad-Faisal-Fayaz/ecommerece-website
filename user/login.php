<?php
// /user/login.php — User login
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$errors = [];
$email  = '';
$flash  = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email.';
    } elseif (empty($password)) {
        $errors[] = 'Password is required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            // Update password hash if needed
            if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
                $new = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new, $user['id']]);
            }

            $redirect = $user['role'] === 'admin' ? BASE_URL . '/admin/index.php' : BASE_URL . '/index.php';
            redirect($redirect, 'Welcome back, ' . $user['name'] . '!', 'success');
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<div class="page-auth">
    <div class="form-wrap">
        <h2 class="form-title">Welcome Back</h2>
        <p class="form-sub">Sign in to your ShopWave account.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-error">
                <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/user/login.php">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="you@example.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">
                Sign In
            </button>
        </form>

        <div style="margin-top:20px;padding:16px;background:var(--cream);border-radius:6px;font-size:13px;color:var(--mid);">
            <strong>Demo Admin:</strong> admin@shopwave.com / admin123
        </div>

        <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--mid);">
            New here? <a href="<?= BASE_URL ?>/user/register.php" style="color:var(--accent);">Create an account →</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
