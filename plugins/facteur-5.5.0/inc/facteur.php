<?php

/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_factory
 */

defined('_FACTEUR_NOMBRE_ESSAIS_ENVOI_MAIL') || define('_FACTEUR_NOMBRE_ESSAIS_ENVOI_MAIL', 5);
defined('_FACTEUR_NOMBRE_ESSAIS_SEUIL_NOTIFIE') || define('_FACTEUR_NOMBRE_ESSAIS_SEUIL_NOTIFIE', 1);
defined('_FACTEUR_DELAI_MAX_ESSAIS_ENVOI_MAIL') || define('_FACTEUR_DELAI_MAX_ESSAIS_ENVOI_MAIL', 18);

/**
 * @param array|string $destinataires
 *   si array : un tableau de mails
 * si string : un mail ou une liste de mails séparés par des virgules
 * @param string $sujet
 * @param array $message
 *     string $texte : le corps d'email au format texte
 *     string $html : le corps d'email au format html
 *     string $from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string $nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string $cc : destinataires en copie conforme
 *     string $bcc : destinataires en copie conforme cachee
 *     string|array $repondre_a : une ou plusieurs adresses à qui répondre.
 *       On peut aussi donner une liste de tableaux du type :
 *         array('email' => 'test@exemple.com', 'nom' => 'Adresse de test')
 *       pour spécifier un nom d'envoyeur pour chaque adresse.
 *     string $nom_repondre_a : le nom d'envoyeur pour compléter l'email repondre_a
 *     string $adresse_erreur : addresse de retour en cas d'erreur d'envoi
 *     array $pieces_jointes : listes de pieces a embarquer dans l'email, chacune au format array :
 *       string $chemin : chemin file system pour trouver le fichier a embarquer
 *       string $nom : nom du document tel qu'apparaissant dans l'email
 *       string $encodage : encodage a utiliser, parmi 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 *       string $mime : mime type du document
 *     array $headers : tableau d'en-tetes personalises, une entree par ligne d'en-tete
 *     bool $exceptions : lancer une exception en cas d'erreur (false par defaut)
 *     bool $important : un flag pour signaler les messages important qui necessitent un feedback en cas d'erreur
 * @return bool|array
 * @throws Exception
 */
