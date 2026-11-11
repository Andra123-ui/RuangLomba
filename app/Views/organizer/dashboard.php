<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container org-shell">
    <?= $this->include('organizer/_sidebar') ?>

    <div class="org-main">
        <header class="org-head">
            <div>
                <h1>Halo, <?= esc(session()->get('userName')) ?></h1>
                <p>Kelola lomba dan event yang kamu publikasikan.</p>
            </div>
            <a href="<?= base_url('organizer/lomba/baru') ?>" class="btn btn-primary">Tambah Peluang</a>
        </header>

        <div class="row g-3 mb-4">
            <?php
            $labels = ['approved' => 'Tayang', 'pending' => 'Menunggu persetujuan', 'rejected' => 'Ditolak', 'expired' => 'Berakhir'];
            foreach ($labels as $key => $label): ?>
            <div class="col-6 col-xl-3">
                <div class="card card-ruang h-100">
                    <div class="card-body">
                        <div class="dash-num"><?= (int) $summary[$key] ?></div>
                        <div class="dash-cap"><?= esc($label) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <a href="<?= base_url('organizer/lomba') ?>" class="btn btn-outline-primary">Kelola Peluang Saya</a>
    </div>
</div>

<?= $this->endSection() ?>