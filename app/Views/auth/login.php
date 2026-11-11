<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<h1 class="auth-title">Masuk</h1>
<p class="auth-sub">Lanjutkan perjalananmu bersama RuangLomba.</p>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form action="<?= base_url('masuk') ?>" method="post" novalidate>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control auth-input" id="email" name="email" value="<?= esc(old('email')) ?>"
            autocomplete="email" autofocus required>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="auth-pass">
            <input type="password" class="form-control auth-input" id="password" name="password"
                autocomplete="current-password" required>
            <button type="button" class="auth-pass-toggle" data-toggle-pass="password" aria-label="Tampilkan password"
                aria-pressed="false">
                <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Masuk</button>
</form>

<p class="auth-switch">
    Belum punya akun? <a href="<?= base_url('daftar') ?>">Daftar sebagai peserta</a>
    atau <a href="<?= base_url('daftar?role=organizer') ?>">penyelenggara</a>.
</p>

<?= $this->endSection() ?>