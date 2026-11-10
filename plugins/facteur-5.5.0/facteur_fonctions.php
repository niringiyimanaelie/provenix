<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher partiellement un mot de passe que l'on ne veut pas rendre lisible par un champ hidden
 * @param string $passe
 * @param bool $afficher_partiellement
 * @param int|null $portion_pourcent
 * @return string
 */
function facteur_affiche_password_masque($passe, $afficher_partiellement = false, $portion_pourcent = null) {
	if (function_exists('spip_affiche_mot_de_passe_masque')) {
		return spip_affiche_mot_de_passe_masque($passe, $afficher_partiellement, $portion_pourcent);
	}
	$l = strlen($passe);

	if ($l<=8 or !$afficher_partiellement){
		if (!$l) {
			return ''; // montrer qu'il y a pas de mot de passe si il y en a pas
		}
		return str_pad('',$afficher_partiellement ? $l : 16,'*');
	}

	if (is_null($portion_pourcent)) {
		if (!defined('_SPIP_AFFICHE_MOT_DE_PASSE_MASQUE_PERCENT')) {
			define('_SPIP_AFFICHE_MOT_DE_PASSE_MASQUE_PERCENT', 20); // 20%
		}
		$portion_pourcent = _SPIP_AFFICHE_MOT_DE_PASSE_MASQUE_PERCENT;
	}
	if ($portion_pourcent >= 100) {
		return $passe;
	}
	$e = intval(ceil($l * $portion_pourcent / 100 / 2));
	$e = max($e, 0);
	$mid = str_pad('',$l-2*$e,'*');
	if ($e>0 and strlen($mid)>8){
		$mid = '***...***';
	}
	return substr($passe,0,$e) . $mid . ($e > 0 ? substr($passe,-$e) : '');
}

/**
 * Un filtre pour transformer les retour ligne texte en br si besoin (si pas autobr actif)
 *
 * @param string $texte
 * @return string
 */
function facteur_nl2br_si_pas_autobr($texte){
	if (_AUTOBR) return $texte;
	include_spip("inc/filtres");
	$texte = post_autobr($texte);
	return $texte;
}



/**
 * @see inc_facteur_mail_wrap_to_html_dist
 *
 * @param string $texte_ou_html
 * @return string
 */
function facteur_email_wrap_to_html($texte_ou_html){

	$facteur_mail_wrap_to_html = charger_fonction('facteur_mail_wrap_to_html', 'inc');
	return $facteur_mail_wrap_to_html($texte_ou_html);
}

/**
 * voir inc/facteur_convertir_styles_inline
 *
 * @param string $body
 * @return string
 */
function facteur_convertir_styles_inline($body){

	$facteur_convertir_styles_inline = charger_fonction('facteur_convertir_styles_inline', 'inc');
	return $facteur_convertir_styles_inline($body);
}


/**
 * voir inc/facteur_mail_html2text
 * @param string $html
 * @return string
 */
function facteur_mail_html2text($html){

	$facteur_mail_html2text = charger_fonction('facteur_mail_html2text', 'inc');
	return $facteur_mail_html2text($html);
}


/**
 * Insertion dans le pipeline formulaire_fond (SPIP)
 *
 * On indique dans le formulaire de configuration de l'identité du site
 * que facteur surchargera l'email configuré ici pour envoyer les emails
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifé
 */
function facteur_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'configurer_identite'
	  and include_spip('inc/config')
	  and lire_config('facteur/adresse_envoi') === 'oui'
	  and strlen($email = lire_config('facteur/adresse_envoi_email', '')) ) {
		$url = generer_url_ecrire('configurer_facteur');
		$ajout = '<p class="notice" style="margin-top:0">'._T('facteur:message_identite_email', array('url' => $url, 'email' => $email)).'</p>';
		if (preg_match(",<(div|li) [^>]*class=[\"']editer editer_email_webmaster.*>,Uims", $flux['data'], $match)) {
			$p = strpos($flux['data'], (string) $match[0]);
			$p = strpos($flux['data'], "<input", $p);
			$p = strpos($flux['data'], "</".$match[1], $p);
			$flux['data'] = substr_replace($flux['data'], $ajout, $p, 0);
		}
	}
	return $flux;
}
