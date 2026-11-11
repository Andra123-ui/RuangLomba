<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <h1 class="reveal">Pusat Bantuan</h1>
        <p class="reveal">Jawaban untuk pertanyaan yang sering diajukan.</p>
    </div>
</section>

<section class="section page-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php $n = 0; ?>
                <?php foreach ($faqs as $group => $items): ?>
                <h2 class="faq-group reveal"><?= esc($group) ?></h2>

                <div class="accordion faq-accordion reveal" id="faq-<?= url_title($group, '-', true) ?>">
                    <?php foreach ($items as $item): $n++; ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-item-<?= $n ?>" aria-expanded="false"
                                aria-controls="faq-item-<?= $n ?>">
                                <?= esc($item['q']) ?>
                            </button>
                        </h3>
                        <div id="faq-item-<?= $n ?>" class="accordion-collapse collapse"
                            data-bs-parent="#faq-<?= url_title($group, '-', true) ?>">
                            <div class="accordion-body"><?= esc($item['a']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>

                <div class="help-contact reveal">
                    <h2>Masih butuh bantuan?</h2>
                    <p>Tim kami siap membantu. Ceritakan kendalamu dan kami akan membalas lewat email.</p>
                    <a href="mailto:halo@ruanglomba.id" class="btn btn-primary">Hubungi Kami</a>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>