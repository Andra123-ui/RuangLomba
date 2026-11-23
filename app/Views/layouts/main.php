<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' — RuangLomba' : 'RuangLomba — Temukan Peluang. Buka Jalanmu.' ?></title>
    <meta name="description"
        content="RuangLomba adalah platform untuk menemukan lomba, beasiswa, webinar, workshop, seminar, dan berbagai peluang bagi pelajar dan anak muda.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap 5.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (dipakai halaman Cari Peluang) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Theme RuangLomba -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>

    <?php
    $currentPath = trim(uri_string(), '/');
    $isLoggedIn  = (bool) session()->get('isLoggedIn');
    $userRole    = (string) session()->get('userRole');
    $userName    = trim((string) session()->get('userName'));
    $userId      = (int) session()->get('userId');

    $isAdminPanel = $isLoggedIn && $userRole === 'admin'
        && (str_starts_with($currentPath, 'admin/') || $currentPath === 'notifikasi');
    $isOrganizerPanel = $isLoggedIn && $userRole === 'organizer'
        && (str_starts_with($currentPath, 'organizer/') || $currentPath === 'notifikasi');
    $isParticipantPage = $isLoggedIn && $userRole === 'participant' && (
        in_array($currentPath, ['pendaftaran-saya', 'tersimpan', 'profil', 'tim', 'notifikasi'], true)
        || preg_match('#^peluang/\d+/daftar$#', $currentPath) === 1
    );
    $showRoleSidebar = $isAdminPanel || $isOrganizerPanel || $isParticipantPage;
    $hidePublicNavbar = $isAdminPanel || $isOrganizerPanel;

    $userAvatar = '';
    if ($isLoggedIn && $userId > 0) {
        $profile = db_connect()->table('users')
            ->select('avatar')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
        $userAvatar = media_url($profile['avatar'] ?? '');
    }

    $displayUserName = $userName;
    if (mb_strlen($userName) > 16) {
        $nameParts = preg_split('/\s+/u', $userName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($nameParts) > 1) {
            $displayUserName = array_shift($nameParts);
            foreach ($nameParts as $part) {
                $displayUserName .= ' ' . mb_strtoupper(mb_substr($part, 0, 1));
            }
        } else {
            $displayUserName = mb_substr($userName, 0, 13) . '…';
        }
    }
    ?>

    <div class="scroll-progress"></div>

    <?php if (! $hidePublicNavbar): ?>
    <?= view('layouts/navbar', [
        'userAvatar'      => $userAvatar,
        'displayUserName' => $displayUserName,
    ]) ?>
    <?php endif; ?>

    <?php if ($showRoleSidebar): ?>
    <div class="role-app-shell role-app-<?= esc($userRole, 'attr') ?>">
        <?= view('layouts/role_sidebar', [
            'currentPath'     => $currentPath,
            'displayUserName' => $displayUserName,
            'userAvatar'      => $userAvatar,
            'userName'        => $userName,
            'userRole'        => $userRole,
        ]) ?>
        <main class="role-app-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <?php else: ?>
    <?= $this->renderSection('content') ?>
    <?php endif; ?>

    <?= $this->include('layouts/confirm_dialog') ?>
    <?= $this->include('layouts/toast') ?>

    <!-- Bootstrap Bundle (termasuk Popper, dipakai untuk navbar collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>

</html>