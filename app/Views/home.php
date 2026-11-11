<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/* ==========================================================================
   Helper tampilan (lokal untuk view ini)
   ========================================================================== */

// Ikon kategori peluang (Font Awesome 6, dimuat di layouts/main.php). Key = nama kategori huruf kecil.
$catIcons = [
    'lomba'     => 'fa-trophy',
    'beasiswa'  => 'fa-graduation-cap',
    'webinar'   => 'fa-display',
    'workshop'  => 'fa-screwdriver-wrench',
    'seminar'   => 'fa-microphone-lines',
    'magang'    => 'fa-briefcase',
    'volunteer' => 'fa-hand-holding-heart',
];
$catIconDefault = 'fa-star';

// Alias nama lain -> key ikon
$catAlias = [
    'kompetisi' => 'lomba', 'pelatihan' => 'workshop', 'konferensi' => 'seminar',
    'internship' => 'magang', 'relawan' => 'volunteer', 'kegiatan' => 'seminar',
];

// Ikon kategori minat (Font Awesome 6). Key = nama kategori huruf kecil.
$interestIcons = [
    'teknologi' => 'fa-code',
    'desain'    => 'fa-pen-ruler',
    'seni'      => 'fa-palette',
    'sains'     => 'fa-flask',
    'bisnis'    => 'fa-chart-line',
    'menulis'   => 'fa-pen-nib',
    'olahraga'  => 'fa-futbol',
    'fotografi' => 'fa-camera',
    'video'     => 'fa-video',
    'umum'      => 'fa-shapes',
];
$interestIconDefault = 'fa-star';

// Testimoni sementara statis. Ganti dengan data controller ($testimonials) bila tabelnya sudah ada.
$testimonials = $testimonials ?? [
    ['name' => 'Nama Pengguna 1', 'role' => 'Siswa SMA · Surabaya',   'quote' => 'Isi testimoni pengguna pertama di sini.', 'rating' => 5],
    ['name' => 'Nama Pengguna 2', 'role' => 'Mahasiswa · Universitas', 'quote' => 'Isi testimoni pengguna kedua di sini.',   'rating' => 5],
    ['name' => 'Nama Pengguna 3', 'role' => 'Penyelenggara Lomba',     'quote' => 'Isi testimoni pengguna ketiga di sini.',  'rating' => 5],
];
?>

<!-- ==========================================================================
     1. HERO
     ========================================================================== -->
<section class="hero">
    <div class="hero-illustration"
        style="background-image:url('<?= base_url('assets/images/hero-illustration.jpg') ?>');" aria-hidden="true">
    </div>
    <div class="hero-fade" aria-hidden="true"></div>

    <div class="container">
        <div class="hero-copy reveal">
            <h1>
                Temukan <span class="hl">Lomba</span>.
                Kembangkan <span class="hl">Potensi</span>.
                Raih <span class="hl">Prestasi</span>.
            </h1>

            <p class="desc">
                Temukan berbagai lomba dan event yang sesuai dengan minat, keahlian, dan tujuanmu dalam satu platform.
            </p>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="<?= base_url('cari') ?>" class="btn btn-primary">Jelajahi Peluang</a>
                <a href="<?= base_url('cari?type=Lomba') ?>" class="btn btn-outline-primary">Top Lomba</a>
            </div>
        </div>

        <div class="hero-stats reveal">
            <div class="hero-stat">
                <span class="ico">
                    <i class="fa-solid fa-building"></i>
                </span>
                <div>
                    <div class="num"><?= esc($stats['organizers']) ?></div>
                    <div class="cap">Penyelenggara</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="ico">
                    <i class="fa-solid fa-user-graduate"></i>
                </span>
                <div>
                    <div class="num"><?= esc($stats['users']) ?></div>
                    <div class="cap">Pengguna Terdaftar</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="ico">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
                <div>
                    <div class="num"><?= esc($stats['total']) ?></div>
                    <div class="cap">Peluang</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="ico">
                    <i class="fa-solid fa-bolt"></i>
                </span>
                <div>
                    <div class="num"><?= esc($stats['active']) ?></div>
                    <div class="cap">Peluang Aktif</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     2. KATEGORI PELUANG — kartu ikon (tanpa gambar)
     ========================================================================== -->
