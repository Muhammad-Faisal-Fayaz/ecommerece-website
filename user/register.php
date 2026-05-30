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
$values = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $values = compact('name', 'email');

    if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?,?,?)')
                ->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
            redirect(BASE_URL . '/user/login.php', 'Account created! Please sign in.', 'success');
        }
    }
}

$pageTitle = 'Join ShopWave';
$authTitle = 'Join the <em>Wave</em>';
$authSubtitle = 'Create your account and unlock a shopping experience unlike any other.';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/auth_wrap_start.php'; ?>

        <h2 class="form-title">Create Account</h2>
        <p class="form-sub">Start your journey with ShopWave today.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/user/register.php">
            <?php csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($values['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="padding:14px;">Create Account</button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--ink-soft);">
            Already a member? <a href="<?= BASE_URL ?>/user/login.php" style="color:var(--gold);font-weight:600;">Sign in</a>
        </p>

<?php include '../includes/auth_wrap_end.php'; ?>
<?php include '../includes/footer.php'; ?>
