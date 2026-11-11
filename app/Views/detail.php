<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$deadlineTs = strtotime($competition['deadline']);
$daysLeft   = (int) floor(($deadlineTs - strtotime('today')) / 86400);
$isClosed   = $daysLeft < 0;
$isFree     = (int) $competition['registration_fee'] === 0;
$desc       = trim($competition['description'] ?? '');

$regLink = trim($competition['registration_link'] ?? '');
if (!preg_match('#^https?://#i', $regLink)) {
    $regLink = '';
}
$regMode    = $competition['registration_mode'] ?? 'external';
$userRole   = session()->get('userRole');
$isLoggedIn = (bool) session()->get('isLoggedIn');
$myReg      = $myRegistration ?? null;
$isBookmarked = $isBookmarked ?? false;
$likesCount   = (int) ($competition['likes'] ?? 0);
$viewsCount   = (int) ($competition['views'] ?? 0);
$regStatusMap = [
    'pending'  => 'Menunggu verifikasi',
    'verified' => 'Pendaftaran terverifikasi',
    'rejected' => 'Pendaftaran ditolak',
];

$orgName    = $competition['organizer_name'] ?? '-';
$orgInitial = strtoupper(mb_substr($orgName, 0, 1));
$orgLogo    = media_url($competition['organizer_logo'] ?? '', 'assets/images/organizers/');
$orgBio     = trim($competition['organizer_bio'] ?? '');
$orgAddr    = trim($competition['organizer_address'] ?? '');
$orgIg      = $links['instagram'] ?? '';
$orgWeb     = $links['website'] ?? '';
?>

<style>
.detail-page {
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
    min-height: 100vh;
    padding: 36px 0 90px;
    background: linear-gradient(165deg, #FFFFFF 0%, #F0F4FE 42%, #E5EDFC 70%, #FFFFFF 100%);
}

.detail-page::before,
.detail-page::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}

.detail-page::before {
    width: 380px;
    height: 380px;
    top: -140px;
    right: -90px;
    background: radial-gradient(circle, var(--x-light) 0%, transparent 70%);
    opacity: .6;
}

.detail-page::after {
    width: 340px;
    height: 340px;
    bottom: -150px;
    left: -90px;
    background: radial-gradient(circle, var(--x-aqua) 0%, transparent 70%);
    opacity: .2;
}

.detail-wrap {
    position: relative;
    z-index: 1;
    width: min(1240px, calc(100% - 40px));
    margin: 0 auto;
}

.detail-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    padding: 9px 16px;
    border-radius: 999px;
    border: 1.5px solid var(--x-border);
    background: #fff;
    color: var(--x-gray);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: border-color .25s ease, color .25s ease, transform .25s ease;
}

.detail-back:hover {
    border-color: var(--x-navy);
    color: var(--x-navy);
    transform: translateX(-3px);
}

/* ================================
   LAYOUT UTAMA
================================= */
.detail-layout {
    display: grid;
    grid-template-columns: 290px minmax(0, 1fr);
    gap: 36px;
    align-items: start;
}

/* ================================
   KIRI: REKOMENDASI
================================= */
.rec-title {
    margin: 0 0 16px;
    color: #111111;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 24px;
    font-weight: 400;
}

.rec-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.rec-card {
    position: relative;
    display: block;
    overflow: hidden;
    background: #fff;
    border: 1px solid var(--x-border);
    border-radius: 16px;
    box-shadow: 0 14px 32px -22px rgba(0, 36, 107, .2);
    color: inherit;
    text-decoration: none;
    transition: transform .4s var(--x-ease), box-shadow .4s var(--x-ease), border-color .4s var(--x-ease);
}

.rec-card:hover {
    transform: translateY(-5px);
    border-color: var(--x-navy);
    box-shadow: 0 24px 44px -20px rgba(0, 36, 107, .3);
    color: inherit;
}

.rec-poster {
    position: relative;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid), var(--x-light));
}

.rec-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .6s var(--x-ease);
}

.rec-card:hover .rec-poster img {
    transform: scale(1.06);
}

.rec-poster .poster-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, .9);
    font-size: 34px;
    background:
        radial-gradient(circle at 20% 20%, rgba(95, 216, 224, .45), transparent 28%),
        linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
}

