<?php
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function provenix_article_plan()
{
    static $plan;
    if ($plan === null) {
        $plan = json_decode(
            file_get_contents(dirname(__DIR__) . "/article-plan.json"),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }
    return $plan;
}

function provenix_article_bindings()
{
    static $bindings;
    if ($bindings === null) {
        $bindings = [];
        foreach (provenix_article_plan()["articles"] as $article) {
            foreach ($article["fields"] as $field => $definition) {
                foreach ($definition["keys"] as $index => $key) {
                    $bindings[$key] = [
                        "role" => $article["role"],
                        "field" => $field,
                        "index" => $index,
                        "definition" => $definition,
                    ];
                }
            }
        }
    }
    return $bindings;
}

function provenix_article_row($role)
{
    include_spip("inc/config");
    $id = (int) lire_config("provenix_articles/" . $role, 0);
    if (!$id) {
        return [];
    }
    if (!isset($GLOBALS["provenix_article_cache"][$id])) {
        $GLOBALS["provenix_article_cache"][$id] =
            sql_fetsel("*", "spip_articles", "id_article=" . $id . " AND statut='publie'") ?: [];
    }
    return $GLOBALS["provenix_article_cache"][$id];
}

function provenix_article_value($binding)
{
    $row = provenix_article_row($binding["role"]);
    $value = (string) ($row[$binding["field"]] ?? "");
    $definition = $binding["definition"];
    if (count($definition["keys"]) === 1) {
        return $value;
    }
    if (!empty($definition["list"])) {
        $parts = preg_split("/\R/", $value);
        $parts = array_values(
            array_filter(
                array_map(static function ($line) {
                    return preg_replace("/^-\*\s*/", "", trim($line));
                }, $parts),
                "strlen",
            ),
        );
    } else {
        $parts = explode($definition["separator"], str_replace("\r\n", "\n", $value));
    }
    return $parts[$binding["index"]] ?? "";
}

function provenix_article_body($role)
{
    include_spip("inc/texte");
    $row = provenix_article_row($role);
    if (empty($row["texte"])) {
        return "";
    }
    lang_select($row["lang"] ?: "en");
    try {
        return propre($row["texte"]);
    } finally {
        lang_select();
    }
}

function provenix_article_directory($group)
{
    include_spip("inc/config");
    $result = [];
    foreach (provenix_article_plan()["articles"] as $article) {
        if (
            $article["section"] !== $group &&
            !($group === "services" && $article["section"] === "service_catalogue")
        ) {
            continue;
        }
        $id = (int) lire_config("provenix_articles/" . $article["role"], 0);
        if ($id) {
            $row = sql_fetsel("titre,statut", "spip_articles", "id_article=" . $id);
            $result[] = [
                "id" => $id,
                "label" => $article["label"],
                "titre" => $row["titre"] ?? "",
                "statut" => $row["statut"] ?? "",
            ];
        }
    }
    return $result;
}

/** Idempotent migration: preserve current edits and never overwrite existing articles. */
function provenix_migrate_articles()
{
    include_spip("inc/config");
    include_spip("inc/autoriser");
    include_spip("action/editer_rubrique");
    include_spip("action/editer_article");
    $versioned = lire_config("objets_versions", []);
    if (!is_array($versioned)) {
        $versioned = [];
    }
    if (!in_array("spip_articles", $versioned, true)) {
        $versioned[] = "spip_articles";
        ecrire_config("objets_versions", $versioned);
    }
    $content = lire_config("provenix_contenu", []);
    $sections = lire_config("provenix_sections", []);
    $articles = lire_config("provenix_articles", []);
    // Keep a database backup of the original configuration for recovery.
    if (lire_config("provenix_contenu_avant_articles", null) === null) {
        ecrire_config("provenix_contenu_avant_articles", $content);
    }
    foreach (provenix_article_plan()["sections"] as $role => $title) {
        if (!empty($sections[$role])) {
            continue;
        }
        $parent = $role === "service_catalogue" ? (int) $sections["services"] : 0;
        $sections[$role] = rubrique_inserer($parent, ["titre" => $title, "lang" => "en"]);
        if (!$sections[$role]) {
            throw new RuntimeException("Could not create section: " . $role);
        }
        ecrire_config("provenix_sections", $sections);
    }
    foreach (provenix_article_plan()["articles"] as $article) {
        $role = $article["role"];
        if (!empty($articles[$role])) {
            continue;
        }
        $fields = ["lang" => "en", "statut" => "prepa"];
        foreach ($article["fields"] as $field => $definition) {
            $values = array_map(static function ($key) use ($content) {
                return (string) ($content[$key] ?? "");
            }, $definition["keys"]);
            if (!empty($definition["list"])) {
                $fields[$field] = implode(
                    "\n",
                    array_map(static function ($value) {
                        return "-* " . $value;
                    }, $values),
                );
            } else {
                $fields[$field] = implode($definition["separator"], $values);
            }
        }
        $id = article_inserer((int) $sections[$article["section"]]);
        if (!$id) {
            throw new RuntimeException("Could not create article: " . $role);
        }
        $error = article_modifier($id, $fields);
        if ($error) {
            throw new RuntimeException($error);
        }
        // Upgrade jobs can run without a visitor session. Scope this exception
        // to publishing only the article just created by the migration.
        $section_id = (int) $sections[$article["section"]];
        autoriser_exception("publierdans", "rubrique", $section_id);
        try {
            $error = article_instituer($id, ["statut" => "publie"]);
        } finally {
            autoriser_exception("publierdans", "rubrique", $section_id, false);
        }
        if ($error) {
            throw new RuntimeException($error);
        }
        if (sql_getfetsel("statut", "spip_articles", "id_article=" . $id) !== "publie") {
            throw new RuntimeException("Migration article was not published: " . $role);
        }
        include_spip("action/editer_liens");
        $author = (int) sql_getfetsel(
            "id_auteur",
            "spip_auteurs",
            "statut='0minirezo' AND webmestre='oui'",
        );
        if ($author) {
            objet_associer(["auteur" => $author], ["article" => $id]);
        }
        $articles[$role] = $id;
        ecrire_config("provenix_articles", $articles);
    }
    foreach (array_keys(provenix_article_bindings()) as $key) {
        unset($content[$key]);
    }
    ecrire_config("provenix_contenu", $content);
    foreach (
        [
            "articles_surtitre",
            "articles_soustitre",
            "articles_descriptif",
            "articles_chapeau",
            "articles_ps",
            "articles_urlref",
        ]
        as $option
    ) {
        ecrire_meta($option, "oui");
    }
    include_spip("inc/invalideur");
    suivre_invalideur("1");
}

function provenix_section($role)
{
    include_spip("inc/config");
    return (int) lire_config("provenix_sections/" . $role, 0);
}

function provenix_service_role($id)
{
    include_spip("inc/config");
    foreach (lire_config("provenix_articles", []) as $role => $article_id) {
        if ((int) $article_id === (int) $id) {
            return $role;
        }
    }
    return "service-" . (int) $id;
}

function provenix_champ($id, $field)
{
    $allowed = ["titre", "surtitre", "soustitre", "descriptif", "chapo", "nom_site"];
    if (!in_array($field, $allowed, true)) {
        return "";
    }
    $row =
        sql_fetsel("*", "spip_articles", "id_article=" . (int) $id . " AND statut='publie'") ?: [];
    $value = $row[$field] ?? "";
    if ($value === "" && in_array($field, ["soustitre", "nom_site"], true)) {
        $value = $row["titre"] ?? "";
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function provenix_service_list($id)
{
    $text = sql_getfetsel(
        "texte",
        "spip_articles",
        "id_article=" . (int) $id . " AND statut='publie'",
    );
    $items = [];
    foreach (preg_split("/\R/", (string) $text) as $line) {
        $line = preg_replace("/^-\*\s*/", "", trim($line));
        if ($line !== "") {
            $items[] =
                "<li>" . htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "</li>";
        }
    }
    return $items ? '<ul class="check-list">' . implode("", $items) . "</ul>" : "";
}

function provenix_service_icon($number)
{
    return [1 => "⌘", 2 => "ƒ", 3 => "◎"][(int) $number] ?? "◎";
}

function provenix_service_number($number)
{
    return sprintf("%02d", (int) $number);
}
