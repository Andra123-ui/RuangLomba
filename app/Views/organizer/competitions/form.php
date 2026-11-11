<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = ! empty($competition);
$errors = session()->getFlashdata('errors') ?? [];
$action = $isEdit
    ? base_url('organizer/lomba/' . $competition['id'])
    : base_url('organizer/lomba');

// nilai: input lama (kalau validasi gagal) > data peluang > default
$val = fn (string $k, $d = '') => old($k, $competition[$k] ?? $d);
$inv = fn (string $k) => isset($errors[$k]) ? ' is-invalid' : '';

$deadlineVal = old('deadline', $isEdit ? date('Y-m-d', strtotime($competition['deadline'])) : '');
$eventVal    = old('event_date', ($isEdit && $competition['event_date']) ? date('Y-m-d\TH:i', strtotime($competition['event_date'])) : '');
$prizeVal    = old('prize', $isEdit ? (int) $competition['prize'] : 0);
$feeVal      = old('registration_fee', $isEdit ? (int) $competition['registration_fee'] : 0);
$modeVal     = $val('implementation_type', 'Online');
$regMode   = old('registration_mode', $isEdit ? ($competition['registration_mode'] ?? 'external') : 'external');
$regLocked = $regLocked ?? false;
if ($regLocked) {
    $regMode = 'internal';
}
?>

