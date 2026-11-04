<?php
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}
include_spip("inc/autoriser");
include_spip("inc/config");
include_spip("provenix_site_fonctions");

function formulaires_editer_provenix_charger_dist($group = "home")
{
    if (!autoriser("configurer") || !isset(provenix_groupes()[$group])) {
        return false;
    }
    $values = ["group" => $group, "sections" => provenix_champs($group)];
    foreach (provenix_schema($group) as $field) {
        $values[$field["key"]] = provenix_valeur($field["key"]);
    }
    return $values;
}

function formulaires_editer_provenix_verifier_dist($group = "home")
{
    if (!autoriser("configurer") || !isset(provenix_groupes()[$group])) {
        return ["message_erreur" => "You are not authorized to edit this content."];
    }
    $errors = [];
    foreach (provenix_schema($group) as $field) {
        $value = _request($field["key"]);
        if (!is_string($value) || strlen($value) > 12000) {
            $errors[$field["key"]] = "Enter text shorter than 12,000 bytes.";
        } elseif ($field["type"] === "email" && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$field["key"]] = "Enter a valid email address.";
        }
    }
    if ($errors) {
        $errors["message_erreur"] = "Please correct the highlighted fields.";
    }
    return $errors;
}

function formulaires_editer_provenix_traiter_dist($group = "home")
{
    // Check again at the write boundary, including calls outside the form loader.
    if ($errors = formulaires_editer_provenix_verifier_dist($group)) {
        return ["message_erreur" => $errors["message_erreur"]];
    }
    $content = lire_config("provenix_contenu", []);
    foreach (provenix_schema($group) as $field) {
        $content[$field["key"]] = trim(_request($field["key"]));
    }
    ecrire_config("provenix_contenu", $content);
    include_spip("inc/invalideur");
    suivre_invalideur("1");
    return [
        "message_ok" => "Website content saved. Your changes are now published.",
        "editable" => true,
    ];
}