function facteur_envoyer_mail($destinataires, string $sujet, array $message, int $try = 0) {
	$args_retry = [$destinataires, $sujet, $message, $try];
	$important = false;

	// si $message est un tableau -> fonctionnalites etendues
	// avec entrees possible : html, texte, pieces_jointes, nom_envoyeur, ...

	// si on fournit un $message['html'] deliberemment vide, c'est qu'on n'en veut pas, et donc on restera au format texte
	$message_html = isset($message['html']) ? ($message['html'] ?: ' ') : '';
	$message_texte = isset($message['texte']) ? nettoyer_caracteres_mail($message['texte']) : '';
	$pieces_jointes = $message['pieces_jointes'] ?? [];
	$nom_envoyeur = $message['nom_envoyeur'] ?? '';
	$from = $message['from'] ?? '';
	$cc = $message['cc'] ?? [];
	$bcc = $message['bcc'] ?? [];
	$repondre_a = $message['repondre_a'] ?? '';
	$nom_repondre_a = $message['nom_repondre_a'] ?? '';
	$adresse_erreur = $message['adresse_erreur'] ?? '';
	$headers = $message['headers'] ?? [];
	if (is_string($headers)) {
		$headers = array_map('trim', explode("\n", $headers));
		$headers = array_filter($headers);
	}
	$important = (isset($message['important']) ? !!$message['important'] : $important);

	if (!strlen($sujet)) {
		$sujet = facteur_extraire_sujet($message_html, $message_texte);
	}

	$sujet = nettoyer_titre_email($sujet);

	// si le mail est en texte brut, on l'encapsule dans un modele surchargeable
	// pour garder le texte brut, il suffit de faire un modele qui renvoie uniquement #ENV*{texte}
	if ($message_texte and !$message_html and _EMAIL_AUTO_CONVERT_TEXT_TO_HTML) {
		$message_html = recuperer_fond('emails/texte', ['texte' => $message_texte, 'sujet' => $sujet]);
	}
	$message_html = trim($message_html);

	// si le mail est en HTML sans alternative, la generer
	if ($message_html and !$message_texte) {
		$facteur_mail_html2text = charger_fonction('facteur_mail_html2text', 'inc');
		$message_texte = $facteur_mail_html2text($message_html);
	}

	$exceptions = false;
	if (is_array($message) and isset($message['exceptions'])) {
		$exceptions = $message['exceptions'];
	}

	// On crée l'objet Facteur (PHPMailer) pour le manipuler ensuite
	$options = [];
	if ($exceptions) {
		$options['exceptions'] = $exceptions;
	}

	/** @var \SPIP\Facteur\FacteurMail $facteur */
	$facteur = facteur_factory($options);



	// commençons par verifier les destinataires
	// plusieurs destinataires peuvent etre fournis separes par des virgules
	// c'est un format standard dans l'envoi de mail
	// les passer au format array
	// Préparons les destinataires
	$destinataires = facteur_preparer_liste_emails($destinataires);
	$cc = facteur_preparer_liste_emails($cc);
	$bcc = facteur_preparer_liste_emails($bcc);
	$erreur = facteur_destinataires($facteur, $destinataires, $cc, $bcc);
	// si aucun destinataire valide on a une erreur et on va pas plus loin
	if ($erreur) {
		spip_log($erreur, 'mail.' . _LOG_ERREUR);
		if ($exceptions) {
			throw new Exception($erreur);
		}
		return false;
	}


	$facteur->setObjet($sujet);
	$facteur->setMessage($message_html, $message_texte);

	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (empty($from) and empty($facteur->From)) {
		$from = $GLOBALS['meta']['email_envoi'];
		if (empty($from) or !email_valide($from)) {
			spip_log('Meta email_envoi invalide. Le mail sera probablement vu comme spam.', 'mail.' . _LOG_ERREUR);
			$from = reset($destinataires);
		}
	}

	// "Marie Toto <Marie@toto.com>"
	if (preg_match(',^([^<>"]*)<([^<>"]+)>$,i', $from, $m)) {
		$nom_envoyeur = trim($m[1]);
		$from = trim($m[2]);
	}
	if (!empty($from)) {
		$facteur->From = $from;
		// la valeur par défaut de la config n'est probablement pas valable pour ce mail,
		// on l'écrase pour cet envoi
		$facteur->FromName = '';
	}

	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur) {
		$facteur->FromName = $nom_envoyeur;
	}

	// Si plusieurs emails dans le from, pas de Name !
	if (strpos($facteur->From, ',') !== false) {
		$facteur->FromName = '';
	}

	// S'il y a une adresse de reply-to
	if ($repondre_a) {
		if (is_array($repondre_a)) {
			foreach ($repondre_a as $courriel) {
				if (is_array($courriel)) {
					$facteur->AddReplyTo($courriel['email'], $courriel['nom']);
				} else {
					$facteur->AddReplyTo($courriel);
				}
			}
		} elseif ($nom_repondre_a) {
			$facteur->AddReplyTo($repondre_a, $nom_repondre_a);
		} else {
			$facteur->AddReplyTo($repondre_a);
		}
	}

	// S'il y a des pièces jointes on les ajoute proprement
	if (is_countable($pieces_jointes) ? count($pieces_jointes) : 0) {
		foreach ($pieces_jointes as $piece) {
			if (!empty($piece['chemin']) and file_exists($piece['chemin'])) {
				$facteur->AddAttachment(
					$piece['chemin'],
					$piece['nom'] ?? '',
					(isset($piece['encodage']) and in_array($piece['encodage'], ['base64', '7bit', '8bit', 'binary', 'quoted-printable'])) ? $piece['encodage'] : 'base64',
					$piece['mime'] ?? SPIP\Facteur\FacteurMail::_mime_types(pathinfo($piece['chemin'], PATHINFO_EXTENSION))
				);
			} else {
				spip_log('Piece jointe manquante ignoree : ' . json_encode($piece, JSON_THROW_ON_ERROR), 'facteur' . _LOG_ERREUR);
			}
		}
	}

	// Si une adresse email a été spécifiée pour les retours en erreur, on l'ajoute
	if (!empty($adresse_erreur)) {
		$facteur->Sender = $adresse_erreur;
	}

	if ($important) {
		$facteur->setImportant();
	}

	// si entetes personalises : les ajouter
	// attention aux collisions : si on utilise l'option cc de $message
	// et qu'on envoie en meme temps un header Cc: xxx, yyy
	// on aura 2 lignes Cc: dans les headers
	if (!empty($headers)) {
		foreach ($headers as $h) {
			// verifions le format correct : il faut au moins un ":" dans le header
			// et on filtre le Content-Type: qui sera de toute facon fourni par facteur
			if (
				strpos($h, ':') !== false
				and strncmp($h, 'Content-Type:', 13) !== 0
			) {
				if (strpos($h, 'Message-ID:') === 0) {
					$facteur->MessageID = trim(explode(':', $h, 2)[1]);
				} else {
					$facteur->AddCustomHeader($h);
				}
			}
		}
	}

	// indiquer au facteur si c'est un essai final ou non
	// et envoyer un header X-retry si c'est un retry
	$facteur->setIsFinalTry($try >= _FACTEUR_NOMBRE_ESSAIS_ENVOI_MAIL);
	if ($try > 0) {
		$facteur->addCustomHeader('X-retry',(string)$try . ($facteur->getIsFinalTry() ? '-last' : ''));
	}

	// On passe dans un pipeline pour modifier tout le facteur avant l'envoi
	/** @var \SPIP\Facteur\FacteurMail $facteur */
	$facteur = pipeline('facteur_pre_envoi', $facteur);

	// Et c'est parti on envoie enfin
	$backtrace = facteur_backtrace();
	$trace = $facteur->getMessageLog();
	spip_log("mail via facteur\n$trace", 'mail' . _LOG_FACTEUR);
	spip_log("mail\n$backtrace\n$trace", 'facteur' . _LOG_FACTEUR);

	// si c'est un mail important, preparer le forward a envoyer en cas d'echec
	// mais on delegue la gestion de cet envoi au facteur qui est le seul a savoir quoi faire
	// en fonction de la reponse et du modus operandi pour connaitre le status du message
	$dest_alertes = $facteur->Sender;
	if ($facteur->getIsFinalTry() && $important && $dest_alertes) {
		$dest = (is_array($destinataires) ? implode(', ', $destinataires) : $destinataires);
		$sujet_alerte = _T('facteur:sujet_alerte_mail_fail', ['dest' => $dest, 'sujet' => $sujet]);
		$args = func_get_args();
		$args[0] = $dest_alertes;
		$args[1] = $sujet_alerte;
		$args[2]['important'] = false; // ne pas faire une alerte sur l'envoi de l'alerte etc.
		if (!empty($args[2]['pieces_jointes'])) {
			foreach ($args[2]['pieces_jointes'] as $k => $pj) {
				// passer les chemins en absolus car on sait pas si l'alerte sera lancee depuis le meme cote racine/ecrire
				$args[2]['pieces_jointes'][$k]['chemin'] = realpath($pj['chemin']);
			}
		}
		$facteur->setSendFailFunction('envoyer_mail', $args, 'inc/');
	}

	$retour = $facteur->Send();

	if (!$retour) {
		spip_log('Erreur Envoi mail via Facteur : ' . print_r($facteur->ErrorInfo, true), 'mail' . _LOG_ERREUR);
		// si le mail est important, le facteur aura gere l'envoi de l'alerte fail
		// mais ici on gere une nouvelle tentative plus tard ou un dump du mail en echec
		// on recheck isFinalTry car selon la nature de l'erreur le facteur a pu indiquer qu'il est inutile de faire un nouvel essai
		if ($facteur->getIsFinalTry()) {
			$try = _FACTEUR_NOMBRE_ESSAIS_ENVOI_MAIL;
		}
		facteur_reprogrammer_ou_dumper_mail_echec($args_retry, $try + 1);
	}

	return $retour;
}

