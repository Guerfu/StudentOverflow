<?php
// public/contact.php (controller)

if (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
  require __DIR__ . '/PHPMailer/src/PHPMailer.php';
  require __DIR__ . '/PHPMailer/src/SMTP.php';
  require __DIR__ . '/PHPMailer/src/Exception.php';
} else {
  require dirname(__DIR__) . '/vendor/phpmailer/src/PHPMailer.php';
  require dirname(__DIR__) . '/vendor/phpmailer/src/SMTP.php';
  require dirname(__DIR__) . '/vendor/phpmailer/src/Exception.php';
}

// --- App includes ---
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/view.php';
require_once __DIR__ . '/../lib/media.php';

auth_require_login(); // must be logged in to send from their account email

// Load SMTP config if present (root or /config)
$mailCfg = null;
$root = dirname(__DIR__);
foreach ([$root . '/mail_config.php', $root . '/config/mail_config.php'] as $cfg) {
  if (is_file($cfg)) { require $cfg; break; }
}

// --- Admin recipient (fixed) ---
$ADMIN_EMAIL = 'hauntgcc240106@gmail.com';
$ADMIN_NAME  = 'Site Administrator';

// --- Helper---
$senderEmail = $_SESSION['email'] ?? null;
if (!$senderEmail && ($uid = auth_user_id())) {
  $q = $pdo->prepare("SELECT email FROM users WHERE id = :id");
  $q->execute([':id' => $uid]);
  $row = $q->fetch();
  if ($row && !empty($row['email'])) $senderEmail = $row['email'];
}

// --- Form model ---
$errors = [];
$okDb = false;
$okMail = false;

$name    = auth_username() ?? '';           // default to account username
$email   = $senderEmail ?? '';              // locked to account email
$subject = '';
$message = '';

// --- Handle POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // We ignore posted email and force the sender to account email
  $name    = trim($_POST['name'] ?? ($name ?: ''));
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '') $errors[] = 'Name is required.';
  if (!$senderEmail || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Your account email is missing or invalid.';
  if ($subject === '') $errors[] = 'Subject is required.';
  if ($message === '') $errors[] = 'Message is required.';

  if (!$errors) {
    // 1) Save to DB
    try {
      $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, subject, message)
        VALUES (:n, :e, :s, :m)
      ");
      $stmt->execute([':n'=>$name, ':e'=>$senderEmail, ':s'=>$subject, ':m'=>$message]);
      $okDb = true;
    } catch (Throwable $e) {
      $errors[] = 'Database error saving your message.';
    }

    // 2) Send email (only if SMTP config is available)
    if ($okDb && isset($mailCfg) && is_array($mailCfg)) {
      try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        // $mail->SMTPDebug = 2; $mail->Debugoutput = 'error_log'; // debug if needed

        $mail->isSMTP();
        $mail->Host       = $mailCfg['host']      ?? 'smtp.gmail.com';
        $mail->Port       = $mailCfg['port']      ?? 587;
        $mail->SMTPAuth   = $mailCfg['smtp_auth'] ?? true;
        $mail->Username   = $mailCfg['user']      ?? '';
        $mail->Password   = $mailCfg['pass']      ?? '';
        $mail->SMTPSecure = $mailCfg['secure']    ?? 'tls';

        $mail->setFrom($mailCfg['from_email'], $mailCfg['from_name']);   // authenticated address
        $mail->addAddress($mailCfg['to_email'], $mailCfg['to_name']);    // admin recipient
        $mail->addReplyTo($senderEmail, $name);                           // student's email from account

        if (!empty($mailCfg['from_email'])) {
          $mail->Sender = $mailCfg['from_email']; // some MTAs use this as envelope-from
        }

        $mail->Subject = '[Contact] ' . $subject;
        $mail->isHTML(false);
        $mail->Body = "From: $name <{$senderEmail}>\n\n" . $message;

        $okMail = $mail->send();
      } catch (Throwable $e) {
        // error_log('Mail send failed: ' . $e->getMessage());
        $okMail = false;
      }
    }

    if ($okDb) {
      // Clear form fields after success (name sticks to account username)
      $subject = $message = '';
    }
  }
}

// ---- Render view (no header/footer includes here; layout wraps it) ----
render('public/contact.html.php', [
  'okDb'        => $okDb,
  'okMail'      => $okMail,
  'errors'      => $errors,
  'name'        => $name,
  'senderEmail' => $senderEmail,
  'subject'     => $subject,
  'message'     => $message,
]);

