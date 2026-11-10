<?php

/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Envoyer_mails
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_LOG_FACTEUR')) {
	define('_LOG_FACTEUR', _LOG_INFO);
}
if (!defined('_EMAIL_AUTO_CONVERT_TEXT_TO_HTML')) {
	define('_EMAIL_AUTO_CONVERT_TEXT_TO_HTML', true);
}

// inclure le fichier natif de SPIP, pour les fonctions annexes
include_once _DIR_RESTREINT . 'inc/envoyer_mail.php';
include_spip('inc/facteur');


/**
 * @param array|string $destinataire
 *   si array : un tableau de mails
 *   si string : un mail ou une liste de mails séparés par des virgules
 * @param string $sujet
 * @param string|array $message
 *   au format string, c'est un corps d'email au format texte, comme supporte nativement par le core
 *   au format array, c'est un corps etendu qui peut contenir
 *     string texte : le corps d'email au format texte
 *     string html : le corps d'email au format html
 *     string from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string cc : destinataires en copie conforme
 *     string bcc : destinataires en copie conforme cachee
 *     string|array repondre_a : une ou plusieurs adresses à qui répondre.
 *       On peut aussi donner une liste de tableaux du type :
 *         array('email' => 'test@exemple.com', 'nom' => 'Adresse de test')
 *       pour spécifier un nom d'envoyeur pour chaque adresse.
 *     string nom_repondre_a : le nom d'envoyeur pour compléter l'email repondre_a
 *     string adresse_erreur : addresse de retour en cas d'erreur d'envoi
 *     array pieces_jointes : listes de pieces a embarquer dans l'email, chacune au format array :
 *       string chemin : chemin file system pour trouver le fichier a embarquer
 *       string nom : nom du document tel qu'apparaissant dans l'email
 *       string encodage : encodage a utiliser, parmi 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 *       string mime : mime type du document
 *     array headers : tableau d'en-tetes personalises, une entree par ligne d'en-tete
 *     bool exceptions : lancer une exception en cas d'erreur (false par defaut)
 *     bool important : un flag pour signaler les messages important qui necessitent un feedback en cas d'erreur
 * @param string|null $from (deprecie, utiliser l'entree from de $message)
 * @param string $headers (deprecie, utiliser l'entree headers de $message)
 * @return bool
 * @throws Exception
 */
function inc_envoyer_mail($destinataire, string $sujet, $message, ?string $from = '', ?string $headers = '') {

	if (!is_array($message)) {
		$message_string = $message;
		$message = [];
		$headers = $headers ?? '';
		if ($headers and preg_match(',Content-Type:\s*text/html,ims', $headers)) {
			$message['html'] = $message_string;
		} else {
			// Autodetection : tester si le mail est en HTML
			if (
				strpos($headers, 'Content-Type:') === false
				and strpos($message_string, '<') !== false // eviter les tests suivants si possible
				and $ttrim = trim($message_string)
				and substr($ttrim, 0, 1) == '<'
				and substr($ttrim, -1, 1) == '>'
				and stripos($ttrim, '</html>') !== false
			) {
				$message['html'] = $message_string;
			} // c'est vraiment un message texte
			else {
				$message['texte'] = nettoyer_caracteres_mail($message_string);
			}
		}
	}

	if (!is_null($headers) and strlen($headers) and empty($message['headers'])) {
		$headers = array_map('trim', explode("\n", $headers));
		$headers = array_filter($headers);
		if (!empty($headers)) {
			$message['headers'] = $headers;
		}
	}

	if (!is_null($from) and strlen($from) and empty($message['from'])) {
		$message['from'] = trim($from);
	}

	$res = facteur_envoyer_mail($destinataire, $sujet, $message);
	return  $res ? true : false;
}
