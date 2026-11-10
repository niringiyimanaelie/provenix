<?php
/** Local-only integration checks; mail must be captured by loopback Mailpit. */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit();
}
chdir(dirname(__DIR__));
if (ini_get('SMTP') !== 'localhost' || (int) ini_get('smtp_port') !== 25 || ini_get('sendmail_path') !== ''
    || !@file_get_contents('http://127.0.0.1:8025/api/v1/messages')) {
    throw new RuntimeException('Start the local Mailpit capture before running these tests.');
}
require 'vendor/autoload.php';
require 'ecrire/inc_version.php';
include_spip('base/abstract_sql');
include_spip('inc/provenix_contact');
include_spip('formulaires/provenix_contact');
$GLOBALS['ip'] = '192.0.2.123';
$directory = sous_repertoire(_DIR_TMP, 'provenix-contact');
$file = $directory . hash_hmac('sha256', $GLOBALS['ip'], secret_du_site()) . '.json';
$before = file_exists($file) ? file_get_contents($file) : null;
include_spip('inc/config');
$original_config = lire_config('facteur');
if (($original_config['mailer'] ?? '') !== 'smtp' || ($original_config['smtp_host'] ?? '') !== '127.0.0.1' || (int) ($original_config['smtp_port'] ?? 0) !== 25) {
    throw new RuntimeException('Facteur must target local Mailpit only.');
}
function contact_check($condition, $label)
{
    if (!$condition) {
        throw new RuntimeException($label);
    }
    echo 'PASS ' . $label . "\n";
}
function contact_test_ticket($age = 5)
{
    $payload = (time() - $age) . ':' . bin2hex(random_bytes(16));
    return $payload . ':' . calculer_cle_action('provenix-contact:' . $payload);
}
function contact_test_request($overrides = [])
{
    $values = array_replace([
        'contact_name' => 'Local Test Visitor',
        'contact_email' => 'visitor@example.invalid',
        'contact_company' => 'Local Testing',
        'contact_topic' => 'general',
        'contact_message' => 'This is a local server-side form integration test.',
        'contact_website' => '',
        'contact_ticket' => contact_test_ticket(),
    ], $overrides);
    foreach ($values as $key => $value) {
        set_request($key, $value);
    }
}
try {
    contact_test_request();
    contact_check(!provenix_contact_errors(), 'Valid request accepted');
    $cases = [
        ['contact_name', '', 'Required name'],
        ['contact_name', ['unexpected'], 'Array input rejected'],
        ['contact_name', str_repeat("\u{00E9}", 101), 'Unicode character length enforced'],
        ['contact_name', "\xFF", 'Malformed UTF-8 rejected'],
        ['contact_email', "visitor@example.invalid\r\nBcc: victim@example.invalid", 'Header injection rejected'],
        ['contact_email', 'invalid', 'Invalid email rejected'],
        ['contact_company', str_repeat('x', 161), 'Company length enforced'],
        ['contact_message', 'short', 'Message minimum length enforced'],
        ['contact_message', str_repeat('x', 4001), 'Message maximum length enforced'],
        ['contact_topic', 'arbitrary-subject', 'Only configured topics accepted'],
        ['contact_website', 'spam', 'Honeypot rejected'],
        ['contact_ticket', 'tampered', 'Tampered ticket rejected'],
        ['contact_ticket', contact_test_ticket(7201), 'Expired ticket rejected'],
        ['contact_ticket', contact_test_ticket(-10), 'Future ticket rejected'],
    ];
    foreach ($cases as [$field, $value, $label]) {
        contact_test_request([$field => $value]);
        contact_check((bool) provenix_contact_errors(), $label);
    }
    file_put_contents($file, json_encode(['attempts' => [], 'sent' => []]));
    contact_test_request();
    ecrire_config('facteur/smtp_port', 1);
    $failed = formulaires_provenix_contact_traiter_dist();
    contact_check(isset($failed['message_erreur']) && $failed['editable'], 'Transport failure returns error and keeps form editable');
    contact_check(empty(json_decode(file_get_contents($file), true)['sent']), 'Failed delivery is not recorded as sent');
    ecrire_config('facteur', $original_config);
    $accepted = formulaires_provenix_contact_traiter_dist();
    contact_check(isset($accepted['message_ok']) && !$accepted['editable'], 'Retry succeeds through the native SPIP mail transport');
    $duplicate = formulaires_provenix_contact_traiter_dist();
    contact_check(($duplicate['message_ok'] ?? '') === provenix_texte('contact_duplicate'), 'Repeated POST is not sent twice');
    contact_test_request();
    $duplicate = formulaires_provenix_contact_traiter_dist();
    contact_check(($duplicate['message_ok'] ?? '') === provenix_texte('contact_duplicate'), 'Identical content with a fresh ticket is not sent twice');
    file_put_contents($file, json_encode(['attempts' => array_fill(0, 5, time()), 'sent' => []]));
    contact_test_request(['contact_message' => 'Another message used only to check the rate limit.']);
    $limited = formulaires_provenix_contact_traiter_dist();
    contact_check(($limited['message_erreur'] ?? '') === provenix_texte('contact_rate_limit'), 'Server-side request rate limit enforced');
} finally {
    ecrire_config('facteur', $original_config);
    if ($before === null) {
        @unlink($file);
    } else {
        file_put_contents($file, $before);
    }
}
