<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
function provenix_site_autoriser($flux) {
    return $flux;
}
function autoriser_provenixcontenu_menu_dist($faire, $type, $id, $qui, $opt) {
    return autoriser('configurer', '', 0, $qui);
}
