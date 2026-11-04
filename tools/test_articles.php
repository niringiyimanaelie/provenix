<?php
/** Local integration checks. Every modified article is restored in finally. */
if (PHP_SAPI !== "cli") {
    http_response_code(403);
    exit();
}
chdir(dirname(__DIR__));
require "vendor/autoload.php";
require "ecrire/inc_version.php";
include_spip("inc/config");
include_spip("inc/autoriser");
include_spip("inc/provenix_articles");
include_spip("provenix_site_fonctions");
include_spip("action/editer_article");
lire_metas();
$GLOBALS["visiteur_session"] = sql_fetsel(
    "id_auteur,statut,webmestre,lang",
    "spip_auteurs",
    "statut='0minirezo' AND webmestre='oui'",
);
$GLOBALS["visiteur_session"]["restreint"] = "";
$test_author = $GLOBALS["visiteur_session"];
function check_article($ok, $label)
{
    if (!$ok) {
        throw new RuntimeException($label);
    }
    echo "PASS $label\n";
}
function article_http($page = "")
{
    return file_get_contents("http://localhost:8080/provenix/" . $page);
}
function reset_test_cache()
{
    unset($GLOBALS["provenix_article_cache"]);
    touch_meta(time() - (_META_CACHE_TIME << 1));
}

$mapping = lire_config("provenix_articles", []);
check_article(count($mapping) === 22, "22 native articles mapped to the existing website");
check_article(
    (int) sql_countsel(
        "spip_articles",
        sql_in("id_article", array_values($mapping)) . " AND statut='publie'",
    ) === 22,
    "All migrated articles are published",
);
check_article(
    autoriser("modifier", "article", (int) $mapping["home_intro"]),
    "Existing administrator can edit native articles",
);
foreach (provenix_article_bindings() as $key => $binding) {
    check_article(
        !array_key_exists($key, lire_config("provenix_contenu", [])),
        "$key is no longer stored in active website settings",
    );
}
$before = sql_countsel("spip_articles");
provenix_migrate_articles();
check_article(
    sql_countsel("spip_articles") === $before,
    "Migration can rerun without creating duplicates",
);

$id = (int) $mapping["home_intro"];
$original = sql_fetsel("titre,statut,date", "spip_articles", "id_article=" . $id);
article_http();
sleep(1);
try {
    article_modifier($id, [
        "titre" => "Native article verification",
        "statut" => $original["statut"],
    ]);
    reset_test_cache();
    check_article(
        strpos(article_http(), "Native article verification") !== false,
        "Native article editing updates the homepage",
    );
} finally {
    sleep(1);
    article_modifier($id, ["titre" => $original["titre"], "statut" => $original["statut"]]);
    reset_test_cache();
}
check_article(
    strpos(article_http(), "Native article verification") === false,
    "Original homepage article restored",
);

$id = (int) $mapping["lineage"];
$original = sql_fetsel("statut,date", "spip_articles", "id_article=" . $id);
article_http("spip.php?page=services");
sleep(1);
try {
    // Revision hooks may initialize an anonymous web session in CLI. Restore
    // the test administrator for each simulated request (no cookie is created).
    $GLOBALS["visiteur_session"] = $test_author;
    check_article(
        autoriser("publierdans", "rubrique", provenix_section("service_catalogue")),
        "Administrator can publish services",
    );
    article_instituer($id, ["statut" => "prepa"]);
    reset_test_cache();
    check_article(
        strpos(article_http("spip.php?page=services"), 'id="lineage"') === false,
        "Draft service is hidden from the Services page",
    );
    check_article(
        strpos(article_http(), "#lineage") === false,
        "Draft service is hidden from homepage cards",
    );
} finally {
    sleep(1);
    $GLOBALS["visiteur_session"] = $test_author;
    article_instituer($id, $original);
    reset_test_cache();
}
check_article(
    strpos(article_http("spip.php?page=services"), 'id="lineage"') !== false,
    "Republished service appears again",
);
check_article(
    (int) sql_countsel("spip_versions", "objet='article' AND id_objet=" . $id) > 0,
    "Native article revision history is recorded",
);
