<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* Tombol hati: hapus dari tersimpan */
.bm-remove {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 4;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, .95);
    color: #E11D48;
    font-size: 15px;
    cursor: pointer;
    box-shadow: 0 8px 18px -8px rgba(0, 18, 51, .45);
    transition: transform .25s var(--ease), background .25s ease, color .25s ease;
}

.bm-remove:hover {
    transform: scale(1.12);
    background: #E11D48;
    color: #fff;
}

/* Pembungkus kartu: animasi saat dihapus */
.bm-col {
    display: flex;
    transition: opacity .35s ease, transform .35s ease;
}

.bm-col>.card {
    flex: 1;
}

.bm-col.is-leaving {
    opacity: 0;
    transform: scale(.94);
}
</style>

<section class="section section-gradient">
    <div class="container">
        <div class="section-head mb-4">
            <h2>Peluang Tersimpan</h2>
            <p>Peluang yang kamu simpan, supaya mudah ditemukan lagi sebelum deadline.</p>
        </div>

        <?php if (empty($bookmarks)): ?>
        <div class="help-contact mt-0">
            <h2>Belum ada peluang tersimpan</h2>
            <p>Tekan tombol Simpan di halaman peluang untuk menyimpannya di sini.</p>
            <a href="<?= base_url('cari') ?>" class="btn btn-primary">Jelajahi Peluang</a>
        </div>
        <?php else: ?>
        <div class="competition-grid" id="bmGrid" data-csrf="<?= csrf_hash() ?>">
            <?php foreach ($bookmarks as $b): ?>
            <div class="bm-col">
                <?= view('components/opportunity_card', ['competition' => $b, 'removable' => true], ['saveData' => false]) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    var grid = document.getElementById('bmGrid');
    if (!grid) return;

    var csrf = grid.dataset.csrf;
    var busy = false;

    grid.addEventListener('click', function(e) {
        var btn = e.target.closest('.bm-remove');
        if (!btn || busy) return;

        busy = true;
        var col = btn.closest('.bm-col');

        fetch(btn.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                if (res.redirected) {
                    window.location.href = res.url;
                    throw new Error('redirect');
                }
                return res.json().catch(function() {
                        return {};
                    })
                    .then(function(data) {
                        return {
                            ok: res.ok,
                            data: data
                        };
                    });
            })
            .then(function(r) {
                if (r.data.csrf) csrf = r.data.csrf;
                if (!r.ok) throw new Error('gagal');

                // Ternyata belum tersimpan (jarang terjadi): muat ulang agar daftar sesuai data
                if (r.data.saved) {
                    window.location.reload();
                    return;
                }

                col.classList.add('is-leaving');
                setTimeout(function() {
                    col.remove();
                    if (!grid.querySelector('.bm-col')) window.location.reload();
                }, 350);
            })
            .catch(function(err) {
                if (err.message !== 'redirect') alert('Gagal menghapus dari tersimpan. Coba lagi.');
            })
            .finally(function() {
                busy = false;
            });
    });
})();
</script>

<?= $this->endSection() ?>