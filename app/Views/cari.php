<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
/* ================================
   CARI PELUANG
   (gaya kartu & grid ada di style.css, section 6d)
================================= */

.explore-page {
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
    padding: 55px 0 90px;
    background: linear-gradient(165deg, #FFFFFF 0%, #F0F4FE 42%, #E5EDFC 70%, #FFFFFF 100%);
}

.explore-page::before,
.explore-page::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}

.explore-page::before {
    width: 380px;
    height: 380px;
    top: -140px;
    right: -90px;
    background: radial-gradient(circle, var(--x-light) 0%, transparent 70%);
    opacity: .6;
}

.explore-page::after {
    width: 340px;
    height: 340px;
    bottom: -150px;
    left: -90px;
    background: radial-gradient(circle, var(--x-aqua) 0%, transparent 70%);
    opacity: .2;
}

.explore-wrap {
    position: relative;
    z-index: 1;
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

/* ================================
   HEADER
================================= */
.explore-header {
    margin-bottom: 32px;
}

.explore-eyebrow {
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

.explore-eyebrow i {
    font-size: 12px;
}

.explore-header h1 {
    margin: 0;
    color: #111111;
    font-size: clamp(36px, 5vw, 54px);
    line-height: 1.12;
    letter-spacing: -0.01em;
    font-weight: 400;
}

.explore-header h1 span {
    font-style: italic;
    background: linear-gradient(100deg, var(--x-navy) 0%, var(--x-teal) 40%, var(--x-aqua) 60%, var(--x-navy) 100%);
    background-size: 260% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    animation: exploreShimmer 7s linear infinite;
}

@keyframes exploreShimmer {
    0% {
        background-position: 0% 50%;
    }

    100% {
        background-position: 200% 50%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .explore-header h1 span {
        animation: none;
    }
}

.explore-header p {
    max-width: 640px;
    margin: 16px 0 0;
    color: var(--x-gray);
    font-size: 16px;
    line-height: 1.7;
}

/* ================================
   SEARCH (di-scope supaya tidak bentrok dengan .search-box di style.css)
================================= */
.explore-page .search-box {
    position: relative;
    display: block;
    gap: 0;
    padding: 0;
    margin-bottom: 18px;
    background: transparent;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}

.explore-page .search-box i {
    position: absolute;
    left: 22px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--x-gray);
    font-size: 16px;
    z-index: 2;
    transition: color .25s ease;
}

.explore-page .search-box:focus-within i {
    color: var(--x-navy);
}

.explore-page .search-box input {
    width: 100%;
    height: 60px;
    border: 1.5px solid transparent;
    border-radius: 999px;
    background: #fff;
    padding: 0 24px 0 54px;
    outline: none;
    color: #111111;
    font-size: 15px;
    box-shadow: 0 14px 34px -20px rgba(0, 36, 107, .3);
    transition: border-color .3s var(--x-ease), box-shadow .3s var(--x-ease);
}

.explore-page .search-box input:focus {
    border-color: var(--x-navy);
    box-shadow: 0 0 0 4px var(--x-dim), 0 14px 30px -16px rgba(0, 36, 107, .3);
}

.explore-page .search-box input::placeholder {
    color: #9CA3AF;
}

/* ================================
   FILTER PANEL
   (tanpa overflow:hidden supaya dropdown tidak terpotong)
================================= */
.filter-panel {
    position: relative;
    background: #fff;
    border: 1px solid rgba(255, 255, 255, .7);
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 38px;
    box-shadow: 0 34px 70px -34px rgba(0, 36, 107, .28);
}

.filter-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 24px;
    /* sama dengan radius panel */
    border-radius: 24px 24px 0 0;
    pointer-events: none;
    background: linear-gradient(90deg, var(--x-navy), var(--x-aqua), var(--x-teal));
    /* sisakan hanya 3px paling atas, mengikuti lengkung sudut */
    -webkit-mask: linear-gradient(#000 0 3px, transparent 3px);
    mask: linear-gradient(#000 0 3px, transparent 3px);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 13px;
}

.filter-item {
    position: relative;
}

/* ================================
   CUSTOM DROPDOWN
================================= */
.fd-native {
    display: none;
}

.fd {
    position: relative;
}

.fd.is-open {
    z-index: 30;
}

.fd-trigger {
    width: 100%;
    height: 58px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 14px 0 10px;
    border: 1.5px solid var(--x-border);
    border-radius: 16px;
    background: #fff;
    text-align: left;
    cursor: pointer;
    transition: border-color .25s ease, box-shadow .25s ease, background .25s ease, transform .25s ease;
}

.fd-trigger:hover {
    border-color: var(--x-light);
    transform: translateY(-2px);
    box-shadow: 0 14px 26px -16px rgba(0, 36, 107, .35);
}

.fd.is-open .fd-trigger,
.fd-trigger:focus-visible {
    outline: none;
    border-color: var(--x-navy);
    box-shadow: 0 0 0 4px var(--x-dim);
    transform: none;
}

.fd-icon {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: var(--x-dim);
    color: var(--x-navy);
    font-size: 14px;
    transition: background .3s ease, color .3s ease, transform .3s var(--x-ease);
}

.fd-trigger:hover .fd-icon {
    transform: scale(1.08) rotate(-4deg);
}

.fd-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.fd-label {
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--x-gray);
}

.fd-value {
    font-size: 13.5px;
    font-weight: 600;
    color: #111111;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fd-chevron {
    font-size: 11px;
    color: var(--x-gray);
    transition: transform .35s var(--x-ease), color .25s ease;
}

.fd.is-open .fd-chevron {
    transform: rotate(180deg);
    color: var(--x-navy);
}

/* filter yang sudah dipilih */
.fd.has-value .fd-trigger {
    background: linear-gradient(135deg, #F4F7FF, var(--x-dim));
    border-color: var(--x-light);
}

.fd.has-value .fd-icon {
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid) 60%, var(--x-aqua));
    color: #fff;
    box-shadow: 0 8px 16px -8px rgba(0, 36, 107, .5);
}

.fd.has-value .fd-value {
    color: var(--x-navy);
}

/* panel pilihan */
.fd-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    min-width: 100%;
    width: max-content;
    max-width: 280px;
    max-height: 272px;
    overflow-y: auto;
    padding: 8px;
    background: #fff;
    border: 1px solid var(--x-border);
    border-radius: 16px;
    box-shadow: 0 30px 60px -20px rgba(0, 36, 107, .35), 0 0 0 1px rgba(0, 36, 107, .03);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px) scale(.97);
    transform-origin: top left;
    transition: opacity .25s var(--x-ease), transform .3s var(--x-ease), visibility .25s;
    scrollbar-width: thin;
    scrollbar-color: var(--x-light) transparent;
}

