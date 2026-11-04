<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/** Content is stored in SPIP, never read from the installation seed at runtime. */
function provenix_valeur($key) {
    include_spip('inc/config');
    return (string) lire_config('provenix_contenu/' . $key, '');
}

/** Plain-text fields cannot inject markup, scripts, or HTML attributes. */
function provenix_texte($key) {
    return htmlspecialchars(provenix_valeur($key), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function provenix_groupes() {
    return ['home' => 'Home', 'about' => 'About us', 'services' => 'Services', 'contact' => 'Contact', 'shared' => 'Shared content'];
}

function provenix_schema($group = null) {
    static $schema;
    if ($schema === null) {
        $schema = json_decode(file_get_contents(__DIR__ . '/content-schema.json'), true, 512, JSON_THROW_ON_ERROR);
    }
    return $group === null ? $schema : array_values(array_filter($schema, static function ($field) use ($group) {
        return $field['group'] === $group;
    }));
}

function provenix_champs($group) {
    $sections = [];
    foreach (provenix_schema($group) as $field) {
        $sections[$field['section']][] = $field;
    }
    return $sections;
}
