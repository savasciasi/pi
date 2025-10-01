<?php
require_once __DIR__ . '/../config/config.php';

$settings = get_settings();
$hero = fetchOne('SELECT * FROM hero LIMIT 1');
$pilates = fetchOne('SELECT * FROM about_pilates LIMIT 1');
$history = fetchOne('SELECT * FROM history LIMIT 1');
$instructor = fetchOne('SELECT * FROM instructor LIMIT 1');
$trainings = fetchAll('SELECT * FROM trainings ORDER BY sort_order, id');
$equipments = fetchAll('SELECT * FROM equipments ORDER BY sort_order, id');
$plans = fetchAll('SELECT * FROM plans ORDER BY sort_order, id');
$schedule = fetchAll('SELECT * FROM schedule_entries ORDER BY day_order, start_time');
$faq = fetchAll('SELECT * FROM faq ORDER BY sort_order, id');
$footerLinks = fetchAll('SELECT * FROM footer_links ORDER BY position ASC');

$siteName = setting('site_name', 'Pi Studio Pilates');
$siteLanguage = setting('language', 'tr');
$logoPath = setting('logo');
$logoUrl = $logoPath ? media_url($logoPath) : null;
$tagline = $hero['subtitle'] ?? 'Vücudunu güçlendir, nefesinle ak.';
$contactEmail = setting('contact_email', 'info@pistudiopilates.com');
$contactPhone = setting('contact_phone', '+90 530 111 22 33');
$instagramUrl = setting('instagram_url', 'https://www.instagram.com');
$whatsappNumber = setting('whatsapp_number', '+90 530 111 22 33');
$address = $hero['address'] ?? setting('address', 'Bağdat Caddesi No:123, İstanbul');
$whatsappLink = whatsapp_link($whatsappNumber) ?: 'https://wa.me/905301112233';

$heroBackground = media_url($hero['background_media'] ?? null, 'assets/img/hero-bg.jpg', 'pilates studio interior');
$historyImage = media_url($history['image'] ?? null, 'assets/img/history.jpg', 'joseph pilates history');
$instructorPhoto = media_url($instructor['photo'] ?? null, 'assets/img/pinar.jpg', 'pilates instructor portrait');

$historyEvents = [];
$rawHistory = trim((string)($history['content'] ?? ''));
if ($rawHistory !== '') {
    foreach (preg_split('/\r\n|\r|\n/', $rawHistory) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (strpos($line, '|') !== false) {
            [$year, $text] = array_map('trim', explode('|', $line, 2));
        } else {
            $year = '';
            $text = $line;
        }
        $historyEvents[] = ['year' => $year, 'text' => $text];
    }
}

if (empty($historyEvents)) {
    $historyEvents = [
        ['year' => "1920'ler", 'text' => 'Joseph Pilates kontorloji yaklaşımını geliştirerek modern pilatesin temelini attı.'],
        ['year' => "1990'lar", 'text' => 'Pilates metodu fizik tedavi ve hareket alanlarında popülerleşerek dünya çapında yayıldı.'],
        ['year' => 'Günümüz', 'text' => 'Pi Studio Pilates, kontroloji prensiplerini bilimsel yaklaşım ve kişiye özel programlarla buluşturuyor.'],
    ];
}

function dayLabel(int $dayOrder): string
{
    $days = [1 => 'Pazartesi', 2 => 'Salı', 3 => 'Çarşamba', 4 => 'Perşembe', 5 => 'Cuma', 6 => 'Cumartesi', 7 => 'Pazar'];
    return $days[$dayOrder] ?? '';
}

