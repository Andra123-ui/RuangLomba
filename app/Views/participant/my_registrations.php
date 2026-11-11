<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusMap = [
    'pending'  => ['Menunggu verifikasi', 'warning'],
    'verified' => ['Terverifikasi', 'success'],
    'rejected' => ['Ditolak', 'danger'],
];
$labels = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];
?>

<section class="section section-gradient">
    <div class="container">
        <div class="section-head reveal is-visible mb-4">
            <h2>Pendaftaran Saya</h2>
            <p>Pantau status pendaftaranmu di peluang yang memakai pendaftaran RuangLomba.</p>
        </div>

        <?php if (empty($registrations)): ?>
        <div class="help-contact mt-0">
            <h2>Belum ada pendaftaran</h2>
            <p>Jelajahi peluang dan daftar ke yang sesuai dengan minatmu.</p>
            <a href="<?= base_url('cari') ?>" class="btn btn-primary">Jelajahi Peluang</a>
        </div>
        <?php else: ?>

        <div class="row g-3 mb-4">
            <?php foreach ($labels as $key => $label): ?>
            <div class="col-4">
                <div class="card card-ruang h-100">
                    <div class="card-body">
                        <div class="dash-num"><?= (int) $summary[$key] ?></div>
                        <div class="dash-cap"><?= esc($label) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="table-responsive bg-white border rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Peluang</th>
                        <th>Penyelenggara</th>
                        <th>Tanggal daftar</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $r):
                        [$label, $color] = $statusMap[$r['status']] ?? [$r['status'], 'secondary']; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (! empty($r['poster'])): ?>
                                <img src="<?= esc(media_url($r['poster']), 'attr') ?>" alt="" class="poster-thumb">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold"><?= esc($r['title']) ?></div>
                                    <small class="text-secondary"><?= esc($r['type']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($r['organizer_name']) ?></td>
                        <td><?= esc(date('d M Y', strtotime($r['registered_at']))) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $color ?>"><?= esc($label) ?></span>
                            <?php if ($r['status'] === 'rejected' && ! empty($r['reject_reason'])): ?>
                            <div class="small text-secondary mt-1">Alasan: <?= esc($r['reject_reason']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('peluang/' . $r['competition_id']) ?>"
                                class="btn btn-sm btn-outline-primary">Lihat</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>