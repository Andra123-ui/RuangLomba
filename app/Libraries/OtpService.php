<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class OtpService
{
    public const TTL          = 600; // 10 menit
    public const COOLDOWN     = 60;  // detik antar pengiriman
    public const MAX_ATTEMPTS = 5;

    /** Sisa detik sebelum boleh kirim ulang (0 = boleh) */
    public function cooldownLeft(string $email, string $purpose): int
    {
        $last = $this->latest($email, $purpose);
        if (! $last) {
            return 0;
        }

        return max(0, self::COOLDOWN - (time() - strtotime($last['created_at'])));
    }

    /** Buat OTP baru. Return ['ok'=>bool, 'wait'=>int, 'code'=>string] */
    public function issue(string $email, string $purpose): array
    {
        $wait = $this->cooldownLeft($email, $purpose);
        if ($wait > 0) {
            return ['ok' => false, 'wait' => $wait];
        }

        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        // Matikan OTP lama yang masih aktif
        $db->table('email_otps')
            ->where('email', $email)->where('purpose', $purpose)->where('used_at', null)
            ->update(['used_at' => $now]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $db->table('email_otps')->insert([
            'email'      => $email,
            'purpose'    => $purpose,
            'code_hash'  => password_hash($code, PASSWORD_DEFAULT),
            'attempts'   => 0,
            'expires_at' => date('Y-m-d H:i:s', time() + self::TTL),
            'created_at' => $now,
        ]);

        if (ENVIRONMENT === 'development') {
            log_message('debug', "OTP [{$purpose}] {$email}: {$code}");
        }

        return ['ok' => true, 'wait' => self::COOLDOWN, 'code' => $code];
    }

    /** Return ['ok'=>bool, 'message'=>string] */
    public function verify(string $email, string $purpose, string $code): array
    {
        $row = $this->latest($email, $purpose);

        if (! $row || $row['used_at'] !== null) {
            return ['ok' => false, 'message' => 'Kode tidak ditemukan. Minta kode baru.'];
        }
        if (strtotime($row['expires_at']) < time()) {
            return ['ok' => false, 'message' => 'Kode sudah kedaluwarsa. Minta kode baru.'];
        }
        if ((int) $row['attempts'] >= self::MAX_ATTEMPTS) {
            return ['ok' => false, 'message' => 'Terlalu banyak percobaan salah. Minta kode baru.'];
        }

        $db = db_connect();

        if (! password_verify($code, $row['code_hash'])) {
            $db->table('email_otps')->where('id', $row['id'])->set('attempts', 'attempts + 1', false)->update();
            $left = self::MAX_ATTEMPTS - ((int) $row['attempts'] + 1);

            return ['ok' => false, 'message' => $left > 0
                ? "Kode salah. Sisa percobaan: {$left}."
                : 'Kode salah dan percobaan habis. Minta kode baru.'];
        }

        $db->table('email_otps')->where('id', $row['id'])->update(['used_at' => date('Y-m-d H:i:s')]);

        return ['ok' => true, 'message' => ''];
    }

    public function sendMail(string $email, string $name, string $code, string $purpose): bool
    {
        $config = config('Email');

        $mail = new PHPMailer(true);

        try {
            // ---- Konfigurasi SMTP ----
            $mail->isSMTP();
            $mail->Host       = $config->SMTPHost ?: 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $config->SMTPUser;
            $mail->Password   = $config->SMTPPass;
            $mail->Port       = $config->SMTPPort ?: 465;

            // Port 465 pakai SMTPS (implicit SSL), port 587 pakai STARTTLS
            if ((int) $mail->Port === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet  = 'UTF-8';
            $mail->Timeout  = 30;

            // ---- Pengirim & penerima ----
            $mail->setFrom($config->fromEmail, $config->fromName ?: 'RuangLomba');
            $mail->addAddress($email, $name);

            // ---- Konten ----
            $mail->isHTML(true);
            $mail->Subject = $purpose === 'reset'
                ? 'Kode reset password RuangLomba'
                : 'Kode verifikasi RuangLomba';
            $mail->Body = view('emails/otp', [
                'name'    => $name,
                'code'    => $code,
                'purpose' => $purpose,
                'minutes' => (int) (self::TTL / 60),
            ]);

            $mail->send();

            return true;
        } catch (MailException $e) {
            log_message('error', 'Gagal kirim OTP (PHPMailer): ' . $mail->ErrorInfo);

            return false;
        }
    }

    private function latest(string $email, string $purpose): ?array
    {
        return db_connect()->table('email_otps')
            ->where('email', $email)->where('purpose', $purpose)
            ->orderBy('id', 'DESC')->get(1)->getRowArray();
    }
}