<section class="section section-gradient" id="kategori">
    <div class="container">
        <div class="section-head reveal">
            <h2>Satu ruang, ribuan kesempatan.</h2>
            <p>Temukan berbagai kesempatan yang sesuai dengan minat, kemampuan, dan tujuanmu.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($opportunity_categories as $i => $cat): ?>
            <?php
                $key  = mb_strtolower(trim($cat['name']));
                $key  = $catAlias[$key] ?? $key;
                $icon = $catIcons[$key] ?? $catIconDefault;
                ?>
            <div class="col">
                <a href="<?= base_url('cari') . '?' . http_build_query(['type' => $cat['name']]) ?>"
                    class="card card-ruang cat-card h-100 text-decoration-none reveal reveal-delay-<?= ($i % 3) + 1 ?>">
                    <span class="cat-card-watermark" aria-hidden="true"><?= esc($cat['num']) ?></span>

                    <div class="card-body">
                        <div class="cat-card-top">
                            <span class="cat-card-icon" aria-hidden="true">
                                <i class="fa-solid <?= esc($icon, 'attr') ?>"></i>
                            </span>
                            <span class="cat-card-count"><?= (int) $cat['total'] ?> peluang</span>
                        </div>

                        <h3 class="cat-card-name"><?= esc($cat['name']) ?></h3>
                        <p class="cat-card-desc"><?= esc($cat['desc']) ?></p>

                        <span class="cat-card-arrow">Jelajahi <span>&rarr;</span></span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================================================
     3. LOMBA PILIHAN
     ========================================================================== -->
<section class="section" id="featured">
    <div class="container">
        <div class="section-head reveal">
            <h2>Lomba pilihan.</h2>
            <p>Temukan lomba menarik yang mungkin cocok untukmu.</p>
        </div>

        <?php if (empty($featured_opportunities)): ?>
        <p class="text-center text-secondary py-5">Belum ada lomba pilihan saat ini.</p>
        <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($featured_opportunities as $opp): ?>
            <?php $closed = $opp['hdays'] < 0; ?>
            <div class="col">
                <a href="<?= base_url('peluang/' . $opp['id']) ?>"
                    class="card card-ruang h-100 text-decoration-none reveal">

                    <div class="opp-thumb">
                        <?php if (!empty($opp['image'])): ?>
                        <img src="<?= esc($opp['image'], 'attr') ?>" alt="<?= esc($opp['title']) ?>" loading="lazy">
                        <?php endif; ?>
                        <span class="opp-hbadge<?= $closed ? ' is-closed' : '' ?>">
                            <?= $closed ? 'Ditutup' : 'H-' . esc($opp['hdays']) ?>
                        </span>
                    </div>

                    <div class="opp-stats">
                        <span class="opp-stat">
                            <i class="fa-regular fa-heart"></i>
                            <?= esc($opp['likes']) ?> suka
                        </span>
                        <span class="opp-stat">
                            <i class="fa-regular fa-eye"></i>
                            <?= esc($opp['views']) ?>x dilihat
                        </span>
                    </div>

                    <div class="opp-detail-btn">
                        Lihat Detail
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>

                    <div class="card-body">
                        <span class="opp-tag"><?= esc($opp['type']) ?></span>
                        <h3 class="opp-title"><?= esc($opp['title']) ?></h3>

                        <ul class="opp-info">
                            <li>
                                <i class="fa-solid fa-graduation-cap"></i>
                                <?= esc($opp['jenjang']) ?>
                            </li>
                            <li>
                                <i class="fa-solid fa-tag"></i>
                                <?= esc($opp['harga']) ?>
                            </li>
                            <li>
                                <i class="fa-solid fa-location-dot"></i>
                                <?= esc($opp['lokasi']) ?>
                            </li>
                            <li>
                                <i class="fa-regular fa-calendar"></i>
                                <?= esc($opp['tanggal']) ?>
                            </li>
                        </ul>

                        <div class="opp-organizer">
                            <span class="opp-org-avatar"><?= esc($opp['organizer_initial']) ?></span>
                            <div>
                                <div class="opp-org-label">Diselenggarakan oleh</div>
                                <div class="opp-org-name"><?= esc($opp['organizer']) ?></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ==========================================================================
     4. LOMBAMATCH AI — hanya untuk user yang sudah login
     ========================================================================== -->
<?php if ($isLoggedIn): ?>
<section class="section pt-0" id="lombamatch">
    <div class="container">
        <div class="match-section reveal">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 match-copy">
                    <h2>Bingung memilih lomba?<br>Temukan lomba yang cocok untukmu dengan
                        <span class="accent accent-light">LombaMatch AI</span>.
                    </h2>
                    <p>Ceritakan minat dan keahlianmu, dan kami bantu tunjukkan peluang yang paling relevan denganmu.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="match-panel">
                        <div class="match-field">
                            <div class="label">Minat</div>
                            <div class="value">Desain Grafis &middot; UI/UX</div>
                        </div>
                        <div class="match-field">
                            <div class="label">Keahlian</div>
                            <div class="value">Figma &middot; Photoshop</div>
                        </div>
                        <div class="match-field">
                            <div class="label">Biaya</div>
                            <div class="value">Gratis</div>
                        </div>
                        <div class="match-field">
                            <div class="label">Mode</div>
                            <div class="value">Online</div>
                        </div>

                        <a href="#" class="btn btn-primary">Temukan Lomba</a>

                        <div class="match-result" style="display:none;">
                            <span class="match-result-badge">Direkomendasikan</span>
                            <div>
                                <div class="match-result-title">UI/UX Design Competition</div>
                                <div class="match-result-meta">UI/UX &amp; Design &middot; Online &middot; Gratis</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ==========================================================================
     5. BERDASARKAN MINAT — panel navy + kartu kaca
     ========================================================================== -->