<div class="container org-shell">
    <?= $this->include('organizer/_sidebar') ?>

    <div class="org-main">
        <header class="org-head">
            <div>
                <h1><?= $isEdit ? 'Ubah Peluang' : 'Tambah Peluang' ?></h1>
                <p><?= $isEdit
                    ? 'Perbarui informasi peluangmu.'
                    : 'Peluang baru akan ditinjau admin sebelum tayang di RuangLomba.' ?></p>
            </div>
        </header>

        <div class="org-form">

            <?php if (! empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($isEdit && $competition['status'] !== 'pending'): ?>
            <div class="alert alert-warning" role="alert">
                Setelah disimpan, peluang ini akan ditinjau ulang admin dan sementara tidak tampil di halaman
                publik.
            </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control auth-input<?= $inv('title') ?>" id="title" name="title"
                            value="<?= esc($val('title')) ?>" maxlength="200" required>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">Jenis peluang</label>
                        <select class="form-select auth-input<?= $inv('type') ?>" id="type" name="type" required>
                            <option value="" disabled <?= $val('type') ? '' : 'selected' ?>>Pilih jenis</option>
                            <?php foreach ($types as $t): ?>
                            <option value="<?= esc($t, 'attr') ?>" <?= $val('type') === $t ? 'selected' : '' ?>>
                                <?= esc($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select auth-input<?= $inv('category_id') ?>" id="category_id"
                            name="category_id" required>
                            <option value="" disabled <?= $val('category_id') ? '' : 'selected' ?>>Pilih kategori
                            </option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>"
                                <?= (string) $val('category_id') === (string) $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control auth-input<?= $inv('description') ?>" id="description"
                            name="description" rows="5" maxlength="5000"
                            required><?= esc($val('description')) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="education_level" class="form-label">Jenjang</label>
                        <select class="form-select auth-input<?= $inv('education_level') ?>" id="education_level"
                            name="education_level" required>
                            <?php foreach ($levels as $lv): ?>
                            <option value="<?= esc($lv, 'attr') ?>"
                                <?= $val('education_level', 'Umum') === $lv ? 'selected' : '' ?>><?= esc($lv) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="competition_level" class="form-label">Tingkat</label>
                        <select class="form-select auth-input<?= $inv('competition_level') ?>" id="competition_level"
                            name="competition_level" required>
                            <?php foreach ($scopes as $sc): ?>
                            <option value="<?= esc($sc, 'attr') ?>"
                                <?= $val('competition_level', 'Nasional') === $sc ? 'selected' : '' ?>>
                                <?= esc($sc) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="implementation_type" class="form-label">Mode pelaksanaan</label>
                        <select class="form-select auth-input<?= $inv('implementation_type') ?>"
                            id="implementation_type" name="implementation_type" required>
                            <?php foreach ($modes as $m): ?>
                            <option value="<?= esc($m, 'attr') ?>" <?= $modeVal === $m ? 'selected' : '' ?>>
                                <?= esc($m) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12" id="locationWrap">
                        <label for="location" class="form-label">Lokasi</label>
                        <input type="text" class="form-control auth-input<?= $inv('location') ?>" id="location"
                            name="location" value="<?= esc($modeVal === 'Online' ? '' : $val('location')) ?>"
                            maxlength="150" placeholder="Contoh: Surabaya, Gedung Convention Hall">
                    </div>

                    <div class="col-md-6">
                        <label for="prize" class="form-label">Total hadiah (Rp)</label>
                        <input type="number" min="0" step="1" class="form-control auth-input<?= $inv('prize') ?>"
                            id="prize" name="prize" value="<?= esc($prizeVal) ?>">
                        <div class="form-text">Isi 0 kalau tidak ada hadiah uang.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="registration_fee" class="form-label">Biaya pendaftaran (Rp)</label>
                        <input type="number" min="0" step="1"
                            class="form-control auth-input<?= $inv('registration_fee') ?>" id="registration_fee"
                            name="registration_fee" value="<?= esc($feeVal) ?>">
                        <div class="form-text">Isi 0 kalau gratis.</div>
                    </div>

                    <div class="col-12">
                        <label for="requirements" class="form-label">Persyaratan <span
                                class="text-secondary">(opsional)</span></label>
                        <textarea class="form-control auth-input<?= $inv('requirements') ?>" id="requirements"
                            name="requirements" rows="3" maxlength="2000"><?= esc($val('requirements')) ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Cara pendaftaran</label>

                        <div class="role-switch mb-2">
                            <label class="role-option">
                                <input type="radio" name="registration_mode" value="external"
                                    <?= $regMode === 'external' ? 'checked' : '' ?> <?= $regLocked ? 'disabled' : '' ?>>
                                <span>Link eksternal</span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="registration_mode" value="internal"
                                    <?= $regMode === 'internal' ? 'checked' : '' ?> <?= $regLocked ? 'disabled' : '' ?>>
                                <span>Pendaftaran RuangLomba</span>
                            </label>
                        </div>

                        <?php if ($regLocked): ?>
                        <input type="hidden" name="registration_mode" value="internal">
                        <div class="form-text">Cara pendaftaran dikunci karena sudah ada peserta yang mendaftar.</div>
                        <?php endif; ?>

                        <div id="modeHintExternal" class="form-text">
                            Peserta diarahkan ke Google Form atau website pendaftaranmu. Kamu memverifikasi
                            peserta sendiri di sana.
                        </div>
                        <div id="modeHintInternal" class="form-text">
                            Peserta mendaftar langsung di RuangLomba. Kamu bisa melihat daftar pendaftar dan
                            memverifikasinya dari menu Peluang Saya.
                        </div>
                    </div>

                    <div class="col-12" id="linkWrap">
                        <label for="registration_link" class="form-label">Link pendaftaran</label>
                        <input type="url" class="form-control auth-input<?= $inv('registration_link') ?>"
                            id="registration_link" name="registration_link"
                            value="<?= esc($val('registration_link')) ?>" placeholder="https://">
                    </div>

                    <div class="col-md-6">
                        <label for="deadline" class="form-label">Deadline pendaftaran</label>
                        <input type="date" class="form-control auth-input<?= $inv('deadline') ?>" id="deadline"
                            name="deadline" value="<?= esc($deadlineVal) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="event_date" class="form-label">Tanggal pelaksanaan <span
                                class="text-secondary">(opsional)</span></label>
                        <input type="datetime-local" class="form-control auth-input<?= $inv('event_date') ?>"
                            id="event_date" name="event_date" value="<?= esc($eventVal) ?>">
                    </div>

                    <div class="col-12">
                        <label for="poster" class="form-label">Poster
                            <?= $isEdit ? '<span class="text-secondary">(kosongkan kalau tidak diganti)</span>' : '' ?>
                        </label>
                        <input type="file" class="form-control auth-input<?= $inv('poster') ?>" id="poster"
                            name="poster" accept="image/jpeg,image/png,image/webp" <?= $isEdit ? '' : 'required' ?>>
                        <div class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB. Rasio 4:3 paling pas.</div>

                        <img id="posterPreview" class="poster-preview mt-3"
                            src="<?= $isEdit && $competition['poster'] ? esc(media_url($competition['poster']), 'attr') : '' ?>"
                            alt="Pratinjau poster" <?= $isEdit && $competition['poster'] ? '' : 'hidden' ?>>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? 'Simpan Perubahan' : 'Kirim untuk Ditinjau' ?>
                    </button>
                    <a href="<?= base_url('organizer/lomba') ?>" class="btn btn-outline-primary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    // Lokasi hanya relevan untuk Offline / Hybrid
    var mode = document.getElementById('implementation_type');
    var wrap = document.getElementById('locationWrap');
    var loc = document.getElementById('location');

    function applyMode() {
        var needs = mode.value !== 'Online';
        wrap.hidden = !needs;
        loc.disabled = !needs;
        loc.required = needs;
    }
    mode.addEventListener('change', applyMode);
    applyMode();

    // Cara pendaftaran: link hanya untuk mode eksternal
    var regRadios = document.querySelectorAll('input[name="registration_mode"]');
    var linkWrap = document.getElementById('linkWrap');
    var linkInput = document.getElementById('registration_link');
    var hintExt = document.getElementById('modeHintExternal');
    var hintInt = document.getElementById('modeHintInternal');

    function applyReg() {
        var checked = document.querySelector('input[name="registration_mode"]:checked');
        var external = !checked || checked.value === 'external';
        linkWrap.hidden = !external;
        linkInput.disabled = !external;
        linkInput.required = external;
        hintExt.hidden = !external;
        hintInt.hidden = external;
    }
    regRadios.forEach(function(r) {
        r.addEventListener('change', applyReg);
    });
    applyReg();

    // Pratinjau poster yang baru dipilih
    var input = document.getElementById('poster');
    var preview = document.getElementById('posterPreview');

    input.addEventListener('change', function() {
        var file = input.files[0];
        if (!file) return;
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
    });
})();
</script>

<?= $this->endSection() ?>