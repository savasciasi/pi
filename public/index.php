<?php
require_once __DIR__ . '/../config/config.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?');
$requestUri = rtrim($requestUri, '/');

if (empty($requestUri)) {
    $requestUri = '/';
}

$lang = getCurrentLang();
$slug = 'home';

if (preg_match('#^/(tr|en)(/(.+))?$#', $requestUri, $matches)) {
    $lang = $matches[1];
    setCurrentLang($lang);
    $slug = $matches[3] ?? 'home';
} elseif (preg_match('#^/(.+)$#', $requestUri, $matches)) {
    $slug = $matches[1];
}

$page = getPage($slug, $lang);

if (!$page) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404</title></head><body><h1>404 - Page Not Found</h1></body></html>';
    exit;
}

$siteName = getSetting('site_name', 'Pi Studio Pilates');
$whatsappNumber = getSetting('whatsapp_number', '+05417672104');
$whatsappMessage = getSetting('whatsapp_message', 'Merhaba, randevu almak istiyorum.');
$instagramStudio = getSetting('instagram_studio', 'https://www.instagram.com/pi_studyo_pilatess');
$instagramInstructor = getSetting('instagram_instructor', 'https://www.instagram.com/uverrcinnkaa');
$contactEmail = getSetting('contact_email', 'info@pistudiopilates.com');
$address = getSetting('address', 'Ankara, Türkiye');
$workingHours = getSetting('working_hours', 'Pazartesi - Cumartesi: 09:00 - 20:00<br>Pazar: Kapalı');
$videoPlaylist = json_decode(getSetting('video_playlist', '[]'), true);
if (!is_array($videoPlaylist)) {
    $videoPlaylist = ['assets/video/bg.mp4', 'assets/video/bg1.mp4', 'assets/video/bg2.mp4', 'assets/video/bg3.mp4'];
}

$whatsappLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsappNumber) . '?text=' . urlencode($whatsappMessage);

$navItems = [];
if ($lang === 'tr') {
    $navItems = [
        ['label' => 'Anasayfa', 'slug' => 'home'],
        ['label' => 'Eğitmen', 'slug' => 'egitmen'],
        ['label' => 'İletişim', 'slug' => 'contact']
    ];
} else {
    $navItems = [
        ['label' => 'Home', 'slug' => 'home'],
        ['label' => 'Instructor', 'slug' => 'instructor'],
        ['label' => 'Contact', 'slug' => 'contact']
    ];
}

