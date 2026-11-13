<div class="otp-wrap" id="otpWrap" role="group" aria-label="Kode OTP 6 digit">
    <?php for ($i = 0; $i < 6; $i++): ?>
    <input type="text" class="otp-box" inputmode="numeric" pattern="[0-9]*" maxlength="1"
        autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>" aria-label="Digit <?= $i + 1 ?>">
    <?php endfor; ?>
</div>
<input type="hidden" name="otp" id="otpValue">

<script>
(function() {
    var wrap = document.getElementById('otpWrap');
    var boxes = Array.prototype.slice.call(wrap.querySelectorAll('.otp-box'));
    var hid = document.getElementById('otpValue');
    var form = hid.form;

    function sync() {
        hid.value = boxes.map(function(b) {
            return b.value;
        }).join('');
        boxes.forEach(function(b) {
            b.classList.toggle('filled', b.value !== '');
        });
    }

    boxes.forEach(function(box, i) {
        box.addEventListener('input', function() {
            box.value = box.value.replace(/\D/g, '').slice(-1);
            sync();
            if (box.value && boxes[i + 1]) boxes[i + 1].focus();
            if (hid.value.length === 6 && form.dataset.auto === '1') form.requestSubmit();
        });
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !box.value && boxes[i - 1]) {
                boxes[i - 1].focus();
                boxes[i - 1].value = '';
                sync();
            }
            if (e.key === 'ArrowLeft' && boxes[i - 1]) boxes[i - 1].focus();
            if (e.key === 'ArrowRight' && boxes[i + 1]) boxes[i + 1].focus();
        });
        box.addEventListener('focus', function() {
            box.select();
        });
        box.addEventListener('paste', function(e) {
            e.preventDefault();
            var d = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '')
                .slice(0, 6);
            d.split('').forEach(function(c, k) {
                if (boxes[k]) boxes[k].value = c;
            });
            sync();
            (boxes[Math.min(d.length, 5)] || boxes[5]).focus();
            if (hid.value.length === 6 && form.dataset.auto === '1') form.requestSubmit();
        });
    });

    // Goyang kotak kalau submit tanpa 6 digit
    form.addEventListener('submit', function(e) {
        if (hid.value.length < 6) {
            e.preventDefault();
            wrap.classList.remove('err');
            void wrap.offsetWidth;
            wrap.classList.add('err');
            (boxes.find(function(b) {
                return !b.value;
            }) || boxes[5]).focus();
        }
    });

    <?php if (session()->getFlashdata('error') || session()->getFlashdata('errors')): ?>
    wrap.classList.add('err');
    <?php endif; ?>
    boxes[0].focus();
})();
</script>