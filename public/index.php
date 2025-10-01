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

function dayLabel(int $dayOrder): string
{
    $days = [1 => 'Pazartesi', 2 => 'Salı', 3 => 'Çarşamba', 4 => 'Perşembe', 5 => 'Cuma', 6 => 'Cumartesi', 7 => 'Pazar'];
    return $days[$dayOrder] ?? '';
}

?><!DOCTYPE html>
<html lang="<?= htmlspecialchars($siteLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hero['title'] ?? $siteName); ?></title>
    <meta name="description" content="<?= htmlspecialchars($tagline); ?>">
    <meta property="og:title" content="<?= htmlspecialchars($hero['title'] ?? $siteName); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($tagline); ?>">
    <meta property="og:image" content="<?= htmlspecialchars(media_url($hero['background_media'] ?? null)); ?>">
    <link rel="stylesheet" href="<?= ASSET_URL ?>css/styles.css">
</head>
<body>
<header class="hero" style="background-image: url('<?= htmlspecialchars(media_url($hero['background_media'] ?? null)); ?>');">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1><?= htmlspecialchars($hero['title'] ?? $siteName); ?></h1>
        <p><?= nl2br(htmlspecialchars($tagline)); ?></p>
        <div class="hero-buttons">
            <a class="btn primary" href="<?= htmlspecialchars($hero['cta_primary_link'] ?? '#contact'); ?>"><?= htmlspecialchars($hero['cta_primary_text'] ?? 'Deneme Dersi Al'); ?></a>
            <a class="btn secondary" href="<?= htmlspecialchars($hero['cta_secondary_link'] ?? '#schedule'); ?>"><?= htmlspecialchars($hero['cta_secondary_text'] ?? 'Ders Programı'); ?></a>
        </div>
    </div>
</header>

<nav class="main-nav">
    <div class="logo">
        <?php if ($logoUrl): ?>
            <img src="<?= htmlspecialchars($logoUrl); ?>" alt="<?= htmlspecialchars($siteName); ?>">
        <?php else: ?>
            <?= htmlspecialchars($siteName); ?>
        <?php endif; ?>
    </div>
    <ul>
        <li><a href="#about">Pilates Nedir?</a></li>
        <li><a href="#equipments">Ekipmanlar</a></li>
        <li><a href="#history">Tarihçe</a></li>
        <li><a href="#instructor">Pınar Sarı Koçak</a></li>
        <li><a href="#trainings">Eğitimler</a></li>
        <li><a href="#plans">Dersler & Üyelik</a></li>
        <li><a href="#faq">S.S.S.</a></li>
        <li><a href="#contact">İletişim</a></li>
    </ul>
</nav>

