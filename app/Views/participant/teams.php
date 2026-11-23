<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-bg">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 reveal is-visible">
            <div>
                <h2>Tim Saya</h2>
                <p class="text-secondary mb-0">Bentuk tim atau bergabung bersama rekan untuk mengikuti kompetisi beregu di RuangLomba.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalJoinTeam">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Gabung Tim
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateTeam">
                    <i class="fa-solid fa-plus me-1"></i> Buat Tim Baru
                </button>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (empty($teams)): ?>
        <div class="help-contact mt-0 text-center py-5">
            <div class="display-4 text-primary mb-3"><i class="fa-solid fa-users"></i></div>
            <h3 class="fw-bold">Belum Ada Tim</h3>
            <p class="text-secondary mx-auto" style="max-width: 500px;">
                Kamu belum tergabung dalam tim manapun. Buat tim baru dan undang temanmu, atau masukkan kode bergabung tim dari temanmu.
            </p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateTeam">
                    <i class="fa-solid fa-plus me-1"></i> Buat Tim Baru
                </button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalJoinTeam">
                    <i class="fa-solid fa-key me-1"></i> Masukkan Kode Tim
                </button>
            </div>
        </div>
        <?php else: ?>

        <div class="row g-4">
            <?php foreach ($teams as $t): ?>
            <div class="col-lg-6">
                <div class="card card-ruang p-4 h-100 bg-white shadow-sm border position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="fw-bold mb-0 text-dark"><?= esc($t['name']) ?></h4>
                                <?php if ($t['is_leader']): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Ketua</span>
                                <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">Anggota</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-secondary small mt-1">Dibuat <?= date('d M Y', strtotime($t['created_at'])) ?></div>
                        </div>

                        <!-- Join Code Box -->
                        <div class="text-end">
                            <span class="text-secondary small d-block">Kode Undangan:</span>
                            <div class="badge bg-light text-dark border px-2 py-1 fs-6 font-monospace" id="code-<?= $t['id'] ?>">
                                <?= esc($t['join_code']) ?>
                            </div>
                            <button class="btn btn-link btn-sm p-0 ms-1 text-decoration-none copy-btn" data-code="<?= esc($t['join_code']) ?>" title="Salin Kode">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Members List -->
                    <div class="mb-4">
                        <div class="small fw-semibold text-secondary text-uppercase mb-2" style="letter-spacing: 0.5px;">
                            Anggota Tim (<?= count($t['members']) ?> Orang)
                        </div>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                            <?php foreach ($t['members'] as $m): ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between p-2 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (! empty($m['avatar'])): ?>
                                        <img src="<?= esc(media_url($m['avatar']), 'attr') ?>" alt="" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 700;">
                                            <?= strtoupper(substr($m['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold small text-dark"><?= esc($m['name']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($m['email']) ?></div>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($m['role'] === 'leader'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>Ketua</span>
                                    <?php else: ?>
                                    <span class="badge bg-light text-secondary border">Anggota</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-secondary">
                            Siap didaftarkan ke lomba bertipe Tim
                        </span>
                        <form action="<?= base_url('tim/' . $t['id'] . '/keluar') ?>" method="post"
                              onsubmit="return confirm('<?= $t['is_leader'] ? 'Kamu adalah ketua tim. Membubarkan tim akan menghapus tim ini untuk semua anggota. Lanjutkan?' : 'Yakin ingin keluar dari tim ini?' ?>');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid <?= $t['is_leader'] ? 'fa-trash' : 'fa-arrow-right-from-bracket' ?> me-1"></i>
                                <?= $t['is_leader'] ? 'Bubarkan Tim' : 'Keluar Tim' ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Buat Tim -->
<div class="modal fade" id="modalCreateTeam" tabindex="-1" aria-labelledby="modalCreateTeamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('tim/buat') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalCreateTeamLabel">Buat Tim Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-secondary small mb-3">Sebagai pembuat tim, kamu otomatis menjadi <strong>Ketua Tim</strong> dan akan mendapatkan kode undangan untuk dibagikan ke anggota.</p>
                    <div class="mb-3">
                        <label for="team_name" class="form-label fw-semibold">Nama Tim</label>
                        <input type="text" class="form-control auth-input" id="team_name" name="name"
                               placeholder="Contoh: Garuda Tech Squad" required maxlength="100">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Buat Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Gabung Tim -->
<div class="modal fade" id="modalJoinTeam" tabindex="-1" aria-labelledby="modalJoinTeamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('tim/gabung') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalJoinTeamLabel">Gabung ke Tim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-secondary small mb-3">Masukkan kode undangan tim yang diberikan oleh ketua timmu.</p>
                    <div class="mb-3">
                        <label for="join_code" class="form-label fw-semibold">Kode Undangan Tim</label>
                        <input type="text" class="form-control auth-input text-uppercase font-monospace" id="join_code" name="join_code"
                               placeholder="Contoh: RL-9F2B8" required maxlength="20">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Gabung Tim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var code = this.getAttribute('data-code');
            navigator.clipboard.writeText(code).then(function() {
                btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="fa-regular fa-copy"></i>';
                }, 2000);
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
