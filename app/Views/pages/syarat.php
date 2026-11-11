<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <h1 class="reveal">Syarat &amp; Ketentuan</h1>
        <p class="reveal">Terakhir diperbarui: <?= date('d F Y') ?></p>
    </div>
</section>

<section class="section page-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 page-prose">
                <p>
                    Dengan mengakses atau menggunakan RuangLomba, kamu menyetujui syarat dan ketentuan berikut.
                    Jika tidak setuju, mohon untuk tidak menggunakan layanan ini.
                </p>

                <h2>1. Tentang Layanan</h2>
                <p>
                    RuangLomba menyediakan informasi lomba, beasiswa, webinar, workshop, seminar, dan peluang
                    sejenis yang dipublikasikan oleh penyelenggara. Kami bukan penyelenggara dari peluang
                    tersebut, kecuali dinyatakan lain.
                </p>

                <h2>2. Akun Pengguna</h2>
                <ul>
                    <li>Kamu bertanggung jawab atas kerahasiaan akun dan seluruh aktivitas di dalamnya.</li>
                    <li>Data yang kamu berikan harus benar dan tidak menyesatkan.</li>
                    <li>Kami berhak menonaktifkan akun yang melanggar ketentuan ini.</li>
                </ul>

                <h2>3. Pendaftaran ke Peluang</h2>
                <p>
                    Pendaftaran, seleksi, biaya, dan hadiah sepenuhnya diatur oleh penyelenggara. RuangLomba
                    tidak bertanggung jawab atas keputusan penyelenggara, perubahan jadwal, pembatalan, atau
                    hasil seleksi.
                </p>

                <h2>4. Ketentuan untuk Penyelenggara</h2>
                <ul>
                    <li>Informasi yang dipublikasikan harus akurat, jelas, dan dapat dipertanggungjawabkan.</li>
                    <li>Dilarang mempublikasikan konten penipuan, menyesatkan, melanggar hukum, atau melanggar hak pihak
                        lain.</li>
                    <li>Kami berhak menolak, mengubah, atau menghapus publikasi yang tidak sesuai.</li>
                </ul>

                <h2>5. Larangan</h2>
                <ul>
                    <li>Menggunakan layanan untuk tujuan melanggar hukum.</li>
                    <li>Mengganggu keamanan atau kinerja situs, termasuk scraping atau akses otomatis tanpa izin.</li>
                    <li>Menyamar sebagai pihak lain atau menyebarkan informasi palsu.</li>
                </ul>

                <h2>6. Hak Kekayaan Intelektual</h2>
                <p>
                    Tampilan, logo, dan konten asli RuangLomba dilindungi hak cipta. Konten yang dipublikasikan
                    penyelenggara tetap menjadi milik masing-masing pemiliknya.
                </p>

                <h2>7. Batasan Tanggung Jawab</h2>
                <p>
                    Layanan disediakan sebagaimana adanya. Kami berupaya menjaga informasi tetap akurat, tetapi
                    tidak menjamin seluruh informasi selalu lengkap, terbaru, dan bebas kesalahan.
                </p>

                <h2>8. Perubahan Ketentuan</h2>
                <p>
                    Ketentuan ini dapat diperbarui sewaktu-waktu. Penggunaan layanan setelah perubahan berarti
                    kamu menyetujui ketentuan terbaru.
                </p>

                <h2>9. Kontak</h2>
                <p>
                    Pertanyaan tentang ketentuan ini dapat dikirim ke
                    <a href="mailto:halo@ruanglomba.id">halo@ruanglomba.id</a>.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>