<?php
// /includes/mailer.php — PHPMailer wrapper

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_app_email(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
{
    if (!config_is_ready('mail')) {
        error_log('ShopWave mail: config/local.php is missing or incomplete.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = app_config('mail_host', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = app_config('mail_username');
        $mail->Password   = app_config('mail_password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) app_config('mail_port', 587);

        $mail->setFrom(
            app_config('mail_from_email', app_config('mail_username')),
            app_config('mail_from_name', app_config('app_name', 'ShopWave'))
        );
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('ShopWave mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function email_template(string $title, string $contentHtml): string
{
    $app = htmlspecialchars(app_config('app_name', 'ShopWave'));
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f1eb;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1eb;padding:32px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#fafaf8;border:1px solid #d8d4cb;border-radius:8px;overflow:hidden;">
        <tr><td style="background:#0a0a0a;color:#c8a96e;padding:24px 32px;font-size:22px;letter-spacing:2px;">{$app}</td></tr>
        <tr><td style="padding:32px;color:#2a2a2a;">
          <h2 style="margin:0 0 16px;font-size:20px;color:#0a0a0a;">{$title}</h2>
          {$contentHtml}
        </td></tr>
        <tr><td style="padding:16px 32px;background:#f4f1eb;font-size:12px;color:#6b6b6b;">
          &copy; {$app}. This is an automated message — please do not reply.
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}
