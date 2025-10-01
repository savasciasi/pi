<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

validateCsrf($_POST['csrf_token'] ?? '');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$preference = trim($_POST['preference'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '') {
    setFlash('contact_form', [
        'type' => 'error',
        'message' => 'Lütfen gerekli alanları doldurun.',
    ]);
    header('Location: index.php#contact');
    exit;
}

executeQuery('INSERT INTO messages (name, email, phone, preference, message) VALUES (:name, :email, :phone, :preference, :message)', [
    ':name' => $name,
    ':email' => $email,
    ':phone' => $phone,
    ':preference' => $preference,
    ':message' => $message,
]);

send_contact_mail($name, $email, $phone, $preference, $message);

setFlash('contact_form', [
    'type' => 'success',
    'message' => 'Mesajınız alınmıştır. En kısa sürede size dönüş yapılacaktır.',
]);
header('Location: index.php#contact');
exit;
