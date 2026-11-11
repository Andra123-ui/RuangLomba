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

    <div class="scroll-progress"></div>

    <?= $this->include('layouts/navbar') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('layouts/toast') ?>

    <!-- Bootstrap Bundle (termasuk Popper, dipakai untuk navbar collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>

</html>