<section class="section" id="minat">
    <div class="container">
        <div class="interest-panel reveal">
            <span class="interest-glow g1" aria-hidden="true"></span>
            <span class="interest-glow g2" aria-hidden="true"></span>

            <div class="interest-head">
                <span class="interest-eyebrow">Kategori Minat</span>
                <h2>Temukan berdasarkan <span class="accent">minat</span>.</h2>
                <p>Pilih bidang yang kamu suka, kami tunjukkan peluang yang paling relevan.</p>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                <?php foreach ($interest_categories as $i => $interest): ?>
                <?php
                    $icon = $interestIcons[mb_strtolower(trim($interest['name']))] ?? $interestIconDefault;
                    $num  = $interest['num'] ?? sprintf('%02d', $i + 1);
                    ?>
                <div class="col">
                    <a href="<?= base_url('cari?category=' . (int) $interest['id']) ?>"
                        class="interest-card h-100 reveal reveal-delay-<?= ($i % 3) + 1 ?>">
                        <span class="interest-top">
                            <span class="interest-num"><?= esc($num) ?></span>
                            <span class="interest-go" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
                        </span>

                        <span class="interest-ico" aria-hidden="true">
                            <i class="fa-solid <?= esc($icon, 'attr') ?>"></i>
                        </span>

                        <span class="interest-name"><?= esc($interest['name']) ?></span>
                        <?php if (isset($interest['total'])): ?>
                        <span class="interest-meta"><?= (int) $interest['total'] ?> peluang</span>
                        <?php endif; ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     6. CARA KERJA
     ========================================================================== -->
<section class="section" id="cara-kerja">
    <div class="container">
        <div class="section-head reveal">
            <h2>Mulai dalam tiga langkah.</h2>
        </div>

        <div class="row steps-layout align-items-center g-5">
            <div class="col-lg-5 steps-list reveal">
                <div class="step-item" data-step="1">
                    <span class="step-item-num">01</span>
                    <div>
                        <h3>Temukan</h3>
                        <p>Jelajahi peluang yang sesuai denganmu.</p>
                    </div>
                </div>
                <div class="step-item" data-step="2">
                    <span class="step-item-num">02</span>
                    <div>
                        <h3>Pilih</h3>
                        <p>Periksa detail, deadline, biaya, dan persyaratannya.</p>
                    </div>
                </div>
                <div class="step-item" data-step="3">
                    <span class="step-item-num">03</span>
                    <div>
                        <h3>Daftar</h3>
                        <p>Ikuti instruksi pendaftaran dari penyelenggara.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 steps-visual reveal reveal-delay-2">
                <div class="steps-mock">
                    <div class="mock-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Cari lomba, beasiswa, webinar...
                    </div>

                    <div class="mock-card">
                        <div class="mock-card-thumb"></div>
                        <div class="mock-card-body">
                            <span class="mock-tag">Lomba</span>
                            <div class="mock-title">UI/UX Design Competition</div>
                            <div class="mock-meta">Online &middot; Gratis &middot; Deadline 30 Sep 2026</div>
                        </div>
                    </div>

                    <div class="mock-btn">Daftar Sekarang</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     7. TESTIMONI
     ========================================================================== -->
<section class="section section-gradient" id="testimoni">
    <div class="container">
        <div class="section-head reveal">
            <h2>Kata mereka tentang RuangLomba.</h2>
            <p>Cerita dari pelajar dan penyelenggara yang sudah menemukan peluangnya.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($testimonials as $i => $t): ?>
            <div class="col">
                <figure class="card card-ruang testi-card h-100 reveal reveal-delay-<?= ($i % 3) + 1 ?>">
                    <div class="card-body">
                        <div class="testi-stars" aria-label="Rating <?= (int) $t['rating'] ?> dari 5">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="fa-solid fa-star <?= $s <= (int) $t['rating'] ? 'on' : '' ?>"
                                aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>

                        <blockquote class="testi-quote">&ldquo;<?= esc($t['quote']) ?>&rdquo;</blockquote>

                        <figcaption class="testi-author">
                            <span class="testi-avatar"><?= esc(mb_strtoupper(mb_substr($t['name'], 0, 1))) ?></span>
                            <div>
                                <div class="testi-name"><?= esc($t['name']) ?></div>
                                <div class="testi-role"><?= esc($t['role']) ?></div>
                            </div>
                        </figcaption>
                    </div>
                </figure>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================================================
     8. ORGANIZER
     ========================================================================== -->
