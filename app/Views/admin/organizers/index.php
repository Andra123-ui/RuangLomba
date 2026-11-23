<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$tabs = [
    'pending'    => 'Menunggu Review',
    'verified'   => 'Terverifikasi (Resmi)',
    'rejected'   => 'Ditolak',
    'unverified' => 'Belum Mengajukan',
];
$badge = [
    'pending'    => 'warning text-dark',
    'verified'   => 'success',
    'rejected'   => 'danger',
    'unverified' => 'secondary',
];
?>

<section class="page-bg">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Verifikasi Legalitas Instansi Penyelenggara</h2>
                <p class="text-secondary mb-0">Tinjau dokumen legalitas instansi penyelenggara untuk menyematkan <strong>Lencana Verified (Centang Biru)</strong>.</p>
            </div>
            <div>
                <a href="<?= base_url('admin/peluang') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-trophy me-1"></i> Moderasi Peluang
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <ul class="nav nav-pills gap-2 mb-4">
            <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $status === $key ? 'active' : 'bg-white border' ?>"
                    href="<?= base_url('admin/organizer?status=' . $key) ?>">
                    <?= esc($label) ?>
                    <span class="badge rounded-pill text-bg-light ms-1"><?= (int) ($counts[$key] ?? 0) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (empty($organizers)): ?>
        <div class="help-contact mt-0 text-center py-5">
            <h3>Tidak Ada Organizer</h3>
            <p class="text-secondary">Tidak ada pengajuan organizer dengan status "<?= esc($tabs[$status]) ?>".</p>
        </div>
        <?php else: ?>
        <div class="table-responsive bg-white border rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Instansi / Lembaga</th>
                        <th>Tipe</th>
                        <th>Kontak</th>
                        <th class="text-center">Total Lomba</th>
                        <th>Dokumen Legalitas</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organizers as $org): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (! empty($org['avatar'])): ?>
                                    <img src="<?= esc(media_url($org['avatar']), 'attr') ?>" alt="" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-size: 1.1rem; font-weight: 700;">
                                        <?= strtoupper(substr($org['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark">
                                        <?= esc($org['name']) ?>
                                        <?php if ($org['verification_status'] === 'verified'): ?>
                                            <i class="fa-solid fa-circle-check text-primary ms-1" title="Terverifikasi"></i>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-secondary"><?= esc($org['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= esc($org['org_type']) ?></span>
                        </td>
                        <td>
                            <div class="small fw-semibold"><?= esc($org['phone']) ?></div>
                            <?php if (! empty($org['link'])): ?>
                            <a href="<?= esc($org['link'], 'attr') ?>" target="_blank" class="small text-decoration-none">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Web/Sosmed
                            </a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary border"><?= (int) $org['total_competitions'] ?> Lomba</span>
                        </td>
                        <td>
                            <?php if (! empty($org['verification_document'])): ?>
                            <a href="<?= esc(media_url($org['verification_document']), 'attr') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-file-pdf me-1"></i> Buka Dokumen
                            </a>
                            <?php else: ?>
                            <span class="text-secondary small">Belum mengunggah</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $badge[$org['verification_status']] ?? 'secondary' ?> px-2 py-1">
                                <?= esc($tabs[$org['verification_status']] ?? $org['verification_status']) ?>
                            </span>
                            <?php if ($org['verification_status'] === 'rejected' && ! empty($org['rejection_reason'])): ?>
                            <div class="small text-danger mt-1">Catatan: <?= esc($org['rejection_reason']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            <a href="<?= base_url('admin/organizer/' . $org['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                Tinjau
                            </a>
                            <?php if ($org['verification_status'] !== 'verified' && ! empty($org['verification_document'])): ?>
                            <form action="<?= base_url('admin/organizer/' . $org['id'] . '/verifikasi') ?>" method="post" class="d-inline"
                                  onsubmit="return confirm('Verifikasi instansi ini secara resmi? Centang biru akan aktif.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-success ms-1">
                                    <i class="fa-solid fa-check me-1"></i> Setujui
                                </button>
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
