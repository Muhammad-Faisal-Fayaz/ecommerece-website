<?php
// /user/register.php — User registration
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$errors = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';

    $values = compact('name', 'email');

    if (strlen($name) < 2)    $errors[] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)")->execute([$name, $email, $hashed]);
            redirect('/user/login.php', 'Account created! Please login.', 'success');
        }
    }
}

$pageTitle = 'Create Account — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<div class="page-auth">
    <div class="form-wrap">
        <h2 class="form-title">Create Account</h2>
        <p class="form-sub">Join ShopWave and start shopping today.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-error">
                <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/user/register.php">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control"
                    value="<?= htmlspecialchars($values['name'] ?? '') ?>"
                    placeholder="John Doe" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($values['email'] ?? '') ?>"
                    placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Min. 8 characters" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm" class="form-control"
                    placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">
                Create Account
            </button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--mid);">
            Already have an account? <a href="<?= BASE_URL ?>/user/login.php" style="color:var(--accent);">Login →</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
