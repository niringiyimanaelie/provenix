<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function provenix_site_upgrade($nom_meta_base_version, $version_cible) {
    include_spip('base/upgrade');
    include_spip('inc/provenix_articles');
    maj_plugin($nom_meta_base_version, $version_cible, [
        'create' => [['provenix_site_initialiser'], ['provenix_migrate_articles']],
        '1.1.0' => [['provenix_migrate_articles']],
    ]);
}

function provenix_site_initialiser() {
    include_spip('inc/config');
    $initial = json_decode(file_get_contents(__DIR__ . '/initial-content.json'), true, 512, JSON_THROW_ON_ERROR);
    // Existing editorial changes always take precedence on reinstall or migration.
    $existing = lire_config('provenix_contenu', []);
    ecrire_config('provenix_contenu', array_replace($initial, is_array($existing) ? $existing : []));
    include_spip('inc/invalideur');
    suivre_invalideur('1');
}

function provenix_site_vider_tables($nom_meta_base_version) {
    // Preserve editorial content if an administrator uninstalls the plugin.
    effacer_meta($nom_meta_base_version);
}
