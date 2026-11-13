<?php /** @var string $action @var int $wait */ ?>
<form action="<?= base_url($action) ?>" method="post" class="text-center mt-4" id="resendForm">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-ghost" id="resendBtn" <?= $wait > 0 ? 'disabled' : '' ?>>
        <i class="fa-regular fa-paper-plane me-1"></i>
        <span id="resendText"><?= $wait > 0 ? 'Kirim ulang dalam ' . (int) $wait . ' dtk' : 'Kirim ulang kode' ?></span>
    </button>
</form>
<script>
(function() {
    var left = <?= (int) $wait ?>;
    var btn = document.getElementById('resendBtn');
    var txt = document.getElementById('resendText');
    if (left <= 0) return;
    var t = setInterval(function() {
        left--;
        if (left <= 0) {
            clearInterval(t);
            btn.disabled = false;
            txt.textContent = 'Kirim ulang kode';
            return;
        }
        txt.textContent = 'Kirim ulang dalam ' + left + ' dtk';
    }, 1000);
})();
</script>