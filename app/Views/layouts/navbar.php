<?php
$current    = trim(uri_string(), '/');
$isLoggedIn = (bool) session()->get('isLoggedIn');
$userName   = (string) session()->get('userName');
$userRole   = (string) session()->get('userRole');
$userEmail  = (string) session()->get('userEmail');
$userAvatar = $userAvatar ?? '';
$displayUserName = $displayUserName ?? $userName;
$roleLabels = [
    'admin'       => 'Administrator',
    'organizer'   => 'Penyelenggara',
    'participant' => 'Peserta',
];
?>
<nav class="navbar navbar-expand-lg navbar-ruang sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">RuangLomba</a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-lg-auto mb-3 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $current === '' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === 'cari' ? 'active' : '' ?>"
                        href="<?= base_url('cari') ?>">Jelajahi Peluang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === 'leaderboard' ? 'active' : '' ?>"
                        href="<?= base_url('leaderboard') ?>">Leaderboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === 'untuk-organizer' ? 'active' : '' ?>"
                        href="<?= base_url('untuk-organizer') ?>">Organizer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === 'tentang-kami' ? 'active' : '' ?>"
                        href="<?= base_url('tentang-kami') ?>">Tentang Kami</a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 gap-lg-3">
                <?php if ($isLoggedIn): ?>
                <?php
                $unreadCount = db_connect()->table('notifications')
                    ->where('user_id', (int) session()->get('userId'))
                    ->where('is_read', 0)
                    ->countAllResults();
                ?>
                <a href="<?= base_url('notifikasi') ?>" class="nav-bell"
                    aria-label="Notifikasi<?= $unreadCount > 0 ? ', ' . $unreadCount . ' belum dibaca' : '' ?>">
                    <i class="fa-regular fa-bell" aria-hidden="true"></i>
                    <span class="d-lg-none ms-2">Notifikasi</span>
                    <?php if ($unreadCount > 0): ?>
                    <span class="nav-bell-badge"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown">
                    <button
                        class="btn dropdown-toggle d-flex align-items-center gap-2 user-menu-btn"
                        type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false"
                        aria-label="Menu pengguna: <?= esc($userName, 'attr') ?>">
                        <?php if ($userAvatar !== ''): ?>
                        <img src="<?= esc($userAvatar, 'attr') ?>" alt="" class="nav-user-avatar">
                        <?php else: ?>
                        <span class="nav-user-avatar nav-user-avatar-fallback" aria-hidden="true">
                            <?= esc(mb_strtoupper(mb_substr($userName, 0, 1))) ?>
                        </span>
                        <?php endif; ?>
                        <span class="nav-user-name"><?= esc($displayUserName) ?></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end nav-user-dropdown" aria-labelledby="userMenuButton">
                        <li>
                            <div class="nav-user-summary">
                                <?php if ($userAvatar !== ''): ?>
                                <img src="<?= esc($userAvatar, 'attr') ?>" alt="" class="nav-user-summary-avatar">
                                <?php else: ?>
                                <span class="nav-user-summary-avatar nav-user-avatar-fallback" aria-hidden="true">
                                    <?= esc(mb_strtoupper(mb_substr($userName, 0, 1))) ?>
                                </span>
                                <?php endif; ?>
                                <span class="nav-user-summary-copy">
                                    <span class="nav-user-summary-name"><?= esc($userName) ?></span>
                                    <span class="nav-user-summary-meta">
                                        <?= esc($userEmail !== '' ? $userEmail : ($roleLabels[$userRole] ?? 'Akun')) ?>
                                    </span>
                                </span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php if ($userRole === 'admin'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                <i class="fa-solid fa-gauge-high fa-fw me-2"></i>Dashboard Admin
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/peluang') ?>">
                                <i class="fa-solid fa-shield-halved fa-fw me-2"></i>Moderasi Peluang
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/organizer') ?>">
                                <i class="fa-solid fa-certificate fa-fw me-2"></i>Verifikasi Organizer
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/pengguna') ?>">
                                <i class="fa-solid fa-users-gear fa-fw me-2"></i>Manajemen Pengguna
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php endif; ?>

                        <?php if ($userRole === 'organizer'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/dashboard') ?>">
                                <i class="fa-solid fa-gauge-high fa-fw me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/lomba') ?>">
                                <i class="fa-solid fa-trophy fa-fw me-2"></i>Peluang Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/lomba/baru') ?>">
                                <i class="fa-solid fa-circle-plus fa-fw me-2"></i>Tambah Peluang
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/verifikasi') ?>">
                                <i class="fa-solid fa-shield-halved fa-fw me-2"></i>Verifikasi Instansi
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php endif; ?>

                        <?php if ($userRole === 'participant'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('pendaftaran-saya') ?>">
                                <i class="fa-solid fa-clipboard-list fa-fw me-2"></i>Pendaftaran Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('tersimpan') ?>">
                                <i class="fa-solid fa-heart fa-fw me-2"></i>Peluang Tersimpan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('profil') ?>">
                                <i class="fa-solid fa-user-gear fa-fw me-2"></i>Profil &amp; Preferensi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('tim') ?>">
                                <i class="fa-solid fa-users-gear fa-fw me-2"></i>Tim Saya
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php endif; ?>

                        <li>
                            <form action="<?= base_url('keluar') ?>" method="post" class="m-0"
                                data-confirm-title="Keluar dari akun?" data-confirm-label="Keluar"
                                data-confirm-message="Yakin ingin keluar?">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-arrow-right-from-bracket fa-fw me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <?php else: ?>
                <a href="<?= base_url('masuk') ?>" class="link-masuk">Masuk</a>
                <a href="<?= base_url('daftar') ?>" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>