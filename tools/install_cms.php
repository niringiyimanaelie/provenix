<?php
/** CLI-only installer. Uses SPIP APIs and never resets existing editorial values. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require 'ecrire/inc_version.php';
include_spip('inc/plugin');
// Force directory discovery after adding a new plugin to an existing install.
clearstatcache();
[$infos, $list, $invalid] = liste_plugin_valides(['provenix_site'], true);
if ($invalid) { throw new RuntimeException(json_encode($invalid)); }
ecrire_plugin_actifs(['provenix_site'], false, 'ajoute');
require_once 'plugins/provenix_site/provenix_site_administrations.php';
provenix_site_upgrade('provenix_site_base_version', '1.0.0');
echo "Website content plugin installed.\n";
