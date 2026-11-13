<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$tabs = [
    'pending'  => 'Menunggu',
    'approved' => 'Tayang',
    'rejected' => 'Ditolak',
    'expired'  => 'Berakhir',
];
$badge = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'expired' => 'secondary'];
?>

<section class="page-bg">
    <div class="container">
        <div class="section-head mb-4">
            <h2>Moderasi Peluang</h2>
            <p>Tinjau peluang yang dikirim penyelenggara sebelum tayang di RuangLomba.</p>
        </div>

        <ul class="nav nav-pills gap-2 mb-4">
            <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $status === $key ? 'active' : 'bg-white border' ?>"
                    href="<?= base_url('admin/peluang?status=' . $key) ?>">
                    <?= esc($label) ?>
                    <span class="badge rounded-pill text-bg-light ms-1"><?= (int) $counts[$key] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (empty($competitions)): ?>
        <div class="help-contact mt-0">
            <h2>Tidak ada peluang</h2>
            <p>Belum ada peluang dengan status "<?= esc($tabs[$status]) ?>".</p>
        </div>
        <?php else: ?>
        <div class="table-responsive bg-white border rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Peluang</th>
                        <th>Penyelenggara</th>
                        <th>Dikirim</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitions as $c): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (! empty($c['poster'])): ?>
                                <img src="<?= esc(media_url($c['poster']), 'attr') ?>" alt="" class="poster-thumb">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-semibold"><?= esc($c['title']) ?></div>
                                    <small class="text-secondary"><?= esc($c['type']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($c['organizer_name']) ?></td>
                        <td><?= esc(date('d M Y', strtotime($c['created_at']))) ?></td>
                        <td><?= esc(date('d M Y', strtotime($c['deadline']))) ?></td>
                        <td><span class="badge text-bg-<?= $badge[$c['status']] ?? 'secondary' ?>">
                                <?= esc($tabs[$c['status']] ?? $c['status']) ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/peluang/' . $c['id']) ?>"
                                class="btn btn-sm btn-outline-primary">Tinjau</a>
                            <?php if ($c['status'] !== 'approved'): ?>
                            <form action="<?= base_url('admin/peluang/' . $c['id'] . '/setujui') ?>" method="post"
                                class="d-inline" onsubmit="return confirm('Setujui dan tayangkan peluang ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                            </form>
                            <?php endif; ?>
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