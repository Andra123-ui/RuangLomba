<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Masuk') ?> · RuangLomba</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
    :root {
        --navy: #00246B;
        --blue: #2563FF;
        --sky: #CADCFC;
        --cyan: #00D9FF;
        --ink: #111111;
        --gray: #6B7280;
        --line: #E5E7EB;
        --ease: cubic-bezier(.22, 1, .36, 1);
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        min-height: 100%;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        color: var(--ink);
        background: #fff;
        -webkit-font-smoothing: antialiased;
    }

    /* ---------- Layout ---------- */
    .auth-shell {
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        min-height: 100vh;
    }

    /* Panel kiri */
    .auth-aside {
        position: relative;
        overflow: hidden;
        color: #fff;
        padding: 48px 56px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: linear-gradient(135deg, #00246B 0%, #0b3fb8 45%, #2563FF 100%);
        background-size: 200% 200%;
        animation: bgShift 14s ease-in-out infinite alternate;
    }

    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .55;
        pointer-events: none;
    }

    .blob.b1 {
        width: 340px;
        height: 340px;
        background: var(--cyan);
        top: -90px;
        right: -80px;
        animation: drift 11s ease-in-out infinite alternate;
    }

    .blob.b2 {
        width: 300px;
        height: 300px;
        background: #5b8dff;
        bottom: -100px;
        left: -60px;
        animation: drift 13s ease-in-out infinite alternate-reverse;
    }

    .blob.b3 {
        width: 180px;
        height: 180px;
        background: var(--sky);
        top: 45%;
        left: 55%;
        opacity: .25;
        animation: drift 9s ease-in-out infinite alternate;
    }

    .brand {
        font-family: 'Instrument Serif', serif;
        font-size: 1.9rem;
        letter-spacing: -.01em;
        color: inherit;
        text-decoration: none;
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: baseline;
        gap: 2px;
    }

    .brand i {
        color: var(--cyan);
        font-style: normal;
    }

    .aside-copy {
        position: relative;
        z-index: 2;
        max-width: 460px;
    }

    .aside-copy h2 {
        font-family: 'Instrument Serif', serif;
        font-weight: 400;
        font-size: clamp(2.4rem, 4vw, 3.6rem);
        line-height: 1.04;
        letter-spacing: -.02em;
        margin: 0 0 16px;
    }

    .aside-copy h2 em {
        font-style: italic;
        color: var(--cyan);
    }

    .aside-copy p {
        color: rgba(255, 255, 255, .78);
        font-size: 1.02rem;
        line-height: 1.6;
        margin: 0;
    }

    .float-stack {
        position: relative;
        z-index: 2;
        height: 150px;
        margin-top: 28px;
    }

    .float-card {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: .88rem;
        font-weight: 500;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .28);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 20, 80, .25);
        animation: floaty 6s ease-in-out infinite;
    }

    .float-card .ic {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, .2);
    }

    .float-card small {
        display: block;
        color: rgba(255, 255, 255, .7);
        font-weight: 400;
        font-size: .75rem;
    }

    .fc1 {
        left: 0;
        top: 0;
    }

    .fc2 {
        left: 150px;
        top: 56px;
        animation-delay: -2s;
    }

    .fc3 {
        left: 20px;
        top: 104px;
        animation-delay: -4s;
    }

    .aside-foot {
        position: relative;
        z-index: 2;
        color: rgba(255, 255, 255, .6);
        font-size: .82rem;
    }

    /* Panel kanan */
    .auth-main {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        position: relative;
    }

    .auth-main::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(520px 320px at 100% 0%, rgba(37, 99, 255, .07), transparent 70%),
            radial-gradient(420px 300px at 0% 100%, rgba(0, 217, 255, .07), transparent 70%);
    }

    .auth-card {
        width: 100%;
        max-width: 440px;
        position: relative;
        animation: rise .7s var(--ease) both;
    }

    .auth-card.wide {
        max-width: 480px;
    }

    .mobile-brand {
        display: none;
        margin-bottom: 28px;
        color: var(--navy);
    }

    .auth-title {
        font-family: 'Instrument Serif', serif;
        font-weight: 400;
        font-size: 2.5rem;
        letter-spacing: -.02em;
        line-height: 1.05;
        margin: 0 0 8px;
        color: var(--ink);
    }

    .auth-sub {
        color: var(--gray);
        margin-bottom: 28px;
    }

    .stagger>* {
        animation: rise .6s var(--ease) both;
        animation-delay: calc(var(--i, 0) * 55ms + 120ms);
    }

    /* ---------- Form ---------- */
    .form-label {
        font-weight: 500;
        font-size: .875rem;
        margin-bottom: 6px;
    }

    .auth-input {
        border: 1.5px solid var(--line);
        border-radius: 14px;
        padding: 13px 16px;
        background: #F8FAFF;
        font-size: .96rem;
        transition: border-color .25s, box-shadow .25s, background .25s, transform .25s var(--ease);
    }

    .auth-input:hover {
        border-color: #cfd8ea;
    }

    .auth-input:focus {
        border-color: var(--blue);
        background: #fff;
        transform: translateY(-1px);
        box-shadow: 0 0 0 4px rgba(37, 99, 255, .14), 0 8px 20px rgba(37, 99, 255, .08);
    }

    select.auth-input {
        cursor: pointer;
    }

    .auth-pass {
        position: relative;
    }

    .auth-pass .auth-input {
        padding-right: 48px;
    }

    .auth-pass-toggle {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border: 0;
        background: transparent;
        color: var(--gray);
        border-radius: 10px;
        transition: color .2s, background .2s;
    }

    .auth-pass-toggle:hover {
        color: var(--blue);
        background: rgba(37, 99, 255, .08);
    }

    .btn-primary {
        --bs-btn-bg: var(--blue);
        --bs-btn-border-color: var(--blue);
        --bs-btn-hover-bg: #1b4fe0;
        --bs-btn-hover-border-color: #1b4fe0;
        --bs-btn-active-bg: #163fb5;
        --bs-btn-active-border-color: #163fb5;
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        padding: 13px 18px;
        font-weight: 600;
        background-image: linear-gradient(135deg, #2563FF, #1b4fe0);
        box-shadow: 0 10px 24px rgba(37, 99, 255, .28);
        transition: transform .25s var(--ease), box-shadow .25s var(--ease);
    }

    .btn-primary::after {
        content: '';
        position: absolute;
        top: 0;
        left: -80%;
        width: 50%;
        height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .45), transparent);
        transform: skewX(-20deg);
        transition: left .7s var(--ease);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(37, 99, 255, .34);
    }

    .btn-primary:hover::after {
        left: 130%;
    }

    .btn-primary:active {
        transform: translateY(0) scale(.985);
    }

    .btn-primary.loading {
        pointer-events: none;
        opacity: .85;
    }

    .btn-primary .spin {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, .5);
        border-top-color: #fff;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: -2px;
        animation: spin .7s linear infinite;
    }

    .btn-primary.loading .spin {
        display: inline-block;
    }

    .btn-ghost {
        border: 1.5px solid var(--line);
        background: #fff;
        color: var(--ink);
        border-radius: 14px;
        padding: 11px 18px;
        font-weight: 500;
        font-size: .92rem;
        transition: all .25s var(--ease);
    }

    .btn-ghost:hover:not(:disabled) {
        border-color: var(--blue);
        color: var(--blue);
        transform: translateY(-1px);
    }

    .btn-ghost:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .alert {
        border: 0;
        border-radius: 14px;
        font-size: .9rem;
        animation: slideIn .5s var(--ease) both;
    }

    .alert-danger {
        background: #FEF2F2;
        color: #B42318;
        animation: slideIn .5s var(--ease) both, shake .5s .15s;
    }

    .alert-success {
        background: #ECFDF3;
        color: #067647;
    }

    .auth-link {
        color: var(--blue);
        font-weight: 500;
        text-decoration: none;
        background: linear-gradient(currentColor, currentColor) 0 100% / 0 1.5px no-repeat;
        transition: background-size .3s var(--ease);
    }

    .auth-link:hover {
        background-size: 100% 1.5px;
        color: var(--blue);
    }

    .auth-switch,
    .auth-terms {
        text-align: center;
        color: var(--gray);
        font-size: .9rem;
        margin-top: 22px;
    }

    .auth-terms {
        font-size: .78rem;
        margin-top: 16px;
        line-height: 1.6;
    }

    .auth-terms a,
    .auth-switch a {
        color: var(--blue);
        text-decoration: none;
        font-weight: 500;
    }

    .auth-terms a:hover,
    .auth-switch a:hover {
        text-decoration: underline;
    }

    /* Switch peran */
    .role-switch {
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1fr;
        padding: 5px;
        margin-bottom: 22px;
        background: #EEF3FF;
        border-radius: 16px;
    }

    .role-pill {
        position: absolute;
        top: 5px;
        bottom: 5px;
        left: 5px;
        width: calc(50% - 5px);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(37, 99, 255, .18);
        transition: transform .45s var(--ease);
    }

    .role-switch:has(input[value="organizer"]:checked) .role-pill {
        transform: translateX(100%);
    }

    .role-option {
        position: relative;
        z-index: 1;
        margin: 0;
        text-align: center;
        cursor: pointer;
    }

    .role-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-option span {
        display: block;
        padding: 11px 8px;
        font-size: .9rem;
        font-weight: 500;
        color: var(--gray);
        transition: color .3s;
    }

    .role-option input:checked+span {
        color: var(--blue);
        font-weight: 600;
    }

    .role-option input:focus-visible+span {
        outline: 2px solid var(--blue);
        outline-offset: -2px;
        border-radius: 12px;
    }

    .role-fields {
        border: 0;
        padding: 0;
        margin: 0;
        min-width: 0;
    }

    .role-fields:not([hidden]) {
        animation: fadeSlide .5s var(--ease) both;
    }

    /* Strength meter */
    .meter {
        display: flex;
        gap: 6px;
        margin-top: 10px;
    }

    .meter i {
        flex: 1;
        height: 5px;
        border-radius: 99px;
        background: var(--line);
        transition: background .35s, transform .35s var(--ease);
        transform-origin: left;
    }

    .meter[data-s="1"] i:nth-child(-n+1) {
        background: #F04438;
    }

    .meter[data-s="2"] i:nth-child(-n+2) {
        background: #F79009;
    }

    .meter[data-s="3"] i:nth-child(-n+3) {
        background: #2563FF;
    }

    .meter[data-s="4"] i {
        background: #12B76A;
    }

    .meter-text {
        font-size: .78rem;
        color: var(--gray);
        margin-top: 6px;
        min-height: 1.1em;
    }

    /* OTP */
    .otp-badge {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        color: var(--blue);
        background: linear-gradient(135deg, #EAF0FF, #D9F7FF);
        margin-bottom: 20px;
        animation: pop .7s var(--ease) both, floaty 5s 1s ease-in-out infinite;
    }

    .otp-wrap {
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }

    .otp-box {
        width: 100%;
        max-width: 58px;
        height: 64px;
        text-align: center;
        font-size: 1.6rem;
        font-weight: 600;
        border: 1.5px solid var(--line);
        border-radius: 16px;
        background: #F8FAFF;
        color: var(--ink);
        transition: all .25s var(--ease);
        caret-color: var(--blue);
    }

    .otp-box:focus {
        border-color: var(--blue);
        background: #fff;
        transform: translateY(-3px) scale(1.04);
        box-shadow: 0 0 0 4px rgba(37, 99, 255, .14), 0 10px 22px rgba(37, 99, 255, .12);
        outline: 0;
    }

    .otp-box.filled {
        border-color: var(--blue);
        background: #fff;
        animation: pop .3s var(--ease);
    }

    .otp-wrap.err .otp-box {
        border-color: #F04438;
        animation: shake .45s;
    }

    /* ---------- Keyframes ---------- */
    @keyframes rise {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-14px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes pop {
        0% {
            transform: scale(.85);
        }

        60% {
            transform: scale(1.08);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes floaty {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes drift {
        from {
            transform: translate(0, 0) scale(1);
        }

        to {
            transform: translate(40px, 30px) scale(1.15);
        }
    }

    @keyframes bgShift {
        from {
            background-position: 0% 0%;
        }

        to {
            background-position: 100% 100%;
        }
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-7px);
        }

        40% {
            transform: translateX(6px);
        }

        60% {
            transform: translateX(-4px);
        }

        80% {
            transform: translateX(3px);
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
        .auth-shell {
            grid-template-columns: 1fr;
        }

        .auth-aside {
            display: none;
        }

        .auth-main {
            align-items: flex-start;
            padding: 28px 20px 40px;
            min-height: 100vh;
        }

        .mobile-brand {
            display: inline-flex;
        }

        .auth-title {
            font-size: 2.1rem;
        }
    }

    @media (max-width: 400px) {
        .otp-wrap {
            gap: 6px;
        }

        .otp-box {
            height: 56px;
            font-size: 1.4rem;
            border-radius: 14px;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
    </style>
</head>

<body>
    <div class="auth-shell">

        <aside class="auth-aside">
            <span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span>

            <a href="<?= base_url('/') ?>" class="brand">RuangLomba<i>.</i></a>

            <div>
                <div class="aside-copy">
                    <h2>Temukan peluang. <em>Buka jalanmu.</em></h2>
                    <p>Lomba, beasiswa, webinar, dan event terbaik untuk pelajar dan anak muda, semua dalam satu tempat.
                    </p>
                </div>
                <div class="float-stack" aria-hidden="true">
                    <div class="float-card fc1"><span class="ic"><i class="fa-solid fa-trophy"></i></span>
                        <div>Lomba Nasional<small>1.200+ kompetisi</small></div>
                    </div>
                    <div class="float-card fc2"><span class="ic"><i class="fa-solid fa-graduation-cap"></i></span>
                        <div>Beasiswa<small>Terbuka untuk semua jenjang</small></div>
                    </div>
                    <div class="float-card fc3"><span class="ic"><i class="fa-solid fa-bolt"></i></span>
                        <div>Webinar &amp; Workshop<small>Belajar langsung dari ahli</small></div>
                    </div>
                </div>
            </div>

            <div class="aside-foot">© <?= date('Y') ?> RuangLomba. Semua hak dilindungi.</div>
        </aside>

        <main class="auth-main">
            <div class="auth-card <?= esc($cardClass ?? '', 'attr') ?>">
                <a href="<?= base_url('/') ?>" class="brand mobile-brand">RuangLomba<i>.</i></a>
                <div class="stagger" id="stagger">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    (function() {
        // Delay animasi bertahap untuk setiap elemen
        document.querySelectorAll('#stagger > *').forEach(function(el, i) {
            el.style.setProperty('--i', i);
        });

        // Tampil/sembunyi password
        document.querySelectorAll('[data-toggle-pass]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.getElementById(btn.dataset.togglePass);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', show);
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' :
                'Tampilkan password');
                btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' :
                    'fa-regular fa-eye';
            });
        });

        // State loading di tombol submit
        document.querySelectorAll('form[data-loading]').forEach(function(form) {
            form.addEventListener('submit', function() {
                var b = form.querySelector('button[type="submit"]');
                if (b) {
                    b.classList.add('loading');
                }
            });
        });
    })();
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>