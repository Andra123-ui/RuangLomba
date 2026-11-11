<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'RuangLomba') ?> | RuangLomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>

<body>
    <main class="auth">
        <div class="auth-form-side">
            <div class="auth-form-inner">
                <a href="<?= base_url('/') ?>" class="auth-logo">RuangLomba</a>
                <?= $this->renderSection('content') ?>
            </div>
        </div>

        <aside class="auth-visual" aria-hidden="true">
            <div class="auth-visual-bg"
                style="background-image:url('<?= base_url('assets/images/hero-illustration.jpg') ?>');"></div>
            <div class="auth-visual-overlay"></div>
            <div class="auth-visual-copy">
                <h2>Temukan peluang.<br>Buka jalanmu.</h2>
                <p>Satu tempat untuk lomba, beasiswa, webinar, dan banyak kesempatan lainnya.</p>
            </div>
        </aside>
    </main>

    <script>
    document.querySelectorAll('[data-toggle-pass]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.getElementById(btn.dataset.togglePass);
            var icon = btn.querySelector('i');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>