.fd.is-open .fd-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.fd-option {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 10px 12px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #374151;
    font-size: 13.5px;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    transition: background .2s ease, color .2s ease, padding-left .25s var(--x-ease);
}

.fd-option i {
    font-size: 11px;
    color: var(--x-navy);
    opacity: 0;
    transform: scale(.5);
    transition: opacity .2s ease, transform .3s var(--x-ease);
}

.fd-option:hover,
.fd-option:focus-visible {
    outline: none;
    background: var(--x-dim);
    color: var(--x-navy);
    padding-left: 16px;
}

.fd-option.is-selected {
    background: linear-gradient(135deg, #F4F7FF, var(--x-dim));
    color: var(--x-navy);
    font-weight: 700;
}

.fd-option.is-selected i {
    opacity: 1;
    transform: scale(1);
}

.fd-option.is-placeholder {
    color: var(--x-gray);
}

/* dropdown nonaktif (Lokasi) */
.fd.is-disabled .fd-trigger {
    opacity: .55;
    cursor: not-allowed;
    background: #F9FAFB;
    border-color: var(--x-border);
    transform: none;
    box-shadow: none;
}

.fd.is-disabled .fd-icon {
    background: #EEF0F4;
    color: var(--x-gray);
    transform: none;
}

.fd.is-disabled .fd-value {
    color: var(--x-gray);
    font-weight: 500;
}

/* input lokasi */
.filter-item input {
    width: 100%;
    height: 58px;
    border: 1.5px solid var(--x-border);
    border-radius: 16px;
    padding: 0 14px 0 52px;
    color: #111111;
    font-size: 13.5px;
    font-weight: 600;
    outline: none;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3e%3cpath d='M12 21s7-6.3 7-11.5A7 7 0 005 9.5C5 14.7 12 21 12 21z' stroke='%2300246B' stroke-width='2' stroke-linejoin='round'/%3e%3ccircle cx='12' cy='9.5' r='2.4' stroke='%2300246B' stroke-width='2'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: 18px center;
    background-size: 18px;
    transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
}

.filter-item input::placeholder {
    color: #9CA3AF;
    font-weight: 500;
}

.filter-item input:hover {
    border-color: var(--x-light);
    transform: translateY(-2px);
    box-shadow: 0 14px 26px -16px rgba(0, 36, 107, .35);
}

.filter-item input:focus {
    border-color: var(--x-navy);
    box-shadow: 0 0 0 4px var(--x-dim);
    transform: none;
}

/* ================================
   FILTER ACTIONS
================================= */
.filter-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 18px;
}

