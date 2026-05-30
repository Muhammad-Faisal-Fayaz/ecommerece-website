<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../includes/otp.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
if (empty($_SESSION['reset_email'])) {
    redirect(BASE_URL . '/user/forgot-password.php', 'Enter your email first.', 'info');
}

$errors = [];
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $otp = preg_replace('/\D/', '', $_POST['otp'] ?? '');
    if (strlen($otp) !== OTP_LENGTH) {
        $errors[] = 'Enter the 6-digit code.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !verify_password_otp($pdo, (int) $user['id'], $otp)) {
            $errors[] = 'Invalid or expired code.';
        } else {
            $_SESSION['reset_user_id'] = (int) $user['id'];
            $_SESSION['reset_verified'] = true;
            redirect(BASE_URL . '/user/reset-password.php', 'Code verified.', 'success');
        }
    }
}

$pageTitle = 'Verify Code — ShopWave';
$authTitle = 'Verify <em>Identity</em>';
$authSubtitle = 'Code sent to ' . $email;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/auth_wrap_start.php'; ?>

        <h2 class="form-title">Enter Code</h2>
        <p class="form-sub">6-digit verification code</p>
        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-group">
                <input type="text" name="otp" class="form-control otp-input" inputmode="numeric" maxlength="6" placeholder="000000" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Verify</button>
        </form>
        <p style="text-align:center;margin-top:20px;"><a href="<?= BASE_URL ?>/user/forgot-password.php" style="color:var(--gold);">Resend code</a></p>

<?php include '../includes/auth_wrap_end.php'; ?>
<?php include '../includes/footer.php'; ?>
