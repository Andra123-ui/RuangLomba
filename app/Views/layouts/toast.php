<?php
$toastError   = session()->getFlashdata('error');
$toastSuccess = session()->getFlashdata('success');
$toastMsg     = $toastError ?? $toastSuccess;
?>
<?php if ($toastMsg): ?>
<div class="rl-toast <?= $toastError ? 'is-error' : 'is-success' ?>" id="rlToast" role="status" aria-live="polite">
    <span class="rl-toast-ico">
        <i class="fa-solid <?= $toastError ? 'fa-circle-exclamation' : 'fa-circle-check' ?>" aria-hidden="true"></i>
    </span>
    <div class="rl-toast-msg"><?= esc($toastMsg) ?></div>
    <button type="button" class="rl-toast-close" aria-label="Tutup">&times;</button>
</div>

<script>
(function() {
    var t = document.getElementById('rlToast');
    if (!t) return;

    requestAnimationFrame(function() {
        t.classList.add('show');
    });

    function hide() {
        t.classList.remove('show');
        setTimeout(function() {
            t.remove();
        }, 350);
    }

    t.querySelector('.rl-toast-close').addEventListener('click', hide);
    setTimeout(hide, 5000);
})();
</script>
<?php endif; ?>