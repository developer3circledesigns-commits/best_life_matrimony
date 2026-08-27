<?php
require_once __DIR__ . '/includes/config.php';

// Advertising enquiry submission endpoint.
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
if (!$errors && !rate_limit_check('advertise', 5, 300)) {
  $errors['auth'] = 'Too many enquiries. Please wait a few minutes.';
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$company = trim($data['company'] ?? '');
$message = trim($data['message'] ?? '');

if (!$errors) {
  if ($name === '') $errors['name'] = 'Name is required.';
  if (strlen($name) > 150) $errors['name'] = 'Name must be 150 characters or fewer.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
  if (strlen($email) > 191) $errors['email'] = 'Email too long.';
  if (strlen($company) > 150) $errors['company'] = 'Company must be 150 characters or fewer.';
  if ($message === '') $errors['message'] = 'Message is required.';
  if (strlen($message) > 5000) $errors['message'] = 'Message must be 5000 characters or fewer.';
}

if ($errors) {
  if ($wantsJson) {
    header('Content-Type: application/json');
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $errors]);
  } else {
    session_start();
    $_SESSION['ad_errors'] = $errors;
    $_SESSION['ad_old'] = ['name' => $name, 'email' => $email, 'company' => $company, 'message' => $message];
    header('Location: ./advertise.php#enquire');
  }
  exit;
}

rate_limit_increment('advertise');

$subject = 'Advertising enquiry from BestLife Matrimony';
$htmlBody = '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>'
  . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
  . '<p><strong>Company / Brand:</strong> ' . htmlspecialchars($company) . '</p>'
  . '<hr><p>' . nl2br(htmlspecialchars($message)) . '</p>';
$textBody = "Name: $name\nEmail: $email\nCompany: $company\n\nMessage:\n$message";
$sent = send_email('info@bestlifematrimony.com', $subject, $htmlBody, $textBody, $email);
rate_limit_reset('advertise');

if ($wantsJson) {
  header('Content-Type: application/json');
  echo json_encode(['ok' => true, 'sent' => $sent, 'message' => 'Thank you, ' . $name . '! Your enquiry has been received. We\'ll get back to you shortly.']);
} else {
  session_start();
  $_SESSION['ad_success'] = ['name' => $name];
  header('Location: ./advertise.php#enquire');
}