/**
 * Ressayer d'envoyer un mail dumpé dans un fichier suite à un essai
 *
 * @param string $mailid
 * @return void
 */
function facteur_retry_envoyer_mail(string $mailid) {
	$dir_tmp_facteur = sous_repertoire(_DIR_TMP, 'facteur');

	$file = $dir_tmp_facteur . $mailid . ".json";
	if (file_exists($file)) {
		$arguments = file_get_contents($file);
		if ($arguments = json_decode($arguments, true)) {

			include_spip('inc/envoyer_mail');

			$function = array_shift($arguments);
			if (function_exists($function)) {
				spip_log("facteur_retry_envoyer_mail: Nouvel essai pour l'envoi du mail $mailid via $function()", 'facteur' . _LOG_INFO_IMPORTANTE);
				@unlink($file);
				$function(...$arguments);
				return;
			}
		}

		spip_log("facteur_retry_envoyer_mail: Impossible de traiter le mail $mailid", 'facteur' . _LOG_ERREUR);
		$dir_tmp_facteur_failed = sous_repertoire($dir_tmp_facteur, 'failed');
		$file_archived = $dir_tmp_facteur_failed . basename($file);
		@rename($file, $file_archived);
		spip_log("Mail en echec archivé dans : $file_archived", 'facteur' . _LOG_INFO_IMPORTANTE);
	}
}


