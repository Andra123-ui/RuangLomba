<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Ikon per jenis notifikasi
$icons = [
    'registration'   => 'fa-clipboard-check',
    'competition'    => 'fa-bullhorn',
    'deadline'       => 'fa-clock',
    'recommendation' => 'fa-lightbulb',
];

// "5 menit lalu", "2 hari lalu", dst.
$ago = function (string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)     return 'Baru saja';
    if ($diff < 3600)   return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400)  return floor($diff / 3600) . ' jam lalu';
    if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
    return date('d M Y', strtotime($dt));
};
?>

<style>
/* ================================
   LATAR HALAMAN
   Selalu setinggi layar, dengan cahaya blur di tiga sudut
================================= */
.notif-page {
    position: relative;
    overflow: hidden;
    min-height: calc(100vh - 77px);
    padding: 56px 0 96px;
    background: linear-gradient(165deg, #FFFFFF 0%, #F0F4FE 40%, #E5EDFC 72%, #F7F9FF 100%);
}

.notif-page::before,
.notif-page::after,
.notif-glow {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}

.notif-page::before {
    width: 420px;
    height: 420px;
    top: -150px;
    right: -100px;
    background: radial-gradient(circle, var(--ocean-light) 0%, transparent 70%);
    opacity: .75;
}

.notif-page::after {
    width: 380px;
    height: 380px;
    bottom: -160px;
    left: -100px;
    background: radial-gradient(circle, var(--ocean-aqua) 0%, transparent 70%);
    opacity: .28;
}

.notif-glow {
    width: 300px;
    height: 300px;
    top: 42%;
    right: -120px;
    background: radial-gradient(circle, var(--ocean-teal) 0%, transparent 70%);
    opacity: .12;
}

.notif-wrap {
    position: relative;
    z-index: 1;
    max-width: 780px;
}

/* ================================
   HEADER
================================= */
.notif-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 28px;
}

.notif-head-main {
    display: flex;
    align-items: center;
    gap: 16px;
}

.notif-head-ico {
    width: 56px;
    height: 56px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    color: #fff;
    font-size: 22px;
    background: linear-gradient(135deg, var(--ocean-navy), var(--ocean-mid) 60%, var(--ocean-teal));
    box-shadow: 0 16px 28px -14px rgba(0, 36, 107, .65);
}

.notif-head h1 {
    margin: 0;
    font-size: clamp(30px, 4vw, 40px);
    line-height: 1.1;
}

.notif-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 8px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--c-gray);
    background: rgba(255, 255, 255, .8);
    border: 1px solid var(--c-border);
}

.notif-chip.has-unread {
    color: #BE123C;
    background: #FFF1F2;
    border-color: #FECDD3;
}

.notif-chip.has-unread::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #E11D48;
}

/* ================================
   DAFTAR NOTIFIKASI
================================= */
.notif-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notif-form {
    margin: 0;
}

.notif-item {
    position: relative;
    overflow: hidden;
    width: 100%;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 18px 22px;
    border: 1px solid var(--c-border);
    border-radius: 18px;
    background: rgba(255, 255, 255, .92);
    text-align: left;
    cursor: pointer;
    box-shadow: 0 14px 30px -24px rgba(0, 36, 107, .35);
    transition: transform .3s var(--ease), box-shadow .3s var(--ease), border-color .3s var(--ease);
}

.notif-item:hover {
    transform: translateY(-3px);
    border-color: var(--c-blue);
    box-shadow: 0 22px 40px -22px rgba(0, 36, 107, .4);
}

/* Belum dibaca: tint biru + garis aksen di kiri */
.notif-item.is-unread {
    background: linear-gradient(135deg, #FFFFFF 0%, #EEF3FF 100%);
    border-color: var(--c-lightblue);
}

.notif-item.is-unread::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--ocean-navy), var(--ocean-aqua));
}

.notif-ico {
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--c-blue-dim);
    color: var(--c-blue);
    font-size: 17px;
    transition: transform .3s var(--ease);
}

.notif-item:hover .notif-ico {
    transform: rotate(-6deg) scale(1.06);
}

