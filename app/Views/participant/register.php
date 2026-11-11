<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$inv    = fn (string $k) => isset($errors[$k]) ? ' is-invalid' : '';
$isFree = (int) $competition['registration_fee'] === 0;
?>

<section class="section section-gradient">
    <div class="container" style="max-width: 720px;">

        <a href="<?= base_url('peluang/' . $competition['id']) ?>" class="btn btn-outline-primary btn-sm mb-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>

        <div class="bg-white border rounded-4 shadow-sm p-4 p-md-5">
            <h1 class="h2 mb-1">Daftar Peluang</h1>
            <p class="text-secondary mb-4">Lengkapi data berikut untuk mendaftar.</p>

            <!-- Ringkasan peluang -->
            <div class="p-3 rounded-3 mb-4" style="background: var(--c-blue-dim);">
                <div class="fw-semibold"><?= esc($competition['title']) ?></div>
                <div class="small text-secondary mt-1">
                    <?= esc($competition['organizer_name']) ?> &middot;
                    Deadline <?= date('d M Y', strtotime($competition['deadline'])) ?> &middot;
                    <?= $isFree ? 'Gratis' : 'Rp ' . number_format($competition['registration_fee'], 0, ',', '.') ?>
                </div>
            </div>

            <?php if (! empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('peluang/' . $competition['id'] . '/daftar') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control auth-input" value="<?= esc($user['name'] ?? '') ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control auth-input" value="<?= esc($user['email'] ?? '') ?>"
                            readonly>
                    </div>

                    <div class="col-12">
                        <label for="phone" class="form-label">Nomor HP</label>
                        <input type="tel" class="form-control auth-input<?= $inv('phone') ?>" id="phone" name="phone"
                            value="<?= esc(old('phone')) ?>" placeholder="081234567890" required>
                    </div>

                    <div class="col-12">
                        <label for="institution" class="form-label">Sekolah / Instansi</label>
                        <input type="text" class="form-control auth-input<?= $inv('institution') ?>" id="institution"
                            name="institution" value="<?= esc(old('institution')) ?>" maxlength="150"
                            placeholder="Contoh: SMAN 1 Surabaya" required>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input<?= $inv('consent') ?>" type="checkbox" value="1" id="consent"
                                name="consent" <?= old('consent') ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="consent">
                                Saya setuju data di atas dibagikan kepada penyelenggara untuk keperluan
                                pendaftaran.
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4 w-100">Kirim Pendaftaran</button>
            </form>
        </div>
    </div>
</section>

<?= $this->endSection() ?>