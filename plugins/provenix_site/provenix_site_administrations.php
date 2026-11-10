<?php
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function provenix_site_upgrade($nom_meta_base_version, $version_cible)
{
    include_spip("base/upgrade");
    include_spip("inc/provenix_articles");
    maj_plugin($nom_meta_base_version, $version_cible, [
        "create" => [
            ["provenix_site_initialiser"],
            ["provenix_migrate_articles"],
            ["provenix_install_platform_logos"],
            ["provenix_install_lineage_demo"],
            ["provenix_clean_diagram_content"],
            ["provenix_install_contact_form"],
        ],
        "1.1.0" => [["provenix_migrate_articles"]],
        "1.2.0" => [
            ["provenix_prepare_platforms"],
            ["provenix_migrate_articles"],
            ["provenix_install_platform_logos"],
        ],
        "1.3.0" => [["provenix_install_lineage_demo"]],
        "1.4.0" => [["provenix_prepare_ontology"], ["provenix_migrate_articles"]],
        "1.4.1" => [["provenix_clean_diagram_content"]],
        "1.5.0" => [["provenix_install_contact_form"]],
    ]);
}

function provenix_site_initialiser()
{
    include_spip("inc/config");
    $initial = json_decode(
        file_get_contents(__DIR__ . "/initial-content.json"),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );
    // Existing editorial changes always take precedence on reinstall or migration.
    $existing = lire_config("provenix_contenu", []);
    ecrire_config(
        "provenix_contenu",
        array_replace($initial, is_array($existing) ? $existing : []),
    );
    include_spip("inc/invalideur");
    suivre_invalideur("1");
}

function provenix_site_vider_tables($nom_meta_base_version)
{
    // Preserve editorial content if an administrator uninstalls the plugin.
    effacer_meta($nom_meta_base_version);
}

/** Rename the former DuckDB slot once, before moving platform names to articles. */
function provenix_prepare_platforms()
{
    include_spip("inc/config");
    $content = lire_config("provenix_contenu", []);
    if (($content["sommaire_038"] ?? "") === "DuckDB") {
        $content["sommaire_038"] = "MotherDuck";
        ecrire_config("provenix_contenu", $content);
    }
}

/** Seed native article logos once; normal edits use SPIP's Article logo controls. */
function provenix_install_platform_logos()
{
    include_spip("inc/config");
    include_spip("action/editer_logo");
    include_spip("inc/autoriser");
    $logos = [
        "platform_snowflake" => "snowflake.svg",
        "platform_databricks" => "databricks.svg",
        "platform_motherduck" => "motherduck.png",
    ];
    $installed = lire_config("provenix_platform_logos_installed", []);
    $chercher_logo = charger_fonction("chercher_logo", "inc");
    foreach ($logos as $role => $file) {
        $id = (int) lire_config("provenix_articles/" . $role, 0);
        if (!$id) {
            throw new RuntimeException("Missing platform article: " . $role);
        }
        if (empty($installed[$role]) && !$chercher_logo($id, "id_article", "on")) {
            // SPIP consumes uploaded files; keep the distribution asset intact.
            $temp = tempnam(_DIR_TMP, "platform-");
            copy(__DIR__ . "/media/" . $file, $temp);
            autoriser_exception("joindredocument", "article", $id);
            try {
                $error = logo_modifier("article", $id, "on", [
                    "tmp_name" => $temp, "name" => $file, "error" => 0,
                ]);
                if ($error) {
                    throw new RuntimeException("Platform logo: " . $error);
                }
            } finally {
                autoriser_exception("joindredocument", "article", $id, false);
                if (file_exists($temp)) {
                    unlink($temp);
                }
            }
        }
        $installed[$role] = true;
        ecrire_config("provenix_platform_logos_installed", $installed);
    }
    include_spip("inc/invalideur");
    suivre_invalideur("1");
}