.rec-type {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .92);
    color: var(--x-navy);
    font-size: 11px;
    font-weight: 700;
    box-shadow: 0 6px 14px rgba(0, 18, 51, .12);
}

.rec-body {
    padding: 14px 16px 16px;
}

.rec-cat {
    color: var(--x-teal-dark);
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.rec-name {
    margin: 6px 0 10px;
    color: #111111;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 18px;
    line-height: 1.3;
    font-weight: 400;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rec-name a {
    color: inherit;
    text-decoration: none;
}

.rec-name a::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 2;
}

.rec-meta {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rec-meta li {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    color: var(--x-gray);
    font-size: 12.5px;
}

.rec-meta li i {
    width: 14px;
    flex-shrink: 0;
    text-align: center;
    color: var(--x-navy);
    font-size: 12px;
}

.rec-meta li span {
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rec-empty {
    padding: 28px 18px;
    text-align: center;
    border: 1.5px dashed var(--x-light);
    border-radius: 16px;
    background: rgba(255, 255, 255, .75);
    color: var(--x-gray);
    font-size: 13.5px;
}

/* ================================
   KANAN: POSTER + INFO
================================= */
.detail-main {
    min-width: 0;
}

.detail-top {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 32px;
    align-items: stretch;
    /* poster mengikuti tinggi kolom info */
}

.detail-poster {
    position: relative;
    min-height: 420px;
    overflow: hidden;
    border-radius: 18px;
    border: 1px solid var(--x-border);
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid), var(--x-light));
    box-shadow: 0 24px 50px -28px rgba(0, 36, 107, .4);
}

.detail-poster img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}

.detail-poster .poster-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, .9);
    font-size: 72px;
    background:
        radial-gradient(circle at 20% 20%, rgba(95, 216, 224, .45), transparent 28%),
        linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
}

.detail-info {
    min-width: 0;
}

.detail-title {
    margin: 0;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--x-border);
    color: #111111;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: clamp(26px, 3vw, 36px);
    line-height: 1.2;
    font-weight: 400;
    letter-spacing: -0.01em;
}

.detail-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 18px 0 4px;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 999px;
    border: 1.5px solid var(--x-border);
    background: #fff;
    color: var(--x-navy);
    font-size: 13px;
    font-weight: 600;
}

.tag.is-cat {
    color: var(--x-teal-dark);
    background: rgba(20, 184, 166, .1);
    border-color: transparent;
}

.detail-facts {
    list-style: none;
    margin: 18px 0 0;
    padding: 0 0 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    border-bottom: 1px solid var(--x-border);
}

.detail-facts li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #374151;
    font-size: 14.5px;
    line-height: 1.5;
}

.detail-facts .ico {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 13px;
}

.detail-facts small {
    display: block;
    color: var(--x-gray);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.detail-facts strong {
    font-weight: 600;
    color: #111111;
}

.detail-facts .free {
    color: var(--x-teal-dark);
}

.detail-org-mini {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 0;
}

.avatar {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--x-navy), var(--x-aqua));
    color: #fff;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 18px;
}

.detail-org-mini small {
    display: block;
    color: var(--x-gray);
    font-size: 12px;
}

.detail-org-mini strong {
    font-size: 14.5px;
    font-weight: 600;
    color: #111111;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #E11D48, #F43F5E);
    box-shadow: 0 8px 18px -6px rgba(225, 29, 72, .5);
}

.status-chip.is-ok {
    background: rgba(20, 184, 166, .12);
    color: var(--x-teal-dark);
    box-shadow: none;
}

.status-chip.is-closed {
    background: #F3F4F6;
    color: var(--x-gray);
    box-shadow: none;
}

.btn-detail {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    height: 52px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 36, 107, .22), inset 0 1px 0 rgba(255, 255, 255, .15);
    transition: transform .35s ease, box-shadow .35s ease, background .35s ease;
}

.btn-detail:hover {
    color: #fff;
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid), var(--x-aqua));
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 36, 107, .3), 0 0 22px rgba(95, 216, 224, .25);
}