function equipmentImage(array $equipment): string
{
    $title = trim($equipment['title'] ?? '');
    $topic = $title !== '' ? 'pilates equipment ' . $title : 'pilates equipment';
    return media_url($equipment['img'] ?? null, null, $topic);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($siteLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hero['title'] ?? $siteName); ?></title>
    <meta name="description" content="<?= htmlspecialchars($tagline); ?>">
    <meta property="og:title" content="<?= htmlspecialchars($hero['title'] ?? $siteName); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($tagline); ?>">
    <meta property="og:image" content="<?= htmlspecialchars(media_url($hero['background_media'] ?? null, 'assets/img/hero-bg.jpg', 'pilates studio interior')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>css/style.css">
</head>
<body id="top">
<nav class="navbar navbar-expand-lg fixed-top navbar-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#top">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl); ?>" alt="<?= htmlspecialchars($siteName); ?>">
            <?php endif; ?>
            <span><?= htmlspecialchars($siteName); ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Menüyü Aç">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="primaryNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="#about">Pilates Nedir?</a></li>
                <li class="nav-item"><a class="nav-link" href="#equipments">Ekipmanlar</a></li>
                <li class="nav-item"><a class="nav-link" href="#history">Tarihçe</a></li>
                <li class="nav-item"><a class="nav-link" href="#instructor">Pınar Sarı Koçak</a></li>
                <li class="nav-item"><a class="nav-link" href="#trainings">Eğitimler</a></li>
                <li class="nav-item"><a class="nav-link" href="#plans">Dersler & Üyelik</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">S.S.S.</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">İletişim</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero" style="--hero-image: url('<?= htmlspecialchars($heroBackground); ?>');">
    <div class="container text-center text-white">
        <div class="hero-content mx-auto">
            <span class="hero-kicker">Pi Studio Pilates</span>
            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($hero['title'] ?? $siteName); ?></h1>
            <p class="lead mx-auto mb-4 col-lg-7"><?= nl2br(htmlspecialchars($tagline)); ?></p>
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
                <a class="btn btn-primary btn-lg px-4" href="<?= htmlspecialchars($hero['cta_primary_link'] ?? '#contact'); ?>"><?= htmlspecialchars($hero['cta_primary_text'] ?? 'Deneme Dersi Al'); ?></a>
                <a class="btn btn-outline-light btn-lg px-4" href="<?= htmlspecialchars($hero['cta_secondary_link'] ?? '#schedule'); ?>"><?= htmlspecialchars($hero['cta_secondary_text'] ?? 'Ders Programı'); ?></a>
            </div>
        </div>
    </div>
</header>

