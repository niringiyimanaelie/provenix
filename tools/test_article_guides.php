<?php
/** Read-only regression check: guide links must resolve to actual article IDs. */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
chdir(dirname(__DIR__));
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8080';
define('_ESPACE_PRIVE', true);
require 'vendor/autoload.php';
require 'ecrire/inc_version.php';
include_spip('inc/texte');
include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip('inc/config');
include_spip('inc/session');
include_spip('inc/provenix_articles');
$GLOBALS['visiteur_session'] = sql_fetsel(
    'id_auteur,statut,webmestre,lang',
    'spip_auteurs',
    "statut='0minirezo' AND webmestre='oui'"
);
if (!$GLOBALS['visiteur_session']) {
    throw new RuntimeException('An existing webmaster is required.');
}
$GLOBALS['visiteur_session']['restreint'] = '';
$checked = [];
foreach (lire_config('provenix_sections', []) as $section_id) {
    $html = recuperer_fond('prive/squelettes/contenu/rubrique', [
        'exec' => 'rubrique', 'id_rubrique' => $section_id, 'espace_prive' => 1,
    ], ['var_mode' => 'recalcul']);
    $doc = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($doc);
    foreach ($xpath->query('//div[contains(@class,"provenix-editor-guide")]//a') as $link) {
        // DOMDocument decodes HTML entities once, just as a browser does.
        $url = $link->getAttribute('href');
        parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
        $id = (int) ($query['id_article'] ?? 0);
        if (($query['exec'] ?? '') !== 'article' || !$id || strpos($url, '&amp;') !== false) {
            throw new RuntimeException('Malformed guide URL: ' . $url);
        }
        if (!sql_countsel('spip_articles', 'id_article=' . $id)) {
            throw new RuntimeException('Missing linked article: ' . $id);
        }
        $checked[$id] = true;
    }
}
$expected = array_values(lire_config('provenix_articles', []));
if (array_diff($expected, array_keys($checked))) {
    throw new RuntimeException('Some mapped article links were not checked.');
}
echo 'PASS ' . count($checked) . " article links resolve correctly after browser-style HTML decoding.\n";
