<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-bg leaderboard-page">
    <div class="container py-4">
        <header class="leaderboard-hero">
            <div class="leaderboard-hero-copy">
                <span class="leaderboard-eyebrow">
                    <i class="fa-solid fa-crown" aria-hidden="true"></i>
                    RuangLomba Hall of Achievement
                </span>
                <h1>Prestasi terbaik<br><span>layak dirayakan.</span></h1>
                <p>Peringkat peserta berdasarkan prestasi lomba, partisipasi terverifikasi, dan profil lengkap.</p>
                <div class="leaderboard-hero-meta">
                    <span><i class="fa-solid fa-users" aria-hidden="true"></i><?= $totalUsers ?> peserta</span>
                    <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Hasil terverifikasi</span>
                </div>
            </div>
            <div class="leaderboard-hero-art" aria-hidden="true">
                <div class="leaderboard-trophy-orbit leaderboard-trophy-orbit-outer"></div>
                <div class="leaderboard-trophy-orbit leaderboard-trophy-orbit-inner"></div>
                <div class="leaderboard-trophy">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <span class="leaderboard-hero-star leaderboard-hero-star-one"><i class="fa-solid fa-star"></i></span>
                <span class="leaderboard-hero-star leaderboard-hero-star-two"><i class="fa-solid fa-star"></i></span>
                <span class="leaderboard-hero-rank">TOP<br><strong>01</strong></span>
            </div>
        </header>

        <section class="leaderboard-rules mb-5" aria-labelledby="leaderboardRulesTitle">
            <div class="leaderboard-section-heading">
                <div>
                    <span class="leaderboard-section-kicker">SISTEM POIN</span>
                    <h2 id="leaderboardRulesTitle">Poin datang dari pencapaian.</h2>
                </div>
                <p>Daftar lomba saja tidak menambah poin. Hasil juara dicatat oleh penyelenggara setelah lomba selesai.</p>
            </div>
            <div class="row g-3">
                <div class="col-lg-7">
                    <article class="leaderboard-rule-card leaderboard-rule-card-award h-100">
                        <div class="leaderboard-rule-icon"><i class="fa-solid fa-medal" aria-hidden="true"></i></div>
                        <div class="leaderboard-rule-content">
                            <h3>Prestasi lomba</h3>
                            <p>Poin diberikan setelah penyelenggara mencatat hasil resmi.</p>
                            <div class="leaderboard-medal-points">
                                <span class="leaderboard-medal-point leaderboard-medal-gold">
                                    <i class="fa-solid fa-crown" aria-hidden="true"></i><strong>+100</strong><small>Juara 1</small>
                                </span>
                                <span class="leaderboard-medal-point leaderboard-medal-silver">
                                    <i class="fa-solid fa-medal" aria-hidden="true"></i><strong>+75</strong><small>Juara 2</small>
                                </span>
                                <span class="leaderboard-medal-point leaderboard-medal-bronze">
                                    <i class="fa-solid fa-medal" aria-hidden="true"></i><strong>+50</strong><small>Juara 3</small>
                                </span>
                                <span class="leaderboard-medal-point leaderboard-medal-none">
                                    <i class="fa-solid fa-minus" aria-hidden="true"></i><strong>0</strong><small>Bukan juara</small>
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-5">
                    <article class="leaderboard-rule-card leaderboard-rule-card-participation h-100">
                        <div class="leaderboard-rule-icon"><i class="fa-solid fa-bolt" aria-hidden="true"></i></div>
                        <div class="leaderboard-rule-content">
                            <h3>Partisipasi &amp; profil</h3>
                            <div class="leaderboard-rule-bonus">
                                <strong>+50</strong>
                                <span>Acara selain lomba yang sudah diverifikasi penyelenggara</span>
                            </div>
                            <div class="leaderboard-rule-bonus">
                                <strong>+20</strong>
                                <span>Lengkapi profil dan preferensi minat pertama kali</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="leaderboard-standing mb-5" aria-label="Posisi peserta">
            <?php if ($myRank): ?>
            <article class="leaderboard-my-rank">
                <div class="leaderboard-my-rank-main">
                    <span class="leaderboard-my-rank-label"><i class="fa-solid fa-user-check" aria-hidden="true"></i> POSISI KAMU</span>
                    <div class="leaderboard-my-rank-number">#<?= (int) $myRank['rank'] ?></div>
                    <div class="leaderboard-my-rank-name"><?= esc($myRank['name']) ?></div>
                    <div class="leaderboard-my-rank-subtitle">Dari <?= $totalUsers ?> peserta terdaftar</div>
                </div>
                <div class="leaderboard-my-rank-stats">
                    <div>
                        <span>Total poin</span>
                        <strong><i class="fa-solid fa-star" aria-hidden="true"></i><?= number_format((int) $myRank['points'], 0, ',', '.') ?></strong>
                    </div>
                    <div>
                        <span>Prestasi podium</span>
                        <strong><i class="fa-solid fa-trophy" aria-hidden="true"></i><?= (int) $myRank['podium_finishes'] ?></strong>
                    </div>
                </div>
            </article>
            <?php else: ?>
            <article class="leaderboard-join-card">
                <div class="leaderboard-join-icon"><i class="fa-solid fa-ranking-star" aria-hidden="true"></i></div>
                <div>
                    <h2>Mulai bangun prestasimu</h2>
                    <p>Lengkapi profil, ikuti acara, dan raih podium untuk naik di leaderboard.</p>
                </div>
                <a href="<?= base_url('masuk') ?>" class="btn btn-light">Masuk / Daftar <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
            </article>
            <?php endif; ?>
        </section>

        <?php if (! empty($topThree)): ?>
        <section class="leaderboard-podium mb-5" aria-labelledby="podiumTitle">
            <div class="leaderboard-section-heading leaderboard-section-heading-centered">
                <div>
                    <span class="leaderboard-section-kicker">YANG TERDEPAN</span>
                    <h2 id="podiumTitle">Panggung para juara</h2>
                </div>
                <p>Apresiasi untuk peserta dengan poin tertinggi saat ini.</p>
            </div>
            <?php
            $podiumOrder = [];
            if (isset($topThree[1])) $podiumOrder[] = [2, $topThree[1], 'silver'];
            if (isset($topThree[0])) $podiumOrder[] = [1, $topThree[0], 'gold'];
            if (isset($topThree[2])) $podiumOrder[] = [3, $topThree[2], 'bronze'];
            ?>
            <div class="row g-3 justify-content-center align-items-end">
                <?php foreach ($podiumOrder as [$rankNum, $user, $medalClass]): ?>
                <?php $orderClass = $rankNum === 1 ? 'order-1 order-md-2' : ($rankNum === 2 ? 'order-2 order-md-1' : 'order-3 order-md-3'); ?>
                <div class="col-md-4 col-sm-6 <?= $orderClass ?>">
                    <article class="leaderboard-podium-card leaderboard-podium-<?= $medalClass ?> <?= $rankNum === 1 ? 'leaderboard-podium-first' : '' ?>">
                        <div class="leaderboard-podium-rank">
                            <i class="fa-solid <?= $rankNum === 1 ? 'fa-crown' : 'fa-medal' ?>" aria-hidden="true"></i>
                            #<?= $rankNum ?>
                        </div>
                        <?php if (! empty($user['avatar'])): ?>
                        <img src="<?= esc(media_url($user['avatar']), 'attr') ?>" alt="" class="leaderboard-podium-avatar">
                        <?php else: ?>
                        <div class="leaderboard-podium-avatar leaderboard-podium-avatar-fallback">
                            <?= esc(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?>
                        </div>
                        <?php endif; ?>
                        <h3><?= esc($user['name']) ?></h3>
                        <p><?= esc($user['preferred_level'] ?: 'Peserta') ?></p>
                        <div class="leaderboard-podium-score">
                            <strong><?= number_format((int) $user['points'], 0, ',', '.') ?></strong>
                            <span>poin</span>
                        </div>
                        <div class="leaderboard-podium-finish">
                            <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                            <?= (int) $user['podium_finishes'] ?> prestasi podium
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="leaderboard-table-card" aria-labelledby="rankingTableTitle">
            <div class="leaderboard-table-heading">
                <div>
                    <span class="leaderboard-section-kicker">KLASEMEN</span>
                    <h2 id="rankingTableTitle">Peringkat peserta</h2>
                </div>
                <span class="leaderboard-participant-count"><?= count($rankings) ?> peserta</span>
            </div>
            <div class="table-responsive">
                <table class="table leaderboard-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">Rank</th>
                            <th>Peserta</th>
                            <th>Jenjang</th>
                            <th class="text-center">Podium</th>
                            <th class="text-end">Total poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankings)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-ranking-star d-block fs-3 mb-2" aria-hidden="true"></i>
                                Belum ada peserta di leaderboard.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rankings as $r): ?>
                        <tr class="<?= $myRank && (int) $myRank['id'] === (int) $r['id'] ? 'leaderboard-current-user' : '' ?>">
                            <td class="text-center">
                                <span class="leaderboard-rank-badge <?= (int) $r['rank'] <= 3 ? 'leaderboard-rank-top-' . (int) $r['rank'] : '' ?>">
                                    <?php if ((int) $r['rank'] === 1): ?><i class="fa-solid fa-crown" aria-hidden="true"></i><?php endif; ?>
                                    #<?= (int) $r['rank'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="leaderboard-participant">
                                    <?php if (! empty($r['avatar'])): ?>
                                    <img src="<?= esc(media_url($r['avatar']), 'attr') ?>" alt="" class="leaderboard-list-avatar">
                                    <?php else: ?>
                                    <span class="leaderboard-list-avatar leaderboard-list-avatar-fallback">
                                        <?= esc(mb_strtoupper(mb_substr($r['name'], 0, 1))) ?>
                                    </span>
                                    <?php endif; ?>
                                    <span>
                                        <strong><?= esc($r['name']) ?></strong>
                                        <small>Bergabung <?= esc(date('M Y', strtotime($r['created_at']))) ?></small>
                                    </span>
                                </div>
                            </td>
                            <td><span class="leaderboard-level"><?= esc($r['preferred_level'] ?: 'Umum') ?></span></td>
                            <td class="text-center">
                                <span class="leaderboard-podium-count">
                                    <i class="fa-solid fa-trophy" aria-hidden="true"></i><?= (int) $r['podium_finishes'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="leaderboard-total-points">
                                    <i class="fa-solid fa-star" aria-hidden="true"></i><?= number_format((int) $r['points'], 0, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>

<?= $this->endSection() ?>
