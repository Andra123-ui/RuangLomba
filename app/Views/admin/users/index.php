<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-bg">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Manajemen Pengguna &amp; Moderasi Nama</h2>
                <p class="text-secondary mb-0">Pantau akun peserta dan penyelenggara. Anda dapat mereset nama yang melanggar pedoman etika / norma SARA.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/peluang') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-trophy me-1"></i> Moderasi Peluang
                </a>
                <a href="<?= base_url('admin/organizer') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-certificate me-1"></i> Verifikasi Organizer
                </a>
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

        <!-- Search and Filter Bar -->
        <div class="card card-ruang p-3 bg-white shadow-sm mb-4">
            <form action="<?= base_url('admin/pengguna') ?>" method="get" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
                        <input type="text" name="q" class="form-control auth-input border-start-0" placeholder="Cari nama atau email..." value="<?= esc($q) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select auth-input">
                        <option value="">Semua Peran</option>
                        <option value="participant" <?= $role === 'participant' ? 'selected' : '' ?>>Peserta (Participant)</option>
                        <option value="organizer" <?= $role === 'organizer' ? 'selected' : '' ?>>Penyelenggara (Organizer)</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                    <?php if ($q !== '' || $role): ?>
                    <a href="<?= base_url('admin/pengguna') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <?php if (empty($users)): ?>
        <div class="help-contact mt-0 text-center py-5">
            <h3>Tidak Ada Pengguna Ditemukan</h3>
            <p class="text-secondary">Tidak ada data pengguna yang cocok dengan pencarianmu.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive bg-white border rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th class="text-center">Poin</th>
                        <th>Terdaftar</th>
                        <th class="text-end pe-4">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-secondary small fw-semibold">#<?= $u['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (! empty($u['avatar'])): ?>
                                    <img src="<?= esc(media_url($u['avatar']), 'attr') ?>" alt="" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.9rem; font-weight: 700;">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="fw-semibold text-dark"><?= esc($u['name']) ?></span>
                            </div>
                        </td>
                        <td><small class="text-secondary"><?= esc($u['email']) ?></small></td>
                        <td>
                            <?php if ($u['role'] === 'organizer'): ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Penyelenggara</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">Peserta</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border fw-bold"><?= number_format((int) ($u['points'] ?? 0), 0, ',', '.') ?></span>
                        </td>
                        <td><small class="text-secondary"><?= date('d M Y', strtotime($u['created_at'])) ?></small></td>
                        <td class="text-end pe-4">
                            <form action="<?= base_url('admin/pengguna/' . $u['id'] . '/reset-nama') ?>" method="post" class="d-inline"
                                  data-confirm-title="Reset nama akun" data-confirm-label="Reset nama"
                                  data-confirm-message="Apakah nama akun ini melanggar pedoman? Nama akan direset menjadi default dan pengguna dikirimi notifikasi.">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Reset nama jika tidak pantas / SARA">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Reset Nama
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
