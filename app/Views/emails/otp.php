<?php $isReset = ($purpose ?? '') === 'reset'; ?>
<!DOCTYPE html>
<html lang="id">

<body style="margin:0;padding:0;background:#F3F6FF;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:480px;background:#ffffff;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td style="background:#00246B;padding:24px 32px;color:#ffffff;font-size:22px;font-weight:bold;">
                            RuangLomba<span style="color:#00D9FF;">.</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;color:#111111;">
                            <p style="margin:0 0 8px;font-size:16px;">Halo <?= esc($name) ?>,</p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6B7280;line-height:1.6;">
                                <?= $isReset ? 'Gunakan kode berikut untuk membuat password baru:' : 'Gunakan kode berikut untuk memverifikasi emailmu:' ?>
                            </p>
                            <div style="text-align:center;margin:0 0 24px;">
                                <span
                                    style="display:inline-block;font-size:34px;letter-spacing:10px;font-weight:bold;color:#2563FF;background:#EEF3FF;padding:16px 24px;border-radius:14px;">
                                    <?= esc($code) ?>
                                </span>
                            </div>
                            <p style="margin:0 0 8px;font-size:13px;color:#6B7280;line-height:1.6;">
                                Kode berlaku <?= (int) $minutes ?> menit. Jangan bagikan kode ini kepada siapa pun,
                                termasuk pihak yang mengaku dari RuangLomba.
                            </p>
                            <p style="margin:0;font-size:13px;color:#6B7280;line-height:1.6;">
                                Kalau kamu tidak merasa memintanya, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>