<main>
    <section id="about" class="section-spacer bg-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10">
                    <p class="section-title">Pilates Nedir?</p>
                    <h2 class="section-heading mb-3">Nefes, Kontrol ve Core Gücü</h2>
                    <p class="section-lead"><?= nl2br(htmlspecialchars($pilates['content'] ?? 'Pilates; nefes, kontrol ve core gücünü temel alan kapsamlı bir zihin-beden pratiğidir. Doğru nefes ve hizalanma ile bedeninizi yeniden keşfedin.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section id="equipments" class="section-spacer bg-light">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col-lg-7">
                    <p class="section-title">Ekipmanlar</p>
                    <h2 class="section-heading mb-3">Stüdyomuzdaki Profesyonel Pilates Ekipmanları</h2>
                </div>
                <div class="col-lg-5 text-lg-end text-muted">
                    <p class="section-lead mb-0">Her öğrencimiz için özel tasarlanan programlarla Mat, Reformer, Tower, Cadillac, Chair ve Barrel ekipmanları kullanıyoruz.</p>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php foreach ($equipments as $equipment): ?>
                    <div class="col">
                        <article class="equipment-card h-100">
                            <div class="equipment-thumb">
                                <img src="<?= htmlspecialchars(equipmentImage($equipment)); ?>" alt="<?= htmlspecialchars($equipment['title']); ?>">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h3 class="h5 mb-2"><?= htmlspecialchars($equipment['title']); ?></h3>
                                <p class="flex-grow-1 mb-3"><?= nl2br(htmlspecialchars($equipment['description'])); ?></p>
                                <?php if (!empty($equipment['more_info'])): ?>
                                    <button class="btn btn-outline-primary mt-auto" data-bs-toggle="modal" data-bs-target="#equipmentModal<?= (int)$equipment['id']; ?>">Daha Fazla</button>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="history" class="section-spacer history-section">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-5">
                    <div class="history-figure">
                        <img src="<?= htmlspecialchars($historyImage); ?>" alt="Joseph Pilates">
                    </div>
                </div>
                <div class="col-lg-7">
                    <p class="section-title">Kısaca Tarihçe</p>
                    <h2 class="section-heading mb-4">Kontrolojiden Modern Pilates&#39;e</h2>
                    <div class="history-timeline">
                        <?php foreach ($historyEvents as $event): ?>
                            <article class="history-step">
                                <div class="history-marker"></div>
                                <div class="history-content">
                                    <?php if (!empty($event['year'])): ?>
                                        <span class="history-year"><?= htmlspecialchars($event['year']); ?></span>
                                    <?php endif; ?>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($event['text'])); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="instructor" class="section-spacer bg-light">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7 order-lg-1 order-2">
                    <p class="section-title">Eğitmen</p>
                    <h2 class="section-heading mb-3">Pınar Sarı Koçak Kimdir?</h2>
                    <p class="section-lead mb-4"><?= nl2br(htmlspecialchars($instructor['bio'] ?? 'Pınar Sarı Koçak, hareket anatomi bilgisi ve kişiye özel pilates yaklaşımıyla bedeninizi yeniden keşfetmenizi sağlar.')); ?></p>
                    <?php if (!empty($instructor['highlights'])): ?>
                        <ul class="instructor-highlights list-unstyled row row-cols-1 row-cols-sm-2 g-3 mb-0">
                            <?php foreach (explode("\n", $instructor['highlights']) as $highlight): ?>
                                <?php $trimmed = trim($highlight); ?>
                                <?php if ($trimmed === '') { continue; } ?>
                                <li class="col">
                                    <div class="instructor-highlight d-flex align-items-start gap-2">
                                        <span class="instructor-highlight-icon" aria-hidden="true">&#9679;</span>
                                        <span><?= htmlspecialchars($trimmed); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-lg-5 order-lg-2 order-1">
                    <div class="instructor-photo-wrapper">
                        <img src="<?= htmlspecialchars($instructorPhoto); ?>" class="instructor-photo" alt="Pınar Sarı Koçak">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="trainings" class="section-spacer bg-white">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <p class="section-title">Eğitimler</p>
                    <h2 class="section-heading mb-3">Sertifikalar ve Uzmanlıklar</h2>
                    <p class="section-lead">Pınar Sarı Koçak&#39;ın tamamladığı ve devam eden eğitimler, öğrencilerimizin güvenli ve etkili bir deneyim yaşamasını sağlar.</p>
                </div>
            </div>
            <div class="accordion trainings-accordion" id="trainingsAccordion">
                <?php foreach ($trainings as $index => $training): ?>
                    <?php $collapseId = 'training-' . (int)$training['id']; ?>
                    <div class="accordion-item mb-3 shadow-sm">
                        <h3 class="accordion-header" id="heading-<?= $collapseId; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $collapseId; ?>" aria-expanded="false" aria-controls="collapse-<?= $collapseId; ?>">
                                <span class="me-3 badge rounded-pill bg-primary-subtle text-primary fw-semibold"><?= htmlspecialchars($training['year'] ?: 'Devam'); ?></span>
                                <span><?= htmlspecialchars($training['title']); ?></span>
                            </button>
                        </h3>
                        <div id="collapse-<?= $collapseId; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $collapseId; ?>" data-bs-parent="#trainingsAccordion">
                            <div class="accordion-body">
                                <?= nl2br(htmlspecialchars($training['description'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="plans" class="section-spacer bg-light">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <p class="section-title">Dersler &amp; Üyelik</p>
                    <h2 class="section-heading mb-3">Size Uygun Pilates Paketi</h2>
                    <p class="section-lead">Deneme dersinden kişiye özel programlara kadar esnek paket seçenekleri ile hedeflerinize odaklanın.</p>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                <?php foreach ($plans as $plan): ?>
                    <div class="col">
                        <div class="plan-card h-100">
                            <h3 class="h5 mb-2"><?= htmlspecialchars($plan['title']); ?></h3>
                            <p class="price mb-3"><?= htmlspecialchars($plan['price']); ?></p>
                            <p class="flex-grow-1 mb-4"><?= nl2br(htmlspecialchars($plan['description'])); ?></p>
                            <a class="btn btn-outline-primary w-100" href="#contact">Bilgi Al</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="schedule" class="schedule-table mt-5">
                <div class="card p-4">
                    <h3 class="h4 mb-4 text-center">Haftalık Ders Programı</h3>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Gün</th>
                                    <th scope="col">Saat</th>
                                    <th scope="col">Ders</th>
                                    <th scope="col">Seviye</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule as $entry): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(dayLabel((int)$entry['day_order'])); ?></td>
                                        <td><?= htmlspecialchars(format_time($entry['start_time'])); ?></td>
                                        <td><?= htmlspecialchars($entry['class_type']); ?></td>
                                        <td><?= htmlspecialchars($entry['level']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="section-spacer bg-white">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <p class="section-title">S.S.S.</p>
                    <h2 class="section-heading mb-3">Sık Sorulan Sorular</h2>
                    <p class="section-lead">Ders öncesi aklınıza takılan konular için hızlı cevaplar.</p>
                </div>
            </div>
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faq as $index => $item): ?>
                    <?php $collapseId = 'faq-' . (int)$item['id']; ?>
                    <div class="accordion-item mb-3 shadow-sm">
                        <h3 class="accordion-header" id="heading-<?= $collapseId; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $collapseId; ?>" aria-expanded="false" aria-controls="collapse-<?= $collapseId; ?>">
                                <?= htmlspecialchars($item['question']); ?>
                            </button>
                        </h3>
                        <div id="collapse-<?= $collapseId; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $collapseId; ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?= nl2br(htmlspecialchars($item['answer'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="contact" class="section-spacer bg-light">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-7 order-lg-1 order-2">
                    <div class="contact-card">
                        <h3 class="h4 mb-4">Deneme Dersi Talep Formu</h3>
                        <?php if ($notice = getFlash('contact_form')): ?>
                            <?php $noticeType = is_array($notice) ? ($notice['type'] ?? 'success') : 'success'; ?>
                            <?php $noticeMessage = is_array($notice) ? ($notice['message'] ?? '') : $notice; ?>
                            <?php if ($noticeMessage): ?>
                                <div class="alert <?= $noticeType === 'error' ? 'error' : 'success'; ?>"><?= htmlspecialchars($noticeMessage); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <form action="process_contact.php" method="post" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" id="phone" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="preference" class="form-label">Ders Tercihi</label>
                                <input type="text" id="preference" name="preference" class="form-control" placeholder="Birebir, ikili, grup...">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Mesajınız</label>
                                <textarea id="message" name="message" rows="4" class="form-control" placeholder="Beklentilerinizi ve hedeflerinizi paylaşın..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 order-lg-2 order-1">
                    <p class="section-title">İletişim</p>
                    <h2 class="section-heading mb-3">Pi Studio Pilates&#39;e Ulaşın</h2>
                    <p class="section-lead mb-4">Deneme dersi talepleriniz, paket sorularınız ve stüdyomuza dair merak ettikleriniz için bize ulaşın.</p>
                    <div class="contact-details">
                        <p><strong>Telefon:</strong> <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $contactPhone)); ?>"><?= htmlspecialchars($contactPhone); ?></a></p>
                        <p><strong>E-posta:</strong> <a href="mailto:<?= htmlspecialchars($contactEmail); ?>"><?= htmlspecialchars($contactEmail); ?></a></p>
                        <p><strong>Adres:</strong> <?= htmlspecialchars($address); ?></p>
                    </div>
                    <div class="map-responsive mt-4">
                        <?php
                        $mapEmbed = $hero['map_embed'] ?? '';
                        if ($mapEmbed && stripos($mapEmbed, '<iframe') !== false) {
                            echo $mapEmbed;
                        } else {
                            $mapSrc = $mapEmbed ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.715183116564!2d28.97953!3d41.015137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDFsw4AwMCcwNi4xIk4gMjjCsDE5JzA2LjMiRQ!5e0!3m2!1str!2str!4v1700000000000';
                            ?>
                            <div class="ratio ratio-4x3">
                                <iframe src="<?= htmlspecialchars($mapSrc); ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="brand">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl); ?>" alt="<?= htmlspecialchars($siteName); ?>">
                    <?php endif; ?>
                    <p class="mb-2 fw-semibold text-white"><?= htmlspecialchars($siteName); ?></p>
                    <p><?= htmlspecialchars($tagline); ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5>Menü</h5>
                <ul class="list-unstyled d-grid gap-2">
                    <li><a href="#about">Pilates Nedir?</a></li>
                    <li><a href="#equipments">Ekipmanlar</a></li>
                    <li><a href="#plans">Üyelik</a></li>
                    <li><a href="#faq">S.S.S.</a></li>
                    <li><a href="#contact">İletişim</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5>Bağlantılar</h5>
                <div class="d-flex align-items-center gap-3 mb-3 social-links">
                    <a href="<?= htmlspecialchars($instagramUrl); ?>" aria-label="Instagram" target="_blank" rel="noopener">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="<?= htmlspecialchars($whatsappLink); ?>" aria-label="WhatsApp" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
                <ul class="list-unstyled d-grid gap-2">
                    <?php foreach ($footerLinks as $link): ?>
                        <li><a href="<?= htmlspecialchars($link['url']); ?>" target="<?= $link['target'] === '_blank' ? '_blank' : '_self'; ?>" rel="<?= $link['target'] === '_blank' ? 'noopener' : 'nofollow'; ?>"><?= htmlspecialchars($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="bottom-bar text-center mt-4">
            <small>&copy; <span data-current-year></span> <?= htmlspecialchars($siteName); ?>. Tüm hakları saklıdır.</small>
        </div>
    </div>
</footer>

<div class="floating-whatsapp">
    <a class="btn btn-success btn-lg d-flex align-items-center gap-2" href="<?= htmlspecialchars($whatsappLink); ?>" target="_blank" rel="noopener">
        <span>WhatsApp</span>
    </a>
</div>

<?php foreach ($equipments as $equipment): ?>
    <?php if (!empty($equipment['more_info'])): ?>
        <div class="modal fade" id="equipmentModal<?= (int)$equipment['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= htmlspecialchars($equipment['title']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <?= nl2br(htmlspecialchars($equipment['more_info'])); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= ASSET_URL ?>js/main.js"></script>
</body>
</html>