.filter-info {
    color: var(--x-gray);
    font-size: 13px;
}

.filter-info i {
    color: var(--x-navy);
    margin-right: 4px;
}

.btn-filter {
    position: relative;
    overflow: hidden;
    height: 44px;
    border: 0;
    border-radius: 999px;
    padding: 0 24px;
    background: linear-gradient(135deg, var(--x-deep), var(--x-navy), var(--x-mid));
    color: #fff;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 36, 107, .22), inset 0 1px 0 rgba(255, 255, 255, .15);
    transition: transform .35s ease, box-shadow .35s ease, background .35s ease;
}

.btn-filter:hover {
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid), var(--x-aqua));
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 36, 107, .3), 0 0 22px rgba(95, 216, 224, .25), inset 0 1px 0 rgba(255, 255, 255, .2);
}

.btn-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 22px;
    border-radius: 999px;
    border: 1.5px solid var(--x-border);
    color: var(--x-gray);
    background: #fff;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    transition: border-color .25s ease, color .25s ease, transform .25s ease;
}

.btn-reset:hover {
    border-color: var(--x-navy);
    color: var(--x-navy);
    transform: translateY(-2px);
}

/* ================================
   RESULT HEADER
================================= */
.result-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 22px;
}

.result-header h2 {
    margin: 0;
    color: #111111;
    font-size: 28px;
    font-weight: 400;
}

.result-header p {
    margin: 4px 0 0;
    color: var(--x-gray);
    font-size: 13.5px;
}

/* ================================
   EMPTY
================================= */
.empty-state {
    grid-column: 1 / -1;
    padding: 70px 20px;
    text-align: center;
    border: 1.5px dashed var(--x-light);
    border-radius: 20px;
    background: rgba(255, 255, 255, .75);
}

.empty-state i {
    font-size: 42px;
    color: var(--x-light);
    margin-bottom: 15px;
}

.empty-state h3 {
    margin: 0 0 7px;
    color: #111111;
    font-size: 24px;
    font-weight: 400;
}

.empty-state p {
    margin: 0;
    color: var(--x-gray);
    font-size: 14px;
}

