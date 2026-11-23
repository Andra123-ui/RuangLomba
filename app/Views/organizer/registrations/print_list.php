<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #212529;
        }
        .report-sheet {
            max-width: 960px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #dee2e6;
        }
        .report-header {
            border-bottom: 2px solid #212529;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .table th {
            background-color: #f1f3f5 !important;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            font-size: 13.5px;
        }
        @media print {
            body { background: #fff; }
            .report-sheet {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<?php $isExternal = ($competition['registration_mode'] ?? 'internal') === 'external'; ?>

<div class="container">
    <div class="no-print my-4 d-flex justify-content-between align-items-center" style="max-width: 960px; margin: 0 auto;">
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Tutup
        </button>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="fa-solid fa-print me-1"></i> Cetak Dokumen / Ekspor PDF
        </button>
    </div>

    <div class="report-sheet">
        <!-- Header Dokumen Laporan -->
        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-0"><?= $isExternal ? 'REKAP LAPORAN PENDAFTARAN EKSTERNAL' : 'LAPORAN REKAPITULASI PENDAFTAR' ?></h3>
                <div class="text-secondary small">
                    <?= $isExternal ? 'Laporan mandiri peserta &middot; ' : 'Dokumen Resmi RuangLomba &middot; ' ?>
                    Dicetak pada <?= date('d F Y, H:i') ?> WIB
                </div>
            </div>
            <div class="text-end">
                <span class="fs-4 fw-bold text-primary">RuangLomba</span>
            </div>
        </div>

        <!-- Rincian Peluang -->
        <div class="card p-3 mb-4 bg-light border-0">
            <div class="row g-2">
                <div class="col-md-6">
                    <span class="text-secondary small d-block">Judul Peluang / Kompetisi:</span>
                    <strong class="fs-6"><?= esc($competition['title']) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary small d-block">Jenis / Jenjang:</span>
                    <strong><?= esc($competition['type']) ?> &middot; <?= esc($competition['education_level']) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary small d-block"><?= $isExternal ? 'Total Laporan:' : 'Total Pendaftar:' ?></span>
                    <strong class="text-primary fs-6"><?= count($registrations) ?> <?= $isExternal ? 'Laporan' : 'Peserta' ?></strong>
                </div>
            </div>
        </div>
        <?php if ($isExternal): ?>
        <p class="small text-secondary">Daftar berikut berisi laporan mandiri peserta untuk pendaftaran eksternal, bukan data yang disinkronkan otomatis dari platform penyelenggara.</p>
        <?php endif; ?>

        <!-- Tabel Rekap Data -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th>Nama Peserta</th>
                        <th>Email</th>
                        <th>No. Telepon / WA</th>
                        <th>Sekolah / Instansi</th>
                        <th>Tipe</th>
                        <th class="text-center">Status</th>
                        <th>Waktu Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-secondary">Belum ada data pendaftar.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($registrations as $idx => $r): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="fw-semibold"><?= esc($r['name']) ?></td>
                        <td><?= esc($r['email']) ?></td>
                        <td><?= esc($r['phone'] ?: '-') ?></td>
                        <td><?= esc($r['institution'] ?: '-') ?></td>
                        <td>
                            <?php if (! empty($r['team_name'])): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    Tim: <?= esc($r['team_name']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-secondary border">Individu</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($r['status'] === 'verified'): ?>
                                <span class="badge bg-success"><?= $isExternal ? 'Terverifikasi penyelenggara' : 'Terverifikasi' ?></span>
                            <?php elseif ($r['status'] === 'pending'): ?>
                                <span class="badge bg-warning text-dark"><?= $isExternal ? 'Menunggu verifikasi penyelenggara' : 'Menunggu' ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($r['registered_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kolom Tanda Tangan Penyelenggara -->
        <div class="row mt-5 pt-3">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <div class="small text-secondary mb-1">Surabaya, <?= date('d F Y') ?></div>
                <div class="small fw-semibold text-dark mb-5">Penyelenggara Kegiatan,</div>
                <div class="fw-bold text-decoration-underline"><?= esc(session()->get('userName')) ?></div>
                <div class="small text-secondary">Panitia Pelaksana</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