.btn-detail.is-disabled {
    background: #E5E7EB;
    color: var(--x-gray);
    box-shadow: none;
    pointer-events: none;
}

.btn-detail.is-registered {
    background: rgba(20, 184, 166, .12);
    color: var(--x-teal-dark);
    box-shadow: none;
    pointer-events: none;
}

.btn-detail.is-rejected {
    background: #FEE2E2;
    color: #B91C1C;
    box-shadow: none;
    pointer-events: none;
}

/* ================================
   DESKRIPSI
================================= */
.desc-box {
    margin-top: 28px;
    padding: 26px 30px;
    background: #F0E2DE;
    border-radius: 18px;
    color: #111111;
    font-size: 15px;
    line-height: 1.75;
    white-space: pre-line;
    overflow-wrap: anywhere;
}

.desc-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 22px;
}

.link-report {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--x-navy);
    font-size: 14.5px;
    font-weight: 600;
    text-decoration: underline;
}

.link-report:hover {
    color: var(--x-mid);
}

.desc-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
    padding: 0 20px;
    border: 1.5px solid var(--x-navy);
    border-radius: 999px;
    background: #fff;
    color: var(--x-navy);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .25s ease, color .25s ease, transform .25s ease;
}

.btn-outline:hover {
    background: var(--x-navy);
    color: #fff;
    transform: translateY(-2px);
}

/* Tombol Simpan (suka + simpan) */
.btn-save .save-count {
    min-width: 26px;
    padding: 2px 8px;
    border-radius: 999px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    transition: background .25s ease, color .25s ease;
}

.btn-save.is-saved {
    background: var(--x-navy);
    border-color: var(--x-navy);
    color: #fff;
}

.btn-save.is-saved:hover {
    background: var(--x-mid);
    border-color: var(--x-mid);
}

.btn-save.is-saved .save-count {
    background: rgba(255, 255, 255, .18);
    color: #fff;
}

.btn-save.is-pop i {
    animation: savePop .45s var(--x-ease);
}

@keyframes savePop {
    0% {
        transform: scale(1);
    }

    40% {
        transform: scale(1.55);
    }

    100% {
        transform: scale(1);
    }
}

.btn-outline.is-disabled {
    opacity: .55;
    cursor: not-allowed;
    pointer-events: none;
}

.detail-views {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-right: 6px;
    color: var(--x-gray);
    font-size: 13px;
}

/* ================================
   PROFIL PENYELENGGARA
================================= */
.org-heading {
    margin: 34px 0 16px;
    color: #111111;
    font-family: 'Inter', sans-serif;
    font-size: 21px;
    font-weight: 700;
}

.org-profile {
    padding: 24px 26px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 6px 26px rgba(0, 18, 51, .12);
}

.org-profile-name {
    margin: 0;
    color: var(--x-navy);
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 700;
}

.org-profile-logo {
    display: block;
    max-width: 260px;
    max-height: 110px;
    width: auto;
    height: auto;
    margin: 22px 0;
    object-fit: contain;
}

.org-profile-bio {
    margin: 18px 0 0;
    color: #1f2937;
    font-size: 15px;
    line-height: 1.75;
    white-space: pre-line;
}

.org-profile-meta {
    margin: 18px 0 0;
    color: #111111;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.org-profile-meta a {
    color: inherit;
    text-decoration: none;
}

.org-profile-meta a:hover {
    text-decoration: underline;
}

/* ================================
   POSTER ZOOM + LIGHTBOX
================================= */
.poster-zoom {
    position: absolute;
    inset: 0;
    padding: 0;
    border: 0;
    background: transparent;
    cursor: zoom-in;
}

.poster-zoom img {
    transition: transform .6s var(--x-ease);
}

.poster-zoom:hover img {
    transform: scale(1.03);
}

.poster-zoom-hint {
    position: absolute;
    right: 14px;
    bottom: 14px;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(0, 18, 51, .72);
    backdrop-filter: blur(6px);
    color: #fff;
    font-size: 12.5px;
    font-weight: 600;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity .3s var(--x-ease), transform .3s var(--x-ease);
}

.poster-zoom:hover .poster-zoom-hint,
.poster-zoom:focus-visible .poster-zoom-hint {
    opacity: 1;
    transform: translateY(0);
}

