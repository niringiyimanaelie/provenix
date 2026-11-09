<?php
/** CLI-only installer. Uses SPIP APIs and never resets existing editorial values. */
if (PHP_SAPI !== "cli") {
    http_response_code(403);
    exit();
}
chdir(dirname(__DIR__));
require "vendor/autoload.php";
require "ecrire/inc_version.php";
include_spip("inc/plugin");
// Force directory discovery after adding a new plugin to an existing install.
clearstatcache();
[$infos, $list, $invalid] = liste_plugin_valides(["provenix_site"], true);
if ($invalid) {
    throw new RuntimeException(json_encode($invalid));
}
ecrire_plugin_actifs(["provenix_site"], false, "ajoute");
$active = unserialize($GLOBALS["meta"]["plugin"] ?? "a:0:{}");
if (!isset($active["PROVENIX_SITE"])) {
    throw new RuntimeException("Provenix plugin did not activate.");
}
// SPIP retains earlier activation errors even after a successful retry.
// Clear only this now-valid plugin's stale error, preserving all other errors.
$activation_errors = plugin_donne_erreurs(true, false);
if (is_array($activation_errors)) {
    foreach (array_keys($activation_errors) as $path) {
        if (
            in_array(
                rtrim(str_replace("\\", "/", $path), "/"),
                ["plugins/provenix_site", "provenix_site"],
                true,
            )
        ) {
            unset($activation_errors[$path]);
        }
    }
    if ($activation_errors) {
        ecrire_meta("plugin_erreur_activation", serialize($activation_errors));
    } else {
        effacer_meta("plugin_erreur_activation");
    }
}
require_once "plugins/provenix_site/provenix_site_administrations.php";
$GLOBALS["visiteur_session"] =
    sql_fetsel(
        "id_auteur,statut,webmestre,lang",
        "spip_auteurs",
        "statut='0minirezo' AND webmestre='oui'",
    ) ?:
    [];
if (!$GLOBALS["visiteur_session"]) {
    throw new RuntimeException("An existing SPIP webmaster is required.");
}
$GLOBALS["visiteur_session"]["restreint"] = "";
provenix_site_upgrade("provenix_site_base_version", "1.4.1");
echo "Website content plugin installed.\n";
