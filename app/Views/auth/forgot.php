<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div>
    <div class="otp-badge"><i class="fa-solid fa-key"></i></div>
    <h1 class="auth-title">Lupa password?</h1>
    <p class="auth-sub">Masukkan email akunmu. Kami kirim kode untuk membuat password baru.</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form action="<?= base_url('lupa-password') ?>" method="post" novalidate data-loading>
    <?= csrf_field() ?>
    <div class="mb-4">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control auth-input" id="email" name="email" value="<?= esc(old('email')) ?>"
            autocomplete="email" placeholder="kamu@email.com" autofocus required>
    </div>
    <button type="submit" class="btn btn-primary w-100"><span class="spin"></span>Kirim kode</button>
</form>

<p class="auth-switch"><a href="<?= base_url('masuk') ?>"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke halaman
        masuk</a></p>

<?= $this->endSection() ?>