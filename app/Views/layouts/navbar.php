<?php
$current    = trim(uri_string(), '/');
$isLoggedIn = (bool) session()->get('isLoggedIn');
$userName   = (string) session()->get('userName');
$userRole   = (string) session()->get('userRole');
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
                        class="btn btn-outline-primary btn-sm dropdown-toggle w-100 d-flex align-items-center justify-content-between user-menu-btn"
                        type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false"
                        aria-label="Menu pengguna: <?= esc($userName) ?>">
                        <span class="text-truncate" style="max-width: 140px;">
                            <?= esc($userName) ?>
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenuButton">
                        <?php if ($userRole === 'admin'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/peluang') ?>">
                                <i class="fa-solid fa-shield-halved fa-fw me-2"></i>Moderasi Peluang
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php endif; ?>

                        <?php if ($userRole === 'organizer'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('organizer/lomba') ?>">
                                <i class="bi bi-trophy me-2"></i>Peluang Saya
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
                            <hr class="dropdown-divider">
                        </li>
                        <?php endif; ?>

                        <li>
                            <form action="<?= base_url('keluar') ?>" method="post" class="m-0"
                                onsubmit="return confirm('Yakin ingin keluar?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
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