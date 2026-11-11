<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <h1 class="reveal">Tentang RuangLomba</h1>
        <p class="reveal">Temukan peluang. Buka jalanmu.</p>
    </div>
</section>

<section class="section page-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 page-prose">
                <h2>Apa itu RuangLomba?</h2>
                <p>
                    RuangLomba adalah platform yang mempertemukan pelajar dan mahasiswa dengan beragam peluang,
                    mulai dari lomba, beasiswa, webinar, workshop, hingga seminar, dalam satu tempat. Kami ingin
                    kesempatan untuk berkembang lebih mudah ditemukan, bukan hanya oleh mereka yang kebetulan
                    tahu.
                </p>

                <h2>Mengapa kami ada?</h2>
                <p>
                    Informasi lomba dan event sering tersebar di banyak tempat dan mudah terlewat. RuangLomba
                    mengumpulkannya agar kamu bisa mencari, membandingkan, dan menyiapkan diri dengan lebih
                    tenang.
                </p>

                <h2>Apa yang bisa kamu lakukan?</h2>
                <ul>
                    <li>Menjelajahi peluang berdasarkan jenis, minat, jenjang, biaya, dan lokasi.</li>
                    <li>Melihat detail, deadline, dan persyaratan sebelum mendaftar.</li>
                    <li>Mendapat rekomendasi lomba yang sesuai lewat LombaMatch AI.</li>
                    <li>Untuk penyelenggara: mempublikasikan lomba atau event agar ditemukan peserta yang tepat.</li>
                </ul>

                <h2>Catatan penting</h2>
                <p>
                    Pendaftaran dan seleksi dilakukan oleh masing-masing penyelenggara. RuangLomba membantu
                    kamu menemukan informasinya, tetapi tidak menjamin hasil seleksi atau kebenaran setiap
                    detail dari penyelenggara. Selalu periksa informasi resmi sebelum mendaftar.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="<?= base_url('cari') ?>" class="btn btn-primary">Jelajahi Peluang</a>
                    <a href="<?= base_url('untuk-organizer') ?>" class="btn btn-outline-primary">Publikasikan
                        Lombamu</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>