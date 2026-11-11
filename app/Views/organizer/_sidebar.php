<?php
$path   = trim(uri_string(), '/');
$name   = (string) session()->get('userName');
$onList = str_starts_with($path, 'organizer/lomba') && $path !== 'organizer/lomba/baru';
$cur    = 'aria-current="page"';
?>
<aside class="org-sidebar" aria-label="Menu penyelenggara">
    <div class="org-user">
        <span class="org-user-avatar"><?= esc(mb_strtoupper(mb_substr($name, 0, 1))) ?></span>
        <div class="min-w-0">
            <div class="org-user-name"><?= esc($name) ?></div>
            <div class="org-user-role">Penyelenggara</div>
        </div>
    </div>

    <ul class="org-menu">
        <li>
            <a href="<?= base_url('organizer/dashboard') ?>"
                class="<?= $path === 'organizer/dashboard' ? 'active' : '' ?>"
                <?= $path === 'organizer/dashboard' ? $cur : '' ?>>
                <i class="fa-solid fa-gauge" aria-hidden="true"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= base_url('organizer/lomba') ?>" class="<?= $onList ? 'active' : '' ?>"
                <?= $onList ? $cur : '' ?>>
                <i class="fa-solid fa-list-check" aria-hidden="true"></i> Peluang Saya
            </a>
        </li>
        <li>
            <a href="<?= base_url('organizer/lomba/baru') ?>"
                class="<?= $path === 'organizer/lomba/baru' ? 'active' : '' ?>"
                <?= $path === 'organizer/lomba/baru' ? $cur : '' ?>>
                <i class="fa-solid fa-circle-plus" aria-hidden="true"></i> Tambah Peluang
            </a>
        </li>

        <li class="org-menu-sep" aria-hidden="true"></li>

        <li>
            <a href="<?= base_url('/') ?>">
                <i class="fa-solid fa-house" aria-hidden="true"></i> Ke Beranda
            </a>
        </li>
        <li>
            <form action="<?= base_url('keluar') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <button type="submit">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Keluar
                </button>
            </form>
        </li>
    </ul>
</aside>