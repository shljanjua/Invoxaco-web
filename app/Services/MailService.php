<?php

namespace App\Services;

use App\Core\Logger;
use App\Models\SmtpSetting;
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $smtp = SmtpSetting::active();
        $fallback = require __DIR__ . '/../Config/mail.php';

        $host = $smtp['host'] ?? '' ?: $fallback['host'];
        $port = $smtp['port'] ?? '' ?: $fallback['port'];
        $encryption = $smtp['encryption'] ?? '' ?: $fallback['encryption'];
        $username = $smtp['username'] ?? '' ?: $fallback['username'];
        $password = $smtp['password'] ?? '' ?: $fallback['password'];
        $fromAddress = $smtp['from_address'] ?? '' ?: $fallback['from_address'];
        $fromName = $smtp['from_name'] ?? '' ?: $fallback['from_name'];

        $this->mailer->isSMTP();
        // Fail fast instead of hanging a user's request for minutes when the
        // mail server is slow or unreachable (default PHPMailer timeout is 300s).
        $this->mailer->Timeout = 12;
        $this->mailer->Host = $host;
        $this->mailer->Port = (int) $port;
        $this->mailer->SMTPAuth = !empty($username);
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->SMTPSecure = $encryption ?: PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->setFrom($fromAddress, $fromName);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, array $attachments = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);

            foreach ($attachments as $attachment) {
                $this->mailer->addStringAttachment($attachment['content'], $attachment['name'], 'base64', $attachment['mime'] ?? 'application/octet-stream');
            }

            return $this->mailer->send();
        } catch (MailException $e) {
            Logger::error('Mail send failed: ' . $e->getMessage());

            return false;
        }
    }
}