/* ================================
   PAGINATION
================================= */
.pagination-wrap {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.pagination-wrap nav {
    display: flex;
    gap: 6px;
}

.pagination-wrap a,
.pagination-wrap span {
    min-width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
    border-radius: 999px;
    border: 1.5px solid var(--x-border);
    background: #fff;
    color: var(--x-gray);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: border-color .25s ease, color .25s ease, transform .25s ease;
}

.pagination-wrap a:hover {
    border-color: var(--x-navy);
    color: var(--x-navy);
    transform: translateY(-2px);
}

.pagination-wrap .active span,
.pagination-wrap li.active a,
.pagination-wrap span[aria-current] {
    background: linear-gradient(135deg, var(--x-navy), var(--x-mid));
    border-color: transparent;
    color: #fff;
}

/* ================================
   RESPONSIVE
================================= */
@media (max-width: 1000px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 650px) {
    .explore-page {
        padding: 35px 0 60px;
    }

    .explore-wrap {
        width: min(100% - 24px, 1180px);
    }

    .explore-header h1 {
        font-size: 38px;
    }

    .filter-panel {
        padding: 18px;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .fd-menu {
        max-width: none;
        width: 100%;
    }

    .filter-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .filter-actions>div {
        width: 100%;
    }

    .filter-actions button,
    .filter-actions a {
        width: 100%;
    }

    .result-header {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>

<section class="explore-page">

    <div class="explore-wrap">

        <!-- HEADER -->
        <div class="explore-header">

            <div class="explore-eyebrow">
                <i class="fa-solid fa-compass"></i>
                RuangLomba
            </div>

            <h1>
                Jelajahi <span>Peluang</span>
            </h1>

            <p>
                Temukan lomba, beasiswa, webinar, workshop, seminar,
                dan berbagai peluang lainnya yang sesuai dengan minatmu.
            </p>

        </div>


        <!-- SEARCH -->
        <form action="<?= base_url('cari') ?>" method="get">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>"
                    placeholder="Cari lomba, beasiswa, webinar, workshop...">

            </div>


            <!-- FILTER -->
            <div class="filter-panel">

                <div class="filter-grid">

                    <!-- JENIS -->
                    <div class="filter-item">
                        <select name="type">

                            <option value="">Semua Jenis</option>

                            <?php
                            $types = [
                                'Lomba',
                                'Beasiswa',
                                'Webinar',
                                'Workshop',
                                'Seminar',
                                'Program & Event'
                            ];
                            ?>

                            <?php foreach ($types as $item): ?>

                            <option value="<?= esc($item) ?>" <?= ($selectedType ?? '') === $item ? 'selected' : '' ?>>
                                <?= esc($item) ?>
                            </option>

                            <?php endforeach; ?>

                        </select>
                    </div>


                    <!-- KATEGORI -->
                    <div class="filter-item">

                        <select name="category">

                            <option value="">Semua Kategori</option>

                            <?php foreach ($categories as $category): ?>

                            <option value="<?= esc($category['id']) ?>"
                                <?= (string)($selectedCategory ?? '') === (string)$category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- JENJANG -->
                    <div class="filter-item">

                        <select name="education_level">

                            <option value="">Semua Jenjang</option>
                            <option value="SD" <?= ($selectedEducationLevel ?? '') === 'SD' ? 'selected' : '' ?>>
                                SD
                            </option>
                            <option value="SMP" <?= ($selectedEducationLevel ?? '') === 'SMP' ? 'selected' : '' ?>>
                                SMP
                            </option>
                            <option value="SMA/SMK/MA"
                                <?= ($selectedEducationLevel ?? '') === 'SMA/SMK/MA' ? 'selected' : '' ?>>
                                SMA / SMK / MA
                            </option>
                            <option value="Mahasiswa"
                                <?= ($selectedEducationLevel ?? '') === 'Mahasiswa' ? 'selected' : '' ?>>
                                Mahasiswa
                            </option>
                            <option value="Umum" <?= ($selectedEducationLevel ?? '') === 'Umum' ? 'selected' : '' ?>>
                                Umum
                            </option>

                        </select>

                    </div>


                    <!-- TINGKAT -->
                    <div class="filter-item">

                        <select name="competition_level">

                            <option value="">Semua Tingkat</option>

                            <option value="Nasional"
                                <?= ($selectedCompetitionLevel ?? '') === 'Nasional' ? 'selected' : '' ?>>
                                Nasional
                            </option>

                            <option value="Internasional"
                                <?= ($selectedCompetitionLevel ?? '') === 'Internasional' ? 'selected' : '' ?>>
                                Internasional
                            </option>

                        </select>

                    </div>


                    <!-- MODE -->
                    <div class="filter-item">

                        <select name="implementation_type">

                            <option value="">Semua Mode</option>

                            <option value="Online"
                                <?= ($selectedImplementationType ?? '') === 'Online' ? 'selected' : '' ?>>
                                Online
                            </option>

                            <option value="Offline"
                                <?= ($selectedImplementationType ?? '') === 'Offline' ? 'selected' : '' ?>>
                                Offline
                            </option>

                            <option value="Hybrid"
                                <?= ($selectedImplementationType ?? '') === 'Hybrid' ? 'selected' : '' ?>>
                                Hybrid
                            </option>

                        </select>

                    </div>


                    <!-- BIAYA -->
                    <div class="filter-item">

                        <select name="fee">

                            <option value="">Semua Biaya</option>

                            <option value="gratis" <?= ($selectedFee ?? '') === 'gratis' ? 'selected' : '' ?>>
                                Gratis
                            </option>

                            <option value="berbayar" <?= ($selectedFee ?? '') === 'berbayar' ? 'selected' : '' ?>>
                                Berbayar
                            </option>

                        </select>

                    </div>


                    <!-- DEADLINE -->
                    <div class="filter-item">

                        <select name="deadline">

                            <option value="">Semua Deadline</option>

                            <option value="terdekat" <?= ($selectedDeadline ?? '') === 'terdekat' ? 'selected' : '' ?>>
                                Deadline Terdekat
                            </option>

                            <option value="hari_ini" <?= ($selectedDeadline ?? '') === 'hari_ini' ? 'selected' : '' ?>>
                                Hari Ini
                            </option>

                            <option value="minggu_ini"
                                <?= ($selectedDeadline ?? '') === 'minggu_ini' ? 'selected' : '' ?>>
                                Minggu Ini
                            </option>

                            <option value="bulan_ini"
                                <?= ($selectedDeadline ?? '') === 'bulan_ini' ? 'selected' : '' ?>>
                                Bulan Ini
                            </option>

                        </select>

                    </div>


                    <!-- LOKASI (opsinya diisi lewat JavaScript sesuai Mode) -->
                    <div class="filter-item">
                        <select name="location" id="filterLocation">
                            <option value="">Semua Lokasi</option>
                        </select>
                    </div>

                </div>


                <div class="filter-actions">

                    <div class="filter-info">
                        <i class="fa-solid fa-sliders"></i>
                        Gunakan filter untuk menemukan peluang yang sesuai.
                    </div>

                    <div style="display:flex; gap:8px;">

                        <a href="<?= base_url('cari') ?>" class="btn-reset">
                            Reset
                        </a>

                        <button type="submit" class="btn-filter">
                            <i class="fa-solid fa-filter"></i>
                            Terapkan Filter
                        </button>

                    </div>

                </div>

            </div>

        </form>


        <!-- RESULT HEADER -->
        <div class="result-header">

            <div>

                <h2>
                    Peluang Terbaru
                </h2>

                <p>
                    <?= count($competitions) ?> peluang ditemukan
                </p>

            </div>

        </div>


        <!-- CARDS (kartu memakai komponen bersama: app/Views/components/opportunity_card.php) -->
        <div class="competition-grid">

            <?php if (!empty($competitions)): ?>

            <?php foreach ($competitions as $competition): ?>
            <?= view('components/opportunity_card', ['competition' => $competition], ['saveData' => false]) ?>
            <?php endforeach; ?>

            <?php else: ?>

            <div class="empty-state">

                <i class="fa-regular fa-folder-open"></i>

                <h3>
                    Belum menemukan peluang
                </h3>

                <p>
                    Coba ubah kata pencarian atau filter yang kamu gunakan.
                </p>

            </div>

            <?php endif; ?>

        </div>


        <!-- PAGINATION -->
        <?php if (!empty($competitions)): ?>

        <div class="pagination-wrap">

            <?= $pager->links() ?>

        </div>

        <?php endif; ?>

    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var LOCS =
        <?= json_encode($locationsByMode ?? ['Offline' => [], 'Hybrid' => []], JSON_UNESCAPED_UNICODE) ?>;
    var SELECTED_LOC = <?= json_encode($selectedLocation ?? '') ?>;

    var meta = {
        type: {
            label: 'Jenis',
            icon: 'fa-layer-group'
        },
        category: {
            label: 'Kategori',
            icon: 'fa-tag'
        },
        education_level: {
            label: 'Jenjang',
            icon: 'fa-graduation-cap'
        },
        competition_level: {
            label: 'Tingkat',
            icon: 'fa-earth-asia'
        },
        implementation_type: {
            label: 'Mode',
            icon: 'fa-laptop'
        },
        fee: {
            label: 'Biaya',
            icon: 'fa-wallet'
        },
        deadline: {
            label: 'Deadline',
            icon: 'fa-calendar-days'
        },
        location: {
            label: 'Lokasi',
            icon: 'fa-location-dot'
        }
    };

    var all = [];
    var byName = {};

    function closeAll(except) {
        all.forEach(function(d) {
            if (d.wrap !== except) {
                d.wrap.classList.remove('is-open');
                d.trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.querySelectorAll('.filter-item select').forEach(function(select) {
        var m = meta[select.name] || {
            label: '',
            icon: 'fa-list'
        };

        var wrap = document.createElement('div');
        wrap.className = 'fd';
        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);
        select.classList.add('fd-native');

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'fd-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.innerHTML =
            '<span class="fd-icon"><i class="fa-solid ' + m.icon + '"></i></span>' +
            '<span class="fd-text"><span class="fd-label"></span><span class="fd-value"></span></span>' +
            '<i class="fa-solid fa-chevron-down fd-chevron"></i>';
        trigger.querySelector('.fd-label').textContent = m.label;

        var menu = document.createElement('div');
        menu.className = 'fd-menu';
        menu.setAttribute('role', 'listbox');

        var items = [];

        function sync() {
            var current = select.options[select.selectedIndex];
            trigger.querySelector('.fd-value').textContent = current ? current.text.trim() : '';
            wrap.classList.toggle('has-value', select.value !== '');
            items.forEach(function(it) {
                var on = it.dataset.value === select.value;
                it.classList.toggle('is-selected', on);
                it.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }

        function close(refocus) {
            wrap.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
            if (refocus) trigger.focus();
        }

        // Bangun ulang daftar pilihan dari <option> di select
        function renderItems() {
            menu.innerHTML = '';
            items.length = 0;
            Array.prototype.forEach.call(select.options, function(opt) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'fd-option' + (opt.value === '' ? ' is-placeholder' : '');
                item.setAttribute('role', 'option');
                item.dataset.value = opt.value;
                item.innerHTML = '<span></span><i class="fa-solid fa-check"></i>';
                item.firstChild.textContent = opt.text.trim();
                menu.appendChild(item);
                items.push(item);

                item.addEventListener('click', function() {
                    select.value = opt.value;
                    sync();
                    close(true);
                    select.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                });
            });
            sync();
        }

        function open() {
            if (select.disabled) return;
            closeAll(wrap);
            wrap.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
            var target = menu.querySelector('.is-selected') || items[0];
            if (target) target.scrollIntoView({
                block: 'nearest'
            });
        }

        wrap.appendChild(trigger);
        wrap.appendChild(menu);

        trigger.addEventListener('click', function() {
            wrap.classList.contains('is-open') ? close() : open();
        });

        trigger.addEventListener('keydown', function(e) {
            if (select.disabled) return;
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                open();
                (menu.querySelector('.is-selected') || items[0]).focus();
            }
        });

        menu.addEventListener('keydown', function(e) {
            var idx = items.indexOf(document.activeElement);
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                items[Math.min(idx + 1, items.length - 1)].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                items[Math.max(idx - 1, 0)].focus();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                close(true);
            }
        });

        renderItems();

        var entry = {
            wrap: wrap,
            trigger: trigger,
            select: select,
            sync: sync,
            renderItems: renderItems
        };
        all.push(entry);
        byName[select.name] = entry;
    });

    /* ===== Lokasi: aktif hanya jika Mode = Offline / Hybrid,
           pilihannya sesuai lokasi peluang pada mode tersebut ===== */
    var modeSelect = document.querySelector('select[name="implementation_type"]');
    var loc = byName['location'];

    function fillLocations(mode, keepValue) {
        var list = LOCS[mode] || [];
        loc.select.innerHTML = '';

        var ph = document.createElement('option');
        ph.value = '';
        ph.textContent = list.length ? 'Semua Lokasi' : 'Belum ada lokasi';
        loc.select.appendChild(ph);

        list.forEach(function(name) {
            var o = document.createElement('option');
            o.value = name;
            o.textContent = name;
            loc.select.appendChild(o);
        });

        loc.select.value = (keepValue && list.indexOf(keepValue) !== -1) ? keepValue : '';
        loc.renderItems();
    }

    function applyLocationState(initial) {
        if (!modeSelect || !loc) return;
        var mode = modeSelect.value;
        var allowed = (mode === 'Offline' || mode === 'Hybrid');

        loc.select.disabled = !allowed;
        loc.trigger.disabled = !allowed;
        loc.wrap.classList.toggle('is-disabled', !allowed);
        loc.wrap.classList.remove('is-open');

        if (allowed) {
            fillLocations(mode, initial ? SELECTED_LOC : '');
        } else {
            loc.select.innerHTML = '<option value="">Semua Lokasi</option>';
            loc.select.value = '';
            loc.renderItems();
            loc.trigger.querySelector('.fd-value').textContent = 'Pilih mode Offline/Hybrid';
        }
    }

    if (modeSelect && loc) {
        modeSelect.addEventListener('change', function() {
            applyLocationState(false);
        });
        applyLocationState(true);
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.fd')) closeAll();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAll();
    });
});
</script>

<?= $this->endSection() ?>