.notif-item.is-unread .notif-ico {
    background: linear-gradient(135deg, var(--ocean-navy), var(--ocean-mid) 60%, var(--ocean-aqua));
    color: #fff;
    box-shadow: 0 10px 18px -10px rgba(0, 36, 107, .6);
}

.notif-body {
    flex: 1;
    min-width: 0;
}

.notif-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14.5px;
    font-weight: 600;
    color: var(--c-black);
}

.notif-dot {
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #E11D48;
    box-shadow: 0 0 0 3px rgba(225, 29, 72, .15);
}

.notif-msg {
    margin: 4px 0 0;
    font-size: 13.5px;
    line-height: 1.6;
    color: #4B5563;
    overflow-wrap: anywhere;
}

.notif-time {
    flex-shrink: 0;
    padding-top: 2px;
    font-size: 12px;
    color: var(--c-gray);
    white-space: nowrap;
}

/* ================================
   KOSONG
================================= */
.notif-empty {
    padding: 64px 24px;
    text-align: center;
    border: 1.5px dashed var(--c-lightblue);
    border-radius: 22px;
    background: rgba(255, 255, 255, .8);
}

.notif-empty-ico {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 28px;
    color: var(--c-blue);
    background: var(--c-blue-dim);
}

.notif-empty h2 {
    margin: 0 0 8px;
    font-size: 26px;
}

.notif-empty p {
    margin: 0;
    color: var(--c-gray);
    font-size: 14.5px;
}

@media (max-width: 575.98px) {
    .notif-page {
        padding: 36px 0 64px;
    }

    .notif-head-ico {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        font-size: 19px;
    }

    .notif-item {
        padding: 14px 16px;
        gap: 12px;
    }

    .notif-ico {
        width: 40px;
        height: 40px;
        font-size: 15px;
    }

    .notif-time {
        display: none;
    }
}
</style>

<section class="notif-page">
    <span class="notif-glow" aria-hidden="true"></span>

    <div class="container notif-wrap">

        <header class="notif-head">
            <div class="notif-head-main">
                <span class="notif-head-ico"><i class="fa-regular fa-bell" aria-hidden="true"></i></span>
                <div>
                    <h1>Notifikasi</h1>
                    <span class="notif-chip<?= $unread > 0 ? ' has-unread' : '' ?>">
                        <?= $unread > 0 ? $unread . ' belum dibaca' : 'Semua sudah dibaca' ?>
                    </span>
                </div>
            </div>

            <?php if ($unread > 0): ?>
            <form action="<?= base_url('notifikasi/baca-semua') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-check-double me-1" aria-hidden="true"></i> Tandai semua dibaca
                </button>
            </form>
            <?php endif; ?>
        </header>

        <?php if (empty($notifications)): ?>
        <div class="notif-empty">
            <div class="notif-empty-ico"><i class="fa-regular fa-bell-slash" aria-hidden="true"></i></div>
            <h2>Belum ada notifikasi</h2>
            <p>Pemberitahuan tentang pendaftaran dan peluangmu akan muncul di sini.</p>
        </div>
        <?php else: ?>
        <div class="notif-list">
            <?php foreach ($notifications as $n):
                $unreadItem = (int) $n['is_read'] === 0;
                $icon = $icons[$n['type']] ?? 'fa-bell';
            ?>
            <form action="<?= base_url('notifikasi/' . $n['id'] . '/baca') ?>" method="post" class="notif-form">
                <?= csrf_field() ?>
                <button type="submit" class="notif-item<?= $unreadItem ? ' is-unread' : '' ?>">
                    <span class="notif-ico"><i class="fa-solid <?= esc($icon, 'attr') ?>" aria-hidden="true"></i></span>

                    <span class="notif-body">
                        <span class="notif-title">
                            <?= esc($n['title']) ?>
                            <?php if ($unreadItem): ?><span class="notif-dot"
                                aria-label="Belum dibaca"></span><?php endif; ?>
                        </span>
                        <span class="notif-msg d-block"><?= esc($n['message']) ?></span>
                    </span>

                    <span class="notif-time"><?= esc($ago($n['created_at'])) ?></span>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->endSection() ?>