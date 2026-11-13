<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <div class="otp-badge"><i class="fa-solid fa-shield-halved"></i></div>
    <h1 class="auth-title">Reset password</h1>
    <p class="auth-sub">Masukkan kode yang dikirim ke <strong class="text-dark"><?= esc($email) ?></strong>.</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (! empty($errors)): ?>
<div class="alert alert-danger" role="alert">
    <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div id="otpAlert" class="alert alert-danger d-none" role="alert"></div>
<div id="otpSuccess" class="alert alert-success d-none" role="alert"></div>

<!-- ============ LANGKAH 1: OTP (disembunyikan kalau sudah verified) ============ -->
<div id="otpSection" class="<?= $verified ? 'd-none' : '' ?>">
    <form id="otpForm" action="<?= base_url('reset-password') ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <div class="mb-4">
            <label class="form-label">Kode OTP</label>
            <?= view('auth/_otp') ?>
        </div>

        <button type="submit" class="btn btn-primary w-100" id="otpBtn">
            <span class="spin"></span>Verifikasi kode
        </button>
    </form>
</div>

<!-- ============ LANGKAH 2: PASSWORD BARU (muncul setelah OTP benar) ============ -->
<div id="passwordSection" class="<?= $verified ? '' : 'd-none' ?>">
    <hr class="my-4">
    <h2 class="auth-title" style="font-size:1.1rem">Buat password baru</h2>

    <form id="passwordForm" action="<?= base_url('reset-password/simpan') ?>" method="post" novalidate data-loading>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="password" class="form-label">Password baru</label>
            <div class="auth-pass">
                <input type="password" class="form-control auth-input" id="password" name="password"
                    autocomplete="new-password" minlength="8" required>
                <button type="button" class="auth-pass-toggle" data-toggle-pass="password"
                    aria-label="Tampilkan password" aria-pressed="false"><i class="fa-regular fa-eye"
                        aria-hidden="true"></i></button>
            </div>
            <div class="form-text">Minimal 8 karakter.</div>
        </div>

        <div class="mb-4">
            <label for="password_confirm" class="form-label">Ulangi password baru</label>
            <div class="auth-pass">
                <input type="password" class="form-control auth-input" id="password_confirm" name="password_confirm"
                    autocomplete="new-password" required>
                <button type="button" class="auth-pass-toggle" data-toggle-pass="password_confirm"
                    aria-label="Tampilkan password" aria-pressed="false"><i class="fa-regular fa-eye"
                        aria-hidden="true"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100"><span class="spin"></span>Ubah password</button>
    </form>
</div>

<!-- ============ RESEND (disembunyikan kalau sudah verified) ============ -->
<div id="resendSection" class="<?= $verified ? 'd-none' : '' ?>">
    <?= view('auth/_resend', ['action' => 'reset-password/kirim-ulang', 'wait' => $wait]) ?>
</div>

<p class="auth-switch"><a href="<?= base_url('masuk') ?>"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke halaman
        masuk</a></p>

<script>
(function() {
    const otpSection = document.getElementById('otpSection');
    const otpForm = document.getElementById('otpForm');
    const otpBtn = document.getElementById('otpBtn');
    const otpAlert = document.getElementById('otpAlert');
    const otpOk = document.getElementById('otpSuccess');
    const passSec = document.getElementById('passwordSection');
    const resendSec = document.getElementById('resendSection');

    otpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        otpAlert.classList.add('d-none');
        otpOk.classList.add('d-none');
        otpBtn.disabled = true;

        try {
            const res = await fetch(otpForm.action, {
                method: 'POST',
                body: new FormData(otpForm),
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
            });
            const data = await res.json();

            if (data.ok) {
                // Sembunyikan form OTP + tombol kirim ulang, tampilkan form password
                otpSection.classList.add('d-none');
                resendSec.classList.add('d-none');
                passSec.classList.remove('d-none');

                otpOk.textContent = data.message || 'Kode benar. Silakan buat password baru.';
                otpOk.classList.remove('d-none');

                passSec.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                otpAlert.textContent = data.message || 'Kode salah.';
                otpAlert.classList.remove('d-none');
            }
        } catch (err) {
            otpAlert.textContent = 'Terjadi kesalahan. Coba lagi.';
            otpAlert.classList.remove('d-none');
        } finally {
            otpBtn.disabled = false;
        }
    });
})();
</script>

<?= $this->endSection() ?>