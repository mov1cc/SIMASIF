<?php

namespace App\Services;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = require BASE_PATH . '/app/Config/mail.php';
    }

    /**
     * Kirim email reset password. Mengembalikan true kalau terkirim.
     * Error SMTP dicatat di storage/logs/error.log, tidak dilempar ke pengguna.
     */
    public function kirimResetPassword(string $email, string $nama, string $link, int $berlakuMenit): bool
    {
        $namaAman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        $linkAman = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

        $html = "<p>Halo {$namaAman},</p>"
            . "<p>Kami menerima permintaan reset password untuk akun SIMASIF Anda. "
            . "Klik tautan berikut untuk membuat password baru "
            . "(berlaku {$berlakuMenit} menit):</p>"
            . "<p><a href=\"{$linkAman}\">{$linkAman}</a></p>"
            . "<p>Jika Anda tidak merasa meminta ini, abaikan email ini. "
            . "Password Anda tidak akan berubah.</p>";

        $teks = "Halo {$nama},\n\n"
            . "Buka tautan berikut untuk membuat password baru (berlaku {$berlakuMenit} menit):\n"
            . "{$link}\n\n"
            . "Jika Anda tidak merasa meminta ini, abaikan email ini.";

        return $this->kirim($email, $nama, 'Reset Password SIMASIF', $html, $teks);
    }

    private function kirim(string $ke, string $namaKe, string $subjek, string $html, string $teks): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = (string) $this->cfg['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = (string) $this->cfg['username'];
            $mail->Password   = (string) $this->cfg['password'];
            $mail->SMTPSecure = $this->cfg['encryption'] === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) $this->cfg['port'];
            $mail->Timeout    = 15;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom((string) $this->cfg['from_address'], (string) $this->cfg['from_name']);
            $mail->addAddress($ke, $namaKe);

            $mail->isHTML(true);
            $mail->Subject = $subjek;
            $mail->Body    = $html;
            $mail->AltBody = $teks;

            $mail->send();

            return true;
        } catch (MailException $e) {
            error_log(
                '[' . date('Y-m-d H:i:s') . '] MailService: ' . $mail->ErrorInfo . PHP_EOL,
                3,
                BASE_PATH . '/storage/logs/error.log'
            );

            return false;
        }
    }
}