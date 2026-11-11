<?php
/**
 * Kartu peluang — dipakai di halaman Jelajahi dan Peluang Tersimpan.
 *
 * Data:
 *   $competition  baris competitions + category_name + organizer_name
 *   $removable    (opsional) true = tampilkan tombol hapus dari tersimpan
 */
$removable  = $removable ?? false;
$deadlineTs = strtotime($competition['deadline']);
$daysLeft   = (int) floor(($deadlineTs - strtotime('today')) / 86400);
$isClosed   = $daysLeft < 0;
$isFree     = (int) $competition['registration_fee'] === 0;
$orgName    = $competition['organizer_name'] ?? '-';
$orgInitial = strtoupper(mb_substr($orgName, 0, 1));

$place = $competition['implementation_type'] . ' (' . $competition['competition_level'] . ')';
if (! empty($competition['location']) && $competition['location'] !== $competition['implementation_type']) {
    $place .= ' · ' . $competition['location'];
}
?>
<article class="card card-ruang opp-card h-100">

    <div class="opp-thumb">
        <?php if (! empty($competition['poster'])): ?>
        <img src="<?= esc(media_url($competition['poster']), 'attr') ?>" alt="<?= esc($competition['title']) ?>"
            loading="lazy">
        <?php else: ?>
        <div class="poster-placeholder"><i class="fa-solid fa-trophy"></i></div>
        <?php endif; ?>

        <span class="opp-hbadge<?= $isClosed ? ' is-closed' : '' ?>">
            <?= $isClosed ? 'Ditutup' : 'H-' . $daysLeft ?>
        </span>

        <?php if ($removable): ?>
        <button type="button" class="bm-remove" data-url="<?= base_url('peluang/' . $competition['id'] . '/simpan') ?>"
            title="Hapus dari tersimpan" aria-label="Hapus dari tersimpan">
            <i class="fa-solid fa-heart"></i>
        </button>
        <?php endif; ?>
    </div>

    <div class="opp-stats">
        <span class="opp-stat">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path
                    d="M12 20.5C12 20.5 3.5 15.2 3.5 9.3C3.5 6.4 5.9 4 8.8 4C10.4 4 11.6 4.9 12 5.9C12.4 4.9 13.6 4 15.2 4C18.1 4 20.5 6.4 20.5 9.3C20.5 15.2 12 20.5 12 20.5Z"
                    stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round" />
            </svg>
            <?= number_format((int) $competition['likes'], 0, ',', '.') ?> suka
        </span>
        <span class="opp-stat">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" stroke-width="1.8"
                    stroke-linejoin="round" />
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" />
            </svg>
            <?= number_format((int) $competition['views'], 0, ',', '.') ?>x dilihat
        </span>
    </div>

    <div class="opp-detail-btn">
        Lihat Detail
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </div>

    <div class="card-body">
        <div class="opp-tags">
            <span class="opp-tag"><?= esc($competition['type']) ?></span>
            <span class="opp-tag is-cat"><?= esc($competition['category_name']) ?></span>
        </div>

        <h3 class="opp-title">
            <a href="<?= base_url('peluang/' . $competition['id']) ?>" class="stretched-link">
                <?= esc($competition['title']) ?>
            </a>
        </h3>

        <ul class="opp-info">
            <li>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3 2 8l10 5 10-5-10-5z" stroke="currentColor" stroke-width="2"
                        stroke-linejoin="round" />
                    <path d="M6 10.5V16c0 1.7 2.7 3 6 3s6-1.3 6-3v-5.5" stroke="currentColor" stroke-width="2" />
                </svg>
                <span><?= esc($competition['education_level']) ?></span>
            </li>
            <li>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                    <path
                        d="M12 7v10M9.5 9.5c0-1.1 1.1-2 2.5-2s2.5.7 2.5 1.8-1 1.6-2.5 2-2.5.9-2.5 2S10.6 15 12 15s2.5-.9 2.5-2"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
                <span><?= $isFree ? 'Gratis' : 'Rp ' . number_format($competition['registration_fee'], 0, ',', '.') ?></span>
            </li>
            <li>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path d="M12 21s7-6.3 7-11.5A7 7 0 005 9.5C5 14.7 12 21 12 21z" stroke="currentColor"
                        stroke-width="2" stroke-linejoin="round" />
                    <circle cx="12" cy="9.5" r="2.4" stroke="currentColor" stroke-width="2" />
                </svg>
                <span title="<?= esc($place, 'attr') ?>"><?= esc($place) ?></span>
            </li>
            <li>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                    <path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Deadline <?= date('d M Y', $deadlineTs) ?></span>
            </li>
        </ul>

        <div class="opp-organizer">
            <span class="opp-org-avatar"><?= esc($orgInitial) ?></span>
            <div>
                <div class="opp-org-label">Diselenggarakan oleh</div>
                <div class="opp-org-name"><?= esc($orgName) ?></div>
            </div>
        </div>
    </div>

</article>