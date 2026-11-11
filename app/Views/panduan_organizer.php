<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Ganti dengan route daftar/masuk organizer kalau sudah dibuat
$daftarUrl = base_url('daftar');
?>

<style>
/* ================================
   UNTUK ORGANIZER (DISAMAKAN DENGAN EXPLORE PAGE)
================================= */
.org-page {
    --x-deep: #001233;
    --x-navy: #00246B;
    --x-mid: #0B3D91;
    --x-teal: #14B8A6;
    --x-teal-dark: #0F766E;
    --x-aqua: #5FD8E0;
    --x-light: #CADCFC;
    --x-dim: #E8EDFF;
    --x-gray: #6B7280;
    --x-border: #E5E7EB;
    --x-ease: cubic-bezier(0.16, 1, 0.3, 1);

    position: relative;
    overflow: hidden;
    padding: 55px 0 90px;
    background: linear-gradient(165deg, #FFFFFF 0%, #F0F4FE 42%, #E5EDFC 70%, #FFFFFF 100%);
}

.org-page::before,
.org-page::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}

.org-page::before {
    width: 380px;
    height: 380px;
    top: -140px;
    right: -90px;
    background: radial-gradient(circle, var(--x-light) 0%, transparent 70%);
    opacity: .6;
}

.org-page::after {
    width: 340px;
    height: 340px;
    bottom: -150px;
    left: -90px;
    background: radial-gradient(circle, var(--x-aqua) 0%, transparent 70%);
    opacity: .2;
}

.org-wrap {
    position: relative;
    z-index: 1;
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

/* ================================
   HEADER
================================= */
.org-header {
    margin-bottom: 48px;
    max-width: 760px;
}

.org-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
}

.org-eyebrow i {
    font-size: 12px;
}

.org-header h1 {
    margin: 0;
    color: #111111;
    font-size: clamp(36px, 5vw, 54px);
    line-height: 1.12;
    letter-spacing: -0.01em;
    font-weight: 400;
}

.org-header h1 span {
    font-style: italic;
    background: linear-gradient(100deg, var(--x-navy) 0%, var(--x-teal) 40%, var(--x-aqua) 60%, var(--x-navy) 100%);
    background-size: 260% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    animation: orgShimmer 7s linear infinite;
}

@keyframes orgShimmer {
    0% {
        background-position: 0% 50%;
    }

    100% {
        background-position: 200% 50%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .org-header h1 span {
        animation: none;
    }
}

.org-header p {
    max-width: 640px;
    margin: 16px 0 0;
    color: var(--x-gray);
    font-size: 16px;
    line-height: 1.7;
}

.org-header .btns {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 28px;
}

/* Tombol Custom menyesuaikan btn-primary bawaan */
.btn-org-primary {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    padding: 0 28px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(0, 36, 107, .22), inset 0 1px 0 rgba(255, 255, 255, .15);
    transition: transform .35s ease, box-shadow .35s ease, background .35s ease;
}

.btn-org-primary:hover {
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid), var(--x-aqua));
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 36, 107, .3), 0 0 22px rgba(95, 216, 224, .25), inset 0 1px 0 rgba(255, 255, 255, .2);
    color: #fff;
}

.btn-org-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    padding: 0 24px;
    border-radius: 999px;
    border: 1.5px solid var(--x-border);
    color: var(--x-gray);
    background: #fff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: border-color .25s ease, color .25s ease, transform .25s ease;
}

.btn-org-outline:hover {
    border-color: var(--x-navy);
    color: var(--x-navy);
    transform: translateY(-2px);
}

/* ================================
   SECTION
================================= */
.org-section {
    margin-top: 64px;
}

.org-section-header {
    margin-bottom: 28px;
}

.org-section-title {
    margin: 0 0 10px;
    color: #111111;
    font-size: clamp(28px, 3.4vw, 38px);
    line-height: 1.15;
    font-weight: 400;
}

.org-section-sub {
    max-width: 640px;
    margin: 0;
    color: var(--x-gray);
    font-size: 15.5px;
    line-height: 1.7;
}

/* Base Card disamakan dengan border-radius Explore */
.org-card {
    background: #fff;
    border: 1.5px solid rgba(255, 255, 255, .7);
    border-radius: 24px;
    box-shadow: 0 24px 50px -30px rgba(0, 36, 107, .25);
    transition: transform .4s var(--x-ease), box-shadow .4s var(--x-ease);
}

/* ================================
   LANGKAH (STEPS)
================================= */
.org-steps {
    padding: 12px 32px;
}

.org-step {
    position: relative;
    display: flex;
    gap: 24px;
    padding: 28px 0;
}

