<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <h1 class="reveal">Kebijakan Privasi</h1>
        <p class="reveal">Terakhir diperbarui: <?= date('d F Y') ?></p>
    </div>
</section>

<section class="section page-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 page-prose">
                <p>
                    Kebijakan ini menjelaskan bagaimana RuangLomba mengumpulkan, menggunakan, dan melindungi
                    data pribadimu.
                </p>

                <h2>1. Data yang Kami Kumpulkan</h2>
                <ul>
                    <li>Data akun: nama, alamat email, dan data profil yang kamu isi.</li>
                    <li>Data penggunaan: peluang yang kamu lihat, sukai, atau cari.</li>
                    <li>Data teknis: jenis perangkat, browser, dan alamat IP untuk keamanan dan analitik.</li>
                    <li>Informasi minat dan keahlian yang kamu masukkan untuk fitur rekomendasi.</li>
                </ul>

                <h2>2. Penggunaan Data</h2>
                <ul>
                    <li>Menyediakan dan menjalankan layanan serta akun kamu.</li>
                    <li>Menampilkan dan merekomendasikan peluang yang relevan.</li>
                    <li>Meningkatkan kualitas, keamanan, dan performa platform.</li>
                    <li>Menghubungi kamu terkait layanan, jika diperlukan.</li>
                </ul>

                <h2>3. Berbagi Data</h2>
                <p>
                    Kami tidak menjual data pribadimu. Data hanya dibagikan jika diperlukan untuk menjalankan
                    layanan (misalnya penyedia hosting), dipersyaratkan oleh hukum, atau atas persetujuanmu.
                </p>

                <h2>4. Keamanan Data</h2>
                <p>
                    Kami menerapkan langkah wajar untuk melindungi data dari akses yang tidak sah. Namun, tidak
                    ada sistem yang sepenuhnya bebas risiko, jadi jaga juga kerahasiaan akunmu.
                </p>

                <h2>5. Cookie</h2>
                <p>
                    Kami dapat menggunakan cookie untuk menjaga sesi login dan memahami penggunaan situs. Kamu
                    bisa mengatur atau menonaktifkan cookie lewat pengaturan browser.
                </p>

                <h2>6. Hak Kamu</h2>
                <ul>
                    <li>Mengakses dan memperbarui data akunmu.</li>
                    <li>Meminta penghapusan akun dan data terkait.</li>
                    <li>Menarik persetujuan penggunaan data tertentu.</li>
                </ul>

                <h2>7. Perubahan Kebijakan</h2>
                <p>
                    Kebijakan ini dapat diperbarui sewaktu-waktu. Perubahan penting akan diberitahukan melalui
                    situs.
                </p>

                <h2>8. Kontak</h2>
                <p>
                    Untuk pertanyaan atau permintaan terkait data pribadi, hubungi
                    <a href="mailto:halo@ruanglomba.id">halo@ruanglomba.id</a>.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>