/**
 * Gérer l'echec de l'envoi de mail :
 *   * si on a atteint le nombre maxi d'essais on le dump dans tmp/facteur/failed/
 *   * sinon on le dump dans tmp/facteur/retry/ et on lance un job_queue pour le re-essayer plus tard
 *
 * @param array $arguments
 * @param int $try
 * @return void
 */
function facteur_reprogrammer_ou_dumper_mail_echec(array $arguments, int $try) {
	$dir_tmp_facteur = sous_repertoire(_DIR_TMP, 'facteur');

	// ajouter en tete le nom de la fonction
	array_unshift($arguments, 'facteur_envoyer_mail');

	// md5 invariant avec le nombre d'essai
	array_pop($arguments);
	$md5 = md5(json_encode($arguments));

	// mettre a jour le nombre d'essai dans les arguments
	$arguments[] = $try;

	// le dump des arguments
	$arguments = json_encode($arguments);

	if ($try >= _FACTEUR_NOMBRE_ESSAIS_ENVOI_MAIL) {
		// un mail definitivement en echec est stocke pour retraitement manuel eventuel
		$dir_tmp_facteur = sous_repertoire($dir_tmp_facteur, 'failed');
		$file = $dir_tmp_facteur . $md5 . ".json";
		file_put_contents($file, $arguments);
		spip_log("Mail en echec archivé dans : $file", 'facteur' . _LOG_INFO_IMPORTANTE);
	}
	else {
		// on ressaye plus tard
		$dir_tmp_facteur = sous_repertoire($dir_tmp_facteur, 'retry');
		$fileid = "$try-$md5";
		$file = $dir_tmp_facteur . $fileid . ".json";
		file_put_contents($file, $arguments);

		switch ($try) {
			case 1: //
				// 10mn
				$delay = 10 * 60;
				break;
			case 2:
				// 1h
				$delay = 60 * 60;
				break;
			case 3:
				// 3h
				$delay = 3 * 60 * 60;
				break;
			case 4:
				// 12h
				$delay = 12 * 60 * 60;
				break;
			case 5:
			default:
				$delay = _FACTEUR_DELAI_MAX_ESSAIS_ENVOI_MAIL * 60 * 60;
				break;
		}

		spip_log("Mail archivé pour un nouvel essai dans {$delay}s : $file", 'facteur' . _LOG_INFO_IMPORTANTE);
		job_queue_add('facteur_retry_envoyer_mail', "Re-essayer d'envoyer le mail en echec $fileid", ["retry/$fileid"], 'inc/facteur', false, time() + $delay);
	}

}


/**
 * Une fonction pour transformer un ou des emails en tableau d'emails
 * accepte en entree
 * - un unique email
 * - une liste d'emails au format string, séparés par une virgule
 * - un tableau d'emails
 *
 * La fonction pour explode la liste d'email est tirée du plugin verifier
 * https://git.spip.net/spip-contrib-extensions/verifier/-/blob/master/verifier/email.php?ref_type=heads#L70
 *
 * @param string|array $valeur
 * @return array
 */
function facteur_emails_to_array($valeur) {
	if (is_array($valeur)) {
		return array_map('trim', $valeur);
	}
	$p = strpos($valeur, '@');
	if ( $p !== false && strpos($valeur, ',', $p) !== false ) {
		if ($parts = preg_split('/(@[^@,]+),/' , $valeur, -1, PREG_SPLIT_DELIM_CAPTURE)) {
			$adresses = [];
			while (!empty($parts)) {
				$left = array_shift($parts);
				if (!empty($parts)) {
					$adresses[] = $left . array_shift($parts);
				} else {
					$adresses[] = $left;
				}
			}
			$adresses = array_map('trim', $adresses);
			$adresses = array_filter($adresses);
			return $adresses;
		}
	}
	return [trim($valeur)];
}