.org-step+.org-step {
    border-top: 1px solid var(--x-border);
}

.org-step:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 23px;
    top: 82px;
    bottom: -28px;
    width: 1px;
    background: var(--x-border);
}

.org-step-num {
    position: relative;
    z-index: 1;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid));
    color: #fff;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 19px;
    box-shadow: 0 10px 22px -10px rgba(0, 36, 107, .5);
}

.org-step h3 {
    margin: 2px 0 8px;
    color: #111;
    font-family: 'Inter', sans-serif;
    font-size: 17px;
    font-weight: 700;
}

.org-step p {
    margin: 0;
    color: #4B5563;
    font-size: 14.5px;
    line-height: 1.75;
}

.org-step ul {
    margin: 12px 0 0;
    padding-left: 18px;
    color: #4B5563;
    font-size: 14px;
    line-height: 1.8;
}

/* ================================
   DATA YANG DISIAPKAN (GRID)
================================= */
.org-fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.org-field {
    padding: 26px 28px;
}

.org-field h3 {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 16px;
    color: #111;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 700;
}

.org-field h3 i {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 15px;
}

.org-field ul {
    margin: 0;
    padding-left: 20px;
    color: #4B5563;
    font-size: 14.5px;
    line-height: 1.85;
}

/* Chips dimiripkan dengan gaya tag Explore */
.org-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.org-chip {
    padding: 6px 14px;
    border-radius: 999px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 13px;
    font-weight: 600;
    transition: background .3s, color .3s;
}

.org-chip.teal {
    background: rgba(20, 184, 166, .12);
    color: var(--x-teal-dark);
}

.badge-req {
    display: inline-block;
    margin-left: 8px;
    padding: 3px 10px;
    border-radius: 999px;
    background: #FEE2E2;
    color: #B91C1C;
    font-size: 11px;
    font-weight: 700;
    vertical-align: middle;
}

.badge-opt {
    background: #F3F4F6;
    color: var(--x-gray);
}

/* ================================
   STATUS GRID
================================= */
.org-status {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
}

.org-status-item {
    padding: 24px;
}

.org-status-item .dot {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
}

.dot.pending {
    background: #FEF3C7;
    color: #92400E;
}

.dot.approved {
    background: rgba(20, 184, 166, .14);
    color: var(--x-teal-dark);
}

.dot.rejected {
    background: #FEE2E2;
    color: #B91C1C;
}

.dot.expired {
    background: #F3F4F6;
    color: var(--x-gray);
}

.org-status-item p {
    margin: 14px 0 0;
    color: #4B5563;
    font-size: 14px;
    line-height: 1.7;
}

/* ================================
   AKORDEON (SYARAT & KETENTUAN)
================================= */
.org-acc .accordion-item {
    margin-bottom: 14px;
    border: 1.5px solid var(--x-border);
    border-radius: 18px !important;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 12px -8px rgba(0, 36, 107, .08);
}

.org-acc .accordion-button {
    gap: 14px;
    padding: 22px 24px;
    background: #fff;
    color: #111;
    font-size: 20px;
    font-weight: 400;
    box-shadow: none;
    transition: background .3s, color .3s;
}

.org-acc .accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #F4F7FF, var(--x-dim));
    color: var(--x-navy);
}

.org-acc .accordion-button:focus {
    box-shadow: 0 0 0 4px var(--x-dim);
    border-color: var(--x-light);
}

.org-acc .accordion-button .no {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 13px;
    font-weight: 700;
}

.org-acc .accordion-body {
    padding: 16px 24px 24px 68px;
    color: #4B5563;
    font-size: 15px;
    line-height: 1.85;
}

.org-acc .accordion-body ul {
    margin: 12px 0 0;
    padding-left: 20px;
}

/* ================================
   CTA BOTTOM
================================= */
.org-cta {
    position: relative;
    overflow: hidden;
    margin-top: 72px;
    padding: 64px 40px;
    border-radius: 28px;
    background: linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
    color: #fff;
    text-align: center;
    box-shadow: 0 40px 80px -40px rgba(0, 18, 51, .7);
}

.org-cta::before {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    top: -180px;
    right: -120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(95, 216, 224, .35), transparent 70%);
    pointer-events: none;
}

.org-cta h2 {
    position: relative;
    margin: 0;
    color: #fff;
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 400;
}

.org-cta p {
    position: relative;
    max-width: 520px;
    margin: 16px auto 0;
    color: rgba(255, 255, 255, .78);
    font-size: 16px;
    line-height: 1.7;
}

.org-cta .btns {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 14px;
    margin-top: 32px;
}