$otherLang = $lang === 'tr' ? 'en' : 'tr';
$otherLangSlug = $slug;
if ($slug === 'egitmen' && $lang === 'tr') {
    $otherLangSlug = 'instructor';
} elseif ($slug === 'instructor' && $lang === 'en') {
    $otherLangSlug = 'egitmen';
}
$langSwitchUrl = '/' . $otherLang . '/' . $otherLangSlug;

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="<?php echo escape($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($page['meta_title'] ?: $page['title']); ?></title>
    <meta name="description" content="<?php echo escape($page['meta_description'] ?: ''); ?>">

    <meta property="og:title" content="<?php echo escape($page['meta_title'] ?: $page['title']); ?>">
    <meta property="og:description" content="<?php echo escape($page['meta_description'] ?: ''); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo escape(APP_URL . '/' . $lang . '/' . $slug); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo escape($page['meta_title'] ?: $page['title']); ?>">
    <meta name="twitter:description" content="<?php echo escape($page['meta_description'] ?: ''); ?>">

    <link rel="alternate" hreflang="tr" href="<?php echo escape(APP_URL . '/tr/' . ($slug === 'instructor' ? 'egitmen' : $slug)); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo escape(APP_URL . '/en/' . ($slug === 'egitmen' ? 'instructor' : $slug)); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-container">
        <a href="/<?php echo $lang; ?>/home" class="navbar-brand">
            <?php echo escape($siteName); ?>
        </a>

        <button class="navbar-toggler" id="navToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="navbar-menu" id="navMenu">
            <ul class="navbar-nav">
                <?php foreach ($navItems as $item): ?>
                <li class="nav-item">
                    <a href="/<?php echo $lang; ?>/<?php echo $item['slug']; ?>" class="nav-link<?php echo $slug === $item['slug'] ? ' active' : ''; ?>">
                        <?php echo escape($item['label']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="navbar-actions">
                <a href="<?php echo escape($langSwitchUrl); ?>" class="lang-switch">
                    <?php echo $lang === 'tr' ? 'EN' : 'TR'; ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php if ($slug === 'home'): ?>
<section class="hero-section">
    <div class="video-background">
        <video id="heroVideo" autoplay muted playsinline></video>
    </div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <?php echo $page['content']; ?>
        <div class="hero-buttons">
            <a href="<?php echo escape($whatsappLink); ?>" class="btn btn-primary" target="_blank" rel="noopener">
                <?php echo $lang === 'tr' ? 'Randevu Al' : 'Book Appointment'; ?>
            </a>
            <a href="/<?php echo $lang; ?>/<?php echo $lang === 'tr' ? 'egitmen' : 'instructor'; ?>" class="btn btn-secondary">
                <?php echo $lang === 'tr' ? 'Eğitmen' : 'Instructor'; ?>
            </a>
        </div>
    </div>
</section>
<?php elseif ($slug === 'egitmen' || $slug === 'instructor'): ?>
<section class="page-section instructor-page">
    <div class="container">
        <div class="instructor-content">
            <?php echo $page['content']; ?>
        </div>
        <div class="social-links">
            <a href="<?php echo escape($instagramInstructor); ?>" target="_blank" rel="noopener" class="social-link">
                Instagram
            </a>
        </div>
    </div>
</section>
<?php elseif ($slug === 'contact'): ?>
<section class="page-section contact-page">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-form-wrapper">
                <?php echo $page['content']; ?>

                <?php if (isset($_SESSION['contact_success'])): ?>
                    <div class="alert alert-success">
                        <?php echo escape($_SESSION['contact_success']); ?>
                        <?php unset($_SESSION['contact_success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['contact_error'])): ?>
                    <div class="alert alert-error">
                        <?php echo escape($_SESSION['contact_error']); ?>
                        <?php unset($_SESSION['contact_error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/public/process_contact.php" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">

                    <div class="form-group">
                        <label for="name"><?php echo $lang === 'tr' ? 'Ad Soyad' : 'Full Name'; ?></label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><?php echo $lang === 'tr' ? 'E-posta' : 'Email'; ?></label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><?php echo $lang === 'tr' ? 'Telefon' : 'Phone'; ?></label>
                        <input type="tel" id="phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="message"><?php echo $lang === 'tr' ? 'Mesaj' : 'Message'; ?></label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?php echo $lang === 'tr' ? 'Gönder' : 'Send'; ?>
                    </button>
                </form>
            </div>

            <div class="contact-info">
                <h3><?php echo $lang === 'tr' ? 'İletişim Bilgileri' : 'Contact Information'; ?></h3>

                <div class="contact-item">
                    <strong><?php echo $lang === 'tr' ? 'WhatsApp' : 'WhatsApp'; ?>:</strong>
                    <a href="<?php echo escape($whatsappLink); ?>" target="_blank" rel="noopener">
                        <?php echo escape($whatsappNumber); ?>
                    </a>
                </div>

                <div class="contact-item">
                    <strong><?php echo $lang === 'tr' ? 'E-posta' : 'Email'; ?>:</strong>
                    <a href="mailto:<?php echo escape($contactEmail); ?>">
                        <?php echo escape($contactEmail); ?>
                    </a>
                </div>

                <div class="contact-item">
                    <strong><?php echo $lang === 'tr' ? 'Adres' : 'Address'; ?>:</strong>
                    <p><?php echo escape($address); ?></p>
                </div>

                <div class="contact-item">
                    <strong><?php echo $lang === 'tr' ? 'Çalışma Saatleri' : 'Working Hours'; ?>:</strong>
                    <p><?php echo $workingHours; ?></p>
                </div>

                <div class="social-links">
                    <a href="<?php echo escape($instagramStudio); ?>" target="_blank" rel="noopener" class="social-link">
                        Instagram (Studio)
                    </a>
                    <a href="<?php echo escape($instagramInstructor); ?>" target="_blank" rel="noopener" class="social-link">
                        Instagram (<?php echo $lang === 'tr' ? 'Eğitmen' : 'Instructor'; ?>)
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="page-section">
    <div class="container">
        <div class="page-content">
            <?php echo $page['content']; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4><?php echo escape($siteName); ?></h4>
                <p><?php echo $lang === 'tr' ? 'Profesyonel Pilates Eğitimi' : 'Professional Pilates Training'; ?></p>
            </div>

            <div class="footer-section">
                <h4><?php echo $lang === 'tr' ? 'İletişim' : 'Contact'; ?></h4>
                <p>
                    <a href="<?php echo escape($whatsappLink); ?>" target="_blank" rel="noopener">
                        <?php echo escape($whatsappNumber); ?>
                    </a>
                </p>
                <p>
                    <a href="mailto:<?php echo escape($contactEmail); ?>">
                        <?php echo escape($contactEmail); ?>
                    </a>
                </p>
            </div>

            <div class="footer-section">
                <h4><?php echo $lang === 'tr' ? 'Sosyal Medya' : 'Social Media'; ?></h4>
                <div class="social-links">
                    <a href="<?php echo escape($instagramStudio); ?>" target="_blank" rel="noopener">Instagram (Studio)</a>
                    <a href="<?php echo escape($instagramInstructor); ?>" target="_blank" rel="noopener">Instagram (<?php echo $lang === 'tr' ? 'Eğitmen' : 'Instructor'; ?>)</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo escape($siteName); ?>. <?php echo $lang === 'tr' ? 'Tüm hakları saklıdır.' : 'All rights reserved.'; ?></p>
        </div>
    </div>
</footer>

<a href="<?php echo escape($whatsappLink); ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C6.48 2 2 6.48 2 12C2 13.87 2.57 15.64 3.56 17.13L2.05 21.95L7.05 20.47C8.48 21.36 10.18 21.88 12 21.88C17.52 21.88 22 17.4 22 11.88C22 6.36 17.52 1.88 12 1.88M12.05 3.88C16.42 3.88 20 7.46 20 11.83C20 16.2 16.42 19.78 12.05 19.78C10.53 19.78 9.11 19.36 7.88 18.62L7.55 18.43L4.43 19.26L5.28 16.22L5.06 15.87C4.24 14.61 3.78 13.13 3.78 11.58C3.78 7.21 7.36 3.63 11.73 3.63M8.5 7.5C8.32 7.5 8.03 7.57 7.8 7.85C7.57 8.13 6.9 8.75 6.9 9.98C6.9 11.21 7.82 12.4 7.95 12.58C8.08 12.76 9.72 15.36 12.23 16.43C14.4 17.34 14.74 17.2 15.11 17.17C15.48 17.14 16.5 16.56 16.72 15.96C16.94 15.36 16.94 14.84 16.88 14.73C16.82 14.62 16.64 14.56 16.37 14.43C16.1 14.3 14.88 13.68 14.63 13.59C14.38 13.5 14.2 13.46 14.02 13.73C13.84 14 13.35 14.58 13.2 14.76C13.05 14.94 12.9 14.96 12.63 14.83C12.36 14.7 11.49 14.43 10.45 13.5C9.64 12.77 9.09 11.88 8.94 11.61C8.79 11.34 8.92 11.2 9.05 11.07C9.17 10.95 9.32 10.75 9.45 10.6C9.58 10.45 9.62 10.34 9.71 10.16C9.8 9.98 9.76 9.83 9.69 9.7C9.62 9.57 9.09 8.34 8.86 7.81C8.63 7.28 8.4 7.35 8.25 7.34C8.1 7.33 7.92 7.33 7.74 7.33" fill="currentColor"/>
    </svg>
</a>

<script>
const videoPlaylist = <?php echo json_encode($videoPlaylist); ?>;
</script>
<script src="/assets/js/main.js"></script>
</body>
</html>
