<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$fmt = fn (int $n): string => number_format($n, 0, ',', '.');

// Kartu aktivitas: ikon, angka, keterangan
$activity = [
    [
        'icon'  => 'fa-users',
        'value' => $stats['registrants'],
        'label' => 'Total Pendaftar',
        'hint'  => $stats['verified'] . ' terverifikasi',
    ],
    [
        'icon'  => 'fa-hourglass-half',
        'value' => $stats['pending'],
        'label' => 'Menunggu Verifikasi',
        'hint'  => $stats['pending'] > 0 ? 'Perlu ditindak' : 'Tidak ada antrean',
        'alert' => $stats['pending'] > 0,
        'href'  => base_url('organizer/lomba'),
    ],
    [
        'icon'  => 'fa-eye',
        'value' => $stats['views'],
        'label' => 'Dilihat',
        'hint'  => 'Dari semua peluangmu',
    ],
    [
        'icon'  => 'fa-heart',
        'value' => $stats['likes'],
        'label' => 'Suka',
        'hint'  => 'Peserta yang menyimpan',
    ],
];

$statusLabels = [
    'approved' => 'Tayang',
    'pending'  => 'Menunggu persetujuan',
    'rejected' => 'Ditolak',
    'expired'  => 'Berakhir',
];
?>

<style>
.dash-group {
    margin: 0 0 12px;
    font-family: var(--f-sans);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--c-gray);
}

.dash-stat {
    position: relative;
    height: 100%;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 22px;
    color: inherit;
}

.dash-stat-ico {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    font-size: 18px;
    color: #fff;
    background: linear-gradient(135deg, var(--ocean-navy), var(--ocean-mid) 60%, var(--ocean-teal));
    box-shadow: 0 12px 20px -12px rgba(0, 36, 107, .7);
    transition: transform .4s var(--ease);
}

.card-ruang:hover .dash-stat-ico {
    transform: rotate(-8deg) scale(1.08);
}

.dash-stat-hint {
    margin-top: 8px;
    font-size: 12px;
    color: var(--c-gray);
}

/* Perlu ditindak: nuansa oranye */
.dash-stat.is-alert .dash-stat-ico {
    background: linear-gradient(135deg, #D97706, #F59E0B);
    box-shadow: 0 12px 20px -12px rgba(217, 119, 6, .7);
}

.dash-stat.is-alert .dash-stat-hint {
    font-weight: 600;
    color: #B45309;
}

a.card-ruang {
    text-decoration: none;
}
</style>

<div class="container org-shell">
    <?= $this->include('organizer/_sidebar') ?>

    <div class="org-main">
        <header class="org-head">
            <div>
                <h1>Halo, <?= esc(session()->get('userName')) ?></h1>
                <p>Ringkasan peluang dan pendaftar yang kamu kelola.</p>
            </div>
            <a href="<?= base_url('organizer/lomba/baru') ?>" class="btn btn-primary">Tambah Peluang</a>
        </header>

        <!-- AKTIVITAS -->
        <h2 class="dash-group">Aktivitas</h2>
        <div class="row g-3 mb-4">
            <?php foreach ($activity as $a):
                $isAlert = ! empty($a['alert']);
                $tag     = isset($a['href']) ? 'a' : 'div';
                $href    = isset($a['href']) ? ' href="' . esc($a['href'], 'attr') . '"' : '';
            ?>
            <div class="col-6 col-xl-3">
                <<?= $tag ?><?= $href ?> class="card card-ruang h-100">
                    <div class="dash-stat<?= $isAlert ? ' is-alert' : '' ?>">
                        <span class="dash-stat-ico"><i class="fa-solid <?= esc($a['icon'], 'attr') ?>"
                                aria-hidden="true"></i></span>
                        <div>
                            <div class="dash-num"><?= $fmt((int) $a['value']) ?></div>
                            <div class="dash-cap"><?= esc($a['label']) ?></div>
                            <div class="dash-stat-hint"><?= esc($a['hint']) ?></div>
                        </div>
                    </div>
                </<?= $tag ?>>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- STATUS PELUANG -->
        <h2 class="dash-group">Status peluang</h2>
        <div class="row g-3 mb-4">
            <?php foreach ($statusLabels as $key => $label): ?>
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