<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$fmt = static fn (int $number): string => number_format($number, 0, ',', '.');
?>

<style>
.admin-dashboard { max-width: 1160px; }
.admin-dash-hero {
    padding: clamp(24px, 4vw, 42px);
    border: 1px solid var(--c-border);
    border-radius: 18px;
    color: var(--c-black);
    background: #fff;
    box-shadow: 0 8px 24px -20px rgba(15, 23, 42, .35);
}
.admin-dash-hero h1 { margin: 0; font-size: clamp(28px, 4vw, 40px); }
.admin-dash-hero p { margin: 10px 0 0; color: var(--c-gray); }
.admin-dash-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--c-blue);
}
.admin-dash-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 30px 0 14px;
}
.admin-dash-section-title h2 { margin: 0; font-size: 20px; }
.admin-dash-section-title p { margin: 4px 0 0; color: var(--c-gray); font-size: 13px; }
.admin-dash-stat {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    height: 100%;
    padding: 20px;
}
.admin-dash-stat-icon {
    display: grid;
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    place-items: center;
    border-radius: 13px;
    color: var(--c-blue);
    background: #EFF4FF;
}
.admin-dash-stat-icon.is-warn { color: #B45309; background: #FFF7ED; }
.admin-dash-stat-number { font-size: 32px; font-weight: 700; line-height: 1; color: var(--c-black); }
.admin-dash-stat-label { margin-top: 6px; font-size: 13px; color: var(--c-gray); }
.admin-dash-stat-hint { margin-top: 7px; font-size: 12px; color: var(--c-gray); }
.admin-dash-queue { height: 100%; }
.admin-dash-queue .list-group-item { padding: 14px 18px; border-color: var(--c-border); }
.admin-dash-queue .list-group-item:first-child { border-top: 0; }
.admin-dash-queue .list-group-item:last-child { border-bottom: 0; }
.admin-dash-row-title { font-weight: 650; color: var(--c-black); }
.admin-dash-row-meta { margin-top: 4px; font-size: 12px; color: var(--c-gray); }
.admin-dash-empty { padding: 26px 18px; color: var(--c-gray); text-align: center; }
@media (max-width: 575.98px) {
    .admin-dash-section-title { align-items: flex-start; }
}
</style>

<section class="page-bg">
    <div class="container admin-dashboard">
        <header class="admin-dash-hero">
            <div class="admin-dash-eyebrow"><i class="fa-solid fa-shield-halved"></i> RuangLomba · Panel Admin</div>
            <h1>Halo, <?= esc(session()->get('userName')) ?></h1>
            <p>Pantau pengajuan organizer dan moderasi peluang dari satu tempat.</p>
        </header>

        <div class="admin-dash-section-title">
            <div>
                <h2>Ringkasan moderasi</h2>
                <p>Antrean yang membutuhkan tinjauan admin.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 col-xl-3">
                <a class="card card-ruang h-100 text-decoration-none" href="<?= base_url('admin/organizer?status=pending') ?>">
                    <div class="admin-dash-stat">
                        <span class="admin-dash-stat-icon is-warn"><i class="fa-solid fa-building-circle-check"></i></span>
                        <div>
                            <div class="admin-dash-stat-number"><?= $fmt($organizerCounts['pending']) ?></div>
                            <div class="admin-dash-stat-label">Verifikasi organizer</div>
                            <div class="admin-dash-stat-hint">Menunggu peninjauan</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="card card-ruang h-100 text-decoration-none" href="<?= base_url('admin/peluang?status=pending') ?>">
                    <div class="admin-dash-stat">
                        <span class="admin-dash-stat-icon is-warn"><i class="fa-solid fa-hourglass-half"></i></span>
                        <div>
                            <div class="admin-dash-stat-number"><?= $fmt($competitionCounts['pending']) ?></div>
                            <div class="admin-dash-stat-label">Moderasi peluang</div>
                            <div class="admin-dash-stat-hint">Menunggu persetujuan</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="card card-ruang h-100 text-decoration-none" href="<?= base_url('admin/organizer?status=verified') ?>">
                    <div class="admin-dash-stat">
                        <span class="admin-dash-stat-icon"><i class="fa-solid fa-certificate"></i></span>
                        <div>
                            <div class="admin-dash-stat-number"><?= $fmt($organizerCounts['verified']) ?></div>
                            <div class="admin-dash-stat-label">Organizer terverifikasi</div>
                            <div class="admin-dash-stat-hint">Lencana aktif</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="card card-ruang h-100 text-decoration-none" href="<?= base_url('admin/peluang?status=approved') ?>">
                    <div class="admin-dash-stat">
                        <span class="admin-dash-stat-icon"><i class="fa-solid fa-trophy"></i></span>
                        <div>
                            <div class="admin-dash-stat-number"><?= $fmt($competitionCounts['approved']) ?></div>
                            <div class="admin-dash-stat-label">Peluang tayang</div>
                            <div class="admin-dash-stat-hint">Disetujui admin</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <section class="card card-ruang admin-dash-queue">
                    <div class="card-body pb-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <h2 class="h5 fw-bold mb-1">Pengajuan organizer</h2>
                                <p class="small text-secondary mb-0">Periksa dokumen dan identitas instansi.</p>
                            </div>
                            <a class="btn btn-outline-primary btn-sm text-nowrap" href="<?= base_url('admin/organizer?status=pending') ?>">Lihat semua</a>
                        </div>
                    </div>
                    <?php if (empty($pendingOrganizers)): ?>
                    <div class="admin-dash-empty"><i class="fa-solid fa-circle-check me-1"></i> Tidak ada pengajuan yang menunggu.</div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pendingOrganizers as $organizer): ?>
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3"
                            href="<?= base_url('admin/organizer/' . $organizer['id']) ?>">
                            <span class="min-w-0">
                                <span class="admin-dash-row-title d-block text-truncate"><?= esc($organizer['name']) ?></span>
                                <span class="admin-dash-row-meta"><?= esc($organizer['org_type']) ?> · <?= esc($organizer['email']) ?></span>
                            </span>
                            <span class="badge text-bg-warning flex-shrink-0">Tinjau</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card card-ruang admin-dash-queue">
                    <div class="card-body pb-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <h2 class="h5 fw-bold mb-1">Peluang menunggu</h2>
                                <p class="small text-secondary mb-0">Tinjau informasi sebelum ditayangkan.</p>
                            </div>
                            <a class="btn btn-outline-primary btn-sm text-nowrap" href="<?= base_url('admin/peluang?status=pending') ?>">Lihat semua</a>
                        </div>
                    </div>
                    <?php if (empty($pendingCompetitions)): ?>
                    <div class="admin-dash-empty"><i class="fa-solid fa-circle-check me-1"></i> Tidak ada peluang yang menunggu.</div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pendingCompetitions as $competition): ?>
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3"
                            href="<?= base_url('admin/peluang/' . $competition['id']) ?>">
                            <span class="min-w-0">
                                <span class="admin-dash-row-title d-block text-truncate"><?= esc($competition['title']) ?></span>
                                <span class="admin-dash-row-meta"><?= esc($competition['organizer_name']) ?> · <?= esc(date('d M Y', strtotime($competition['created_at']))) ?></span>
                            </span>
                            <span class="badge text-bg-warning flex-shrink-0">Tinjau</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
