<?php
// /includes/otp.php — Password reset OTP helpers

const OTP_LENGTH = 6;
const OTP_EXPIRY_MINUTES = 15;
const OTP_MAX_REQUESTS_PER_HOUR = 5;

function generate_otp_code(): string
{
    return str_pad((string) random_int(0, 999999), OTP_LENGTH, '0', STR_PAD_LEFT);
}

function otp_rate_limit_key(string $email): string
{
    return 'otp_requests_' . md5(strtolower(trim($email)));
}

function can_request_otp(string $email): bool
{
    $key = otp_rate_limit_key($email);
    $requests = $_SESSION[$key] ?? [];
    $hourAgo = time() - 3600;
    $requests = array_filter($requests, fn($t) => $t > $hourAgo);
    $_SESSION[$key] = $requests;
    return count($requests) < OTP_MAX_REQUESTS_PER_HOUR;
}

function record_otp_request(string $email): void
{
    $key = otp_rate_limit_key($email);
    $_SESSION[$key] = $_SESSION[$key] ?? [];
    $_SESSION[$key][] = time();
}

function store_password_otp(PDO $pdo, int $userId, string $plainOtp): void
{
    $pdo->prepare('DELETE FROM password_reset_otps WHERE user_id = ?')->execute([$userId]);

    $hash = password_hash($plainOtp, PASSWORD_BCRYPT);
    $expires = date('Y-m-d H:i:s', time() + OTP_EXPIRY_MINUTES * 60);

    $pdo->prepare(
        'INSERT INTO password_reset_otps (user_id, otp_hash, expires_at) VALUES (?,?,?)'
    )->execute([$userId, $hash, $expires]);
}

function verify_password_otp(PDO $pdo, int $userId, string $plainOtp): bool
{
    $stmt = $pdo->prepare(
        'SELECT * FROM password_reset_otps
         WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($plainOtp, $row['otp_hash'])) {
        return false;
    }

    $pdo->prepare('UPDATE password_reset_otps SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
    return true;
}

function send_password_reset_otp_email(string $email, string $name, string $otp): bool
{
    require_once __DIR__ . '/mailer.php';

    $app = app_config('app_name', 'ShopWave');
    $minutes = OTP_EXPIRY_MINUTES;

    $content = "
        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
        <p>Use this one-time code to reset your {$app} password:</p>
        <p style=\"font-size:32px;letter-spacing:8px;font-weight:bold;color:#0a0a0a;margin:24px 0;\">{$otp}</p>
        <p style=\"color:#6b6b6b;font-size:14px;\">This code expires in {$minutes} minutes. If you did not request this, you can ignore this email.</p>
    ";

    return send_app_email(
        $email,
        $name,
        "{$app} — Password reset code",
        email_template('Password reset code', $content),
        "Your {$app} password reset code is: {$otp}. It expires in {$minutes} minutes."
    );
}