/** Install the editable illustrative example without changing article content. */
function provenix_install_lineage_demo()
{
    include_spip("inc/config");
    $content = lire_config("provenix_contenu", []);
    $old_keys = [];
    for ($number = 10; $number <= 33; $number++) {
        $key = sprintf("sommaire_%03d", $number);
        if (isset($content[$key])) {
            $old_keys[$key] = $content[$key];
            unset($content[$key]);
        }
    }
    if (lire_config("provenix_lineage_before", null) === null) {
        ecrire_config("provenix_lineage_before", $old_keys);
    }
    $seed = json_decode(file_get_contents(__DIR__ . "/initial-content.json"), true);
    foreach ($seed as $key => $value) {
        if (strpos($key, "lineage_") === 0 && !array_key_exists($key, $content)) {
            $content[$key] = $value;
        }
    }
    ecrire_config("provenix_contenu", $content);
    include_spip("inc/invalideur");
    suivre_invalideur("1");
}

/** Supply defaults only for the new ontology article before native migration. */
function provenix_prepare_ontology()
{
    include_spip("inc/config");
    $seed = json_decode(file_get_contents(__DIR__ . "/initial-content.json"), true);
    $content = lire_config("provenix_contenu", []);
    foreach ($seed as $key => $value) {
        if (strpos($key, "ontology_") === 0 && !array_key_exists($key, $content)) {
            $content[$key] = $value;
        }
    }
    ecrire_config("provenix_contenu", $content);
}

/** Decorative arrows belong to the theme; preserve the editor's label wording. */
function provenix_clean_diagram_content()
{
    include_spip("inc/config");
    include_spip("inc/autoriser");
    include_spip("action/editer_article");
    $bindings = provenix_article_bindings();
    foreach (["sommaire_069", "about_025", "services_039"] as $key) {
        $role = $bindings[$key]["role"];
        $id = (int) lire_config("provenix_articles/" . $role, 0);
        if (!$id) {
            continue;
        }
        $label = sql_getfetsel("nom_site", "spip_articles", "id_article=" . $id);
        if ($label === false) {
            continue;
        }
        $clean = preg_replace('/\s*[\x{2190}-\x{21FF}]\x{FE0F}?\s*$/u', '', $label);
        if ($clean === null || $clean === $label) {
            continue;
        }
        autoriser_exception("modifier", "article", $id);
        try {
            $error = article_modifier($id, ["nom_site" => $clean]);
            if ($error) {
                throw new RuntimeException((string) $error);
            }
        } finally {
            autoriser_exception("modifier", "article", $id, false);
        }
    }
    $content = lire_config("provenix_contenu", []);
    if (array_key_exists("lineage_hint", $content)) {
        unset($content["lineage_hint"]);
        ecrire_config("provenix_contenu", $content);
    }
    include_spip("inc/invalideur");
    suivre_invalideur("1");
}

/** Replace only former default mail-app instructions; retain editorial changes. */
function provenix_install_contact_form()
{
    include_spip('inc/config');
    $seed = json_decode(file_get_contents(__DIR__ . '/initial-content.json'), true);
    $old = json_decode('{"contact_015":"We\u2019ll prepare an email in your preferred mail app.","contact_030":"Prepare email","contact_031":"This form opens your email app. Your message is only sent when you choose Send there."}', true);
    $content = lire_config('provenix_contenu', []);
    $bindings = provenix_article_bindings();
    foreach ($seed as $key => $value) {
        if (isset($bindings[$key])) {
            continue;
        }
        if (!array_key_exists($key, $content) && strpos($key, 'contact_') === 0) {
            $content[$key] = $value;
        } elseif (isset($old[$key]) && $content[$key] === $old[$key]) {
            $content[$key] = $value;
        }
    }
    unset($content['email_status']);
    ecrire_config('provenix_contenu', $content);
    include_spip('inc/invalideur');
    suivre_invalideur('1');
}
