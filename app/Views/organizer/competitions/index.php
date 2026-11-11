<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusMap = [
    'pending'  => ['Menunggu persetujuan', 'warning'],
    'approved' => ['Tayang', 'success'],
    'rejected' => ['Ditolak', 'danger'],
    'expired'  => ['Berakhir', 'secondary'],
];
?>

<div class="container org-shell">
    <?= $this->include('organizer/_sidebar') ?>

    <div class="org-main">
        <header class="org-head">
            <div>
                <h1>Peluang Saya</h1>
                <p>Kelola lomba, beasiswa, webinar, dan event yang kamu publikasikan.</p>
            </div>
            <a href="<?= base_url('organizer/lomba/baru') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Tambah Peluang
            </a>
        </header>

        <?php if (empty($competitions)): ?>
        <div class="help-contact mt-0">
            <h2>Belum ada peluang</h2>
            <p>Mulai publikasikan lomba atau event pertamamu agar ditemukan peserta yang tepat.</p>
            <a href="<?= base_url('organizer/lomba/baru') ?>" class="btn btn-primary">Tambah Peluang</a>
        </div>
        <?php else: ?>
        <div class="org-card">
            <div class="table-responsive">
                <table class="table table-hover org-table align-middle">
                    <thead>
                        <tr>
                            <th>Peluang</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Pendaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitions as $c):
                            [$label, $color] = $statusMap[$c['status']] ?? [$c['status'], 'secondary'];

                            $daysLeft = (int) floor((strtotime($c['deadline']) - strtotime('today')) / 86400);
                            if ($daysLeft < 0) {
                                $dlText  = 'Sudah lewat';
                                $dlClass = 'is-over';
                            } elseif ($daysLeft === 0) {
                                $dlText  = 'Berakhir hari ini';
                                $dlClass = 'is-soon';
                            } else {
                                $dlText  = $daysLeft . ' hari lagi';
                                $dlClass = $daysLeft <= 7 ? 'is-soon' : '';
                            }

                            $isInternal = ($c['registration_mode'] ?? 'external') === 'internal';
                            $rc = $regCounts[(int) $c['id']] ?? ['total' => 0, 'pending' => 0];
                        ?>
                        <tr>
                            <td>
                                <div class="org-opp">
                                    <img src="<?= esc(media_url($c['poster']), 'attr') ?>" alt="" class="poster-thumb">
                                    <div>
                                        <div class="org-opp-title"><?= esc($c['title']) ?></div>
                                        <div class="org-opp-type">
                                            <?= esc($c['type']) ?>
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i><?= (int) $c['views'] ?>
                                            <i class="fa-regular fa-heart"
                                                aria-hidden="true"></i><?= (int) $c['likes'] ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge rounded-pill text-bg-<?= $color ?>"><?= esc($label) ?></span>
                            </td>

                            <td>
                                <div class="org-deadline">
                                    <?= esc(date('d M Y', strtotime($c['deadline']))) ?>
                                    <small class="<?= $dlClass ?>"><?= esc($dlText) ?></small>
                                </div>
                            </td>

                            <td>
                                <?php if ($isInternal): ?>
                                <div class="org-reg">
                                    <a href="<?= base_url('organizer/lomba/' . $c['id'] . '/pendaftar') ?>"
                                        class="org-reg-link">
                                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                                        Pendaftar
                                        <b><?= $rc['total'] ?></b>
                                    </a>
                                    <?php if ($rc['pending'] > 0): ?>
                                    <span class="org-reg-new"><?= $rc['pending'] ?> menunggu verifikasi</span>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <span class="org-reg-muted">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>
                                    Link eksternal
                                </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <div class="org-actions">
                                    <?php if ($c['status'] === 'approved'): ?>
                                    <a href="<?= base_url('peluang/' . $c['id']) ?>"
                                        class="btn btn-icon btn-outline-secondary" target="_blank"
                                        title="Lihat halaman publik" aria-label="Lihat halaman publik">
                                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <?php endif; ?>

                                    <a href="<?= base_url('organizer/lomba/' . $c['id'] . '/edit') ?>"
                                        class="btn btn-icon btn-outline-primary" title="Ubah" aria-label="Ubah peluang">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    </a>

                                    <button type="button" class="btn btn-icon btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-delete-url="<?= base_url('organizer/lomba/' . $c['id'] . '/hapus') ?>"
                                        data-delete-title="<?= esc($c['title'], 'attr') ?>" title="Hapus"
                                        aria-label="Hapus peluang">
                                        <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" id="deleteForm" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header border-0">
                <h2 class="modal-title h5" id="deleteModalLabel">Hapus peluang?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-2">Kamu akan menghapus <strong id="deleteTitle"></strong>.</p>
                <p class="text-secondary small mb-0">Data pendaftar dan simpanan peserta untuk peluang ini juga ikut
                    terhapus. Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('deleteForm').action = btn.dataset.deleteUrl;
    document.getElementById('deleteTitle').textContent = btn.dataset.deleteTitle;
});
</script>

<?= $this->endSection() ?>