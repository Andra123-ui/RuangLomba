<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$tabs = [
    ''         => 'Semua',
    'pending'  => 'Menunggu',
    'verified' => 'Terverifikasi',
    'rejected' => 'Ditolak',
];
$badge = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
$total = array_sum($counts);
$countOf = fn (string $k) => $k === '' ? $total : (int) $counts[$k];

// 0812... / +62812... -> 62812... untuk tautan WhatsApp
$waNumber = function (string $phone): string {
    $p = preg_replace('/\D+/', '', $phone);
    return str_starts_with($p, '0') ? '62' . substr($p, 1) : $p;
};
?>

<div class="container org-shell">
    <?= $this->include('organizer/_sidebar') ?>

    <div class="org-main">
        <header class="org-head">
            <div>
                <h1>Pendaftar</h1>
                <p><?= esc($competition['title']) ?></p>
            </div>
            <a href="<?= base_url('organizer/lomba') ?>" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Peluang Saya
            </a>
        </header>

        <ul class="nav nav-pills gap-2 mb-4">
            <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $status === $key ? 'active' : 'bg-white border' ?>"
                    href="<?= base_url('organizer/lomba/' . $competition['id'] . '/pendaftar' . ($key !== '' ? '?status=' . $key : '')) ?>">
                    <?= esc($label) ?>
                    <span class="badge rounded-pill text-bg-light ms-1"><?= $countOf($key) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (empty($registrations)): ?>
        <div class="help-contact mt-0">
            <h2>Belum ada pendaftar</h2>
            <p><?= $status === '' ? 'Pendaftar akan muncul di sini setelah peserta mendaftar.' : 'Tidak ada pendaftar dengan status ini.' ?>
            </p>
        </div>
        <?php else: ?>
        <div class="table-responsive bg-white border rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kontak</th>
                        <th>Sekolah / Instansi</th>
                        <th>Tanggal daftar</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $r): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($r['name']) ?></div>
                            <small class="text-secondary"><?= esc($r['email']) ?></small>
                        </td>
                        <td>
                            <?php if (! empty($r['phone'])): ?>
                            <a href="https://wa.me/<?= esc($waNumber($r['phone']), 'attr') ?>" target="_blank"
                                rel="noopener noreferrer"><?= esc($r['phone']) ?></a>
                            <?php else: ?>
                            <span class="text-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($r['institution'] ?: '-') ?></td>
                        <td><?= esc(date('d M Y', strtotime($r['registered_at']))) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $badge[$r['status']] ?? 'secondary' ?>">
                                <?= esc($tabs[$r['status']] ?? $r['status']) ?></span>
                            <?php if ($r['status'] === 'rejected' && ! empty($r['reject_reason'])): ?>
                            <div class="small text-secondary mt-1">Alasan: <?= esc($r['reject_reason']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if ($r['status'] !== 'verified'): ?>
                            <form action="<?= base_url('organizer/pendaftar/' . $r['id'] . '/verifikasi') ?>"
                                method="post" class="d-inline" onsubmit="return confirm('Verifikasi pendaftar ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-success">Verifikasi</button>
                            </form>
                            <?php endif; ?>

                            <?php if ($r['status'] !== 'rejected'): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#rejectModal"
                                data-reject-url="<?= base_url('organizer/pendaftar/' . $r['id'] . '/tolak') ?>"
                                data-reject-name="<?= esc($r['name'], 'attr') ?>">Tolak</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal tolak pendaftar -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" id="rejectForm" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header border-0">
                <h2 class="modal-title h5" id="rejectModalLabel">Tolak pendaftar?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-3">Kamu akan menolak pendaftaran <strong id="rejectName"></strong>.</p>
                <label for="rejectReason" class="form-label">Alasan penolakan</label>
                <textarea class="form-control" id="rejectReason" name="reason" rows="3" minlength="3" maxlength="255"
                    required placeholder="Contoh: Data tidak sesuai persyaratan jenjang."></textarea>
                <div class="form-text">Alasan ini akan dilihat peserta.</div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak pendaftar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('rejectForm').action = btn.dataset.rejectUrl;
    document.getElementById('rejectName').textContent = btn.dataset.rejectName;
    document.getElementById('rejectReason').value = '';
});
</script>

<?= $this->endSection() ?>