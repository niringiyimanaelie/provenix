<?php

/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Formulaires\Tester_facteur
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_tester_facteur_charger_dist() {
	include_spip('inc/config');

	$valeurs = [
		'email_test' => $GLOBALS['meta']['email_webmaster'],
		'email_test_from' => '',
		'email_test_piece_jointe' => 0,
		'email_test_important' => 0,
	];
	if (!empty($GLOBALS['visiteur_session']['email'])) {
		$valeurs['email_test'] = $GLOBALS['visiteur_session']['email'];
	}

	if (defined('_TEST_EMAIL_DEST')) {
		if (_TEST_EMAIL_DEST) {
			include_spip('inc/facteur');
			$dest_forces = facteur_emails_to_array(_TEST_EMAIL_DEST);
			$valeurs['_message_warning'] = _T('facteur:info_envois_forces_vers_email', ['email' => $dest_forces]);
		}
		else {
			$valeurs['_message_warning'] = _T('facteur:info_envois_bloques_constante');
		}
	}

	if (isset($GLOBALS['_message_html_test'])) {
		$valeurs['_message_html_test'] = $GLOBALS['_message_html_test'];
	}

	return $valeurs;
}

function formulaires_tester_facteur_verifier_dist() {
	$erreurs = [];

	if (!$email = _request('email_test')) {
		$erreurs['email_test'] = _T('info_obligatoire');
	} elseif (!email_valide($email)) {
		$erreurs['email_test'] = _T('form_email_non_valide');
	}
	if (
		$from = _request('email_test_from')
		and !email_valide($from)
	) {
		$erreurs['email_test_from'] = _T('form_email_non_valide');
	}

	return $erreurs;
}

function formulaires_tester_facteur_traiter_dist() {

	// envoyer un message de test ?
	$res = [];
	$destinataire = _request('email_test');
	$message_html = '';
	$options = [];
	if ($from = _request('email_test_from')) {
		$options['from'] = $from;
	}
	if (_request('email_test_piece_jointe')) {
		$options['piece_jointe'] = true;
	}
	if (_request('email_test_important')) {
		$options['important'] = true;
	}

	$err = facteur_envoyer_mail_test($destinataire, _T('facteur:corps_email_de_test'), $message_html, $options);
	if ($err) {
		$res['message_erreur'] = nl2br($err);
	} else {
		$res['message_ok'] = _T('facteur:email_test_envoye');
		$GLOBALS['_message_html_test'] = $message_html;
	}

	return $res;
}

/**
 * Inliner du contenu base64 pour presenter le html du mail de test envoye
 * @param string $texte
 * @param string $type
 * @return string
 */
function facteur_inline_base64src($texte, $type = 'text/html') {
	return "data:$type;charset=" . $GLOBALS['meta']['charset'] . ';base64,' . base64_encode($texte);
}

/**
 * Fonction pour tester un envoi de mail ver sun destinataire
 * renvoie une erreur eventuelle ou rien si tout est OK
 * @param string $destinataire
 * @param string $titre
 * @param string $message_html
 * @param array $options
 * @return string
 *   message erreur ou vide si tout est OK
 */
function facteur_envoyer_mail_test($destinataire, $titre, &$message_html, $options = []) {

	$piece_jointe = [];

	if (test_plugin_actif('medias') and !empty($options['piece_jointe'])) {
		include_spip('inc/documents');
		// trouver une piece jointe dans les documents si possible, la plus legere possible, c'est juste pour le principe
		// mais de preference un pdf car ça trig moins les antispam qu'un fichier office par exemple
		foreach (['pdf', '%'] as $ext) {
			$docs = sql_allfetsel('*', 'spip_documents', 'extension LIKE ' . sql_quote($ext) . ' AND media=' . sql_quote('file') . ' AND distant=' . sql_quote('non') . ' AND brise=0', '', 'taille', '0,10');
			foreach ($docs as $doc) {
				$file = get_spip_doc($doc['fichier']);
				if (file_exists($file)) {
					$mime = sql_getfetsel('mime_type', 'spip_types_documents', 'extension=' . sql_quote($doc['extension']));
					$piece_jointe = [
						'chemin' => $file,
						'nom' => $doc['titre'] ?: basename($doc['fichier']),
						'mime' => $mime,
					];
					break 2;
				}
			}
		}
		unset($options['piece_jointe']);
	}

	// trouver un article, de preference dans la langue du site, avec une image jointe
	$id_article = 0;
	if (defined('_FACTEUR_TESTER_MAIL_ID_ARTICLE')) {
		$id_article = sql_getfetsel('id_article', 'spip_articles', "statut='publie' AND id_article=" . intval(_FACTEUR_TESTER_MAIL_ID_ARTICLE));
	}
	if (!$id_article) {
		foreach ([$GLOBALS['meta']['langue_site'], '%'] as $lang) {
			foreach (['%<img%', '%<emb%', '%<doc%', '%'] as $modele) {
				if ($modele === '%' and $lang !== '%') {
					continue;
				}
				$id_article = sql_getfetsel('id_article', 'spip_articles', "statut='publie' AND lang LIKE " . sql_quote($lang) . ' AND texte LIKE ' . sql_quote($modele), '', 'LENGTH(texte) DESC,id_article', '0,1');
				if ($id_article) {
					break 2;
				}
			}
		}
	}
	if (!$id_article) {
		return _T('facteur:erreur') . ' ' . _T('facteur:erreur_aucun_article_test');
	}

	$message_html	= recuperer_fond('emails/test_email_html', ['piece_jointe' => $piece_jointe, 'id_article' => $id_article]);
	$message_texte	= recuperer_fond('emails/test_email_texte', ['piece_jointe' => $piece_jointe, 'id_article' => $id_article]);
	$corps = [
		'html' => $message_html,
		'texte' => $message_texte,
		'exceptions' => true,
	];

	if ($piece_jointe) {
		$corps['pieces_jointes'] = [$piece_jointe];
	}

	if ($options) {
		$corps = array_merge($options, $corps);
	}

	// prefixer le titre par la date, c'est utile quand on debug et teste de nombreuses fois...
	$titre = '[' . date('Y-m-d H:i:s') . '] ' . $titre;

	// passer par envoyer_mail pour bien passer par les pipeline et avoir tous les logs
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	try {
		$retour = $envoyer_mail($destinataire, $titre, $corps);
	} catch (Exception $e) {
		return $e->getMessage();
	}

	// si echec mais pas d'exception, on signale de regarder dans les logs
	if (!$retour) {
		return _T('facteur:erreur') . ' ' . _T('facteur:erreur_dans_log');
	}

	// tout est OK, pas d'erreur
	return '';
}