<main>
    <section id="about" class="section">
        <div class="section-header">
            <h2>Pilates Nedir?</h2>
            <p><?= nl2br(htmlspecialchars($pilates['content'] ?? 'Pilates; nefes, kontrol ve core gücünü temel alan kapsamlı bir zihin-beden pratiğidir.')); ?></p>
        </div>
    </section>

    <section id="equipments" class="section light">
        <h2>Ekipmanlar</h2>
        <div class="grid">
            <?php foreach ($equipments as $equipment): ?>
                <article class="card">
                    <?php if (!empty($equipment['img'])): ?>
                        <img src="<?= htmlspecialchars(media_url($equipment['img'])); ?>" alt="<?= htmlspecialchars($equipment['title']); ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($equipment['title']); ?></h3>
                    <p><?= nl2br(htmlspecialchars($equipment['description'])); ?></p>
                    <?php if (!empty($equipment['more_info'])): ?>
                        <button class="btn tertiary" data-modal="equipment-<?= (int)$equipment['id']; ?>">Daha Fazla</button>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="history" class="section">
        <div class="section-header">
            <h2>Kısaca Tarihçe</h2>
            <p><?= nl2br(htmlspecialchars($history['content'] ?? '20. yüzyılın başında Joseph Pilates tarafından geliştirilen "kontroloji", modern pilates pratiğinin temelini oluşturur.')); ?></p>
        </div>
    </section>

    <section id="instructor" class="section split">
        <div class="image-col">
            <?php if (!empty($instructor['photo'])): ?>
                <img src="<?= htmlspecialchars(media_url($instructor['photo'])); ?>" alt="Pınar Sarı Koçak">
            <?php endif; ?>
        </div>
        <div class="text-col">
            <h2>Pınar Sarı Koçak Kimdir?</h2>
            <p><?= nl2br(htmlspecialchars($instructor['bio'] ?? '')); ?></p>
            <?php if (!empty($instructor['highlights'])): ?>
                <ul>
                    <?php foreach (explode("\n", $instructor['highlights']) as $highlight): ?>
                        <?php if (trim($highlight) === '') continue; ?>
                        <li><?= htmlspecialchars($highlight); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <section id="trainings" class="section light">
        <h2>Eğitimler</h2>
        <div class="grid">
            <?php foreach ($trainings as $training): ?>
                <article class="card">
                    <?php if (!empty($training['img'])): ?>
                        <img src="<?= htmlspecialchars(media_url($training['img'])); ?>" alt="<?= htmlspecialchars($training['title']); ?>">
                    <?php endif; ?>
                    <div>
                        <h3><?= htmlspecialchars($training['title']); ?></h3>
                        <?php if (!empty($training['year'])): ?>
                            <span class="badge"><?= htmlspecialchars($training['year']); ?></span>
                        <?php endif; ?>
                        <p><?= nl2br(htmlspecialchars($training['description'])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="plans" class="section">
        <h2>Dersler & Üyelik</h2>
        <div class="plan-grid">
            <?php foreach ($plans as $plan): ?>
                <article class="plan-card">
                    <h3><?= htmlspecialchars($plan['title']); ?></h3>
                    <p class="price"><?= htmlspecialchars($plan['price']); ?></p>
                    <p><?= nl2br(htmlspecialchars($plan['description'])); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div id="schedule" class="schedule">
            <h3>Ders Programı</h3>
            <table>
                <thead>
                    <tr>
                        <th>Gün</th>
                        <th>Saat</th>
                        <th>Ders</th>
                        <th>Seviye</th>
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
    </section>

    <section id="faq" class="section light">
        <h2>Sık Sorulan Sorular</h2>
        <div class="accordion">
            <?php foreach ($faq as $item): ?>
                <details>
                    <summary><?= htmlspecialchars($item['question']); ?></summary>
                    <p><?= nl2br(htmlspecialchars($item['answer'])); ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="contact" class="section contact">
        <div class="contact-details">
            <h2>İletişim</h2>
            <p><strong>Telefon:</strong> <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $contactPhone)); ?>"><?= htmlspecialchars($contactPhone); ?></a></p>
            <p><strong>E-posta:</strong> <a href="mailto:<?= htmlspecialchars($contactEmail); ?>"><?= htmlspecialchars($contactEmail); ?></a></p>
            <p><strong>Adres:</strong> <?= htmlspecialchars($address); ?></p>
            <div class="map">
                <?php
                $mapEmbed = $hero['map_embed'] ?? '';
                if ($mapEmbed && stripos($mapEmbed, '<iframe') !== false) {
                    echo $mapEmbed;
                } else {
                    $mapSrc = $mapEmbed ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.715183116564!2d28.97953!3d41.015137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDFsw4AwMCcwNi4xIk4gMjjCsDE5JzA2LjMiRQ!5e0!3m2!1str!2str!4v1700000000000';
                    ?>
                    <iframe src="<?= htmlspecialchars($mapSrc); ?>" width="100%" height="300" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="contact-form">
            <h3>Deneme Dersi Talep Formu</h3>
            <?php if ($notice = getFlash('contact_form')): ?>
                <?php $noticeType = is_array($notice) ? ($notice['type'] ?? 'success') : 'success'; ?>
                <?php $noticeMessage = is_array($notice) ? ($notice['message'] ?? '') : $notice; ?>
                <?php if ($noticeMessage): ?>
                    <div class="alert <?= $noticeType === 'error' ? 'error' : 'success'; ?>"><?= htmlspecialchars($noticeMessage); ?></div>
                <?php endif; ?>
            <?php endif; ?>
            <form action="process_contact.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
                <label for="name">Ad Soyad</label>
                <input type="text" id="name" name="name" required>
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" name="phone">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" required>
                <label for="preference">Ders Tercihi</label>
                <input type="text" id="preference" name="preference">
                <label for="message">Mesajınız</label>
                <textarea id="message" name="message" rows="4"></textarea>
                <button type="submit" class="btn primary">Gönder</button>
            </form>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-col">
        <div class="logo">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl); ?>" alt="<?= htmlspecialchars($siteName); ?>">
            <?php else: ?>
                <?= htmlspecialchars($siteName); ?>
            <?php endif; ?>
        </div>
        <p><?= htmlspecialchars($tagline); ?></p>
    </div>
    <div class="footer-col">
        <nav>
            <ul>
                <li><a href="#about">Pilates Nedir?</a></li>
                <li><a href="#equipments">Ekipmanlar</a></li>
                <li><a href="#plans">Üyelik</a></li>
                <li><a href="#faq">S.S.S.</a></li>
                <li><a href="#contact">İletişim</a></li>
            </ul>
        </nav>
    </div>
    <div class="footer-col">
        <div class="socials">
            <a href="<?= htmlspecialchars($instagramUrl); ?>" aria-label="Instagram" target="_blank" rel="noopener">Instagram</a>
            <a href="<?= htmlspecialchars($whatsappLink); ?>" aria-label="WhatsApp" target="_blank" rel="noopener">WhatsApp</a>
        </div>
        <ul class="footer-links">
            <?php foreach ($footerLinks as $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']); ?>" target="<?= $link['target'] === '_blank' ? '_blank' : '_self'; ?>"><?= htmlspecialchars($link['label']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</footer>
<div class="whatsapp">
    <a href="<?= htmlspecialchars($whatsappLink); ?>" aria-label="WhatsApp" target="_blank" rel="noopener">WhatsApp</a>
</div>

<?php foreach ($equipments as $equipment): ?>
    <?php if (!empty($equipment['more_info'])): ?>
        <div id="equipment-<?= (int)$equipment['id']; ?>" class="modal">
            <div class="modal-content">
                <button class="modal-close" data-close>&times;</button>
                <h3><?= htmlspecialchars($equipment['title']); ?></h3>
                <p><?= nl2br(htmlspecialchars($equipment['more_info'])); ?></p>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script src="<?= ASSET_URL ?>js/main.js"></script>
</body>
</html>
