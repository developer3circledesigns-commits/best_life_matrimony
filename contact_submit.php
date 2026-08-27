<?php
require_once __DIR__ . '/includes/config.php';

// Contact form submission endpoint.
// Accepts standard form POST (application/x-www-form-urlencoded) or JSON body.
$isJson = (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
if ($isJson) {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw ?: '', true) ?: [];
} else {
  $data = $_POST;
}

$wantsJson = $isJson || (($data['format'] ?? '') === 'json');

$errors = [];
if (!csrf_verify()) {
  $errors['auth'] = 'Invalid request. Please try again.';
}

if (!$errors && !rate_limit_check('contact', 5, 300)) {
  $errors['auth'] = 'Too many messages. Please wait a few minutes.';
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

if (!$errors) {
  if ($name === '') $errors['name'] = 'Name is required.';
  if (strlen($name) > 150) $errors['name'] = 'Name must be 150 characters or fewer.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
  if (strlen($email) > 191) $errors['email'] = 'Email too long.';
  if (strlen($phone) < 8 || strlen($phone) > 30) $errors['phone'] = 'Valid phone required.';
  if ($message === '') $errors['message'] = 'Message is required.';
  if (strlen($message) > 5000) $errors['message'] = 'Message must be 5000 characters or fewer.';
}

if ($errors) {
  if ($wantsJson) {
    header('Content-Type: application/json');
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $errors]);
  } else {
    // Return to contact page with preserved fields
    session_start();
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_old'] = ['name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $message];
    header('Location: ./contact.php');
  }
  exit;
}

rate_limit_increment('contact');

$to = 'info@bestlifematrimony.com';
$subject = 'New contact enquiry from BestLife Matrimony website';
$htmlBody = '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>'
  . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
  . '<p><strong>Phone:</strong> ' . htmlspecialchars($phone) . '</p>'
  . '<hr><p>' . nl2br(htmlspecialchars($message)) . '</p>';
$textBody = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
$sent = send_email($to, $subject, $htmlBody, $textBody, $email);
rate_limit_reset('contact');

if ($wantsJson) {
  header('Content-Type: application/json');
  echo json_encode(['ok' => true, 'sent' => $sent, 'message' => 'Thank you, ' . $name . '! ' . ($sent ? 'Your message has been sent. We\'ll get back to you soon.' : 'Your message has been received. We\'ll get back to you soon.')]);
} else {
  session_start();
  $_SESSION['contact_success'] = ['name' => $name, 'sent' => $sent];
  header('Location: ./contact.php#contact');
}
