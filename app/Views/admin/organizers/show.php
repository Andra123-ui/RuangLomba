<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-bg">
    <div class="container py-4" style="max-width: 860px;">
        <div class="mb-4">
            <a href="<?= base_url('admin/organizer?status=' . $organizer['verification_status']) ?>" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Verifikasi
            </a>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">Tinjau Legalitas Penyelenggara</h2>
                <?php if ($organizer['verification_status'] === 'verified'): ?>
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-circle-check me-1"></i> Terverifikasi</span>
                <?php elseif ($organizer['verification_status'] === 'pending'): ?>
                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Verifikasi</span>
                <?php else: ?>
                    <span class="badge bg-secondary fs-6"><?= ucfirst($organizer['verification_status']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
        <?php endif; ?>

        <!-- Organizer Detail Card -->
        <div class="card card-ruang p-4 bg-white shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3 pb-3 border-bottom mb-3">
                <?php if (! empty($organizer['avatar'])): ?>
                    <img src="<?= esc(media_url($organizer['avatar']), 'attr') ?>" alt="" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: 700;">
                        <?= strtoupper(substr($organizer['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h4 class="fw-bold mb-1 text-dark"><?= esc($organizer['name']) ?></h4>
                    <div class="text-secondary small"><?= esc($organizer['email']) ?> &middot; Terdaftar <?= date('d M Y', strtotime($organizer['registered_at'])) ?></div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Tipe Organisasi:</span>
                    <strong class="fs-6"><?= esc($organizer['org_type']) ?></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Nomor Telepon / WhatsApp:</span>
                    <strong class="fs-6"><?= esc($organizer['phone']) ?></strong>
                </div>
                <div class="col-12">
                    <span class="text-secondary small d-block">Tautan Media Sosial / Website:</span>
                    <?php if (! empty($organizer['link'])): ?>
                    <a href="<?= esc($organizer['link'], 'attr') ?>" target="_blank" class="fw-semibold text-primary">
                        <?= esc($organizer['link']) ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                    </a>
                    <?php else: ?>
                    <span class="text-secondary">-</span>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <span class="text-secondary small d-block">Deskripsi Instansi:</span>
                    <p class="mb-0 text-dark"><?= nl2br(esc($organizer['description'] ?: 'Tidak ada deskripsi.')) ?></p>
                </div>
            </div>
        </div>

        <!-- Document Preview Card -->
        <div class="card card-ruang p-4 bg-white shadow-sm mb-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom"><i class="fa-solid fa-file-shield text-primary me-2"></i>Berkas Legalitas yang Diajukan</h5>
            <?php if (! empty($organizer['verification_document'])): ?>
                <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold text-dark mb-1">Dokumen Legalitas Resmi</div>
                        <div class="small text-secondary"><?= esc(basename($organizer['verification_document'])) ?></div>
                    </div>
                    <a href="<?= esc(media_url($organizer['verification_document']), 'attr') ?>" target="_blank" class="btn btn-primary btn-sm px-3">
                        <i class="fa-solid fa-eye me-1"></i> Buka / Unduh File
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary mb-0 py-3 text-center">
                    Penyelenggara belum mengunggah berkas legalitas.
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Panel -->
        <div class="card card-ruang p-4 bg-white shadow-sm">
            <h5 class="fw-bold mb-3">Keputusan Admin</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($organizer['verification_status'] !== 'verified' && ! empty($organizer['verification_document'])): ?>
                <form action="<?= base_url('admin/organizer/' . $organizer['id'] . '/verifikasi') ?>" method="post"
                      data-confirm-title="Verifikasi instansi" data-confirm-label="Setujui"
                      data-confirm-message="Apakah Anda yakin dokumen legalitas instansi ini SAH? Lencana Centang Biru akan langsung aktif.">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-check me-1"></i> Setujui &amp; Beri Centang Biru
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($organizer['verification_status'] !== 'rejected'): ?>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fa-solid fa-xmark me-1"></i> Tolak Pengajuan
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/organizer/' . $organizer['id'] . '/tolak') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="rejectModalLabel">Tolak Pengajuan Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="reason" class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea class="form-control auth-input" id="reason" name="reason" rows="3"
                          placeholder="Contoh: Dokumen tidak berkop resmi atau gambar buram..." required></textarea>
                <div class="form-text small">Alasan ini akan dikirimkan langsung ke notifikasi penyelenggara.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
