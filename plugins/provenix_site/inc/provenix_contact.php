<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
include_spip('inc/config');
include_spip('inc/securiser_action');
include_spip('provenix_site_fonctions');

/** A short-lived, signed ticket; no visitor details are stored in it. */
function provenix_contact_ticket()
{
    $payload = time() . ':' . bin2hex(random_bytes(16));
    return $payload . ':' . calculer_cle_action('provenix-contact:' . $payload);
}

function provenix_contact_topics()
{
    return [
        'walkthrough' => 'contact_024',
        'lineage' => 'contact_025',
        'partnership' => 'contact_026',
        'general' => 'contact_027',
    ];
}

function provenix_contact_values()
{
    $values = [];
    foreach (['name', 'email', 'company', 'topic', 'message'] as $field) {
        $value = _request('contact_' . $field);
        $values[$field] = is_string($value) ? trim($value) : '';
    }
    return $values;
}

/** Validate independently of HTML attributes and the native CVT signature. */
function provenix_contact_errors()
{
    $errors = [];
    $limits = ['name' => 100, 'email' => 200, 'company' => 160, 'topic' => 40, 'message' => 4000];
    $values = provenix_contact_values();
    foreach ($limits as $field => $limit) {
        $raw = _request('contact_' . $field);
        if ($field === 'company' && $raw === null) {
            $raw = '';
        }
        $key = 'contact_' . $field;
        if (!is_string($raw) || !mb_check_encoding($raw, 'UTF-8') || strpos($raw, "\0") !== false) {
            $errors[$key] = provenix_texte('contact_invalid');
        } elseif ($field !== 'company' && $values[$field] === '') {
            $errors[$key] = provenix_texte('contact_required');
        } elseif (mb_strlen($values[$field], 'UTF-8') > $limit) {
            $errors[$key] = provenix_texte('contact_too_long');
        } elseif ($field !== 'message' && preg_match('/[\r\n]/', $values[$field])) {
            $errors[$key] = provenix_texte('contact_invalid');
        }
    }
    if (!isset($errors['contact_email']) && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = provenix_texte('contact_invalid_email');
    }
    if (!isset($errors['contact_message']) && mb_strlen($values['message'], 'UTF-8') < 10) {
        $errors['contact_message'] = provenix_texte('contact_short_message');
    }
    if (!array_key_exists($values['topic'], provenix_contact_topics())) {
        $errors['contact_topic'] = provenix_texte('contact_invalid');
    }
    $trap = _request('contact_website');
    if (!is_string($trap) || $trap !== '') {
        $errors['message_erreur'] = provenix_texte('contact_spam');
    }
    $ticket = _request('contact_ticket');
    if (!is_string($ticket) || !preg_match('/^([0-9]{10}):([a-f0-9]{32}):([a-f0-9]{64})$/D', $ticket, $match)
        || !verifier_cle_action('provenix-contact:' . $match[1] . ':' . $match[2], $match[3])
        || time() - (int) $match[1] < 0 || time() - (int) $match[1] > 7200) {
        $errors['message_erreur'] = provenix_texte('contact_expired');
    }
    if ($errors && !isset($errors['message_erreur'])) {
        $errors['message_erreur'] = provenix_texte('contact_correct_errors');
    }
    return $errors;
}

/** Atomic, bounded anti-abuse state; contains hashes/times, never message text. */
function provenix_contact_send($values)
{
    $directory = sous_repertoire(_DIR_TMP, 'provenix-contact');
    $ip = $GLOBALS['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
    $key = hash_hmac('sha256', (string) $ip, secret_du_site());
    $file = $directory . $key . '.json';
    $handle = @fopen($file, 'c+');
    if (!$handle) {
        return ['message_erreur' => provenix_texte('contact_failure'), 'editable' => true];
    }
    try {
        if (!flock($handle, LOCK_EX)) {
            return ['message_erreur' => provenix_texte('contact_failure'), 'editable' => true];
        }
        $state = json_decode(stream_get_contents($handle), true) ?: [];
        $now = time();
        $attempts = array_values(array_filter($state['attempts'] ?? [], function ($time) use ($now) {
            return $time > $now - 600;
        }));
        $sent = array_filter($state['sent'] ?? [], function ($time) use ($now) {
            return $time > $now - 7200;
        });
        $ticket = hash('sha256', _request('contact_ticket'));
        $fingerprint = hash_hmac('sha256', json_encode($values), secret_du_site());
        if (isset($sent[$ticket]) || isset($sent[$fingerprint])) {
            return ['message_ok' => provenix_texte('contact_duplicate'), 'editable' => false];
        }
        if (count($attempts) >= 5) {
            return ['message_erreur' => provenix_texte('contact_rate_limit'), 'editable' => true];
        }
        $attempts[] = $now;
        if (!provenix_contact_store_state($handle, $attempts, $sent)) {
            return ['message_erreur' => provenix_texte('contact_failure'), 'editable' => true];
        }
        $recipient = trim(provenix_valeur('contact_email'));
        $sender = trim((string) lire_config('email_envoi', ''));
        if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $sender = $recipient;
        }
        $accepted = false;
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) && filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $send = charger_fonction('envoyer_mail', 'inc');
            $topic = provenix_valeur(provenix_contact_topics()[$values['topic']]);
            $subject = nettoyer_titre_email(provenix_valeur('email_subject') . ' ' . $topic);
            $sender_name = nettoyer_titre_email((string) lire_config('nom_site', ''));
            $body = provenix_valeur('email_name') . ' ' . $values['name'] . "\n"
                . provenix_valeur('email_address') . ' ' . $values['email'] . "\n"
                . provenix_valeur('email_company') . ' '
                . ($values['company'] ?: provenix_valeur('email_empty_company')) . "\n\n"
                . $values['message'];
            try {
                $accepted = $send($recipient, $subject, [
                    'texte' => $body,
                    'from' => $sender,
                    'nom_envoyeur' => $sender_name,
                    'repondre_a' => $values['email'],
                    'nom_repondre_a' => $values['name'],
                ]);
            } catch (Throwable $error) {
                spip_log('Contact mail transport failed.', 'provenix_contact');
            }
        }
        if ($accepted) {
            $sent[$ticket] = $now;
            $sent[$fingerprint] = $now;
        }
        if (!provenix_contact_store_state($handle, $attempts, $sent)) {
            spip_log('Contact anti-abuse state could not be saved.', 'provenix_contact');
        }
        return $accepted
            ? ['message_ok' => provenix_texte('contact_success'), 'editable' => false]
            : ['message_erreur' => provenix_texte('contact_failure'), 'editable' => true];
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function provenix_contact_store_state($handle, $attempts, $sent)
{
    $json = json_encode(['attempts' => $attempts, 'sent' => $sent]);
    return rewind($handle) && ftruncate($handle, 0)
        && fwrite($handle, $json) === strlen($json) && fflush($handle);
}
