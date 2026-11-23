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

$howFeatured = $featured_opportunities[0] ?? null;
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
<?php if ($isLoggedIn && ! empty($lomba_match)): ?>
<section class="section pt-0" id="lombamatch">
    <div class="container">
        <div class="match-section match-showcase reveal">
            <div class="row g-4 g-xl-5 align-items-center">
                <div class="col-lg-6 match-copy">
                    <span class="match-eyebrow"><i class="fa-solid fa-wand-magic-sparkles"></i> Rekomendasi personal</span>
                    <h2>Lebih dekat dengan peluang yang tepat.</h2>
                    <p>LombaMatch AI menyusun rekomendasi dari minat, keahlian, jenjang, dan preferensi yang kamu atur—agar pencarian terasa lebih terarah.</p>

                    <div class="match-benefits">
                        <div><i class="fa-solid fa-circle-check"></i> Disesuaikan dengan preferensimu</div>
                        <div><i class="fa-solid fa-circle-check"></i> Hanya menampilkan peluang aktif</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="<?= base_url('profil') ?>" class="btn btn-light btn-sm">
                            <i class="fa-solid fa-sliders me-1"></i> Atur preferensi
                        </a>
                        <a href="<?= base_url('cari') ?>" class="btn btn-outline-light btn-sm">
                            <i class="fa-solid fa-arrow-right me-1"></i> Lihat semua peluang
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="match-board">
                        <div class="match-board-head">
                            <div>
                                <span class="match-board-kicker">Profil kecocokan</span>
                                <h3>Preferensimu</h3>
                            </div>
                            <span class="match-board-icon"><i class="fa-solid fa-sliders"></i></span>
                        </div>

                        <div class="match-preferences">
                            <div class="match-preference">
                                <span><i class="fa-solid fa-heart"></i> Minat</span>
                                <strong><?= esc($lomba_match['interests_text']) ?></strong>
                            </div>
                            <div class="match-preference">
                                <span><i class="fa-solid fa-graduation-cap"></i> Jenjang</span>
                                <strong><?= esc($lomba_match['level_text']) ?></strong>
                            </div>
                            <div class="match-preference">
                                <span><i class="fa-solid fa-laptop"></i> Mode</span>
                                <strong><?= esc($lomba_match['mode_text']) ?></strong>
                            </div>
                            <div class="match-preference">
                                <span><i class="fa-solid fa-wallet"></i> Biaya</span>
                                <strong><?= esc($lomba_match['fee_text']) ?></strong>
                            </div>
                        </div>

                        <div class="match-recommendation-head">
                            <div>
                                <h4>Rekomendasi untukmu</h4>
                                <p><?= ! empty($lomba_match['has_preferences']) ? 'Diurutkan berdasarkan kecocokan profil' : 'Atur preferensi untuk hasil yang lebih personal' ?></p>
                            </div>
                            <span class="match-count"><?= count($lomba_match['matches']) ?> pilihan</span>
                        </div>

                        <?php if (! empty($lomba_match['matches'])): ?>
                            <div class="match-results">
                                <?php foreach ($lomba_match['matches'] as $m): ?>
                                <a href="<?= base_url('peluang/' . $m['id']) ?>" class="match-result">
                                    <span class="match-result-thumb">
                                        <?php if (! empty($m['poster'])): ?>
                                        <img src="<?= esc($m['poster'], 'attr') ?>" alt="" loading="lazy">
                                        <?php else: ?>
                                        <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </span>
                                    <span class="match-result-content">
                                        <strong class="match-result-title"><?= esc($m['title']) ?></strong>
                                        <span class="match-result-meta"><?= esc($m['category_name']) ?> · <?= esc($m['implementation_type']) ?> · <?= esc($m['fee_label']) ?></span>
                                        <?php if (! empty($m['matched_skills'])): ?>
                                        <span class="match-skill-match"><i class="fa-solid fa-check"></i> Keahlian cocok: <?= esc(implode(', ', $m['matched_skills'])) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="match-result-score">
                                        <?= ! empty($lomba_match['has_preferences']) ? (int) $m['pct'] . '%' : 'Aktif' ?>
                                        <small><?= ! empty($lomba_match['has_preferences']) ? 'cocok' : 'peluang' ?></small>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="match-empty">
                                <i class="fa-solid fa-compass"></i>
                                Belum ada lomba yang sedang aktif sesuai filter saat ini.
                            </div>
                        <?php endif; ?>
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
            <p>Dari menemukan kesempatan sampai siap mendaftar, semuanya dimulai di RuangLomba.</p>
        </div>

        <div class="row how-layout g-4 align-items-stretch">
            <div class="col-lg-5 reveal">
                <div class="how-steps" role="tablist" aria-label="Cara menggunakan RuangLomba" data-how-tabs>
                    <button class="how-step is-active" id="how-step-0" type="button" role="tab"
                        aria-selected="true" aria-controls="how-panel-0" tabindex="0" data-how-tab="0">
                        <span class="how-step-index">01</span>
                        <span class="how-step-copy">
                            <strong>Jelajahi peluang</strong>
                            <span>Telusuri lomba, beasiswa, webinar, dan lainnya.</span>
                        </span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <button class="how-step" id="how-step-1" type="button" role="tab"
                        aria-selected="false" aria-controls="how-panel-1" tabindex="-1" data-how-tab="1">
                        <span class="how-step-index">02</span>
                        <span class="how-step-copy">
                            <strong>Pilih yang paling pas</strong>
                            <span>Cek deadline, biaya, jenjang, dan syaratnya.</span>
                        </span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <button class="how-step" id="how-step-2" type="button" role="tab"
                        aria-selected="false" aria-controls="how-panel-2" tabindex="-1" data-how-tab="2">
                        <span class="how-step-index">03</span>
                        <span class="how-step-copy">
                            <strong>Siapkan pendaftaran</strong>
                            <span>Ikuti panduan pendaftaran dari penyelenggara.</span>
                        </span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-7 reveal reveal-delay-2">
                <div class="how-preview" aria-live="polite">
                    <div class="how-panel is-active" id="how-panel-0" role="tabpanel"
                        aria-labelledby="how-step-0" tabindex="0" data-how-panel="0">
                        <span class="how-panel-eyebrow">Langkah 1 · Jelajahi</span>
                        <h3>Semua peluang, satu pencarian.</h3>
                        <p>Gunakan kategori dan filter untuk mempersempit pilihanmu.</p>
                        <div class="how-search-preview">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Cari lomba, beasiswa, webinar...</span>
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div class="how-filter-chips">
                            <span><i class="fa-solid fa-trophy"></i> Lomba</span>
                            <span><i class="fa-solid fa-graduation-cap"></i> Beasiswa</span>
                            <span><i class="fa-solid fa-display"></i> Webinar</span>
                        </div>
                    </div>

                    <div class="how-panel" id="how-panel-1" role="tabpanel"
                        aria-labelledby="how-step-1" tabindex="0" data-how-panel="1" hidden>
                        <span class="how-panel-eyebrow">Langkah 2 · Pilih</span>
                        <h3>Periksa detail sebelum memutuskan.</h3>
                        <p>Lihat informasi penting dan pastikan peluang sesuai dengan kebutuhanmu.</p>
                        <?php if ($howFeatured): ?>
                        <a href="<?= base_url('peluang/' . $howFeatured['id']) ?>" class="how-opportunity">
                            <span class="how-opportunity-image">
                                <?php if (! empty($howFeatured['image'])): ?>
                                <img src="<?= esc($howFeatured['image'], 'attr') ?>" alt="" loading="lazy">
                                <?php else: ?>
                                <i class="fa-solid fa-trophy"></i>
                                <?php endif; ?>
                            </span>
                            <span class="how-opportunity-info">
                                <span class="how-opportunity-type"><?= esc($howFeatured['type']) ?></span>
                                <strong><?= esc($howFeatured['title']) ?></strong>
                                <span><?= esc($howFeatured['lokasi']) ?> · <?= esc($howFeatured['harga']) ?></span>
                                <span><?= esc($howFeatured['tanggal']) ?></span>
                            </span>
                            <i class="fa-solid fa-arrow-up-right-from-square how-opportunity-arrow"></i>
                        </a>
                        <?php else: ?>
                        <div class="how-no-opportunity">Peluang terbaru akan tampil di sini setelah disetujui admin.</div>
                        <?php endif; ?>
                    </div>

                    <div class="how-panel" id="how-panel-2" role="tabpanel"
                        aria-labelledby="how-step-2" tabindex="0" data-how-panel="2" hidden>
                        <span class="how-panel-eyebrow">Langkah 3 · Daftar</span>
                        <div class="how-ready-icon"><i class="fa-solid fa-paper-plane"></i></div>
                        <h3>Kesempatan berikutnya bisa jadi milikmu.</h3>
                        <p>Ikuti cara pendaftaran yang tertera pada detail peluang dan pantau statusmu.</p>
                        <a href="<?= base_url($isLoggedIn ? 'cari' : 'daftar') ?>" class="btn btn-primary">
                            <?= $isLoggedIn ? 'Cari peluang sekarang' : 'Mulai sebagai peserta' ?>
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
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
            <span class="proof-eyebrow"><i class="fa-solid fa-chart-line"></i> Aktivitas nyata</span>
            <h2>Dampak RuangLomba, dari data yang berjalan.</h2>
            <p>Statistik komunitas di bawah ini diambil langsung dari aktivitas platform.</p>
        </div>

        <?php
        $proofStats = [
            [
                'icon'  => 'fa-user-graduate',
                'value' => $stats['users'],
                'label' => 'Peserta terdaftar',
                'hint'  => 'Akun peserta di RuangLomba',
            ],
            [
                'icon'  => 'fa-bullseye',
                'value' => $stats['active'],
                'label' => 'Peluang aktif',
                'hint'  => 'Sudah disetujui dan belum melewati deadline',
            ],
            [
                'icon'  => 'fa-circle-check',
                'value' => $stats['verified_registrations'],
                'label' => 'Pendaftaran terverifikasi',
                'hint'  => 'Dikonfirmasi oleh penyelenggara',
            ],
        ];
        ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($proofStats as $i => $proof): ?>
            <div class="col">
                <article class="proof-card reveal reveal-delay-<?= $i + 1 ?>">
                    <span class="proof-card-icon"><i class="fa-solid <?= esc($proof['icon'], 'attr') ?>"></i></span>
                    <span class="proof-card-value"><?= esc($proof['value']) ?></span>
                    <h3><?= esc($proof['label']) ?></h3>
                    <p><?= esc($proof['hint']) ?></p>
                    <span class="proof-card-source"><i class="fa-solid fa-database"></i> Data langsung RuangLomba</span>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="proof-note"><i class="fa-solid fa-circle-info me-1"></i> RuangLomba belum menyimpan kutipan testimoni pengguna. Bagian ini menampilkan angka aktivitas asli, bukan ulasan yang dibuat-buat.</p>
    </div>
</section>

<script>
(function() {
    var tablist = document.querySelector('[data-how-tabs]');
    if (!tablist) return;

    var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[data-how-tab]'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('[data-how-panel]'));

    function activate(index, moveFocus) {
        tabs.forEach(function(tab, i) {
            var active = i === index;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.setAttribute('tabindex', active ? '0' : '-1');
            panels[i].classList.toggle('is-active', active);
            panels[i].hidden = !active;
        });

        if (moveFocus) tabs[index].focus();
    }

    tabs.forEach(function(tab, index) {
        tab.addEventListener('click', function() {
            activate(index, false);
        });

        tab.addEventListener('keydown', function(event) {
            var nextIndex = index;
            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                nextIndex = (index + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                nextIndex = (index - 1 + tabs.length) % tabs.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = tabs.length - 1;
            } else {
                return;
            }

            event.preventDefault();
            activate(nextIndex, true);
        });
    });
})();
</script>

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