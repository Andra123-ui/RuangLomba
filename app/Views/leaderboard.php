<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-bg">
    <div class="container py-4">
        <!-- Header -->
        <div class="text-center mb-5 reveal is-visible">
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="fa-solid fa-trophy me-1"></i> RuangLomba Gamifikasi
            </span>
            <h1 class="fw-bold mb-2">Leaderboard &amp; Prestasi Peserta</h1>
            <p class="text-secondary mx-auto" style="max-width: 650px;">
                Peringkat talenta muda teraktif dan berprestasi berdasarkan akumulasi poin keikutsertaan kompetisi di platform RuangLomba.
            </p>
        </div>

        <!-- System Points Info & My Position -->
        <div class="row g-3 mb-5">
            <div class="col-md-7">
                <div class="card card-ruang p-4 h-100 bg-white">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-info text-primary me-2"></i>Cara Mendapatkan Poin</h5>
                    <div class="row g-2 mt-1">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="fw-bold text-success fs-5">+20 Poin</div>
                                <div class="small text-secondary">Lengkapi profil &amp; preferensi minat pertamamu</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="fw-bold text-primary fs-5">+50 Poin</div>
                                <div class="small text-secondary">Setiap pendaftaran lomba berhasil diverifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <?php if ($myRank): ?>
                <div class="card card-ruang p-4 h-100 text-white" style="background: linear-gradient(135deg, #1B3C87 0%, #2563FF 100%);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-white text-primary fw-bold px-3 py-1 rounded-pill">Posisi Kamu</span>
                        <i class="fa-solid fa-medal fs-4 text-warning"></i>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-1 fw-bold">#<?= $myRank['rank'] ?></div>
                        <div>
                            <div class="fw-semibold fs-5"><?= esc($myRank['name']) ?></div>
                            <div class="small opacity-75">Dari <?= $totalUsers ?> peserta terdaftar</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between">
                        <span>Total Poin: <strong class="fs-5"><?= number_format($myRank['points'], 0, ',', '.') ?></strong></span>
                        <span><?= $myRank['verified_competitions'] ?> Lomba Terverifikasi</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-ruang p-4 h-100 bg-white text-center d-flex flex-column justify-content-center">
                    <h6 class="fw-bold text-dark mb-1">Ingin masuk Leaderboard?</h6>
                    <p class="text-secondary small mb-3">Daftar sebagai peserta, lengkapi preferensi minatmu, dan ikuti berbagai kompetisi impianmu!</p>
                    <div>
                        <a href="<?= base_url('masuk') ?>" class="btn btn-outline-primary btn-sm px-3">Masuk / Daftar Sekarang</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Podium Top 3 -->
        <?php if (! empty($topThree)): ?>
        <div class="mb-5">
            <h4 class="fw-bold text-center mb-4"><i class="fa-solid fa-crown text-warning me-2"></i>Top 3 Juara</h4>
            <div class="row g-4 justify-content-center align-items-end">
                <?php
                // Urutan podium: Juara 2 (kiri), Juara 1 (tengah), Juara 3 (kanan)
                $podiumOrder = [];
                if (isset($topThree[1])) $podiumOrder[] = [2, $topThree[1], '#C0C0C0', 'silver', '170px'];
                if (isset($topThree[0])) $podiumOrder[] = [1, $topThree[0], '#FFD700', 'gold', '210px'];
                if (isset($topThree[2])) $podiumOrder[] = [3, $topThree[2], '#CD7F32', 'bronze', '150px'];
                ?>

                <?php foreach ($podiumOrder as [$rankNum, $user, $color, $badgeTitle, $height]): ?>
                <div class="col-md-4 col-sm-6 text-center order-<?= $rankNum === 1 ? '1 order-md-2' : ($rankNum === 2 ? '2 order-md-1' : '3 order-md-3') ?>">
                    <div class="card card-ruang p-3 h-100 bg-white position-relative shadow-sm border-top border-4" style="border-top-color: <?= $color ?> !important;">
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge rounded-circle p-2 text-white shadow" style="background-color: <?= $color ?>; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 700;">
                                <?= $rankNum ?>
                            </span>
                        </div>
                        <div class="mt-3 mb-2">
                            <?php if (! empty($user['avatar'])): ?>
                                <img src="<?= esc(media_url($user['avatar']), 'attr') ?>" alt="" class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 70px; height: 70px; font-size: 1.6rem; font-weight: 700;">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold mb-1 text-truncate"><?= esc($user['name']) ?></h5>
                        <div class="text-secondary small mb-2"><?= esc($user['preferred_level'] ?: 'Peserta') ?></div>
                        <div class="p-2 bg-light rounded-3">
                            <span class="fs-5 fw-bold text-primary"><?= number_format($user['points'], 0, ',', '.') ?></span>
                            <span class="small text-secondary">Poin</span>
                        </div>
                        <div class="small text-secondary mt-2">
                            <i class="fa-solid fa-circle-check text-success me-1"></i><?= $user['verified_competitions'] ?> Lomba Terverifikasi
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Full Ranking Table -->
        <div class="card card-ruang p-0 bg-white overflow-hidden shadow-sm">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h5 class="fw-bold mb-0">Tabel Peringkat Lengkap</h5>
                <span class="badge bg-secondary"><?= count($rankings) ?> Peserta Terdata</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Rank</th>
                            <th>Peserta</th>
                            <th>Jenjang</th>
                            <th class="text-center">Lomba Terverifikasi</th>
                            <th class="text-end pe-4">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankings)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">Belum ada data peserta.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rankings as $r): ?>
                        <tr class="<?= ($myRank && $myRank['id'] === $r['id']) ? 'table-primary fw-semibold' : '' ?>">
                            <td class="text-center">
                                <?php if ($r['rank'] === 1): ?>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-crown me-1"></i>#1</span>
                                <?php elseif ($r['rank'] === 2): ?>
                                    <span class="badge bg-secondary text-white fw-bold px-2 py-1">#2</span>
                                <?php elseif ($r['rank'] === 3): ?>
                                    <span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color: #CD7F32 !important;">#3</span>
                                <?php else: ?>
                                    <span class="text-secondary fw-semibold">#<?= $r['rank'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (! empty($r['avatar'])): ?>
                                        <img src="<?= esc(media_url($r['avatar']), 'attr') ?>" alt="" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-size: 1rem; font-weight: 700;">
                                            <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark"><?= esc($r['name']) ?></div>
                                        <div class="text-secondary small">Bergabung <?= date('M Y', strtotime($r['created_at'])) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= esc($r['preferred_level'] ?: 'Umum') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <?= $r['verified_competitions'] ?> Lomba
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="fs-6 fw-bold text-primary">
                                    <i class="fa-solid fa-star text-warning me-1"></i><?= number_format($r['points'], 0, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
