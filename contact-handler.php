<?php
declare(strict_types=1);

/**
 * Contact form handler for kontakt.html.
 * Requires a PHP-capable web host (standard on virtually all German shared
 * hosting, e.g. IONOS/1&1, Strato, all-inkl). Sends the enquiry by e-mail
 * to $recipient using PHP's built-in mail() function - no external
 * service or API key needed.
 */

$recipient = 'info@brandschutz-gosau.de';
$siteUrl   = 'https://www.brandschutz-gosau.de';

function redirect(string $url): void {
    header('Location: ' . $url, true, 303);
    exit;
}

function cleanField(string $value): string {
    // Strip line breaks so form input can never inject extra mail headers.
    $value = str_replace(["\r", "\n"], ' ', $value);
    return trim($value);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($siteUrl . '/kontakt.html');
}

// Honeypot: real visitors never fill this hidden field.
$honeypot = trim((string)($_POST['hp_website'] ?? ''));
if ($honeypot !== '') {
    redirect($siteUrl . '/danke.html');
}

$name       = cleanField((string)($_POST['name'] ?? ''));
$email      = cleanField((string)($_POST['email'] ?? ''));
$telefon    = cleanField((string)($_POST['telefon'] ?? ''));
$betreff    = cleanField((string)($_POST['betreff'] ?? ''));
$nachricht  = trim((string)($_POST['nachricht'] ?? ''));
$datenschutz = (string)($_POST['datenschutz'] ?? '');

$errors = [];
if ($name === '') { $errors[] = 'name'; }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'email'; }
if ($nachricht === '') { $errors[] = 'nachricht'; }
if ($datenschutz !== 'on') { $errors[] = 'datenschutz'; }

if (!empty($errors)) {
    redirect($siteUrl . '/kontakt.html?error=1#kontaktformular');
}

$subjectSuffix = $betreff !== '' ? ' – ' . $betreff : '';
$subject = '=?UTF-8?B?' . base64_encode('Neue Anfrage über die Website' . $subjectSuffix) . '?=';

$bodyLines = [
    'Neue Nachricht über das Kontaktformular auf ' . $siteUrl,
    '',
    'Name: ' . $name,
    'E-Mail: ' . $email,
    'Telefon: ' . ($telefon !== '' ? $telefon : '-'),
    'Anliegen: ' . ($betreff !== '' ? $betreff : '-'),
    '',
    'Nachricht:',
    $nachricht,
];
$body = implode("\r\n", $bodyLines);

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: Website Brandschutz Gosau <website@brandschutz-gosau.de>',
    'Reply-To: ' . $name . ' <' . $email . '>',
];

$sent = mail($recipient, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    redirect($siteUrl . '/danke.html');
}

redirect($siteUrl . '/kontakt.html?error=1#kontaktformular');
