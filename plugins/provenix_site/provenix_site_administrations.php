<?php
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function provenix_site_upgrade($nom_meta_base_version, $version_cible)
{
    include_spip("base/upgrade");
    include_spip("inc/provenix_articles");
    maj_plugin($nom_meta_base_version, $version_cible, [
        "create" => [["provenix_site_initialiser"], ["provenix_migrate_articles"], ["provenix_install_platform_logos"]],
        "1.1.0" => [["provenix_migrate_articles"]],
        "1.2.0" => [["provenix_prepare_platforms"], ["provenix_migrate_articles"], ["provenix_install_platform_logos"]],
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