/**
 * Prend une liste d'email
 * Explose si nécessaire en tableau
 * Prend chaque email
 * Le trim
 * Supprime les emails invalide
 * @param string|array
 * @return array
**/
function facteur_preparer_liste_emails($emails): array {
	$emails = facteur_emails_to_array($emails);
	$emails = array_map('email_valide', $emails);
	$emails = array_filter($emails);
	return $emails;
}

/**
 * Initialiser les destinataires en s'assurant qu'il y en a au moins un valide
 * @param \Spip\Facteur\FacteurMail $facteur
 * @param array $to
 * @param array $cc
 * @param array $bcc
 * @return string
 * @throws \PHPMailer\PHPMailer\Exception
 */
function facteur_destinataires(Spip\Facteur\FacteurMail $facteur, array $to, array $cc, array $bcc): string {

	// mode TEST : forcer l'email ou bloquer tout envoi
	if (defined('_TEST_EMAIL_DEST')) {
		if (!_TEST_EMAIL_DEST) {
			return _T('facteur:erreur_envoi_bloque_constante');
		} else {
			$to = facteur_emails_to_array(_TEST_EMAIL_DEST);
			$cc = [];
			$bcc = [];
		}
	}

	// verifier qu'on a au moins un destinataire, meme si c'est un bcc ou un cc
	// suppression des adresses de courriels invalides, si aucune valide, renvoyer une erreur

	// initialiser les destinataires
	$nb_dest_valides = $facteur->setDest($to);

	// S'il y a des copies à envoyer
	if (!empty($cc)) {
		foreach ($cc as $courriel) {
			if (!in_array($courriel, $to)) {
				if ($nb_dest_valides ? $facteur->AddCC($courriel) : $facteur->setDest($courriel)) {
					$nb_dest_valides++;
					$to[] = $courriel; // on ajoute le mail au to local pour verifier l'unicite des destinataires
				}
			}
		}
	}

	// S'il y a des copies cachées à envoyer
	if (!empty($bcc)) {
		// si on avait pas encore de destinataire valide et 1 BCC on peut le mettre en destinataire
		// sinon non car alors il ne serait plus caché
		if (!$nb_dest_valides and count($bcc) === 1) {
			$nb_dest_valides = $facteur->setDest($bcc);
		}
		else {
			foreach ($bcc as $courriel) {
				if (!in_array($courriel, $to)) {
					if ($facteur->AddBCC($courriel)) {
						$nb_dest_valides++;
						$to[] = $courriel; // on ajoute le mail au to local pour verifier l'unicite des destinataires
					}
				}
			}
		}
	}

	if (!$nb_dest_valides) {
		return _L("Aucune adresse email de destination valable pour l'envoi du courriel.");
	}

	return '';
}


/**
 * Generer le FacteurXXX selon la config par defaut/passee en options
 * @param array $options
 *		bool $exceptions
 *      toute valeur qui surcharge les options fournies par `facteur_config()`
 * @return \SPIP\Facteur\FacteurMail
 * @throws \PHPMailer\PHPMailer\Exception
 * @see facteur_config()
 * @api
 */
function facteur_factory($options = []) {

	if (!is_array($options)) {
		$options = [];
	}
	$options = facteur_config($options);

	$config_mailer = $options['mailer'];
	$methodes = facteur_lister_methodes_mailer();
	if (
		!empty($methodes[$config_mailer]['class'])
		and $FacteurClass = $methodes[$config_mailer]['class']
		and include_spip("inc/Facteur/$FacteurClass")
		and class_exists($FacteurClass = "SPIP\\Facteur\\{$FacteurClass}")
	) {
		return new $FacteurClass($options);
	} else {
		spip_log("Impossible de trouver la methode $config_mailer ou sa classe " . (empty($methodes[$config_mailer]) ? '' : $methodes[$config_mailer]), 'facteur' . _LOG_ERREUR);

		// fallback fonction mail()
		include_spip('inc/Facteur/FacteurMail');
		return new SPIP\Facteur\FacteurMail($options);
	}
}

/**
 * Lister les methodes mailer disponibles et le nom de la classe a instancier
 * @return array[]
 */
function facteur_lister_methodes_mailer() {

	$methodes = [
		'mail' => [
			'class' => 'FacteurMail',
			'password' => [],
		],
		'smtp' => [
			'class' => 'FacteurSMTP',
			'password' => ['smtp_password'],
		],
	];

	// permettre l'extension via un pipeline
	$methodes = pipeline(
		'facteur_lister_methodes_mailer',
		[
			'args' => [],
			'data' => $methodes
		]
	);

	return $methodes;
}


