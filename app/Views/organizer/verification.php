<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$status = $profile['verification_status'] ?? 'unverified';
$errors = session()->getFlashdata('errors') ?? [];
?>

<div class="org-main">
        <header class="org-head">
            <div>
                <h1>Verifikasi Instansi Penyelenggara</h1>
                <p>Dapatkan <strong>Lencana Centang Biru (Verified Organizer)</strong> untuk meningkatkan kepercayaan peserta dan mencegah indikasi lomba fiktif.</p>
            </div>
        </header>

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

        <?php if (! empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $e): ?>
                <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Status Card -->
        <?php if ($status === 'verified'): ?>
        <div class="card card-ruang p-4 p-md-5 text-center bg-white border border-success shadow-sm mb-4">
            <div class="mb-3">
                <i class="fa-solid fa-circle-check text-primary display-3"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                Instansi Terverifikasi Resmi
                <i class="fa-solid fa-certificate text-primary ms-1" title="Centang Biru Aktif"></i>
            </h3>
            <p class="text-secondary mx-auto mb-4" style="max-width: 600px;">
                Selamat! Akun penyelenggara <strong><?= esc($user['name']) ?></strong> telah divalidasi resmi oleh Admin RuangLomba. Semua lomba yang kamu buat akan menampilkan lencana terpercaya.
            </p>
            <div class="p-3 bg-light rounded-3 d-inline-block mx-auto border small text-start">
                <div><strong>Tipe Organisasi:</strong> <?= esc($profile['org_type']) ?></div>
                <div><strong>Tanggal Disetujui:</strong> <?= date('d F Y', strtotime($profile['verified_at'] ?? 'now')) ?></div>
                <?php if (! empty($profile['verification_document'])): ?>
                <div class="mt-2">
                    <a href="<?= esc(media_url($profile['verification_document']), 'attr') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-file-lines me-1"></i> Lihat Dokumen Legalitas
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php elseif ($status === 'pending'): ?>
        <div class="card card-ruang p-4 p-md-5 text-center bg-white border border-warning shadow-sm mb-4">
            <div class="mb-3">
                <i class="fa-solid fa-hourglass-half text-warning display-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Menunggu Peninjauan Admin</h3>
            <p class="text-secondary mx-auto mb-4" style="max-width: 550px;">
                Dokumen legalitas instansimu telah kami terima dan sedang dalam proses verifikasi oleh Admin RuangLomba. Kami akan mengirimkan notifikasi setelah proses selesai.
            </p>
            <?php if (! empty($profile['verification_document'])): ?>
            <div>
                <a href="<?= esc(media_url($profile['verification_document']), 'attr') ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-file-pdf me-1"></i> Pratinjau Dokumen yang Diajukan
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>

        <?php if ($status === 'rejected'): ?>
        <div class="alert alert-danger p-4 mb-4" role="alert">
            <h5 class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-2"></i>Pengajuan Verifikasi Ditolak</h5>
            <p class="mb-2">Admin memberikan catatan evaluasi berikut:</p>
            <div class="p-3 bg-white rounded border border-danger text-dark fw-semibold mb-3">
                <?= esc($profile['rejection_reason'] ?: 'Dokumen belum memenuhi kriteria keabsahan.') ?>
            </div>
            <p class="small mb-0">Silakan unggah kembali dokumen legalitas yang lebih jelas atau sesuai catatan di atas.</p>
        </div>
        <?php endif; ?>

        <!-- Form Pengajuan Verifikasi -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card card-ruang p-4 bg-white shadow-sm">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom">Unggah Dokumen Legalitas Instansi</h5>
                    <form action="<?= base_url('organizer/verifikasi') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Organisasi / Lembaga</label>
                            <input type="text" class="form-control auth-input bg-light" value="<?= esc($user['name']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Instansi</label>
                            <input type="text" class="form-control auth-input bg-light" value="<?= esc($profile['org_type']) ?>" readonly>
                        </div>

                        <div class="mb-4">
                            <label for="document" class="form-label fw-semibold">File Dokumen Legalitas / SK <span class="text-danger">*</span></label>
                            <input type="file" class="form-control auth-input" id="document" name="document"
                                   accept="application/pdf,image/jpeg,image/png,image/webp" required>
                            <div class="form-text small">Format: PDF, JPG, PNG, atau WebP. Maksimal 4 MB.</div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-paper-plane me-1"></i> Ajukan Verifikasi ke Admin
                        </button>
                    </form>
                </div>
            </div>

            <!-- Petunjuk Dokumen -->
            <div class="col-lg-5">
                <div class="card card-ruang p-4 bg-white shadow-sm h-100">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-circle-question text-primary me-2"></i>Dokumen yang Dapat Diterima</h5>
                    <p class="text-secondary small mb-3">Untuk memastikan kepastian identitas penyelenggara, lampirkan salah satu dokumen resmi berikut:</p>

                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-check text-success mt-1"></i>
                            <div><strong>Sekolah / Kampus:</strong> Surat Rekomendasi Kepala Sekolah / Dekan / Surat Keterangan BEM/HIMA resmi.</div>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-check text-success mt-1"></i>
                            <div><strong>Komunitas:</strong> Surat Keputusan (SK) Pengurus Komunitas atau Surat Izin Kegiatan.</div>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-check text-success mt-1"></i>
                            <div><strong>Perusahaan / Startup:</strong> NIB / Akta Perusahaan / Surat Tugas Panitia Pelaksana.</div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-check text-success mt-1"></i>
                            <div><strong>Panitia Event:</strong> Surat Pernyataan Tanggung Jawab &amp; Foto KTA/KTM Ketua Pelaksana.</div>
                        </li>
                    </ul>

                    <div class="p-3 bg-light rounded-3 border small text-secondary">
                        <i class="fa-solid fa-shield-cat text-primary me-1"></i> Data dan dokumen yang kamu kirimkan hanya digunakan oleh Admin RuangLomba untuk verifikasi keabsahan.
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?= $this->endSection() ?>
