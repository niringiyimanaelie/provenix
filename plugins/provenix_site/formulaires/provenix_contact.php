<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
include_spip('inc/provenix_contact');

function formulaires_provenix_contact_charger_dist()
{
    return [
        'contact_name' => '',
        'contact_email' => '',
        'contact_company' => '',
        'contact_topic' => 'walkthrough',
        'contact_message' => '',
        'contact_website' => '',
        'contact_ticket' => provenix_contact_ticket(),
        'topics' => provenix_contact_topics(),
    ];
}

function formulaires_provenix_contact_verifier_dist()
{
    return provenix_contact_errors();
}

function formulaires_provenix_contact_traiter_dist()
{
    // Recheck at the send boundary, including calls outside the normal CVT flow.
    if ($errors = provenix_contact_errors()) {
        return ['message_erreur' => $errors['message_erreur'], 'editable' => true];
    }
    return provenix_contact_send(provenix_contact_values());
}