.poster-zoom:focus-visible {
    outline: 3px solid var(--x-aqua);
    outline-offset: -3px;
}

.lightbox {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 56px 20px 20px;
    background: rgba(0, 12, 36, .88);
    backdrop-filter: blur(8px);
    opacity: 0;
    visibility: hidden;
    transition: opacity .3s var(--x-ease), visibility .3s;
    cursor: zoom-out;
}

.lightbox.is-open {
    opacity: 1;
    visibility: visible;
}

.lightbox-img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 30px 80px rgba(0, 0, 0, .5);
    transform: scale(.94);
    transition: transform .35s var(--x-ease);
    cursor: default;
}

.lightbox.is-open .lightbox-img {
    transform: scale(1);
}

.lightbox-close {
    position: absolute;
    top: 14px;
    right: 16px;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, .14);
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    transition: background .25s ease, transform .25s ease;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, .28);
    transform: rotate(90deg);
}

/* ================================
   RESPONSIVE
================================= */
@media (max-width: 991px) {
    .detail-layout {
        grid-template-columns: 1fr;
    }

    .detail-side {
        order: 2;
    }

    .rec-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .detail-top {
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .detail-poster {
        min-height: 0;
        aspect-ratio: 4 / 3;
    }
}

@media (max-width: 575px) {
    .detail-page {
        padding: 26px 0 60px;
    }

    .detail-wrap {
        width: min(100% - 24px, 1240px);
    }

    .desc-box,
    .org-profile {
        padding: 20px;
    }

    .rec-list {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="detail-page">
    <div class="detail-wrap">

        <a href="<?= base_url('cari') ?>" class="detail-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Jelajahi Peluang
        </a>

        <div class="detail-layout">

            <!-- ================= KIRI: REKOMENDASI ================= -->
            <aside class="detail-side">
                <h2 class="rec-title">Peluang serupa</h2>

                <?php if (!empty($related)): ?>
                <div class="rec-list">
                    <?php foreach ($related as $rec): ?>
                    <article class="rec-card">
                        <div class="rec-poster">
                            <?php if (!empty($rec['poster'])): ?>
                            <img src="<?= esc(media_url($rec['poster']), 'attr') ?>" alt="<?= esc($rec['title']) ?>"
                                loading="lazy">
                            <?php else: ?>
                            <div class="poster-placeholder"><i class="fa-solid fa-trophy"></i></div>
                            <?php endif; ?>
                            <span class="rec-type"><?= esc($rec['type']) ?></span>
                        </div>
                        <div class="rec-body">
                            <div class="rec-cat"><?= esc($rec['category_name']) ?></div>
                            <h3 class="rec-name">
                                <a href="<?= base_url('peluang/' . $rec['id']) ?>"><?= esc($rec['title']) ?></a>
                            </h3>
                            <ul class="rec-meta">
                                <li><i
                                        class="fa-solid fa-graduation-cap"></i><span><?= esc($rec['education_level']) ?></span>
                                </li>
                                <li><i
                                        class="fa-solid fa-wallet"></i><span><?= (int) $rec['registration_fee'] === 0 ? 'Gratis' : 'Rp ' . number_format($rec['registration_fee'], 0, ',', '.') ?></span>
                                </li>
                                <li><i class="fa-solid fa-location-dot"></i><span><?= esc($rec['implementation_type']) ?>
                                        &middot; <?= esc($rec['competition_level']) ?></span></li>
                                <li><i class="fa-solid fa-calendar-days"></i><span>Deadline
                                        <?= date('d M Y', strtotime($rec['deadline'])) ?></span></li>
                            </ul>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="rec-empty">Belum ada peluang serupa saat ini.</div>
                <?php endif; ?>
            </aside>
            <!-- ================= KANAN: KONTEN ================= -->
            <div class="detail-main">

                <div class="detail-top">

                    <!-- POSTER -->
                    <div class="detail-poster">
                        <?php if (!empty($competition['poster'])): ?>
                        <button type="button" class="poster-zoom" id="posterZoom" aria-label="Lihat poster penuh">
                            <img src="<?= esc(media_url($competition['poster']), 'attr') ?>"
                                alt="<?= esc($competition['title']) ?>" id="posterImg">
                            <span class="poster-zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i> Lihat
                                penuh</span>
                        </button>
                        <?php else: ?>
                        <div class="poster-placeholder"><i class="fa-solid fa-trophy"></i></div>
                        <?php endif; ?>
                    </div>

                    <!-- INFO -->
                    <div class="detail-info">

                        <h1 class="detail-title"><?= esc($competition['title']) ?></h1>

                        <div class="detail-tags">
                            <span class="tag"><?= esc($competition['type']) ?></span>
                            <span class="tag is-cat"><?= esc($competition['category_name']) ?></span>
                        </div>

                        <ul class="detail-facts">
                            <li>
                                <span class="ico"><i class="fa-solid fa-graduation-cap"></i></span>
                                <div>
                                    <small>Jenjang</small>
                                    <strong><?= esc($competition['education_level']) ?></strong>
                                </div>
                            </li>
                            <li>
                                <span class="ico"><i class="fa-solid fa-wallet"></i></span>
                                <div>
                                    <small>Biaya</small>
                                    <?php if ($isFree): ?>
                                    <strong class="free">Gratis</strong>
                                    <?php else: ?>
                                    <strong>Rp
                                        <?= number_format($competition['registration_fee'], 0, ',', '.') ?></strong>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li>
                                <span class="ico"><i class="fa-solid fa-location-dot"></i></span>
                                <div>
                                    <small>Pelaksanaan</small>
                                    <strong>
                                        <?= esc($competition['implementation_type']) ?>
                                        (<?= esc($competition['competition_level']) ?>)

                                        <?php 
                                            // Hanya tampilkan lokasi jika berbeda dengan tipe pelaksanaan
                                            if (!empty($competition['location']) && $competition['location'] !== $competition['implementation_type']): 
                                        ?>
                                        &middot; <?= esc($competition['location']) ?>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </li>
                            <li>
                                <span class="ico"><i class="fa-solid fa-calendar-days"></i></span>
                                <div>
                                    <small>Deadline pendaftaran</small>
                                    <strong><?= date('d M Y', $deadlineTs) ?></strong>
                                </div>
                            </li>
                        </ul>

                        <div class="detail-org-mini">
                            <span class="avatar"><?= esc($orgInitial) ?></span>
                            <div>
                                <small>Diselenggarakan oleh</small>
                                <strong><?= esc($orgName) ?></strong>
                            </div>
                        </div>

                        <?php if ($isClosed): ?>
                        <span class="status-chip is-closed"><i class="fa-solid fa-lock"></i> Pendaftaran ditutup</span>
                        <?php elseif ($daysLeft <= 7): ?>
                        <span class="status-chip">
                            <i class="fa-solid fa-fire"></i>
                            <?= $daysLeft === 0 ? 'Berakhir hari ini' : 'H-' . $daysLeft ?>
                        </span>
                        <?php else: ?>
                        <span class="status-chip is-ok"><i class="fa-solid fa-circle-check"></i> Pendaftaran
                            dibuka</span>
                        <?php endif; ?>

                        <?php if ($regMode === 'internal'): ?>

                        <?php if ($myReg): ?>
                        <span
                            class="btn-detail <?= $myReg['status'] === 'rejected' ? 'is-rejected' : 'is-registered' ?>">
                            <i
                                class="fa-solid <?= $myReg['status'] === 'rejected' ? 'fa-circle-xmark' : 'fa-circle-check' ?>"></i>
                            <?= esc($regStatusMap[$myReg['status']] ?? $myReg['status']) ?>
                        </span>
                        <?php elseif ($isClosed): ?>
                        <span class="btn-detail is-disabled">Pendaftaran Ditutup</span>
                        <?php elseif ($isLoggedIn && $userRole !== 'participant'): ?>
                        <span class="btn-detail is-disabled">Pendaftaran untuk peserta</span>
                        <?php else: ?>
                        <a href="<?= base_url('peluang/' . $competition['id'] . '/daftar') ?>" class="btn-detail">
                            <?= $isLoggedIn ? 'Daftar Sekarang' : 'Masuk untuk Mendaftar' ?>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>

                        <?php elseif (!$isClosed && $regLink !== ''): ?>
                        <a href="<?= esc($regLink, 'attr') ?>" target="_blank" rel="noopener noreferrer"
                            class="btn-detail">
                            Daftar Sekarang <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <?php else: ?>
                        <span class="btn-detail is-disabled">
                            <?= $isClosed ? 'Pendaftaran Ditutup' : 'Link Belum Tersedia' ?>
                        </span>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- DESKRIPSI -->
                <div class="desc-box"><?= esc($desc !== '' ? $desc : 'Penyelenggara belum menambahkan deskripsi.') ?>
                </div>

                <div class="desc-actions">
                    <a href="#" class="link-report">
                        <i class="fa-solid fa-circle-exclamation"></i> Laporkan Lomba
                    </a>

                    <div class="desc-buttons">
                        <span class="detail-views">
                            <i class="fa-regular fa-eye"></i>
                            <?= number_format($viewsCount, 0, ',', '.') ?>x dilihat
                        </span>

                        <?php if ($userRole === 'participant'): ?>
                        <button type="button" class="btn-outline btn-save<?= $isBookmarked ? ' is-saved' : '' ?>"
                            id="btnSave" data-url="<?= base_url('peluang/' . $competition['id'] . '/simpan') ?>"
                            data-csrf="<?= csrf_hash() ?>" data-likes="<?= $likesCount ?>"
                            aria-pressed="<?= $isBookmarked ? 'true' : 'false' ?>">
                            <i class="<?= $isBookmarked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                            <span class="save-label"><?= $isBookmarked ? 'Tersimpan' : 'Simpan' ?></span>
                            <span class="save-count"><?= number_format($likesCount, 0, ',', '.') ?></span>
                        </button>
                        <?php elseif (! $isLoggedIn): ?>
                        <a href="<?= base_url('masuk') ?>" class="btn-outline">
                            <i class="fa-regular fa-heart"></i> Simpan
                            <span class="save-count"><?= number_format($likesCount, 0, ',', '.') ?></span>
                        </a>
                        <?php else: ?>
                        <span class="btn-outline is-disabled" title="Fitur simpan hanya untuk peserta">
                            <i class="fa-regular fa-heart"></i> Simpan
                            <span class="save-count"><?= number_format($likesCount, 0, ',', '.') ?></span>
                        </span>
                        <?php endif; ?>

                        <button type="button" class="btn-outline" id="btnShare">
                            <i class="fa-solid fa-share-from-square"></i> Bagikan
                        </button>
                    </div>
                </div>

                <!-- PROFIL PENYELENGGARA -->
                <h2 class="org-heading">Profile Penyelenggara</h2>

                <div class="org-profile">
                    <h3 class="org-profile-name"><?= esc($orgName) ?></h3>

                    <?php if ($orgLogo !== ''): ?>
                    <img src="<?= esc($orgLogo, 'attr') ?>" alt="<?= esc($orgName) ?>" class="org-profile-logo">
                    <?php endif; ?>

                    <?php if ($orgBio !== ''): ?>
                    <p class="org-profile-bio"><?= esc($orgBio) ?></p>
                    <?php endif; ?>

                    <?php if ($orgAddr !== '' || $orgIg !== '' || $orgWeb !== ''): ?>
                    <div class="org-profile-meta">
                        <?php if ($orgAddr !== ''): ?>
                        <div>Alamat: <?= esc($orgAddr) ?></div>
                        <?php endif; ?>
                        <?php if ($orgIg !== ''): ?>
                        <div>Media Sosial Instagram:
                            <a href="<?= esc($orgIg, 'attr') ?>" target="_blank"
                                rel="noopener noreferrer"><?= esc($orgIg) ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($orgWeb !== ''): ?>
                        <div>Kunjungi Kami:
                            <a href="<?= esc($orgWeb, 'attr') ?>" target="_blank"
                                rel="noopener noreferrer"><?= esc($orgWeb) ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</section>

<?php if (!empty($competition['poster'])): ?>
<div class="lightbox" id="lightbox" aria-hidden="true">
    <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Tutup">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <img src="<?= esc(media_url($competition['poster']), 'attr') ?>" alt="<?= esc($competition['title']) ?>"
        class="lightbox-img">
</div>
<?php endif; ?>

<script>
document.getElementById('btnShare').addEventListener('click', function() {
    var btn = this;
    var data = {
        title: document.title,
        url: window.location.href
    };

    if (navigator.share) {
        navigator.share(data).catch(function() {});
        return;
    }

    navigator.clipboard.writeText(data.url).then(function() {
        var old = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Link disalin';
        setTimeout(function() {
            btn.innerHTML = old;
        }, 1800);
    });
});

(function() {
    var trigger = document.getElementById('posterZoom');
    var box = document.getElementById('lightbox');
    if (!trigger || !box) return;

    var closeBtn = document.getElementById('lightboxClose');

    function open() {
        box.classList.add('is-open');
        box.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function close() {
        box.classList.remove('is-open');
        box.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        trigger.focus();
    }

    trigger.addEventListener('click', open);
    closeBtn.addEventListener('click', close);

    box.addEventListener('click', function(e) {
        if (e.target === box) close();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && box.classList.contains('is-open')) close();
    });
})();

/* ===== Simpan / batal simpan tanpa reload ===== */
(function() {
    var btn = document.getElementById('btnSave');
    if (!btn) return;

    var icon = btn.querySelector('i');
    var label = btn.querySelector('.save-label');
    var countEl = btn.querySelector('.save-count');
    var nf = new Intl.NumberFormat('id-ID');

    var csrf = btn.dataset.csrf;
    var saved = btn.classList.contains('is-saved');
    var likes = parseInt(btn.dataset.likes, 10) || 0;
    var busy = false;

    function render() {
        btn.classList.toggle('is-saved', saved);
        btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
        icon.className = (saved ? 'fa-solid' : 'fa-regular') + ' fa-heart';
        label.textContent = saved ? 'Tersimpan' : 'Simpan';
        countEl.textContent = nf.format(likes);
    }

    // Toast kecil memakai gaya .rl-toast yang sudah ada
    function notify(msg) {
        var old = document.getElementById('rlToastJs');
        if (old) old.remove();

        var t = document.createElement('div');
        t.id = 'rlToastJs';
        t.className = 'rl-toast is-error';
        t.setAttribute('role', 'status');
        t.innerHTML = '<span class="rl-toast-ico"><i class="fa-solid fa-circle-exclamation"></i></span>' +
            '<div class="rl-toast-msg"></div>';
        t.querySelector('.rl-toast-msg').textContent = msg;
        document.body.appendChild(t);

        requestAnimationFrame(function() {
            t.classList.add('show');
        });
        setTimeout(function() {
            t.classList.remove('show');
            setTimeout(function() {
                t.remove();
            }, 350);
        }, 3500);
    }

    btn.addEventListener('click', function() {
        if (busy) return;
        busy = true;

        // Tampilan langsung berubah (optimistis); dikembalikan kalau server gagal
        var prevSaved = saved;
        var prevLikes = likes;
        saved = !saved;
        likes = Math.max(0, likes + (saved ? 1 : -1));
        render();

        btn.classList.remove('is-pop');
        void btn.offsetWidth; // restart animasi
        btn.classList.add('is-pop');

        fetch(btn.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                // Sesi habis: filter mengalihkan ke halaman masuk
                if (res.redirected) {
                    window.location.href = res.url;
                    throw new Error('redirect');
                }
                return res.json().catch(function() {
                        return {};
                    })
                    .then(function(data) {
                        return {
                            ok: res.ok,
                            data: data
                        };
                    });
            })
            .then(function(r) {
                if (r.data.csrf) csrf = r.data.csrf;
                if (!r.ok) throw new Error(r.data.message || 'Gagal menyimpan. Coba lagi.');

                // Samakan dengan nilai asli dari server
                saved = r.data.saved;
                likes = r.data.likes;
                render();
            })
            .catch(function(err) {
                if (err.message === 'redirect') return;
                saved = prevSaved;
                likes = prevLikes;
                render();
                notify(err.message || 'Koneksi bermasalah. Coba lagi.');
            })
            .finally(function() {
                busy = false;
            });
    });
})();
</script>

<?= $this->endSection() ?>