.btn-light-ruang {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    padding: 0 28px;
    background: #fff;
    color: var(--x-navy);
    border: 0;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: transform .25s, background .25s;
}

.btn-light-ruang:hover {
    background: var(--x-light);
    color: var(--x-navy);
    transform: translateY(-2px);
}

.btn-outline-light-ruang {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    padding: 0 24px;
    background: transparent;
    color: #fff;
    border: 1.5px solid rgba(255, 255, 255, .4);
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: transform .25s, background .25s, border-color .25s;
}

.btn-outline-light-ruang:hover {
    background: rgba(255, 255, 255, .1);
    border-color: #fff;
    transform: translateY(-2px);
}

/* ================================
   RESPONSIVE
================================= */
@media (max-width: 991px) {
    .org-status {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .org-fields {
        grid-template-columns: 1fr;
    }

    .org-header h1 {
        font-size: 38px;
    }

    .org-steps {
        padding: 8px 20px;
    }

    .org-step:not(:last-child)::before {
        left: 23px;
        top: 78px;
    }

    .org-acc .accordion-body {
        padding-left: 24px;
    }

    .org-cta {
        padding: 48px 24px;
        border-radius: 24px;
    }

    .org-cta .btns {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
        margin: 28px auto 0;
    }

    .btn-light-ruang,
    .btn-outline-light-ruang {
        width: 100%;
    }

    .org-header .btns {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
    }

    .btn-org-primary,
    .btn-org-outline {
        width: 100%;
    }
}

@media (max-width: 575px) {
    .org-page {
        padding: 36px 0 60px;
    }

    .org-wrap {
        width: min(100% - 24px, 1180px);
    }

    .org-status {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="org-page">
    <div class="org-wrap">

        <!-- HEADER -->
        <div class="org-header">
            <div class="org-eyebrow"><i class="fa-solid fa-bullhorn"></i> Untuk Organizer</div>
            <h1>Buat lombamu <span>ditemukan</span>.</h1>
            <p>
                Punya lomba, beasiswa, webinar, atau event lain? Ikuti langkah di bawah ini agar peluangmu
                tampil di RuangLomba dan dilihat oleh pelajar di seluruh Indonesia.
            </p>
            <div class="btns">
                <a href="<?= esc($daftarUrl, 'attr') ?>" class="btn-org-primary">
                    Daftar sebagai Organizer <span style="margin-left:8px;">&rarr;</span>
                </a>
                <a href="#syarat" class="btn-org-outline">Baca Syarat &amp; Ketentuan</a>
            </div>
        </div>

        <!-- LANGKAH -->
        <div class="org-section" style="margin-top:0;">
            <div class="org-section-header">
                <h2 class="org-section-title">Lima langkah menayangkan peluang.</h2>
                <p class="org-section-sub">Dari membuat akun sampai peluangmu tampil di halaman Jelajahi Peluang.</p>
            </div>

            <div class="org-card org-steps">
                <div class="org-step">
                    <span class="org-step-num">1</span>
                    <div>
                        <h3>Buat akun dan masuk sebagai Organizer</h3>
                        <p>Daftar dengan email aktif, lalu pilih peran sebagai penyelenggara. Gunakan nama resmi
                            lembaga, komunitas, atau panitia karena nama ini yang tampil sebagai
                            <em>Diselenggarakan oleh</em>.
                        </p>
                    </div>
                </div>

                <div class="org-step">
                    <span class="org-step-num">2</span>
                    <div>
                        <h3>Lengkapi profil penyelenggara</h3>
                        <p>Profil kamu ditampilkan di bagian bawah halaman detail setiap peluang.</p>
                        <ul>
                            <li>Nama penyelenggara dan logo</li>
                            <li>Deskripsi singkat tentang organisasimu</li>
                            <li>Alamat, Instagram, dan website</li>
                        </ul>
                    </div>
                </div>

                <div class="org-step">
                    <span class="org-step-num">3</span>
                    <div>
                        <h3>Isi data peluang</h3>
                        <p>Lengkapi formulir peluang dengan informasi yang akurat. Daftar lengkap kolom yang perlu
                            diisi ada di bagian <a href="#data" style="color:var(--x-navy); font-weight:600;">Data yang
                                perlu disiapkan</a>.</p>
                    </div>
                </div>

                <div class="org-step">
                    <span class="org-step-num">4</span>
                    <div>
                        <h3>Tunggu verifikasi admin</h3>
                        <p>Setiap pengajuan berstatus <strong>Menunggu</strong> sampai diperiksa admin. Peluang yang
                            belum disetujui tidak tampil di halaman publik.</p>
                    </div>
                </div>

                <div class="org-step">
                    <span class="org-step-num">5</span>
                    <div>
                        <h3>Peluang tayang dan dipantau</h3>
                        <p>Setelah disetujui, peluangmu muncul di halaman Jelajahi Peluang. Jumlah suka dan jumlah
                            dilihat ikut bertambah, dan peluang dengan peminat terbanyak berkesempatan masuk
                            <em>Lomba pilihan</em> di beranda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATA YANG DISIAPKAN -->
        <div class="org-section" id="data">
            <div class="org-section-header">
                <h2 class="org-section-title">Data yang perlu disiapkan.</h2>
                <p class="org-section-sub">Siapkan informasi ini sebelum mengisi formulir supaya pengajuan tidak
                    bolak-balik
                    direvisi.</p>
            </div>

            <div class="org-fields">
                <div class="org-card org-field">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Informasi utama <span class="badge-req">Wajib</span>
                    </h3>
                    <ul>
                        <li>Judul peluang</li>
                        <li>Deskripsi lengkap (tujuan, hadiah, alur, kontak)</li>
                        <li>Jenis dan kategori</li>
                        <li>Poster</li>
                    </ul>
                </div>

                <div class="org-card org-field">
                    <h3><i class="fa-solid fa-users"></i> Peserta dan pelaksanaan <span class="badge-req">Wajib</span>
                    </h3>
                    <ul>
                        <li>Jenjang: SD, SMP, SMA/SMK/MA, Mahasiswa, atau Umum</li>
                        <li>Tingkat: Nasional atau Internasional</li>
                        <li>Mode: Online, Offline, atau Hybrid</li>
                        <li>Lokasi (untuk Offline dan Hybrid)</li>
                    </ul>
                </div>

                <div class="org-card org-field">
                    <h3><i class="fa-solid fa-calendar-days"></i> Waktu dan biaya <span class="badge-req">Wajib</span>
                    </h3>
                    <ul>
                        <li>Batas akhir pendaftaran (deadline)</li>
                        <li>Tanggal pelaksanaan</li>
                        <li>Biaya pendaftaran (isi 0 jika gratis)</li>
                        <li>Link pendaftaran yang aktif</li>
                    </ul>
                </div>

                <div class="org-card org-field">
                    <h3><i class="fa-solid fa-circle-plus"></i> Pelengkap <span
                            class="badge-req badge-opt">Opsional</span></h3>
                    <ul>
                        <li>Total hadiah</li>
                        <li>Persyaratan peserta</li>
                        <li>Link media sosial dan website peluang</li>
                    </ul>
                </div>
            </div>

            <div class="org-card org-field" style="margin-top:20px;">
                <h3><i class="fa-solid fa-layer-group"></i> Jenis peluang yang bisa dipublikasikan</h3>
                <div class="org-chips" style="margin-bottom:24px;">
                    <?php foreach ($types as $t): ?>
                    <span class="org-chip"><?= esc($t) ?></span>
                    <?php endforeach; ?>
                </div>

                <h3><i class="fa-solid fa-tag"></i> Kategori yang tersedia</h3>
                <div class="org-chips">
                    <?php foreach ($categories as $c): ?>
                    <span class="org-chip teal"><?= esc($c['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- STATUS -->
        <div class="org-section">
            <div class="org-section-header">
                <h2 class="org-section-title">Arti status pengajuan.</h2>
                <p class="org-section-sub">Kamu bisa memantau posisi peluangmu lewat status berikut.</p>
            </div>

            <div class="org-status">
                <div class="org-card org-status-item">
                    <span class="dot pending"><i class="fa-solid fa-hourglass-half"></i> Menunggu</span>
                    <p>Pengajuan sudah diterima dan sedang diperiksa admin. Belum tampil di publik.</p>
                </div>
                <div class="org-card org-status-item">
                    <span class="dot approved"><i class="fa-solid fa-circle-check"></i> Disetujui</span>
                    <p>Peluang lolos verifikasi dan tampil di halaman Jelajahi Peluang.</p>
                </div>
                <div class="org-card org-status-item">
                    <span class="dot rejected"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                    <p>Pengajuan tidak memenuhi ketentuan. Perbaiki datanya, lalu ajukan kembali.</p>
                </div>
                <div class="org-card org-status-item">
                    <span class="dot expired"><i class="fa-solid fa-clock"></i> Berakhir</span>
                    <p>Deadline sudah lewat sehingga pendaftaran dinyatakan tutup.</p>
                </div>
            </div>
        </div>

        <!-- SYARAT & KETENTUAN -->
        <div class="org-section" id="syarat">
            <div class="org-section-header">
                <h2 class="org-section-title">Syarat &amp; ketentuan.</h2>
                <p class="org-section-sub">Dengan menayangkan peluang di RuangLomba, penyelenggara menyetujui ketentuan
                    berikut.</p>
            </div>

            <div class="accordion org-acc" id="accSyarat">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#s1"
                            aria-expanded="true" aria-controls="s1">
                            <span class="no">1</span> Kelayakan penyelenggara
                        </button>
                    </h3>
                    <div id="s1" class="accordion-collapse collapse show" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Penyelenggara adalah lembaga, institusi pendidikan, komunitas, perusahaan, atau panitia
                            yang dapat dihubungi dan bertanggung jawab atas peluang yang dipublikasikan. Satu
                            akun mewakili satu penyelenggara.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s2" aria-expanded="false" aria-controls="s2">
                            <span class="no">2</span> Keaslian dan keakuratan informasi
                        </button>
                    </h3>
                    <div id="s2" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Seluruh data harus benar, terkini, dan dapat dipertanggungjawabkan.
                            <ul>
                                <li>Hadiah, jadwal, dan persyaratan sesuai dengan kondisi sebenarnya.</li>
                                <li>Deadline berada di masa depan saat peluang diajukan.</li>
                                <li>Perubahan jadwal atau ketentuan wajib diperbarui oleh penyelenggara.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s3" aria-expanded="false" aria-controls="s3">
                            <span class="no">3</span> Biaya pendaftaran yang transparan
                        </button>
                    </h3>
                    <div id="s3" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Biaya pendaftaran ditulis dengan jelas pada kolom biaya, dan isi 0 untuk peluang gratis.
                            Dilarang menarik biaya tambahan yang tidak dicantumkan di halaman peluang.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s4" aria-expanded="false" aria-controls="s4">
                            <span class="no">4</span> Link pendaftaran yang aktif dan aman
                        </button>
                    </h3>
                    <div id="s4" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Link pendaftaran harus diawali <code>http://</code> atau <code>https://</code>, dapat
                            dibuka, dan mengarah ke halaman pendaftaran resmi. Pendaftaran dilakukan langsung di
                            situs atau formulir penyelenggara, bukan di RuangLomba.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s5" aria-expanded="false" aria-controls="s5">
                            <span class="no">5</span> Poster dan hak cipta
                        </button>
                    </h3>
                    <div id="s5" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Poster dan logo yang diunggah adalah milik penyelenggara atau digunakan dengan izin
                            pemiliknya. Gunakan gambar yang jelas dan tidak mengandung unsur yang melanggar hak
                            cipta pihak lain.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s6" aria-expanded="false" aria-controls="s6">
                            <span class="no">6</span> Konten yang dilarang
                        </button>
                    </h3>
                    <div id="s6" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Peluang akan ditolak atau diturunkan apabila mengandung:
                            <ul>
                                <li>Unsur SARA, ujaran kebencian, atau kekerasan</li>
                                <li>Konten pornografi atau perjudian</li>
                                <li>Penipuan, skema piramida, atau janji hadiah yang tidak jelas</li>
                                <li>Informasi pribadi pihak lain tanpa izin</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s7" aria-expanded="false" aria-controls="s7">
                            <span class="no">7</span> Proses verifikasi
                        </button>
                    </h3>
                    <div id="s7" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            Admin berhak memeriksa, menyetujui, menolak, atau menurunkan peluang yang tidak sesuai
                            dengan ketentuan, kapan pun diperlukan. Peluang yang ditolak dapat diajukan kembali
                            setelah diperbaiki.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#s8" aria-expanded="false" aria-controls="s8">
                            <span class="no">8</span> Tanggung jawab penyelenggara
                        </button>
                    </h3>
                    <div id="s8" class="accordion-collapse collapse" data-bs-parent="#accSyarat">
                        <div class="accordion-body">
                            RuangLomba adalah platform penyebaran informasi. Pelaksanaan lomba, penilaian,
                            pembagian hadiah, serta komunikasi dengan peserta sepenuhnya menjadi tanggung jawab
                            penyelenggara.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="org-cta">
            <h2>Siap menayangkan peluangmu?</h2>
            <p>Buat akun organizer, lengkapi profil, dan ajukan peluang pertamamu hari ini.</p>
            <div class="btns">
                <a href="<?= esc($daftarUrl, 'attr') ?>" class="btn-light-ruang">
                    Daftar sebagai Organizer <span style="margin-left:8px;">&rarr;</span>
                </a>
                <a href="<?= base_url('cari') ?>" class="btn-outline-light-ruang">Lihat peluang yang sudah tayang</a>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>