/**
 * Recuperer la config de Facteur, avec eventuelle surcharge
 * en s'assurant que les meta ont bien ete migrees
 *
 * @param array $options
 * @return array
 */
function facteur_config($options = []) {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}

	// si jamais les meta sont pas migrees... le faire a l'arrache !
	if (empty($GLOBALS['meta']['facteur']) or !@unserialize($GLOBALS['meta']['facteur'])) {
		include_spip('facteur_administrations');
		facteur_migre_metas_to_config();
	}

	$config = lire_config('facteur');
	if (!empty($options) and is_array($options)) {
		$config = array_merge($config, $options);
	}

	if (!isset($config['adresse_envoi'])
		or $config['adresse_envoi'] !== 'oui'
		or !$config['adresse_envoi_email']) {
		$config = array_merge($config, facteur_config_envoyeur_par_defaut());
	}

	$config['adresses_site'] = [
		$GLOBALS['meta']['adresse_site'] . '/',
		url_de_base(),
	];

	// et on emule la globale facteur_smtp pour les plugins qui s'appuient dessus comme mailshot
	// @deprecated : ne devrait plus servir
	$GLOBALS['meta']['facteur_smtp'] = ($config['mailer'] === 'smtp' ? 'oui' : 'non');

	return $config;
}

/**
 * Generer la config par defaut de l'envoyeur, hors reglage specifique ou surcharge
 * @return array
 */
function facteur_config_envoyeur_par_defaut() {
	if (!function_exists('extraire_multi')) {
		include_spip('inc/filtres');
	}

	$config = [
		'adresse_envoi_email' => $GLOBALS['meta']['email_webmaster'] ?? '',
		'adresse_envoi_nom' => strip_tags(extraire_multi($GLOBALS['meta']['nom_site'])),
	];

	if (!empty($GLOBALS['meta']['email_envoi'])) {
		$config['adresse_envoi_email'] = $GLOBALS['meta']['email_envoi'];
	}

	return $config;
}


/**
 * Extraire automatiquement le sujet d'un message si besoin
 * @param $message_html
 * @param string $message_texte
 * @return string
 */
function facteur_extraire_sujet($message_html, $message_texte = '') {
	if (strlen($message_html = trim($message_html))) {
		// dans ce cas on ruse un peu : extraire le sujet du title
		if (preg_match(',<title>(.*)</title>,Uims', $message_html, $m)) {
			return ($sujet = $m[1]);
		} else {
			// fallback, on prend le body si on le trouve
			if (preg_match(',<body[^>]*>(.*)</body>,Uims', $message_html, $m)) {
				$message_html = $m[1];
			}
			// et on le nettoie/decoupe comme du texte
			$message_texte = textebrut($message_html);
		}
	} else {
		$message_texte = supprimer_tags($message_texte);
	}

	// et on extrait la premiere ligne de vrai texte...
	// nettoyer le html et les retours chariots
	$message_texte = str_replace("\r\n", "\r", $message_texte);
	$message_texte = str_replace("\r", "\n", $message_texte);
	$message_texte = trim($message_texte);
	// decouper
	$message_texte = explode("\n", $message_texte);

	// extraire la premiere ligne de texte brut
	return ($sujet = array_shift($message_texte));
}


/**
 * Retourne la pile de fonctions utilisée pour envoyer un mail
 *
 * @note
 *     Ignore les fonctions `include_once`, `include_spip`, `find_in_path`
 * @return array|string
 *     pile d'appel
 **/
function facteur_backtrace($limit = 10) {
	$trace = debug_backtrace();
	$caller = array_shift($trace);
	while (count($trace) and (empty($trace[0]['file']) or $trace[0]['file'] === $caller['file'] or $trace[0]['file'] === __FILE__)) {
		array_shift($trace);
	}

	$message = count($trace) ? $trace[0]['file'] . ' L' . $trace[0]['line'] : '';
	$f = [];
	while (count($trace) and $t = array_shift($trace) and count($f) < $limit) {
		if (in_array($t['function'], ['include_once', 'include_spip', 'find_in_path'])) {
			break;
		}
		$f[] = $t['function'];
	}
	if (count($f)) {
		$message .= ' [' . implode('(),', $f) . '()]';
	}

	return $message;
}
