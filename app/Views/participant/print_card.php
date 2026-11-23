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
        .print-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .card-header-bar {
            border-bottom: 3px solid #2563FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .meta-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-value {
            font-size: 16px;
            font-weight: 600;
            color: #111;
        }
        .stamp-box {
            border: 2px dashed #198754;
            background-color: rgba(25, 135, 84, 0.05);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
        }
        @media print {
            body { background: #fff; }
            .print-container {
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

<div class="container">
    <div class="no-print my-4 d-flex justify-content-between align-items-center" style="max-width: 800px; margin: 0 auto;">
        <a href="<?= base_url('pendaftaran-saya') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="fa-solid fa-print me-1"></i> Cetak / Simpan PDF
        </button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="card-header-bar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-primary mb-1">RuangLomba</h2>
                <div class="text-secondary small">Platform Informasi &amp; Pendaftaran Kompetisi Terpadu</div>
            </div>
            <div class="text-end">
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6 rounded-pill">
                    <i class="fa-solid fa-circle-check me-1"></i> TERVERIFIKASI
                </span>
                <div class="small text-secondary mt-1">No. Reg: #RL-REG-<?= str_pad($reg['id'], 5, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>

        <div class="text-center mb-4">
            <h4 class="fw-bold text-uppercase mb-1">KARTU TANDA BUKTI PENDAFTARAN RESMI</h4>
            <p class="text-secondary small">Dokumen ini merupakan bukti keikutsertaan sah yang diterbitkan oleh sistem RuangLomba.</p>
        </div>

        <!-- Section 1: Informasi Peluang -->
        <div class="p-3 bg-light rounded-3 mb-4 border">
            <div class="meta-label mb-1">Kompetisi / Acara</div>
            <h4 class="fw-bold text-dark mb-2"><?= esc($reg['comp_title']) ?></h4>
            <div class="row g-2 text-secondary small">
                <div class="col-md-4">
                    <strong>Penyelenggara:</strong> <?= esc($reg['organizer_name']) ?>
                </div>
                <div class="col-md-4">
                    <strong>Tipe:</strong> <?= esc($reg['comp_type']) ?> &middot; <?= esc($reg['education_level']) ?>
                </div>
                <div class="col-md-4">
                    <strong>Pelaksanaan:</strong> <?= esc($reg['implementation_type']) ?> (<?= esc($reg['location'] ?: 'Online') ?>)
                </div>
            </div>
        </div>

        <!-- Section 2: Data Peserta -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="meta-label">Nama Peserta</div>
                <div class="meta-value"><?= esc($reg['participant_name']) ?></div>
            </div>

            <div class="col-md-6">
                <div class="meta-label">Email Terdaftar</div>
                <div class="meta-value"><?= esc($reg['participant_email']) ?></div>
            </div>

            <div class="col-md-6">
                <div class="meta-label">Nomor Kontak / WhatsApp</div>
                <div class="meta-value"><?= esc($reg['phone']) ?></div>
            </div>

            <div class="col-md-6">
                <div class="meta-label">Sekolah / Instansi</div>
                <div class="meta-value"><?= esc($reg['institution']) ?></div>
            </div>

            <?php if (! empty($reg['team_name'])): ?>
            <div class="col-12">
                <div class="p-2 px-3 bg-primary-subtle text-primary border border-primary-subtle rounded-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="small fw-semibold text-uppercase">Terdaftar Sebagai Tim:</span>
                        <div class="fw-bold fs-6"><?= esc($reg['team_name']) ?></div>
                    </div>
                    <span class="badge bg-primary">Beregu</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section 3: Verifikasi & Catatan -->
        <div class="row g-3 align-items-center pt-3 border-top">
            <div class="col-md-7">
                <div class="small text-secondary">
                    <div><strong>Waktu Pendaftaran:</strong> <?= date('d F Y, H:i', strtotime($reg['registered_at'])) ?> WIB</div>
                    <div><strong>Waktu Verifikasi:</strong> <?= date('d F Y, H:i', strtotime($reg['verified_at'] ?? $reg['registered_at'])) ?> WIB</div>
                    <div class="mt-2 fst-italic">Simpan dan tunjukkan kartu ini pada saat daftar ulang atau teknikal meeting kegiatan.</div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="stamp-box">
                    <i class="fa-solid fa-stamp text-success fs-3 mb-1"></i>
                    <div class="fw-bold text-success">DIVALIDASI SISTEM</div>
                    <div class="text-secondary" style="font-size: 11px;">RUANGLOMBA OFFICIAL VERIFICATION</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
