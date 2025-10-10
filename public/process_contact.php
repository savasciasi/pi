<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/');
    exit;
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['contact_error'] = 'Invalid security token. Please try again.';
    redirect('/' . ($_POST['lang'] ?? 'tr') . '/contact');
    exit;
}

if (!checkRateLimit('contact_form', RATE_LIMIT_CONTACT, 900)) {
    $_SESSION['contact_error'] = $_POST['lang'] === 'en'
        ? 'Too many attempts. Please try again in 15 minutes.'
        : 'Çok fazla deneme. Lütfen 15 dakika sonra tekrar deneyin.';
    redirect('/' . ($_POST['lang'] ?? 'tr') . '/contact');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
$lang = $_POST['lang'] ?? 'tr';

if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['contact_error'] = $lang === 'en'
        ? 'Please fill in all required fields.'
        : 'Lütfen tüm gerekli alanları doldurun.';
    redirect('/' . $lang . '/contact');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = $lang === 'en'
        ? 'Please enter a valid email address.'
        : 'Lütfen geçerli bir e-posta adresi girin.';
    redirect('/' . $lang . '/contact');
    exit;
}

try {
    $db = getDb();
    $stmt = $db->prepare('INSERT INTO contact_messages (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $phone, $message]);

    if (SMTP_HOST && SMTP_USERNAME) {
        $to = getSetting('contact_email', 'info@pistudiopilates.com');
        $subject = $lang === 'en' ? 'New Contact Form Submission' : 'Yeni İletişim Formu Mesajı';
        $body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
        $headers = "From: " . SMTP_FROM_EMAIL . "\r\nReply-To: $email\r\n";

        @mail($to, $subject, $body, $headers);
    }

    $_SESSION['contact_success'] = $lang === 'en'
        ? 'Thank you for your message! We will get back to you soon.'
        : 'Mesajınız için teşekkürler! En kısa sürede size dönüş yapacağız.';
} catch (Exception $e) {
    $_SESSION['contact_error'] = $lang === 'en'
        ? 'An error occurred. Please try again later.'
        : 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';

    if (APP_DEBUG) {
        $_SESSION['contact_error'] .= ' ' . $e->getMessage();
    }
}

redirect('/' . $lang . '/contact');
exit;
