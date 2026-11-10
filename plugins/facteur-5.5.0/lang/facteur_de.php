<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=de
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'Die Konfiguration des Briefträgers wurde gespeichert.',
	'configuration_adresse_envoi' => 'Standard-Absenderadresse',
	'configuration_facteur' => 'Postbote',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Validierung des SSL-Zertifikats',
	'configuration_mailer' => 'Versandart',
	'configuration_smtp' => 'Auswahl der Versandmethode',
	'configuration_smtp_descriptif' => 'Im Zweifel hier die mail() Funktion von PHP eintragen.',
	'corps_email_de_test' => 'Das ist ein Versandtest mit Sondärzeichen: Bär Größe Maß accentué',

	// E
	'email_envoye_par' => 'Absender @site@',
	'email_test_envoye' => 'Die Testmail wurde fehlerfrei verschickt. Falls sie nicht richtig ankommt, bearbeiten sie ihre Serverkonfiguration oder kontaktieren sie den Administrator.',
	'erreur' => 'Fehler',
	'erreur_confirm_ip_sans_hostname' => 'Wollen Sie diese IP Adresse wirklich als SMTP Host verwenden ?',
	'erreur_dans_log' => ' : mehr Details in der Logdatei',
	'erreur_envoi_bloque_constante' => 'Versand wird durch die Konstante <tt>_TEST_EMAIL_DEST</tt> blockiert.
Überprüfen sie die Datei <tt>mes_options.php</tt>',
	'erreur_generale' => 'Konfigurationsfehler. Bitte Inhalt des Formulars korrigieren.',
	'erreur_invalid_host' => 'falscher Servername',
	'erreur_invalid_port' => 'falsche Portnummer',
	'erreur_ip_sans_hostname' => 'Diese IP Adresse entspricht keinem Domainnamen.',

	// F
	'facteur_adresse_envoi_email' => 'E-Mail :',
	'facteur_adresse_envoi_nom' => 'Name:',
	'facteur_bcc' => 'Blindkopie (BCC) :',
	'facteur_cc' => 'Kopie (CC) :',
	'facteur_copies' => 'Kopien:',
	'facteur_copies_descriptif' => 'Eine Kopie der E-Mails wird an die angegebenen Adressen geschickt. Geben sie eine Adresse als Empfänger der Kopie bzw. Blindkopie an.',
	'facteur_email_test' => 'Eine Testmail versenden an:',
	'facteur_filtre_accents' => 'Sonderzeichen in HTML-Entitäten umwandeln (z.B. für Hotmail).',
	'facteur_filtre_css' => 'Stile zwischen <head> und </head> zu "inline" Stilen umwandeln, sinnvoll für Webmail die inline-Stile externen vorzieht.',
	'facteur_filtre_images' => 'Verlinkte Bilder in E-Mail einbetten',
	'facteur_filtre_iso_8859' => 'Nach ISO-8859-1 umwandeln',
	'facteur_filtres' => 'Filter',
	'facteur_filtres_descriptif' => 'Beim Versand können die Mails durch mehrere Filter behandelt werden.',
	'facteur_smtp_auth' => 'Autorisierung erforderlich:',
	'facteur_smtp_auth_non' => 'nein',
	'facteur_smtp_auth_oui' => 'ja',
	'facteur_smtp_host' => 'Server:',
	'facteur_smtp_password' => 'Passwort:',
	'facteur_smtp_port' => 'Port:',
	'facteur_smtp_secure' => 'Verschlüsselte Verbindung:',
	'facteur_smtp_secure_non' => 'nein',
	'facteur_smtp_secure_ssl' => 'SSL (obsolet)',
	'facteur_smtp_secure_tls' => 'TLS (empfohlen)',
	'facteur_smtp_sender' => 'Fehlercodes (optional)',
	'facteur_smtp_sender_descriptif' => 'Legt im Kopf der Mail die Empfängeradresse für Fehlermeldungen fest (bzw. den Return-Path).',
	'facteur_smtp_tls_allow_self_signed_non' => 'Das SSL-Zertifikat des SMTP-Servers ist von einer Zertifizierungsstelle erstellt worden (empfohlen).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'das SSL-Zertifikat des SMTP-Servers ist selbst signiert.',
	'facteur_smtp_username' => 'Benutzername:',

	// I
	'info_envois_bloques_constante' => 'Alle Sendungen werden durch die Konstante <tt>_TEST_EMAIL_DEST</tt> blockiert.',
	'info_envois_forces_vers_email' => 'Alle Sendungen werden durch die Konstante <tt>_TEST_EMAIL_DEST</tt> an die Adresse <b>@email@</b> erzwungen.',

	// L
	'label_email_test_avec_piece_jointe' => 'Mit Anhang',
	'label_email_test_from' => 'Absender',
	'label_email_test_from_placeholder' => 'from@example.org (optional)',
	'label_email_test_important' => 'Diese E-Mail ist wichtig',
	'label_facteur_forcer_from' => 'Diese Versandadresse verwenden wenn  die Domain im Feld <tt>From</tt> nicht identisch ist',
	'label_message_envoye' => 'Gesendete Nachricht:',

	// M
	'message_identite_email' => 'Die  <a href="@url@">Konfiguration des Plugins <i>Postbote</i> </a> überschreibt diese E-Mail-Adresse beim Versenden von E-Mails mit <b>@email@>.',

	// N
	'note_test_configuration' => 'Eine Mail wird an diese Adresse geschickt.',

	// P
	'personnaliser' => 'Individuelle Einstellungen',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] Versand gescheitert an @dest@ (Betreff: @sujet@)',

	// T
	'tester' => 'Testen',
	'tester_la_configuration' => 'Konfiguration testen',
	'titre_configurer_facteur' => 'Konfiguration Postbote',

	// U
	'utiliser_mail' => 'Funktion <tt>mail()</tt> von PHP verwenden',
	'utiliser_reglages_site' => 'Die Einstellungen der SPIP-Website verwenden
',
	'utiliser_smtp' => 'SMTP verwenden',

	// V
	'valider' => ' OK ',
	'version_html' => 'HTML-Version.',
	'version_texte' => 'Textversion.',
];
