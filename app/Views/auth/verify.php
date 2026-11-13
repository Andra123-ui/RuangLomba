<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div>
    <div class="otp-badge"><i class="fa-regular fa-envelope-open"></i></div>
    <h1 class="auth-title">Cek emailmu</h1>
    <p class="auth-sub">Kami mengirim kode 6 digit ke <strong class="text-dark"><?= esc($email) ?></strong>. Berlaku 10
        menit.</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form action="<?= base_url('verifikasi') ?>" method="post" novalidate data-loading data-auto="1">
    <?= csrf_field() ?>
    <div class="mb-4"><?= view('auth/_otp') ?></div>
    <button type="submit" class="btn btn-primary w-100"><span class="spin"></span>Verifikasi</button>
</form>

<?= view('auth/_resend', ['action' => 'verifikasi/kirim-ulang', 'wait' => $wait]) ?>

<p class="auth-switch"><a href="<?= base_url('masuk') ?>"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke halaman
        masuk</a></p>

<?= $this->endSection() ?>