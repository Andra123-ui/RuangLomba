<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$c      = $competition;
$isFree = (int) $c['registration_fee'] === 0;
$badge  = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'expired' => 'secondary'];
$label  = ['pending' => 'Menunggu', 'approved' => 'Tayang', 'rejected' => 'Ditolak', 'expired' => 'Berakhir'];
$isExternal = ($c['registration_mode'] ?? 'external') === 'external';
?>

<section class="section section-gradient">
    <div class="container" style="max-width: 980px;">

        <a href="<?= base_url('admin/peluang') ?>" class="btn btn-outline-primary btn-sm mb-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke daftar
        </a>

        <div class="bg-white border rounded-4 shadow-sm p-4 p-md-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="badge text-bg-<?= $badge[$c['status']] ?? 'secondary' ?> mb-2">
                        <?= esc($label[$c['status']] ?? $c['status']) ?></span>
                    <h1 class="h2 mb-1"><?= esc($c['title']) ?></h1>
                    <div class="text-secondary small">
                        <?= esc($c['type']) ?> &middot; <?= esc($c['category_name']) ?> &middot;
                        Dikirim <?= esc(date('d M Y', strtotime($c['created_at']))) ?>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-5">
                    <?php if (! empty($c['poster'])): ?>
                    <img src="<?= esc(media_url($c['poster']), 'attr') ?>" alt="<?= esc($c['title']) ?>"
                        class="poster-preview" style="max-width: 100%;">
                    <?php else: ?>
                    <div class="rec-empty">Tidak ada poster.</div>
                    <?php endif; ?>
                </div>

                <div class="col-md-7">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">Penyelenggara</dt>
                        <dd class="col-sm-8"><?= esc($c['organizer_name']) ?>
                            <small class="text-secondary">(<?= esc($c['organizer_email']) ?>)</small>
                        </dd>

                        <dt class="col-sm-4 text-secondary">Jenjang</dt>
                        <dd class="col-sm-8"><?= esc($c['education_level']) ?></dd>

                        <dt class="col-sm-4 text-secondary">Tingkat</dt>
                        <dd class="col-sm-8"><?= esc($c['competition_level']) ?></dd>

                        <dt class="col-sm-4 text-secondary">Pelaksanaan</dt>
                        <dd class="col-sm-8"><?= esc($c['implementation_type']) ?>
                            <?php if (! empty($c['location']) && $c['location'] !== $c['implementation_type']): ?>
                            &middot; <?= esc($c['location']) ?>
                            <?php endif; ?></dd>

                        <dt class="col-sm-4 text-secondary">Biaya</dt>
                        <dd class="col-sm-8">
                            <?= $isFree ? 'Gratis' : 'Rp ' . number_format($c['registration_fee'], 0, ',', '.') ?></dd>

                        <dt class="col-sm-4 text-secondary">Hadiah</dt>
                        <dd class="col-sm-8">
                            <?= (int) $c['prize'] === 0 ? '-' : 'Rp ' . number_format($c['prize'], 0, ',', '.') ?></dd>

                        <dt class="col-sm-4 text-secondary">Deadline</dt>
                        <dd class="col-sm-8"><?= esc(date('d M Y', strtotime($c['deadline']))) ?></dd>

                        <dt class="col-sm-4 text-secondary">Pelaksanaan acara</dt>
                        <dd class="col-sm-8">
                            <?= $c['event_date'] ? esc(date('d M Y H:i', strtotime($c['event_date']))) : '-' ?></dd>

                        <dt class="col-sm-4 text-secondary">Pendaftaran</dt>
                        <dd class="col-sm-8">
                            <?php if ($isExternal): ?>
                            Link eksternal:
                            <?php if (! empty($c['registration_link'])): ?>
                            <a href="<?= esc($c['registration_link'], 'attr') ?>" target="_blank"
                                rel="noopener noreferrer"><?= esc($c['registration_link']) ?></a>
                            <?php else: ?>
                            <span class="text-danger">belum diisi</span>
                            <?php endif; ?>
                            <?php else: ?>
                            Pendaftaran RuangLomba
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h5">Deskripsi</h2>
            <p style="white-space: pre-line; overflow-wrap: anywhere;"><?= esc($c['description']) ?></p>

            <?php if (! empty($c['requirements'])): ?>
            <h2 class="h5 mt-4">Persyaratan</h2>
            <p style="white-space: pre-line; overflow-wrap: anywhere;"><?= esc($c['requirements']) ?></p>
            <?php endif; ?>

            <hr class="my-4">

            <?php if ($c['status'] === 'approved'): ?>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-secondary">Peluang ini sudah tayang.</span>
                <a href="<?= base_url('peluang/' . $c['id']) ?>" target="_blank"
                    class="btn btn-sm btn-outline-primary">Lihat halaman publik</a>
            </div>
            <?php else: ?>
            <div class="row g-3">
                <div class="col-md-5">
                    <form action="<?= base_url('admin/peluang/' . $c['id'] . '/setujui') ?>" method="post"
                        onsubmit="return confirm('Setujui dan tayangkan peluang ini?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-check me-1"></i> Setujui &amp; Tayangkan
                        </button>
                    </form>
                </div>

                <?php if ($c['status'] !== 'rejected'): ?>
                <div class="col-md-7">
                    <form action="<?= base_url('admin/peluang/' . $c['id'] . '/tolak') ?>" method="post"
                        onsubmit="return confirm('Tolak peluang ini?');">
                        <?= csrf_field() ?>
                        <div class="input-group">
                            <input type="text" name="reason" class="form-control" maxlength="255"
                                placeholder="Alasan penolakan (opsional)">
                            <button type="submit" class="btn btn-outline-danger">Tolak</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>