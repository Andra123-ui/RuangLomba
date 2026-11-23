<?php
$roleLabels = [
    'admin'       => 'Administrator',
    'organizer'   => 'Penyelenggara',
    'participant' => 'Peserta',
];

if ($userRole === 'admin') {
    $links = [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'href' => 'admin/dashboard', 'active' => $currentPath === 'admin/dashboard'],
        ['label' => 'Moderasi peluang', 'icon' => 'fa-trophy', 'href' => 'admin/peluang', 'active' => str_starts_with($currentPath, 'admin/peluang')],
        ['label' => 'Organizer', 'icon' => 'fa-building-shield', 'href' => 'admin/organizer', 'active' => str_starts_with($currentPath, 'admin/organizer')],
        ['label' => 'Pengguna', 'icon' => 'fa-users-gear', 'href' => 'admin/pengguna', 'active' => str_starts_with($currentPath, 'admin/pengguna')],
        ['label' => 'Notifikasi', 'icon' => 'fa-bell', 'href' => 'notifikasi', 'active' => $currentPath === 'notifikasi'],
    ];
} elseif ($userRole === 'organizer') {
    $links = [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'href' => 'organizer/dashboard', 'active' => $currentPath === 'organizer/dashboard'],
        ['label' => 'Peluang saya', 'icon' => 'fa-trophy', 'href' => 'organizer/lomba', 'active' => $currentPath === 'organizer/lomba' || (str_starts_with($currentPath, 'organizer/lomba/') && ! str_ends_with($currentPath, '/baru'))],
        ['label' => 'Tambah peluang', 'icon' => 'fa-circle-plus', 'href' => 'organizer/lomba/baru', 'active' => str_ends_with($currentPath, '/baru')],
        ['label' => 'Verifikasi instansi', 'icon' => 'fa-shield-halved', 'href' => 'organizer/verifikasi', 'active' => $currentPath === 'organizer/verifikasi'],
        ['label' => 'Notifikasi', 'icon' => 'fa-bell', 'href' => 'notifikasi', 'active' => $currentPath === 'notifikasi'],
    ];
} else {
    $links = [
        ['label' => 'Pendaftaran saya', 'icon' => 'fa-clipboard-list', 'href' => 'pendaftaran-saya', 'active' => $currentPath === 'pendaftaran-saya'],
        ['label' => 'Peluang tersimpan', 'icon' => 'fa-bookmark', 'href' => 'tersimpan', 'active' => $currentPath === 'tersimpan'],
        ['label' => 'Tim saya', 'icon' => 'fa-people-group', 'href' => 'tim', 'active' => $currentPath === 'tim'],
        ['label' => 'Profil & preferensi', 'icon' => 'fa-user-gear', 'href' => 'profil', 'active' => $currentPath === 'profil'],
        ['label' => 'Notifikasi', 'icon' => 'fa-bell', 'href' => 'notifikasi', 'active' => $currentPath === 'notifikasi'],
    ];
}
?>

<aside class="role-sidebar" aria-label="Navigasi <?= esc($roleLabels[$userRole] ?? 'akun') ?>">
    <a class="role-sidebar-brand" href="<?= base_url('/') ?>">
        <span class="role-sidebar-brand-mark"><i class="fa-solid fa-shapes" aria-hidden="true"></i></span>
        <span>RuangLomba</span>
    </a>

    <div class="role-sidebar-user">
        <?php if ($userAvatar !== ''): ?>
        <img src="<?= esc($userAvatar, 'attr') ?>" alt="" class="role-sidebar-avatar">
        <?php else: ?>
        <span class="role-sidebar-avatar role-sidebar-avatar-fallback" aria-hidden="true">
            <?= esc(mb_strtoupper(mb_substr($userName, 0, 1))) ?>
        </span>
        <?php endif; ?>
        <span class="role-sidebar-user-copy">
            <span class="role-sidebar-user-name" title="<?= esc($userName, 'attr') ?>"><?= esc($displayUserName) ?></span>
            <span class="role-sidebar-user-role"><?= esc($roleLabels[$userRole] ?? 'Akun') ?></span>
        </span>
    </div>

    <nav class="role-sidebar-nav">
        <span class="role-sidebar-label">Menu utama</span>
        <ul>
            <?php foreach ($links as $link): ?>
            <li>
                <a href="<?= base_url($link['href']) ?>" class="role-sidebar-link<?= $link['active'] ? ' active' : '' ?>"
                    <?= $link['active'] ? 'aria-current="page"' : '' ?>>
                    <i class="fa-solid <?= esc($link['icon'], 'attr') ?>" aria-hidden="true"></i>
                    <span><?= esc($link['label']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
            <?php if ($userRole === 'participant'): ?>
            <li>
                <a href="<?= base_url('cari') ?>" class="role-sidebar-link">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span>Jelajahi peluang</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="role-sidebar-footer">
        <a href="<?= base_url('/') ?>" class="role-sidebar-link">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            <span>Ke situs RuangLomba</span>
        </a>
        <form action="<?= base_url('keluar') ?>" method="post" data-confirm-title="Keluar dari akun?"
            data-confirm-label="Keluar" data-confirm-message="Yakin ingin keluar?">
            <?= csrf_field() ?>
            <button type="submit" class="role-sidebar-link role-sidebar-logout">
                <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
