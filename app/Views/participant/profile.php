<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];
$success = session()->getFlashdata('success');
$inv = fn (string $k) => isset($errors[$k]) ? ' is-invalid' : '';
$val = fn (string $k, $d = '') => old($k, $user[$k] ?? $d);
$prefVal = fn (string $k, $d = '') => old($k, $preference[$k] ?? $d);
?>

<section class="page-bg">
    <div class="container py-4">
        <div class="section-head reveal is-visible mb-4">
            <h2>Profil &amp; Preferensi Saya</h2>
            <p>Atur informasi profil, minat, dan keahlianmu agar <strong>LombaMatch AI</strong> dapat memberikan rekomendasi lomba yang paling akurat.</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
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

        <div class="row g-4">
            <!-- Left Card: User Info & Gamification Points -->
            <div class="col-lg-4">
                <div class="card card-ruang p-4 text-center h-100">
                    <div class="mb-3">
                        <?php if (! empty($user['avatar'])): ?>
                            <img src="<?= esc(media_url($user['avatar']), 'attr') ?>" alt="Avatar" class="rounded-circle shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 110px; height: 110px; font-size: 2.5rem; font-weight: 700;">
                                <?= strtoupper(substr($user['name'] ?? 'P', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="mb-1 fw-bold"><?= esc($user['name']) ?></h4>
                    <p class="text-secondary small mb-3"><?= esc($user['email']) ?></p>

                    <div class="p-3 bg-light rounded-4 mb-3 border">
                        <div class="text-secondary small fw-semibold">Poin Prestasi &amp; Keaktifan</div>
                        <div class="fs-2 fw-bold text-primary my-1">
                            <i class="fa-solid fa-star text-warning me-1"></i><?= number_format((int) ($user['points'] ?? 0), 0, ',', '.') ?>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            Peserta Aktif
                        </span>
                    </div>

                    <div class="text-start small text-secondary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Bergabung sejak:</span>
                            <span class="fw-semibold text-dark"><?= date('d M Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Status Akun:</span>
                            <span class="badge bg-success-subtle text-success">Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Form Profil & Preferensi -->
            <div class="col-lg-8">
                <div class="card card-ruang p-4">
                    <form action="<?= base_url('profil') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <h5 class="fw-bold mb-3 pb-2 border-bottom">Informasi Dasar</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control auth-input<?= $inv('name') ?>" id="name" name="name"
                                    value="<?= esc($val('name')) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label for="avatar" class="form-label fw-semibold">Ganti Foto Profil</label>
                                <input type="file" class="form-control auth-input<?= $inv('avatar') ?>" id="avatar" name="avatar"
                                    accept="image/jpeg,image/png,image/webp">
                                <div class="form-text small">Maks 2MB (JPG, PNG, WebP)</div>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 pb-2 border-bottom">Preferensi Lomba (LombaMatch AI)</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="preferred_level" class="form-label fw-semibold">Jenjang Pendidikan</label>
                                <select class="form-select auth-input" id="preferred_level" name="preferred_level">
                                    <option value="">Pilih Jenjang</option>
                                    <?php foreach ($levels as $lv): ?>
                                    <option value="<?= esc($lv, 'attr') ?>" <?= $prefVal('preferred_level') === $lv ? 'selected' : '' ?>>
                                        <?= esc($lv) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="preferred_mode" class="form-label fw-semibold">Mode Pelaksanaan Favorit</label>
                                <select class="form-select auth-input" id="preferred_mode" name="preferred_mode">
                                    <option value="">Tidak Masalah</option>
                                    <?php foreach ($modes as $m): ?>
                                    <option value="<?= esc($m, 'attr') ?>" <?= $prefVal('preferred_mode') === $m ? 'selected' : '' ?>>
                                        <?= esc($m) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="preferred_fee" class="form-label fw-semibold">Preferensi Biaya</label>
                                <select class="form-select auth-input" id="preferred_fee" name="preferred_fee">
                                    <?php foreach ($fees as $f): ?>
                                    <option value="<?= esc($f, 'attr') ?>" <?= $prefVal('preferred_fee', 'Tidak Masalah') === $f ? 'selected' : '' ?>>
                                        <?= esc($f) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Minat / Kategori -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Bidang Minat Favorit</label>
                            <p class="text-secondary small mb-2">Pilih kategori yang paling sesuai dengan passion kamu:</p>
                            <div class="row g-2">
                                <?php foreach ($categories as $cat): ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="form-check p-2 border rounded-3 h-100 bg-white">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="interests[]"
                                            value="<?= esc($cat['name'], 'attr') ?>" id="cat_<?= $cat['id'] ?>"
                                            <?= in_array($cat['name'], $selectedInterests, true) ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-medium" for="cat_<?= $cat['id'] ?>">
                                            <?= esc($cat['name']) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Keahlian / Skills -->
                        <div class="mb-4">
                            <label for="skills" class="form-label fw-semibold">Keahlian &amp; Tools yang Dikuasai</label>
                            <input type="text" class="form-control auth-input" id="skills" name="skills"
                                value="<?= esc(old('skills', implode(', ', $skills))) ?>"
                                placeholder="Contoh: Figma, UI/UX, Python, Web Design, Public Speaking, Canva">
                            <div class="form-text small">Pisahkan setiap keahlian dengan tanda koma (,). Sistem AI akan mencocokkan kata kunci ini dengan syarat lomba.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Preferensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
