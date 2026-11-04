<?php
/** Integration checks against this local SPIP installation; CLI only. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
chdir(dirname(__DIR__));
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8080';
require 'vendor/autoload.php';
require 'ecrire/inc_version.php';
include_spip('provenix_site_fonctions');
include_spip('formulaires/editer_provenix');
// A test makes several saves in one PHP process, unlike normal HTTP requests.
// Load the database directly so a previous interrupted test cannot reuse cache.
lire_metas();
touch_meta(time() - (_META_CACHE_TIME << 1));

function verify($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS $message\n";
}

if (($argv[1] ?? '') === 'anonymous') {
    $GLOBALS['visiteur_session'] = ['id_auteur' => 0, 'statut' => '', 'restreint' => ''];
    verify(formulaires_editer_provenix_charger_dist('home') === false, 'Anonymous users cannot load the editor');
    verify(isset(formulaires_editer_provenix_traiter_dist('home')['message_erreur']), 'Anonymous users cannot save content');
    exit;
}

$GLOBALS['visiteur_session'] = sql_fetsel('id_auteur,statut,webmestre,lang', 'spip_auteurs', "statut='0minirezo' AND webmestre='oui'");
if (!$GLOBALS['visiteur_session']) { throw new RuntimeException('No existing webmaster found for local integration tests.'); }
$GLOBALS['visiteur_session']['restreint'] = '';
verify(autoriser('configurer'), 'Existing administrator has editor permission');

foreach (provenix_groupes() as $group => $label) {
    $loaded = formulaires_editer_provenix_charger_dist($group);
    verify(is_array($loaded) && !empty($loaded['sections']), "$label content loads");
    $rendered = recuperer_fond('prive/squelettes/contenu/provenix_contenu', ['groupe' => $group]);
    file_put_contents("tmp/cms-editor-$group.html", $rendered);
    foreach (provenix_schema($group) as $field) {
        verify(strpos($rendered, 'name="' . $field['key'] . '"') !== false, "Rendered editor field {$field['key']}");
    }
    verify(strpos($rendered, 'formulaire_action_args') !== false, "$label form uses SPIP signed actions");
}

foreach (provenix_schema('shared') as $field) { set_request($field['key'], provenix_valeur($field['key'])); }
set_request('contact_email', 'not-an-email');
verify(isset(formulaires_editer_provenix_verifier_dist('shared')['contact_email']), 'Invalid contact email rejected');
set_request('contact_email', provenix_valeur('contact_email'));
set_request('inc_header_001', ['unexpected-array']);
verify(isset(formulaires_editer_provenix_verifier_dist('shared')['inc_header_001']), 'Non-text input rejected');

// Check encoding without saving the payload to the database.
$memory = $GLOBALS['meta']['provenix_contenu'];
$values = unserialize($memory);
$values['sommaire_003'] = '<script>alert("test")</script>';
$GLOBALS['meta']['provenix_contenu'] = serialize($values);
verify(strpos(provenix_texte('sommaire_003'), '<script>') === false, 'CMS content is HTML-escaped');
$GLOBALS['meta']['provenix_contenu'] = $memory;

$original = provenix_valeur('sommaire_003');
foreach (provenix_schema('home') as $field) { set_request($field['key'], provenix_valeur($field['key'])); }
file_get_contents('http://localhost:8080/provenix/');
sleep(1);
try {
    set_request('sommaire_003', 'CMS save verification');
    verify(empty(formulaires_editer_provenix_verifier_dist('home')), 'Home form validates');
    $saved = formulaires_editer_provenix_traiter_dist('home');
    verify(isset($saved['message_ok']), 'CMS save succeeds');
    $live = file_get_contents('http://localhost:8080/provenix/');
    verify(strpos($live, 'CMS save verification') !== false, 'Saved CMS change reaches the public page without cache clearing');
} finally {
    sleep(1);
    set_request('sommaire_003', $original);
    formulaires_editer_provenix_traiter_dist('home');
    // ecrire_meta only touches metadata cache once per request. Our test's
    // intervening HTTP request rebuilt that cache, so invalidate it again.
    touch_meta(time() - (_META_CACHE_TIME << 1));
}
verify(strpos(file_get_contents('http://localhost:8080/provenix/'), 'CMS save verification') === false, 'Original content restored');
