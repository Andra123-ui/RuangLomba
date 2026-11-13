<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->protocol   = env('email.default.protocol', 'smtp');
        $this->SMTPHost   = env('email.default.SMTPHost', 'smtp.gmail.com');
        $this->SMTPUser   = env('email.default.SMTPUser', '');
        $this->SMTPPass   = env('email.default.SMTPPass', '');
        $this->SMTPPort   = (int) env('email.default.SMTPPort', 465);
        $this->SMTPCrypto = env('email.default.SMTPCrypto', 'ssl');
        $this->fromEmail  = env('email.default.fromEmail', '');
        $this->fromName   = env('email.default.fromName', 'RuangLomba');
        $this->mailType   = env('email.default.mailType', 'html');
        $this->charset    = env('email.default.charset', 'UTF-8');
        $this->newline    = env('email.default.newline', "\r\n");
        $this->CRLF       = env('email.default.CRLF', "\r\n");
    }

    // Properti lainnya tetap dipertahankan (tidak perlu diubah)
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';
    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'mail';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = '';
    public string $SMTPAuthMethod = 'login';
    public string $SMTPUser = '';
    public string $SMTPPass = '';
    public int    $SMTPPort = 25;
    public int $SMTPTimeout = 30;
    public bool   $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls';
    public bool   $wordWrap = true;
    public int    $wrapChars = 76;
    public string $mailType = 'text';
    public string $charset = 'UTF-8';
    public bool   $validate = false;
    public int    $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool   $BCCBatchMode = false;
    public int    $BCCBatchSize = 200;
    public bool   $DSN = false;
}