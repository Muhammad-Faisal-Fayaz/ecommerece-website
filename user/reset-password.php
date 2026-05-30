<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_user_id'])) {
    redirect(BASE_URL . '/user/forgot-password.php', 'Verify your code first.', 'info');
}

$errors = [];
$userId = (int) $_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    if (strlen($password) < 8) $errors[] = 'Min. 8 characters.';
    elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';
    else {
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_BCRYPT), $userId]);
        unset($_SESSION['reset_email'], $_SESSION['reset_verified'], $_SESSION['reset_user_id']);
        redirect(BASE_URL . '/user/login.php', 'Password updated. Sign in now.', 'success');
    }
}

$pageTitle = 'New Password — ShopWave';
$authTitle = 'New <em>Password</em>';
$authSubtitle = 'Choose a strong password for your account.';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/auth_wrap_start.php'; ?>

        <h2 class="form-title">Set Password</h2>
        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Update Password</button>
        </form>

<?php include '../includes/auth_wrap_end.php'; ?>
<?php include '../includes/footer.php'; ?>