<section class="organizer" id="organizer">
    <div class="organizer-bg" style="background-image:url('<?= base_url('assets/images/organizer-bg.png') ?>');"
        aria-hidden="true"></div>
    <div class="organizer-overlay" aria-hidden="true"></div>

    <div class="container reveal">
        <h2>Punya lomba atau event?<br>Buat lombamu ditemukan.</h2>
        <p>Publikasikan lomba dan berbagai event-mu agar dapat ditemukan oleh orang yang tepat.</p>
        <a href="<?= base_url('untuk-organizer') ?>" class="btn btn-primary">Publikasikan</a>
    </div>
</section>

<!-- ==========================================================================
     9. MOTIVASI
     ========================================================================== -->
<section class="motivate">
    <div class="motivate-glow g1"></div>
    <div class="motivate-glow g2"></div>

    <div class="container reveal">
        <h2>Satu kesempatan bisa menjadi awal dari <span class="accent">sesuatu yang besar</span>.</h2>
        <p>Mulai dari satu lomba, satu webinar, satu beasiswa, atau satu pengalaman baru.</p>
    </div>
</section>

<!-- ==========================================================================
     10. FOOTER
     ========================================================================== -->
<footer>
    <div class="container" id="tentang">
        <div class="row g-4 pb-5">

            <!-- Brand -->
            <div class="col-lg-4 footer-brand">
                <div class="logo">RuangLomba</div>
                <p class="footer-tagline">Temukan peluang. Buka jalanmu.</p>
                <p class="footer-desc">
                    RuangLomba adalah platform yang mempertemukan pelajar dan mahasiswa dengan beragam peluang, mulai
                    dari lomba, beasiswa, webinar, workshop, hingga seminar, dalam satu tempat. Penyelenggara juga bisa
                    mempublikasikan acaranya agar ditemukan oleh peserta yang tepat.
                </p>

                <div class="footer-social">
                    <a href="#" aria-label="Instagram" target="_blank" rel="noopener">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="LinkedIn" target="_blank" rel="noopener">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a href="#" aria-label="TikTok" target="_blank" rel="noopener">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                </div>
            </div>

            <!-- Jelajahi -->
            <div class="col-6 col-lg-2 footer-col">
                <h4>Jelajahi</h4>
                <ul>
                    <li><a href="<?= base_url('cari') ?>">Jelajahi Peluang</a></li>
                    <li><a href="<?= base_url('cari?type=Lomba') ?>">Lomba</a></li>
                    <li><a href="<?= base_url('cari?type=Beasiswa') ?>">Beasiswa</a></li>
                    <li><a href="<?= base_url('cari?type=Webinar') ?>">Webinar</a></li>
                    <li><a href="<?= base_url('cari?type=Workshop') ?>">Workshop</a></li>
                    <li><a href="<?= base_url('cari?type=Seminar') ?>">Seminar</a></li>
                </ul>
            </div>

            <!-- RuangLomba -->
            <div class="col-6 col-lg-2 footer-col">
                <h4>RuangLomba</h4>
                <ul>
                    <li><a href="<?= base_url('tentang-kami') ?>">Tentang Kami</a></li>
                    <li><a href="<?= base_url('#kategori') ?>">Kategori Peluang</a></li>
                    <li><a href="<?= base_url('#testimoni') ?>">Testimoni</a></li>
                    <li><a href="<?= base_url('bantuan') ?>">Bantuan</a></li>
                    <li><a href="<?= base_url('syarat-ketentuan') ?>">Syarat &amp; Ketentuan</a></li>
                    <li><a href="<?= base_url('kebijakan-privasi') ?>">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <!-- Organizer + Kontak + Peta -->
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    <div class="col-6 footer-col">
                        <h4>Organizer</h4>
                        <ul>
                            <li><a href="<?= base_url('untuk-organizer') ?>">Publikasikan</a></li>
                            <li><a href="<?= base_url('bantuan#faq-untuk-organizer') ?>">Bantuan Organizer</a></li>
                        </ul>
                    </div>

                    <div class="col-6 footer-col">
                        <h4>Kontak</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="fa-regular fa-envelope"></i>
                                <a href="mailto:halo@ruanglomba.id">halo@ruanglomba.id</a>
                            </li>
                            <li>
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Surabaya, Indonesia</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12">
                        <div class="footer-map">
                            <iframe src="https://www.google.com/maps?q=Surabaya,+Indonesia&output=embed"
                                title="Lokasi RuangLomba" loading="lazy" allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> RuangLomba. All rights reserved.</span>
            <span class="footer-bottom-note">Dibuat untuk pelajar Indonesia.</span>
        </div>
    </div>
</footer>

<?= $this->endSection() ?>