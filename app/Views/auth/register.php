<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<?php
$activeRole = old('role', $role);
$errors     = session()->getFlashdata('errors') ?? [];
$levels     = ['SD', 'SMP', 'SMA/SMK/MA', 'Mahasiswa', 'Umum'];
$orgTypes   = ['Sekolah', 'Kampus', 'Komunitas', 'Perusahaan', 'Lainnya'];
?>

<div>
    <h1 class="auth-title">Buat akun</h1>
    <p class="auth-sub">Gratis. Pilih peranmu, lalu isi data singkat.</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (! empty($errors)): ?>
<div class="alert alert-danger" role="alert">
    <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?>
        <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?= base_url('daftar') ?>" method="post" id="registerForm" novalidate data-loading>
    <?= csrf_field() ?>

    <div class="role-switch" role="radiogroup" aria-label="Daftar sebagai">
        <span class="role-pill" aria-hidden="true"></span>
        <label class="role-option">
            <input type="radio" name="role" value="participant" <?= $activeRole === 'participant' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-user-graduate me-1"></i> Saya Peserta</span>
        </label>
        <label class="role-option">
            <input type="radio" name="role" value="organizer" <?= $activeRole === 'organizer' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-bullhorn me-1"></i> Saya Penyelenggara</span>
        </label>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label" id="nameLabel">Nama lengkap</label>
        <input type="text" class="form-control auth-input" id="name" name="name" value="<?= esc(old('name')) ?>"
            autocomplete="name" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control auth-input" id="email" name="email" value="<?= esc(old('email')) ?>"
            autocomplete="email" placeholder="kamu@email.com" required>
        <div class="form-text">Kami akan mengirim kode verifikasi ke email ini.</div>
    </div>

    <fieldset class="role-fields" id="fields-participant">
        <div class="mb-3">
            <label for="education_level" class="form-label">Jenjang pendidikan</label>
            <select class="form-select auth-input" id="education_level" name="education_level" required>
                <option value="" disabled <?= old('education_level') ? '' : 'selected' ?>>Pilih jenjang</option>
                <?php foreach ($levels as $lv): ?>
                <option value="<?= esc($lv, 'attr') ?>" <?= old('education_level') === $lv ? 'selected' : '' ?>>
                    <?= esc($lv) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>

    <fieldset class="role-fields" id="fields-organizer">
        <div class="mb-3">
            <label for="org_type" class="form-label">Jenis penyelenggara</label>
            <select class="form-select auth-input" id="org_type" name="org_type" required>
                <option value="" disabled <?= old('org_type') ? '' : 'selected' ?>>Pilih jenis</option>
                <?php foreach ($orgTypes as $ot): ?>
                <option value="<?= esc($ot, 'attr') ?>" <?= old('org_type') === $ot ? 'selected' : '' ?>>
                    <?= esc($ot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Nomor WhatsApp</label>
            <input type="tel" class="form-control auth-input" id="phone" name="phone" value="<?= esc(old('phone')) ?>"
                placeholder="081234567890" autocomplete="tel" required>
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Website atau Instagram <span
                    class="text-secondary">(opsional)</span></label>
            <input type="url" class="form-control auth-input" id="link" name="link" value="<?= esc(old('link')) ?>"
                placeholder="https://instagram.com/akunmu">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi singkat <span
                    class="text-secondary">(opsional)</span></label>
            <textarea class="form-control auth-input" id="description" name="description" rows="3"
                maxlength="500"><?= esc(old('description')) ?></textarea>
        </div>
    </fieldset>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="auth-pass">
            <input type="password" class="form-control auth-input" id="password" name="password"
                autocomplete="new-password" minlength="8" required>
            <button type="button" class="auth-pass-toggle" data-toggle-pass="password" aria-label="Tampilkan password"
                aria-pressed="false">
                <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
        </div>
        <div class="meter" id="meter" data-s="0"><i></i><i></i><i></i><i></i></div>
        <div class="meter-text" id="meterText">Minimal 8 karakter.</div>
    </div>

    <div class="mb-4">
        <label for="password_confirm" class="form-label">Ulangi password</label>
        <div class="auth-pass">
            <input type="password" class="form-control auth-input" id="password_confirm" name="password_confirm"
                autocomplete="new-password" required>
            <button type="button" class="auth-pass-toggle" data-toggle-pass="password_confirm"
                aria-label="Tampilkan password" aria-pressed="false">
                <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100"><span class="spin"></span>Daftar</button>

    <p class="auth-terms">
        Dengan mendaftar, kamu menyetujui
        <a href="<?= base_url('syarat-ketentuan') ?>" target="_blank">Syarat &amp; Ketentuan</a> dan
        <a href="<?= base_url('kebijakan-privasi') ?>" target="_blank">Kebijakan Privasi</a>.
    </p>
</form>

<p class="auth-switch">
    Sudah punya akun? <a href="<?= base_url('masuk') ?>">Masuk</a>
</p>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    var radios = document.querySelectorAll('input[name="role"]');
    var participant = document.getElementById('fields-participant');
    var organizer = document.getElementById('fields-organizer');
    var nameLabel = document.getElementById('nameLabel');

    function applyRole() {
        var isOrg = document.querySelector('input[name="role"]:checked').value === 'organizer';

        // fieldset disabled = input di dalamnya tidak terkirim & tidak divalidasi browser
        participant.disabled = isOrg;
        organizer.disabled = !isOrg;
        participant.hidden = isOrg;
        organizer.hidden = !isOrg;

        nameLabel.textContent = isOrg ? 'Nama penyelenggara / organisasi' : 'Nama lengkap';
    }
    radios.forEach(function(r) {
        r.addEventListener('change', applyRole);
    });
    applyRole();

    // Indikator kekuatan password
    var pass = document.getElementById('password');
    var meter = document.getElementById('meter');
    var text = document.getElementById('meterText');
    var labels = ['Minimal 8 karakter.', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];

    pass.addEventListener('input', function() {
        var v = pass.value,
            s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) s++;
        if (!v.length) s = 0;
        meter.dataset.s = s;
        text.textContent = labels[s];
    });
})();
</script>
<?= $this->endSection() ?>